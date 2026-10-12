<?php $title = '- Equipment Report'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment Report</h1><p class="text-gray-500 dark:text-gray-400 mt-1"><?= ucfirst($type) ?> equipment</p></div>
        <a href="/reports" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; All Reports</a>
    </div>
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                <select name="type" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All Equipment</option>
                    <option value="missing" <?= $type === 'missing' ? 'selected' : '' ?>>Missing Equipment</option>
                    <option value="defective" <?= $type === 'defective' ? 'selected' : '' ?>>Defective Equipment</option>
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Laboratory</label>
                <select name="laboratory_id" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <option value="">All</option>
                    <?php foreach ($laboratories as $lab): ?>
                    <option value="<?= $lab['id'] ?>" <?= (string)$labId === (string)$lab['id'] ? 'selected' : '' ?>><?= escape($lab['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end"><button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium w-full">Generate</button></div>
        </div>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Tag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($data as $row): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm font-mono text-gray-900"><?= escape($row['asset_tag']) ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= escape($row['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['category_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['laboratory_name'] ?? '') ?></td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700"><?= ucfirst(str_replace('_', ' ', $row['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?><tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No records found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
