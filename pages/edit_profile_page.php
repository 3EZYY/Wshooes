<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: /Wshooes/auth/login.php');
    exit;
}

// Get user data
$user = new User();
$user->id = $_SESSION['user_id'];
$user->read_single();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/profile_pictures/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid('profile_') . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            $error_message = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        } else {
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Update user profile picture in database
                $profile_picture_url = '/Wshooes/assets/uploads/profile_pictures/' . $new_filename;
                $user->profile_picture = $profile_picture_url;
                if (!$user->update_profile_picture()) {
                    $error_message = 'Failed to update profile picture in database.';
                }
            } else {
                $error_message = 'Failed to move uploaded file. Error: ' . error_get_last()['message'];
            }
        }
    }
    
    // Update other user information
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $street = $_POST['street'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $country = $_POST['country'] ?? '';
    
    // Update user data
    $user->full_name = $full_name;
    $user->email = $email;
    $user->phone_number = $phone;
    
    if ($user->update()) {
        $success_message = 'Profile updated successfully!';
        $_SESSION['full_name'] = $full_name;
    } else {
        $error_message = 'Failed to update profile.';
    }
}

// Get updated user data
$user->read_single();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Wshooes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1d4ed8;
            --background-color: #f1f5f9;
            --text-color: #1e293b;
            --border-radius: 12px;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --hover-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Custom Header */
        .custom-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
        }

        .custom-header .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.4rem;
            text-decoration: none;
            color: white;
        }

        .custom-header .logo i {
            margin-right: 0.75rem;
            font-size: 1.6rem;
        }

        .custom-header .nav-icons {
            display: flex;
            gap: 1.5rem;
        }

        .custom-header .nav-icons a {
            color: white;
            font-size: 1.2rem;
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .custom-header .nav-icons a:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        /* Back Button */
        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            margin: 1.5rem 0;
            transition: all 0.3s ease;
        }

        .back-link i {
            margin-right: 0.5rem;
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-4px);
        }

        /* Profile Container */
        .profile-container {
            max-width: 900px;
            margin: 0 auto 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .profile-container:hover {
            box-shadow: var(--hover-shadow);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .profile-header p {
            margin-top: 0.75rem;
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Profile Picture */
        .profile-picture-container {
            text-align: center;
            margin: -4rem auto 2rem;
            position: relative;
            width: 140px;
            height: 140px;
        }

        .profile-picture {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: var(--card-shadow);
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-color);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.02);
            box-shadow: var(--hover-shadow);
        }

        .change-picture-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
        }

        .change-picture-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }

        /* Form Styling */
        .profile-form {
            padding: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background-color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control:disabled {
            background-color: #f8fafc;
            cursor: not-allowed;
        }

        /* Button Styling */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: var(--card-shadow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }

        .btn-secondary {
            background-color: #e2e8f0;
            border: none;
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
            color: var(--text-color);
            transform: translateY(-2px);
        }

        /* Alert Styling */
        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: none;
            box-shadow: var(--card-shadow);
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
            }

            .profile-header {
                padding: 1.5rem;
            }

            .profile-picture-container {
                margin: -3rem auto 1.5rem;
                width: 120px;
                height: 120px;
            }

            .profile-picture {
                width: 120px;
                height: 120px;
            }

            .change-picture-btn {
                width: 36px;
                height: 36px;
            }

            .profile-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Header -->
    <div class="custom-header">
        <a href="/Wshooes/index.php" class="logo">
            <i class="fas fa-shoe-prints"></i>
            <span>Wshooes</span>
        </a>
        <div class="nav-icons">
            <a href="/Wshooes/index.php" title="Home"><i class="fas fa-home"></i></a>
            <a href="/Wshooes/pages/user_profile.php" title="Profile"><i class="fas fa-user"></i></a>
            <a href="/Wshooes/auth/logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>

    <div class="container">
        <a href="/Wshooes/pages/user_profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>

        <div class="profile-container">
            <div class="profile-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                <p>Update your personal information</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success m-3">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger m-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="profile-picture-container">
                <?php if ($user->profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($user->profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <div class="profile-picture">
                        <?php echo strtoupper(substr($user->full_name ?? 'U', 0, 2)); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data" id="profile-picture-form">
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="d-none">
                    <label for="profile_picture" class="change-picture-btn" title="Change Profile Picture">
                        <i class="fas fa-camera"></i>
                    </label>
                </form>
            </div>

            <div class="profile-form">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-title">Personal Information</div>
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user->full_name ?? ''); ?>" placeholder="Enter your full name">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" placeholder="Enter your email">
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone_number ?? ''); ?>" placeholder="Enter your phone number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-title">Shipping Address</div>
                            <div class="form-group">
                                <label for="street" class="form-label">Street Address</label>
                                <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars($user->street ?? ''); ?>" placeholder="Enter your street address">
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user->city ?? ''); ?>" placeholder="Enter your city">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="zip" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control" id="zip" name="zip" value="<?php echo htmlspecialchars($user->zip ?? ''); ?>" placeholder="ZIP code">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user->country ?? 'Indonesia'); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='/Wshooes/pages/user_profile.php'">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto submit form when profile picture is selected
        document.getElementById('profile_picture').addEventListener('change', function() {
            document.getElementById('profile-picture-form').submit();
        });
    </script>
</body>
</html>