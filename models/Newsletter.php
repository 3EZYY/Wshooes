<?php
require_once __DIR__ . '/../config/connection.php';

class Newsletter {
    private $conn;
    private $table = 'newsletter_subscribers';
    
    // Newsletter properties
    public $id;
    public $email;
    public $is_active;
    public $subscribed_at;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Subscribe email to newsletter
    public function subscribe() {
        // Sanitize email
        $this->email = sanitize_input($this->email);
        
        // Validate email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Check if email already exists
        $existing = $this->get_by_email($this->email);
        
        if ($existing) {
            // If already subscribed but inactive, reactivate
            if (!$existing['is_active']) {
                return $this->update_status($existing['id'], 1);
            }
            return true; // Already subscribed and active
        }
        
        // Create query
        $query = "INSERT INTO {$this->table} (email, is_active, subscribed_at) VALUES (?, 1, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('s', $this->email);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            $this->is_active = 1;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error subscribing to newsletter: {$stmt->error}");
        return false;
    }
    
    // Unsubscribe email from newsletter
    public function unsubscribe() {
        // Sanitize email
        $this->email = sanitize_input($this->email);
        
        // Get subscriber by email
        $subscriber = $this->get_by_email($this->email);
        
        if (!$subscriber) {
            return false; // Not subscribed
        }
        
        return $this->update_status($subscriber['id'], 0);
    }
    
    // Update subscription status
    private function update_status($id, $status) {
        // Create query
        $query = "UPDATE {$this->table} SET is_active = ? WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $status, $id);
        
        // Execute query
        if ($stmt->execute()) {
            $this->is_active = $status;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating newsletter status: {$stmt->error}");
        return false;
    }
    
    // Get subscriber by email
    private function get_by_email($email) {
        // Create query
        $query = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('s', $email);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Get all active subscribers
    public function get_active_subscribers() {
        // Create query
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY subscribed_at DESC";
        
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
    
    // Count active subscribers
    public function count_active_subscribers() {
        // Create query
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    // Delete subscriber
    public function delete($id) {
        // Create query
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('i', $id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error deleting newsletter subscriber: {$stmt->error}");
        return false;
    }
    
    // Get subscribers with pagination
    public function get_subscribers($limit = 10, $offset = 0, $active_only = false) {
        // Create query
        $query = "SELECT * FROM {$this->table}";
        
        if ($active_only) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY subscribed_at DESC LIMIT ?, ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $offset, $limit);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
}