<?php $title = '- Scan Attendance'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Scan - <?= escape($laboratory['name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-md">
                <i data-lucide="qr-code" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laboratory Attendance</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1"><?= escape($laboratory['name']) ?> (Room <?= escape($laboratory['room_number']) ?>)</p>
            <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Shuffle Mode Enabled
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <!-- Shuffle Mode Selector -->
            <div class="mb-4 p-2 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-around text-xs font-medium">
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="shuffle_mode" value="auto" checked onchange="setMode('auto')" class="text-primary-600">
                    <span class="text-gray-700 dark:text-gray-300">🔄 Auto-Shuffle</span>
                </label>
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="shuffle_mode" value="in" onchange="setMode('in')" class="text-primary-600">
                    <span class="text-green-600 font-semibold">⬇ Time In</span>
                </label>
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="shuffle_mode" value="out" onchange="setMode('out')" class="text-primary-600">
                    <span class="text-amber-600 font-semibold">⬆ Time Out</span>
                </label>
            </div>

            <?php if ($student): ?>
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/40 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-lg font-bold text-primary-600"><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span>
                    </div>
                    <p class="font-semibold text-gray-900 dark:text-white"><?= escape($student['full_name']) ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= escape($student['student_number']) ?></p>
                    <?php if (!empty($student['or_number'])): ?>
                        <p class="text-[11px] text-primary-600 font-medium mt-1">1st Sem OR: <?= escape($student['or_number']) ?></p>
                    <?php endif; ?>
                </div>
                <form method="POST" action="/attendance/scan-redirect/<?= $laboratory['id'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action_mode" id="actionMode" value="auto">
                    <input type="hidden" name="student_number" value="<?= escape($student['student_number']) ?>">
                    <button type="submit" id="submitBtn" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">Record Attendance</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/attendance/scan-redirect/<?= $laboratory['id'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action_mode" id="actionMode" value="auto">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Student Number</label>
                    <input type="text" name="student_number" placeholder="e.g. 2023-00101" required autofocus class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm mb-3 focus:ring-2 focus:ring-primary-500">
                    <button type="submit" id="submitBtn" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">Submit Attendance</button>
                </form>
            <?php endif; ?>
        </div>
        <p class="text-center text-xs text-gray-400 mt-4">&larr; <a href="/student/dashboard" class="hover:underline">Return to Student Dashboard</a></p>
    </div>
    <script>
        function setMode(mode) {
            document.getElementById('actionMode').value = mode;
            const btn = document.getElementById('submitBtn');
            if (mode === 'in') {
                btn.textContent = 'Record Time In';
                btn.className = 'w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm';
            } else if (mode === 'out') {
                btn.textContent = 'Record Time Out';
                btn.className = 'w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm';
            } else {
                btn.textContent = 'Record Attendance (Auto)';
                btn.className = 'w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm';
            }
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>
</html>
