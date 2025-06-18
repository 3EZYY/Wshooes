<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Categories";
$page_description = "Add, edit, and manage product categories";

// Handle category operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("INSERT INTO categories (name, description, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $name, $description);
        
        if ($stmt->execute()) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Failed to add category.";
        }
    }
    
    if (isset($_POST['update_category'])) {
        $category_id = (int)$_POST['category_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $category_id);
        
        if ($stmt->execute()) {
            $success_message = "Category updated successfully!";
        } else {
            $error_message = "Failed to update category.";
        }
    }
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Check if category has products
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $check_stmt->bind_param("i", $category_id);
    $check_stmt->execute();
    $count = $check_stmt->get_result()->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error_message = "Cannot delete category with existing products. Please move or delete products first.";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $delete_stmt->bind_param("i", $category_id);
        
        if ($delete_stmt->execute()) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Failed to delete category.";
        }
    }
}

// Get all categories with product counts
$categories = $conn->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
")->fetch_all(MYSQLI_ASSOC);

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

        <!-- Add Category Form -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Add New Category</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="add_category" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold">All Categories</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $category['id']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($category['description'] ?? 'No description'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $category['product_count']; ?> products
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($category['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>', '<?php echo htmlspecialchars($category['description']); ?>')" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <?php if ($category['product_count'] == 0): ?>
                                <a href="?delete=<?php echo $category['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this category?')"
                                   class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php else: ?>
                                <span class="text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-trash"></i> Cannot Delete
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Edit Category Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Category</h3>
                <form method="POST" id="editForm">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <input type="text" name="name" id="editName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" id="editDescription" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" name="update_category" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php include 'partials/footer.php'; ?>
