<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/User.php';

class OrderController {
    private $order;
    private $cart;
    private $user;
    
    public function __construct() {
        $this->order = new Order();
        $this->cart = new Cart();
        $this->user = new User();
    }
    
    // Create new order
    public function create_order($user_id, $order_data) {
        // Validate cart has items
        if (!$this->cart->count_items($user_id)) {
            return [
                'success' => false,
                'message' => 'Keranjang kosong'
            ];
        }
        
        // Get cart total
        $cart_total = $this->cart->calculate_total($user_id);
        
        // Set order properties
        $this->order->user_id = $user_id;
        $this->order->total_amount = $cart_total + ($order_data['shipping_cost'] ?? 0);
        $this->order->shipping_cost = $order_data['shipping_cost'] ?? 0;
        $this->order->tax_amount = $order_data['tax_amount'] ?? 0;
        $this->order->discount_amount = $order_data['discount_amount'] ?? 0;
        $this->order->shipping_address = $order_data['shipping_address'] ?? '';
        $this->order->shipping_city = $order_data['shipping_city'] ?? '';
        $this->order->shipping_postal_code = $order_data['shipping_postal_code'] ?? '';
        $this->order->notes = $order_data['notes'] ?? '';
        $this->order->payment_method = $order_data['payment_method'] ?? 'bank_transfer';
        
        // Create order
        if ($this->order->create()) {
            // Move cart items to order
            if ($this->cart->move_to_order($user_id, $this->order->id)) {
                return [
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat',
                    'order_id' => $this->order->id,
                    'order_number' => $this->order->order_number
                ];
            } else {
                // If failed to move cart items, delete the order
                // This should be handled by transaction in the model, but just in case
                return [
                    'success' => false,
                    'message' => 'Gagal memproses item pesanan'
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Gagal membuat pesanan'
        ];
    }
    
    // Get user orders
    public function get_user_orders($user_id, $limit = 10, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get orders
        $orders = $this->order->read($user_id, null, $limit, $offset);
        
        // Get total orders count for pagination
        $total_orders = $this->order->count_orders($user_id);
        
        // Calculate total pages
        $total_pages = ceil($total_orders / $limit);
        
        return [
            'orders' => $orders,
            'total_orders' => $total_orders,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Get single order
    public function get_order($order_id, $user_id = null) {
        $this->order->id = $order_id;
        
        if ($this->order->read_single()) {
            // If user_id is provided, check if order belongs to user
            if ($user_id && $this->order->user_id != $user_id) {
                return false;
            }
            
            // Get order items
            $items = $this->order->get_items();
            
            return [
                'order' => $this->order,
                'items' => $items
            ];
        }
        
        return false;
    }
    
    // Get order by order number
    public function get_order_by_number($order_number, $user_id = null) {
        $this->order->id = $order_number; // Using ID field for order number search
        
        if ($this->order->read_single()) {
            // If user_id is provided, check if order belongs to user
            if ($user_id && $this->order->user_id != $user_id) {
                return false;
            }
            
            // Get order items
            $items = $this->order->get_items();
            
            return [
                'order' => $this->order,
                'items' => $items
            ];
        }
        
        return false;
    }
    
    // Update order status
    public function update_order_status($order_id, $status) {
        $this->order->id = $order_id;
        
        if ($this->order->read_single() && $this->order->update_status($status)) {
            return [
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui',
                'new_status' => $status
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memperbarui status pesanan'
        ];
    }
    
    // Update payment status
    public function update_payment_status($order_id, $payment_status) {
        $this->order->id = $order_id;
        
        if ($this->order->read_single() && $this->order->update_payment_status($payment_status)) {
            return [
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui',
                'new_status' => $payment_status
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memperbarui status pembayaran'
        ];
    }
    
    // Update tracking information
    public function update_tracking($order_id, $tracking_number) {
        $this->order->id = $order_id;
        
        if ($this->order->read_single() && $this->order->update_tracking($tracking_number)) {
            return [
                'success' => true,
                'message' => 'Informasi pengiriman berhasil diperbarui',
                'tracking_number' => $tracking_number
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memperbarui informasi pengiriman'
        ];
    }
    
    // Update order information
    public function update_order_info($order_id, $order_data) {
        $this->order->id = $order_id;
        
        if (!$this->order->read_single()) {
            return [
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ];
        }
        
        // Update order properties
        if (isset($order_data['shipping_address'])) {
            $this->order->shipping_address = $order_data['shipping_address'];
        }
        
        if (isset($order_data['shipping_city'])) {
            $this->order->shipping_city = $order_data['shipping_city'];
        }
        
        if (isset($order_data['shipping_postal_code'])) {
            $this->order->shipping_postal_code = $order_data['shipping_postal_code'];
        }
        
        if (isset($order_data['notes'])) {
            $this->order->notes = $order_data['notes'];
        }
        
        if (isset($order_data['payment_method'])) {
            $this->order->payment_method = $order_data['payment_method'];
        }
        
        if (isset($order_data['tracking_number'])) {
            $this->order->tracking_number = $order_data['tracking_number'];
        }
        
        // Update order
        if ($this->order->update()) {
            return [
                'success' => true,
                'message' => 'Informasi pesanan berhasil diperbarui',
                'order_id' => $this->order->id
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memperbarui informasi pesanan'
        ];
    }
    
    // Process payment confirmation
    public function process_payment_confirmation($order_id, $payment_data, $payment_proof = null) {
        $this->order->id = $order_id;
        
        if (!$this->order->read_single()) {
            return [
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ];
        }
        
        // Check if payment already confirmed
        if ($this->order->payment_status === 'paid') {
            return [
                'success' => false,
                'message' => 'Pembayaran sudah dikonfirmasi sebelumnya'
            ];
        }
        
        // Upload payment proof if provided
        $payment_proof_path = '';
        if ($payment_proof && $payment_proof['error'] === UPLOAD_ERR_OK) {
            $payment_proof_path = $this->upload_payment_proof($payment_proof, $this->order->order_number);
            
            if (!$payment_proof_path) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengunggah bukti pembayaran'
                ];
            }
        }
        
        // Store payment confirmation in database
        // This would typically be in a separate payment_confirmations table
        // For simplicity, we'll just update the order status
        
        // Update payment status to 'pending_verification'
        if ($this->order->update_payment_status('pending_verification')) {
            // In a real application, you would send notification to admin
            // and store payment details in a separate table
            
            return [
                'success' => true,
                'message' => 'Konfirmasi pembayaran berhasil dikirim. Pembayaran akan diverifikasi oleh admin.',
                'payment_proof' => $payment_proof_path
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal memproses konfirmasi pembayaran'
        ];
    }
    
    // Admin: Get all orders
    public function get_all_orders($status = null, $limit = 20, $page = 1) {
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Get orders
        $orders = $this->order->read(null, $status, $limit, $offset);
        
        // Get total orders count for pagination
        $total_orders = $this->order->count_orders(null, $status);
        
        // Calculate total pages
        $total_pages = ceil($total_orders / $limit);
        
        return [
            'orders' => $orders,
            'total_orders' => $total_orders,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
    }
    
    // Admin: Get order statistics
    public function get_order_statistics($period = 'month') {
        return $this->order->get_statistics($period);
    }
    
    // Helper: Upload payment proof
    private function upload_payment_proof($file, $order_number) {
        // Define upload directory
        $upload_dir = __DIR__ . '/../uploads/payments/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate filename
        $filename = 'payment_' . $order_number . '_' . date('YmdHis') . '.jpg';
        $target_file = $upload_dir . $filename;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return 'uploads/payments/' . $filename; // Return relative path
        }
        
        return false;
    }
    
    // Calculate shipping cost
    public function calculate_shipping_cost($city, $weight) {
        // In a real application, this would call a shipping API
        // For simplicity, we'll use a basic calculation
        $base_cost = 10000; // Base shipping cost in IDR
        $weight_cost = $weight * 1000; // 1000 IDR per kg
        
        // Different rates for different cities
        $city_multiplier = 1.0;
        switch (strtolower($city)) {
            case 'jakarta':
                $city_multiplier = 1.0;
                break;
            case 'bandung':
            case 'surabaya':
            case 'semarang':
                $city_multiplier = 1.2;
                break;
            case 'medan':
            case 'makassar':
                $city_multiplier = 1.5;
                break;
            default:
                $city_multiplier = 2.0; // Other cities
        }
        
        $total_cost = ($base_cost + $weight_cost) * $city_multiplier;
        
        return round($total_cost); // Round to nearest integer
    }
}