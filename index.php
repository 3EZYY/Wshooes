<?php
/**
 * Wshooes E-commerce Website
 * Homepage / Landing Page
 * 
 * Fungsi utama:
 * 1. Entry point utama website
 * 2. Homepage dengan hero section dan featured products
 * 3. Session management
 * 4. Bootstrap aplikasi
 */

// Start session
session_start();

// Include necessary files
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/includes/functions.php';

// Check if user wants to access admin panel
if (isset($_GET['admin'])) {
    // Check if user is admin
    if (is_logged_in() && is_admin()) {
        header('Location: /Wshooes/admin/dashboard.php');
    } else {
        header('Location: /Wshooes/auth/login.php?redirect=admin');
    }
    exit;
}

// Check if user wants to access specific page
if (isset($_GET['page'])) {
    $page = sanitize_input($_GET['page']);
    $allowed_pages = ['products', 'about', 'contact', 'cart', 'profile'];
    
    if (in_array($page, $allowed_pages)) {
        $page_file = __DIR__ . '/pages/' . $page . '.php';
        if (file_exists($page_file)) {
            require_once $page_file;
            exit;
        }
    }
}

// If no specific page requested, show homepage content below
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wshooes - Premium Footwear</title>
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
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .product-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            background: rgba(30, 58, 138, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
        }
        .feature-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            background: rgba(30, 58, 138, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
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
                    <a href="/Wshooes/" class="text-accent font-medium">Beranda</a>
                    <a href="/Wshooes/pages/all_product.php" class="hover:text-accent transition-colors font-medium">Produk</a>
                    <a href="/Wshooes/pages/collection.php" class="hover:text-accent transition-colors font-medium">Koleksi</a>
                    <a href="/Wshooes/pages/about.php" class="hover:text-accent transition-colors font-medium">Tentang</a>
                    <a href="/Wshooes/pages/terms_privacy.php#contact" class="hover:text-accent transition-colors font-medium">Kontak</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="pages/cart_page.php" class="relative hover:text-accent transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if (isset($cart_count) && $cart_count > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative">
                            <button id="user-menu" class="flex items-center space-x-2 hover:text-accent transition-colors">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span class="hidden md:inline"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                            </button>
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-darkBlue border border-accent/20 rounded-lg shadow-xl">
                                <div class="py-1">
                                    <a href="/Wshooes/pages/edit_profile_page.php" class="block px-4 py-2 text-sm hover:bg-accent/10">Edit Profil</a>
                                    <a href="/Wshooes/pages/user_profile.php" class="block px-4 py-2 text-sm hover:bg-accent/10">Pesanan Saya</a>
                                    <a href="/Wshooes/config/auth/logout.php" class="block px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">Keluar</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/Wshooes/config/auth/login.php" class="bg-accent hover:bg-blue-600 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Langkah Menuju Gaya</h2>
                <p class="text-xl mb-8 text-gray-300">Temukan sepatu premium yang menggabungkan kenyamanan dan fashion untuk setiap kesempatan.</p>
                <div class="flex space-x-4">
                    <a href="/Wshooes/pages/all_product.php" 
                       class="bg-gradient-to-r from-primary to-secondary hover:opacity-90 px-6 py-3 rounded-lg font-medium transition flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Belanja Sekarang
                    </a>
                    <a href="/Wshooes/pages/collection.php" 
                       class="border border-accent/30 hover:bg-accent/10 px-6 py-3 rounded-lg font-medium transition flex items-center">
                        <i class="fas fa-th-large mr-2"></i>
                        Lihat Koleksi
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <img src="https://images.unsplash.com/photo-1600269452121-1f5d1415f5b1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                     alt="Premium Shoes" 
                     class="rounded-xl shadow-2xl max-w-md w-full h-auto border border-accent/20">
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 gradient-text">Produk Unggulan</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">Pilihan sepatu terpopuler dan terkini kami</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card rounded-xl overflow-hidden">
                    <div class="relative">
                        <img src="assets/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="w-full h-64 object-cover">
                        <?php if ($product['discount_price'] > 0): ?>
                        <div class="absolute top-3 left-3">
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                SALE
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $product['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm text-gray-400 ml-2">(<?php echo $product['review_count']; ?> ulasan)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <?php if ($product['discount_price'] > 0): ?>
                                    <p class="text-xl font-bold text-accent">Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?></p>
                                    <p class="text-sm text-gray-400 line-through">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <?php else: ?>
                                    <p class="text-xl font-bold text-accent">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <?php endif; ?>
                            </div>
                            <button onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                    class="bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-4 rounded-lg transition flex items-center space-x-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Beli</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/Wshooes/pages/all_product.php" 
                   class="border border-accent/30 hover:bg-accent/10 px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-th-large mr-2"></i>
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 gradient-text">Mengapa Memilih Wshooes</h2>
                <p class="text-gray-300 max-w-2xl mx-auto">Kami berkomitmen memberikan pengalaman berbelanja sepatu terbaik</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card p-6 rounded-xl">
                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-medal text-accent text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Kualitas Premium</h3>
                    <p class="text-gray-300">Sepatu kami dibuat dengan bahan berkualitas tinggi untuk ketahanan dan kenyamanan.</p>
                </div>
                
                <div class="feature-card p-6 rounded-xl">
                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-truck text-accent text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Pengiriman Cepat</h3>
                    <p class="text-gray-300">Dapatkan pesanan Anda dalam 2-3 hari kerja ke seluruh Indonesia.</p>
                </div>
                
                <div class="feature-card p-6 rounded-xl">
                    <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-headset text-accent text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Layanan 24/7</h3>
                    <p class="text-gray-300">Tim customer service kami siap membantu Anda kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-dark via-primary to-secondary py-12">
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
                <p>&copy; 2024 Wshooes. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Handle user dropdown menu
        document.addEventListener('DOMContentLoaded', function() {
            const userMenu = document.getElementById('user-menu');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (userMenu && userDropdown) {
                userMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target)) {
                        userDropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Add to cart functionality
        function addToCart(product) {
            // Show product modal with size and color selection
            showProductModal(product);
        }

        // Product modal functionality
        let currentProduct = null;
        const sizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        const colors = ['Black', 'White', 'Red', 'Blue', 'Grey', 'Green'];

        function showProductModal(product) {
            currentProduct = product;
            // Implementation similar to all_product.php
            // Show modal with size and color selection
            // After selection, send to add_to_cart.php
        }
    </script>
</body>
</html>