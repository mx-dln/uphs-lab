<?php $title = '- Request for Segment'; ?>
<?php ob_start(); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="/segment-requests" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 mb-2 inline-block">&larr; Back to Segment Requests</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Request for Segment (Long NR)</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Submit request for laboratory partition, equipment bay, or specialized workstation allocation</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <form method="POST" action="/segment-requests" class="space-y-4 text-xs">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Requester Name <span class="text-red-500">*</span></label>
                    <input type="text" name="requester_name" value="<?= escape($_SESSION['user']['name'] ?? '') ?>" required placeholder="e.g. Prof. Maria Santos" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Requester Email <span class="text-red-500">*</span></label>
                    <input type="email" name="requester_email" value="<?= escape($_SESSION['user']['email'] ?? '') ?>" required placeholder="msantos@uphs.edu.ph" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Target Laboratory <span class="text-red-500">*</span></label>
                    <select name="laboratory_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">Select Laboratory...</option>
                        <?php foreach ($laboratories as $lab): ?>
                            <option value="<?= $lab['id'] ?>"><?= escape($lab['name']) ?> (Room <?= escape($lab['room_number']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Segment / Bay Name <span class="text-red-500">*</span></label>
                    <input type="text" name="segment_name" required placeholder="e.g. Cisco Bay Segment A, Multimedia Station 2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Date Needed <span class="text-red-500">*</span></label>
                    <input type="date" name="request_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Start Time</label>
                    <input type="text" name="start_time" placeholder="08:00 AM" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">End Time</label>
                    <input type="text" name="end_time" placeholder="11:00 AM" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Expected Users / Students</label>
                    <input type="number" name="expected_users" value="20" min="1" max="100" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Equipment / Peripherals Needed</label>
                    <input type="text" name="equipment_needed" placeholder="e.g. Cisco Routers, Projector, Network Cables" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Purpose & Class Description <span class="text-red-500">*</span></label>
                <textarea name="purpose" rows="3" required placeholder="Describe course, subject, activity, or event for this segment request..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <a href="/segment-requests" class="text-xs text-gray-500 hover:underline">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Submit Segment Request
                </button>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
