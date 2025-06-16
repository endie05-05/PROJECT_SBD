<?php
session_start();
include 'koneksi.php';

// Generate ID Sesi unik
if (!isset($_SESSION['id_sesi'])) {
    $_SESSION['id_sesi'] = 'S' . date('YmdHis') . sprintf('%03d', rand(1, 999));
}

// Ambil data toko
$query_toko = "SELECT * FROM toko";
$result_toko = mysqli_query($conn, $query_toko);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodcourt BC UNAND</title>
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
            position: relative;
        }

        .section-title {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .website-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            padding: 30px 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .table-info {
            background: #8b7355;
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }

        .table-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .date-time {
            background: #8b7355;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .shop-list {
            list-style: none;
        }

        .shop-item {
            background: #8b7355;
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .shop-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .shop-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: white;
        }

        .shop-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }

        .shop-name {
            font-size: 18px;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #8b7355;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background: white;
        }

        .input-field:focus {
            outline: none;
            border-color: #8b7355;
        }

        .start-button {
            width: 100%;
            background: #8b7355;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .start-button:hover {
            background: #6d5a42;
        }

        .cart-popup {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #8b7355;
            color: white;
            padding: 15px 25px;
            border-radius: 25px;
            cursor: pointer;
            display: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translate(-50%, 100px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #f5e6a8;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            padding: 10px;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="section-title">SECTION 1</div>
            <div class="website-title">Foodcourt BC UNAND</div>
        </div>
        
        <div class="content">
            <form id="startForm" method="POST" action="process_start.php">
                <div class="input-group">
                    <label class="input-label" for="no_meja">Nomor Meja:</label>
                    <select class="input-field" id="no_meja" name="no_meja" required>
                        <option value="">Pilih Nomor Meja</option>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="table-info" id="tableInfo" style="display: none;">
                    <div class="table-number">No. Meja <span id="selectedTable"></span></div>
                </div>
                
                <div class="date-time">
                    <div id="currentDateTime"></div>
                </div>
                
                <button type="submit" class="start-button">Mulai Pemesanan</button>
            </form>
        </div>
    </div>

    <!-- Cart Popup -->
    <div class="cart-popup" id="cartPopup" onclick="showOrderDetail()">
        <span id="cartText">Lihat Pesanan (0 item)</span>
    </div>

    <script>
        // Update waktu real-time
        function updateDateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            };
            document.getElementById('currentDateTime').textContent = 
                now.toLocaleDateString('id-ID', options);
        }

        // Update setiap detik
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Show table info when selected
        document.getElementById('no_meja').addEventListener('change', function() {
            const tableInfo = document.getElementById('tableInfo');
            const selectedTable = document.getElementById('selectedTable');
            
            if (this.value) {
                selectedTable.textContent = this.value;
                tableInfo.style.display = 'block';
            } else {
                tableInfo.style.display = 'none';
            }
        });

        // Check if there are items in session storage (untuk cart popup)
        function checkCart() {
            const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
            const cartPopup = document.getElementById('cartPopup');
            const cartText = document.getElementById('cartText');
            
            if (cartItems.length > 0) {
                const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
                cartText.textContent = `Lihat Pesanan (${totalItems} item)`;
                cartPopup.style.display = 'block';
            } else {
                cartPopup.style.display = 'none';
            }
        }

        function showOrderDetail() {
            window.location.href = 'order_detail.php';
        }

        // Check cart on page load
        checkCart();
        
        // Check cart periodically
        setInterval(checkCart, 1000);
    </script>
</body>
</html>