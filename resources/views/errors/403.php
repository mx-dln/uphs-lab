<!DOCTYPE html>
<html lang="en" class="<?= isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <div class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i data-lucide="shield-off" class="w-10 h-10 text-yellow-600 dark:text-yellow-400"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">403</h1>
        <p class="text-lg text-gray-500 dark:text-gray-400 mb-6">Access denied. You do not have permission to view this resource.</p>
        <a href="/dashboard" class="inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>
    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
