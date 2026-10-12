<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print All Laboratory QR Codes</title>
    <script src="/assets/js/tailwind.min.js"></script>
    <style>
        @media print {
            @page { margin: 0.5in; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .break-page { page-break-after: always; }
        }
        .qr-label { font-size: 10px; }
    </style>
</head>
<body class="p-4">
    <div class="no-print text-center mb-6">
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-medium">Print All QR Codes</button>
        <p class="text-xs text-gray-500 mt-2"><?= count($laboratories) ?> laboratories</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <?php foreach ($laboratories as $lab): ?>
        <div class="border border-gray-200 rounded-lg p-3 text-center <?= $loop->even ?? '' ?>">
            <?php if ($lab['qr_code'] && file_exists(dirname(__DIR__, 3) . '/' . $lab['qr_code'])): ?>
                <img src="/<?= $lab['qr_code'] ?>" alt="QR" class="w-32 h-32 mx-auto">
            <?php else: ?>
                <div class="w-32 h-32 bg-gray-100 mx-auto flex items-center justify-center text-xs text-gray-400">No QR</div>
            <?php endif; ?>
            <p class="text-xs font-bold text-gray-900 mt-2"><?= escape($lab['name']) ?></p>
            <p class="text-xs text-gray-500">Room <?= escape($lab['room_number']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
