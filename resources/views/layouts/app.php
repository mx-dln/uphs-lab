<!DOCTYPE html>
<html lang="en" class="<?= isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($_ENV['APP_NAME'] ?? 'UPHS Computer Laboratory Management System') ?> <?= $title ?? '' ?></title>
    <!-- Offline CSS & Script dependencies (Local, No CDN required) -->
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#2563eb', 600: '#1d4ed8', 700: '#1e40af', 800: '#1e3a8a', 900: '#172554' },
                        secondary: { 50: '#f1f5f9', 100: '#e2e8f0', 200: '#cbd5e1', 300: '#94a3b8', 400: '#64748b', 500: '#1e293b', 600: '#0f172a', 700: '#020617' },
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased text-gray-800 dark:text-gray-100">
    <?php if (isset($_SESSION['user'])): ?>
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>
        <?php require __DIR__ . '/sidebar.php'; ?>
        <div class="lg:pl-64">
            <?php require __DIR__ . '/navbar.php'; ?>
            <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
                <?php require __DIR__ . '/flash.php'; ?>
                <?= $content ?? '' ?>
            </main>
        </div>
    <?php else: ?>
        <main class="min-h-screen">
            <?php require __DIR__ . '/flash.php'; ?>
            <?= $content ?? '' ?>
        </main>
    <?php endif; ?>

    <!-- Offline Vendor Libraries -->
    <script src="/assets/js/chart.min.js"></script>
    <script src="/assets/js/html5-qrcode.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
