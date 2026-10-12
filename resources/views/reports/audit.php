<?php $title = '- Audit Report'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Report</h1></div>
        <a href="/reports" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; All Reports</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conducted By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($data as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= escape($row['laboratory_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['conducted_by_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($row['audit_date']) ?></td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700"><?= ucfirst($row['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?><tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No records found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
