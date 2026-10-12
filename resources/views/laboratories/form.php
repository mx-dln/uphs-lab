<?php $title = $laboratory ? '- Edit Laboratory' : '- Add Laboratory'; ?>
<?php ob_start(); ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $laboratory ? 'Edit Laboratory' : 'Add Laboratory' ?></h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1"><?= $laboratory ? 'Update laboratory information' : 'Create a new laboratory' ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="<?= $laboratory ? '/laboratories/' . $laboratory['id'] : '/laboratories' ?>" class="p-6 sm:p-8 space-y-5">
            <?= csrf_field() ?>
            <?php if ($laboratory): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Laboratory Name</label>
                    <input type="text" name="name" value="<?= escape($laboratory['name'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Room Number</label>
                    <input type="text" name="room_number" value="<?= escape($laboratory['room_number'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Building</label>
                    <input type="text" name="building" value="<?= escape($laboratory['building'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Capacity</label>
                    <input type="number" name="capacity" value="<?= (int)($laboratory['capacity'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500"><?= escape($laboratory['description'] ?? '') ?></textarea>
            </div>
            <div class="flex items-center space-x-4 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors"><?= $laboratory ? 'Update' : 'Create' ?></button>
                <a href="/laboratories" class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium rounded-xl transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
