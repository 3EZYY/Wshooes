<?php
require_once 'config/connection.php';

echo "<h2>🔑 Informasi Admin Wshooes</h2>";

try {
    // Get database connection
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    // Check existing admin users
    $query = "SELECT id, username, email, role, created_at FROM users WHERE role = 'admin'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo "<h3>✅ Admin Users yang Ada:</h3>";
        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        
        while ($admin = $result->fetch_assoc()) {
            echo "<div style='background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; border-radius: 4px;'>";
            echo "<strong>🧑‍💼 " . htmlspecialchars($admin['username']) . "</strong><br>";
            echo "📧 Email: <code>" . htmlspecialchars($admin['email']) . "</code><br>";
            echo "🔐 Role: <span style='background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;'>" . htmlspecialchars($admin['role']) . "</span><br>";
            echo "📅 Created: " . $admin['created_at'] . "<br>";
            echo "</div>";
        }
        echo "</div>";
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>🚀 Cara Login Admin:</h4>";
        echo "<ol>";
        echo "<li>Buka: <code>http://localhost/Wshooes/admin/login.php</code></li>";
        echo "<li>Email: <code>admin@wshooes.com</code></li>";
        echo "<li>Password: <code>password</code></li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "<h3>❌ Tidak ada admin user. Mari buat admin baru:</h3>";
        
        // Create default admin
        $admin_username = 'Admin';
        $admin_email = 'admin@wshooes.com';
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $role = 'admin';
        
        $insert_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $admin_username, $admin_email, $admin_password, $role);
        
        if ($stmt->execute()) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px;'>";
            echo "✅ Admin berhasil dibuat!<br>";
            echo "<strong>Email:</strong> admin@wshooes.com<br>";
            echo "<strong>Password:</strong> password<br>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px;'>";
            echo "❌ Gagal membuat admin: " . $conn->error;
            echo "</div>";
        }
    }
    
    // Show total users
    $total_query = "SELECT COUNT(*) as total FROM users";
    $total_result = $conn->query($total_query);
    $total_users = $total_result->fetch_assoc()['total'];
    
    $customer_query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
    $customer_result = $conn->query($customer_query);
    $total_customers = $customer_result->fetch_assoc()['total'];
    
    echo "<div style='background: #e9ecef; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>📊 Statistik Users:</h4>";
    echo "👥 Total Users: <strong>{$total_users}</strong><br>";
    echo "🛍️ Total Customers: <strong>{$total_customers}</strong><br>";
    echo "🧑‍💼 Total Admins: <strong>" . ($result ? $result->num_rows : 0) . "</strong>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}
?>

<div style="margin: 30px 0;">
    <h3>🔗 Quick Links:</h3>
    <a href="admin/login.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
        🔑 Admin Login
    </a>
    <a href="admin/dashboard.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
        🏠 Dashboard
    </a>
    <a href="pages/collection.php" style="background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;">
        🛍️ Collection
    </a>
</div>

<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <h4>⚠️ Keamanan:</h4>
    <p>Setelah login pertama kali, sebaiknya:</p>
    <ul>
        <li>Ganti password default</li>
        <li>Buat admin user baru dengan kredensial yang kuat</li>
        <li>Hapus atau nonaktifkan admin default jika diperlukan</li>
    </ul>
</div>
