<?php
session_start();
require_once '../config/connection.php';
require_once '../models/Cart.php';
require_once '../models/Order.php';
require_once '../models/User.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $session_id = session_id();
    
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $province = $_POST['province'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $shipping_method = $_POST['shipping_method'] ?? 'regular';
    
    // Calculate shipping cost
    $shipping_cost = ($shipping_method === 'express') ? 50000 : 25000;
    
    // Get cart items
    $cart = new Cart();
    $cart_items = $cart->getCartItems($session_id, $user_id);
    $cart_total = $cart->getCartTotal($session_id, $user_id);
    
    if (empty($cart_items)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit();
    }
    
    // Create order
    $order = new Order();
    $order->user_id = $user_id;
    $order->total_amount = $cart_total + $shipping_cost;
    $order->shipping_cost = $shipping_cost;
    $order->tax_amount = 0;
    $order->discount_amount = 0;
    $order->status = 'pending';
    $order->payment_status = 'pending';
    $order->payment_method = 'cod';
    $order->shipping_address = $address;
    $order->shipping_city = $city;
    $order->shipping_postal_code = $postal_code;
    $order->notes = "Pengiriman: " . ucfirst($shipping_method) . " - " . $first_name . " " . $last_name . " - " . $phone;
    
    if ($order->create()) {
        // Add order items
        foreach ($cart_items as $item) {
            $order->add_item(
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['product_name'],
                $item['image_url'],
                $item['size'],
                $item['color']
            );
        }
        
        // Clear cart
        $cart->clearCart($session_id, $user_id);
        
        // Prepare response data for receipt
        $order_items = [];
        foreach ($cart_items as $item) {
            $order_items[] = [
                'name' => $item['product_name'] . ' (' . $item['size'] . ', ' . $item['color'] . ')',
                'qty' => $item['quantity'],
                'total' => $item['price'] * $item['quantity']
            ];
        }
        
        $response_data = [
            'success' => true,
            'message' => 'Order created successfully',
            'order' => [
                'order_number' => $order->order_number,
                'customer_name' => $first_name . ' ' . $last_name,
                'items' => $order_items,
                'subtotal' => $cart_total,
                'shipping' => $shipping_cost,
                'total' => $cart_total + $shipping_cost
            ]
        ];
        
        echo json_encode($response_data);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    }
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>
