<?php $title = '- Settings'; ?>
<?php ob_start(); ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Settings</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Configure system parameters</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="/settings" class="p-6 sm:p-8 space-y-5">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Duplicate Scan Minutes</label>
                    <input type="number" name="duplicate_scan_minutes" value="<?= escape($settings['duplicate_scan_minutes'] ?? '30') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                    <p class="text-xs text-gray-500 mt-1">Minutes to prevent duplicate attendance scans</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Session Lifetime (minutes)</label>
                    <input type="number" name="session_lifetime" value="<?= escape($settings['session_lifetime'] ?? '120') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">App Name</label>
                    <input type="text" name="app_name" value="<?= escape($settings['app_name'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">QR Code Size</label>
                    <input type="number" name="qr_size" value="<?= escape($settings['qr_size'] ?? '300') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors">Save Settings</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
