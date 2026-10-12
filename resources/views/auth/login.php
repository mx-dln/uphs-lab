<?php $title = '- Login'; ?>
<!DOCTYPE html>
<html lang="en" class="<?= isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($_ENV['APP_NAME'] ?? 'UPHS Computer Laboratory Management System') ?> - Login</title>
    <!-- Offline CSS & Script dependencies -->
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#2563eb', 600: '#1d4ed8', 700: '#1e40af', 800: '#1e3a8a', 900: '#172554' },
                    },
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center bg-gray-900">
        <img src="/assets/images/login-bg.jpg" alt="Login" class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/60 to-transparent"></div>
        <div class="relative z-10 text-center px-12">
            <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-24 mx-auto mb-6 drop-shadow-lg">
            <h1 class="text-3xl font-extrabold text-white mb-2 tracking-tight">University of Perpetual Help System</h1>
            <p class="text-blue-300 text-lg font-medium">Cauayan City Campus</p>
            <p class="text-gray-300 text-sm mt-3">Computer Laboratory Management System</p>
            <div class="mt-6 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                <span class="w-2 h-2 mr-2 bg-emerald-400 rounded-full animate-pulse"></span>
                Offline Standalone System
            </div>
        </div>
    </div>
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8">
        <div class="w-full max-w-md animate-fade-in">
            <div class="text-center lg:text-left mb-8">
                <div class="lg:hidden flex justify-center mb-4">
                    <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-14">
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Sign in with Admin or Faculty credentials</p>
            </div>
            
            <?php
            $success = session_flash('success');
            $error = session_flash('error');
            ?>
            <?php if ($success): ?><div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400"><?= escape($success) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400"><?= escape($error) ?></div><?php endif; ?>
            
            <form method="POST" action="/login" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400"></i>
                        <input type="email" id="emailInput" name="email" required class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="admin@labmanagement.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400"></i>
                        <input type="password" id="passInput" name="password" required class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    Sign In
                </button>
            </form>

            <!-- Quick Demo Accounts -->
            <div class="mt-6 p-3.5 bg-gray-100 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700 text-xs">
                <p class="font-medium text-gray-700 dark:text-gray-300 mb-2">Offline Demo Credentials (Click to Fill):</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="fillCreds('admin@labmanagement.com', 'admin123')" class="px-2.5 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50">Admin</button>
                    <button type="button" onclick="fillCreds('faculty@labmanagement.com', 'admin123')" class="px-2.5 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50">Faculty</button>
                    <button type="button" onclick="fillCreds('hr@labmanagement.com', 'admin123')" class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold rounded-lg border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-100">HR Officer</button>
                    <button type="button" onclick="fillCreds('custodian@labmanagement.com', 'admin123')" class="px-2.5 py-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50">Custodian</button>
                </div>
            </div>

            <!-- Account Request & Student Portal links -->
            <div class="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <a href="/request-account" class="text-primary-600 dark:text-primary-400 hover:underline font-medium flex items-center gap-1">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>Request for Account</span>
                </a>
                <a href="/student/login" class="text-gray-600 dark:text-gray-300 hover:underline flex items-center gap-1">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                    <span>Student Portal / Scan</span>
                </a>
            </div>
        </div>
    </div>
    <script>
        function fillCreds(email, pass) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passInput').value = pass;
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
