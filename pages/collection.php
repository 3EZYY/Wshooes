<?php
require_once '../config/connection.php';
require_once '../models/Product.php';

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$product = new Product();

// Get products by category
$sneakers = $product->get_by_category('sneakers');
$casual = $product->get_by_category('casual');
$sport = $product->get_by_category('sport');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Sepatu - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(to right, #1e40af, #1e3a8a);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .product-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .collection-header {
            background: linear-gradient(to right, rgba(30, 64, 175, 0.9), rgba(30, 58, 138, 0.9));
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a class="flex items-center space-x-2 text-white text-2xl font-bold" href="../index.php">
                    <i class="fas fa-shoe-prints"></i>
                    <span>Wshooes</span>
                </a>
                <div class="flex space-x-6">
                    <a class="text-white hover:text-blue-200 transition" href="../index.php">Home</a>
                    <a class="text-white hover:text-blue-200 transition" href="all_product.php">All Products</a>
                    <a class="text-white hover:text-blue-200 transition" href="collection.php">Collections</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-200 to-purple-200">
                <i class="fas fa-gem"></i> Koleksi Sepatu
            </h1>
            <p class="text-xl text-blue-200">Temukan koleksi sepatu terbaik untuk setiap aktivitas Anda</p>
        </div>
    </section>

    <!-- Collections Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <!-- Sneakers Collection -->
            <div class="mb-16">
                <div class="collection-header rounded-lg p-8 mb-8">
                    <h2 class="text-3xl font-bold mb-2 flex items-center">
                        <i class="fas fa-running mr-3"></i> Sneakers
                    </h2>
                    <p class="text-blue-200">Koleksi sneakers stylish untuk gaya kasual Anda</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if (!empty($sneakers)): ?>
                        <?php foreach ($sneakers as $shoe): ?>
                            <div class="product-card rounded-xl overflow-hidden">
                                <div class="aspect-w-1 aspect-h-1 relative">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             class="w-full h-64 object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-64 flex items-center justify-center bg-blue-900">
                                            <i class="fas fa-shoe-prints text-4xl text-blue-200"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-blue-200 mb-2"><?php echo htmlspecialchars($shoe['name']); ?></h3>
                                    <p class="text-2xl text-blue-300 mb-4">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></p>
                                    <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" 
                                       class="block w-full py-2 px-4 text-center bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-300">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full glass-card rounded-lg p-8 text-center">
                            <i class="fas fa-box-open text-4xl text-blue-400 mb-4"></i>
                            <h4 class="text-xl font-bold text-blue-200 mb-2">Koleksi Sneakers Segera Hadir</h4>
                            <p class="text-blue-300">Kami sedang mempersiapkan koleksi sneakers terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Casual Collection -->
            <div class="mb-16">
                <div class="collection-header rounded-lg p-8 mb-8">
                    <h2 class="text-3xl font-bold mb-2 flex items-center">
                        <i class="fas fa-walking mr-3"></i> Casual
                    </h2>
                    <p class="text-blue-200">Sepatu kasual nyaman untuk aktivitas sehari-hari</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if (!empty($casual)): ?>
                        <?php foreach ($casual as $shoe): ?>
                            <div class="product-card rounded-xl overflow-hidden">
                                <div class="aspect-w-1 aspect-h-1 relative">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             class="w-full h-64 object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-64 flex items-center justify-center bg-blue-900">
                                            <i class="fas fa-shoe-prints text-4xl text-blue-200"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-blue-200 mb-2"><?php echo htmlspecialchars($shoe['name']); ?></h3>
                                    <p class="text-2xl text-blue-300 mb-4">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></p>
                                    <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" 
                                       class="block w-full py-2 px-4 text-center bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-300">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full glass-card rounded-lg p-8 text-center">
                            <i class="fas fa-box-open text-4xl text-blue-400 mb-4"></i>
                            <h4 class="text-xl font-bold text-blue-200 mb-2">Koleksi Casual Segera Hadir</h4>
                            <p class="text-blue-300">Kami sedang mempersiapkan koleksi sepatu casual terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sport Collection -->
            <div class="mb-16">
                <div class="collection-header rounded-lg p-8 mb-8">
                    <h2 class="text-3xl font-bold mb-2 flex items-center">
                        <i class="fas fa-dumbbell mr-3"></i> Sport
                    </h2>
                    <p class="text-blue-200">Sepatu olahraga berkualitas tinggi untuk performa maksimal</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if (!empty($sport)): ?>
                        <?php foreach ($sport as $shoe): ?>
                            <div class="product-card rounded-xl overflow-hidden">
                                <div class="aspect-w-1 aspect-h-1 relative">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             class="w-full h-64 object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-64 flex items-center justify-center bg-blue-900">
                                            <i class="fas fa-shoe-prints text-4xl text-blue-200"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-blue-200 mb-2"><?php echo htmlspecialchars($shoe['name']); ?></h3>
                                    <p class="text-2xl text-blue-300 mb-4">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></p>
                                    <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" 
                                       class="block w-full py-2 px-4 text-center bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-300">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full glass-card rounded-lg p-8 text-center">
                            <i class="fas fa-box-open text-4xl text-blue-400 mb-4"></i>
                            <h4 class="text-xl font-bold text-blue-200 mb-2">Koleksi Sport Segera Hadir</h4>
                            <p class="text-blue-300">Kami sedang mempersiapkan koleksi sepatu olahraga terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/collection.js"></script>
</body>
</html>

