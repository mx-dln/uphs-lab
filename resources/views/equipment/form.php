<?php 
$title = $equipment ? '- Edit Equipment' : '- Add Equipment'; 
$classifications = $classifications ?? \App\Helpers\Constants::EQUIPMENT_CLASSIFICATIONS;
?>
<?php ob_start(); ?>
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="/equipment" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 mb-2 inline-block">&larr; Back to Equipment Inventory</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $equipment ? 'Edit Equipment' : 'Add New Equipment' ?></h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5"><?= $equipment ? 'Update property code, specifications, and laboratory assignment' : 'Register equipment with Property Code, classification, and specifications' ?></p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-primary-50 text-primary-700 border border-primary-200">
                <?= $equipment ? 'Asset Tag: ' . escape($equipment['asset_tag']) : 'New Registration' ?>
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="<?= $equipment ? '/equipment/' . $equipment['id'] : '/equipment' ?>" class="p-6 sm:p-8 space-y-6">
            <?= csrf_field() ?>

            <!-- Property Identification -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4 text-primary-600"></i>
                    <span>Property Identification</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Property Code (Official)</label>
                        <input type="text" name="property_code" value="<?= escape($equipment['property_code'] ?? '') ?>" placeholder="e.g. PROP-2026-CL1-001" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <span class="text-[10px] text-gray-400 mt-1 block">Leave empty to auto-generate</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Asset Tag <span class="text-red-500">*</span></label>
                        <input type="text" name="asset_tag" value="<?= escape($equipment['asset_tag'] ?? '') ?>" required placeholder="e.g. EQ-SU-001" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Equipment Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?= escape($equipment['name'] ?? '') ?>" required placeholder="e.g. Dell OptiPlex 7090 Desktop" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <!-- Classification & Placement -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-indigo-600"></i>
                    <span>Classification & Assignment</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Classification <span class="text-red-500">*</span></label>
                        <select name="classification" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <?php foreach ($classifications as $clf): ?>
                                <option value="<?= escape($clf) ?>" <?= ($equipment['classification'] ?? 'ICT Equipment') === $clf ? 'selected' : '' ?>>
                                    <?= escape($clf) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($equipment['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= escape($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Assigned Laboratory <span class="text-red-500">*</span></label>
                        <select name="laboratory_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Laboratory</option>
                            <?php foreach ($laboratories as $lab): ?>
                                <option value="<?= $lab['id'] ?>" <?= ($equipment['laboratory_id'] ?? '') == $lab['id'] ? 'selected' : '' ?>>
                                    <?= escape($lab['name']) ?> (Room <?= escape($lab['room_number']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hardware Specifications -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <i data-lucide="cpu" class="w-4 h-4 text-purple-600"></i>
                    <span>Hardware Specifications</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Brand / Manufacturer</label>
                        <input type="text" name="brand" value="<?= escape($equipment['brand'] ?? '') ?>" placeholder="e.g. Dell, HP, Cisco, Epson" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Model / Part Name</label>
                        <input type="text" name="model" value="<?= escape($equipment['model'] ?? '') ?>" placeholder="e.g. OptiPlex 7090 / Core i7" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Serial Number</label>
                        <input type="text" name="serial_number" value="<?= escape($equipment['serial_number'] ?? '') ?>" placeholder="e.g. SN-990184-XYZ" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-mono text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <!-- Financial & Status -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>Procurement & Status</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Acquisition Date</label>
                        <input type="date" name="acquisition_date" value="<?= escape($equipment['acquisition_date'] ?? date('Y-m-d')) ?>" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Purchase Cost (PHP)</label>
                        <input type="number" step="0.01" name="purchase_cost" value="<?= escape($equipment['purchase_cost'] ?? '0.00') ?>" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Current Status</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <?php foreach (\App\Helpers\Constants::EQUIPMENT_STATUS as $st): ?>
                                <option value="<?= $st ?>" <?= ($equipment['status'] ?? 'active') === $st ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $st)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Notes & Maintenance Remarks</label>
                <textarea name="notes" rows="3" placeholder="Condition, location in lab, segment placement, warranty remarks..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500"><?= escape($equipment['notes'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="/equipment" class="px-5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium rounded-xl transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-sm transition-colors">
                    <?= $equipment ? 'Update Equipment Record' : 'Save Equipment & Generate QR' ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
