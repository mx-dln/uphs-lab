<?php $title = '- Students'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Students</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage student records</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/students/export-csv" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
            </a>
            <a href="/students/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Student</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <form method="GET" class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3.5 top-3 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="search" value="<?= escape($search) ?>" placeholder="Search students..." class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                </div>
                <select name="course" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                    <option value="<?= escape($c['course']) ?>" <?= $course === $c['course'] ? 'selected' : '' ?>><?= escape($c['course']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="year_level" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <option value="">All Year Levels</option>
                    <?php foreach ($yearLevels as $yl): ?>
                    <option value="<?= $yl ?>" <?= $yearLevel === $yl ? 'selected' : '' ?>><?= $yl ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Filter</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Student #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Section</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($students as $s): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= escape($s['student_number']) ?></td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white"><?= escape($s['full_name']) ?></div>
                            <?php if ($s['email']): ?><div class="text-xs text-gray-500"><?= escape($s['email']) ?></div><?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($s['course'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($s['year_level'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?= escape($s['section'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="/students/<?= $s['id'] ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                            <a href="/students/<?= $s['id'] ?>/edit" class="text-gray-500 hover:text-gray-700 text-sm ml-3">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($students)): ?>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">No students found.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <h3 class="text-sm sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Import Students from CSV</h3>
        <form method="POST" action="/students/import-csv" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <?= csrf_field() ?>
            <input type="file" name="csv_file" accept=".csv" required class="text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-600 dark:file:text-primary-400 hover:file:bg-primary-100 w-full sm:w-auto">
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium w-full sm:w-auto">Import</button>
        </form>
        <?php if (isset($_SESSION['_flash']['import_errors'])): ?>
        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
            <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-2">Import Errors:</p>
            <ul class="text-sm text-red-600 dark:text-red-300 space-y-1">
                <?php foreach ($_SESSION['_flash']['import_errors'] as $err): ?>
                <li><?= escape($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['_flash']['import_errors']); ?>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
