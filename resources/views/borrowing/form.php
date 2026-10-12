<?php $title = '- New Borrowing'; ?>
<?php ob_start(); ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Borrowing Request</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Create a new equipment borrowing request</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="/borrowing" class="p-6 sm:p-8 space-y-5">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Borrower</label>
                    <select name="borrower_id" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                        <option value="">Select User</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= escape($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Equipment</label>
                    <select name="equipment_id" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                        <option value="">Select Equipment</option>
                        <?php foreach ($equipment as $eq): ?>
                        <option value="<?= $eq['id'] ?>"><?= escape($eq['name']) ?> (<?= escape($eq['asset_tag']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date Borrowed</label>
                    <input type="date" name="date_borrowed" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expected Return</label>
                    <input type="date" name="expected_return" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm"></textarea>
            </div>
            <div class="flex items-center space-x-4 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl">Submit Request</button>
                <a href="/borrowing" class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium rounded-xl">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
