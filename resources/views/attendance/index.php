<?php $title = '- Attendance'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance Records</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Monitor student laboratory attendance</p>
        </div>
        <a href="/attendance/today" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">
            <i data-lucide="clock" class="w-4 h-4"></i>
            <span>Today's Attendance</span>
        </a>
    </div>

    <form method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date</label>
                <input type="date" name="date" value="<?= escape($date) ?>" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Laboratory</label>
                <select name="laboratory_id" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <option value="">All Laboratories</option>
                    <?php foreach ($laboratories as $lab): ?>
                    <option value="<?= $lab['id'] ?>" <?= (string)$laboratoryId === (string)$lab['id'] ? 'selected' : '' ?>><?= escape($lab['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium w-full">Filter</button>
            </div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
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
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['attendance_date']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($a['time_in']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= $a['time_out'] ? escape($a['time_out']) : '<span class="text-gray-400">---</span>' ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono"><?= escape($a['ip_address'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendance)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No attendance records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
