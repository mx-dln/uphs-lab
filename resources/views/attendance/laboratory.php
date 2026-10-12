<?php $title = '- Attendance - ' . escape($laboratory['name']); ?>
<?php ob_start(); ?>
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= escape($laboratory['name'] ?? '') ?></h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Attendance Records</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                    <?php foreach ($attendance as $a): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?= escape($a['student_name'] ?? '') ?></div>
                            <div class="text-xs text-gray-500"><?= escape($a['student_number'] ?? '') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($a['attendance_date']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= escape($a['time_in']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
