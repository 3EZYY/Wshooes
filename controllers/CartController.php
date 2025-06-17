<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $cart;
    private $product;
    
    public function __construct() {
        $this->cart = new Cart();
        $this->product = new Product();
    }
    
    // Add item to cart
    public function add_to_cart($user_id, $product_id, $quantity = 1, $size = '', $color = '') {
        // Validate product exists and has stock
        $this->product->id = $product_id;
        if (!$this->product->read_single() || $this->product->stock < $quantity) {
            return [
                'success' => false,
                'message' => 'Produk tidak tersedia atau stok tidak mencukupi'
            ];
        }
        
        // Set cart properties
        $this->cart->user_id = $user_id;
        $this->cart->product_id = $product_id;
        $this->cart->quantity = $quantity;
        $this->cart->size = $size;
        $this->cart->color = $color;
        
        // Add to cart
        if ($this->cart->add_item()) {
            return [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_count' => $this->cart->count_items($user_id)
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal menambahkan produk ke keranjang'
        ];
    }
    
    // Update cart item quantity
    public function update_quantity($user_id, $cart_id, $quantity) {
        // Validate quantity
        if ($quantity < 1) {
            return [
                'success' => false,
                'message' => 'Jumlah produk minimal 1'
            ];
        }
        
        // Set cart user ID for security check
        $this->cart->user_id = $user_id;
        
        // Update quantity
        if ($this->cart->update_quantity($cart_id, $quantity)) {
            // Get updated cart total
            $cart_total = $this->cart->calculate_total($user_id);
            
            return [
                'success' => true,
                'message' => 'Jumlah produk berhasil diperbarui',
                'cart_total' => $cart_total
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memperbarui jumlah produk'
        ];
    }
    
    // Remove item from cart
    public function remove_from_cart($user_id, $cart_id) {
        // Set cart user ID for security check
        $this->cart->user_id = $user_id;
        
        // Remove item
        if ($this->cart->remove_item($cart_id)) {
            // Get updated cart total and count
            $cart_total = $this->cart->calculate_total($user_id);
            $cart_count = $this->cart->count_items($user_id);
            
            return [
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang',
                'cart_total' => $cart_total,
                'cart_count' => $cart_count
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal menghapus produk dari keranjang'
        ];
    }
    
    // Clear cart
    public function clear_cart($user_id) {
        if ($this->cart->clear_cart($user_id)) {
            return [
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal mengosongkan keranjang'
        ];
    }
    
    // Get cart items
    public function get_cart_items($user_id) {
        // Get cart items
        $cart_items = $this->cart->get_cart_items($user_id);
        
        // Get cart total
        $cart_total = $this->cart->calculate_total($user_id);
        
        // Get cart count
        $cart_count = $this->cart->count_items($user_id);
        
        return [
            'items' => $cart_items,
            'total' => $cart_total,
            'count' => $cart_count
        ];
    }
    
    // Move cart to order
    public function move_to_order($user_id, $order_id) {
        return $this->cart->move_to_order($user_id, $order_id);
    }
    
    // Get cart count (for header display)
    public function get_cart_count($user_id) {
        return $this->cart->count_items($user_id);
    }
    
    // Check if cart has items
    public function has_items($user_id) {
        return $this->cart->count_items($user_id) > 0;
    }
    
    // Validate cart items (check stock availability)
    public function validate_cart($user_id) {
        $cart_items = $this->cart->get_cart_items($user_id);
        $invalid_items = [];
        
        if (!$cart_items || $cart_items->num_rows === 0) {
            return [
                'valid' => false,
                'message' => 'Keranjang kosong',
                'invalid_items' => []
            ];
        }
        
        while ($item = $cart_items->fetch_assoc()) {
            // Check if product still exists and has enough stock
            $this->product->id = $item['product_id'];
            
            if (!$this->product->read_single() || $this->product->status !== 'active') {
                $invalid_items[] = [
                    'cart_id' => $item['id'],
                    'product_name' => $item['name'],
                    'reason' => 'Produk tidak tersedia'
                ];
            } elseif ($this->product->stock < $item['quantity']) {
                $invalid_items[] = [
                    'cart_id' => $item['id'],
                    'product_name' => $item['name'],
                    'reason' => 'Stok tidak mencukupi (tersedia: ' . $this->product->stock . ')',
                    'available_stock' => $this->product->stock
                ];
            }
        }
        
        if (empty($invalid_items)) {
            return [
                'valid' => true,
                'message' => 'Semua produk tersedia',
                'invalid_items' => []
            ];
        }
        
        return [
            'valid' => false,
            'message' => 'Beberapa produk tidak tersedia atau stok tidak mencukupi',
            'invalid_items' => $invalid_items
        ];
    }
}