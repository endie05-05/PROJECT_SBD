<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['no_meja']) || !isset($_GET['id_toko'])) {
    header('Location: index.php');
    exit();
}

$id_toko = $_GET['id_toko'];

// Ambil data toko
$query_toko = "SELECT * FROM toko WHERE ID_Toko = ?";
$stmt = mysqli_prepare($conn, $query_toko);
mysqli_stmt_bind_param($stmt, "s", $id_toko);
mysqli_stmt_execute($stmt);
$result_toko = mysqli_stmt_get_result($stmt);
$toko = mysqli_fetch_assoc($result_toko);

// Ambil data menu
$query_menu = "SELECT * FROM item WHERE ID_Toko = ?";
$stmt = mysqli_prepare($conn, $query_menu);
mysqli_stmt_bind_param($stmt, "s", $id_toko);
mysqli_stmt_execute($stmt);
$result_menu = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $toko['Nama_Toko'] ?> - Menu</title>
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

        .shop-title {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .shop-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .menu-item {
            background: #8b7355;
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
            color: white;
        }

        .menu-item-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .menu-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .menu-price {
            font-size: 14px;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 15px;
            display: inline-block;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 50%;
            background: white;
            color: #8b7355;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .qty-btn:hover {
            background: #f0f0f0;
        }

        .qty-btn:disabled {
            background: #ddd;
            color: #999;
            cursor: not-allowed;
        }

        .quantity {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
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

        .cart-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button class="back-button" onclick="history.back()">←</button>
            <div class="section-title">SECTION UNTUK <?= strtoupper(str_replace(' CAFE', '', $toko['Nama_Toko'])) ?></div>
            <div class="shop-title">
                <div class="shop-icon">🏪</div>
                <div><?= $toko['Nama_Toko'] ?></div>
            </div>
        </div>
        
        <div class="content">
            <?php while($menu = mysqli_fetch_assoc($result_menu)): ?>
            <div class="menu-item">
                <div class="menu-item-content">
                    <div class="menu-info">
                        <h3><?= $menu['Nama_Item'] ?></h3>
                        <div class="menu-price"><?= number_format($menu['Harga_Item'], 0, ',', '.') ?></div>
                    </div>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="decreaseQuantity('<?= $menu['ID_Item'] ?>')">−</button>
                        <span class="quantity" id="qty_<?= $menu['ID_Item'] ?>">0</span>
                        <button class="qty-btn" onclick="increaseQuantity('<?= $menu['ID_Item'] ?>', '<?= $menu['Nama_Item'] ?>', <?= $menu['Harga_Item'] ?>, '<?= $toko['Nama_Toko'] ?>')">+</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Cart Popup -->
    <div class="cart-popup" id="cartPopup" onclick="showOrderDetail()">
        <div class="cart-info">
            <span id="cartText">Lihat Pesanan (0 item)</span>
            <span class="cart-total" id="cartTotal">Rp 0</span>
        </div>
    </div>

    <script>
        let cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');

        function updateQuantityDisplay() {
            cartItems.forEach(item => {
                const qtyElement = document.getElementById('qty_' + item.id);
                if (qtyElement) {
                    qtyElement.textContent = item.quantity;
                }
            });
        }

        function increaseQuantity(id, name, price, shopName) {
            const existingItem = cartItems.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cartItems.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    shopName: shopName
                });
            }
            
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            updateQuantityDisplay();
            updateCartPopup();
        }

        function decreaseQuantity(id) {
            const existingItem = cartItems.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity--;
                if (existingItem.quantity <= 0) {
                    cartItems = cartItems.filter(item => item.id !== id);
                }
            }
            
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            updateQuantityDisplay();
            updateCartPopup();
        }

        function updateCartPopup() {
            const cartPopup = document.getElementById('cartPopup');
            const cartText = document.getElementById('cartText');
            const cartTotal = document.getElementById('cartTotal');
            
            if (cartItems.length > 0) {
                const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
                const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                
                cartText.textContent = `Lihat Pesanan (${totalItems} item)`;
                cartTotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
                cartPopup.style.display = 'block';
            } else {
                cartPopup.style.display = 'none';
            }
        }

        function showOrderDetail() {
            window.location.href = 'order_detail.php';
        }

        // Initialize display
        updateQuantityDisplay();
        updateCartPopup();
    </script>
</body>
</html>