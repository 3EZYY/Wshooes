<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

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
    public $address; // Temporary property for address during registration
    public $created_at;
    public $updated_at;
    public $remember_token;
    public $remember_expires;
    public $reset_token;
    public $token_expires;
    
    public function __construct() {
        try {
            $db = Database::getInstance();
            $this->conn = $db->getConnection();
        } catch (Exception $e) {
            error_log("Failed to initialize User model: " . $e->getMessage());
            throw new Exception("Failed to initialize database connection");
        }
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
            
            // If address is provided, save it to user_addresses table
            if (!empty($this->address)) {
                $this->save_address($this->address);
            }
            
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Save user address
    public function save_address($address, $city = 'Unknown', $postal_code = '00000', $is_default = true) {
        $query = "INSERT INTO user_addresses (user_id, address, city, postal_code, is_default, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Failed to prepare address statement: " . $this->conn->error);
            return false;
        }
        
        $address = sanitize_input($address);
        $city = sanitize_input($city);
        $postal_code = sanitize_input($postal_code);
        
        $stmt->bind_param('isssi', $this->id, $address, $city, $postal_code, $is_default);
        
        if ($stmt->execute()) {
            return true;
        }
        
        error_log("Error saving address: {$stmt->error}");
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
        try {
            // Check if connection is valid
            if (!$this->conn || !$this->conn->ping()) {
                $db = Database::getInstance();
                $this->conn = $db->getConnection();
            }

            $query = "SELECT * FROM {$this->table} WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            
            $email = Database::sanitize_input($email);
            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                throw new Exception("Failed to get result: " . $stmt->error);
            }
            
            if ($row = $result->fetch_assoc()) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->full_name = $row['full_name'];
                $this->role = $row['role'];
                $this->reset_token = $row['reset_token'] ?? null;
                $this->token_expires = $row['token_expires'] ?? null;
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in User::get_by_email(): " . $e->getMessage());
            throw $e;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
    
    // Get user by username
    public function get_by_username($username) {
        try {
            // Check if connection is valid
            if (!$this->conn || !$this->conn->ping()) {
                $db = Database::getInstance();
                $this->conn = $db->getConnection();
            }

            $query = "SELECT * FROM {$this->table} WHERE username = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            
            $username = sanitize_input($username);
            $stmt->bind_param("s", $username);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                throw new Exception("Failed to get result: " . $stmt->error);
            }
            
            if ($row = $result->fetch_assoc()) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->full_name = $row['full_name'];
                $this->role = $row['role'];
                $this->reset_token = $row['reset_token'] ?? null;
                $this->token_expires = $row['token_expires'] ?? null;
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in User::get_by_username(): " . $e->getMessage());
            throw $e;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
    
    // Update user
    public function update() {
        try {
            $query = "UPDATE " . $this->table . "
                    SET username = ?, 
                        email = ?, 
                        full_name = ?,
                        reset_token = ?,
                        token_expires = ?,
                        updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->full_name = htmlspecialchars(strip_tags($this->full_name));
            
            // Bind data
            $stmt->bind_param("sssssi",
                $this->username,
                $this->email,
                $this->full_name,
                $this->reset_token,
                $this->token_expires,
                $this->id
            );
            
            if($stmt->execute()){
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in User::update(): " . $e->getMessage());
            return false;
        }
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
        try {
            return password_verify($password, $this->password);
        } catch (Exception $e) {
            error_log("Error in User::verify_password(): " . $e->getMessage());
            throw new Exception("Failed to verify password");
        }
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
    
    // Set remember token
    public function set_remember_token($token, $expires) {
        $query = "UPDATE {$this->table} SET remember_token = ?, token_expires = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $expires_date = date('Y-m-d H:i:s', $expires);
        $stmt->bind_param('ssi', $token, $expires_date, $this->id);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    // Clear remember token
    public function clear_remember_token() {
        $sql = "UPDATE users SET remember_token = NULL, token_expires = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->id]);
    }
    
    // Get user by remember token
    public function get_user_by_remember_token($token) {
        $sql = "SELECT * FROM users WHERE remember_token = ? AND token_expires > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token]);
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->full_name = $user['full_name'];
            $this->role = $user['role'];
            return true;
        }
        
        return false;
    }
    
    // Update user profile picture
    public function update_profile_picture() {
        $query = "UPDATE {$this->table} SET profile_picture = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param('si', $this->profile_image, $this->id);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    // Save reset token
    public function save_reset_token($token, $expires) {
        $query = "UPDATE {$this->table} SET reset_token = ?, token_expires = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param('ssi', $token, $expires, $this->id);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    // Verify reset token
    public function verify_reset_token($token) {
        $query = "SELECT id FROM {$this->table} WHERE reset_token = ? AND token_expires > NOW()";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param('s', $token);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $this->id = $row['id'];
            return true;
        }
        
        return false;
    }
    
    // Clear reset token
    public function clear_reset_token() {
        $query = "UPDATE {$this->table} SET reset_token = NULL, token_expires = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param('i', $this->id);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        return true;
    }
    
    // Update password
    public function update_password($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param('si', $hashed_password, $this->id);
        
        if (!$stmt->execute()) {
            error_log("Execute error: " . $stmt->error);
            return false;
        }
        
        return true;
    }
}