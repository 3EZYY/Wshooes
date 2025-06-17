<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/check_out.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',
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
                    <span class="text-sm font-medium">Checkout</span>
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                        <span class="text-primary font-bold">3</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Checkout Form Section -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-secondary mb-6">Informasi Pengiriman</h2>
                    
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Depan</label>
                                <input type="text" id="first-name" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                            </div>
                            <div>
                                <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Belakang</label>
                                <input type="text" id="last-name" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                            </div>
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea id="address" rows="3" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                <input type="text" id="city" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                            </div>
                            <div>
                                <label for="postal-code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" id="postal-code" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Negara</label>
                                <select id="country" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                                    <option>Indonesia</option>
                                    <option>Malaysia</option>
                                    <option>Singapore</option>
                                    <option>Thailand</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="tel" id="phone" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-secondary mb-4">Metode Pengiriman</h3>
                            <div class="space-y-3">
                                <div class="checkout-card payment-method border border-gray-200 rounded-lg p-4 cursor-pointer selected">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-6 rounded-full border-2 border-primary flex items-center justify-center">
                                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                            </div>
                                            <span class="font-medium">Reguler</span>
                                        </div>
                                        <span class="text-primary font-medium">Rp 25.000</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2 ml-9">Estimasi tiba dalam 3-5 hari kerja</p>
                                </div>
                                <div class="checkout-card payment-method border border-gray-200 rounded-lg p-4 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div class="w-3 h-3 rounded-full bg-transparent"></div>
                                            </div>
                                            <span class="font-medium">Express</span>
                                        </div>
                                        <span class="text-primary font-medium">Rp 50.000</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2 ml-9">Estimasi tiba dalam 1-2 hari kerja</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-secondary mb-6">Metode Pembayaran</h2>
                    
                    <div class="space-y-4">
                        <div class="checkout-card payment-method border border-gray-200 rounded-lg p-4 cursor-pointer selected">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full border-2 border-primary flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                                </div>
                                <i class="fab fa-cc-visa text-3xl text-blue-900"></i>
                                <span class="font-medium">Kartu Kredit/Debit</span>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="card-number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu</label>
                                    <input type="text" id="card-number" placeholder="1234 5678 9012 3456" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                                </div>
                                <div>
                                    <label for="card-name" class="block text-sm font-medium text-gray-700 mb-1">Nama di Kartu</label>
                                    <input type="text" id="card-name" placeholder="John Doe" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                                </div>
                                <div>
                                    <label for="expiry-date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa</label>
                                    <input type="text" id="expiry-date" placeholder="MM/YY" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                                </div>
                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" id="cvv" placeholder="123" class="input-field w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary">
                                </div>
                            </div>
                        </div>
                        
                        <div class="checkout-card payment-method border border-gray-200 rounded-lg p-4 cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-transparent"></div>
                                </div>
                                <i class="fas fa-university text-3xl text-secondary"></i>
                                <span class="font-medium">Transfer Bank</span>
                            </div>
                        </div>
                        
                        <div class="checkout-card payment-method border border-gray-200 rounded-lg p-4 cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-transparent"></div>
                                </div>
                                <i class="fas fa-wallet text-3xl text-purple-600"></i>
                                <span class="font-medium">E-Wallet</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Section -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-secondary mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal (3 item)</span>
                            <span class="font-medium">Rp 4.696.000</span>
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
                            <span>Rp 4.721.000</span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-medium text-secondary mb-2">Produk Anda</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
                                     alt="Running Shoes" class="w-12 h-12 rounded-lg object-cover">
                                <div class="ml-3">
                                    <p class="text-sm font-medium">Wshooes Runner Pro</p>
                                    <p class="text-xs text-gray-500">1 × Rp 1.299.000</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <img src="https://images.unsplash.com/photo-1600269452121-1f5d1414f7ce?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" 
                                     alt="Casual Shoes" class="w-12 h-12 rounded-lg object-cover">
                                <div class="ml-3">
                                    <p class="text-sm font-medium">Wshooes Urban Classic</p>
                                    <p class="text-xs text-gray-500">2 × Rp 899.000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button class="w-full bg-gradient-to-r from-primary to-purple-600 text-white py-3 rounded-lg font-bold hover:opacity-90 transition-opacity shadow-lg text-lg">
                        Bayar Sekarang
                    </button>
                    
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <p>Dengan melanjutkan, Anda menyetujui <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a> kami</p>
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

    <script src="../assets/Js/check_out.js"></script>
</body>
</html>