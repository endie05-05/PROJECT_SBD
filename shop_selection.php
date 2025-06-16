<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['no_meja'])) {
    header('Location: index.php');
    exit();
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
    <title>Pilih Toko - Foodcourt BC UNAND</title>
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
            color: rgba(255,255,255,0.8);
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

        .back-button {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.2s;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button class="back-button" onclick="history.back()">←</button>
            <div class="section-title">SECTION 1</div>
            <div class="website-title">Pilih Toko</div>
        </div>
        
        <div class="content">
            <div class="table-info">
                <div class="table-number">No. Meja <?= $_SESSION['no_meja'] ?></div>
            </div>
            
            <div class="date-time">
                <div id="currentDateTime"></div>
            </div>
            
            <ul class="shop-list">
                <?php while($toko = mysqli_fetch_assoc($result_toko)): ?>
                <li class="shop-item">
                    <a href="shop_menu.php?id_toko=<?= $toko['ID_Toko'] ?>" class="shop-link">
                        <div class="shop-icon">🏪</div>
                        <div class="shop-name"><?= $toko['Nama_Toko'] ?></div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
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

        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Check cart
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

        checkCart();
        setInterval(checkCart, 1000);
    </script>
</body>
</html>