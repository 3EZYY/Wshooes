<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/Wshooes/assets/css/user_profile.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6b21a8',
                        secondary: '#1e3a8a',
                        accent: '#9333ea',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shoe-prints text-2xl"></i>
                <h1 class="text-2xl font-bold">Wshooes</h1>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a href="/Wshooes/index.php" class="hover:text-purple-200 transition">Home</a>
                <a href="#" class="hover:text-purple-200 transition">Products</a>
                <a href="#" class="hover:text-purple-200 transition">New Arrivals</a>
                <a href="#" class="hover:text-purple-200 transition">Sale</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-purple-200 transition">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </a>
                <a href="#" class="hover:text-purple-200 transition">
                    <i class="fas fa-bell text-xl"></i>
                </a>
                <button class="md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Profile Section -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="gradient-bg p-6 text-white text-center">
                        <div class="relative mx-auto w-32 h-32 rounded-full border-4 border-white mb-4 overflow-hidden">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile" class="w-full h-full object-cover">
                            <button class="absolute bottom-0 right-0 bg-accent rounded-full p-2 hover:bg-purple-800 transition">
                                <i class="fas fa-camera text-white"></i>
                            </button>
                        </div>
                        <h2 class="text-2xl font-bold">Sarah Johnson</h2>
                        <p class="text-purple-200">Premium Member</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-primary"></i> Personal Information
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Full Name</p>
                                    <p class="font-medium">Sarah Johnson</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium">sarah.johnson@example.com</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium">+1 (555) 123-4567</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Member Since</p>
                                    <p class="font-medium">March 2021</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-truck mr-2 text-primary"></i> Shipping Address
                            </h3>
                            <div>
                                <p class="text-sm text-gray-500">Primary Address</p>
                                <p class="font-medium">123 Main Street, Apt 4B</p>
                                <p class="font-medium">New York, NY 10001</p>
                                <p class="font-medium">United States</p>
                            </div>
                        </div>
                        
                        <button class="w-full bg-primary hover:bg-purple-900 text-white py-2 px-4 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Order History Section -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="gradient-bg p-6 text-white">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-history mr-3"></i> Order History
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Order Filter -->
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-2">
                            <div class="relative">
                                <select class="appearance-none bg-gray-100 border border-gray-300 rounded-lg py-2 px-4 pr-8 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option>All Orders</option>
                                    <option>Completed</option>
                                    <option>Processing</option>
                                    <option>Shipped</option>
                                    <option>Cancelled</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text" placeholder="Search orders..." class="bg-gray-100 border border-gray-300 rounded-lg py-2 px-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <button class="absolute right-0 top-0 h-full px-3 text-gray-500">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Order List -->
                        <div class="space-y-4">
                            <!-- Order 1 -->
                            <div class="order-card transition bg-white border border-gray-200 rounded-lg p-4 hover:border-primary">
                                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Order #</p>
                                        <p class="font-bold">WSH-2023-05678</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">June 12, 2023</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="font-bold text-primary">$189.95</p>
                                    </div>
                                    <div>
                                        <span class="status-badge bg-green-100 text-green-800">Delivered</span>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                    <img src="https://images.unsplash.com/photo-1600269452121-1f5d14148cd6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                    <div class="text-gray-500 text-sm">
                                        + 1 more item
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <button class="text-primary hover:text-purple-900 font-medium flex items-center">
                                        <i class="fas fa-redo-alt mr-2"></i> Buy Again
                                    </button>
                                    <button class="bg-primary hover:bg-purple-900 text-white py-2 px-4 rounded-lg transition flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> View Details
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Order 2 -->
                            <div class="order-card transition bg-white border border-gray-200 rounded-lg p-4 hover:border-primary">
                                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Order #</p>
                                        <p class="font-bold">WSH-2023-04532</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">May 28, 2023</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="font-bold text-primary">$129.99</p>
                                    </div>
                                    <div>
                                        <span class="status-badge bg-blue-100 text-blue-800">Shipped</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 mb-4">
                                    <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                </div>
                                
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <button class="text-primary hover:text-purple-900 font-medium flex items-center">
                                        <i class="fas fa-redo-alt mr-2"></i> Buy Again
                                    </button>
                                    <button class="bg-primary hover:bg-purple-900 text-white py-2 px-4 rounded-lg transition flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> View Details
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Order 3 -->
                            <div class="order-card transition bg-white border border-gray-200 rounded-lg p-4 hover:border-primary">
                                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Order #</p>
                                        <p class="font-bold">WSH-2023-03921</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">April 15, 2023</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="font-bold text-primary">$79.98</p>
                                    </div>
                                    <div>
                                        <span class="status-badge bg-yellow-100 text-yellow-800">Processing</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 mb-4">
                                    <img src="https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                </div>
                                
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <button class="text-primary hover:text-purple-900 font-medium flex items-center">
                                        <i class="fas fa-redo-alt mr-2"></i> Buy Again
                                    </button>
                                    <button class="bg-primary hover:bg-purple-900 text-white py-2 px-4 rounded-lg transition flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> View Details
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Order 4 -->
                            <div class="order-card transition bg-white border border-gray-200 rounded-lg p-4 hover:border-primary">
                                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Order #</p>
                                        <p class="font-bold">WSH-2023-02876</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">March 5, 2023</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="font-bold text-primary">$149.95</p>
                                    </div>
                                    <div>
                                        <span class="status-badge bg-red-100 text-red-800">Cancelled</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 mb-4">
                                    <img src="https://images.unsplash.com/photo-1605348532760-6753d2c43329?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                </div>
                                
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <button class="text-primary hover:text-purple-900 font-medium flex items-center">
                                        <i class="fas fa-redo-alt mr-2"></i> Buy Again
                                    </button>
                                    <button class="bg-primary hover:bg-purple-900 text-white py-2 px-4 rounded-lg transition flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="flex justify-center mt-8">
                            <nav class="flex items-center space-x-1">
                                <button class="px-3 py-1 rounded-full text-gray-500 hover:bg-gray-100">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="px-3 py-1 rounded-full bg-primary text-white">1</button>
                                <button class="px-3 py-1 rounded-full hover:bg-gray-100">2</button>
                                <button class="px-3 py-1 rounded-full hover:bg-gray-100">3</button>
                                <span class="px-2">...</span>
                                <button class="px-3 py-1 rounded-full hover:bg-gray-100">8</button>
                                <button class="px-3 py-1 rounded-full text-gray-500 hover:bg-gray-100">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
    <script src="/Wshooes/Js/user_profile.js"></script>
</body>
</html>