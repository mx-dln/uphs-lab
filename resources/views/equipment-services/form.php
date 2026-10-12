<?php $title = '- Log Equipment Service'; ?>
<?php ob_start(); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="/equipment-services" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 mb-2 inline-block">&larr; Back to Services List</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Log Equipment Service & Maintenance</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Record routine servicing, preventive check, hardware repair, or technical inspection</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <form method="POST" action="/equipment-services" class="space-y-4 text-xs">
            <?= csrf_field() ?>

            <div>
                <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Equipment Asset <span class="text-red-500">*</span></label>
                <select name="equipment_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    <option value="">Select Equipment...</option>
                    <?php foreach ($equipmentList as $item): ?>
                        <option value="<?= $item['id'] ?>" <?= ($equipmentId == $item['id']) ? 'selected' : '' ?>>
                            <?= escape($item['name']) ?> &middot; [<?= escape($item['property_code'] ?? $item['asset_tag']) ?>]
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Service Type <span class="text-red-500">*</span></label>
                    <select name="service_type" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        <?php foreach ($serviceTypes as $st): ?>
                            <option value="<?= escape($st) ?>"><?= escape($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Service Date <span class="text-red-500">*</span></label>
                    <input type="date" name="service_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Technician / Engineer</label>
                    <input type="text" name="technician_name" placeholder="e.g. Alex Villanueva" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Company / Department</label>
                    <input type="text" name="company_or_dept" placeholder="e.g. UPHS IT Support / HP Authorized Care" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Service Cost (PHP)</label>
                    <input type="number" step="0.01" name="cost" value="0.00" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Current Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        <option value="completed" selected>Completed</option>
                        <option value="in_progress">In Progress / Under Repair</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Next Recommended Service</label>
                    <input type="date" name="next_service_date" value="<?= date('Y-m-d', strtotime('+6 months')) ?>" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Service Notes & Diagnosis</label>
                <textarea name="notes" rows="3" placeholder="Parts replaced, diagnostic notes, cleaning details, calibration settings..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <a href="/equipment-services" class="text-xs text-gray-500 hover:underline">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Save Service Record
                </button>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
