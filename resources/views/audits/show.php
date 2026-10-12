<?php $title = '- Audit - ' . escape($audit['laboratory_name']); ?>
<?php ob_start(); ?>
<script src="/assets/js/html5-qrcode.min.js"></script>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="/audits" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-2 inline-block">&larr; Back to Audits</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Results</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1"><?= escape($audit['laboratory_name'] ?? '') ?> - <?= escape($audit['audit_date']) ?></p>
        </div>
        <?php if ($audit['status'] === 'in_progress'): ?>
        <div class="flex items-center space-x-3">
            <form method="POST" action="/audits/<?= $audit['id'] ?>/complete" onsubmit="return confirmComplete(this)">
                <?= csrf_field() ?>
                <button type="submit" id="completeBtn" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium flex items-center space-x-1.5">
                    <span id="completeBtnText">Complete Audit</span>
                    <svg id="completeBtnSpinner" class="hidden animate-spin w-4 h-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <?php
        $total = count($audit['items'] ?? []);
        $present = count(array_filter($audit['items'] ?? [], fn($i) => $i['status'] === 'present'));
        $missing = count(array_filter($audit['items'] ?? [], fn($i) => $i['status'] === 'missing'));
        $defective = count(array_filter($audit['items'] ?? [], fn($i) => $i['status'] === 'defective'));
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500">Completion</p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?= $audit['completion_percentage'] ?>%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500">Present</p>
            <p class="text-2xl font-bold text-green-600 mt-1"><?= $present ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500">Missing</p>
            <p class="text-2xl font-bold text-red-600 mt-1"><?= $missing ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500">Defective</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1"><?= $defective ?></p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Items</h3>
                <?php if ($audit['status'] === 'in_progress'): ?>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" id="scanEquipInput" placeholder="Asset tag" class="flex-1 min-w-[140px] px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                    <button onclick="lookupEquipment()" id="findBtn" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm whitespace-nowrap flex items-center space-x-1.5">
                        <span id="findBtnText">Find</span>
                        <svg id="findBtnSpinner" class="hidden animate-spin w-4 h-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </button>
                    <button type="button" onclick="openScannerModal()" class="inline-flex items-center space-x-1.5 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        <span id="scannerBtnText">Scan QR</span>
                    </button>
                </div>
                <!-- QR Scanner Modal -->
                <div id="qrScannerModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Scan Equipment QR</h4>
                            <button onclick="closeScannerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    <div class="p-4">
                        <div id="qr-reader" class="bg-black rounded-xl overflow-hidden aspect-square"></div>
                        <div id="scanningIndicator" class="hidden flex items-center justify-center space-x-2 mt-3 text-xs text-primary-600">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span>Scanning...</span>
                        </div>
                        <p id="scannerHint" class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">Point camera at equipment QR sticker</p>
                    </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="auditTable" class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Tag</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scanned At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($audit['items'] as $item): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 <?= $item['status'] === 'present' ? 'bg-green-50 dark:bg-green-900/10' : '' ?>">
                        <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-white"><button data-eqid="<?= $item['equipment_id'] ?>" style="display:none"></button><?= escape($item['asset_tag'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?= escape($item['equipment_name'] ?? '') ?></td>
                        <td class="px-6 py-4 eq-status">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $item['status'] === 'present' ? 'bg-green-100 text-green-700' : ($item['status'] === 'missing' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 eq-scanned"><?= $item['scanned_at'] ? date('M d, Y h:i A', strtotime($item['scanned_at'])) : 'Not scanned' ?></td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($item['status'] === 'missing'): ?>
                            <form method="POST" action="/investigations" class="inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="equipment_id" value="<?= $item['equipment_id'] ?>">
                                <input type="hidden" name="audit_id" value="<?= $audit['id'] ?>">
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg">Investigate</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($audit['status'] === 'in_progress'): ?>
<script>
let html5QrCode = null;
let scannerActive = false;
let scannerTimeout = null;
let scanningInProgress = false;

function openScannerModal() {
    document.getElementById('qrScannerModal').classList.remove('hidden');
    document.getElementById('scannerBtnText').textContent = 'Scanning...';
    document.getElementById('scannerHint').textContent = 'Starting camera...';
    startScanner();
}

function closeScannerModal() {
    if (scannerTimeout) clearTimeout(scannerTimeout);
    document.getElementById('qrScannerModal').classList.add('hidden');
    document.getElementById('scannerBtnText').textContent = 'Scan QR';
    document.getElementById('scannerHint').textContent = 'Point camera at equipment QR sticker';
    document.getElementById('scanningIndicator').classList.add('hidden');
    stopScanner();
}

function toggleScanner() { openScannerModal(); }

function startScanner() {
    if (scannerActive) return;
    scannerActive = true;
    document.getElementById('scanningIndicator').classList.remove('hidden');
    document.getElementById('scannerHint').textContent = 'Camera active, point at QR code';

    // Auto-close scanner after 60 seconds
    if (scannerTimeout) clearTimeout(scannerTimeout);
    scannerTimeout = setTimeout(() => {
        closeScannerModal();
        showToast('Scanner timed out. Open again to continue.', 'red');
    }, 300000);

    html5QrCode = new Html5Qrcode("qr-reader");
    Html5Qrcode.getCameras().then(devices => {
        const backCam = devices.find(d =>
            d.label.toLowerCase().includes('back') ||
            d.label.toLowerCase().includes('environment') ||
            d.label.toLowerCase().includes('rear')
        );
        const cameraId = backCam ? backCam.id : devices[0]?.id;

        if (cameraId) {
            html5QrCode.start(
                { deviceId: cameraId },
                { fps: 10, qrbox: { width: 200, height: 200 } },
                onScanSuccess,
                () => {}
            ).catch(() => fallbackScan());
        } else {
            fallbackScan();
        }
    }).catch(() => fallbackScan());
}

function fallbackScan() {
    html5QrCode.start(
        { facingMode: { exact: "environment" } },
        { fps: 10, qrbox: { width: 200, height: 200 } },
        onScanSuccess,
        () => {}
    ).catch(() => {
        scannerActive = false;
    });
}

function onScanSuccess(decodedText) {
    if (scanningInProgress) return;
    scanningInProgress = true;
    document.getElementById('scannerHint').textContent = 'Processing...';

    const match = decodedText.match(/\/scan\/equipment\/(\d+)/);
    const assetTagMatch = decodedText.match(/\/equipment\/(\d+)/);
    const equipmentId = match ? match[1] : (assetTagMatch ? assetTagMatch[1] : null);

    if (equipmentId) {
        fetch('/audits/<?= $audit['id'] ?>/scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'equipment_id=' + equipmentId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('✓ Equipment scanned!', 'green');
                // Update table row without reload
                const rows = document.querySelectorAll('#auditTable tbody tr');
                rows.forEach(row => {
                    const btn = row.querySelector('[data-eqid="' + equipmentId + '"]');
                    if (btn) {
                        row.classList.add('bg-green-50', 'dark:bg-green-900/10');
                        row.querySelector('.eq-status').innerHTML = '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Present</span>';
                        row.querySelector('.eq-scanned').textContent = '<?= now('h:i A') ?>';
                    }
                });
                document.getElementById('scannerHint').textContent = 'Ready for next scan...';
                setTimeout(() => {
                    scanningInProgress = false;
                    startScanner();
                }, 3000);
            } else {
                showToast(data.message || 'Scan failed', 'red');
                scanningInProgress = false;
                setTimeout(() => startScanner(), 1500);
            }
        })
        .catch(() => {
            showToast('Connection error', 'red');
            scanningInProgress = false;
            setTimeout(() => startScanner(), 1500);
        });
    } else {
        document.getElementById('scanEquipInput').value = decodedText;
        scanningInProgress = false;
        lookupEquipment();
    }
}

function stopScanner() {
    if (html5QrCode) {
        try {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
                scannerActive = false;
            });
        } catch (e) {
            html5QrCode = null;
            scannerActive = false;
        }
    }
}

function lookupEquipment() {
    const input = document.getElementById('scanEquipInput');
    const value = input.value.trim();
    if (!value) return;

    const btn = document.getElementById('findBtn');
    const btnText = document.getElementById('findBtnText');
    const spinner = document.getElementById('findBtnSpinner');
    btn.disabled = true;
    btnText.textContent = 'Finding...';
    spinner.classList.remove('hidden');

    let foundId = null;
    fetch('/api/equipment/find', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'query=' + encodeURIComponent(value)
    })
    .then(r => r.json())
    .then(data => {
        if (data.id) {
            foundId = data.id;
            return fetch('/audits/<?= $audit['id'] ?>/scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'equipment_id=' + data.id
            });
        }
        throw new Error('Equipment not found');
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('✓ Equipment scanned!', 'green');
            updateRow(foundId);
            document.getElementById('scanEquipInput').value = '';
        } else {
            showToast(data.message || 'Scan failed', 'red');
        }
        btn.disabled = false;
        btnText.textContent = 'Find';
        spinner.classList.add('hidden');
    })
    .catch(e => {
        showToast(e.message, 'red');
        btn.disabled = false;
        btnText.textContent = 'Find';
        spinner.classList.add('hidden');
    });
}

function updateRow(eqId) {
    const rows = document.querySelectorAll('#auditTable tbody tr');
    rows.forEach(row => {
        const btn = row.querySelector('[data-eqid="' + eqId + '"]');
        if (btn) {
            row.classList.add('bg-green-50', 'dark:bg-green-900/10');
            row.querySelector('.eq-status').innerHTML = '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Present</span>';
            row.querySelector('.eq-scanned').textContent = '<?= now('h:i A') ?>';
        }
    });
}

function confirmComplete(form) {
    if (!confirm('Complete this audit? Missing items will be marked as missing.')) return false;
    const btn = document.getElementById('completeBtn');
    const btnText = document.getElementById('completeBtnText');
    const spinner = document.getElementById('completeBtnSpinner');
    btn.disabled = true;
    btnText.textContent = 'Completing...';
    spinner.classList.remove('hidden');
    return true;
}

function showToast(msg, type) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-[100] animate-slide-in';
    toast.innerHTML = '<div class="bg-white dark:bg-gray-800 border-l-4 ' + (type === 'green' ? 'border-green-500' : 'border-red-500') + ' rounded-xl shadow-lg p-4 pr-12 relative max-w-md"><div class="flex items-center space-x-3"><div class="w-8 h-8 ' + (type === 'green' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') + ' rounded-lg flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + (type === 'green' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12') + '"/></svg></div><p class="text-sm font-medium text-gray-900 dark:text-white">' + msg + '</p></div></div>';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100px)'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
