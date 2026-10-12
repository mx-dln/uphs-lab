<?php $title = '- Equipment Services'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Equipment Services & Maintenance</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Track preventive maintenance, repairs, routine inspections, and servicing schedules</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/equipment-services/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>+ Log New Service</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/equipment-services" class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="relative sm:col-span-2">
                <i data-lucide="search" class="absolute left-3.5 top-3 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="<?= escape($search) ?>" placeholder="Search equipment name, property code, technician..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <select name="type" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500">
                    <option value="">All Service Types</option>
                    <?php foreach ($serviceTypes as $st): ?>
                        <option value="<?= escape($st) ?>" <?= ($type === $st) ? 'selected' : '' ?>><?= escape($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <select name="status" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500">
                    <option value="">All Statuses</option>
                    <option value="completed" <?= ($status === 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="in_progress" <?= ($status === 'in_progress') ? 'selected' : '' ?>>In Progress / Under Repair</option>
                    <option value="scheduled" <?= ($status === 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Services Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-5 py-3.5 rounded-l-2xl">Property Code</th>
                        <th class="px-4 py-3.5">Equipment Name</th>
                        <th class="px-4 py-3.5">Service Type</th>
                        <th class="px-4 py-3.5">Technician / Company</th>
                        <th class="px-4 py-3.5">Service Date</th>
                        <th class="px-4 py-3.5">Cost</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 rounded-r-2xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($services)): ?>
                        <tr><td colspan="8" class="py-12 text-center text-gray-400">No equipment service records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $srv): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                <?= escape($srv['property_code'] ?? $srv['asset_tag']) ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="/equipment/<?= $srv['equipment_id'] ?>" class="font-bold text-gray-900 dark:text-white hover:underline">
                                    <?= escape($srv['equipment_name']) ?>
                                </a>
                                <p class="text-[11px] text-gray-400"><?= escape($srv['laboratory_name'] ?? '') ?></p>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-amber-700 dark:text-amber-400">
                                <?= escape($srv['service_type']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                <span class="font-semibold text-gray-800 dark:text-gray-200"><?= escape($srv['technician_name'] ?? 'Staff') ?></span>
                                <?php if (!empty($srv['company_or_dept'])): ?>
                                    <br><span class="text-[11px] text-gray-400"><?= escape($srv['company_or_dept']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">
                                <?= escape($srv['service_date']) ?>
                                <?php if (!empty($srv['next_service_date'])): ?>
                                    <br><span class="text-[10px] text-gray-400">Next: <?= escape($srv['next_service_date']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 font-mono font-medium">
                                <?= $srv['cost'] > 0 ? 'PHP ' . number_format((float)$srv['cost'], 2) : 'Free / Internal' ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    <?= $srv['status'] === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($srv['status'] === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $srv['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <?php if ($srv['status'] !== 'completed'): ?>
                                <form method="POST" action="/equipment-services/<?= $srv['id'] ?>/complete" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-semibold">Mark Done</button>
                                </form>
                                <?php endif; ?>
                                <a href="/equipment/<?= $srv['equipment_id'] ?>" class="text-primary-600 hover:underline font-semibold">Asset</a>
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
