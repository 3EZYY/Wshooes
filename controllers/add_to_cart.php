<?php
session_start();
require_once '../config/connection.php';
require_once '../models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'] ?? null;
    $session_id = session_id();
    
    // Get POST data
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    $size = $_POST['size'] ?? '';
    $color = $_POST['color'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    
    // Validate required fields
    if ($product_id <= 0 || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product data']);
        exit();
    }
    
    if (empty($size) || empty($color)) {
        echo json_encode(['success' => false, 'message' => 'Please select size and color']);
        exit();
    }
    
    // Add to cart
    $cart = new Cart();
    if ($cart->addToCart($session_id, $user_id, $product_id, $quantity, $size, $color, $price)) {
        // Get updated cart count
        $cart_count = $cart->getCartCount($session_id, $user_id);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }
    
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>
