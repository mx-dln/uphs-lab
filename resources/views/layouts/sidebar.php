<?php
$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentUrl = str_replace('/index.php', '', $currentUrl);
$user = $_SESSION['user'] ?? [];
$roleId = (int)($user['role_id'] ?? 0);

$isAdmin = in_array($roleId, [1, 2]); // Super Admin & Lab Admin
$isFaculty = ($roleId === 4);
$isHR = ($roleId === 6);
$isCustodian = ($roleId === 3);
?>
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out" id="sidebar">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-9">
            <div>
                <span class="text-base font-bold text-gray-900 dark:text-white leading-none block">UPHS Lab</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wider block mt-0.5">
                    <?= $isHR ? 'HR Portal' : ($isFaculty ? 'Faculty Portal' : 'Offline System') ?>
                </span>
            </div>
        </div>
        <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <nav class="p-4 pb-20 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
        <div class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Core</div>
        
        <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>

        <?php if (!$isHR): ?>
        <a href="/laboratories" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'laboratories') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="monitor" class="w-5 h-5"></i>
            <span>Laboratories</span>
        </a>

        <?php if (!$isCustodian): ?>
        <a href="/students" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'students') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="users" class="w-5 h-5"></i>
            <span>Students</span>
        </a>

        <a href="/attendance" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'attendance') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="clipboard-check" class="w-5 h-5"></i>
            <span>Attendance</span>
            <span class="ml-auto bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-xs font-medium px-2 py-0.5 rounded-full">Live</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <div class="pt-3 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
            <?= $isHR ? 'HR Asset Tracking' : 'Equipment Management' ?>
        </div>

        <a href="/equipment" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= (str_contains($currentUrl, 'equipment') && !str_contains($currentUrl, 'equipment-services') && !isset($_GET['filter'])) ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="hard-drive" class="w-5 h-5"></i>
            <span>All Equipment</span>
        </a>

        <!-- HR & Admin: Check New Equipment feature -->
        <a href="/equipment?filter=new" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium pl-8 transition-all duration-200 <?= (isset($_GET['filter']) && $_GET['filter'] === 'new') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50/50' ?>">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
            <span>New Equipment</span>
            <span class="ml-auto bg-amber-100 text-amber-800 text-[10px] font-bold px-1.5 py-0.5 rounded-full">HR</span>
        </a>

        <?php if ($isAdmin || $isCustodian): ?>
        <a href="/equipment/create" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium pl-8 transition-all duration-200 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add Equipment</span>
        </a>
        <?php endif; ?>

        <?php if (!$isHR): ?>
        <a href="/equipment-services" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'equipment-services') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="wrench" class="w-5 h-5"></i>
            <span>Services</span>
        </a>
        <?php endif; ?>

        <div class="pt-3 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Operations & Requests</div>

        <a href="/search" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= (str_contains($currentUrl, 'search') || str_contains($currentUrl, 'investigations')) ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="search" class="w-5 h-5"></i>
            <span>Search</span>
        </a>

        <?php if ($isAdmin || $isCustodian): ?>
        <a href="/audits" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'audits') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="search-check" class="w-5 h-5"></i>
            <span>Audits</span>
        </a>
        <?php endif; ?>

        <?php if (!$isHR): ?>
        <a href="/borrowing" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'borrowing') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
            <span>Borrowing</span>
        </a>

        <a href="/segment-requests" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'segment-requests') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="layers" class="w-5 h-5"></i>
            <span>Segment Requests</span>
        </a>
        <?php endif; ?>

        <!-- Account Requests: ADMIN ONLY (Faculty & HR cannot access) -->
        <?php if ($isAdmin): ?>
        <a href="/account-requests" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'account-requests') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="user-check" class="w-5 h-5"></i>
            <span>Account Requests</span>
            <span class="ml-auto bg-rose-100 text-rose-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">Admin</span>
        </a>
        <?php endif; ?>

        <div class="pt-3 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
            <?= $isAdmin ? 'Reports & Administration' : 'Reports & Exports' ?>
        </div>

        <a href="/reports" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'reports') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Reports</span>
        </a>

        <!-- Activity Logs: ADMIN ONLY (Faculty & HR cannot access) -->
        <?php if ($isAdmin): ?>
        <a href="/activity-logs" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'activity-logs') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="activity" class="w-5 h-5"></i>
            <span>Activity Logs</span>
            <span class="ml-auto bg-rose-100 text-rose-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">Admin</span>
        </a>

        <!-- System Settings: ADMIN ONLY (Faculty & HR cannot access) -->
        <a href="/settings" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= str_contains($currentUrl, 'settings') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' ?>">
            <i data-lucide="settings" class="w-5 h-5"></i>
            <span>Settings</span>
            <span class="ml-auto bg-rose-100 text-rose-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">Admin</span>
        </a>
        <?php endif; ?>
    </nav>
</aside>
