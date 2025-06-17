<!-- Admin Header -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-8 py-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?php echo $page_title ?? 'Admin Panel'; ?></h1>
            <p class="text-sm text-gray-600"><?php echo $page_description ?? 'Manage your e-commerce store'; ?></p>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="p-2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">3</span>
                </button>
            </div>
            
            <!-- User Menu -->
            <div class="relative">
                <div class="flex items-center space-x-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'Admin'); ?>&background=3b82f6&color=fff" 
                         alt="Profile" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
