<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/cart_page.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6b46c1',
                        secondary: '#1e3a8a',
                        accent: '#a78bfa',
                    }
                }
            }
        }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="bg-gradient-to-r from-primary to-secondary text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shoe-prints text-2xl text-accent"></i>
                    <h1 class="text-2xl font-bold">Wshooes</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="hover:text-accent transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="#" class="hover:text-accent transition-colors">
                        <i class="fas fa-search"></i>
                    </a>
                    <a href="#" class="hover:text-accent transition-colors relative">
                        <i class="fas fa-heart"></i>
                        <span class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items Section -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-secondary">Keranjang Belanja</h2>
                        <span class="text-gray-600">3 item</span>
                    </div>
                    
                    <!-- Cart Items -->
                    <div class="space-y-6">
                        <!-- Item 1 -->
                        <div class="cart-item bg-white border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row gap-4">
                            <div class="w-full sm:w-1/4">
                                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                     alt="Running Shoes" class="w-full h-auto rounded-lg object-cover">
                            </div>
                            <div class="w-full sm:w-3/4 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary">Wshooes Runner Pro</h3>
                                    <p class="text-gray-600">Running Shoes</p>
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Ukuran: </span>
                                        <span class="text-sm font-medium">42</span>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center mb-3 sm:mb-0">
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-l-lg" onclick="updateQuantity('item1', -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span id="item1-quantity" class="bg-gray-100 px-4 py-1 text-center">1</span>
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-r-lg" onclick="updateQuantity('item1', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-lg font-bold text-primary">Rp 1.299.000</span>
                                        <button class="text-red-500 hover:text-red-700" onclick="removeItem('item1')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Item 2 -->
                        <div class="cart-item bg-white border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row gap-4">
                            <div class="w-full sm:w-1/4">
                                <img src="https://images.unsplash.com/photo-1600269452121-1f5d1414f7ce?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                     alt="Casual Shoes" class="w-full h-auto rounded-lg object-cover">
                            </div>
                            <div class="w-full sm:w-3/4 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary">Wshooes Urban Classic</h3>
                                    <p class="text-gray-600">Casual Shoes</p>
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Ukuran: </span>
                                        <span class="text-sm font-medium">40</span>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center mb-3 sm:mb-0">
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-l-lg" onclick="updateQuantity('item2', -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span id="item2-quantity" class="bg-gray-100 px-4 py-1 text-center">2</span>
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-r-lg" onclick="updateQuantity('item2', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-lg font-bold text-primary">Rp 899.000</span>
                                        <button class="text-red-500 hover:text-red-700" onclick="removeItem('item2')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Item 3 -->
                        <div class="cart-item bg-white border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row gap-4">
                            <div class="w-full sm:w-1/4">
                                <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                     alt="Basketball Shoes" class="w-full h-auto rounded-lg object-cover">
                            </div>
                            <div class="w-full sm:w-3/4 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-secondary">Wshooes Bounce Elite</h3>
                                    <p class="text-gray-600">Basketball Shoes</p>
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Ukuran: </span>
                                        <span class="text-sm font-medium">44</span>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center mb-3 sm:mb-0">
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-l-lg" onclick="updateQuantity('item3', -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span id="item3-quantity" class="bg-gray-100 px-4 py-1 text-center">1</span>
                                        <button class="quantity-btn bg-primary text-white px-3 py-1 rounded-r-lg" onclick="updateQuantity('item3', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-lg font-bold text-primary">Rp 1.599.000</span>
                                        <button class="text-red-500 hover:text-red-700" onclick="removeItem('item3')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200 flex justify-between">
                        <a href="#" class="text-primary font-medium hover:text-secondary transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Lanjutkan Belanja
                        </a>
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                            Perbarui Keranjang
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Section -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-secondary mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" id="subtotal">Rp 4.696.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Rp 25.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diskon</span>
                            <span class="font-medium text-green-600">- Rp 0</span>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-bold text-secondary">
                            <span>Total</span>
                            <span id="total">Rp 4.721.000</span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="promo" class="block text-sm font-medium text-gray-700 mb-2">Kode Promo</label>
                        <div class="flex">
                            <input type="text" id="promo" placeholder="Masukkan kode promo" 
                                   class="promo-input flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:border-primary">
                            <button class="bg-primary text-white px-4 py-2 rounded-r-lg hover:bg-purple-700 transition-colors">
                                Terapkan
                            </button>
                        </div>
                    </div>
                    
                    <button class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-lg font-bold hover:opacity-90 transition-opacity shadow-lg">
                        Lanjut ke Checkout
                    </button>
                    
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <p>Dengan melanjutkan, Anda menyetujui <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a> kami</p>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-medium text-secondary mb-2">Metode Pembayaran</h3>
                        <div class="flex space-x-2">
                            <div class="p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-cc-visa text-2xl text-blue-900"></i>
                            </div>
                            <div class="p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                            </div>
                            <div class="p-2 border border-gray-200 rounded-lg">
                                <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                            </div>
                            <div class="p-2 border border-gray-200 rounded-lg">
                                <i class="fas fa-qrcode text-2xl text-gray-600"></i>
                            </div>
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
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-shoe-prints text-2xl text-accent"></i>
                        <h3 class="text-xl font-bold">Wshooes</h3>
                    </div>
                    <p class="text-gray-300">Sepatu berkualitas dengan desain modern untuk gaya hidup aktif Anda.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Produk</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Bantuan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Pengiriman</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Pengembalian</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Status Pesanan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Hubungi Kami</h4>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-map-marker-alt text-accent"></i>
                            <span class="text-gray-300">Jl. Sepatu No. 123, Jakarta</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-phone text-accent"></i>
                            <span class="text-gray-300">+62 123 4567 890</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-envelope text-accent"></i>
                            <span class="text-gray-300">info@wshooes.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-600 mt-8 pt-6 text-center text-gray-300">
                <p>&copy; 2023 Wshooes. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="../assets/Js/cart_page.js"></script>
</body>
</html>