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

// Handle forgot password form submission
$auth->forgot_password();

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
    <title>Lupa Password - Wshooes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .forgot-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .forgot-header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .form-control {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-reset {
            padding: 12px;
            font-weight: 600;
            border-radius: 5px;
            background-color: #3f51b5;
            border: none;
        }
        .btn-reset:hover {
            background-color: #303f9f;
        }
        .forgot-footer {
            text-align: center;
            margin-top: 20px;
        }
        .forgot-footer a {
            color: #3f51b5;
            text-decoration: none;
        }
        .forgot-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="forgot-container">
            <div class="forgot-header">
                <img src="/assets/images/logo.png" alt="Wshooes Logo">
                <h2>Lupa Password</h2>
                <p>Masukkan email Anda untuk mendapatkan link reset password</p>
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
            
            <form action="/auth/forgot-password.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-reset w-100">Kirim Link Reset Password</button>
            </form>
            
            <div class="forgot-footer">
                <p>Ingat password? <a href="/auth/login.php">Login sekarang</a></p>
                <p>Belum punya akun? <a href="/auth/signup.php">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>