<?php $title = '- Borrowing'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment Borrowing</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage equipment borrowing and returns</p>
        </div>
        <a href="/borrowing/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New Borrowing</span>
        </a>
    </div>

    <form method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center space-x-3">
            <select name="status" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                <option value="">All Status</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="borrowed" <?= $status === 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
                <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Returned</option>
                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Filter</button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrower</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Borrowed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expected Return</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($borrowings as $b): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= escape($b['borrower_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($b['equipment_name'] ?? '') ?> (<?= escape($b['asset_tag'] ?? '') ?>)</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($b['date_borrowed']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($b['expected_return']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                <?php switch($b['status']): case 'returned': ?>bg-green-100 text-green-700<?php break; case 'pending': ?>bg-yellow-100 text-yellow-700<?php break; case 'approved': case 'borrowed': ?>bg-blue-100 text-blue-700<?php break; case 'rejected': ?>bg-red-100 text-red-700<?php break; default: ?>bg-gray-100 text-gray-700<?php endswitch; ?>">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($b['status'] === 'pending'): ?>
                            <form method="POST" action="/borrowing/<?= $b['id'] ?>/approve" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="equipment_id" value="<?= $b['equipment_id'] ?>">
                                <button type="submit" class="text-green-600 hover:text-green-700 text-sm font-medium">Approve</button>
                            </form>
                            <form method="POST" action="/borrowing/<?= $b['id'] ?>/reject" class="inline ml-2">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">Reject</button>
                            </form>
                            <?php elseif ($b['status'] === 'borrowed'): ?>
                            <form method="POST" action="/borrowing/<?= $b['id'] ?>/return" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="equipment_id" value="<?= $b['equipment_id'] ?>">
                                <button type="submit" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Mark Returned</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($borrowings)): ?>
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No borrowing records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
