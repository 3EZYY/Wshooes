<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin access
require_admin();

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$page_title = "System Settings";
$page_description = "Configure system settings and preferences";

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        $settings = [
            'site_name' => trim($_POST['site_name']),
            'site_description' => trim($_POST['site_description']),
            'contact_email' => trim($_POST['contact_email']),
            'contact_phone' => trim($_POST['contact_phone']),
            'currency' => $_POST['currency'],
            'tax_rate' => (float)$_POST['tax_rate'],
            'shipping_fee' => (float)$_POST['shipping_fee'],
            'free_shipping_threshold' => (float)$_POST['free_shipping_threshold'],
            'low_stock_threshold' => (int)$_POST['low_stock_threshold'],
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
            'registration_enabled' => isset($_POST['registration_enabled']) ? 1 : 0,
            'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0
        ];
        
        $updated = 0;
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->bind_param("ss", $key, $value);
            if ($stmt->execute()) {
                $updated++;
            }
        }
        
        if ($updated > 0) {
            $success_message = "Settings updated successfully!";
        } else {
            $error_message = "Failed to update settings.";
        }
    }
}

// Get current settings
$settings_result = $conn->query("SELECT setting_key, setting_value FROM settings");
$current_settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Default values
$defaults = [
    'site_name' => 'Wshooes',
    'site_description' => 'Premium Footwear E-commerce',
    'contact_email' => 'admin@wshooes.com',
    'contact_phone' => '+62 123 456 7890',
    'currency' => 'IDR',
    'tax_rate' => 10,
    'shipping_fee' => 15000,
    'free_shipping_threshold' => 500000,
    'low_stock_threshold' => 10,
    'maintenance_mode' => 0,
    'registration_enabled' => 1,
    'email_notifications' => 1
];

// Merge with current settings
foreach ($defaults as $key => $value) {
    if (!isset($current_settings[$key])) {
        $current_settings[$key] = $value;
    }
}

include 'partials/header.php';
?>

<div class="flex">
    <?php include 'partials/sidebar.php'; ?>
    
    <main class="flex-1 ml-64 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo $page_title; ?></h1>
            <p class="text-gray-600 mt-2"><?php echo $page_description; ?></p>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Quick Navigation & System Tools -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="dashboard.php" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                    <a href="users.php" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition duration-200">
                        <i class="fas fa-users mr-2"></i>Users
                    </a>
                    <a href="orders.php" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition duration-200">
                        <i class="fas fa-shopping-cart mr-2"></i>Orders
                    </a>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="exportSettings()" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-200">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <button onclick="resetToDefaults()" class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition duration-200">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" class="space-y-8">
            <!-- General Settings -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold mb-6 pb-3 border-b border-gray-200">General Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($current_settings['site_name']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email" value="<?php echo htmlspecialchars($current_settings['contact_email']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                        <input type="text" name="site_description" value="<?php echo htmlspecialchars($current_settings['site_description']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($current_settings['contact_phone']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="IDR" <?php echo $current_settings['currency'] === 'IDR' ? 'selected' : ''; ?>>Indonesian Rupiah (IDR)</option>
                            <option value="USD" <?php echo $current_settings['currency'] === 'USD' ? 'selected' : ''; ?>>US Dollar (USD)</option>
                            <option value="EUR" <?php echo $current_settings['currency'] === 'EUR' ? 'selected' : ''; ?>>Euro (EUR)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- E-commerce Settings -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold mb-6 pb-3 border-b border-gray-200">E-commerce Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" step="0.01" min="0" max="100" 
                               value="<?php echo $current_settings['tax_rate']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Fee (IDR)</label>
                        <input type="number" name="shipping_fee" step="1000" min="0" 
                               value="<?php echo $current_settings['shipping_fee']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Free Shipping Threshold (IDR)</label>
                        <input type="number" name="free_shipping_threshold" step="10000" min="0" 
                               value="<?php echo $current_settings['free_shipping_threshold']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Orders above this amount get free shipping</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Alert Threshold</label>
                        <input type="number" name="low_stock_threshold" min="1" 
                               value="<?php echo $current_settings['low_stock_threshold']; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Alert when product stock is below this number</p>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold mb-6 pb-3 border-b border-gray-200">System Settings</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" 
                               <?php echo $current_settings['maintenance_mode'] ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Maintenance Mode</span>
                            <p class="text-sm text-gray-500">Enable to temporarily disable the website for maintenance</p>
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="registration_enabled" id="registration_enabled" 
                               <?php echo $current_settings['registration_enabled'] ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="registration_enabled" class="ml-3">
                            <span class="text-sm font-medium text-gray-700">User Registration</span>
                            <p class="text-sm text-gray-500">Allow new users to register accounts</p>
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" id="email_notifications" 
                               <?php echo $current_settings['email_notifications'] ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="email_notifications" class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Email Notifications</span>
                            <p class="text-sm text-gray-500">Send email notifications for orders and other events</p>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold mb-6 pb-3 border-b border-gray-200">Quick Actions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/Wshooes/setup_sample_data.php" target="_blank"
                       class="flex items-center justify-center px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition duration-200">
                        <i class="fas fa-database mr-2 text-blue-600"></i>
                        <span class="text-blue-700 font-medium">Generate Sample Data</span>
                    </a>
                    
                    <a href="/Wshooes/check_admin.php" target="_blank"
                       class="flex items-center justify-center px-4 py-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition duration-200">
                        <i class="fas fa-info-circle mr-2 text-green-600"></i>
                        <span class="text-green-700 font-medium">Admin Info</span>
                    </a>
                    
                    <button type="button" onclick="clearCache()"
                            class="flex items-center justify-center px-4 py-3 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition duration-200">
                        <i class="fas fa-broom mr-2 text-orange-600"></i>
                        <span class="text-orange-700 font-medium">Clear Cache</span>
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" name="update_settings" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </main>
</div>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the cache? This action cannot be undone.')) {
        // In a real application, this would make an AJAX call to clear cache
        alert('Cache cleared successfully!');
    }
}

// Auto-save settings every 30 seconds (optional)
let settingsChanged = false;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', () => {
        settingsChanged = true;
    });
});

// Warn before leaving if changes are unsaved
window.addEventListener('beforeunload', (e) => {
    if (settingsChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Mark as saved when form is submitted
document.querySelector('form').addEventListener('submit', () => {
    settingsChanged = false;
});
</script>

<?php include 'partials/footer.php'; ?>
