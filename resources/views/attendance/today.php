<?php $title = '- Today\'s Attendance'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Today's Attendance</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1"><?= date('F d, Y') ?> - <?= count($attendance) ?> records</p>
        </div>
        <a href="/attendance" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; All Records</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($attendance as $a): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($a['student_name'] ?? '') ?></div>
                            <div class="text-xs text-gray-500"><?= escape($a['student_number'] ?? '') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['laboratory_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['time_in']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= $a['time_out'] ? escape($a['time_out']) : '<span class="text-gray-400">---</span>' ?></td>
                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs"><?= escape(substr($a['browser'] ?? '', 0, 80)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendance)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No attendance today.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
