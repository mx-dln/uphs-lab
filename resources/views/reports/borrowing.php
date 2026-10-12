<?php $title = '- Borrowing Report'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Borrowing Report</h1></div>
        <a href="/reports" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; All Reports</a>
    </div>
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Start Date</label><input type="date" name="start_date" value="<?= escape($startDate) ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">End Date</label><input type="date" name="end_date" value="<?= escape($endDate) ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm"></div>
            <div class="flex items-end"><button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium w-full">Filter</button></div>
        </div>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrower</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrowed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($data as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?= escape($row['borrower_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['equipment_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['date_borrowed']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['actual_return'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700"><?= ucfirst($row['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?><tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No records found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
