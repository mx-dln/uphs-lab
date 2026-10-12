<?php $title = '- Equipment Scan'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Scan - <?= escape($equipment['name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }</style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-200 p-8">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Equipment Verified</h1>
            <p class="text-2xl font-bold text-primary-600 mt-3"><?= escape($equipment['name']) ?></p>
            <p class="text-sm font-mono text-gray-500 mt-1">Asset Tag: <?= escape($equipment['asset_tag']) ?></p>
            <?php if (!empty($equipment['property_code'])): ?>
                <p class="text-xs font-mono font-semibold text-indigo-600 mt-1">Property Code: <?= escape($equipment['property_code']) ?></p>
            <?php endif; ?>
            <?php if (!empty($equipment['classification'])): ?>
                <p class="text-xs text-gray-600 mt-1">Classification: <?= escape($equipment['classification']) ?></p>
            <?php endif; ?>
            <div class="mt-4 inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                Status: <?= ucfirst(str_replace('_', ' ', $equipment['status'])) ?>
            </div>
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center justify-center gap-4">
                <a href="/equipment/<?= $equipment['id'] ?>" class="text-xs text-primary-600 font-semibold hover:underline">View in Equipment List &rarr;</a>
            </div>
        </div>
    </div>
    <script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
</body>
</html>
