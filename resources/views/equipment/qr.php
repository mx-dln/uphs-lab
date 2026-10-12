<?php $title = '- QR - ' . escape($equipment['name']); ?>
<?php ob_start(); ?>
<div class="max-w-2xl mx-auto text-center">
    <div class="mb-8">
        <a href="/equipment/<?= $equipment['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Equipment</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2"><?= escape($equipment['name']) ?></h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1"><?= escape($equipment['asset_tag']) ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 inline-block">
        <?php if ($equipment['qr_code'] && file_exists(dirname(__DIR__, 3) . '/' . $equipment['qr_code'])): ?>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4">
                <img src="/<?= $equipment['qr_code'] ?>" alt="QR Code" class="w-48 h-48 mx-auto">
                <p class="text-sm font-bold text-gray-900 dark:text-white mt-3 text-center"><?= escape($equipment['name']) ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center"><?= escape($equipment['asset_tag']) ?></p>
            </div>
        <?php else: ?>
            <div class="w-64 h-64 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto">
                <p class="text-gray-500">No QR generated</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="flex items-center justify-center space-x-4 mt-4">
        <button onclick="window.print()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print</span>
        </button>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
