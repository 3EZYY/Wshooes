<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;
    
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->user = new User();
    }
    
    // Handle user registration
    public function register() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validate form data
            $errors = [];
            
            if (empty($full_name)) {
                $errors[] = "Nama lengkap harus diisi";
            }
            
            if (empty($email)) {
                $errors[] = "Email harus diisi";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid";
            }
            
            if (empty($password)) {
                $errors[] = "Password harus diisi";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password minimal 6 karakter";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Konfirmasi password tidak cocok";
            }
            
            // Check if email already exists
            if (!empty($email) && $this->user->get_by_email($email)) {
                $errors[] = "Email sudah terdaftar";
            }
            
            // If no errors, create user
            if (empty($errors)) {
                // Set user properties
                $this->user->full_name = $full_name;
                $this->user->email = $email;
                // Generate username from email (part before @)
                $this->user->username = strtolower(explode('@', $email)[0]);
                $this->user->password = $password; // Model will hash the password
                $this->user->role = 'customer'; // Default role
                
                // Create user
                if ($this->user->create()) {
                    // Set success message
                    $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login.";
                    
                    // Redirect to login page
                    header('Location: /auth/login.php');
                    exit;
                } else {
                    $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
                }
            }
            
            // If there are errors, store them in session
            if (!empty($errors)) {
                $_SESSION['error_messages'] = $errors;
                $_SESSION['form_data'] = [
                    'full_name' => $full_name,
                    'email' => $email
                ];
            }
        }
    }
    
    // Handle user login
    public function login() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            // Validate form data
            $errors = [];
            
            if (empty($email)) {
                $errors[] = "Email harus diisi";
            }
            
            if (empty($password)) {
                $errors[] = "Password harus diisi";
            }
            
            // If no errors, attempt login
            if (empty($errors)) {
                // Get user by email
                $user = $this->user->get_by_email($email);
                
                if ($user && $this->user->verify_password($password)) {
                    // Start session if not already started
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Set user session
                    $_SESSION['user_id'] = $this->user->id;
                    $_SESSION['user_name'] = $this->user->full_name;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['user_role'] = $this->user->role;
                    
                    // Set remember me cookie if checked
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expires = time() + (30 * 24 * 60 * 60); // 30 days
                        
                        // Store token in database
                        $this->user->set_remember_token($token, $expires);
                        
                        // Set cookie
                        setcookie('remember_token', $token, $expires, '/', '', false, true);
                    }
                    
                    // Redirect based on role
                    if ($this->user->role === 'admin') {
                        header('Location: /admin/dashboard.php');
                    } else {
                        header('Location: /index.php');
                    }
                    exit;
                } else {
                    $errors[] = "Email atau password salah";
                }
            }
            
            // If there are errors, store them in session
            if (!empty($errors)) {
                $_SESSION['error_messages'] = $errors;
                $_SESSION['form_data'] = [
                    'email' => $email
                ];
            }
        }
    }
    
    // Handle user logout
    public function logout() {
        // Clear remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            // Clear token in database if user is logged in
            if (isset($_SESSION['user_id'])) {
                $this->user->id = $_SESSION['user_id'];
                $this->user->clear_remember_token();
            }
            
            // Delete cookie
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header('Location: /auth/login.php');
        exit;
    }
    
    // Check if user is logged in via remember me cookie
    public function check_remember_me() {
        // Check if user is not logged in but has remember me cookie
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            // Get user by remember token
            $user = $this->user->get_user_by_remember_token($token);
            
            if ($user) {
                // Set user session
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->full_name;
                $_SESSION['user_email'] = $this->user->email;
                $_SESSION['user_role'] = $this->user->role;
                
                // Refresh token
                $new_token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 days
                
                // Update token in database
                $this->user->set_remember_token($new_token, $expires);
                
                // Update cookie
                setcookie('remember_token', $new_token, $expires, '/', '', false, true);
                
                return true;
            }
        }
        
        return false;
    }
    
    // Check if user is logged in
    public function is_logged_in() {
        // Check if user is logged in via session
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Check if user is logged in via remember me cookie
        return $this->check_remember_me();
    }
    
    // Check if user has admin role
    public function is_admin() {
        return $this->is_logged_in() && $_SESSION['user_role'] === 'admin';
    }
    
    // Require user to be logged in
    public function require_login() {
        if (!$this->is_logged_in()) {
            // Store current URL for redirect after login if it's a valid URL
            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            }
            
            // Redirect to login page
            header('Location: /auth/login.php');
            exit;
        }
    }
    
    // Require user to be admin
    public function require_admin() {
        $this->require_login();
        
        if (!$this->is_admin()) {
            // Redirect to home page with error
            $_SESSION['error_messages'] = ["Anda tidak memiliki akses ke halaman ini"];
            header('Location: /index.php');
            exit;
        }
    }
}