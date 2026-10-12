<?php $title = '- Investigations'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Investigations</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Missing equipment investigation reports</p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Tag</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Initiated By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($investigations as $inv): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= escape($inv['equipment_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400"><?= escape($inv['asset_tag'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($inv['initiated_by_name'] ?? '') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $inv['status'] === 'resolved' ? 'bg-green-100 text-green-700' : ($inv['status'] === 'open' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                <?= ucfirst(str_replace('_', ' ', $inv['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= date('M d, Y', strtotime($inv['created_at'])) ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="/investigations/<?= $inv['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View Report</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($investigations)): ?>
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No investigations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
