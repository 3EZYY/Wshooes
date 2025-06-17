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

// Get token from URL
$token = $_GET['token'] ?? '';

// Validate token
if (empty($token)) {
    $_SESSION['error_messages'] = ['Token tidak valid atau telah kedaluwarsa'];
    header('Location: /auth/login.php');
    exit;
}

// Handle reset password form submission
$auth->reset_password($token);

// Get error messages if any
$error_messages = $_SESSION['error_messages'] ?? [];

// Get success message if any
$success_message = $_SESSION['success_message'] ?? '';

// Clear session variables
unset($_SESSION['error_messages']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Wshooes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .reset-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .reset-header img {
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
        .reset-footer {
            text-align: center;
            margin-top: 20px;
        }
        .reset-footer a {
            color: #3f51b5;
            text-decoration: none;
        }
        .reset-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="reset-container">
            <div class="reset-header">
                <img src="/assets/images/logo.png" alt="Wshooes Logo">
                <h2>Reset Password</h2>
                <p>Masukkan password baru Anda</p>
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
                    <p class="mt-2"><a href="/auth/login.php">Klik di sini untuk login</a></p>
                </div>
            <?php else: ?>
                <form action="/auth/reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-reset w-100">Reset Password</button>
                </form>
            <?php endif; ?>
            
            <div class="reset-footer">
                <p>Ingat password? <a href="/auth/login.php">Login sekarang</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>