<?php $title = '- Investigation - ' . escape($investigation['equipment_name']); ?>
<?php ob_start(); ?>
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <a href="/investigations" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Investigations</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Investigation Report</h1>
        <p class="text-gray-500 dark:text-gray-400"><?= escape($investigation['equipment_name']) ?> (<?= escape($investigation['asset_tag']) ?>)</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Asset Information</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Equipment</dt><dd class="text-sm font-medium text-gray-900"><?= escape($investigation['equipment_name'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Asset Tag</dt><dd class="text-sm font-medium text-gray-900"><?= escape($investigation['asset_tag'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Serial Number</dt><dd class="text-sm font-medium text-gray-900"><?= escape($investigation['serial_number'] ?? 'N/A') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Laboratory</dt><dd class="text-sm font-medium text-gray-900"><?= escape($investigation['laboratory_name'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Current Status</dt><dd class="text-sm font-medium text-red-600"><?= ucfirst(str_replace('_', ' ', $investigation['equipment_status'] ?? '')) ?></dd></div>
            </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Last Audit Date</dt><dd class="text-sm font-medium text-gray-900"><?= $lastAudit['last_audit_date'] ?? 'No audit found' ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Last Seen Date</dt><dd class="text-sm font-medium text-gray-900"><?= ($lastSeen['last_seen'] ?? 'N/A') . ' ' . ($lastSeen['last_time'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500">Investigation Started</dt><dd class="text-sm font-medium text-gray-900"><?= date('M d, Y', strtotime($investigation['created_at'])) ?></dd></div>
            </dl>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Entry Records After Last Audit</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Students who entered the laboratory after the last confirmed audit</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($studentsAfterAudit as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?= escape($s['full_name'] ?? '') ?></div>
                            <div class="text-xs text-gray-500"><?= escape($s['student_number'] ?? '') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($s['attendance_date'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($s['time_in'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($studentsAfterAudit)): ?>
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">No student records found for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($investigation['status'] !== 'resolved'): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resolve Investigation</h3>
        <form method="POST" action="/investigations/<?= $investigation['id'] ?>/resolve" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Findings</label>
                <textarea name="findings" rows="4" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Recommendations</label>
                <textarea name="recommendations" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm"></textarea>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl">Resolve Investigation</button>
        </form>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resolution</h3>
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
            <p class="text-sm font-medium text-green-700 dark:text-green-400">Resolved on <?= date('M d, Y h:i A', strtotime($investigation['resolved_at'])) ?></p>
        </div>
        <?php if ($investigation['findings']): ?>
        <div class="mt-4"><h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Findings</h4><p class="text-sm text-gray-600 dark:text-gray-400"><?= escape($investigation['findings']) ?></p></div>
        <?php endif; ?>
        <?php if ($investigation['recommendations']): ?>
        <div class="mt-4"><h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recommendations</h4><p class="text-sm text-gray-600 dark:text-gray-400"><?= escape($investigation['recommendations']) ?></p></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
