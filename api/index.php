<?php
/**
 * API Router for CRUD Operations
 * Handles all API requests for the e-commerce system
 */

// Enable CORS for frontend requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session
session_start();

// Include necessary files
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Get the request URI and method
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove base path and get clean endpoint
$base_path = '/Wshooes/api/';
$endpoint = str_replace($base_path, '', $request_uri);
$endpoint = explode('?', $endpoint)[0]; // Remove query parameters

// Route the request
try {
    switch ($endpoint) {
        // Authentication endpoints
        case 'auth/login':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            $auth->apiLogin();
            break;
            
        case 'auth/register':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            $auth->apiRegister();
            break;
            
        case 'auth/logout':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $auth = new AuthController();
            $auth->apiLogout();
            break;
            
        // Product endpoints
        case 'products':
            require_once __DIR__ . '/../controllers/ProductController.php';
            $product = new ProductController();
            switch ($request_method) {
                case 'GET':
                    $product->apiGetProducts();
                    break;
                case 'POST':
                    $product->apiCreateProduct();
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        case (preg_match('/^products\/(\d+)$/', $endpoint, $matches) ? true : false):
            require_once __DIR__ . '/../controllers/ProductController.php';
            $product = new ProductController();
            $product_id = $matches[1];
            
            switch ($request_method) {
                case 'GET':
                    $product->apiGetProduct($product_id);
                    break;
                case 'PUT':
                    $product->apiUpdateProduct($product_id);
                    break;
                case 'DELETE':
                    $product->apiDeleteProduct($product_id);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        // Cart endpoints
        case 'cart':
            require_once __DIR__ . '/../controllers/CartController.php';
            $cart = new CartController();
            switch ($request_method) {
                case 'GET':
                    $cart->apiGetCart();
                    break;
                case 'POST':
                    $cart->apiAddToCart();
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        case (preg_match('/^cart\/(\d+)$/', $endpoint, $matches) ? true : false):
            require_once __DIR__ . '/../controllers/CartController.php';
            $cart = new CartController();
            $cart_id = $matches[1];
            
            switch ($request_method) {
                case 'PUT':
                    $cart->apiUpdateCartItem($cart_id);
                    break;
                case 'DELETE':
                    $cart->apiRemoveFromCart($cart_id);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        // Order endpoints
        case 'orders':
            require_once __DIR__ . '/../controllers/OrderController.php';
            $order = new OrderController();
            switch ($request_method) {
                case 'GET':
                    $order->apiGetOrders();
                    break;
                case 'POST':
                    $order->apiCreateOrder();
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}
?>
