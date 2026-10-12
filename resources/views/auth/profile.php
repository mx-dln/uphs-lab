<?php $title = '- Profile'; ?>
<?php $user = $_SESSION['user']; require __DIR__ . '/../layouts/app.php'; ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Manage your account settings</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center text-white text-2xl font-bold"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white"><?= escape($user['name'] ?? '') ?></h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400"><?= escape($user['role_name'] ?? '') ?></p>
                </div>
            </div>
            <form method="POST" action="/profile" class="space-y-5">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="<?= escape($user['name'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="<?= escape($user['email'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors">Update Profile</button>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
