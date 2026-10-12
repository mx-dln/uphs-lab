<?php $title = '- Search & Asset Investigation - ' . escape($investigation['equipment_name']); ?>
<?php ob_start(); ?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="/search" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 transition-colors mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Universal Search & Tracking
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="search" class="w-6 h-6 text-primary-600"></i>
                Asset Trace & Investigation Report
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                <?= escape($investigation['equipment_name']) ?> &middot; Tag: <span class="font-mono font-bold text-gray-700 dark:text-gray-300"><?= escape($investigation['asset_tag']) ?></span>
                <?php if (!empty($investigation['property_code'])): ?>
                    &middot; Property Code: <span class="font-mono font-bold text-primary-600"><?= escape($investigation['property_code']) ?></span>
                <?php endif; ?>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/equipment/<?= $investigation['equipment_id'] ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors">
                <i data-lucide="package" class="w-4 h-4 mr-2"></i> View Asset Details
            </a>
            <a href="/equipment/<?= $investigation['equipment_id'] ?>/property-card-pdf" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Property Tag PDF
            </a>
        </div>
    </div>

    <!-- Status Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Asset Status</span>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $investigation['equipment_status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : ($investigation['equipment_status'] === 'missing' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                    <?= ucfirst(str_replace('_', ' ', $investigation['equipment_status'] ?? 'unknown')) ?>
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Laboratory: <?= escape($investigation['laboratory_name'] ?? 'N/A') ?> (<?= escape($investigation['room_number'] ?? '') ?>)</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Search / Incident Status</span>
            <div class="mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $investigation['status'] === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                    <?= ucfirst($investigation['status'] ?? 'open') ?>
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Started: <?= date('M d, Y', strtotime($investigation['created_at'])) ?></p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Audit Verified</span>
            <div class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                <?= !empty($lastAudit['last_audit_date']) ? date('M d, Y', strtotime($lastAudit['last_audit_date'])) : '<span class="text-gray-400 font-normal">None recorded</span>' ?>
            </div>
            <p class="text-xs text-gray-400 mt-2">Official physical audit check</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Lab Activity Seen</span>
            <div class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                <?= !empty($lastSeen['last_seen']) ? date('M d, Y', strtotime($lastSeen['last_seen'])) : '<span class="text-gray-400 font-normal">No attendance</span>' ?>
            </div>
            <p class="text-xs text-gray-400 mt-2"><?= !empty($lastSeen['last_time']) ? 'Time: ' . date('h:i A', strtotime($lastSeen['last_time'])) : 'N/A' ?></p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i data-lucide="tag" class="w-5 h-5 text-primary-600"></i> Property & Hardware Details
            </h3>
            <dl class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Official Property Code</dt>
                    <dd class="font-mono font-bold text-primary-600"><?= escape($investigation['property_code'] ?? 'None') ?></dd>
                </div>
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Asset Tag</dt>
                    <dd class="font-mono font-medium text-gray-900 dark:text-white"><?= escape($investigation['asset_tag']) ?></dd>
                </div>
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Hardware Item Name</dt>
                    <dd class="font-medium text-gray-900 dark:text-white"><?= escape($investigation['equipment_name']) ?></dd>
                </div>
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Serial Number</dt>
                    <dd class="font-mono text-gray-700 dark:text-gray-300"><?= escape($investigation['serial_number'] ?? 'N/A') ?></dd>
                </div>
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Assigned Laboratory</dt>
                    <dd class="text-gray-900 dark:text-white"><?= escape($investigation['laboratory_name'] ?? '') ?> (Room <?= escape($investigation['room_number'] ?? '') ?>)</dd>
                </div>
                <div class="py-2.5 flex justify-between">
                    <dt class="text-gray-500">Search Initiated By</dt>
                    <dd class="text-gray-900 dark:text-white"><?= escape($investigation['initiated_by_name'] ?? 'Staff') ?></dd>
                </div>
            </dl>
        </div>

        <!-- Investigation Resolution Card -->
        <div>
            <?php if ($investigation['status'] !== 'resolved'): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i> Resolve Search Tracking
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Record your findings, whether the item was located, recovered, or scheduled for replacement.</p>

                <form method="POST" action="/search/<?= $investigation['id'] ?>/resolve" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Findings & Recovery Notes</label>
                        <textarea name="findings" rows="3" required placeholder="Describe where the asset was found or why it went missing..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Recommendations / Preventative Actions</label>
                        <textarea name="recommendations" rows="2" placeholder="e.g. Improved room locking, property tag sticker replacement..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm shadow-sm transition-colors">
                        Mark Search as Resolved
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2 text-emerald-600 font-bold mb-3">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                    <span>Search Case Resolved</span>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-300 mb-4">
                    Resolved on <?= date('F d, Y \a\t h:i A', strtotime($investigation['resolved_at'])) ?>
                </div>
                <?php if (!empty($investigation['findings'])): ?>
                    <div class="mb-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Findings</h4>
                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700"><?= nl2br(escape($investigation['findings'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($investigation['recommendations'])): ?>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Recommendations</h4>
                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700"><?= nl2br(escape($investigation['recommendations'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Student Attendance Trace Table (With 1st Sem OR) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-primary-600"></i> Laboratory Student Entry Records
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Students with recorded attendance in this room after the last confirmed audit date</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                <?= count($studentsAfterAudit) ?> student record(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-3.5">Student Information</th>
                        <th class="px-6 py-3.5">1st Sem OR Number</th>
                        <th class="px-6 py-3.5">Semester & Year</th>
                        <th class="px-6 py-3.5">Attendance Date</th>
                        <th class="px-6 py-3.5">Time In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <?php foreach ($studentsAfterAudit as $s): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white"><?= escape($s['full_name'] ?? '') ?></div>
                                <div class="font-mono text-xs text-gray-500"><?= escape($s['student_number'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (!empty($s['or_number'])): ?>
                                    <span class="font-mono text-xs font-bold text-primary-600 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded-md">
                                        <?= escape($s['or_number']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 italic">No OR Recorded</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300">
                                <?= escape($s['semester'] ?? 'First Semester') ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300">
                                <?= !empty($s['attendance_date']) ? date('M d, Y', strtotime($s['attendance_date'])) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300 font-mono">
                                <?= !empty($s['time_in']) ? date('h:i A', strtotime($s['time_in'])) : 'N/A' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($studentsAfterAudit)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No student attendance logs found for this laboratory during the tracking timeframe.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
