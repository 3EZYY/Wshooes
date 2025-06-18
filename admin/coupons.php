<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Coupons";
$page_description = "Create and manage discount coupons";

// Handle coupon operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_coupon'])) {
        $code = strtoupper(trim($_POST['code']));
        $discount_type = $_POST['discount_type'];
        $discount_value = (float)$_POST['discount_value'];
        $min_amount = (float)$_POST['min_amount'];
        $max_uses = (int)$_POST['max_uses'];
        $expires_at = $_POST['expires_at'];
        
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_amount, max_uses, expires_at, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssddis", $code, $discount_type, $discount_value, $min_amount, $max_uses, $expires_at);
        
        if ($stmt->execute()) {
            $success_message = "Coupon created successfully!";
        } else {
            $error_message = "Failed to create coupon. Code might already exist.";
        }
    }
    
    if (isset($_POST['update_status'])) {
        $coupon_id = (int)$_POST['coupon_id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE coupons SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $coupon_id);
        
        if ($stmt->execute()) {
            $success_message = "Coupon status updated successfully!";
        } else {
            $error_message = "Failed to update coupon status.";
        }
    }
}

// Handle coupon deletion
if (isset($_GET['delete'])) {
    $coupon_id = (int)$_GET['delete'];
    
    $delete_stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $delete_stmt->bind_param("i", $coupon_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Coupon deleted successfully!";
    } else {
        $error_message = "Failed to delete coupon.";
    }
}

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query
$query = "SELECT * FROM coupons WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND code LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$coupons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get coupon statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_coupons,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_coupons,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_coupons,
        SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) as expired_coupons,
        SUM(used_count) as total_uses
    FROM coupons
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
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Quick Navigation & Actions -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="dashboard.php" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                    <a href="orders.php" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>Orders
                    </a>
                    <a href="users.php" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition duration-200">
                        <i class="fas fa-users mr-2"></i>Users
                    </a>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="generateRandomCoupon()" class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition duration-200">
                        <i class="fas fa-random mr-2"></i>Random Code
                    </button>
                    <button onclick="bulkExpireCoupons()" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200">
                        <i class="fas fa-clock mr-2"></i>Bulk Expire
                    </button>
                </div>
            </div>
        </div>

        <!-- Coupon Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-ticket-alt text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Coupons</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_coupons']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['active_coupons']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <i class="fas fa-pause-circle text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Inactive</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['inactive_coupons']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-clock text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Expired</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['expired_coupons']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Uses</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_uses']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Coupon Form -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Create New Coupon</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label>
                    <input type="text" name="code" required placeholder="e.g., SAVE20" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                    <select name="discount_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                    <input type="number" name="discount_value" required step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Amount</label>
                    <input type="number" name="min_amount" step="0.01" min="0" value="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Uses (0 = unlimited)</label>
                    <input type="number" name="max_uses" min="0" value="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expires At</label>
                    <input type="datetime-local" name="expires_at" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" name="add_coupon" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Create Coupon
                    </button>
                </div>
            </form>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Code</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search coupon codes..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Coupons Table -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold">All Coupons (<?php echo count($coupons); ?>)</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($coupons as $coupon): ?>
                        <?php 
                            $is_expired = $coupon['expires_at'] && strtotime($coupon['expires_at']) < time();
                            $is_maxed = $coupon['max_uses'] > 0 && $coupon['used_count'] >= $coupon['max_uses'];
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($coupon['code']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                        <?php echo $coupon['discount_value']; ?>%
                                    <?php else: ?>
                                        <?php echo format_currency($coupon['discount_value']); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo format_currency($coupon['min_amount']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $coupon['used_count']; ?><?php echo $coupon['max_uses'] > 0 ? ' / ' . $coupon['max_uses'] : ' / ∞'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($coupon['expires_at']): ?>
                                    <?php echo date('M d, Y H:i', strtotime($coupon['expires_at'])); ?>
                                    <?php if ($is_expired): ?>
                                        <span class="text-red-500">(Expired)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    Never
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php 
                                    if ($is_expired || $is_maxed) echo 'bg-red-100 text-red-800';
                                    elseif ($coupon['status'] === 'active') echo 'bg-green-100 text-green-800';
                                    else echo 'bg-gray-100 text-gray-800';
                                    ?>">
                                    <?php 
                                    if ($is_expired) echo 'Expired';
                                    elseif ($is_maxed) echo 'Max Uses Reached';
                                    else echo ucfirst($coupon['status']);
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if (!$is_expired && !$is_maxed): ?>
                                <form method="POST" class="inline mr-2">
                                    <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $coupon['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                    <button type="submit" name="update_status" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-<?php echo $coupon['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                        <?php echo $coupon['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <a href="?delete=<?php echo $coupon['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this coupon?')"
                                   class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($coupons)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-ticket-alt text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No coupons found</p>
                    <p class="text-gray-400">Create your first coupon to start offering discounts</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
// Generate random coupon code
function generateRandomCoupon() {
    const prefixes = ['SAVE', 'DISC', 'DEAL', 'SALE', 'PROMO', 'SPECIAL'];
    const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
    const number = Math.floor(Math.random() * 99) + 1;
    const code = prefix + number;
    
    document.querySelector('input[name="code"]').value = code;
    document.querySelector('input[name="code"]').focus();
}

// Bulk expire coupons
function bulkExpireCoupons() {
    if (confirm('Are you sure you want to expire all active coupons? This action cannot be undone.')) {
        // In a real scenario, this would make an AJAX call
        alert('Feature coming soon! This will expire all active coupons.');
    }
}

// Auto-calculate discount preview
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.querySelector('select[name="discount_type"]');
    const discountValue = document.querySelector('input[name="discount_value"]');
    const minAmount = document.querySelector('input[name="min_amount"]');
    
    function updatePreview() {
        const type = discountType.value;
        const value = parseFloat(discountValue.value) || 0;
        const min = parseFloat(minAmount.value) || 0;
        
        let preview = '';
        if (type === 'percentage') {
            preview = `${value}% off (min Rp ${min.toLocaleString('id-ID')})`;
        } else {
            preview = `Rp ${value.toLocaleString('id-ID')} off (min Rp ${min.toLocaleString('id-ID')})`;
        }
        
        const previewElement = document.getElementById('discountPreview');
        if (previewElement) {
            previewElement.textContent = preview;
        }
    }
    
    discountType.addEventListener('change', updatePreview);
    discountValue.addEventListener('input', updatePreview);
    minAmount.addEventListener('input', updatePreview);
    
    // Add preview element if not exists
    if (!document.getElementById('discountPreview')) {
        const previewDiv = document.createElement('div');
        previewDiv.id = 'discountPreview';
        previewDiv.className = 'text-sm text-gray-600 mt-2 font-medium';
        discountValue.parentNode.appendChild(previewDiv);
    }
    
    updatePreview();
});
</script>

<?php include 'partials/footer.php'; ?>
