<?php
require_once __DIR__ . '/../config/connection.php';

class Category {
    private $conn;
    private $table = 'categories';
    
    // Category properties
    public $id;
    public $name;
    public $description;
    public $image;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Create new category
    public function create() {
        // Sanitize inputs
        $this->name = sanitize_input($this->name);
        $this->description = sanitize_input($this->description ?? '');
        $this->image = sanitize_input($this->image ?? '');
        
        // Create query
        $query = "INSERT INTO {$this->table} (name, description, image, created_at) VALUES (?, ?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('sss', $this->name, $this->description, $this->image);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Read all categories
    public function read() {
        // Create query
        $query = "SELECT * FROM {$this->table} ORDER BY name ASC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Read single category
    public function read_single() {
        // Create query
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 0,1";
        
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
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update category
    public function update() {
        // Sanitize inputs
        $this->name = sanitize_input($this->name);
        $this->description = sanitize_input($this->description ?? '');
        $this->image = sanitize_input($this->image ?? '');
        
        // Create query
        $query = "UPDATE {$this->table} 
                  SET name = ?, 
                      description = ?, 
                      image = ?, 
                      updated_at = NOW() 
                  WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('sssi', $this->name, $this->description, $this->image, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Delete category
    public function delete() {
        // First check if there are products in this category
        $query = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            // Cannot delete category with products
            return false;
        }
        
        // Create delete query
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind id
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Get products by category
    public function get_products($limit = null, $offset = 0) {
        // Create query
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = ? AND p.status = 'active' 
                  ORDER BY p.created_at DESC";
        
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
        
        // Bind category ID
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Count products in category
    public function count_products() {
        // Create query
        $query = "SELECT COUNT(*) as total FROM products WHERE category_id = ? AND status = 'active'";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Bind category ID
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}