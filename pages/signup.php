<?php
session_start();

// Include necessary files
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Create auth controller instance
$auth = new AuthController();

// Check if user is already logged in
if ($auth->is_logged_in()) {
    // Redirect to home page
    header('Location: /Wshooes/pages/landing_page.php');
    exit;
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->register();
}

// Get error messages if any
$error_messages = $_SESSION['error_messages'] ?? [];

// Get form data if any (for repopulating form after error)
$form_data = $_SESSION['form_data'] ?? [];

// Get success message if any
$success_message = $_SESSION['success_message'] ?? '';

// Clear session variables
unset($_SESSION['error_messages']);
unset($_SESSION['form_data']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0d1b2a 0%, #1a2332 100%);
        }
        .form-container {
            background: rgba(15, 20, 25, 0.9);
            border: 1px solid rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(10px);
        }
        .input-field {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #e2e8f0;
        }
        .input-field:focus {
            border-color: #3b82f6;
            background: rgba(30, 41, 59, 0.8);
        }
        .btn-primary {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-opacity-90 backdrop-blur-sm text-white shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <a href="/Wshooes/pages/landing_page.php" class="flex items-center space-x-2">
                        <i class="fas fa-shoe-prints text-2xl text-blue-400"></i>
                        <h1 class="text-2xl font-bold text-white">Wshooes</h1>
                    </a>
                    <a href="/Wshooes/auth/login.php" class="text-blue-400 hover:text-blue-300 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center py-12 px-4">
            <div class="form-container rounded-lg shadow-2xl w-full max-w-2xl p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-user-plus text-4xl text-blue-400 mb-4"></i>
                    <h2 class="text-3xl font-bold text-white mb-2">Buat Akun Baru</h2>
                    <p class="text-gray-300">Bergabunglah dengan Wshooes untuk pengalaman berbelanja terbaik</p>
                </div>
                
                <?php if (!empty($error_messages)): ?>
                    <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-200 p-4 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            <?php foreach ($error_messages as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-500 bg-opacity-20 border border-green-500 text-green-200 p-4 rounded-lg mb-6">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form action="/Wshooes/auth/signup.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username *</label>
                            <input type="text" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   id="username" name="username" 
                                   value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" 
                                   required placeholder="Masukkan username">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                            <input type="email" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   id="email" name="email" 
                                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                                   required placeholder="contoh@email.com">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password *</label>
                            <div class="relative">
                                <input type="password" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       id="password" name="password" required placeholder="Minimal 8 karakter">
                                <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-200" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">Konfirmasi Password *</label>
                            <div class="relative">
                                <input type="password" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       id="confirm_password" name="confirm_password" required placeholder="Ulangi password">
                                <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-200" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="confirm_password-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap *</label>
                            <input type="text" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" 
                                   required placeholder="Nama lengkap Anda">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Nomor Telepon *</label>
                            <input type="tel" class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" 
                                   required placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-300 mb-2">Alamat *</label>
                        <textarea class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  id="address" name="address" rows="3" required 
                                  placeholder="Alamat lengkap Anda"><?php echo htmlspecialchars($form_data['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="flex items-start">
                        <input type="checkbox" class="mt-1 mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                               id="terms" name="terms" required>
                        <label for="terms" class="text-sm text-gray-300">
                            Saya setuju dengan <a href="#" class="text-blue-400 hover:text-blue-300">Syarat dan Ketentuan</a> 
                            serta <a href="#" class="text-blue-400 hover:text-blue-300">Kebijakan Privasi</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-white font-semibold hover:shadow-lg transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-transparent text-gray-400">Atau daftar dengan</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition">
                            <i class="fab fa-google mr-2"></i>Google
                        </button>
                        <button type="button" class="flex items-center justify-center px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700 transition">
                            <i class="fab fa-facebook-f mr-2"></i>Facebook
                        </button>
                    </div>
                    
                    <p class="text-gray-400">
                        Sudah punya akun? 
                        <a href="/Wshooes/auth/login.php" class="text-blue-400 hover:text-blue-300 font-medium">Login di sini</a>
                    </p>
                </div>
            </div>
        </main>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value;
            } else if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }
            this.value = value;
        });
    </script>
</body>
</html>