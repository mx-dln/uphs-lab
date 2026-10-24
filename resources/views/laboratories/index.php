<?php $title = '- Laboratories'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laboratories</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage laboratories and their resources</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/laboratories/print-qr" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print QR</span>
            </a>
            <a href="/laboratories/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Laboratory</span>
            </a>
        </div>
    </div>

    <form method="GET" class="mb-6">
        <div class="relative max-w-md">
            <i data-lucide="search" class="absolute left-3.5 top-3 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="<?= escape($search) ?>" placeholder="Search laboratories..." class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($laboratories as $lab): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <?php
                    $labText = strtolower(($lab['name'] ?? '') . ' ' . ($lab['description'] ?? ''));
                    $isComputerLab = preg_match('/computer|ict|information technology|programming|network|multimedia|software|hardware/', $labText);
                    $isMedicalLab = preg_match('/medicine|medical|pharmacy|pharma|nursing|biology|biomedical/', $labText);
                    $labIcon = $isComputerLab ? 'monitor' : ($isMedicalLab ? 'pill' : 'flask-conical');
                    $labIconColor = $isComputerLab
                        ? 'text-blue-600 dark:text-blue-400'
                        : ($isMedicalLab ? 'text-rose-600 dark:text-rose-400' : 'text-primary-600 dark:text-primary-400');
                ?>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="<?= $labIcon ?>" class="w-5 h-5 <?= $labIconColor ?>"></i>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full <?= $lab['status'] === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' ?>">
                        <?= ucfirst($lab['status']) ?>
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?= escape($lab['name']) ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Room <?= escape($lab['room_number']) ?><?= $lab['building'] ? ' - ' . escape($lab['building']) : '' ?></p>
                <?php if ($lab['capacity']): ?><p class="text-sm text-gray-500 dark:text-gray-400">Capacity: <?= (int)$lab['capacity'] ?></p><?php endif; ?>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <a href="/laboratories/<?= $lab['id'] ?>" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">View Details</a>
                <div class="flex items-center space-x-2">
                    <a href="/laboratories/<?= $lab['id'] ?>/edit" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i data-lucide="edit" class="w-4 h-4"></i></a>
                    <button onclick="showLabQR('/<?= $lab['qr_code'] ?>', '<?= escape($lab['name']) ?>', 'Room <?= escape($lab['room_number']) ?>')" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-transparent border-0 cursor-pointer"><i data-lucide="qrcode" class="w-4 h-4"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($laboratories)): ?>
        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">No laboratories found.</div>
        <?php endif; ?>
    </div>
</div>
<!-- QR Modal -->
<div id="labQRModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="closeLabQR()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h4 class="font-semibold text-gray-900 dark:text-white">QR Code</h4>
            <button onclick="closeLabQR()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 text-center">
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 inline-block mb-4">
                <img id="labQRImg" src="" alt="QR" class="w-48 h-48 mx-auto">
                <p id="labQRName" class="text-sm font-bold text-gray-900 dark:text-white mt-3"></p>
                <p id="labQRDesc" class="text-xs text-gray-500 dark:text-gray-400"></p>
            </div>
            <div class="flex items-center justify-center space-x-3">
                <a id="labQRDownload" href="#" download class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-medium">Download</a>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium">Print</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showLabQR(qrPath, name, desc) {
        document.getElementById('labQRImg').src = qrPath;
        document.getElementById('labQRName').textContent = name;
        document.getElementById('labQRDesc').textContent = desc;
        document.getElementById('labQRDownload').href = qrPath;
        document.getElementById('labQRModal').classList.remove('hidden');
    }
    function closeLabQR() {
        document.getElementById('labQRModal').classList.add('hidden');
    }
</script>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
