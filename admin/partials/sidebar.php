<!-- Admin Sidebar -->
<div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white overflow-y-auto">
    <div class="flex items-center justify-center h-16 bg-gray-800">
        <div class="flex items-center space-x-2">
            <i class="fas fa-shoe-prints text-2xl text-blue-400"></i>
            <span class="text-xl font-bold">Wshooes Admin</span>
        </div>
    </div>
    
    <nav class="mt-8">
        <div class="px-4 space-y-2">
            <a href="/Wshooes/admin/dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-chart-line mr-3"></i>
                Dashboard
            </a>
            
            <a href="/Wshooes/admin/products.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-shoe-prints mr-3"></i>
                Products
            </a>
            
            <a href="/Wshooes/admin/orders.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-shopping-cart mr-3"></i>
                Orders
            </a>
            
            <a href="/Wshooes/admin/users.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-users mr-3"></i>
                Users
            </a>
              <div class="pt-4 mt-4 border-t border-gray-700">                <a href="/Wshooes/admin/register.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-user-plus mr-3"></i>
                    Register Admin
                </a>
                  <a href="/Wshooes/admin/categories.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-tags mr-3"></i>
                    Categories
                </a>
                
                <a href="/Wshooes/admin/reviews.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-star mr-3"></i>
                    Reviews
                </a>
                
                <a href="/Wshooes/admin/coupons.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'coupons.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-ticket-alt mr-3"></i>
                    Coupons
                </a>
                
                <a href="/Wshooes/admin/settings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-cog mr-3"></i>
                    Settings
                </a>
            </div>
        </div>
        
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
            <a href="/Wshooes/pages/landing_page.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700" target="_blank">
                <i class="fas fa-external-link-alt mr-3"></i>
                View Site
            </a>            <a href="/Wshooes/admin/logout.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-700 text-red-400">
                <i class="fas fa-sign-out-alt mr-3"></i>
                Logout
            </a>
        </div>
    </nav>
</div>
