<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';

class ProductController {
    private $product;
    private $category;
    private $review;
    
    public function __construct() {
        $this->product = new Product();
        $this->category = new Category();
        $this->review = new Review();
    }
    
    // Get all products with optional filtering and pagination
    public function get_products($category_id = null, $search = null, $sort = null, $limit = 12, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get products
        $products = $this->product->read_all($category_id, $search, $sort, $limit, $offset);
        
        // Get total products count for pagination
        $total_products = $this->product->count_products($category_id, $search);
        
        // Calculate total pages
        $total_pages = ceil($total_products / $limit);
        
        return [
            'products' => $products,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get single product by ID
    public function get_product($id) {
        $this->product->id = $id;
        
        if ($this->product->read_single()) {
            // Get product images
            $images = $this->product->get_images();
            
            // Get product reviews
            $this->review->product_id = $id;
            $reviews = $this->review->get_product_reviews($id, 5); // Get 5 most recent reviews
            
            // Get review statistics
            $rating_data = $this->review->get_average_rating($id);
            $rating_distribution = $this->review->get_rating_distribution($id);
            
            // Get related products
            $related_products = $this->product->get_related_products();
            
            return [
                'product' => $this->product,
                'images' => $images,
                'reviews' => $reviews,
                'rating_data' => $rating_data,
                'rating_distribution' => $rating_distribution,
                'related_products' => $related_products
            ];
        }
        
        return false;
    }
    
    // Get all categories
    public function get_categories() {
        return $this->category->read();
    }
    
    // Get products by category
    public function get_products_by_category($category_id, $limit = 12, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get category
        $this->category->id = $category_id;
        $category_exists = $this->category->read_single();
        
        if (!$category_exists) {
            return false;
        }
        
        // Get products in this category
        $products = $this->category->get_products($limit, $offset);
        
        // Get total products count for pagination
        $total_products = $this->category->count_products();
        
        // Calculate total pages
        $total_pages = ceil($total_products / $limit);
        
        return [
            'category' => $this->category,
            'products' => $products,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Search products
    public function search_products($query, $limit = 12, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Search products
        $products = $this->product->search($query, $limit, $offset);
        
        // Get total products count for pagination
        $total_products = $this->product->count_search_results($query);
        
        // Calculate total pages
        $total_pages = ceil($total_products / $limit);
        
        return [
            'query' => $query,
            'products' => $products,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get featured products
    public function get_featured_products($limit = 8) {
        return $this->product->get_featured_products($limit);
    }
    
    // Get new arrivals
    public function get_new_arrivals($limit = 8) {
        return $this->product->get_new_arrivals($limit);
    }
    
    // Get best selling products
    public function get_best_selling_products($limit = 8) {
        return $this->product->get_best_selling_products($limit);
    }
    
    // Get product reviews
    public function get_product_reviews($product_id, $limit = 10, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get reviews
        $reviews = $this->review->get_product_reviews($product_id, $limit, $offset);
        
        // Get total reviews count for pagination
        $total_reviews = $this->review->count_product_reviews($product_id);
        
        // Calculate total pages
        $total_pages = ceil($total_reviews / $limit);
        
        // Get rating statistics
        $rating_data = $this->review->get_average_rating($product_id);
        $rating_distribution = $this->review->get_rating_distribution($product_id);
        
        return [
            'reviews' => $reviews,
            'total_reviews' => $total_reviews,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'rating_data' => $rating_data,
            'rating_distribution' => $rating_distribution
        ];
    }
    
    // Add product review
    public function add_review($user_id, $product_id, $rating, $comment = '') {
        // Set review properties
        $this->review->user_id = $user_id;
        $this->review->product_id = $product_id;
        $this->review->rating = $rating;
        $this->review->comment = $comment;
        
        // Create review
        if ($this->review->create()) {
            return true;
        }
        
        return false;
    }
    
    // Admin: Create product
    public function create_product($data, $images = []) {
        // Set product properties
        $this->product->name = $data['name'];
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->category_id = $data['category_id'];
        $this->product->stock = $data['stock'] ?? 0;
        $this->product->sizes = $data['sizes'] ?? '';
        $this->product->colors = $data['colors'] ?? '';
        $this->product->brand = $data['brand'] ?? '';
        $this->product->is_featured = $data['is_featured'] ?? 0;
        $this->product->status = $data['status'] ?? 'active';
        
        // Create product
        if ($this->product->create()) {
            // Upload main image if provided
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $main_image = $this->upload_image($_FILES['main_image']);
                if ($main_image) {
                    $this->product->main_image = $main_image;
                    $this->product->update_main_image();
                }
            }
            
            // Upload additional images if provided
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image['error'] === UPLOAD_ERR_OK) {
                        $image_path = $this->upload_image($image);
                        if ($image_path) {
                            $this->product->add_image($image_path);
                        }
                    }
                }
            }
            
            return $this->product->id;
        }
        
        return false;
    }
    
    // Admin: Update product
    public function update_product($id, $data) {
        // Set product ID
        $this->product->id = $id;
        
        // Check if product exists
        if (!$this->product->read_single()) {
            return false;
        }
        
        // Set product properties
        $this->product->name = $data['name'];
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->category_id = $data['category_id'];
        $this->product->stock = $data['stock'] ?? $this->product->stock;
        $this->product->sizes = $data['sizes'] ?? $this->product->sizes;
        $this->product->colors = $data['colors'] ?? $this->product->colors;
        $this->product->brand = $data['brand'] ?? $this->product->brand;
        $this->product->is_featured = $data['is_featured'] ?? $this->product->is_featured;
        $this->product->status = $data['status'] ?? $this->product->status;
        
        // Update product
        if ($this->product->update()) {
            // Upload main image if provided
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $main_image = $this->upload_image($_FILES['main_image']);
                if ($main_image) {
                    $this->product->main_image = $main_image;
                    $this->product->update_main_image();
                }
            }
            
            // Upload additional images if provided
            if (isset($_FILES['additional_images'])) {
                $additional_images = $this->rearray_files($_FILES['additional_images']);
                
                foreach ($additional_images as $image) {
                    if ($image['error'] === UPLOAD_ERR_OK) {
                        $image_path = $this->upload_image($image);
                        if ($image_path) {
                            $this->product->add_image($image_path);
                        }
                    }
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    // Admin: Delete product
    public function delete_product($id) {
        $this->product->id = $id;
        return $this->product->delete();
    }
    
    // Admin: Delete product image
    public function delete_product_image($image_id) {
        return $this->product->delete_image($image_id);
    }
    
    // Helper: Upload image
    private function upload_image($file) {
        // Define upload directory
        $upload_dir = __DIR__ . '/../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . basename($file['name']);
        $target_file = $upload_dir . $filename;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return 'uploads/products/' . $filename; // Return relative path
        }
        
        return false;
    }
    
    // Helper: Re-array files array
    private function rearray_files($file_post) {
        $file_array = [];
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_array[$i][$key] = $file_post[$key][$i];
            }
        }
        
        return $file_array;
    }
}