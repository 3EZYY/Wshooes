<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Users";
$page_description = "View and manage all users, their orders and shopping carts";

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $new_role = $_POST['role'];
        
        $stmt = $conn->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User role updated successfully!";
        } else {
            $error_message = "Failed to update user role.";
        }
    }
}

// Handle user deletion/deactivation
if (isset($_GET['deactivate'])) {
    $user_id = (int)$_GET['deactivate'];
    
    // Instead of deleting, we'll mark as inactive or change role
    $stmt = $conn->prepare("UPDATE users SET role = 'inactive', updated_at = NOW() WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User deactivated successfully!";
    } else {
        $error_message = "Failed to deactivate user.";
    }
}

// Get users with their order and cart statistics
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$query = "SELECT u.*, 
          COUNT(DISTINCT o.id) as total_orders,
          COALESCE(SUM(o.total_amount), 0) as total_spent,
          COUNT(DISTINCT c.id) as cart_items
          FROM users u 
          LEFT JOIN orders o ON u.id = o.user_id 
          LEFT JOIN cart c ON u.id = c.user_id
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (u.username LIKE '%$search%' OR u.email LIKE '%$search%' OR u.full_name LIKE '%$search%')";
}

if (!empty($role_filter)) {
    $query .= " AND u.role = '$role_filter'";
}

$query .= " GROUP BY u.id ORDER BY u.created_at DESC";

$users = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Get user details for modal
$selected_user = null;
$user_orders = [];
$user_cart = [];

if (isset($_GET['view_user'])) {
    $user_id = (int)$_GET['view_user'];
    
    // Get user details
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $selected_user = $user_stmt->get_result()->fetch_assoc();
    
    if ($selected_user) {
        // Get user orders
        $orders_query = "SELECT o.*, 
                        GROUP_CONCAT(CONCAT(p.name, ' (Qty: ', oi.quantity, ')') SEPARATOR ', ') as products
                        FROM orders o 
                        LEFT JOIN order_items oi ON o.id = oi.order_id
                        LEFT JOIN products p ON oi.product_id = p.id
                        WHERE o.user_id = ?
                        GROUP BY o.id
                        ORDER BY o.created_at DESC
                        LIMIT 10";
        $orders_stmt = $conn->prepare($orders_query);
        $orders_stmt->bind_param("i", $user_id);
        $orders_stmt->execute();
        $user_orders = $orders_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get user cart
        $cart_query = "SELECT c.*, p.name, p.price, p.main_image
                       FROM cart c
                       LEFT JOIN products p ON c.product_id = p.id
                       WHERE c.user_id = ?
                       ORDER BY c.created_at DESC";
        $cart_stmt = $conn->prepare($cart_query);
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $user_cart = $cart_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
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
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search by username, email, or full name..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Filter</label>
                        <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Roles</option>
                            <option value="customer" <?php echo $role_filter === 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="inactive" <?php echo $role_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    
                    <a href="users.php" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-users mr-2"></i>Users Management
                        <span class="text-sm text-gray-500">(<?php echo count($users); ?> users)</span>
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avatar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cart Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p>No users found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name'] ?: $user['username']); ?>&background=3b82f6&color=fff&size=40" 
                                             alt="Avatar" class="w-10 h-10 rounded-full">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($user['username']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <select name="role" onchange="this.form.submit()" 
                                                    class="text-sm px-2 py-1 rounded-full font-semibold border-0
                                                    <?php 
                                                    switch($user['role']) {
                                                        case 'admin': echo 'bg-purple-100 text-purple-800'; break;
                                                        case 'customer': echo 'bg-green-100 text-green-800'; break;
                                                        case 'inactive': echo 'bg-red-100 text-red-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>">
                                                <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="inactive" <?php echo $user['role'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                            <input type="hidden" name="update_role" value="1">
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-medium"><?php echo $user['total_orders']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp <?php echo number_format($user['total_spent'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="<?php echo $user['cart_items'] > 0 ? 'text-blue-600 font-medium' : 'text-gray-500'; ?>">
                                            <?php echo $user['cart_items']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="?view_user=<?php echo $user['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="?deactivate=<?php echo $user['id']; ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                <i class="fas fa-ban"></i> Deactivate
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- User Details Modal -->
            <?php if ($selected_user): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="userModal">
                <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user mr-2"></i>User Details: <?php echo htmlspecialchars($selected_user['full_name'] ?: $selected_user['username']); ?>
                        </h3>
                        <a href="users.php" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- User Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">User Information</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($selected_user['full_name'] ?: 'N/A'); ?></p>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($selected_user['username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($selected_user['email']); ?></p>
                                <p><strong>Role:</strong> 
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        <?php echo $selected_user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo ucfirst($selected_user['role']); ?>
                                    </span>
                                </p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($selected_user['phone_number'] ?: 'N/A'); ?></p>
                                <p><strong>Joined:</strong> <?php echo date('F d, Y', strtotime($selected_user['created_at'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo date('F d, Y H:i', strtotime($selected_user['updated_at'])); ?></p>
                            </div>
                        </div>
                        
                        <!-- Current Cart -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">
                                <i class="fas fa-shopping-cart mr-1"></i>Current Cart (<?php echo count($user_cart); ?> items)
                            </h4>
                            <div class="max-h-48 overflow-y-auto">
                                <?php if (empty($user_cart)): ?>
                                    <p class="text-gray-500 text-sm">Cart is empty</p>
                                <?php else: ?>
                                    <?php foreach ($user_cart as $item): ?>
                                        <div class="flex items-center justify-between py-2 border-b border-blue-200 last:border-b-0">
                                            <div class="flex items-center space-x-2">
                                                <?php if ($item['main_image']): ?>
                                                    <img src="../assets/uploads/products/<?php echo $item['main_image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="w-8 h-8 object-cover rounded">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400 text-xs"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="text-sm font-medium"><?php echo htmlspecialchars($item['name']); ?></p>
                                                    <p class="text-xs text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="text-sm font-medium">
                                                Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            <i class="fas fa-history mr-1"></i>Recent Orders (<?php echo count($user_orders); ?> orders)
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($user_orders)): ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No orders found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($user_orders as $order): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium">#<?php echo $order['id']; ?></td>
                                            <td class="px-4 py-2 text-sm max-w-xs truncate" title="<?php echo htmlspecialchars($order['products']); ?>">
                                                <?php echo htmlspecialchars($order['products'] ?: 'No products'); ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
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
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">
                                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        // Auto-submit form when role changes
        document.querySelectorAll('select[name="role"]').forEach(select => {
            select.addEventListener('change', function() {
                if (confirm('Update user role?')) {
                    this.form.submit();
                }
            });
        });
    </script>
</body>
</html>
