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

// Handle registration form submission
$auth->register();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Wshooes/assets/css/style.css">
    <style>
        .signup-container {
            max-width: 550px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .signup-header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .form-control {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-signup {
            padding: 12px;
            font-weight: 600;
            border-radius: 5px;
            background-color: #3f51b5;
            border: none;
        }
        .btn-signup:hover {
            background-color: #303f9f;
        }
        .signup-footer {
            text-align: center;
            margin-top: 20px;
        }
        .signup-footer a {
            color: #3f51b5;
            text-decoration: none;
        }
        .signup-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="signup-container">
            <div class="signup-header">
                <img src="/Wshooes/assets/images/logo.png" alt="Wshooes Logo">
                <h2>Daftar Akun Baru</h2>
                <p>Buat akun Anda untuk mulai berbelanja</p>
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
            
            <form action="/Wshooes/auth/signup.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($form_data['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">Saya setuju dengan <a href="/Wshooes/terms.php">syarat dan ketentuan</a></label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-signup w-100">Daftar</button>
            </form>
            
            <div class="signup-footer">
                <p>Sudah punya akun? <a href="/Wshooes/auth/login.php">Login sekarang</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>