<?php $title = '- Search & Track'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Search & Track (Formerly Investigations)</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Asset lookup, missing equipment investigations, student attendance tracing, and audit follow-ups</p>
        </div>
    </div>

    <!-- Universal Search Bar -->
    <form method="GET" action="/search" class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"></i>
                <input type="text" name="q" value="<?= escape($query) ?>" placeholder="Search by Asset Tag, Property Code, Serial Number, Equipment Name, or Student..." autofocus class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-2xl text-sm focus:ring-2 focus:ring-primary-500 text-gray-900 dark:text-white">
            </div>
            <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Search</span>
            </button>
            <?php if (!empty($query)): ?>
                <a href="/search" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-2xl text-sm font-semibold transition-all">
                    Clear
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Query Results Section (if query is present) -->
    <?php if (!empty($query)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Matched Equipment -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i data-lucide="hard-drive" class="w-4 h-4 text-purple-600"></i>
                <span>Matched Equipment (<?= count($equipmentResults) ?>)</span>
            </h3>
            <div class="space-y-2 max-h-72 overflow-y-auto">
                <?php if (empty($equipmentResults)): ?>
                    <p class="text-xs text-gray-400 py-4 text-center">No equipment matched "<?= escape($query) ?>"</p>
                <?php else: ?>
                    <?php foreach ($equipmentResults as $eq): ?>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <a href="/equipment/<?= $eq['id'] ?>" class="font-bold text-gray-900 dark:text-white hover:underline block"><?= escape($eq['name']) ?></a>
                            <span class="font-mono text-indigo-600"><?= escape($eq['property_code'] ?? 'No Prop Code') ?></span> &middot; 
                            <span class="font-mono text-gray-500"><?= escape($eq['asset_tag']) ?></span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800"><?= ucfirst($eq['status']) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Matched Students -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                <span>Matched Students (<?= count($studentResults) ?>)</span>
            </h3>
            <div class="space-y-2 max-h-72 overflow-y-auto">
                <?php if (empty($studentResults)): ?>
                    <p class="text-xs text-gray-400 py-4 text-center">No students matched "<?= escape($query) ?>"</p>
                <?php else: ?>
                    <?php foreach ($studentResults as $st): ?>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white"><?= escape($st['full_name']) ?></span>
                            <p class="font-mono text-gray-500"><?= escape($st['student_number']) ?> &middot; 1st Sem OR: <?= escape($st['or_number'] ?? 'Pending') ?></p>
                        </div>
                        <a href="/attendance/student/<?= $st['id'] ?>" class="text-primary-600 font-semibold hover:underline">Attendance</a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Active Search & Incident Tracking Records (formerly Investigations) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="shield-alert" class="w-5 h-5 text-red-500"></i>
                    <span>Missing Assets Search & Incident Register</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Tracking missing equipment items, last seen room records, and audit discrepancies</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                <?= count($investigations) ?> Tracked Cases
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-5 py-3.5 rounded-l-2xl">Property Code</th>
                        <th class="px-4 py-3.5">Asset Tag</th>
                        <th class="px-4 py-3.5">Equipment Name</th>
                        <th class="px-4 py-3.5">Laboratory</th>
                        <th class="px-4 py-3.5">Initiated By</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Date Logged</th>
                        <th class="px-5 py-3.5 rounded-r-2xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($investigations)): ?>
                        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No missing asset search records active. Everything is accounted for!</td></tr>
                    <?php else: ?>
                        <?php foreach ($investigations as $inv): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                <?= escape($inv['property_code'] ?? 'N/A') ?>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-gray-600 dark:text-gray-300">
                                <?= escape($inv['asset_tag'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                <?= escape($inv['equipment_name'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                <?= escape($inv['laboratory_name'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                <?= escape($inv['initiated_by_name'] ?? 'Staff') ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    <?= $inv['status'] === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($inv['status'] === 'open' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $inv['status'])) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500">
                                <?= date('M d, Y', strtotime($inv['created_at'])) ?>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="/search/<?= $inv['id'] ?>" class="px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl text-xs font-semibold transition-colors">
                                    Search Trace &rarr;
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
