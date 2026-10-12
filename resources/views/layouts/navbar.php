<?php 
$user = $_SESSION['user'] ?? [];
$roleLabels = \App\Helpers\Constants::ROLE_LABELS;
$roleName = $roleLabels[$user['role_id'] ?? 0] ?? ($user['role_name'] ?? 'User');
?>
<header class="sticky top-0 z-40 bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div class="flex items-center space-x-3">
                <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-8 lg:hidden">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700">
                    <span class="w-2 h-2 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    Offline Mode Active
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="toggleDarkMode()" title="Toggle Dark/Light Mode" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
            </button>
            <div class="relative">
                <button onclick="toggleUserMenu()" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-900 dark:text-white leading-tight"><?= escape($user['name'] ?? '') ?></p>
                        <p class="text-[11px] text-primary-600 dark:text-primary-400 font-medium"><?= escape($roleName) ?></p>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden sm:block"></i>
                </button>
                <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">
                    <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 sm:hidden">
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($user['name'] ?? '') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= escape($roleName) ?></p>
                    </div>
                    <a href="/profile" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <span>Profile</span>
                    </a>
                    <a href="/change-password" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        <span>Change Password</span>
                    </a>
                    <hr class="my-2 border-gray-200 dark:border-gray-700">
                    <form method="POST" action="/logout">
                        <?= csrf_field() ?>
                        <button type="submit" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 w-full text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
