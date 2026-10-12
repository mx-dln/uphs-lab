<?php $title = '- Audits'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Audits</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Track equipment audit records</p>
        </div>
        <div class="flex items-center space-x-3">
            <form method="POST" action="/audits/start" class="flex items-center space-x-2">
                <?= csrf_field() ?>
                <select name="laboratory_id" required class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                    <option value="">Select Laboratory</option>
                    <?php foreach ($laboratories as $lab): ?>
                    <option value="<?= $lab['id'] ?>"><?= escape($lab['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    <span>Start Audit</span>
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conducted By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($audits as $a): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= escape($a['laboratory_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['conducted_by_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['audit_date']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $a['status'] === 'completed' ? 'bg-green-100 text-green-700' : ($a['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') ?>">
                                <?= ucfirst(str_replace('_', ' ', $a['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/audits/<?= $a['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View Results</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($audits)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No audit records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
