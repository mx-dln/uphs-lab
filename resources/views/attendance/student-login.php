<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - UPHS Lab Management</title>
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
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in">
        <div class="text-center mb-6">
            <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-16 mx-auto mb-3 drop-shadow">
            <h1 class="text-2xl font-bold text-gray-900">Student Portal Login</h1>
            <p class="text-gray-500 text-sm mt-1">University of Perpetual Help System - Cauayan City</p>
        </div>

        <?php
        $error = session_flash('error');
        $success = session_flash('success');
        ?>
        <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span><?= escape($error) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span><?= escape($success) ?></span>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
            <form method="POST" action="/student/login" class="space-y-4">
                <?= csrf_field() ?>
                <?php if ($labId): ?>
                <input type="hidden" name="lab_id" value="<?= escape($labId) ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Student Number</label>
                    <div class="relative">
                        <i data-lucide="id-card" class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400"></i>
                        <input type="text" id="stNumInput" name="student_number" required placeholder="e.g. 2023-00101" class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400"></i>
                        <input type="password" id="stPassInput" name="password" required placeholder="••••••••" class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors shadow-md shadow-blue-200">
                    Sign In
                </button>
            </form>

            <!-- Quick Demo Student fill -->
            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Demo Account:</span>
                <button type="button" onclick="fillStudent('2023-00101', 'student123')" class="font-medium text-primary-600 hover:underline">
                    2023-00101 (Juan Carlos)
                </button>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="/login" class="text-xs text-gray-600 hover:text-gray-900 font-medium">&larr; Staff & Faculty Login</a>
        </div>
    </div>
    <script>
        function fillStudent(num, pwd) {
            document.getElementById('stNumInput').value = num;
            document.getElementById('stPassInput').value = pwd;
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>
</html>
