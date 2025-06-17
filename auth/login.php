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
    header('Location: /index.php');
    exit;
}

// Handle login form submission
$auth->login();

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
    <title>Login - Wshooes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .login-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .form-control {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-login {
            padding: 12px;
            font-weight: 600;
            border-radius: 5px;
            background-color: #3f51b5;
            border: none;
        }
        .btn-login:hover {
            background-color: #303f9f;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .login-footer a {
            color: #3f51b5;
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="/assets/images/logo.png" alt="Wshooes Logo">
                <h2>Login</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>
            
            <?php if (!empty($error_messages)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($error_messages as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="/auth/login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100">Login</button>
            </form>
            
            <div class="login-footer">
                <p>Belum punya akun? <a href="/auth/signup.php">Daftar sekarang</a></p>
                <p><a href="/auth/forgot-password.php">Lupa password?</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
