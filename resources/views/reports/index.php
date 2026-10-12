<?php $title = '- Reports'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Generate and export system reports</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="/reports/attendance" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="clipboard-check" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance Report</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">View attendance records by date and laboratory</p>
        </a>
        <a href="/reports/equipment" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="hard-drive" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Equipment Inventory Report</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Full inventory, missing, and defective equipment reports</p>
        </a>
        <a href="/reports/audit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="search-check" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Report</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">View completed audit records and findings</p>
        </a>
        <a href="/reports/borrowing" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="arrow-left-right" class="w-6 h-6 text-orange-600 dark:text-orange-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Borrowing Report</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Equipment borrowing and return history</p>
        </a>
        <a href="/investigations" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="search" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Investigation Report</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Missing equipment investigation reports</p>
        </a>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
