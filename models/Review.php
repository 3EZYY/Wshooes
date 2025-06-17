<?php
require_once __DIR__ . '/../config/connection.php';

class Review {
    private $conn;
    private $table = 'reviews';
    
    // Review properties
    public $id;
    public $user_id;
    public $product_id;
    public $order_id;
    public $rating;
    public $comment;
    public $is_verified;
    public $created_at;
    public $updated_at;
    
    // User properties from join
    public $user_name;
    public $user_image;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Create new review
    public function create() {
        // Check if user has already reviewed this product
        if ($this->has_reviewed()) {
            return $this->update(); // Update existing review instead
        }
        
        // Sanitize inputs
        $this->comment = sanitize_input($this->comment ?? '');
        
        // Validate rating
        $this->rating = min(max(intval($this->rating), 1), 5);
        
        // Check if this is a verified purchase
        $this->is_verified = $this->is_verified_purchase() ? 1 : 0;
        
        // Create query
        $query = "INSERT INTO {$this->table} (
                    user_id, product_id, order_id, rating, comment, is_verified, created_at
                  ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('iiissi', 
            $this->user_id, 
            $this->product_id, 
            $this->order_id, 
            $this->rating, 
            $this->comment, 
            $this->is_verified
        );
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            
            // Update product rating
            $this->update_product_rating();
            
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error creating review: {$stmt->error}");
        return false;
    }
    
    // Check if user has already reviewed this product
    private function has_reviewed() {
        // Create query
        $query = "SELECT id FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $this->user_id, $this->product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            return true;
        }
        
        return false;
    }
    
    // Check if this is a verified purchase
    private function is_verified_purchase() {
        // If order_id is provided, use it to verify
        if (!empty($this->order_id)) {
            return true;
        }
        
        // Otherwise check if user has purchased this product
        $query = "SELECT o.id 
                  FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered' 
                  LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $this->user_id, $this->product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->order_id = $row['id']; // Set the order_id
            return true;
        }
        
        return false;
    }
    
    // Update existing review
    public function update() {
        // Sanitize inputs
        $this->comment = sanitize_input($this->comment ?? '');
        
        // Validate rating
        $this->rating = min(max(intval($this->rating), 1), 5);
        
        // Create query
        $query = "UPDATE {$this->table} 
                  SET rating = ?, comment = ?, updated_at = NOW() 
                  WHERE id = ? AND user_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('isii', 
            $this->rating, 
            $this->comment, 
            $this->id, 
            $this->user_id
        );
        
        // Execute query
        if ($stmt->execute()) {
            // Update product rating
            $this->update_product_rating();
            
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating review: {$stmt->error}");
        return false;
    }
    
    // Delete review
    public function delete() {
        // Create query
        $query = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $this->id, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            // Update product rating
            $this->update_product_rating();
            
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error deleting review: {$stmt->error}");
        return false;
    }
    
    // Read single review
    public function read_single() {
        // Create query
        $query = "SELECT r.*, u.full_name, u.profile_image 
                  FROM {$this->table} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.id = ? 
                  LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind ID
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Set properties
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->product_id = $row['product_id'];
            $this->order_id = $row['order_id'];
            $this->rating = $row['rating'];
            $this->comment = $row['comment'];
            $this->is_verified = $row['is_verified'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Add user info
            $this->user_name = $row['full_name'];
            $this->user_image = $row['profile_image'];
            
            return true;
        }
        
        return false;
    }
    
    // Get reviews for a product
    public function get_product_reviews($product_id, $limit = null, $offset = 0) {
        // Create query
        $query = "SELECT r.*, u.full_name, u.profile_image 
                  FROM {$this->table} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = ? 
                  ORDER BY r.created_at DESC";
        
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
        
        // Bind product ID
        $stmt->bind_param('i', $product_id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get reviews by a user
    public function get_user_reviews($user_id, $limit = null, $offset = 0) {
        // Create query
        $query = "SELECT r.*, p.name as product_name, p.main_image as product_image 
                  FROM {$this->table} r 
                  JOIN products p ON r.product_id = p.id 
                  WHERE r.user_id = ? 
                  ORDER BY r.created_at DESC";
        
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
        
        // Bind user ID
        $stmt->bind_param('i', $user_id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Count reviews for a product
    public function count_product_reviews($product_id) {
        // Create query
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE product_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Bind product ID
        $stmt->bind_param('i', $product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    // Get average rating for a product
    public function get_average_rating($product_id) {
        // Create query
        $query = "SELECT AVG(rating) as average, COUNT(*) as count FROM {$this->table} WHERE product_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return ['average' => 0, 'count' => 0];
        }
        
        // Bind product ID
        $stmt->bind_param('i', $product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return [
            'average' => round($row['average'] ?? 0, 1),
            'count' => $row['count'] ?? 0
        ];
    }
    
    // Get rating distribution for a product
    public function get_rating_distribution($product_id) {
        // Create query
        $query = "SELECT rating, COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE product_id = ? 
                  GROUP BY rating 
                  ORDER BY rating DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind product ID
        $stmt->bind_param('i', $product_id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Initialize distribution array
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];
        
        // Fill in actual counts
        while ($row = $result->fetch_assoc()) {
            $distribution[$row['rating']] = $row['count'];
        }
        
        return $distribution;
    }
    
    // Update product rating
    private function update_product_rating() {
        // Get average rating
        $rating_data = $this->get_average_rating($this->product_id);
        
        // Update product table
        $query = "UPDATE products 
                  SET rating = ?, total_reviews = ? 
                  WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('dii', 
            $rating_data['average'], 
            $rating_data['count'], 
            $this->product_id
        );
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating product rating: {$stmt->error}");
        return false;
    }
}