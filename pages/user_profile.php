<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Wshooes</title>
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
        .profile-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .order-card {
            background: rgba(30, 58, 138, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.1);
            transition: all 0.3s ease;
        }
        .order-card:hover {
            border-color: rgba(59, 130, 246, 0.3);
            background: rgba(30, 58, 138, 0.1);
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
                    <a href="cart_page.php" class="relative hover:text-accent transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if (isset($cart_count) && $cart_count > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user_profile.php" class="text-accent">
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
            <!-- Profile Section -->
            <div class="w-full lg:w-1/3">
                <div class="profile-card rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-secondary p-6 text-white text-center">
                        <div class="relative mx-auto w-32 h-32 rounded-full border-4 border-accent/30 mb-4 overflow-hidden">
                            <?php if (isset($user_info['profile_picture']) && !empty($user_info['profile_picture'])): ?>
                                <img src="../assets/uploads/profile_pictures/<?php echo htmlspecialchars($user_info['profile_picture']); ?>" 
                                     alt="Foto Profil" class="w-full h-full object-cover">
                            <?php else: ?>
                                <img src="../assets/images/default-profile.jpg" alt="Foto Profil Default" class="w-full h-full object-cover">
                            <?php endif; ?>
                            <button class="absolute bottom-0 right-0 bg-accent rounded-full p-2 hover:bg-blue-600 transition">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($user_info['full_name'] ?? 'Pengguna'); ?></h2>
                        <p class="text-blue-200"><?php echo $user_info['membership_level'] ?? 'Member'; ?></p>
                    </div>
                    
                    <div class="p-6 text-gray-300">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center text-white">
                                <i class="fas fa-user-circle mr-2 text-accent"></i> Informasi Pribadi
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-400">Nama Lengkap</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($user_info['full_name'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Email</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($user_info['email'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Telepon</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($user_info['phone_number'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Bergabung Sejak</p>
                                    <p class="font-medium"><?php echo date('d F Y', strtotime($user_info['created_at'] ?? 'now')); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center text-white">
                                <i class="fas fa-truck mr-2 text-accent"></i> Alamat Pengiriman
                            </h3>
                            <div>
                                <p class="text-sm text-gray-400">Alamat Utama</p>
                                <p class="font-medium"><?php echo htmlspecialchars($user_info['address'] ?? '-'); ?></p>
                                <p class="font-medium"><?php echo htmlspecialchars($user_info['city'] ?? '-'); ?></p>
                                <p class="font-medium"><?php echo htmlspecialchars($user_info['postal_code'] ?? '-'); ?></p>
                            </div>
                        </div>
                        
                        <a href="edit_profile_page.php" class="w-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-4 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Order History Section -->
            <div class="w-full lg:w-2/3">
                <div class="profile-card rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-secondary p-6">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-history mr-3"></i> Riwayat Pesanan
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Order Filter -->
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <div class="relative">
                                <select class="appearance-none bg-darkBlue/50 border border-accent/30 rounded-lg py-2 px-4 pr-8 text-white focus:outline-none focus:border-accent">
                                    <option>Semua Pesanan</option>
                                    <option>Selesai</option>
                                    <option>Diproses</option>
                                    <option>Dikirim</option>
                                    <option>Dibatalkan</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="relative flex-1 max-w-md">
                                <input type="text" placeholder="Cari pesanan..." 
                                       class="w-full bg-darkBlue/50 border border-accent/30 rounded-lg py-2 px-4 text-white placeholder-gray-400 focus:outline-none focus:border-accent">
                                <button class="absolute right-0 top-0 h-full px-3 text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Order List -->
                        <div class="space-y-4">
                            <?php if (isset($orders) && !empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                <div class="order-card rounded-xl p-4">
                                    <div class="flex flex-wrap justify-between items-start mb-3 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-400">No. Pesanan</p>
                                            <p class="font-bold"><?php echo htmlspecialchars($order['order_number']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Tanggal</p>
                                            <p class="font-medium"><?php echo date('d F Y', strtotime($order['created_at'])); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Total</p>
                                            <p class="font-bold text-accent">Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></p>
                                        </div>
                                        <div>
                                            <?php
                                            $status_class = '';
                                            switch($order['status']) {
                                                case 'delivered':
                                                    $status_class = 'bg-green-500/20 text-green-400';
                                                    $status_text = 'Terkirim';
                                                    break;
                                                case 'shipped':
                                                    $status_class = 'bg-blue-500/20 text-blue-400';
                                                    $status_text = 'Dikirim';
                                                    break;
                                                case 'processing':
                                                    $status_class = 'bg-yellow-500/20 text-yellow-400';
                                                    $status_text = 'Diproses';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'bg-red-500/20 text-red-400';
                                                    $status_text = 'Dibatalkan';
                                                    break;
                                                default:
                                                    $status_class = 'bg-gray-500/20 text-gray-400';
                                                    $status_text = 'Menunggu';
                                            }
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-4 mb-4">
                                        <?php foreach ($order['items'] as $item): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($order['items']) > 3): ?>
                                        <div class="text-gray-400 text-sm">
                                            + <?php echo count($order['items']) - 3; ?> item lainnya
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex flex-wrap justify-between items-center gap-4">
                                        <button class="text-accent hover:text-blue-400 font-medium flex items-center transition-colors">
                                            <i class="fas fa-redo-alt mr-2"></i> Beli Lagi
                                        </button>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                           class="bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-4 rounded-lg transition flex items-center">
                                            <i class="fas fa-info-circle mr-2"></i> Detail Pesanan
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <i class="fas fa-shopping-bag text-6xl text-gray-600 mb-4"></i>
                                    <h3 class="text-xl font-semibold mb-2">Belum ada pesanan</h3>
                                    <p class="text-gray-400 mb-6">Anda belum memiliki riwayat pesanan</p>
                                    <a href="all_product.php" class="bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-6 rounded-lg transition inline-flex items-center">
                                        <i class="fas fa-shopping-cart mr-2"></i> Mulai Belanja
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <div class="flex justify-center mt-8">
                            <nav class="flex items-center space-x-1">
                                <?php if ($current_page > 1): ?>
                                <a href="?page=<?php echo $current_page - 1; ?>" 
                                   class="p-2 rounded-lg border border-accent/30 hover:bg-accent/10 transition-colors">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-4 py-2 rounded-lg <?php echo $i === $current_page ? 'bg-accent text-white' : 'border border-accent/30 hover:bg-accent/10'; ?> transition-colors">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo $current_page + 1; ?>" 
                                   class="p-2 rounded-lg border border-accent/30 hover:bg-accent/10 transition-colors">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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
                        <li><a href="#" class="hover:text-white transition">Pengembalian & Penukaran</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i> Jl. Sepatu No. 123, Jakarta
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2"></i> (021) 123-4567
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i> info@wshooes.com
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
</body>
</html>