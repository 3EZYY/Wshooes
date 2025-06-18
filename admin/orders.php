<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Orders";
$page_description = "View and manage all customer orders";

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    $update_stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Order status updated successfully!";
    } else {
        $error_message = "Failed to update order status.";
    }
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $order_id = (int)$_GET['delete'];
    
    // Delete order items first
    $conn->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
    
    // Then delete the order
    $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete_stmt->bind_param("i", $order_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Order deleted successfully!";
    } else {
        $error_message = "Failed to delete order.";
    }
}

// Get all orders with user details
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT o.*, u.username, u.email, u.full_name 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($status_filter)) {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
        SUM(total_amount) as total_revenue
    FROM orders
")->fetch_assoc();

include 'partials/header.php';
?>

<div class="flex">
    <?php include 'partials/sidebar.php'; ?>
    
    <main class="flex-1 ml-64 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo $page_title; ?></h1>
            <p class="text-gray-600 mt-2"><?php echo $page_description; ?></p>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Order Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['pending_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-cog text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Processing</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['processing_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-truck text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Shipped</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['shipped_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Delivered</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['delivered_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cancelled</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['cancelled_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Revenue</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo format_currency($stats['total_revenue']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search orders, customers..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">All Orders (<?php echo count($orders); ?>)</h2>
                    <div class="flex space-x-2">
                        <a href="dashboard.php" class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#<?php echo $order['id']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($order['full_name'] ?: $order['username']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo format_currency($order['total_amount']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="if(confirm('Update order status?')) this.form.submit();" 
                                            class="text-sm border-0 bg-transparent font-medium <?php 
                                                echo $order['status'] === 'pending' ? 'text-yellow-600' : 
                                                    ($order['status'] === 'processing' ? 'text-blue-600' : 
                                                    ($order['status'] === 'shipped' ? 'text-purple-600' : 
                                                    ($order['status'] === 'delivered' ? 'text-green-600' : 'text-red-600')));
                                            ?>">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="users.php?user_id=<?php echo $order['user_id']; ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-user"></i> User
                                </a>
                                <a href="?delete=<?php echo $order['id']; ?>" 
                                   class="text-red-600 hover:text-red-900"
                                   onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($orders)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No orders found</p>
                    <p class="text-gray-400">Orders will appear here when customers make purchases</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
// Auto-submit form when status changes
document.querySelectorAll('select[name="status"]').forEach(select => {
    select.addEventListener('change', function() {
        if (confirm('Update order status?')) {
            this.form.submit();
        }
    });
});

// Add loading state to status updates
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const select = form.querySelector('select[name="status"]');
        if (select) {
            select.disabled = true;
            select.style.opacity = '0.5';
        }
    });
});
</script>

<?php include 'partials/footer.php'; ?>
