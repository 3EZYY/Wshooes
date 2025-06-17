<?php
session_start();

// Include necessary files
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Create auth controller instance
$auth = new AuthController();

// Logout user
$auth->logout();

// Redirect to login page
header('Location: /auth/login.php');
exit;