<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['no_meja'])) {
    header('Location: index.php');
    exit();
}

// Proses pesanan jika ada data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderData = json_decode($_POST['orderData'], true);
    
    if ($orderData && !empty($orderData['items'])) {
        // Generate ID Transaksi
        $id_transaksi = 'T' . date('YmdHis') . sprintf('%03d', rand(1, 999));
        
        // Hitung total harga
        $total_harga = 0;
        foreach ($orderData['items'] as $item) {
            $total_harga += $item['price'] * $item['quantity'];
        }
        
        // Insert ke tabel transaksi
        $query_transaksi = "INSERT INTO transaksi (ID_Transaksi, ID_Sesi, Jenis_Transaksi, Total_Harga, Status_Pesanan) VALUES (?, ?, ?, ?, 'Pesanan sedang dibuat')";
        $stmt = mysqli_prepare($conn, $query_transaksi);
        mysqli_stmt_bind_param($stmt, "sssi", $id_transaksi, $_SESSION['id_sesi'], $orderData['paymentMethod'], $total_harga);
        
        if (mysqli_stmt_execute($stmt)) {
            // Insert detail pesanan ke antrian_order
            $order_line = 1;
            foreach ($orderData['items'] as $item) {
                $order_line_id = $id_transaksi . '_' . sprintf('%03d', $order_line);
                $subtotal = $item['price'] * $item['quantity'];
                
                $query_detail = "INSERT INTO antrian_order (Order_Line, ID_Transaksi, ID_Item, Jumlah_Item, Subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt_detail = mysqli_prepare($conn, $query_detail);
                mysqli_stmt_bind_param($stmt_detail, "sssii", $order_line_id, $id_transaksi, $item['id'], $item['quantity'], $subtotal);
                mysqli_stmt_execute($stmt_detail);
                
                $order_line++;
            }
            
            // Simpan ID transaksi ke session
            $_SESSION['current_transaction'] = $id_transaksi;
            $_SESSION['order_data'] = $orderData;
            
            // Return success response
            echo json_encode(['success' => true, 'transaction_id' => $id_transaksi]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal menyimpan transaksi']);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pesanan - Foodcourt BC UNAND</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background: #f5e6a8;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .header {
            background: #8b7355;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px 20px;
            text-align: center;
        }

        .loading {
            margin: 20px 0;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #8b7355;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .message {
            font-size: 18px;
            color: #8b7355;
            margin: 20px 0;
        }

        .btn {
            background: #8b7355;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #6d5a42;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">Memproses Pesanan</div>
        </div>
        
        <div class="content">
            <div class="loading">
                <div class="spinner"></div>
            </div>
            <div class="message" id="message">Sedang memproses pesanan Anda...</div>
            <div id="actions" style="display: none;">
                <button class="btn" onclick="viewReceipt()">Lihat Struk</button>
                <button class="btn" onclick="newOrder()">Pesan Lagi</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderData = localStorage.getItem('currentOrder');
            
            if (!orderData) {
                document.getElementById('message').textContent = 'Data pesanan tidak ditemukan!';
                return;
            }

            // Kirim data ke server
            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'orderData=' + encodeURIComponent(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('message').textContent = 'Pesanan berhasil diproses!';
                    document.getElementById('actions').style.display = 'block';
                    
                    // Clear cart
                    localStorage.removeItem('cartItems');
                    localStorage.removeItem('currentOrder');
                    
                    // Save transaction ID for receipt
                    localStorage.setItem('lastTransactionId', data.transaction_id);
                } else {
                    document.getElementById('message').textContent = 'Gagal memproses pesanan: ' + (data.error || 'Unknown error');
                }
                
                // Hide loading spinner
                document.querySelector('.loading').style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message').textContent = 'Terjadi kesalahan saat memproses pesanan';
                document.querySelector('.loading').style.display = 'none';
            });
        });

        function viewReceipt() {
            window.location.href = 'receipt.php';
        }

        function newOrder() {
            window.location.href = 'shop_selection.php';
        }
    </script>
</body>
</html>