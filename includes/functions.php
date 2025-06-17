<?php
/**
 * Common Functions for Wshooes E-commerce
 * Contains utility functions used throughout the application
 */

/**
 * Redirect to a specific URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require login - redirect to login page if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('/Wshooes/auth/login.php');
    }
}

/**
 * Require admin - redirect if not admin
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('/Wshooes/pages/landing_page.php?error=access_denied');
    }
}

/**
 * Get current user data
 */
function get_current_user_data() {
    if (!is_logged_in()) {
        return null;
    }
    
    global $conn;
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Flash message system
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

function has_flash_messages() {
    return !empty($_SESSION['flash_messages']);
}

/**
 * Format currency (Indonesian Rupiah)
 */
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Generate unique filename for uploads
 */
function generate_filename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email format
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesian format)
 */
function validate_phone($phone) {
    // Remove spaces, dashes, and parentheses
    $clean_phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    // Check if it contains only numbers and + symbol
    return preg_match('/^[0-9+]+$/', $clean_phone) && strlen($clean_phone) >= 10;
}

/**
 * Validate password strength
 */
function validate_password($password) {
    // At least 8 characters, contains uppercase, lowercase, and number
    return strlen($password) >= 8 && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

/**
 * Generate secure random token
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Hash password securely
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Handle database errors
 */
function handle_db_error($query = '') {
    global $conn;
    $error_message = "Database error";
    
    if ($conn && $conn->error) {
        $error_message .= ": " . $conn->error;
    }
    
    if (!empty($query)) {
        $error_message .= " (Query: " . substr($query, 0, 100) . "...)";
    }
    
    error_log($error_message);
}

/**
 * Log application errors
 */
function log_error($message, $context = []) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    
    if (!empty($context)) {
        $log_message .= " - Context: " . json_encode($context);
    }
    
    error_log($log_message);
}

/**
 * Get user's IP address
 */
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Generate breadcrumb navigation
 */
function generate_breadcrumb($current_page = '') {
    $breadcrumbs = [];
    $breadcrumbs[] = ['name' => 'Home', 'url' => '/Wshooes/'];
    
    if (!empty($current_page)) {
        $breadcrumbs[] = ['name' => $current_page, 'url' => ''];
    }
    
    return $breadcrumbs;
}

/**
 * Upload file handler
 */
function upload_file($file, $upload_dir = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    if ($file['size'] > 5000000) { // 5MB limit
        throw new RuntimeException('Exceeded filesize limit.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    $allowed_mime_types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    $extension = array_search($mime_type, $allowed_mime_types, true);
    
    if ($extension === false || !in_array($extension, $allowed_types)) {
        throw new RuntimeException('Invalid file format.');
    }

    $filename = generate_filename($file['name']);
    $upload_path = __DIR__ . '/../' . $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    return $filename;
}

/**
 * Pagination helper
 */
function paginate($total_records, $records_per_page = 10, $current_page = 1) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'records_per_page' => $records_per_page,
        'offset' => $offset,
        'has_previous' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'previous_page' => $current_page - 1,
        'next_page' => $current_page + 1
    ];
}

/**
 * Generate pagination HTML
 */
function generate_pagination_html($pagination, $base_url) {
    $html = '<nav class="pagination-nav">';
    $html .= '<ul class="pagination">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $html .= '<li><a href="' . $base_url . '?page=' . $pagination['previous_page'] . '">&laquo; Previous</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = ($i == $pagination['current_page']) ? 'class="active"' : '';
        $html .= '<li ' . $active . '><a href="' . $base_url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<li><a href="' . $base_url . '?page=' . $pagination['next_page'] . '">Next &raquo;</a></li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Log activity
 */
function log_activity($user_id, $activity, $details = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $activity, $details);
    $stmt->execute();
}

/**
 * Send email (basic implementation)
 */
function send_email($to, $subject, $message, $from = 'noreply@wshooes.com') {
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Generate random string
 */
function generate_random_string($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/**
 * Clean output buffer
 */
function clean_output() {
    if (ob_get_contents()) {
        ob_clean();
    }
}

/**
 * JSON response helper
 */
function json_response($data, $status_code = 200) {
    clean_output();
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Error response helper
 */
function error_response($message, $status_code = 400) {
    json_response(['error' => $message], $status_code);
}

/**
 * Success response helper
 */
function success_response($data, $message = 'Success') {
    json_response(['success' => true, 'message' => $message, 'data' => $data]);
}

/**
 * Debug helper
 */
function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}
?>
