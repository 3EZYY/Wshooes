<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wshooes - Premium Footwear</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/Wshooes/assets/css/landing_page.css">
</head>
<body class="bg-gray-50 text-gray-800 transition-all">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 dark:bg-gray-900 dark:border-b dark:border-gray-800">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shoe-prints text-2xl text-indigo-600 dark:text-indigo-400"></i>
                <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">Wshooes</h1>
            </div>
            
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a>
                <a href="#" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Products</a>
                <a href="#" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Collections</a>
                <a href="#" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">About</a>
                <a href="#" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contact</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i class="fas fa-moon text-gray-600 dark:hidden dark:text-gray-300"></i>
                </button>
                
                <button id="cart-btn" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 relative">
                    <i class="fas fa-shopping-cart text-gray-600 dark:text-gray-300"></i>
                    <span class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </button>
                
                <a href="/Wshooes/auth/login.php" class="hidden md:block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition text-center">
                    Sign In
                </a>
                
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i class="fas fa-bars text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-900 border-t dark:border-gray-800">
            <div class="container mx-auto px-4 py-3 flex flex-col space-y-3">
                <a href="#" class="font-medium py-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a>
                <a href="#" class="font-medium py-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Products</a>
                <a href="#" class="font-medium py-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Collections</a>
                <a href="#" class="font-medium py-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">About</a>
                <a href="#" class="font-medium py-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Contact</a>
                <a href="/Wshooes/auth/login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition w-full block text-center">
                    Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-20 dark:from-indigo-700 dark:to-purple-800">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0 animate-fade-in">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Step Into Style</h2>
                <p class="text-xl mb-8 opacity-90">Discover premium footwear that combines comfort and fashion for every occasion.</p>
                <div class="flex space-x-4">
                    <button class="bg-white text-indigo-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-medium transition">
                        Shop Now
                    </button>
                    <button class="border-2 border-white hover:bg-white hover:text-indigo-600 px-6 py-3 rounded-lg font-medium transition">
                        Explore Collections
                    </button>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center animate-fade-in delay-100">
                <img src="https://images.unsplash.com/photo-1600269452121-1f5d1415f5b1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                     alt="Premium Shoes" 
                     class="rounded-lg shadow-2xl max-w-md w-full h-auto">
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12 animate-fade-in">
                <h2 class="text-3xl font-bold mb-4 dark:text-white">Featured Products</h2>
                <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">Handpicked selection of our most popular and trending footwear</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Product 1 -->
                <div class="shoe-card bg-white rounded-lg overflow-hidden shadow-md dark:bg-gray-800 transition-all animate-fade-in delay-200">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                             alt="Running Shoes" 
                             class="w-full h-64 object-cover">
                        <div class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded">
                            New
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 dark:text-white">Nike Air Max</h3>
                        <p class="text-gray-600 text-sm mb-3 dark:text-gray-300">Running Shoes</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800 dark:text-white">$129.99</span>
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="shoe-card bg-white rounded-lg overflow-hidden shadow-md dark:bg-gray-800 transition-all animate-fade-in delay-300">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                             alt="Casual Shoes" 
                             class="w-full h-64 object-cover">
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                            Sale
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 dark:text-white">Adidas Originals</h3>
                        <p class="text-gray-600 text-sm mb-3 dark:text-gray-300">Casual Sneakers</p>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gray-800 dark:text-white">$89.99</span>
                                <span class="text-sm text-gray-500 line-through ml-2 dark:text-gray-400">$109.99</span>
                            </div>
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="shoe-card bg-white rounded-lg overflow-hidden shadow-md dark:bg-gray-800 transition-all animate-fade-in delay-400">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                             alt="Basketball Shoes" 
                             class="w-full h-64 object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 dark:text-white">Jordan Retro</h3>
                        <p class="text-gray-600 text-sm mb-3 dark:text-gray-300">Basketball Shoes</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800 dark:text-white">$159.99</span>
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="shoe-card bg-white rounded-lg overflow-hidden shadow-md dark:bg-gray-800 transition-all animate-fade-in delay-500">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600269452121-1f5d1415f5b1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" 
                             alt="Premium Shoes" 
                             class="w-full h-64 object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 dark:text-white">Puma RS-X</h3>
                        <p class="text-gray-600 text-sm mb-3 dark:text-gray-300">Lifestyle Sneakers</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800 dark:text-white">$99.99</span>
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <button class="border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white px-6 py-2 rounded-lg font-medium transition dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-indigo-600 dark:hover:text-white">
                    View All Products
                </button>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 dark:text-white">Why Choose Wshooes</h2>
                <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">We're committed to providing the best footwear experience</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm dark:bg-gray-700 transition-all hover:shadow-md">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4 dark:bg-indigo-900">
                        <i class="fas fa-medal text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 dark:text-white">Premium Quality</h3>
                    <p class="text-gray-600 dark:text-gray-300">Our shoes are crafted with the finest materials for durability and comfort.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm dark:bg-gray-700 transition-all hover:shadow-md">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4 dark:bg-indigo-900">
                        <i class="fas fa-truck text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 dark:text-white">Fast Shipping</h3>
                    <p class="text-gray-600 dark:text-gray-300">Get your order delivered to your doorstep within 2-3 business days.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm dark:bg-gray-700 transition-all hover:shadow-md">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4 dark:bg-indigo-900">
                        <i class="fas fa-headset text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 dark:text-white">24/7 Support</h3>
                    <p class="text-gray-600 dark:text-gray-300">Our customer service team is always ready to assist you with any inquiries.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 bg-indigo-600 text-white dark:bg-indigo-800">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
            <p class="mb-8 max-w-2xl mx-auto opacity-90">Subscribe to our newsletter for exclusive offers, new arrivals, and style tips.</p>
            
            <div class="max-w-md mx-auto flex">
                <input type="email" placeholder="Your email address" class="flex-grow px-4 py-3 rounded-l-lg focus:outline-none text-gray-800">
                <button class="bg-indigo-800 hover:bg-indigo-900 px-6 py-3 rounded-r-lg font-medium transition dark:bg-indigo-700 dark:hover:bg-indigo-600">
                    Subscribe
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-shoe-prints text-2xl text-indigo-400"></i>
                        <h3 class="text-2xl font-bold text-indigo-400">Wshooes</h3>
                    </div>
                    <p class="text-gray-400 mb-4">Premium footwear for every step of your journey.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Shop</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Men's Shoes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Women's Shoes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Kids' Shoes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">New Arrivals</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Best Sellers</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Help</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Customer Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Track Order</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Returns & Exchanges</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">FAQs</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>123 Shoe Street, Footwear City, 10100</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@wshooes.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2023 Wshooes. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="login-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 invisible">
        <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md dark:bg-gray-800">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold dark:text-white">Sign In</h3>
                    <button id="close-login" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="login-form" class="space-y-4">
                    <div>
                        <label for="login-email" class="block text-sm font-medium mb-1 dark:text-gray-300">Email</label>
                        <input type="email" id="login-email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label for="login-password" class="block text-sm font-medium mb-1 dark:text-gray-300">Password</label>
                        <input type="password" id="login-password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember-me" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                        </div>
                        
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Don't have an account? 
                        <button id="switch-to-signup" class="text-indigo-600 hover:text-indigo-500 font-medium dark:text-indigo-400 dark:hover:text-indigo-300">Sign up</button>
                    </p>
                </div>
                
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500 dark:bg-gray-800 dark:text-gray-400">Or continue with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <i class="fab fa-google mr-2"></i> Google
                        </button>
                        
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <i class="fab fa-facebook-f mr-2"></i> Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signup-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 opacity-0 invisible">
        <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md dark:bg-gray-800">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold dark:text-white">Create Account</h3>
                    <button id="close-signup" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="signup-form" class="space-y-4">
                    <div>
                        <label for="signup-name" class="block text-sm font-medium mb-1 dark:text-gray-300">Full Name</label>
                        <input type="text" id="signup-name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label for="signup-email" class="block text-sm font-medium mb-1 dark:text-gray-300">Email</label>
                        <input type="email" id="signup-email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label for="signup-password" class="block text-sm font-medium mb-1 dark:text-gray-300">Password</label>
                        <input type="password" id="signup-password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div>
                        <label for="signup-confirm" class="block text-sm font-medium mb-1 dark:text-gray-300">Confirm Password</label>
                        <input type="password" id="signup-confirm" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" required>
                        <label for="terms" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Terms</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition">
                        Create Account
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Already have an account? 
                        <button id="switch-to-login" class="text-indigo-600 hover:text-indigo-500 font-medium dark:text-indigo-400 dark:hover:text-indigo-300">Sign in</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 dark:bg-gray-800">
        <div class="h-full flex flex-col">
            <div class="p-4 border-b flex justify-between items-center dark:border-gray-700">
                <h3 class="text-lg font-semibold dark:text-white">Your Cart (3)</h3>
                <button id="close-cart" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex-grow overflow-y-auto p-4">
                <!-- Cart Item 1 -->
                <div class="flex items-center mb-4 pb-4 border-b dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden dark:bg-gray-700">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" 
                             alt="Nike Air Max" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="font-medium dark:text-white">Nike Air Max</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Size: 10</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="font-medium dark:text-white">$129.99</span>
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">-</button>
                                <span class="px-2 text-sm">1</span>
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Item 2 -->
                <div class="flex items-center mb-4 pb-4 border-b dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden dark:bg-gray-700">
                        <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" 
                             alt="Adidas Originals" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="font-medium dark:text-white">Adidas Originals</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Size: 9</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="font-medium dark:text-white">$89.99</span>
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">-</button>
                                <span class="px-2 text-sm">1</span>
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Item 3 -->
                <div class="flex items-center mb-4 pb-4 border-b dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden dark:bg-gray-700">
                        <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" 
                             alt="Jordan Retro" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="font-medium dark:text-white">Jordan Retro</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Size: 10.5</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="font-medium dark:text-white">$159.99</span>
                            <div class="flex items-center border rounded">
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">-</button>
                                <span class="px-2 text-sm">1</span>
                                <button class="px-2 py-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t dark:border-gray-700">
                <div class="flex justify-between mb-4">
                    <span class="font-medium dark:text-white">Subtotal</span>
                    <span class="font-medium dark:text-white">$379.97</span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="font-medium dark:text-white">Shipping</span>
                    <span class="font-medium dark:text-white">Free</span>
                </div>
                <div class="flex justify-between mb-6">
                    <span class="font-bold dark:text-white">Total</span>
                    <span class="font-bold dark:text-white">$379.97</span>
                </div>
                
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-medium transition dark:bg-indigo-700 dark:hover:bg-indigo-600">
                    Proceed to Checkout
                </button>
                
                <p class="text-center text-sm text-gray-500 mt-4 dark:text-gray-400">
                    or <button id="continue-shopping" class="text-indigo-600 hover:text-indigo-500 font-medium dark:text-indigo-400 dark:hover:text-indigo-300">Continue Shopping</button>
                </p>
            </div>
        </div>
    </div>
    <script src="/Wshooes/assets/js/landing_page.js"></script>
</body>
</html>