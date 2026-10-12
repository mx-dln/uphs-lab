<?php $title = '- ' . escape($student['full_name']); ?>
<?php ob_start(); ?>
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <a href="/students" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Students</a>
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= escape($student['full_name']) ?></h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1"><?= escape($student['student_number']) ?></p>
            </div>
            <a href="/students/<?= $student['id'] ?>/edit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Student Information</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-sm text-gray-500 dark:text-gray-400">Course</dt><dd class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($student['course'] ?? 'N/A') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500 dark:text-gray-400">Year Level</dt><dd class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($student['year_level'] ?? 'N/A') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500 dark:text-gray-400">Section</dt><dd class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($student['section'] ?? 'N/A') ?></dd></div>
                <div class="flex justify-between"><dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt><dd class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($student['email'] ?? 'N/A') ?></dd></div>
            </dl>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Attendance Summary</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= count($attendance) ?></p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Attendance Records</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($attendance as $a): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?= escape($a['attendance_date']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['laboratory_name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['time_in']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
