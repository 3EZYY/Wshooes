<?php
require_once __DIR__ . '/../config/connection.php';

// Helper function for sanitizing input if not already defined elsewhere
if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

// Helper function for handling database errors if not already defined elsewhere
if (!function_exists('handle_db_error')) {
    function handle_db_error($query = '') {
        global $conn;
        $error_message = "Database error: " . $conn->error;
        if (!empty($query)) {
            $error_message .= " in query: " . $query;
        }
        error_log($error_message);
        return false;
    }
}

class Order {
    private $conn;
    private $table = 'orders';
    private $items_table = 'order_items';
    
    // Order properties
    public $id;
    public $order_number;
    public $user_id;
    public $total_amount;
    public $shipping_cost;
    public $tax_amount;
    public $discount_amount;
    public $status;
    public $payment_status;
    public $payment_method;
    public $shipping_address;
    public $shipping_city;
    public $shipping_postal_code;
    public $notes;
    public $tracking_number;
    public $shipped_at;    public $delivered_at;
    public $created_at;
    public $updated_at;
    
    // Additional properties for customer info
    public $customer_name;
    public $customer_email;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // Generate unique order number
    private function generate_order_number() {
        $prefix = 'WSH';
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return $prefix . $timestamp . $random;
    }
    
    // Create new order
    public function create() {
        // Generate order number if not provided
        if (empty($this->order_number)) {
            $this->order_number = $this->generate_order_number();
        }
        
        // Sanitize inputs
        $this->order_number = sanitize_input($this->order_number);
        $this->shipping_address = sanitize_input($this->shipping_address ?? '');
        $this->shipping_city = sanitize_input($this->shipping_city ?? '');
        $this->shipping_postal_code = sanitize_input($this->shipping_postal_code ?? '');
        $this->notes = sanitize_input($this->notes ?? '');
        $this->payment_method = sanitize_input($this->payment_method ?? '');
        $this->tracking_number = sanitize_input($this->tracking_number ?? '');
        
        // Set default values if not provided
        $this->status = $this->status ?? 'pending';
        $this->payment_status = $this->payment_status ?? 'pending';
        $this->shipping_cost = $this->shipping_cost ?? 0;
        $this->tax_amount = $this->tax_amount ?? 0;
        $this->discount_amount = $this->discount_amount ?? 0;
        
        // Begin transaction
        $this->conn->begin_transaction();
        
        try {
            // Create query
            $query = "INSERT INTO {$this->table} (
                        order_number, user_id, total_amount, shipping_cost, 
                        tax_amount, discount_amount, status, payment_status, 
                        payment_method, shipping_address, shipping_city, 
                        shipping_postal_code, notes, tracking_number, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $this->conn->error);
            }
            
            // Bind parameters
            $stmt->bind_param('sidddssssssss', 
                $this->order_number, 
                $this->user_id, 
                $this->total_amount, 
                $this->shipping_cost, 
                $this->tax_amount, 
                $this->discount_amount, 
                $this->status, 
                $this->payment_status, 
                $this->payment_method, 
                $this->shipping_address, 
                $this->shipping_city, 
                $this->shipping_postal_code, 
                $this->notes, 
                $this->tracking_number
            );
            
            // Execute query
            if (!$stmt->execute()) {
                throw new Exception("Error executing order insert: " . $stmt->error);
            }
            
            $this->id = $this->conn->insert_id;
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Order creation error: " . $e->getMessage());
            return false;
        }
    }
    
    // Add order item
    public function add_item($product_id, $quantity, $price, $product_name = '', $product_image = '', $size = '', $color = '') {
        try {
            // Sanitize inputs
            $product_name = sanitize_input($product_name);
            $product_image = sanitize_input($product_image);
            $size = sanitize_input($size);
            $color = sanitize_input($color);
            
            // Calculate total for this item
            $total = $price * $quantity;
              // Create query
            $query = "INSERT INTO {$this->items_table} (
                        order_id, product_id, quantity, price
                    ) VALUES (?, ?, ?, ?)";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $this->conn->error);
            }
              // Bind parameters
            $stmt->bind_param('iiid', 
                $this->id, 
                $product_id, 
                $quantity, 
                $price
            );
            
            // Execute query
            if (!$stmt->execute()) {
                throw new Exception("Error adding order item: " . $stmt->error);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Add order item error: " . $e->getMessage());
            return false;
        }
    }
    
    // Read all orders (with optional filtering and pagination)
    public function read($user_id = null, $status = null, $limit = null, $offset = 0) {
        // Create base query
        $query = "SELECT o.*, u.full_name, u.email 
                  FROM {$this->table} o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add filters if provided
        if ($user_id) {
            $query .= " AND o.user_id = ?";
            $params[] = $user_id;
            $types .= 'i';
        }
        
        if ($status) {
            $query .= " AND o.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        // Add ordering
        $query .= " ORDER BY o.created_at DESC";
        
        // Add limit if provided
        if ($limit) {
            $query .= " LIMIT ?, ?";
            $params[] = $offset;
            $params[] = $limit;
            $types .= 'ii';
        }
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Read single order
    public function read_single() {
        // Create query
        $query = "SELECT o.*, u.full_name, u.email 
                  FROM {$this->table} o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.id = ? OR o.order_number = ? 
                  LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Determine if we're searching by ID or order number
        if (is_numeric($this->id)) {
            $search_param = $this->id;
            $order_number = $this->order_number ?? '';
        } else {
            $search_param = 0;
            $order_number = $this->id; // Using ID field for order number search
        }
        
        // Bind parameters
        $stmt->bind_param('is', $search_param, $order_number);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Set properties
            $this->id = $row['id'];
            $this->order_number = $row['order_number'];
            $this->user_id = $row['user_id'];
            $this->total_amount = $row['total_amount'];
            $this->shipping_cost = $row['shipping_cost'];
            $this->tax_amount = $row['tax_amount'];
            $this->discount_amount = $row['discount_amount'];
            $this->status = $row['status'];
            $this->payment_status = $row['payment_status'];
            $this->payment_method = $row['payment_method'];
            $this->shipping_address = $row['shipping_address'];
            $this->shipping_city = $row['shipping_city'];
            $this->shipping_postal_code = $row['shipping_postal_code'];
            $this->notes = $row['notes'];
            $this->tracking_number = $row['tracking_number'];
            $this->shipped_at = $row['shipped_at'];
            $this->delivered_at = $row['delivered_at'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Add customer info
            $this->customer_name = $row['full_name'];
            $this->customer_email = $row['email'];
            
            return true;
        }
        
        return false;
    }
    
    // Get order items
    public function get_items() {
        // Create query
        $query = "SELECT * FROM {$this->items_table} WHERE order_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind order ID
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Update order status
    public function update_status($status) {
        // Validate status
        $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            return false;
        }
        
        // Create query
        $query = "UPDATE {$this->table} SET status = ?, updated_at = NOW()";
        $params = [$status];
        $types = 's';
        
        // Add shipped_at date if status is shipped
        if ($status === 'shipped' && empty($this->shipped_at)) {
            $query .= ", shipped_at = NOW()";
        }
        
        // Add delivered_at date if status is delivered
        if ($status === 'delivered' && empty($this->delivered_at)) {
            $query .= ", delivered_at = NOW()";
        }
        
        // Complete the query
        $query .= " WHERE id = ?";
        $params[] = $this->id;
        $types .= 'i';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);
        
        // Execute query
        if ($stmt->execute()) {
            $this->status = $status;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating order status: {$stmt->error}");
        return false;
    }
    
    // Update payment status
    public function update_payment_status($payment_status) {
        // Validate payment status
        $valid_statuses = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($payment_status, $valid_statuses)) {
            return false;
        }
        
        // Create query
        $query = "UPDATE {$this->table} SET payment_status = ?, updated_at = NOW() WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('si', $payment_status, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            $this->payment_status = $payment_status;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating payment status: {$stmt->error}");
        return false;
    }
    
    // Update tracking information
    public function update_tracking($tracking_number) {
        // Sanitize input
        $tracking_number = sanitize_input($tracking_number);
        
        // Create query
        $query = "UPDATE {$this->table} SET tracking_number = ?, updated_at = NOW() WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('si', $tracking_number, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            $this->tracking_number = $tracking_number;
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating tracking number: {$stmt->error}");
        return false;
    }
    
    // Update order information
    public function update() {
        // Sanitize inputs
        $this->order_number = sanitize_input($this->order_number);
        $this->shipping_address = sanitize_input($this->shipping_address ?? '');
        $this->shipping_city = sanitize_input($this->shipping_city ?? '');
        $this->shipping_postal_code = sanitize_input($this->shipping_postal_code ?? '');
        $this->notes = sanitize_input($this->notes ?? '');
        $this->payment_method = sanitize_input($this->payment_method ?? '');
        $this->tracking_number = sanitize_input($this->tracking_number ?? '');
        
        try {
            // Create query
            $query = "UPDATE {$this->table} SET 
                        shipping_address = ?, 
                        shipping_city = ?, 
                        shipping_postal_code = ?, 
                        notes = ?, 
                        payment_method = ?, 
                        tracking_number = ?, 
                        updated_at = NOW() 
                      WHERE id = ?";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Database error: " . $this->conn->error);
            }
            
            // Bind parameters
            $stmt->bind_param('ssssssi', 
                $this->shipping_address, 
                $this->shipping_city, 
                $this->shipping_postal_code, 
                $this->notes, 
                $this->payment_method, 
                $this->tracking_number, 
                $this->id
            );
            
            // Execute query
            if (!$stmt->execute()) {
                throw new Exception("Error updating order: " . $stmt->error);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Order update error: " . $e->getMessage());
            return false;
        }
    }
    
    // Count orders
    public function count_orders($user_id = null, $status = null) {
        // Create base query
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add filters if provided
        if ($user_id) {
            $query .= " AND user_id = ?";
            $params[] = $user_id;
            $types .= 'i';
        }
        
        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    // Get order statistics
    public function get_statistics($period = 'month') {
        $stats = [];
        
        // Total orders
        $stats['total_orders'] = $this->count_orders();
        
        // Orders by status
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $stats['orders_by_status'] = [];
        
        foreach ($statuses as $status) {
            $stats['orders_by_status'][$status] = $this->count_orders(null, $status);
        }
        
        // Revenue calculation
        $query = "SELECT 
                    SUM(total_amount) as total_revenue,
                    COUNT(*) as order_count
                  FROM {$this->table} 
                  WHERE payment_status = 'paid'";
        
        // Add time period filter
        if ($period === 'today') {
            $query .= " AND DATE(created_at) = CURDATE()";
        } elseif ($period === 'week') {
            $query .= " AND YEARWEEK(created_at) = YEARWEEK(CURDATE())";
        } elseif ($period === 'month') {
            $query .= " AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        } elseif ($period === 'year') {
            $query .= " AND YEAR(created_at) = YEAR(CURDATE())";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return $stats;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stats['revenue'] = $row['total_revenue'] ?? 0;
        $stats['paid_orders'] = $row['order_count'] ?? 0;
        
        return $stats;
    }
}