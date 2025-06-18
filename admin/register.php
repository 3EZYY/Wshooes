<?php
session_start();
require_once '../config/connection.php';
require_once '../includes/functions.php';

// If already logged in and is admin, redirect to dashboard
if (is_logged_in() && is_admin()) {
    redirect('/Wshooes/admin/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $admin_code = $_POST['admin_code'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif ($admin_code !== 'WSHOOES2025') { // Simple admin code for security
        $error = 'Invalid admin code';
    } else {
        try {
            $database = Database::getInstance();
            $conn = $database->getConnection();
            
            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = 'Email already exists';
            } else {
                // Check if username already exists
                $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $check_stmt->bind_param("s", $username);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $error = 'Username already exists';
                } else {
                    // Create admin user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $role = 'admin';
                    
                    $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("sssss", $username, $email, $full_name, $hashed_password, $role);
                    
                    if ($stmt->execute()) {
                        $success = 'Admin account created successfully! You can now login.';
                        
                        // Clear form data
                        $_POST = [];
                    } else {
                        $error = 'Failed to create account. Please try again.';
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="register-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-2 text-white">
                <i class="fas fa-shoe-prints text-4xl"></i>
                <h1 class="text-3xl font-bold">Wshooes</h1>
            </div>
            <p class="text-white/80 mt-2">Admin Registration</p>
        </div>
        
        <!-- Registration Form -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Create Admin Account</h2>
                <p class="text-gray-600 mt-2">Register as an administrator</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i> Username
                        </label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                               placeholder="admin"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-1"></i> Full Name
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                               placeholder="Admin Name"
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="admin@wshooes.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="admin_code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-1"></i> Admin Code
                    </label>
                    <input type="password" id="admin_code" name="admin_code" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="Enter admin registration code">
                    <p class="text-xs text-gray-500 mt-1">Contact system administrator for the admin code</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 pr-12"
                                   placeholder="Min. 6 characters">
                            <button type="button" onclick="togglePassword('password', 'password-toggle')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="password-toggle"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i> Confirm
                        </label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 pr-12"
                                   placeholder="Repeat password">
                            <button type="button" onclick="togglePassword('confirm_password', 'confirm-toggle')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="confirm-toggle"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-300 transition duration-200 font-medium">
                    <i class="fas fa-user-plus mr-2"></i> Create Admin Account
                </button>
            </form>
              <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an admin account? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Sign in</a>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    <a href="dashboard.php" class="text-purple-600 hover:text-purple-800 font-medium">
                        <i class="fas fa-tachometer-alt mr-1"></i>Go to Dashboard
                    </a>
                </p>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="/Wshooes/index.php" class="flex items-center justify-center text-sm text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Website
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8 text-white/60 text-sm">
            <p>&copy; 2025 Wshooes. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.getElementById(toggleId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
        
        // Auto-focus on first input
        document.getElementById('username').focus();
    </script>
</body>
</html>
