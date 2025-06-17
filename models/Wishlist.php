<?php
require_once __DIR__ . '/../config/connection.php';

class Wishlist {
    private $conn;
    private $table = 'wishlist';
    
    // Wishlist properties
    public $id;
    public $user_id;
    public $product_id;
    public $created_at;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Add product to wishlist
    public function add() {
        // Check if product already in wishlist
        if ($this->is_in_wishlist($this->user_id, $this->product_id)) {
            return true; // Already in wishlist
        }
        
        // Create query
        $query = "INSERT INTO {$this->table} (user_id, product_id, created_at) VALUES (?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $this->user_id, $this->product_id);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error adding to wishlist: {$stmt->error}");
        return false;
    }
    
    // Remove product from wishlist
    public function remove() {
        // Create query
        $query = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $this->user_id, $this->product_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error removing from wishlist: {$stmt->error}");
        return false;
    }
    
    // Check if product is in user's wishlist
    public function is_in_wishlist($user_id, $product_id) {
        // Create query
        $query = "SELECT id FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $user_id, $product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    // Get user's wishlist
    public function get_wishlist($user_id, $limit = null, $offset = 0) {
        // Create query
        $query = "SELECT w.*, p.name, p.price, p.main_image, p.description, p.stock, p.category_id, 
                    c.name as category_name 
                  FROM {$this->table} w 
                  JOIN products p ON w.product_id = p.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE w.user_id = ? 
                  ORDER BY w.created_at DESC";
        
        // Add limit if provided
        if ($limit) {
            $query .= " LIMIT " . intval($offset) . ", " . intval($limit);
        }
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('i', $user_id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Count items in wishlist
    public function count_items($user_id) {
        // Create query
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Bind parameters
        $stmt->bind_param('i', $user_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    // Move wishlist item to cart
    public function move_to_cart($wishlist_id, $quantity = 1, $size = '', $color = '') {
        // Get wishlist item
        $query = "SELECT user_id, product_id FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        $stmt->bind_param('i', $wishlist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        $item = $result->fetch_assoc();
        
        // Add to cart
        require_once __DIR__ . '/Cart.php';
        $cart = new Cart();
        $cart->user_id = $item['user_id'];
        $cart->product_id = $item['product_id'];
        $cart->quantity = $quantity;
        $cart->size = $size;
        $cart->color = $color;
        
        if ($cart->add_item()) {
            // Remove from wishlist
            $query = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                handle_db_error($query);
                return false;
            }
            
            $stmt->bind_param('i', $wishlist_id);
            return $stmt->execute();
        }
        
        return false;
    }
    
    // Clear all items from wishlist
    public function clear_wishlist($user_id) {
        // Create query
        $query = "DELETE FROM {$this->table} WHERE user_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('i', $user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error clearing wishlist: {$stmt->error}");
        return false;
    }
    
    // Get popular wishlist items (for recommendations)
    public function get_popular_items($limit = 10) {
        // Create query to get most wishlisted products
        $query = "SELECT w.product_id, COUNT(*) as wishlist_count, 
                    p.name, p.price, p.main_image, p.description, p.stock, p.category_id, 
                    c.name as category_name 
                  FROM {$this->table} w 
                  JOIN products p ON w.product_id = p.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  GROUP BY w.product_id 
                  ORDER BY wishlist_count DESC 
                  LIMIT ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('i', $limit);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
}