<?php
require_once __DIR__ . '/../config/connection.php';

class Cart {
    private $conn;
    private $table = 'cart';
    
    // Cart properties
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $size;
    public $color;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Add item to cart
    public function add_item() {
        // Check if item already exists in cart
        $existing_item = $this->get_cart_item($this->user_id, $this->product_id, $this->size, $this->color);
        
        if ($existing_item) {
            // Update quantity instead of adding new item
            $new_quantity = $existing_item['quantity'] + $this->quantity;
            return $this->update_quantity($existing_item['id'], $new_quantity);
        }
        
        // Create query
        $query = "INSERT INTO {$this->table} (user_id, product_id, quantity, size, color, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Sanitize inputs
        $this->size = sanitize_input($this->size ?? '');
        $this->color = sanitize_input($this->color ?? '');
        
        // Bind parameters
        $stmt->bind_param('iiiss', 
            $this->user_id, 
            $this->product_id, 
            $this->quantity, 
            $this->size, 
            $this->color
        );
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error adding item to cart: {$stmt->error}");
        return false;
    }
    
    // Get cart items for a user
    public function get_cart_items($user_id) {
        // Create query
        $query = "SELECT c.*, p.name, p.price, p.main_image, 
                    (SELECT COUNT(*) FROM product_images WHERE product_id = p.id) as image_count 
                  FROM {$this->table} c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ? 
                  ORDER BY c.created_at DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind user ID
        $stmt->bind_param('i', $user_id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get specific cart item
    private function get_cart_item($user_id, $product_id, $size = '', $color = '') {
        // Create query
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ? AND product_id = ?";
        
        $params = [$user_id, $product_id];
        $types = 'ii';
        
        // Add size and color to query if provided
        if (!empty($size)) {
            $query .= " AND size = ?";
            $params[] = $size;
            $types .= 's';
        }
        
        if (!empty($color)) {
            $query .= " AND color = ?";
            $params[] = $color;
            $types .= 's';
        }
        
        $query .= " LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Update cart item quantity
    public function update_quantity($cart_id, $quantity) {
        // Create query
        $query = "UPDATE {$this->table} SET quantity = ?, updated_at = NOW() WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $quantity, $cart_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating cart quantity: {$stmt->error}");
        return false;
    }
    
    // Remove item from cart
    public function remove_item($cart_id) {
        // Create query
        $query = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $cart_id, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error removing item from cart: {$stmt->error}");
        return false;
    }
    
    // Clear cart for a user
    public function clear_cart($user_id) {
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
        error_log("Error clearing cart: {$stmt->error}");
        return false;
    }
    
    // Count items in cart
    public function count_items($user_id) {
        // Create query
        $query = "SELECT SUM(quantity) as total FROM {$this->table} WHERE user_id = ?";
        
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
    
    // Calculate cart total
    public function calculate_total($user_id) {
        // Create query
        $query = "SELECT SUM(c.quantity * p.price) as total 
                  FROM {$this->table} c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        
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
    
    // Move cart items to order
    public function move_to_order($user_id, $order_id) {
        // Get all cart items
        $cart_items = $this->get_cart_items($user_id);
        
        if (!$cart_items || $cart_items->num_rows === 0) {
            return false;
        }
        
        // Create Order Item object
        require_once __DIR__ . '/Order.php';
        $order = new Order();
        $order->id = $order_id;
        
        // Begin transaction
        $this->conn->begin_transaction();
        
        try {
            // Add each cart item to order items
            while ($item = $cart_items->fetch_assoc()) {
                // Get product details
                $query = "SELECT name, main_image FROM products WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                
                if (!$stmt) {
                    throw new Exception("Database error: " . $this->conn->error);
                }
                
                $stmt->bind_param('i', $item['product_id']);
                $stmt->execute();
                $product_result = $stmt->get_result();
                $product = $product_result->fetch_assoc();
                
                // Add to order items
                $success = $order->add_item(
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $product['name'],
                    $product['main_image'],
                    $item['size'],
                    $item['color']
                );
                
                if (!$success) {
                    throw new Exception("Failed to add item to order");
                }
                
                // Update product stock
                $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                
                if (!$stmt) {
                    throw new Exception("Database error: " . $this->conn->error);
                }
                
                $stmt->bind_param('ii', $item['quantity'], $item['product_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update product stock");
                }
            }
            
            // Clear the cart
            if (!$this->clear_cart($user_id)) {
                throw new Exception("Failed to clear cart");
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Move to order error: " . $e->getMessage());
            return false;
        }
    }
}