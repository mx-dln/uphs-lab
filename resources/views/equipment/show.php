<?php $title = '- ' . escape($equipment['name']); ?>
<?php ob_start(); ?>
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="/equipment" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Equipment Inventory</a>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight"><?= escape($equipment['name']) ?></h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">
                Official Property Code: <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400"><?= escape($equipment['property_code'] ?? 'N/A') ?></span> &middot; 
                Asset Tag: <span class="font-mono"><?= escape($equipment['asset_tag']) ?></span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Property Tag Card PDF -->
            <a href="/equipment/<?= $equipment['id'] ?>/property-card-pdf" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-xl text-xs font-semibold">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Print Property Tag PDF</span>
            </a>
            <button onclick="openDispatchModal()" class="inline-flex items-center space-x-1.5 px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 rounded-xl text-xs font-semibold">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Dispatch Equipment</span>
            </button>
            <a href="/equipment/<?= $equipment['id'] ?>/qr" class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold hover:bg-gray-50">
                View QR
            </a>
            <a href="/equipment/<?= $equipment['id'] ?>/edit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold">
                Edit
            </a>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">Property Details & Specifications</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Property Code</dt>
                <dd class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= escape($equipment['property_code'] ?? 'N/A') ?></dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Asset Tag</dt>
                <dd class="text-sm font-mono font-bold text-gray-900 dark:text-white mt-1"><?= escape($equipment['asset_tag']) ?></dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Classification</dt>
                <dd class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">
                    <span class="px-2.5 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                        <?= escape($equipment['classification'] ?? 'ICT Equipment') ?>
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Category</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-1"><?= escape($equipment['category_name'] ?? '') ?></dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Assigned Room</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-1"><?= escape($equipment['laboratory_name'] ?? '') ?> (Room <?= escape($equipment['room_number'] ?? '') ?>)</dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Status</dt>
                <dd class="text-sm mt-1">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                        <?= ucfirst(str_replace('_', ' ', $equipment['status'])) ?>
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Brand & Model</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white mt-1"><?= escape(($equipment['brand'] ?? 'N/A') . ' ' . ($equipment['model'] ?? '')) ?></dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Serial Number</dt>
                <dd class="text-sm font-mono font-medium text-gray-900 dark:text-white mt-1"><?= escape($equipment['serial_number'] ?? 'N/A') ?></dd>
            </div>
            <div>
                <dt class="text-gray-400 font-semibold uppercase tracking-wider">Purchase Cost</dt>
                <dd class="text-sm font-bold text-gray-900 dark:text-white mt-1"><?= $equipment['purchase_cost'] ? 'PHP ' . number_format((float)$equipment['purchase_cost'], 2) : 'PHP 0.00' ?></dd>
            </div>
        </dl>

        <?php if ($equipment['notes']): ?>
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Remarks & Notes</h4>
            <p class="text-xs text-gray-700 dark:text-gray-300"><?= escape($equipment['notes']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Equipment Services & Maintenance Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="wrench" class="w-5 h-5 text-amber-600"></i>
                    <span>Services & Maintenance History</span>
                </h3>
                <p class="text-xs text-gray-500">Service logs, routine calibrations, repairs, and technical maintenance</p>
            </div>
            <a href="/equipment-services/create?equipment_id=<?= $equipment['id'] ?>" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded-xl text-xs font-bold transition-colors">
                + Log New Service
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-4 py-2.5 rounded-l-xl">Service Date</th>
                        <th class="px-4 py-2.5">Type</th>
                        <th class="px-4 py-2.5">Technician / Dept</th>
                        <th class="px-4 py-2.5">Cost</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5">Next Service</th>
                        <th class="px-4 py-2.5 rounded-r-xl">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($services)): ?>
                        <tr><td colspan="7" class="py-6 text-center text-gray-400">No service history recorded for this equipment.</td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $srv): ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?= escape($srv['service_date']) ?></td>
                            <td class="px-4 py-3 font-medium text-amber-600"><?= escape($srv['service_type']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($srv['technician_name'] ?? 'In-House') ?></td>
                            <td class="px-4 py-3 font-mono">PHP <?= number_format((float)$srv['cost'], 2) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                                    <?= ucfirst($srv['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500"><?= escape($srv['next_service_date'] ?? 'N/A') ?></td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate"><?= escape($srv['notes'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dispatch & Transfers History -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="arrow-left-right" class="w-5 h-5 text-indigo-600"></i>
                    <span>Equipment Dispatch & Segment Allocation</span>
                </h3>
                <p class="text-xs text-gray-500">Record of dispatches to specific lab segments, departments, or faculty</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-4 py-2.5 rounded-l-xl">Dispatch Date</th>
                        <th class="px-4 py-2.5">Target Lab / Segment</th>
                        <th class="px-4 py-2.5">Assigned To</th>
                        <th class="px-4 py-2.5">Return Due</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($dispatches)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No dispatch records for this equipment. Currently in assigned laboratory.</td></tr>
                    <?php else: ?>
                        <?php foreach ($dispatches as $dsp): ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-semibold"><?= escape($dsp['dispatch_date']) ?></td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                <?= escape($dsp['to_lab_name'] ?? '') ?> 
                                <?php if (!empty($dsp['segment'])): ?>
                                    &middot; <span class="text-primary-600 font-semibold"><?= escape($dsp['segment']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600"><?= escape($dsp['user_name'] ?? 'Lab Segment') ?></td>
                            <td class="px-4 py-3 text-gray-500"><?= escape($dsp['return_due_date'] ?? 'Permanent') ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $dsp['status'] === 'returned' ? 'bg-gray-100 text-gray-700' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= ucfirst($dsp['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if ($dsp['status'] === 'dispatched'): ?>
                                <form method="POST" action="/equipment/dispatch/<?= $dsp['id'] ?>/return" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-[10px] font-semibold">Mark Returned</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dispatch Equipment Modal -->
<div id="dispatchModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200 dark:border-gray-700" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Dispatch Equipment</h3>
            <button onclick="closeDispatchModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="/equipment/<?= $equipment['id'] ?>/dispatch" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1">Target Laboratory</label>
                <select name="to_lab_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs">
                    <option value="">Same Laboratory / Segment</option>
                    <?php foreach ($activeLaboratories as $lab): ?>
                        <option value="<?= $lab['id'] ?>"><?= escape($lab['name']) ?> (Room <?= escape($lab['room_number']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1">Laboratory Segment / Bay</label>
                <input type="text" name="segment" placeholder="e.g. Cisco Bay Segment A, Multimedia Station 2" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1">Purpose / Course Session</label>
                <textarea name="purpose" rows="2" required placeholder="Reason for dispatch, lab class, faculty reservation..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400 mb-1">Expected Return Due Date</label>
                <input type="date" name="return_due_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs">
            </div>
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeDispatchModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm">Confirm Dispatch</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDispatchModal() { document.getElementById('dispatchModal').classList.remove('hidden'); }
    function closeDispatchModal() { document.getElementById('dispatchModal').classList.add('hidden'); }
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
