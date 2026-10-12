<?php $title = '- Request Account'; ?>
<!DOCTYPE html>
<html lang="en" class="<?= isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($_ENV['APP_NAME'] ?? 'UPHS Computer Laboratory Management System') ?> - Request Account</title>
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
    <style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen py-10 px-4 flex items-center justify-center">
    <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-8">
        <div class="text-center mb-8">
            <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-16 mx-auto mb-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Request for System Account</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Submit your details to request an account. Administrators and Faculty will review your request.</p>
        </div>

        <?php
        $success = session_flash('success');
        $error = session_flash('error');
        ?>
        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl text-sm text-green-700 dark:text-green-300">
                <?= escape($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-sm text-red-700 dark:text-red-300">
                <?= escape($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/request-account" class="space-y-4">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="e.g. Maria Santos">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="msantos@uphs.edu.ph">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">ID Number</label>
                    <input type="text" name="id_number" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="e.g. FAC-2026-0091">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Requested Role</label>
                    <select name="requested_role_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="4">Faculty Member</option>
                        <option value="2">Laboratory Administrator</option>
                        <option value="3">Laboratory Custodian</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Department / Segment</label>
                <input type="text" name="department_segment" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="e.g. College of Computer Studies / Network Lab">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Reason / Purpose</label>
                <textarea name="reason" rows="3" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="State your role and reason for accessing the Computer Laboratory Management System..."></textarea>
            </div>

            <div class="pt-2 flex items-center justify-between">
                <a href="/login" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Login</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
