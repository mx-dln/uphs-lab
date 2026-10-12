<?php 
$title = '- Dashboard'; 
$user = $_SESSION['user'] ?? [];
$roleId = (int)($user['role_id'] ?? 0);
$isAdminOrFaculty = in_array($roleId, [1, 2, 4]);
?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Dashboard Options -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Offline Active
                    </span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                    Welcome, <?= escape($user['name'] ?? 'User') ?>! <span class="text-primary-600 dark:text-primary-400 font-medium">(<?= escape(\App\Helpers\Constants::ROLE_LABELS[$roleId] ?? 'Faculty / Administrator') ?>)</span>
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <?php if (isAdmin()): ?>
                <button onclick="openAddAccountModal()" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                    <span>Add Account</span>
                </button>
                <?php endif; ?>

                <?php if (isAdmin() || isCustodian()): ?>
                <a href="/equipment/create" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                    <span>Add Equipment</span>
                </a>
                <?php endif; ?>

                <!-- Check New Equipment for HR, Admin, Faculty -->
                <a href="/equipment?filter=new" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-semibold border border-indigo-200 dark:border-indigo-800 transition-all">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                    <span>Check New Equipment</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800"><?= $new_equipment_count ?? 0 ?></span>
                </a>

                <a href="/equipment/export-pdf" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-xs font-semibold transition-all">
                    <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i>
                    <span>Property Code PDF</span>
                </a>

                <a href="/search" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-xs font-semibold transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-primary-500"></i>
                    <span>Search</span>
                </a>
            </div>
        </div>

        <!-- Dashboard Options Filter Bar -->
        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700/60">
            <form method="GET" action="/dashboard" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-medium text-gray-600 dark:text-gray-400 mb-1">Laboratory Filter</label>
                    <select name="laboratory_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">All Laboratories</option>
                        <?php foreach ($active_laboratories as $lab): ?>
                            <option value="<?= $lab['id'] ?>" <?= ($selected_lab_id == $lab['id']) ? 'selected' : '' ?>>
                                <?= escape($lab['name']) ?> (Room <?= escape($lab['room_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-600 dark:text-gray-400 mb-1">Academic Term / Semester</label>
                    <select name="semester" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="First Semester" <?= $selected_semester === 'First Semester' ? 'selected' : '' ?>>First Semester (Standard)</option>
                        <option value="Second Semester" <?= $selected_semester === 'Second Semester' ? 'selected' : '' ?>>Second Semester</option>
                        <option value="Summer Term" <?= $selected_semester === 'Summer Term' ? 'selected' : '' ?>>Summer Term</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-600 dark:text-gray-400 mb-1">Time Horizon</label>
                    <select name="period" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="today" <?= $selected_period === 'today' ? 'selected' : '' ?>>Today (Live Status)</option>
                        <option value="weekly" <?= $selected_period === 'weekly' ? 'selected' : '' ?>>This Week</option>
                        <option value="monthly" <?= $selected_period === 'monthly' ? 'selected' : '' ?>>This Month</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="w-full py-2 bg-gray-900 hover:bg-black dark:bg-gray-600 dark:hover:bg-gray-500 text-white rounded-xl text-xs font-medium transition-colors">
                        Apply Options
                    </button>
                    <?php if ($selected_lab_id || $selected_semester !== 'First Semester' || $selected_period !== 'today'): ?>
                        <a href="/dashboard" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl text-xs font-medium">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Primary Stats Cards: Features Total Users in Laboratory & First Sem OR -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total users in laboratory -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Total Users in Laboratory</p>
                    <div class="flex items-baseline space-x-2 mt-2">
                        <span class="text-4xl font-extrabold tracking-tight"><?= $total_active_users_in_lab ?></span>
                        <span class="text-xs font-medium text-emerald-100">Currently Active</span>
                    </div>
                    <p class="text-[11px] text-emerald-200 mt-2 flex items-center">
                        <span class="w-2 h-2 mr-1.5 bg-white rounded-full animate-ping"></span>
                        Real-time lab occupants today
                    </p>
                </div>
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>

        <!-- 2. Total Students with First Semester OR -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Students</p>
                    <div class="flex items-baseline space-x-2 mt-2">
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $total_students ?></span>
                        <span class="text-xs text-primary-600 font-medium">Enrolled</span>
                    </div>
                    <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400"><?= $or_stats['first_sem_with_or'] ?? 0 ?></span> with 1st Sem OR
                    </div>
                </div>
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-primary-600 dark:text-primary-400"></i>
                </div>
            </div>
        </div>

        <!-- 3. Total Equipment Inventory -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Equipment</p>
                    <div class="flex items-baseline space-x-2 mt-2">
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $inventory['total'] ?></span>
                        <span class="text-xs text-green-600 font-medium"><?= $inventory['present'] ?> active</span>
                    </div>
                    <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="text-red-500 font-medium"><?= $inventory['missing'] ?> missing</span> &middot; 
                        <span class="text-amber-500 font-medium"><?= $inventory['defective'] ?> repair</span>
                    </div>
                </div>
                <div class="w-14 h-14 bg-purple-50 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center">
                    <i data-lucide="hard-drive" class="w-7 h-7 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- 4. Today's Attendance Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Today's Scans</p>
                    <div class="flex items-baseline space-x-2 mt-2">
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $attendance_today ?></span>
                        <span class="text-xs text-gray-500">logged</span>
                    </div>
                    <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                        <span><?= $attendance_weekly ?> this week</span> &middot; <span><?= $attendance_monthly ?> this month</span>
                    </div>
                </div>
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-amber-600 dark:text-amber-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Laboratory Occupancy Breakdown (Total users in laboratory per room) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-emerald-600"></i>
                    <span>Real-Time Laboratory Occupancy (Total Users Present)</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Live headcount of students and users currently inside each computer laboratory</p>
            </div>
            <a href="/attendance" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                View Live Attendance &rarr;
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach (($laboratoryOccupancy ?? $laboratory_occupancy ?? []) as $room): ?>
                <?php
                $cap = (int)($room['capacity'] ?: 40);
                $occ = (int)$room['active_users'];
                $pct = min(100, round(($occ / $cap) * 100));
                $barColor = $pct > 80 ? 'bg-red-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-emerald-500');
                $statusText = $pct > 80 ? 'Near Capacity' : ($pct > 0 ? 'Occupied' : 'Vacant');
                $badgeBg = $pct > 80 ? 'bg-red-50 text-red-700 border-red-200' : ($pct > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200');
                ?>
                <div class="p-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white"><?= escape($room['name']) ?></h4>
                            <p class="text-xs text-gray-500 font-mono">Room <?= escape($room['room_number']) ?></p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border <?= $badgeBg ?>">
                            <?= $statusText ?>
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between text-xs mt-3 mb-1.5">
                        <span class="font-bold text-gray-900 dark:text-white text-base"><?= $occ ?> <span class="text-xs text-gray-500 font-normal">/ <?= $cap ?> users</span></span>
                        <span class="font-semibold text-gray-600 dark:text-gray-300"><?= $pct ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 overflow-hidden">
                        <div class="<?= $barColor ?> h-2 rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between text-[11px] text-gray-500">
                        <span>Today's Total: <?= $room['total_today_visits'] ?> visits</span>
                        <a href="/attendance/laboratory/<?= $room['id'] ?>" class="text-primary-600 hover:underline">Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Newly Acquired Equipment (HR & Property Watch) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                    <span>Newly Acquired Equipment (HR & Property Watch)</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <?= $new_equipment_count ?? 0 ?> New Items
                    </span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Track recently added assets, Property Codes, lab room deployments, and verification status</p>
            </div>
            <a href="/equipment?filter=new" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                <span>View All New Equipment &rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-semibold">
                        <th class="px-4 py-3 rounded-l-xl">Property Code</th>
                        <th class="px-4 py-3">Asset Tag</th>
                        <th class="px-4 py-3">Equipment Item</th>
                        <th class="px-4 py-3">Classification</th>
                        <th class="px-4 py-3">Deployed Location</th>
                        <th class="px-4 py-3">Acquired / Added</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($new_equipment_list)): ?>
                        <tr><td colspan="7" class="py-6 text-center text-gray-400">No new equipment registered in the last 30 days.</td></tr>
                    <?php else: ?>
                        <?php foreach ($new_equipment_list as $neq): ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                <?= escape($neq['property_code'] ?? 'PROP-N/A') ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-600 dark:text-gray-300">
                                <?= escape($neq['asset_tag']) ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900 dark:text-white"><?= escape($neq['name']) ?></span>
                                <span class="text-[11px] text-gray-400 block"><?= escape($neq['brand'] ? $neq['brand'] . ' ' . $neq['model'] : $neq['category_name']) ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <?= escape($neq['classification'] ?? 'ICT Equipment') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <?= escape($neq['laboratory_name']) ?> (Room <?= escape($neq['room_number']) ?>)
                            </td>
                            <td class="px-4 py-3 text-gray-500 font-mono text-[11px]">
                                <?= !empty($neq['acquisition_date']) ? date('M d, Y', strtotime($neq['acquisition_date'])) : date('M d, Y', strtotime($neq['created_at'])) ?>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <a href="/equipment/<?= $neq['id'] ?>" class="px-2.5 py-1 bg-primary-50 text-primary-700 hover:bg-primary-100 rounded-lg text-xs font-semibold">View</a>
                                <a href="/equipment/<?= $neq['id'] ?>/property-card-pdf" target="_blank" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold">Tag PDF</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Long NR for Dashboard Section -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-5 h-5 text-indigo-600"></i>
                    <span>Long NR Tracking & Requests (Notice of Request / Nature of Request)</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Centralized register of Long NR references for Segment Allocations, Account Requests, and Non-Returned Assets</p>
            </div>
            <!-- Tabs -->
            <div class="flex items-center space-x-1 p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl text-xs font-semibold">
                <button type="button" onclick="switchLongNrTab('segments')" id="tabBtn-segments" class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white transition-all">
                    Segment Requests (<?= count($long_nr_segments) ?>)
                </button>
                <button type="button" onclick="switchLongNrTab('accounts')" id="tabBtn-accounts" class="px-3 py-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 transition-all">
                    Account Requests (<?= count($long_nr_accounts) ?>)
                </button>
                <button type="button" onclick="switchLongNrTab('non_returned')" id="tabBtn-non_returned" class="px-3 py-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 transition-all">
                    Non-Returned (<?= count($long_nr_non_returned) ?>)
                </button>
            </div>
        </div>

        <!-- Tab 1: Long NR Segment Requests -->
        <div id="tabContent-segments" class="block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-semibold">
                        <th class="px-4 py-3 rounded-l-xl">Long NR Number</th>
                        <th class="px-4 py-3">Requester</th>
                        <th class="px-4 py-3">Laboratory & Segment</th>
                        <th class="px-4 py-3">Purpose</th>
                        <th class="px-4 py-3">Schedule</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($long_nr_segments)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">No segment requests recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($long_nr_segments as $sr): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-mono font-bold text-primary-600 dark:text-primary-400"><?= escape($sr['request_number']) ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= escape($sr['requester_name']) ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?= escape($sr['laboratory_name']) ?> &middot; <span class="font-semibold text-gray-800 dark:text-gray-200"><?= escape($sr['segment_name']) ?></span></td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate"><?= escape($sr['purpose']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($sr['request_date']) ?> <?= escape($sr['start_time']) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] <?= $sr['status'] === 'approved' ? 'bg-green-100 text-green-700' : ($sr['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>">
                                    <?= ucfirst($sr['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if ($sr['status'] === 'pending'): ?>
                                <form method="POST" action="/segment-requests/<?= $sr['id'] ?>/approve" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-[10px] font-semibold">Approve</button>
                                </form>
                                <?php endif; ?>
                                <a href="/segment-requests" class="text-primary-600 hover:underline ml-2">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab 2: Long NR Account Requests -->
        <div id="tabContent-accounts" class="hidden overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-semibold">
                        <th class="px-4 py-3 rounded-l-xl">Long NR Number</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email & ID</th>
                        <th class="px-4 py-3">Department / Segment</th>
                        <th class="px-4 py-3">Requested Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($long_nr_accounts)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">No account requests recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($long_nr_accounts as $ar): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-mono font-bold text-primary-600 dark:text-primary-400"><?= escape($ar['request_number']) ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= escape($ar['name']) ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?= escape($ar['email']) ?> <span class="text-gray-400 font-mono">(<?= escape($ar['id_number']) ?>)</span></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?= escape($ar['department_segment']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($ar['requested_role_name'] ?? 'Faculty') ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] <?= $ar['status'] === 'approved' ? 'bg-green-100 text-green-700' : ($ar['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>">
                                    <?= ucfirst($ar['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if ($ar['status'] === 'pending'): ?>
                                <form method="POST" action="/account-requests/<?= $ar['id'] ?>/approve" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-[10px] font-semibold">Approve & Create</button>
                                </form>
                                <?php endif; ?>
                                <a href="/account-requests" class="text-primary-600 hover:underline ml-2">Manage</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab 3: Long NR Non-Returned Items -->
        <div id="tabContent-non_returned" class="hidden overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase font-semibold">
                        <th class="px-4 py-3 rounded-l-xl">Property Code</th>
                        <th class="px-4 py-3">Asset Tag</th>
                        <th class="px-4 py-3">Equipment</th>
                        <th class="px-4 py-3">Borrower</th>
                        <th class="px-4 py-3">Date Borrowed</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($long_nr_non_returned)): ?>
                        <tr><td colspan="8" class="py-8 text-center text-gray-400">No unreturned or overdue equipment. All assets accounted for!</td></tr>
                    <?php else: ?>
                        <?php foreach ($long_nr_non_returned as $nr): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400"><?= escape($nr['property_code'] ?? 'N/A') ?></td>
                            <td class="px-4 py-3 font-mono text-gray-600"><?= escape($nr['asset_tag']) ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= escape($nr['equipment_name']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($nr['borrower_name']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($nr['date_borrowed']) ?></td>
                            <td class="px-4 py-3 font-semibold text-red-600"><?= escape($nr['expected_return']) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-red-100 text-red-700">
                                    <?= ucfirst($nr['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="/borrowing/<?= $nr['id'] ?>/return" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-[10px] font-semibold">Return Asset</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts & Room Audit -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Attendance Trend</h3>
                <span class="text-xs text-gray-400">Past 14 Days</span>
            </div>
            <div class="h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Inventory Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Equipment Condition & Status</h3>
                <a href="/equipment" class="text-xs text-primary-600 font-semibold hover:underline">View All &rarr;</a>
            </div>
            <div class="h-64">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Room Audit & Recent Audits -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Start Room Audit</h3>
            <p class="text-xs text-gray-500 mb-4">Launch immediate inventory verification for a laboratory.</p>
            <form method="POST" action="/audits/start" class="space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Target Laboratory</label>
                    <select name="laboratory_id" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs font-medium text-gray-900 dark:text-white">
                        <option value="">Choose Laboratory...</option>
                        <?php foreach ($active_laboratories as $lab): ?>
                            <option value="<?= $lab['id'] ?>"><?= escape($lab['name']) ?> (Room <?= escape($lab['room_number']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    Begin Verification Audit
                </button>
            </form>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 space-y-1">
                <div class="flex justify-between"><span>Completed Audits:</span> <span class="font-bold text-green-600"><?= $audit_stats['completed'] ?? 0 ?></span></div>
                <div class="flex justify-between"><span>In-Progress:</span> <span class="font-bold text-amber-600"><?= $audit_stats['in_progress'] ?? 0 ?></span></div>
                <div class="flex justify-between"><span>Missing Flags:</span> <span class="font-bold text-red-600"><?= $audit_stats['total_missing'] ?? 0 ?></span></div>
            </div>
        </div>

        <!-- Recent Attendance Table with First Semester OR -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Laboratory Attendance</h3>
                    <p class="text-xs text-gray-500">Live attendance stream with First Semester Official Receipt (OR) verification</p>
                </div>
                <a href="/attendance" class="text-xs font-semibold text-primary-600 hover:underline">Full Log &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                            <th class="px-4 py-2.5 rounded-l-xl">Student</th>
                            <th class="px-4 py-2.5">1st Sem OR #</th>
                            <th class="px-4 py-2.5">Laboratory</th>
                            <th class="px-4 py-2.5">Time In</th>
                            <th class="px-4 py-2.5 rounded-r-xl">Time Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php if (empty($recent_attendance)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">No attendance records logged today yet.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($recent_attendance, 0, 6) as $a): ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 dark:text-white"><?= escape($a['student_name']) ?></div>
                                    <div class="text-[11px] font-mono text-gray-400"><?= escape($a['student_number']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($a['or_number'])): ?>
                                        <span class="font-mono text-xs font-semibold text-primary-600 bg-primary-50 dark:bg-primary-900/30 px-2 py-0.5 rounded-md">
                                            <?= escape($a['or_number']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Pending OR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?= escape($a['laboratory_name']) ?></td>
                                <td class="px-4 py-3 font-semibold text-emerald-600"><?= date('h:i A', strtotime($a['time_in'])) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($a['time_out']): ?>
                                        <span class="font-semibold text-amber-600"><?= date('h:i A', strtotime($a['time_out'])) ?></span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Inside Lab</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isAdmin()): ?>
<!-- Add Account in Dashboard Modal (Admin Only) -->
<div id="addAccountModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200 dark:border-gray-700 animate-fade-in" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/40 text-primary-600 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Add Account in Dashboard</h3>
                    <p class="text-xs text-gray-500">Create administrator, HR, or faculty accounts immediately</p>
                </div>
            </div>
            <button onclick="closeAddAccountModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="/dashboard/account/add" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="Prof. Jane Doe" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="jdoe@uphs.edu.ph" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">System Role</label>
                    <select name="role_id" required class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="4" selected>Faculty</option>
                        <option value="6">Human Resources (HR)</option>
                        <option value="2">Laboratory Administrator</option>
                        <option value="1">Super Administrator</option>
                        <option value="3">Laboratory Custodian</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Department / Segment Assignment</label>
                <input type="text" name="department_segment" placeholder="e.g. Human Resources / College of Computer Studies" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-gray-400 mt-1">Assign department or lab segment. Administrators have full system and user management authority.</p>
            </div>

            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeAddAccountModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function openAddAccountModal() {
    document.getElementById('addAccountModal').classList.remove('hidden');
}
function closeAddAccountModal() {
    document.getElementById('addAccountModal').classList.add('hidden');
}

function switchLongNrTab(tab) {
    const tabs = ['segments', 'accounts', 'non_returned'];
    tabs.forEach(t => {
        const content = document.getElementById('tabContent-' + t);
        const btn = document.getElementById('tabBtn-' + t);
        if (t === tab) {
            content.classList.remove('hidden');
            btn.className = 'px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white font-semibold transition-all';
        } else {
            content.classList.add('hidden');
            btn.className = 'px-3 py-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 transition-all';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Attendance Trend Chart (Offline Chart.js)
    const attCanvas = document.getElementById('attendanceChart');
    if (attCanvas && typeof Chart !== 'undefined') {
        const attCtx = attCanvas.getContext('2d');
        <?php
        $labels = [];
        $values = [];
        foreach ($attendance_trend as $at) {
            $labels[] = date('M d', strtotime($at['date']));
            $values[] = (int)$at['count'];
        }
        ?>
        new Chart(attCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Scans',
                    data: <?= json_encode($values) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    // Inventory Chart (Offline Chart.js)
    const invCanvas = document.getElementById('inventoryChart');
    if (invCanvas && typeof Chart !== 'undefined') {
        const invCtx = invCanvas.getContext('2d');
        <?php
        $invLabels = [];
        $invValues = [];
        foreach ($inventory_status as $is) {
            $invLabels[] = ucfirst(str_replace('_', ' ', $is['status']));
            $invValues[] = (int)$is['count'];
        }
        ?>
        new Chart(invCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($invLabels) ?>,
                datasets: [{
                    data: <?= json_encode($invValues) ?>,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#6b7280'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
            }
        });
    }
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
