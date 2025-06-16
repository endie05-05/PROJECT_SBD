<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['no_meja'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Foodcourt BC UNAND</title>
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

        .header-title {
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .section {
            background: #f5e6a8;
            margin-bottom: 20px;
            border-radius: 12px;
            padding: 15px;
            border: 2px solid #8b7355;
        }

        .section-title {
            font-weight: bold;
            color: #8b7355;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: bold;
            color: #333;
        }

        .item-shop {
            font-size: 12px;
            color: #666;
        }

        .item-price {
            font-size: 14px;
            color: #8b7355;
            font-weight: bold;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 50%;
            background: #8b7355;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .quantity {
            min-width: 25px;
            text-align: center;
            font-weight: bold;
        }

        .payment-method {
            margin-bottom: 15px;
        }

        .payment-options {
            display: flex;
            gap: 10px;
        }

        .payment-option {
            flex: 1;
            padding: 10px;
            border: 2px solid #8b7355;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            background: white;
            transition: all 0.2s;
        }

        .payment-option.selected {
            background: #8b7355;
            color: white;
        }

        .payment-option input {
            display: none;
        }

        .total-section {
            background: #8b7355;
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-final {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 10px;
            margin-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
        }

        .btn-process {
            background: #28a745;
            color: white;
        }

        .btn-process:hover {
            background: #218838;
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
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .notes-section {
            margin-bottom: 15px;
        }

        .notes-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #8b7355;
            border-radius: 8px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button class="back-button" onclick="history.back()">←</button>
            <div class="header-title">Rincian Pesanan</div>
        </div>
        
        <div class="content">
            <div class="section">
                <div class="section-title">RINCIAN PESANAN:</div>
                <div id="orderItems"></div>
            </div>

            <div class="section">
                <div class="section-title">CATATAN:</div>
                <div class="notes-section">
                    <textarea class="notes-input" id="orderNotes" placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                </div>
            </div>

            <div class="section">
                <div class="section-title">METODE PEMBAYARAN:</div>
                <div class="payment-method">
                    <div class="payment-options">
                        <label class="payment-option selected">
                            <input type="radio" name="payment" value="TUNAI" checked>
                            <span>TUNAI</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="NON-TUNAI">
                            <span>NON-TUNAI</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Jumlah:</span>
                    <span id="totalQuantity">0</span>
                </div>
                <div class="total-row total-final">
                    <span>Total:</span>
                    <span id="totalPrice">Rp 0</span>
                </div>
                <div class="total-row">
                    <span>Jenis Transaksi:</span>
                    <span id="paymentType">TUNAI</span>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-cancel" onclick="cancelOrder()">Batalkan Pesanan</button>
                <button class="btn btn-process" onclick="processOrder()">Proses Pesanan</button>
            </div>
        </div>
    </div>

    <script>
        let cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');

        function displayOrderItems() {
            const orderItemsContainer = document.getElementById('orderItems');
            
            if (cartItems.length === 0) {
                orderItemsContainer.innerHTML = '<div class="empty-cart">Keranjang kosong</div>';
                return;
            }

            let html = '';
            cartItems.forEach((item, index) => {
                html += `
                    <div class="order-item">
                        <div class="item-info">
                            <div class="item-name">${item.name}</div>
                            <div class="item-shop">${item.shopName}</div>
                            <div class="item-price">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="decreaseQuantity(${index})">−</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="qty-btn" onclick="increaseQuantity(${index})">+</button>
                        </div>
                    </div>
                `;
            });
            
            orderItemsContainer.innerHTML = html;
            updateTotals();
        }

        function updateTotals() {
            const totalQuantity = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('totalQuantity').textContent = totalQuantity;
            document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        }

        function increaseQuantity(index) {
            cartItems[index].quantity++;
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            displayOrderItems();
        }

        function decreaseQuantity(index) {
            if (cartItems[index].quantity > 1) {
                cartItems[index].quantity--;
            } else {
                cartItems.splice(index, 1);
            }
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            displayOrderItems();
        }

        function cancelOrder() {
            if (confirm('Apakah Anda yakin ingin membatalkan pesanan?')) {
                localStorage.removeItem('cartItems');
                window.location.href = 'shop_selection.php';
            }
        }

        function processOrder() {
            if (cartItems.length === 0) {
                alert('Keranjang kosong! Silakan tambahkan item terlebih dahulu.');
                return;
            }

            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const notes = document.getElementById('orderNotes').value;
            
            // Simpan data pesanan ke localStorage untuk dikirim ke server
            const orderData = {
                items: cartItems,
                paymentMethod: paymentMethod,
                notes: notes,
                tableNumber: <?= $_SESSION['no_meja'] ?>,
                sessionId: '<?= $_SESSION['id_sesi'] ?>'
            };
            
            localStorage.setItem('currentOrder', JSON.stringify(orderData));
            window.location.href = 'process_order.php';
        }

        // Payment method selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
                document.getElementById('paymentType').textContent = this.querySelector('input').value;
            });
        });

        // Initialize display
        displayOrderItems();
    </script>
</body>
</html>