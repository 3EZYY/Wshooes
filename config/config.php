<?php
// filepath: c:\xampp\htdocs\Wshooes\database.php

require_once 'config.php';

class Database {
    private static $host = 'localhost';
    private static $dbname = 'wshooes';
    private static $username = 'root';
    private static $password = '';
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname,
                    self::$username,
                    self::$password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                error_log("Connection failed: " . $e->getMessage());
                throw $e;
            }
        }
        return self::$connection;
    }
}

class DatabaseOperations {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // User Operations
    public function createUser($username, $email, $password, $role = 'customer') {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$username, $email, $hashedPassword, $role]);
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateUser($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if ($key === 'password') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $fields[] = "$key = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }
    
    // Product Operations
    public function createProduct($name, $description, $price, $category, $image = null, $stock = 0) {
        try {
            $sql = "INSERT INTO products (name, description, price, category, image, stock, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $description, $price, $category, $image, $stock]);
        } catch (PDOException $e) {
            error_log("Create product error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllProducts($limit = null, $offset = 0) {
        try {
            $sql = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC";
            if ($limit) {
                $sql .= " LIMIT $limit OFFSET $offset";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get products error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getProductById($id) {
        try {
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get product error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateProduct($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            $sql = "UPDATE products SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteProduct($id) {
        try {
            $sql = "UPDATE products SET status = 'deleted', updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }
    
    // Order Operations
    public function createOrder($user_id, $total_amount, $items) {
        try {
            $this->pdo->beginTransaction();
            
            // Create order
            $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $total_amount]);
            
            $order_id = $this->pdo->lastInsertId();
            
            // Add order items
            foreach ($items as $item) {
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                
                // Update product stock
                $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            $this->pdo->commit();
            return $order_id;
        } catch (PDOException $e) {
            $this->pdo->rollback();
            error_log("Create order error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getOrdersByUser($user_id) {
        try {
            $sql = "SELECT o.*, COUNT(oi.id) as item_count FROM orders o 
                   LEFT JOIN order_items oi ON o.id = oi.order_id 
                   WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get user orders error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getOrderDetails($order_id) {
        try {
            $sql = "SELECT o.*, u.username, u.email FROM orders o 
                   JOIN users u ON o.user_id = u.id WHERE o.id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();
            
            if ($order) {
                $sql = "SELECT oi.*, p.name as product_name FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$order_id]);
                $order['items'] = $stmt->fetchAll();
            }
            
            return $order;
        } catch (PDOException $e) {
            error_log("Get order details error: " . $e->getMessage());
            return false;
        }
    }
    
    // Category Operations
    public function getAllCategories() {
        try {
            $sql = "SELECT DISTINCT category FROM products WHERE status = 'active' ORDER BY category";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Get categories error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getProductsByCategory($category, $limit = null) {
        try {
            $sql = "SELECT * FROM products WHERE category = ? AND status = 'active' ORDER BY created_at DESC";
            if ($limit) {
                $sql .= " LIMIT $limit";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get products by category error: " . $e->getMessage());
            return [];
        }
    }
    
    // Search Operations
    public function searchProducts($keyword) {
        try {
            $sql = "SELECT * FROM products WHERE (name LIKE ? OR description LIKE ?) AND status = 'active' ORDER BY name";
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = "%$keyword%";
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Search products error: " . $e->getMessage());
            return [];
        }
    }
    
    // Statistics (for admin)
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total users
            $sql = "SELECT COUNT(*) FROM users WHERE role = 'customer'";
            $stats['total_users'] = $this->pdo->query($sql)->fetchColumn();
            
            // Total products
            $sql = "SELECT COUNT(*) FROM products WHERE status = 'active'";
            $stats['total_products'] = $this->pdo->query($sql)->fetchColumn();
            
            // Total orders
            $sql = "SELECT COUNT(*) FROM orders";
            $stats['total_orders'] = $this->pdo->query($sql)->fetchColumn();
            
            // Total revenue
            $sql = "SELECT SUM(total_amount) FROM orders WHERE status = 'completed'";
            $stats['total_revenue'] = $this->pdo->query($sql)->fetchColumn() ?: 0;
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return [];
        }
    }
}

// Initialize database operations
$db = new DatabaseOperations();

?>