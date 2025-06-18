<?php
require_once __DIR__ . '/../config/connection.php';

class Product {
    private $conn;
    private $table = 'products';
    
    // Product properties
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $category_id;
    public $category_name;
    public $brand;
    public $sizes;
    public $colors;
    public $main_image;
    public $status;
    public $is_featured;
    public $rating;
    public $total_reviews;
    public $created_at;
    public $updated_at;
      public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // Create new product
    public function create() {
        // Sanitize inputs
        $this->name = sanitize_input($this->name);
        $this->description = sanitize_input($this->description);
        $this->price = floatval($this->price);
        $this->stock = intval($this->stock);
        $this->category_id = intval($this->category_id);
        $this->main_image = sanitize_input($this->main_image ?? '');
        $this->status = sanitize_input($this->status ?? 'active');
        $this->is_featured = $this->is_featured ? 1 : 0;
        $this->brand = sanitize_input($this->brand ?? '');
        $this->sizes = sanitize_input($this->sizes ?? '');
        $this->colors = sanitize_input($this->colors ?? '');
        
        // Create query
        $query = "INSERT INTO {$this->table}
                  (name, description, price, stock, category_id, brand, sizes, colors, main_image, status, is_featured, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ssdissssssi',
            $this->name,
            $this->description,
            $this->price,
            $this->stock,
            $this->category_id,
            $this->brand,
            $this->sizes,
            $this->colors,
            $this->main_image,
            $this->status,
            $this->is_featured
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
    
    // Read all products
    public function read($limit = null, $offset = 0, $category_id = null, $featured_only = false) {
        // Create base query
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active'";
        
        // Add category filter if provided
        if ($category_id) {
            $query .= " AND p.category_id = " . intval($category_id);
        }
        
        // Add featured filter if requested
        if ($featured_only) {
            $query .= " AND p.is_featured = 1";
        }
        
        // Add ordering
        $query .= " ORDER BY p.created_at DESC";
        
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
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Alias for read() to match ProductController usage
    public function read_all($category_id = null, $search = null, $sort = null, $limit = null, $offset = 0) {
        // If search is provided, use search method instead
        if (!empty($search)) {
            return $this->search($search, $limit, $offset);
        }
        
        // Create base query
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active'";
        
        // Add category filter if provided
        if ($category_id) {
            $query .= " AND p.category_id = " . intval($category_id);
        }
        
        // Add sorting
        if ($sort) {
            switch ($sort) {
                case 'price_low':
                    $query .= " ORDER BY p.price ASC";
                    break;
                case 'price_high':
                    $query .= " ORDER BY p.price DESC";
                    break;
                case 'newest':
                    $query .= " ORDER BY p.created_at DESC";
                    break;
                case 'rating':
                    $query .= " ORDER BY p.rating DESC";
                    break;
                default:
                    $query .= " ORDER BY p.created_at DESC";
            }
        } else {
            $query .= " ORDER BY p.created_at DESC";
        }
        
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
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Read single product
    public function read_single() {
        // Create query
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ? AND p.status = 'active' 
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
            
            // Populate object properties from the database row
            $this->hydrate($row);
            
            return true;
        }
        
        return false;
    }
    
    // Update product
    public function update() {
        // Sanitize inputs
        $this->name = sanitize_input($this->name);
        $this->description = sanitize_input($this->description);
        $this->price = floatval($this->price);
        $this->stock = intval($this->stock);
        $this->category_id = intval($this->category_id);
        $this->main_image = sanitize_input($this->main_image ?? '');
        $this->status = sanitize_input($this->status);
        $this->is_featured = $this->is_featured ? 1 : 0;
        $this->brand = sanitize_input($this->brand);
        $this->sizes = sanitize_input($this->sizes);
        $this->colors = sanitize_input($this->colors);
        
        // Create query
        $query = "UPDATE {$this->table}
                  SET name = ?,
                      description = ?,
                      price = ?,
                      stock = ?,
                      category_id = ?,
                      brand = ?,
                      sizes = ?,
                      colors = ?,
                      main_image = ?,
                      status = ?,
                      is_featured = ?,
                      updated_at = NOW()
                  WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ssdissssssii',
            $this->name,
            $this->description,
            $this->price,
            $this->stock,
            $this->category_id,
            $this->brand,
            $this->sizes,
            $this->colors,
            $this->main_image,
            $this->status,
            $this->is_featured,
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
    
    // Delete product (soft delete)
    public function delete() {
        // Create query - we're doing a soft delete by setting status to 'deleted'
        $query = "UPDATE {$this->table} SET status = 'deleted', updated_at = NOW() WHERE id = ?";
        
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
    
    // Search products
    public function search($keyword, $limit = 10, $offset = 0) {
        // Sanitize keyword
        $keyword = sanitize_input($keyword);
        $keyword = "%{$keyword}%";
        
        // Create query
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active' AND 
                        (p.name LIKE ? OR p.description LIKE ?) 
                  ORDER BY p.created_at DESC 
                  LIMIT ?, ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Convert limit and offset to integers
        $limit_int = intval($limit);
        $offset_int = intval($offset);
        
        // Bind parameters
        $stmt->bind_param('ssii', $keyword, $keyword, $offset_int, $limit_int);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Update product stock
    public function update_stock($quantity) {
        // Create query
        $query = "UPDATE {$this->table} 
                  SET stock = stock - ?, 
                      updated_at = NOW() 
                  WHERE id = ? AND stock >= ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Ensure quantity is positive
        $quantity = abs(intval($quantity));
        
        // Bind parameters
        $stmt->bind_param('iii', $quantity, $this->id, $quantity);
        
        // Execute query
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                // No rows affected means stock was insufficient
                return false;
            }
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Get product images
    public function get_images() {
        // Create query
        $query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY id ASC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind product ID
        $stmt->bind_param('i', $this->id);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Add product image
    public function add_image($image_url) {
        // Sanitize input
        $image_url = sanitize_input($image_url);
        
        // Create query
        $query = "INSERT INTO product_images (product_id, image_url, created_at) VALUES (?, ?, NOW())";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('is', $this->id, $image_url);
        
        // Execute query
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Update main image
    public function update_main_image() {
        // Sanitize input
        $this->main_image = sanitize_input($this->main_image);
        
        // Create query
        $query = "UPDATE {$this->table} SET main_image = ?, updated_at = NOW() WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('si', $this->main_image, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error updating main image: {$stmt->error}");
        return false;
    }
    
    // Delete product image
    public function delete_image($image_id) {
        // Create query
        $query = "DELETE FROM product_images WHERE id = ? AND product_id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('ii', $image_id, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error deleting image: {$stmt->error}");
        return false;
    }
    
    // Update product rating
    public function update_rating() {
        // Create query to calculate average rating
        $query = "UPDATE {$this->table} p 
                  SET p.rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?), 
                      p.total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id = ?), 
                      p.updated_at = NOW() 
                  WHERE p.id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $stmt->bind_param('iii', $this->id, $this->id, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        // Print error if something goes wrong
        error_log("Error: {$stmt->error}");
        return false;
    }
    
    // Count products for pagination
    public function count_products($category_id = null, $search = null) {
        // Create base query
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'active'";
        
        // Add category filter if provided
        if ($category_id) {
            $query .= " AND category_id = " . intval($category_id);
        }
        
        // Add search filter if provided
        if (!empty($search)) {
            $search = sanitize_input($search);
            $search = "%{$search}%";
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            
            // Prepare statement with search parameters
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                handle_db_error($query);
                return 0;
            }
            
            // Bind search parameters
            $stmt->bind_param('ss', $search, $search);
        } else {
            // Prepare statement without search parameters
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                handle_db_error($query);
                return 0;
            }
        }
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    // Count search results for pagination
    public function count_search_results($keyword) {
        // Sanitize keyword
        $keyword = sanitize_input($keyword);
        $keyword = "%{$keyword}%";
        
        // Create query
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE status = 'active' AND 
                        (name LIKE ? OR description LIKE ?)";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return 0;
        }
        
        // Bind parameters
        $stmt->bind_param('ss', $keyword, $keyword);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    // Get featured products
    public function get_featured_products($limit = 8) {
        return $this->read($limit, 0, null, true);
    }
    
    // Get new arrivals
    public function get_new_arrivals($limit = 8) {
        // Create query
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active' 
                  ORDER BY p.created_at DESC 
                  LIMIT ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind limit
        $limit_int = intval($limit);
        $stmt->bind_param('i', $limit_int);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get best selling products
    public function get_best_selling_products($limit = 8) {
        // Create query based on order items count
        $query = "SELECT p.*, c.name as category_name, COUNT(oi.id) as order_count 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN order_items oi ON p.id = oi.product_id 
                  WHERE p.status = 'active' 
                  GROUP BY p.id 
                  ORDER BY order_count DESC 
                  LIMIT ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind limit
        $limit_int = intval($limit);
        $stmt->bind_param('i', $limit_int);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get related products
    public function get_related_products($limit = 4) {
        // Create query to get products in the same category
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active' 
                  AND p.category_id = ? 
                  AND p.id != ? 
                  ORDER BY RAND() 
                  LIMIT ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            handle_db_error($query);
            return false;
        }
        
        // Bind parameters
        $limit_int = intval($limit);
        $stmt->bind_param('iii', $this->category_id, $this->id, $limit_int);
        
        // Execute query
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Hydrates the object with data from an associative array.
     * This method populates the object's properties, casting them to appropriate types.
     *
     * @param array $data Associative array of property => value.
     */
    private function hydrate(array $data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                // Type cast values for data consistency
                switch ($key) {
                    case 'id':
                    case 'stock':
                    case 'category_id':
                    case 'total_reviews':
                        $this->$key = (int) $value;
                        break;
                    case 'price':
                    case 'rating':
                        $this->$key = (float) $value;
                        break;
                    case 'is_featured':
                        $this->$key = (bool) $value;
                        break;
                    default:
                        $this->$key = $value;
                        break;
                }
            }
        }
    }
    
    // Get product by ID
    public function get_by_id($id) {
        try {
            // Sanitize input
            $id = intval($id);
            
            if ($id <= 0) {
                return false;
            }
            
            // Create query with category join
            $query = "SELECT p.*, c.name as category_name 
                      FROM {$this->table} p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.id = ? AND p.status = 'active'
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                error_log("Failed to prepare statement: " . $this->conn->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                error_log("Failed to execute statement: " . $stmt->error);
                return false;
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Set object properties
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->description = $row['description'];
                $this->price = $row['price'];
                $this->stock = $row['stock'];
                $this->category_id = $row['category_id'];
                $this->category_name = $row['category_name'];
                $this->brand = $row['brand'];
                $this->sizes = $row['sizes'];
                $this->colors = $row['colors'];
                $this->main_image = $row['main_image'];
                $this->status = $row['status'];
                $this->is_featured = $row['is_featured'];
                $this->rating = $row['rating'] ?? 0;
                $this->total_reviews = $row['total_reviews'] ?? 0;
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                
                return $row; // Return array for easier use
            }
            
            return false;
              } catch (Exception $e) {
            error_log("Error in Product::get_by_id(): " . $e->getMessage());
            return false;
        }
    }
    
    // Get products by category name
    public function get_by_category($category_name, $limit = 8) {
        try {
            // Sanitize input
            $category_name = strtolower(trim($category_name));
            $limit = intval($limit);
            
            if (empty($category_name)) {
                return [];
            }
            
            // Create query to get products by category name
            $query = "SELECT p.*, c.name as category_name 
                      FROM {$this->table} p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE LOWER(c.name) = ? AND p.status = 'active'
                      ORDER BY p.created_at DESC 
                      LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                error_log("Failed to prepare statement: " . $this->conn->error);
                return [];
            }
            
            $stmt->bind_param("si", $category_name, $limit);
            
            if (!$stmt->execute()) {
                error_log("Failed to execute statement: " . $stmt->error);
                return [];
            }
            
            $result = $stmt->get_result();
            $products = [];
            
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return $products;
            
        } catch (Exception $e) {
            error_log("Error in Product::get_by_category(): " . $e->getMessage());
            return [];
        }
    }
}