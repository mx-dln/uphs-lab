<?php $title = $student ? '- Edit Student' : '- Add Student'; ?>
<?php ob_start(); ?>
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $student ? 'Edit Student' : 'Add Student' ?></h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1"><?= $student ? 'Update student information' : 'Register a new student' ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="<?= $student ? '/students/' . $student['id'] : '/students' ?>" class="p-6 sm:p-8 space-y-5">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Student Number</label>
                    <input type="text" name="student_number" value="<?= escape($student['student_number'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                    <input type="text" name="full_name" value="<?= escape($student['full_name'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Course</label>
                    <input type="text" name="course" value="<?= escape($student['course'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Year Level</label>
                    <select name="year_level" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">Select</option>
                        <?php foreach (\App\Helpers\Constants::YEAR_LEVELS as $yl): ?>
                        <option value="<?= $yl ?>" <?= ($student['year_level'] ?? '') === $yl ? 'selected' : '' ?>><?= $yl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Section</label>
                    <input type="text" name="section" value="<?= escape($student['section'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?= escape($student['email'] ?? '') ?>" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password <?= $student ? '(leave empty to keep)' : '' ?></label>
                    <input type="password" name="password" <?= $student ? '' : 'required' ?> class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="flex items-center space-x-4 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors"><?= $student ? 'Update' : 'Create' ?></button>
                <a href="/students" class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium rounded-xl transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
