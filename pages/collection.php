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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Koleksi Sepatu - Wshooes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/collection.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="brand-logo" href="../index.php">
                <i class="fas fa-shoe-prints"></i> Wshooes
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">Home</a>
                <a class="nav-link" href="all_product.php">All Products</a>
                <a class="nav-link active" href="collection.php">Collections</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-gem"></i> Koleksi Sepatu</h1>
            <p>Temukan koleksi sepatu terbaik untuk setiap aktivitas Anda</p>
        </div>
    </section>

    <!-- Collections Section -->
    <section class="collection-section">
        <div class="container">
            <!-- Sneakers Collection -->
            <div class="collection-card">
                <div class="collection-header sneakers">
                    <h2><i class="fas fa-running"></i> Sneakers</h2>
                    <p>Koleksi sneakers stylish untuk gaya kasual Anda</p>
                </div>
                <div class="products-grid">
                    <?php if (!empty($sneakers)): ?>                        <?php foreach ($sneakers as $shoe): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-shoe-prints"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-name"><?php echo htmlspecialchars($shoe['name']); ?></div>
                                <div class="product-price">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></div>
                                <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" class="btn-detail">
                                    Lihat Detail
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-collection">
                            <i class="fas fa-box-open"></i>
                            <h4>Koleksi Sneakers Segera Hadir</h4>
                            <p>Kami sedang mempersiapkan koleksi sneakers terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Casual Collection -->
            <div class="collection-card">
                <div class="collection-header casual">
                    <h2><i class="fas fa-walking"></i> Casual</h2>
                    <p>Sepatu kasual nyaman untuk aktivitas sehari-hari</p>
                </div>
                <div class="products-grid">
                    <?php if (!empty($casual)): ?>                        <?php foreach ($casual as $shoe): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-shoe-prints"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-name"><?php echo htmlspecialchars($shoe['name']); ?></div>
                                <div class="product-price">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></div>
                                <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" class="btn-detail">
                                    Lihat Detail
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-collection">
                            <i class="fas fa-box-open"></i>
                            <h4>Koleksi Casual Segera Hadir</h4>
                            <p>Kami sedang mempersiapkan koleksi sepatu casual terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sport Collection -->
            <div class="collection-card">
                <div class="collection-header sport">
                    <h2><i class="fas fa-dumbbell"></i> Sport</h2>
                    <p>Sepatu olahraga berkualitas tinggi untuk performa maksimal</p>
                </div>
                <div class="products-grid">
                    <?php if (!empty($sport)): ?>                        <?php foreach ($sport as $shoe): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($shoe['main_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars($shoe['main_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($shoe['name']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <i class="fas fa-shoe-prints"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-name"><?php echo htmlspecialchars($shoe['name']); ?></div>
                                <div class="product-price">Rp <?php echo number_format($shoe['price'], 0, ',', '.'); ?></div>
                                <a href="detail_product.php?id=<?php echo $shoe['id']; ?>" class="btn-detail">
                                    Lihat Detail
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-collection">
                            <i class="fas fa-box-open"></i>
                            <h4>Koleksi Sport Segera Hadir</h4>
                            <p>Kami sedang mempersiapkan koleksi sepatu olahraga terbaik untuk Anda</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/collection.js"></script>
</body>
</html>
