<?php $title = '- ' . escape($laboratory['name']); ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="/laboratories" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Laboratories</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= escape($laboratory['name']) ?></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Room <?= escape($laboratory['room_number']) ?><?= $laboratory['building'] ? ' - ' . escape($laboratory['building']) : '' ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/laboratories/<?= $laboratory['id'] ?>/qr" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">View QR</a>
            <a href="/laboratories/<?= $laboratory['id'] ?>/edit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Equipment</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $equipment_count ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Capacity</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= (int)$laboratory['capacity'] ?: 'N/A' ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Attendance Records</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $attendance_count ?></p>
        </div>
    </div>

    <?php if ($laboratory['description']): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
        <p class="text-gray-900 dark:text-white"><?= escape($laboratory['description']) ?></p>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
