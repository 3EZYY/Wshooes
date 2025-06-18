<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Reviews";
$page_description = "View and moderate product reviews";

// Handle review operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $review_id = (int)$_POST['review_id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $review_id);
        
        if ($stmt->execute()) {
            $success_message = "Review status updated successfully!";
        } else {
            $error_message = "Failed to update review status.";
        }
    }
}

// Handle review deletion
if (isset($_GET['delete'])) {
    $review_id = (int)$_GET['delete'];
    
    $delete_stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $delete_stmt->bind_param("i", $review_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Review deleted successfully!";
    } else {
        $error_message = "Failed to delete review.";
    }
}

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';

// Build query
$query = "SELECT r.*, u.username, u.full_name, p.name as product_name 
          FROM reviews r 
          LEFT JOIN users u ON r.user_id = u.id 
          LEFT JOIN products p ON r.product_id = p.id 
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR u.username LIKE ? OR r.comment LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($status_filter)) {
    $query .= " AND r.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($rating_filter)) {
    $query .= " AND r.rating = ?";
    $params[] = $rating_filter;
    $types .= "i";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get review statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_reviews,
        AVG(rating) as avg_rating,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_reviews,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reviews,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_reviews
    FROM reviews
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

        <!-- Quick Navigation -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="dashboard.php" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Dashboard
                </a>
                <a href="products.php" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-200">
                    <i class="fas fa-shoe-prints mr-2"></i>Products
                </a>
                <a href="users.php" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition duration-200">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="orders.php" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition duration-200">
                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                </a>
            </div>
        </div>

        <!-- Review Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Reviews</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_reviews']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-star-half-alt text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average Rating</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_rating'], 1); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['approved_reviews']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['pending_reviews']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['rejected_reviews']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search reviews, products, users..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Ratings</option>
                        <option value="5" <?php echo $rating_filter === '5' ? 'selected' : ''; ?>>5 Stars</option>
                        <option value="4" <?php echo $rating_filter === '4' ? 'selected' : ''; ?>>4 Stars</option>
                        <option value="3" <?php echo $rating_filter === '3' ? 'selected' : ''; ?>>3 Stars</option>
                        <option value="2" <?php echo $rating_filter === '2' ? 'selected' : ''; ?>>2 Stars</option>
                        <option value="1" <?php echo $rating_filter === '1' ? 'selected' : ''; ?>>1 Star</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold">All Reviews (<?php echo count($reviews); ?>)</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($reviews as $review): ?>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($review['product_name']); ?></h3>
                                <div class="ml-4 flex items-center">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star text-sm <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ml-2 text-sm text-gray-600"><?php echo $review['rating']; ?>/5</span>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 mb-3"><?php echo htmlspecialchars($review['comment']); ?></p>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <span>By <?php echo htmlspecialchars($review['full_name'] ?: $review['username']); ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo date('M d, Y H:i', strtotime($review['created_at'])); ?></span>
                                <span class="mx-2">•</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php echo $review['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                              ($review['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($review['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="ml-4 flex items-center space-x-2">
                            <?php if ($review['status'] !== 'approved'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" name="update_status" 
                                        class="text-green-600 hover:text-green-900 px-3 py-1 rounded border border-green-300 hover:bg-green-50">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($review['status'] !== 'rejected'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" name="update_status" 
                                        class="text-red-600 hover:text-red-900 px-3 py-1 rounded border border-red-300 hover:bg-red-50">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </form>
                            <?php endif; ?>
                            
                            <a href="?delete=<?php echo $review['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this review?')"
                               class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($reviews)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-star text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No reviews found</p>
                    <p class="text-gray-400">Reviews will appear here when customers leave them</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include 'partials/footer.php'; ?>
