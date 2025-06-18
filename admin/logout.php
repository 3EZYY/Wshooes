<?php
session_start();
require_once '../includes/functions.php';

// Destroy session
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destroy session
session_destroy();

// Set flash message for login page
session_start();
set_flash_message('success', 'You have been successfully logged out.');

// Redirect to login page
redirect('/Wshooes/admin/login.php');
?>
