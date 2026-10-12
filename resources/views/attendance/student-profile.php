<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Profile - <?= escape($student['full_name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: { 500: '#2563eb', 600: '#1d4ed8' } },
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 16px); }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 px-4 py-2 flex items-center sticky top-0 z-30">
        <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-8 mr-2">
        <span class="font-semibold text-gray-900 text-sm">Student Profile</span>
    </header>

    <main class="flex-1 overflow-y-auto pb-20 p-4">
        <div class="text-center mb-6 pt-4">
            <div class="w-20 h-20 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-200">
                <span class="text-3xl font-bold text-white"><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span>
            </div>
            <h2 class="text-xl font-bold text-gray-900"><?= escape($student['full_name']) ?></h2>
            <p class="text-sm font-mono text-gray-500"><?= escape($student['student_number']) ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                <span class="text-sm text-gray-500">Course</span>
                <span class="text-sm font-medium text-gray-900"><?= escape($student['course'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                <span class="text-sm text-gray-500">Year & Section</span>
                <span class="text-sm font-medium text-gray-900"><?= escape(($student['year_level'] ?? '') . ' ' . ($student['section'] ?? '')) ?></span>
            </div>
            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                <span class="text-sm text-gray-500">Semester</span>
                <span class="text-sm font-medium text-gray-900"><?= escape($student['semester'] ?? 'First Semester') ?></span>
            </div>
            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                <span class="text-sm text-gray-500">1st Sem OR Number</span>
                <span class="text-sm font-mono font-bold text-primary-600"><?= escape($student['or_number'] ?? 'Not Recorded') ?></span>
            </div>
            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                <span class="text-sm text-gray-500">Email</span>
                <span class="text-sm font-medium text-gray-900"><?= escape($student['email'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Total Attendance</span>
                <span class="text-sm font-bold text-primary-600"><?= count($attendance) ?> sessions</span>
            </div>
        </div>

        <div class="mt-6 space-y-3">
            <a href="/student/logout" class="w-full flex items-center justify-center space-x-2 py-3 bg-red-50 text-red-600 rounded-xl text-sm font-medium hover:bg-red-100 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Logout</span>
            </a>
        </div>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pb-safe z-40">
        <div class="flex items-center justify-around h-16">
            <a href="/student/dashboard" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Home</span>
            </a>
            <a href="/student/scan" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="camera" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Scan</span>
            </a>
            <a href="/student/history" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">History</span>
            </a>
            <a href="/student/profile" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-primary-500 border-t-2 border-primary-500">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5 font-medium">Profile</span>
            </a>
        </div>
    </nav>
    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
