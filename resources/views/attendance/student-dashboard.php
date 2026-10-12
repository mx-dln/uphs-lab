<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Home - <?= escape($student['full_name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#2563eb', 600: '#1d4ed8' } },
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 16px); }
        .toast { animation: slideUp 0.3s ease-out, fadeOut 0.3s 3.7s ease-out forwards; }
        @keyframes slideUp { from { transform: translateY(100px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: translateY(-10px); } }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <?php
    $flashMsg = session_flash('success');
    $flashErr = session_flash('error');
    ?>
    <?php if ($flashMsg || $flashErr): ?>
    <div class="fixed bottom-20 left-4 right-4 z-50 toast">
        <div class="max-w-sm mx-auto <?= $flashMsg ? 'bg-green-600' : 'bg-red-600' ?> text-white px-5 py-3 rounded-2xl shadow-lg text-center">
            <p class="text-sm font-medium"><?= escape($flashMsg ?? $flashErr) ?></p>
        </div>
    </div>
    <?php endif; ?>
    <header class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center space-x-2">
            <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-8">
            <span class="font-semibold text-gray-900 text-sm">My Attendance</span>
        </div>
        <span class="text-xs text-gray-400 font-mono"><?= escape($student['student_number']) ?></span>
    </header>

    <main class="flex-1 overflow-y-auto pb-20 p-4">
        <div class="bg-gradient-to-r from-primary-600 to-indigo-700 rounded-2xl p-5 text-white mb-4 shadow-lg shadow-blue-200">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center font-bold text-lg">
                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                </div>
                <div class="flex-1">
                    <h1 class="text-lg font-bold leading-tight"><?= escape($student['full_name']) ?></h1>
                    <p class="text-blue-100 text-xs font-mono"><?= escape($student['student_number']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold"><?= count($attendance) ?></p>
                    <p class="text-blue-100 text-xs">Visits</p>
                </div>
            </div>
            
            <div class="pt-3 border-t border-white/20 flex flex-wrap items-center justify-between gap-2 text-xs text-blue-100">
                <div>
                    <span><?= escape($student['course'] ?? '') ?></span>
                    <?php if ($student['year_level']): ?><span> &middot; <?= escape($student['year_level']) ?></span><?php endif; ?>
                    <?php if ($student['section']): ?><span> &middot; Sec <?= escape($student['section']) ?></span><?php endif; ?>
                </div>
                <?php if (!empty($student['or_number'])): ?>
                <div class="bg-white/20 px-2.5 py-1 rounded-lg text-[11px] font-semibold tracking-wide">
                    <span>1st Sem OR:</span> <span class="font-mono text-white"><?= escape($student['or_number']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="/student/scan" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <i data-lucide="camera" class="w-6 h-6 text-primary-500 mb-1"></i>
                    <span class="text-xs font-medium text-primary-600">Scan QR Code</span>
                </a>
                <a href="/student/history" class="flex flex-col items-center justify-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                    <i data-lucide="clock" class="w-6 h-6 text-indigo-500 mb-1"></i>
                    <span class="text-xs font-medium text-indigo-600">Attendance History</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Recent Activity</h2>
            <?php if (empty($attendance)): ?>
            <div class="text-center py-8">
                <i data-lucide="calendar" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                <p class="text-sm text-gray-400">No attendance yet.<br>Scan a lab QR code to start.</p>
            </div>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach (array_slice($attendance, 0, 5) as $a): ?>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate"><?= escape($a['laboratory_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= date('M d, Y', strtotime($a['attendance_date'])) ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-green-600">In: <?= date('h:i A', strtotime($a['time_in'])) ?></span>
                        <?php if ($a['time_out']): ?><br><span class="text-xs font-medium text-amber-600">Out: <?= date('h:i A', strtotime($a['time_out'])) ?></span><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pb-safe z-40">
        <div class="flex items-center justify-around h-16">
            <a href="/student/dashboard" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-primary-500 border-t-2 border-primary-500">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5 font-medium">Home</span>
            </a>
            <a href="/student/scan" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="camera" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Scan</span>
            </a>
            <a href="/student/history" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">History</span>
            </a>
            <a href="/student/profile" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Profile</span>
            </a>
        </div>
    </nav>
    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
