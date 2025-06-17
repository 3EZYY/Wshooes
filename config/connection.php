<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wshooes_db');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log($e->getMessage());
    die("Sorry, there was a problem connecting to the database. Please try again later.");
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to handle database errors
function handle_db_error($query) {
    global $conn;
    error_log("MySQL Error: " . $conn->error . " in query: " . $query);
    die("Sorry, there was a database error. Please try again later.");
}