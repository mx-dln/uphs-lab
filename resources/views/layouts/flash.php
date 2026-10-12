<?php
$success = session_flash('success');
$error = session_flash('error');
$info = session_flash('info');
$warning = session_flash('warning');
?>
<?php if ($success): ?>
<div class="fixed top-4 right-4 z-[100] animate-slide-in">
    <div class="bg-white dark:bg-gray-800 border-l-4 border-green-500 rounded-xl shadow-lg p-4 pr-12 relative max-w-md">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($success) ?></p>
        </div>
        <button onclick="this.parentElement.remove()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="fixed top-4 right-4 z-[100] animate-slide-in">
    <div class="bg-white dark:bg-gray-800 border-l-4 border-red-500 rounded-xl shadow-lg p-4 pr-12 relative max-w-md">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($error) ?></p>
        </div>
        <button onclick="this.parentElement.remove()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
<?php endif; ?>
