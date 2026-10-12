<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>History - <?= escape($student['full_name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: { 50: '#eff6ff', 500: '#2563eb' } },
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
        <span class="font-semibold text-gray-900 text-sm">Attendance History</span>
        <span class="ml-auto text-xs text-gray-400"><?= count($attendance) ?> records</span>
    </header>

    <main class="flex-1 overflow-y-auto pb-20 p-4">
        <?php if (empty($attendance)): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar" class="w-8 h-8 text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">No Attendance Yet</p>
            <p class="text-xs text-gray-500 mt-1">Scan a lab QR code to record your attendance.</p>
            <a href="/student/scan" class="inline-block mt-4 px-5 py-2.5 bg-primary-500 text-white rounded-xl text-sm font-medium">Scan Now</a>
        </div>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($attendance as $a): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="flask-conical" class="w-5 h-5 text-primary-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= escape($a['laboratory_name']) ?></p>
                            <p class="text-xs text-gray-400"><?= date('D, M d, Y', strtotime($a['attendance_date'])) ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-green-600"><?= date('h:i A', strtotime($a['time_in'])) ?></span>
                        <p class="text-xs text-gray-400">Time in</p>
                        <?php if ($a['time_out']): ?><span class="text-sm font-semibold text-orange-600"><?= date('h:i A', strtotime($a['time_out'])) ?></span><p class="text-xs text-gray-400">Time out</p><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pb-safe z-40">
        <div class="flex items-center justify-around h-16">
            <a href="/student/dashboard" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Home</span>
            </a>
            <a href="/student/scan" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="camera" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Scan</span>
            </a>
            <a href="/student/history" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-primary-500 border-t-3 border-primary-500">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5 font-medium">History</span>
            </a>
            <a href="/student/profile" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Profile</span>
            </a>
        </div>
    </nav>
    <script>lucide.createIcons();</script>
</body>
</html>
