<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$order_id = (int)($_GET['id'] ?? 0);

if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// Get order details with user info
$order_query = "SELECT o.*, u.username, u.email, u.full_name, u.phone_number
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$items_query = "SELECT oi.*, p.name, p.main_image, p.price as current_price
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = "Order Details #" . $order_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Wshooes Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="ml-64 min-h-screen">
        <?php include __DIR__ . '/partials/header.php'; ?>
        
        <main class="p-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="orders.php" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
            
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #<?php echo $order['id']; ?></h1>
                        <p class="text-gray-600">Placed on <?php echo date('F d, Y \a\t H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            <?php 
                            switch($order['status']) {
                                case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                case 'shipped': echo 'bg-blue-100 text-blue-800'; break;
                                case 'processing': echo 'bg-yellow-100 text-yellow-800'; break;
                                case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                        <div class="text-2xl font-bold text-gray-900 mt-2">
                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>Customer Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($order['full_name'] ?: $order['username']); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($order['email']); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($order['phone_number'] ?: 'N/A'); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Username</label>
                            <p class="text-gray-900">@<?php echo htmlspecialchars($order['username']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-receipt mr-2"></i>Order Summary
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order ID</label>
                            <p class="text-gray-900">#<?php echo $order['id']; ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-gray-900"><?php echo ucfirst($order['status']); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Items Count</label>
                            <p class="text-gray-900"><?php echo count($order_items); ?> items</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Date</label>
                            <p class="text-gray-900"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-gray-900"><?php echo date('M d, Y H:i', strtotime($order['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-credit-card mr-2"></i>Payment Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Amount</label>
                            <p class="text-2xl font-bold text-gray-900">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Method</label>
                            <p class="text-gray-900">Cash on Delivery</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Status</label>
                            <p class="text-gray-900">
                                <?php echo $order['status'] === 'delivered' ? 'Paid' : 'Pending'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-shopping-bag mr-2"></i>Order Items
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if ($item['main_image']): ?>
                                            <img src="../assets/uploads/products/<?php echo $item['main_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 class="w-16 h-16 object-cover rounded-lg mr-4">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div class="text-sm text-gray-500">Product ID: <?php echo $item['product_id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Total:
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tools mr-2"></i>Quick Actions
                </h3>
                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="orders.php" class="inline">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        
                        <select name="status" class="mr-2 px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </form>
                    
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-print mr-2"></i>Print Order
                    </button>
                    
                    <a href="users.php?view_user=<?php echo $order['user_id']; ?>" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-user mr-2"></i>View Customer
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
