<?php
session_start();
require_once '../config/connection.php';
require_once '../models/Cart.php';
require_once '../models/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$session_id = session_id();

// Initialize cart and get cart items
$cart = new Cart();
$cart_items = $cart->getCartItems($session_id, $user_id);
$cart_total = $cart->getCartTotal($session_id, $user_id);
$cart_count = $cart->getCartCount($session_id, $user_id);

// Get user info 
$user = new User();
$user->id = $user_id;
$user_info = $user->read_single();

// If cart is empty, redirect to products
if (empty($cart_items)) {
    header('Location: all_product.php?message=cart_empty');
    exit();
}

// Calculate totals
$subtotal = $cart_total;
$shipping_cost = 25000; // Default shipping cost
$tax = 0;
$discount = 0;
$final_total = $subtotal + $shipping_cost - $discount;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Wshooes</title>
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
        .glass-card {
            background: rgba(30, 58, 138, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            background: rgba(30, 58, 138, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
        }
        .input-dark {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: white;
        }
        .input-dark:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        .input-dark::placeholder {
            color: rgba(255, 255, 255, 0.5);
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
                <div class="flex items-center space-x-4">
                    <span class="text-lg font-medium">Checkout</span>
                    <div class="w-10 h-10 bg-accent/20 rounded-full flex items-center justify-center border border-accent/30">
                        <span class="text-accent font-bold"><?php echo $cart_count; ?></span>
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
                <div class="glass-card rounded-xl p-6 mb-6">
                    <h2 class="text-2xl font-bold gradient-text mb-6">
                        <i class="fas fa-shipping-fast mr-2"></i>Informasi Pengiriman
                    </h2>
                    
                    <form id="checkoutForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Depan</label>
                                <input type="text" id="first-name" name="first_name" required 
                                       value="<?php echo isset($user_info['full_name']) ? explode(' ', $user_info['full_name'])[0] : ''; ?>"
                                       class="w-full input-dark rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Belakang</label>
                                <input type="text" id="last-name" name="last_name" required
                                       value="<?php echo isset($user_info['full_name']) && count(explode(' ', $user_info['full_name'])) > 1 ? implode(' ', array_slice(explode(' ', $user_info['full_name']), 1)) : ''; ?>"
                                       class="w-full input-dark rounded-lg px-4 py-2">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="3" required 
                                      class="w-full input-dark rounded-lg px-4 py-2"
                                      placeholder="Masukkan alamat lengkap Anda"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Kota</label>
                                <input type="text" id="city" name="city" required 
                                       class="w-full input-dark rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Kode Pos</label>
                                <input type="text" id="postal-code" name="postal_code" required 
                                       class="w-full input-dark rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Provinsi</label>
                                <input type="text" id="province" name="province" required 
                                       class="w-full input-dark rounded-lg px-4 py-2">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" required 
                                   value="<?php echo $user_info['phone_number'] ?? ''; ?>"
                                   class="w-full input-dark rounded-lg px-4 py-2">
                        </div>
                        
                        <div class="pt-4 border-t border-accent/20">
                            <h3 class="text-lg font-semibold gradient-text mb-4">
                                <i class="fas fa-truck mr-2"></i>Metode Pengiriman
                            </h3>
                            <div class="space-y-3">
                                <label class="flex items-center space-x-3 p-4 glass-card rounded-xl cursor-pointer hover:bg-accent/5 transition-colors">
                                    <input type="radio" name="shipping_method" value="regular" data-cost="25000" checked class="text-accent">
                                    <div class="flex-1">
                                        <div class="font-medium">Regular (3-5 hari)</div>
                                        <div class="text-sm text-gray-400">Pengiriman standar</div>
                                    </div>
                                    <div class="text-accent font-bold">Rp 25.000</div>
                                </label>
                                <label class="flex items-center space-x-3 p-4 glass-card rounded-xl cursor-pointer hover:bg-accent/5 transition-colors">
                                    <input type="radio" name="shipping_method" value="express" data-cost="50000" class="text-accent">
                                    <div class="flex-1">
                                        <div class="font-medium">Express (1-2 hari)</div>
                                        <div class="text-sm text-gray-400">Pengiriman cepat</div>
                                    </div>
                                    <div class="text-accent font-bold">Rp 50.000</div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Payment Method Section -->
                <div class="glass-card rounded-xl p-6">
                    <h2 class="text-2xl font-bold gradient-text mb-6">
                        <i class="fas fa-credit-card mr-2"></i>Metode Pembayaran
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 glass-card rounded-xl bg-accent/5">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-receipt text-2xl text-accent"></i>
                                <div>
                                    <h3 class="font-semibold text-white">Bayar di Tempat (COD)</h3>
                                    <p class="text-sm text-gray-400">Bayar saat barang diterima</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-yellow-900/20 border border-yellow-500/20 rounded-xl">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-info-circle text-yellow-500"></i>
                                <p class="text-sm text-yellow-200">
                                    Setelah checkout, Anda akan mendapat struk pembayaran digital yang bisa ditunjukkan kepada kurir.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Section -->
            <div class="lg:w-1/3">
                <div class="glass-card rounded-xl p-6 sticky top-8">
                    <h2 class="text-xl font-bold gradient-text mb-4">
                        <i class="fas fa-shopping-cart mr-2"></i>Ringkasan Pesanan
                    </h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-3 pb-3 border-b border-accent/20">
                            <img src="../assets/uploads/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                 class="w-16 h-16 object-cover rounded-lg border border-accent/20">
                            <div class="flex-1">
                                <h4 class="font-medium"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p class="text-sm text-gray-400">
                                    <?php echo htmlspecialchars($item['size']); ?> | <?php echo htmlspecialchars($item['color']); ?>
                                </p>
                                <p class="text-sm text-gray-400">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="text-accent font-medium">
                                Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Subtotal (<?php echo $cart_count; ?> item)</span>
                            <span class="font-medium">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Ongkos Kirim</span>
                            <span class="font-medium" id="shipping-cost">Rp <?php echo number_format($shipping_cost, 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Diskon</span>
                            <span class="font-medium text-green-400">- Rp <?php echo number_format($discount, 0, ',', '.'); ?></span>
                        </div>
                        <div class="border-t border-accent/20 pt-3 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span id="final-total" class="text-accent">Rp <?php echo number_format($final_total, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <button onclick="processOrder()" 
                            class="w-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white py-3 rounded-xl font-bold transition-opacity shadow-lg text-lg">
                        <i class="fas fa-credit-card mr-2"></i>Proses Pesanan
                    </button>
                    
                    <div class="mt-4 text-center text-sm text-gray-400">
                        <p>Dengan melanjutkan, Anda menyetujui <a href="#" class="text-accent hover:underline">Syarat & Ketentuan</a> kami</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="glass-card rounded-xl shadow-xl max-w-md w-full mx-4 max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold gradient-text">Struk Pembayaran</h3>
                    <button onclick="closeReceipt()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="receiptContent" class="glass-card rounded-xl p-4">
                    <!-- Receipt content will be generated here -->
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button onclick="printReceipt()" 
                            class="flex-1 bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg font-medium hover:opacity-90">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <button onclick="downloadReceipt()" 
                            class="flex-1 bg-gradient-to-r from-dark to-darkBlue text-white py-2 px-4 rounded-lg font-medium hover:opacity-90">
                        <i class="fas fa-download mr-2"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentShippingCost = <?php echo $shipping_cost; ?>;
        const subtotal = <?php echo $subtotal; ?>;
        const discount = <?php echo $discount; ?>;
        
        // Update total when shipping method changes
        document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                currentShippingCost = parseInt(this.dataset.cost);
                updateTotal();
            });
        });
        
        function updateTotal() {
            const newTotal = subtotal + currentShippingCost - discount;
            document.getElementById('shipping-cost').textContent = 'Rp ' + currentShippingCost.toLocaleString('id-ID');
            document.getElementById('final-total').textContent = 'Rp ' + newTotal.toLocaleString('id-ID');
        }
        
        function processOrder() {
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Show loading
            const button = event.target;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            button.disabled = true;
            
            // Create order
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    generateReceipt(data.order);
                    document.getElementById('receiptModal').classList.remove('hidden');
                    document.getElementById('receiptModal').classList.add('flex');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pesanan');
            })
            .finally(() => {
                button.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Proses Pesanan';
                button.disabled = false;
            });
        }
        
        function generateReceipt(order) {
            const receiptContent = document.getElementById('receiptContent');
            const now = new Date().toLocaleString('id-ID');
            
            receiptContent.innerHTML = `
                <div class="text-center mb-4">
                    <h2 class="text-2xl font-bold text-secondary">WSHOOES</h2>
                    <p class="text-sm text-gray-500">Struk Pembayaran Digital</p>
                </div>
                
                <div class="border-b border-gray-300 pb-3 mb-3">
                    <div class="flex justify-between text-sm">
                        <span>No. Order:</span>
                        <span class="font-bold">${order.order_number}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Tanggal:</span>
                        <span>${now}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Pelanggan:</span>
                        <span>${order.customer_name}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="font-bold text-sm mb-2">DETAIL PESANAN:</h4>
                    ${order.items.map(item => `
                        <div class="flex justify-between text-sm mb-1">
                            <span>${item.name} (${item.qty}x)</span>
                            <span>Rp ${item.total.toLocaleString('id-ID')}</span>
                        </div>
                    `).join('')}
                </div>
                
                <div class="border-t border-gray-300 pt-3">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal:</span>
                        <span>Rp ${order.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Ongkir:</span>
                        <span>Rp ${order.shipping.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t border-gray-300 pt-2 mt-2">
                        <span>TOTAL:</span>
                        <span>Rp ${order.total.toLocaleString('id-ID')}</span>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-yellow-50 rounded text-center">
                    <p class="text-sm font-medium text-yellow-800">BAYAR DI TEMPAT (COD)</p>
                    <p class="text-xs text-yellow-600 mt-1">Tunjukkan struk ini kepada kurir saat barang diantar</p>
                </div>
                
                <div class="mt-4 text-center text-xs text-gray-500">
                    <p>Terima kasih telah berbelanja di Wshooes!</p>
                    <p>Hubungi: +62 123 456 7890</p>
                </div>
            `;
        }
        
        function closeReceipt() {
            document.getElementById('receiptModal').classList.add('hidden');
            document.getElementById('receiptModal').classList.remove('flex');
            // Redirect to order confirmation or home page
            window.location.href = 'order_confirmation.php';
        }
        
        function printReceipt() {
            const printContent = document.getElementById('receiptContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Struk Pembayaran - Wshooes</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .text-center { text-align: center; }
                        .font-bold { font-weight: bold; }
                        .text-sm { font-size: 14px; }
                        .text-xs { font-size: 12px; }
                        .border-t { border-top: 1px solid #ccc; }
                        .border-b { border-bottom: 1px solid #ccc; }
                        .pt-3 { padding-top: 12px; }
                        .pb-3 { padding-bottom: 12px; }
                        .mb-3 { margin-bottom: 12px; }
                        .mt-4 { margin-top: 16px; }
                        .bg-yellow-50 { background-color: #fffbeb; }
                        .text-yellow-800 { color: #92400e; }
                        .text-yellow-600 { color: #d97706; }
                        .text-gray-500 { color: #6b7280; }
                        .flex { display: flex; }
                        .justify-between { justify-content: space-between; }
                    </style>
                </head>
                <body>${printContent}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
        
        function downloadReceipt() {
            // Simple download as text file
            const receiptText = document.getElementById('receiptContent').innerText;
            const blob = new Blob([receiptText], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'struk-pembayaran-wshooes.txt';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</body>
</html>
