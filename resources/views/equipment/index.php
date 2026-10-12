<?php 
$title = '- Equipment Inventory'; 
$classifications = $classifications ?? \App\Helpers\Constants::EQUIPMENT_CLASSIFICATIONS;
?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Equipment Inventory</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Manage laboratory hardware, Property Codes, classifications, and dispatches</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Choice File Import Button (Admin & Custodian only) -->
            <?php if (isAdmin() || isCustodian()): ?>
            <button onclick="openImportModal()" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all">
                <i data-lucide="file-up" class="w-4 h-4 text-emerald-600"></i>
                <span>Import Choice File</span>
            </button>
            <?php endif; ?>

            <!-- Export to PDF Button -->
            <a href="/equipment/export-pdf<?= !empty($labId) ? '?laboratory_id=' . $labId : '' ?>" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all">
                <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i>
                <span>Export Property PDF</span>
            </a>

            <!-- Print QR -->
            <a href="/equipment/print-qr" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all">
                <i data-lucide="printer" class="w-4 h-4 text-blue-500"></i>
                <span>Print QR Codes</span>
            </a>

            <!-- Add Equipment (Admin & Custodian only; hidden from Faculty & HR) -->
            <?php if (isAdmin() || isCustodian()): ?>
            <a href="/equipment/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                <span>Add Equipment</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- HR & New Equipment Quick Filter Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-gray-800 p-2 rounded-2xl border border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-1 text-xs font-medium">
            <a href="/equipment" class="px-3.5 py-2 rounded-xl transition-all <?= ($filter !== 'new') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                All Equipment Items
            </a>
            <a href="/equipment?filter=new" class="px-3.5 py-2 rounded-xl transition-all flex items-center space-x-1.5 <?= ($filter === 'new') ? 'bg-indigo-600 text-white shadow-sm' : 'text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30' ?>">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>⚡ New Equipment (Last 30 Days)</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold <?= ($filter === 'new') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' ?>">
                    <?= $newEquipmentCount ?? 0 ?>
                </span>
            </a>
        </div>
        <?php if ($filter === 'new' || isHR()): ?>
            <div class="text-xs text-indigo-700 dark:text-indigo-300 font-medium px-3 py-1 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl border border-indigo-200 dark:border-indigo-800 flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-indigo-600"></i>
                <span>HR Asset Watch: <?= $newEquipmentCount ?? 0 ?> new asset(s) ready for registration.</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Search & Filter Options Form -->
    <form method="GET" action="/equipment" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <?php if ($filter === 'new'): ?>
            <input type="hidden" name="filter" value="new">
        <?php endif; ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <i data-lucide="search" class="absolute left-3.5 top-3 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="<?= escape($search) ?>" placeholder="Search name, Property Code, Asset Tag, Serial..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <select name="classification" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-primary-500">
                    <option value="">All Classifications</option>
                    <?php foreach ($classifications as $clf): ?>
                        <option value="<?= escape($clf) ?>" <?= ($classification === $clf) ? 'selected' : '' ?>><?= escape($clf) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="laboratory_id" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-primary-500">
                    <option value="">All Laboratories</option>
                    <?php foreach ($laboratories as $lab): ?>
                        <option value="<?= $lab['id'] ?>" <?= ((string)$labId === (string)$lab['id']) ? 'selected' : '' ?>><?= escape($lab['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <select name="status" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-xs focus:ring-2 focus:ring-primary-500">
                    <option value="">All Status</option>
                    <?php foreach (\App\Helpers\Constants::EQUIPMENT_STATUS as $st): ?>
                        <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $st)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Equipment List Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-5 py-3.5 rounded-l-2xl">Property Code</th>
                        <th class="px-4 py-3.5">Asset Tag</th>
                        <th class="px-4 py-3.5">Item Name & Model</th>
                        <th class="px-4 py-3.5">Classification</th>
                        <th class="px-4 py-3.5">Laboratory</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 rounded-r-2xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($equipment as $eq): ?>
                    <?php 
                    $isRecent = (strtotime($eq['acquisition_date'] ?? $eq['created_at']) >= strtotime('-30 days'));
                    ?>
                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            <?= escape($eq['property_code'] ?? 'PROP-N/A') ?>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-gray-600 dark:text-gray-300">
                            <?= escape($eq['asset_tag']) ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center">
                                <a href="/equipment/<?= $eq['id'] ?>" class="font-bold text-gray-900 dark:text-white hover:text-primary-600 transition-colors">
                                    <?= escape($eq['name']) ?>
                                </a>
                                <?php if ($isRecent): ?>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300 ml-1.5">
                                        <i data-lucide="sparkles" class="w-2.5 h-2.5 mr-0.5 text-amber-600"></i>NEW
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[11px] text-gray-400">
                                <?= escape($eq['brand'] ? $eq['brand'] . ' ' . $eq['model'] : $eq['category_name']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                <?= escape($eq['classification'] ?? 'ICT Equipment') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300 font-medium">
                            <?= escape($eq['laboratory_name'] ?? '') ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full 
                                <?php switch($eq['status']): 
                                    case 'active': ?>bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400<?php break; 
                                    case 'missing': ?>bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400<?php break; 
                                    case 'defective': case 'under_repair': ?>bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400<?php break; 
                                    case 'borrowed': ?>bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400<?php break; 
                                    default: ?>bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300<?php endswitch; ?>">
                                <?= ucfirst(str_replace('_', ' ', $eq['status'])) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="/equipment/<?= $eq['id'] ?>" class="text-primary-600 hover:underline font-semibold">View</a>
                            <a href="/equipment/<?= $eq['id'] ?>/property-card-pdf" target="_blank" title="Print Property Tag Card" class="text-indigo-600 hover:underline font-semibold">Tag PDF</a>
                            <?php if (isAdmin() || isCustodian()): ?>
                                <a href="/equipment/<?= $eq['id'] ?>/edit" class="text-gray-500 hover:underline">Edit</a>
                            <?php endif; ?>
                            <button onclick="showQR('/<?= $eq['qr_code'] ?>', '<?= escape($eq['name']) ?>', '<?= escape($eq['property_code'] ?? $eq['asset_tag']) ?>')" class="text-gray-500 hover:text-gray-900 bg-transparent border-0 cursor-pointer">QR</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($equipment)): ?>
                <div class="text-center py-12 text-gray-400">No equipment items found matching your query.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Import Choice File Modal ("dispatchImport choice file") -->
<div id="importModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200 dark:border-gray-700" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="file-up" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Import Choice File</h3>
                    <p class="text-xs text-gray-500">Upload CSV or Excel spreadsheet of equipment</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="/equipment/import-file" enctype="multipart/form-data" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl text-center bg-gray-50 dark:bg-gray-700/30">
                <i data-lucide="upload-cloud" class="w-10 h-10 text-emerald-600 mx-auto mb-2"></i>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Choose file (.csv, .xlsx, .xls)
                </label>
                <input type="file" name="choice_file" accept=".csv,.xlsx,.xls" required class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
            </div>

            <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">Expected Column Order:</p>
                <p>1. Asset Tag &middot; 2. Property Code &middot; 3. Name &middot; 4. Category &middot; 5. Classification &middot; 6. Laboratory &middot; 7. Brand &middot; 8. Model &middot; 9. Serial &middot; 10. Cost &middot; 11. Status</p>
            </div>

            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm">
                    Upload & Import Equipment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- QR Modal -->
<div id="qrModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeQR()">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden border border-gray-200 dark:border-gray-700" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h4 class="font-bold text-gray-900 dark:text-white text-sm">Equipment QR Code</h4>
            <button onclick="closeQR()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 text-center">
            <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-2xl p-4 inline-block mb-4 bg-gray-50 dark:bg-gray-700/30">
                <img id="qrModalImg" src="" alt="QR" class="w-44 h-44 mx-auto">
                <p id="qrModalName" class="text-sm font-bold text-gray-900 dark:text-white mt-3"></p>
                <p id="qrModalTag" class="text-xs font-mono text-gray-500 dark:text-gray-400"></p>
            </div>
            <div class="flex items-center justify-center space-x-2">
                <a id="qrModalDownload" href="#" download class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm">Download</a>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold">Print</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openImportModal() { document.getElementById('importModal').classList.remove('hidden'); }
    function closeImportModal() { document.getElementById('importModal').classList.add('hidden'); }

    let currentQRPath = '';
    function showQR(qrPath, name, tag) {
        currentQRPath = qrPath;
        document.getElementById('qrModalImg').src = qrPath;
        document.getElementById('qrModalName').textContent = name;
        document.getElementById('qrModalTag').textContent = tag;
        document.getElementById('qrModalDownload').href = qrPath;
        document.getElementById('qrModal').classList.remove('hidden');
    }
    function closeQR() { document.getElementById('qrModal').classList.add('hidden'); }
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
