<?php
session_start();

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Get dashboard statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Total products
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
$stats['total_products'] = $result->fetch_assoc()['total'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'");
$stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$recent_orders = $conn->query("
    SELECT o.*, u.full_name, u.email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Low stock products
$low_stock = $conn->query("
    SELECT * FROM products 
    WHERE stock <= 10 AND stock > 0 
    ORDER BY stock ASC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Admin Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="ml-64 min-h-screen">
        <?php include __DIR__ . '/partials/header.php'; ?>
        
        <main class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-500 p-3 rounded-full">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="bg-green-500 p-3 rounded-full">
                            <i class="fas fa-shoe-prints text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_products']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="bg-orange-500 p-3 rounded-full">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_orders']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-500 p-3 rounded-full">
                            <i class="fas fa-money-bill-wave text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo format_currency($stats['total_revenue']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts and Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Sales Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Overview</h3>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
                
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?php echo $order['id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo format_currency($order['total_amount']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            switch($order['status']) {
                                                case 'completed': echo 'bg-green-100 text-green-800'; break;
                                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="/Wshooes/admin/orders.php" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View all orders →</a>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Alert -->
            <?php if (!empty($low_stock)): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Low Stock Alert</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($low_stock as $product): ?>
                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                        <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="text-sm text-gray-600">Stock: <span class="text-red-600 font-medium"><?php echo $product['stock']; ?> left</span></p>
                        <a href="/Wshooes/admin/products.php?edit=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 text-sm">Update stock →</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales (in millions)',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
