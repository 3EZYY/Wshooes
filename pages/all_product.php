<?php
session_start();
require_once '../config/connection.php';
require_once '../models/Product.php';
require_once '../models/Cart.php';

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Initialize product model
$product = new Product();

// Get all products
$products = $product->read();

// Get cart count for display
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart = new Cart();
    $user_id = $_SESSION['user_id'];
    $session_id = session_id();
    $cart_count = $cart->getCartCount($session_id, $user_id);
}

// Handle categories
$categories = ['all', 'sneakers', 'casual', 'sport', 'running'];
$selected_category = $_GET['category'] ?? 'all';

// Filter products by category if needed
if ($selected_category !== 'all') {
    $products = $product->get_by_category($selected_category);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Wshooes</title>
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
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .filter-btn.active {
            background: linear-gradient(45deg, #1e40af, #3b82f6);
            color: white;
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
                    <a href="../index.php" class="hover:text-accent transition-colors font-medium">Home</a>
                    <a href="all_product.php" class="text-accent font-medium">Products</a>
                    <a href="collection.php" class="hover:text-accent transition-colors font-medium">Collections</a>
                    <a href="about.php" class="hover:text-accent transition-colors font-medium">About</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="cart_page.php" class="relative hover:text-accent transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                            <?php echo $cart_count; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user_profile.php" class="hover:text-accent transition-colors">
                            <i class="fas fa-user-circle text-xl"></i>
                        </a>
                        <a href="../config/auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="../config/auth/login.php" class="bg-accent hover:bg-blue-600 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            Login
                        </a>
                    <?php endif; ?>
                    
                    <button class="md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Category Filter -->
        <div class="flex flex-wrap gap-4 mb-8">
            <?php foreach ($categories as $category): ?>
            <button class="filter-btn px-6 py-2 rounded-full border border-accent/30 hover:border-accent transition-colors <?php echo $selected_category === $category ? 'active' : ''; ?>"
                    onclick="window.location.href='?category=<?php echo $category; ?>'">
                <?php echo ucfirst($category); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="product-card rounded-xl overflow-hidden">
                <div class="relative">
                    <img src="../assets/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
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
                        <span class="text-sm text-gray-400 ml-2">(<?php echo $product['review_count']; ?> reviews)</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <?php if ($product['discount_price'] > 0): ?>
                                <p class="text-xl font-bold text-accent">Rp <?php echo number_format($product['discount_price'], 0, ',', '.'); ?></p>
                                <p class="text-sm text-gray-400 line-through">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <?php else: ?>
                                <p class="text-xl font-bold text-accent">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="addToWishlist(<?php echo $product['id']; ?>)" 
                                    class="p-2 rounded-full border border-accent/30 hover:bg-accent/10 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button onclick="showProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>)" 
                            class="w-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-2 px-4 rounded-lg transition flex items-center justify-center space-x-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Add to Cart</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-darkBlue rounded-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white" id="modalProductName"></h3>
                <button onclick="closeProductModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <img id="modalProductImage" src="" alt="" class="w-full h-64 object-cover rounded-lg">
            </div>
            
            <form id="addToCartForm" onsubmit="handleAddToCart(event)" class="space-y-4">
                <input type="hidden" id="modalProductId" name="product_id">
                <input type="hidden" id="modalProductPrice" name="price">
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Size</label>
                    <div class="grid grid-cols-4 gap-2" id="sizeOptions"></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Color</label>
                    <div class="grid grid-cols-4 gap-2" id="colorOptions"></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Quantity</label>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="updateQuantity(-1)" 
                                class="p-2 rounded-lg border border-accent/30 hover:bg-accent/10">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" 
                               class="w-20 text-center bg-transparent border border-accent/30 rounded-lg px-2 py-1 text-white">
                        <button type="button" onclick="updateQuantity(1)"
                                class="p-2 rounded-lg border border-accent/30 hover:bg-accent/10">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-3 rounded-lg transition flex items-center justify-center space-x-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Add to Cart</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-shoe-prints mr-2"></i> Wshooes
                    </h3>
                    <p class="text-gray-300">Your premium destination for the latest and greatest in footwear fashion.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Shop</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition">Men</a></li>
                        <li><a href="#" class="hover:text-white transition">Women</a></li>
                        <li><a href="#" class="hover:text-white transition">Kids</a></li>
                        <li><a href="#" class="hover:text-white transition">New Arrivals</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Help</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition">Customer Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Track Order</a></li>
                        <li><a href="#" class="hover:text-white transition">Returns & Exchanges</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i> 123 Shoe Street, NY 10001
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2"></i> (555) 123-4567
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
                <p>&copy; 2023 Wshooes. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Product modal functionality
        let currentProduct = null;
        const sizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        const colors = ['Black', 'White', 'Red', 'Blue', 'Grey', 'Green'];

        function showProductModal(product) {
            currentProduct = product;
            document.getElementById('modalProductName').textContent = product.name;
            document.getElementById('modalProductImage').src = '../assets/uploads/products/' + product.main_image;
            document.getElementById('modalProductId').value = product.id;
            document.getElementById('modalProductPrice').value = product.discount_price > 0 ? product.discount_price : product.price;
            
            // Generate size options
            const sizeOptions = document.getElementById('sizeOptions');
            sizeOptions.innerHTML = sizes.map(size => `
                <button type="button" onclick="selectSize('${size}')" 
                        class="size-btn p-2 rounded-lg border border-accent/30 hover:bg-accent/10 transition-colors">
                    ${size}
                </button>
            `).join('');
            
            // Generate color options
            const colorOptions = document.getElementById('colorOptions');
            colorOptions.innerHTML = colors.map(color => `
                <button type="button" onclick="selectColor('${color}')" 
                        class="color-btn p-2 rounded-lg border border-accent/30 hover:bg-accent/10 transition-colors">
                    ${color}
                </button>
            `).join('');
            
            document.getElementById('productModal').classList.remove('hidden');
            document.getElementById('productModal').classList.add('flex');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
            document.getElementById('productModal').classList.remove('flex');
            currentProduct = null;
        }

        function selectSize(size) {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.classList.remove('bg-accent', 'text-white');
            });
            event.target.classList.add('bg-accent', 'text-white');
            selectedSize = size;
        }

        function selectColor(color) {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.classList.remove('bg-accent', 'text-white');
            });
            event.target.classList.add('bg-accent', 'text-white');
            selectedColor = color;
        }

        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            const newValue = Math.max(1, parseInt(quantityInput.value) + change);
            quantityInput.value = newValue;
        }

        let selectedSize = '';
        let selectedColor = '';

        function handleAddToCart(event) {
            event.preventDefault();
            
            if (!selectedSize || !selectedColor) {
                alert('Please select both size and color');
                return;
            }
            
            const formData = new FormData(event.target);
            formData.append('size', selectedSize);
            formData.append('color', selectedColor);
            
            fetch('../controllers/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    const cartCountElement = document.querySelector('.fa-shopping-cart + span');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                    }
                    
                    closeProductModal();
                    alert('Product added to cart successfully!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add product to cart');
            });
        }

        function addToWishlist(productId) {
            // Implement wishlist functionality
            alert('Wishlist feature coming soon!');
        }
    </script>
</body>
</html>