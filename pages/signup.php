<?php
// Redirect to the correct signup page
header('Location: /Wshooes/auth/signup.php');
exit;
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Wshooes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #6b21a8 0%, #1e3a8a 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="gradient-bg text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center space-x-2">
                    <a href="home.php" class="flex items-center space-x-2">
                        <i class="fas fa-shoe-prints text-2xl"></i>
                        <h1 class="text-2xl font-bold">Wshooes</h1>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center py-12 px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
                    <p class="text-gray-600 mt-2">Join Wshooes today</p>
                </div>

                <form id="signup-form" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="username" name="username" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address (Optional)</label>
                        <textarea id="address" name="address" rows="3"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Account
                    </button>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <a href="#"
                            class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-google mr-2"></i> Google
                        </a>
                        <a href="#"
                            class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-facebook-f mr-2"></i> Facebook
                        </a>
                    </div>
                </div>

                <p class="mt-8 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
                </p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4">
                <div class="text-center">
                    <p>&copy; 2024 Wshooes. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>
</html>