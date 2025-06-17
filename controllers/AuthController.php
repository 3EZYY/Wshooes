<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/validation.php';

class AuthController {
    private $user;
    private $conn;
    
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        try {
            // Initialize database connection using singleton pattern
            $db = Database::getInstance();
            $this->conn = $db->getConnection();
            
            $this->user = new User();
        } catch (Exception $e) {
            error_log("Failed to initialize AuthController: " . $e->getMessage());
            throw new Exception("Failed to initialize system. Please try again later.");
        }
    }
    
    // Handle user registration
    public function register() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get all form data
            $form_data = [
                'username' => $_POST['username'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'terms' => $_POST['terms'] ?? ''
            ];
            
            // Store form data in session for repopulating form
            $_SESSION['form_data'] = $form_data;
            
            // Validate form data using validation function
            $errors = validate_registration($form_data);
            
            // Check if email already exists
            if (!empty($form_data['email']) && $this->user->get_by_email($form_data['email'])) {
                $errors[] = "Email sudah terdaftar";
            }
            
            // Check if username already exists
            if (!empty($form_data['username']) && $this->user->get_by_username($form_data['username'])) {
                $errors[] = "Username sudah digunakan";
            }
            
            // If no errors, create user
            if (empty($errors)) {
                // Set user properties
                $this->user->username = $form_data['username'];
                $this->user->full_name = $form_data['full_name'];
                $this->user->email = $form_data['email'];
                $this->user->password = $form_data['password']; // Model will hash the password
                $this->user->phone_number = $form_data['phone'];
                $this->user->address = $form_data['address']; // Address will be handled by model
                $this->user->role = 'customer'; // Default role
                
                // Create user
                if ($this->user->create()) {
                    // Clear form data
                    unset($_SESSION['form_data']);
                    
                    // Set success message
                    $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login.";
                    
                    // Redirect to login page
                    header('Location: /Wshooes/auth/login.php');
                    exit;
                } else {
                    $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
                }
            }
            
            // If there are errors, store them in session
            if (!empty($errors)) {
                $_SESSION['error_messages'] = $errors;
            }
        }
    }
    
    // Handle user login
    public function login() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get form data
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $remember = isset($_POST['remember']);
                
                error_log("Login attempt for email: " . $email);
                
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
                    error_log("Attempting to get user by email");
                    // Get user by email
                    if ($this->user->get_by_email($email)) {
                        error_log("User found, verifying password");
                        if ($this->user->verify_password($password)) {
                            error_log("Password verified successfully");
                            // Set user session
                            $_SESSION['user_id'] = $this->user->id;
                            $_SESSION['username'] = $this->user->username;
                            $_SESSION['user_email'] = $this->user->email;
                            $_SESSION['user_role'] = $this->user->role;
                            $_SESSION['user_name'] = $this->user->full_name;
                            
                            // Set remember me cookie if checked
                            if ($remember) {
                                $token = bin2hex(random_bytes(32));
                                $expires = time() + (30 * 24 * 60 * 60); // 30 days
                                
                                // Store token in database
                                $this->user->set_remember_token($token, $expires);
                                
                                // Set cookie
                                setcookie('remember_token', $token, $expires, '/');
                            }
                            
                            error_log("Login successful, redirecting user");
                            // Redirect based on role
                            if ($this->user->role === 'admin') {
                                header('Location: /Wshooes/admin/dashboard.php');
                            } else {
                                header('Location: /Wshooes/index.php');
                            }
                            exit;
                        } else {
                            error_log("Password verification failed");
                            $errors[] = "Password salah";
                        }
                    } else {
                        error_log("User not found with email: " . $email);
                        $errors[] = "Email tidak ditemukan";
                    }
                }
            } catch (Exception $e) {
                error_log("Login error detail: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $errors[] = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
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
        header('Location: /Wshooes/auth/login.php');
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
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    // Require user to be logged in
    public function require_login() {
        if (!$this->is_logged_in()) {
            $_SESSION['error_messages'] = ['Anda harus login terlebih dahulu'];
            header('Location: /Wshooes/auth/login.php');
            exit;
        }
    }
    
    // Require user to be admin
    public function require_admin() {
        $this->require_login();
        
        if (!$this->is_admin()) {
            $_SESSION['error_messages'] = ['Anda tidak memiliki akses ke halaman ini'];
            header('Location: /Wshooes/index.php');
            exit;
        }
    }
}