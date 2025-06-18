<?php
session_start();
require_once '../config/connection.php';
require_once '../models/Cart.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../config/auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$session_id = session_id();

// Initialize cart and get cart items
$cart = new Cart();
$cart_items = $cart->getCartItems($session_id, $user_id);
$cart_total = $cart->getCartTotal($session_id, $user_id);
$cart_count = $cart->getCartCount($session_id, $user_id);

// Calculate totals
$subtotal = $cart_total;
$shipping_cost = 25000; // Default shipping cost
$discount = 0;
$final_total = $subtotal + $shipping_cost - $discount;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#1e3a8a',
                        accent: '#3b82f6',
                        dark: '#0f172a',
                        darkBlue: '#1e293b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
        }
        .cart-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <header class="bg-gradient-to-r from-dark via-primary to-secondary shadow-2xl backdrop-blur-sm border-b border-blue-500/20">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-shoe-prints text-3xl text-accent"></i>
                    <h1 class="text-3xl font-bold gradient-text">Wshooes</h1>
                </div>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="../index.php" class="hover:text-accent transition-colors font-medium">Beranda</a>
                    <a href="all_product.php" class="hover:text-accent transition-colors font-medium">Produk</a>
                    <a href="collection.php" class="hover:text-accent transition-colors font-medium">Koleksi</a>
                    <a href="about.php" class="hover:text-accent transition-colors font-medium">Tentang</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user_profile.php" class="hover:text-accent transition-colors">
                            <i class="fas fa-user-circle text-xl"></i>
                        </a>
                        <a href="../config/auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            Keluar
                        </a>
                    <?php else: ?>
                        <a href="../config/auth/login.php" class="bg-accent hover:bg-blue-600 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items Section -->
            <div class="lg:w-2/3">
                <div class="cart-card rounded-xl p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Keranjang Belanja</h2>
                        <span class="text-accent"><?php echo $cart_count; ?> item</span>
                    </div>
                    
                    <!-- Cart Items -->
                    <div class="space-y-6">
                        <?php if (!empty($cart_items)): ?>
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item border border-accent/20 rounded-xl p-4 flex flex-col sm:flex-row gap-4 bg-darkBlue/30">
                                <div class="w-full sm:w-1/4">
                                    <img src="../assets/uploads/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                         class="w-full h-32 object-cover rounded-lg">
                                </div>
                                <div class="w-full sm:w-3/4 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-gray-300">
                                                <span class="text-gray-400">Ukuran: </span>
                                                <span class="font-medium"><?php echo htmlspecialchars($item['size']); ?></span>
                                            </p>
                                            <p class="text-gray-300">
                                                <span class="text-gray-400">Warna: </span>
                                                <span class="font-medium"><?php echo htmlspecialchars($item['color']); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center mb-3 sm:mb-0">
                                            <button class="quantity-btn bg-accent/20 text-white px-3 py-1 rounded-l-lg hover:bg-accent/30 transition-colors" 
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span id="quantity-<?php echo $item['id']; ?>" 
                                                  class="bg-darkBlue/50 px-4 py-1 text-center border-y border-accent/20">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                            <button class="quantity-btn bg-accent/20 text-white px-3 py-1 rounded-r-lg hover:bg-accent/30 transition-colors" 
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="text-lg font-bold text-accent">
                                                Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                            </span>
                                            <button class="text-red-400 hover:text-red-500 transition-colors" 
                                                    onclick="removeItem(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-shopping-cart text-6xl text-accent/50 mb-4"></i>
                                <h3 class="text-xl font-semibold mb-2">Keranjang Belanja Kosong</h3>
                                <p class="text-gray-400 mb-6">Anda belum menambahkan produk ke keranjang</p>
                                <a href="all_product.php" 
                                   class="bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-6 rounded-lg transition inline-flex items-center">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Mulai Belanja
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($cart_items)): ?>
                    <div class="mt-6 pt-6 border-t border-accent/20 flex justify-between">
                        <a href="all_product.php" class="text-accent hover:text-blue-400 transition-colors font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Lanjutkan Belanja
                        </a>
                        <button onclick="updateCart()" 
                                class="bg-accent/20 text-white px-4 py-2 rounded-lg hover:bg-accent/30 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Perbarui Keranjang
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Summary Section -->
            <?php if (!empty($cart_items)): ?>
            <div class="lg:w-1/3">
                <div class="cart-card rounded-xl p-6 sticky top-8">
                    <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Subtotal</span>
                            <span class="font-medium" id="subtotal">
                                Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Ongkos Kirim</span>
                            <span class="font-medium">
                                Rp <?php echo number_format($shipping_cost, 0, ',', '.'); ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Diskon</span>
                            <span class="font-medium text-green-400">
                                - Rp <?php echo number_format($discount, 0, ',', '.'); ?>
                            </span>
                        </div>
                        <div class="border-t border-accent/20 pt-4 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span id="total" class="text-accent">
                                Rp <?php echo number_format($final_total, 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="promo" class="block text-sm font-medium mb-2">Kode Promo</label>
                        <div class="flex">
                            <input type="text" id="promo" placeholder="Masukkan kode promo" 
                                   class="flex-1 bg-darkBlue/50 border border-accent/30 rounded-l-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-accent">
                            <button class="bg-accent hover:bg-blue-600 text-white px-4 py-2 rounded-r-lg transition-colors">
                                Terapkan
                            </button>
                        </div>
                    </div>
                    
                    <a href="checkout.php" 
                       class="w-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-3 rounded-lg font-bold transition flex items-center justify-center space-x-2">
                        <i class="fas fa-credit-card"></i>
                        <span>Lanjut ke Checkout</span>
                    </a>
                    
                    <div class="mt-4 text-center text-sm text-gray-400">
                        <p>Dengan melanjutkan, Anda menyetujui <a href="#" class="text-accent hover:underline">Syarat & Ketentuan</a> kami</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-dark via-primary to-secondary text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-shoe-prints mr-2"></i> Wshooes
                    </h3>
                    <p class="text-gray-300">Destinasi premium Anda untuk fashion sepatu terkini.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Belanja</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition">Pria</a></li>
                        <li><a href="#" class="hover:text-white transition">Wanita</a></li>
                        <li><a href="#" class="hover:text-white transition">Anak-anak</a></li>
                        <li><a href="#" class="hover:text-white transition">Baru Datang</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition">Layanan Pelanggan</a></li>
                        <li><a href="#" class="hover:text-white transition">Lacak Pesanan</a></li>
                        <li><a href="#" class="hover:text-white transition">Pengembalian</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Jl. Sepatu No. 123, Jakarta
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            (021) 123-4567
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            info@wshooes.com
                        </li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-300 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2023 Wshooes. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        function updateQuantity(itemId, change) {
            const quantityElement = document.getElementById(`quantity-${itemId}`);
            const currentQuantity = parseInt(quantityElement.textContent);
            const newQuantity = Math.max(1, currentQuantity + change);
            
            fetch('../controllers/CartController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&item_id=${itemId}&quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    quantityElement.textContent = newQuantity;
                    updateTotals(data.subtotal, data.total);
                } else {
                    alert('Gagal mengupdate jumlah: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate jumlah');
            });
        }

        function removeItem(itemId) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                return;
            }
            
            fetch('../controllers/CartController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus item: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus item');
            });
        }

        function updateCart() {
            location.reload();
        }

        function updateTotals(subtotal, total) {
            document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            document.getElementById('total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }
    </script>
</body>
</html>