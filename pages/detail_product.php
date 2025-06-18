<?php
session_start();

// Include necessary files
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../includes/functions.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: /Wshooes/pages/all_product.php');
    exit;
}

// Create product instance and get product details
$product = new Product();
$product_data = $product->get_by_id($product_id);

if (!$product_data) {
    header('Location: /Wshooes/pages/all_product.php?error=product_not_found');
    exit;
}

$page_title = $product_data['name'] . ' - Wshooes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #0a0e27;
            color: #ffffff;
            min-height: 100vh;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #312e81 50%, #1e1b4b 100%);
        }
        
        .glass-effect {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        .nav-blur {
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        }
        
        .size-btn {
            transition: all 0.3s ease;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        
        .size-btn:hover, .size-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            transform: translateY(-2px);
        }
        
        .color-option {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .color-option.active::after {
            content: '';
            position: absolute;
            inset: -3px;
            border: 2px solid #3b82f6;
            border-radius: 50%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .btn-secondary {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            transform: translateY(-2px);
        }
        
        .thumbnail {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .thumbnail.active {
            border-color: #3b82f6;
            transform: scale(1.05);
        }
        
        .star-rating {
            color: #fbbf24;
        }
        
        .review-card {
            background: rgba(30, 58, 138, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .related-card {
            background: rgba(30, 58, 138, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>    <!-- Navigation -->
    <nav class="nav-blur sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="../index.php" class="text-2xl font-bold text-blue-400 flex items-center gap-2">
                        <i class="fas fa-shoe-prints"></i>
                        Wshooes
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="all_product.php" class="text-gray-300 hover:text-white transition">Products</a>
                    <a href="collection.php" class="text-gray-300 hover:text-white transition">Collections</a>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-gray-300 hover:text-white transition">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="text-gray-300 hover:text-white transition relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                    <button class="text-gray-300 hover:text-white transition">
                        <i class="fas fa-user"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16">
                
                <!-- Product Images -->
                <div class="space-y-6">
                    <!-- Main Image -->
                    <div class="glass-effect rounded-3xl p-8">
                        <div class="aspect-square bg-white rounded-2xl flex items-center justify-center overflow-hidden">
                            <img id="mainImage" 
                                 src="<?php echo !empty($product_data['main_image']) ? '../assets/uploads/products/' . htmlspecialchars($product_data['main_image']) : 'https://images.unsplash.com/photo-1600269452121-1f5d14148cd6?w=600&q=80'; ?>" 
                                 alt="<?php echo htmlspecialchars($product_data['name']); ?>" 
                                 class="max-w-full max-h-full object-contain">
                        </div>
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <div class="flex gap-4 justify-center">
                        <img src="<?php echo !empty($product_data['main_image']) ? '../assets/uploads/products/' . htmlspecialchars($product_data['main_image']) : 'https://images.unsplash.com/photo-1600269452121-1f5d14148cd6?w=200&q=80'; ?>" 
                             alt="View 1" 
                             class="thumbnail active w-20 h-20 bg-white rounded-xl object-cover cursor-pointer" 
                             onclick="changeImage(this.src)">
                        <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=200&q=80" 
                             alt="View 2" 
                             class="thumbnail w-20 h-20 bg-white rounded-xl object-cover cursor-pointer" 
                             onclick="changeImage(this.src)">
                        <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb89e?w=200&q=80" 
                             alt="View 3" 
                             class="thumbnail w-20 h-20 bg-white rounded-xl object-cover cursor-pointer" 
                             onclick="changeImage(this.src)">
                        <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=200&q=80" 
                             alt="View 4" 
                             class="thumbnail w-20 h-20 bg-white rounded-xl object-cover cursor-pointer" 
                             onclick="changeImage(this.src)">
                    </div>
                </div>

                <!-- Product Details -->
                <div class="space-y-8">
                    <!-- Product Info -->
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-3"><?php echo htmlspecialchars($product_data['name']); ?></h1>
                        <p class="text-blue-300 text-lg mb-4"><?php echo htmlspecialchars($product_data['category_name'] ?? 'Premium Sneakers'); ?></p>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex star-rating">
                                <?php 
                                $rating = floatval($product_data['rating'] ?? 4.5);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="text-gray-300">4.5 (<?php echo intval($product_data['total_reviews'] ?? 127); ?> reviews)</span>
                        </div>
                        
                        <!-- Price -->
                        <div class="text-4xl font-bold text-white mb-8">
                            Rp <?php echo number_format($product_data['price'], 0, ',', '.'); ?>
                        </div>
                    </div>

                    <!-- Color Options -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white">Color</h3>
                        <div class="flex gap-4">
                            <div class="color-option active w-12 h-12 rounded-full bg-blue-600 cursor-pointer border-2 border-transparent" onclick="selectColor(this)"></div>
                            <div class="color-option w-12 h-12 rounded-full bg-gray-800 cursor-pointer border-2 border-transparent" onclick="selectColor(this)"></div>
                            <div class="color-option w-12 h-12 rounded-full bg-red-600 cursor-pointer border-2 border-transparent" onclick="selectColor(this)"></div>
                            <div class="color-option w-12 h-12 rounded-full bg-green-600 cursor-pointer border-2 border-transparent" onclick="selectColor(this)"></div>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">Size</h3>
                            <button class="text-blue-400 hover:text-blue-300 transition flex items-center gap-2">
                                <i class="fas fa-ruler"></i>
                                Size Guide
                            </button>
                        </div>
                        <div class="grid grid-cols-4 gap-3">
                            <?php 
                            $sizes = ['EU 38', 'EU 39', 'EU 40', 'EU 41', 'EU 42', 'EU 43', 'EU 44', 'EU 45'];
                            foreach ($sizes as $size): ?>
                                <button class="size-btn px-4 py-3 rounded-xl text-center text-white font-medium" onclick="selectSize(this)">
                                    <?php echo $size; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white">Quantity</h3>
                        <div class="flex items-center gap-4">
                            <button class="glass-effect w-12 h-12 rounded-xl flex items-center justify-center text-white hover:bg-blue-600 transition" onclick="decreaseQty()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span id="quantity" class="text-2xl font-semibold text-white px-4">1</span>
                            <button class="glass-effect w-12 h-12 rounded-xl flex items-center justify-center text-white hover:bg-blue-600 transition" onclick="increaseQty()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4 pt-6">
                        <button class="btn-primary w-full py-4 rounded-2xl font-semibold text-white text-lg">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Add to Bag
                        </button>
                        <button class="btn-secondary w-full py-4 rounded-2xl font-semibold text-white text-lg">
                            <i class="far fa-heart mr-2"></i>
                            Add to Favourite
                        </button>
                    </div>

                    <!-- Product Features -->
                    <div class="glass-effect rounded-2xl p-6 space-y-4">
                        <h3 class="text-xl font-semibold text-white">Features</h3>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-300">
                                <i class="fas fa-check-circle text-blue-400"></i>
                                Premium materials and craftsmanship
                            </li>
                            <li class="flex items-center gap-3 text-gray-300">
                                <i class="fas fa-check-circle text-blue-400"></i>
                                Advanced cushioning technology
                            </li>
                            <li class="flex items-center gap-3 text-gray-300">
                                <i class="fas fa-check-circle text-blue-400"></i>
                                Breathable and comfortable design
                            </li>
                            <li class="flex items-center gap-3 text-gray-300">
                                <i class="fas fa-check-circle text-blue-400"></i>
                                Durable construction for long-lasting wear
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- Reviews Section -->
    <div class="container mx-auto px-6 py-16">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-8">Customer Reviews</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Review 1 -->
                <div class="review-card rounded-2xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-4">
                            JD
                        </div>
                        <div>
                            <h4 class="font-semibold text-white">John Doe</h4>
                            <div class="flex star-rating text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-300">
                        "These are the most comfortable sneakers I've ever worn! The cushioning is amazing and they look great with everything. Definitely worth the price."
                    </p>
                    <p class="text-gray-500 text-sm mt-3">Posted on June 15, 2023</p>
                </div>

                <!-- Review 2 -->
                <div class="review-card rounded-2xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-4">
                            AS
                        </div>
                        <div>
                            <h4 class="font-semibold text-white">Alice Smith</h4>
                            <div class="flex star-rating text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-300">
                        "Perfect for daily runs. Great support and breathability. They run slightly large, so consider sizing down half a size."
                    </p>
                    <p class="text-gray-500 text-sm mt-3">Posted on May 28, 2023</p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <button class="btn-primary px-8 py-3 rounded-xl font-semibold">
                    View All Reviews
                </button>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="container mx-auto px-6 py-16">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-8">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Product 1 -->
                <div class="related-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80" 
                             alt="Wshoes Sport" class="w-full h-48 object-cover">
                        <div class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                            NEW
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-2 text-white">Wshooes Sport</h3>
                        <p class="text-gray-400 text-sm mb-3">Lightweight Training Shoes</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white text-lg">Rp 1.299.000</span>
                            <button class="text-blue-400 hover:text-blue-300 transition">
                                <i class="fas fa-shopping-cart text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="related-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600269452121-1f5d14148cd6?w=400&q=80" 
                             alt="Wshoes Classic" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-2 text-white">Wshooes Classic</h3>
                        <p class="text-gray-400 text-sm mb-3">Everyday Casual Sneakers</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white text-lg">Rp 1.149.000</span>
                            <button class="text-blue-400 hover:text-blue-300 transition">
                                <i class="fas fa-shopping-cart text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="related-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&q=80" 
                             alt="Wshoes Ultra" class="w-full h-48 object-cover">
                        <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            SALE
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-2 text-white">Wshooes Ultra</h3>
                        <p class="text-gray-400 text-sm mb-3">Premium Running Shoes</p>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-white text-lg">Rp 1.549.000</span>
                                <span class="text-gray-500 text-sm line-through ml-2">Rp 1.899.000</span>
                            </div>
                            <button class="text-blue-400 hover:text-blue-300 transition">
                                <i class="fas fa-shopping-cart text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="related-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb89e?w=400&q=80" 
                             alt="Wshoes Lite" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-2 text-white">Wshooes Lite</h3>
                        <p class="text-gray-400 text-sm mb-3">Minimalist Walking Shoes</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white text-lg">Rp 999.000</span>
                            <button class="text-blue-400 hover:text-blue-300 transition">
                                <i class="fas fa-shopping-cart text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="gradient-bg">
        <div class="container mx-auto px-6 py-16">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                            <i class="fas fa-shoe-prints text-blue-400"></i>
                            Wshooes
                        </h3>
                        <p class="text-gray-300 mb-6">
                            Premium footwear for every step of your journey. Comfort, style, and performance in every pair.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white transition">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white transition">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white transition">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white transition">
                                <i class="fab fa-youtube text-xl"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg mb-6 text-white">Shop</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Men's Shoes</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Women's Shoes</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Kids' Shoes</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">New Arrivals</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Best Sellers</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg mb-6 text-white">Support</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Customer Service</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Track Order</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Returns</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Shipping Info</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Size Guide</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg mb-6 text-white">Newsletter</h4>
                        <p class="text-gray-300 mb-4">
                            Get updates on new arrivals and exclusive offers.
                        </p>
                        <div class="flex">
                            <input type="email" placeholder="Enter your email" 
                                   class="glass-effect text-white placeholder-gray-400 px-4 py-3 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <button class="btn-primary px-6 py-3 rounded-r-xl">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        &copy; 2023 Wshooes. All rights reserved.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        let selectedQuantity = 1;

        function changeImage(src) {
            document.getElementById('mainImage').src = src;
            
            // Update thumbnail active state
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            event.target.classList.add('active');
        }

        function selectColor(element) {
            const colors = document.querySelectorAll('.color-option');
            colors.forEach(color => color.classList.remove('active'));
            element.classList.add('active');
        }

        function selectSize(element) {
            const sizes = document.querySelectorAll('.size-btn');
            sizes.forEach(size => size.classList.remove('active'));
            element.classList.add('active');
        }

        function decreaseQty() {
            if (selectedQuantity > 1) {
                selectedQuantity--;
                document.getElementById('quantity').textContent = selectedQuantity;
            }
        }

        function increaseQty() {
            selectedQuantity++;
            document.getElementById('quantity').textContent = selectedQuantity;
        }

        // Add to cart functionality
        function addToCart() {
            const selectedColor = document.querySelector('.color-option.active');
            const selectedSize = document.querySelector('.size-btn.active');
            
            if (!selectedSize) {
                alert('Please select a size');
                return;
            }
            
            // Simulate cart addition
            alert(`Added ${selectedQuantity} item(s) to cart!`);
            
            // Update cart counter
            const cartCounter = document.querySelector('.fa-shopping-cart').nextElementSibling;
            if (cartCounter) {
                const currentCount = parseInt(cartCounter.textContent) || 0;
                cartCounter.textContent = currentCount + selectedQuantity;
            }
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart button
            const addToCartBtn = document.querySelector('.btn-primary');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', addToCart);
            }
            
            // Favourite button
            const favouriteBtn = document.querySelector('.btn-secondary');
            if (favouriteBtn) {
                favouriteBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.background = 'rgba(239, 68, 68, 0.2)';
                        this.style.borderColor = '#ef4444';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.background = 'rgba(59, 130, 246, 0.1)';
                        this.style.borderColor = 'rgba(59, 130, 246, 0.3)';
                    }
                });
            }
        });
    </script>
</body>
</html>