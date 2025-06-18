<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "Manage Products";
$page_description = "Add, edit, and manage all products";

// Handle product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category_id = (int)$_POST['category_id'];
        
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
        $stmt->bind_param("ssdii", $name, $description, $price, $stock, $category_id);
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            
            // Handle image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
                $upload_dir = __DIR__ . '/../assets/uploads/products/';
                $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $filename)) {
                    $conn->prepare("UPDATE products SET main_image = ? WHERE id = ?")->execute([$filename, $product_id]);
                }
            }
            
            $success_message = "Product added successfully!";
        } else {
            $error_message = "Failed to add product.";
        }
    }
    
    if (isset($_POST['update_product'])) {
        // Update existing product
        $id = (int)$_POST['product_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category_id = (int)$_POST['category_id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category_id, $status, $id);
        
        if ($stmt->execute()) {
            // Handle image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
                $upload_dir = __DIR__ . '/../assets/uploads/products/';
                $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $filename = 'product_' . $id . '_' . time() . '.' . $file_extension;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $filename)) {
                    $conn->prepare("UPDATE products SET main_image = ? WHERE id = ?")->execute([$filename, $id]);
                }
            }
            
            $success_message = "Product updated successfully!";
        } else {
            $error_message = "Failed to update product.";
        }
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    
    $delete_stmt = $conn->prepare("UPDATE products SET status = 'deleted' WHERE id = ?");
    $delete_stmt->bind_param("i", $product_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Product deleted successfully!";
    } else {
        $error_message = "Failed to delete product.";
    }
}

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get products with category names
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT p.*, c.name as category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.status != 'deleted'";

if (!empty($search)) {
    $query .= " AND p.name LIKE '%$search%'";
}

if (!empty($category_filter)) {
    $query .= " AND p.category_id = '$category_filter'";
}

$query .= " ORDER BY p.created_at DESC";

$products = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_product = $edit_stmt->get_result()->fetch_assoc();
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
            
            <!-- Add/Edit Product Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-plus mr-2"></i><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?>
                </h3>
                
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" required
                               value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (Rp)</label>
                        <input type="number" name="price" step="0.01" required
                               value="<?php echo $edit_product['price'] ?? ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                        <input type="number" name="stock" required
                               value="<?php echo $edit_product['stock'] ?? ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <?php if ($edit_product): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="active" <?php echo $edit_product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $edit_product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                        <input type="file" name="main_image" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <?php if ($edit_product && $edit_product['main_image']): ?>
                            <div class="mt-2">
                                <img src="../assets/uploads/products/<?php echo $edit_product['main_image']; ?>" 
                                     alt="Current image" class="w-20 h-20 object-cover rounded">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <button type="submit" name="<?php echo $edit_product ? 'update_product' : 'add_product'; ?>" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
                        </button>
                        
                        <?php if ($edit_product): ?>
                            <a href="products.php" class="ml-3 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search by product name..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Filter</label>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    
                    <a href="products.php" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </form>
            </div>
            
            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-shoe-prints mr-2"></i>Products Management
                        <span class="text-sm text-gray-500">(<?php echo count($products); ?> products)</span>
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p>No products found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($product['main_image']): ?>
                                            <img src="../assets/uploads/products/<?php echo $product['main_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                 class="w-16 h-16 object-cover rounded-lg">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium 
                                            <?php echo $product['stock'] <= 10 ? 'text-red-600' : 'text-gray-900'; ?>">
                                            <?php echo $product['stock']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="?edit=<?php echo $product['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $product['id']; ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
