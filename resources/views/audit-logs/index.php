<?php $title = '- Activity Logs'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Logs</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">System audit trail</p>
        </div>
    </div>

    <form method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center space-x-3">
            <select name="action" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                <option value="">All Actions</option>
                <?php foreach ($actions as $a): ?>
                <option value="<?= escape($a['action']) ?>" <?= $selectedAction === $a['action'] ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $a['action'])) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Filter</button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?= escape($log['user_name'] ?? 'System') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <?= ucfirst(str_replace('_', ' ', $log['action'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($log['description'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-500 dark:text-gray-400"><?= escape($log['ip_address'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No activity logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
