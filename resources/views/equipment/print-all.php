<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print All Equipment QR Codes</title>
    <script src="/assets/js/tailwind.min.js"></script>
    <style>
        @media print {
            @page { margin: 0.3in; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-4">
    <div class="no-print text-center mb-6">
        <div class="flex items-center justify-center space-x-3">
            <select id="labFilter" class="px-4 py-2 border border-gray-300 rounded-xl text-sm">
                <option value="">All Laboratories</option>
                <?php foreach ($laboratories as $lab): ?>
                <option value="<?= $lab['id'] ?>" <?= (string)$labId === (string)$lab['id'] ? 'selected' : '' ?>><?= escape($lab['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium">Print</button>
        </div>
        <p class="text-xs text-gray-500 mt-2"><?= count($equipment) ?> equipment</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        <?php foreach ($equipment as $eq): ?>
        <div class="border border-gray-200 rounded-lg p-2 text-center">
            <?php if ($eq['qr_code'] && file_exists(dirname(__DIR__, 3) . '/' . $eq['qr_code'])): ?>
                <img src="/<?= $eq['qr_code'] ?>" alt="QR" class="w-28 h-28 mx-auto">
            <?php else: ?>
                <div class="w-28 h-28 bg-gray-100 mx-auto flex items-center justify-center text-xs text-gray-400">No QR</div>
            <?php endif; ?>
            <p class="text-[10px] font-bold text-gray-900 mt-1 leading-tight"><?= escape($eq['name']) ?></p>
            <p class="text-[9px] text-gray-500"><?= escape($eq['asset_tag']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.getElementById('labFilter')?.addEventListener('change', function() {
            window.location.href = '/equipment/print-qr?laboratory_id=' + this.value;
        });
    </script>
</body>
</html>
