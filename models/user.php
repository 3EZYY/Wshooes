<?php
require_once __DIR__ . '/../config/connection.php';

class User {
    private $conn;
    private $table = 'users';
    
    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $full_name;
    public $profile_image;
    public $phone_number;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    // Create new user
    public function create() {
        // Sanitize inputs
        $this->username = sanitize_input($this->username);
        $this->email = sanitize_input($this->email);
        $this->full_name = sanitize_input($this->full_name ?? '');
        $this->phone_number = sanitize_input($this->phone_number ?? '');
        
        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Create query
        $query = "INSERT INTO {$this->table} 
                  (username, email, password, role, full_name, phone_number, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $role = $this->role ?? 'customer';
        $stmt->bind_param('ssssss', 
            $this->username, 
            $this->email, 
            $this->password, 
            $role,
            $this->full_name,
            $this->phone_number
        );
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Get user by ID
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
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->full_name = $row['full_name'];
            $this->profile_image = $row['profile_image'];
            $this->phone_number = $row['phone_number'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Get user by email
    public function get_by_email($email) {
        // Sanitize email
        $email = sanitize_input($email);
        
        // Create query
        $query = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind email
        $stmt->bind_param('s', $email);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Set properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password']; // Keep hashed password for verification
            $this->role = $row['role'];
            $this->full_name = $row['full_name'];
            $this->profile_image = $row['profile_image'];
            $this->phone_number = $row['phone_number'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update user
    public function update() {
        // Sanitize inputs
        $this->username = sanitize_input($this->username);
        $this->email = sanitize_input($this->email);
        $this->full_name = sanitize_input($this->full_name ?? '');
        $this->phone_number = sanitize_input($this->phone_number ?? '');
        $this->profile_image = sanitize_input($this->profile_image ?? '');
        
        // Create query
        $query = "UPDATE {$this->table} 
                  SET username = ?, 
                      email = ?, 
                      full_name = ?, 
                      phone_number = ?,
                      profile_image = ?,
                      updated_at = NOW() 
                  WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('sssssi', 
            $this->username, 
            $this->email, 
            $this->full_name,
            $this->phone_number,
            $this->profile_image,
            $this->id
        );
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Change password
    public function change_password($new_password) {
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Create query
        $query = "UPDATE {$this->table} SET password = ?, updated_at = NOW() WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('si', $hashed_password, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Verify password
    public function verify_password($password) {
        return password_verify($password, $this->password);
    }
    
    // Get all users (admin function)
    public function read() {
        // Create query
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        
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
    
    // Delete user (admin function)
    public function delete() {
        // Create query
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
    
    // Set remember token for user
    public function set_remember_token($token, $expires) {
        // Create query
        $query = "UPDATE {$this->table} SET remember_token = ?, token_expires = ? WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Convert expires timestamp to MySQL datetime
        $expires_date = date('Y-m-d H:i:s', $expires);
        
        // Bind parameters
        $stmt->bind_param('ssi', $token, $expires_date, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Clear remember token
    public function clear_remember_token() {
        // Create query
        $query = "UPDATE {$this->table} SET remember_token = NULL, token_expires = NULL WHERE id = ?";
        
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
    
    // Get user by remember token
    public function get_user_by_remember_token($token) {
        // Sanitize token
        $token = sanitize_input($token);
        
        // Create query
        $query = "SELECT * FROM {$this->table} WHERE remember_token = ? AND token_expires > NOW() LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind token
        $stmt->bind_param('s', $token);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Set properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            $this->full_name = $row['full_name'];
            $this->profile_image = $row['profile_image'];
            $this->phone_number = $row['phone_number'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
}