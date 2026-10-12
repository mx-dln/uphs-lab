<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/Helpers/Functions.php';

use App\Services\QRService;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../../config/database.php';
$db = $config['connections']['mysql'];

function getCategoryId($pdo, $name) {
    $stmt = $pdo->prepare("SELECT id FROM equipment_categories WHERE name = ?");
    $stmt->execute([$name]);
    return (int)$stmt->fetchColumn();
}

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}",
        $db['username'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Seeding laboratories and equipment...\n\n";

    // ========== LABORATORIES ==========
    $laboratories = [
        ['Computer Laboratory 1', 'CL-101', 'Main Building', 40, 'General purpose computer lab with 40 workstations'],
        ['Computer Laboratory 2', 'CL-102', 'Main Building', 35, 'Programming and development lab'],
        ['Computer Laboratory 3', 'CL-103', 'Main Building', 30, 'Multimedia and design lab'],
        ['Network Laboratory', 'NL-201', 'IT Building', 25, 'Networking and server configuration lab'],
        ['Engineering Laboratory', 'EL-301', 'Engineering Building', 30, 'Engineering design and simulation lab'],
        ['Science Laboratory', 'SL-401', 'Science Building', 35, 'Computer science research lab'],
    ];

    $checkLab = $pdo->prepare("SELECT COUNT(*) FROM laboratories WHERE room_number = ?");
    $insertLab = $pdo->prepare("INSERT INTO laboratories (name, room_number, building, capacity, description, status) VALUES (?, ?, ?, ?, ?, 'active')");
    $getLabId = $pdo->prepare("SELECT id FROM laboratories WHERE room_number = ?");

    $qrService = new QRService();
    $labIds = [];

    echo "--- Laboratories ---\n";
    foreach ($laboratories as $lab) {
        $checkLab->execute([$lab[1]]);
        if ($checkLab->fetchColumn() == 0) {
            $insertLab->execute($lab);
            $labId = (int)$pdo->lastInsertId();
            $labIds[$lab[1]] = $labId;
            $qrPath = $qrService->generateLaboratoryQR($labId, $lab[0]);
            $pdo->prepare("UPDATE laboratories SET qr_code = ? WHERE id = ?")->execute([$qrPath, $labId]);
            echo "  Created: {$lab[0]} ({$lab[1]})\n";
        } else {
            $getLabId->execute([$lab[1]]);
            $row = $getLabId->fetch();
            $labIds[$lab[1]] = (int)$row['id'];
            echo "  Exists: {$lab[0]} ({$lab[1]})\n";
        }
    }

    // ========== EQUIPMENT ==========
    $equipmentList = [
        ['SU-001', 'Dell Optiplex 5090', 'System Unit', 'Dell', 'Optiplex 5090', 'SN-DELL-001', 'CL-101', '2024-06-01', 45000, 'active', 'Core i7, 16GB RAM, 512GB SSD'],
        ['SU-002', 'Dell Optiplex 5090', 'System Unit', 'Dell', 'Optiplex 5090', 'SN-DELL-002', 'CL-101', '2024-06-01', 45000, 'active', 'Core i7, 16GB RAM, 512GB SSD'],
        ['SU-003', 'Dell Optiplex 5090', 'System Unit', 'Dell', 'Optiplex 5090', 'SN-DELL-003', 'CL-101', '2024-06-01', 45000, 'active', 'Core i7, 16GB RAM, 512GB SSD'],
        ['SU-004', 'HP ProDesk 400', 'System Unit', 'HP', 'ProDesk 400', 'SN-HP-001', 'CL-102', '2024-06-15', 42000, 'active', 'Core i5, 8GB RAM, 256GB SSD'],
        ['SU-005', 'HP ProDesk 400', 'System Unit', 'HP', 'ProDesk 400', 'SN-HP-002', 'CL-102', '2024-06-15', 42000, 'defective', 'Core i5, 8GB RAM - PSU issue'],
        ['SU-006', 'Lenovo ThinkCentre', 'System Unit', 'Lenovo', 'ThinkCentre M70q', 'SN-LEN-001', 'CL-103', '2024-07-01', 48000, 'active', 'Core i7, 16GB RAM'],
        ['MON-001', 'Dell 24" Monitor', 'Monitor', 'Dell', 'P2422H', 'SN-MON-D01', 'CL-101', '2024-06-01', 12000, 'active', '1920x1080 IPS'],
        ['MON-002', 'Dell 24" Monitor', 'Monitor', 'Dell', 'P2422H', 'SN-MON-D02', 'CL-101', '2024-06-01', 12000, 'active', ''],
        ['MON-003', 'Dell 24" Monitor', 'Monitor', 'Dell', 'P2422H', 'SN-MON-D03', 'CL-101', '2024-06-01', 12000, 'active', ''],
        ['MON-004', 'HP 22" Monitor', 'Monitor', 'HP', 'V22', 'SN-MON-H01', 'CL-102', '2024-06-15', 8000, 'active', ''],
        ['MON-005', 'HP 22" Monitor', 'Monitor', 'HP', 'V22', 'SN-MON-H02', 'CL-102', '2024-06-15', 8000, 'missing', ''],
        ['KBD-001', 'Logitech K120', 'Keyboard', 'Logitech', 'K120', 'SN-KBD-001', 'CL-101', '2024-06-01', 450, 'active', 'USB Wired'],
        ['KBD-002', 'Logitech K120', 'Keyboard', 'Logitech', 'K120', 'SN-KBD-002', 'CL-101', '2024-06-01', 450, 'active', ''],
        ['KBD-003', 'Logitech K120', 'Keyboard', 'Logitech', 'K120', 'SN-KBD-003', 'CL-101', '2024-06-01', 450, 'active', ''],
        ['KBD-004', 'HP Business Keyboard', 'Keyboard', 'HP', 'Business Slim', 'SN-KBD-H01', 'CL-102', '2024-06-15', 550, 'active', ''],
        ['MS-001', 'Logitech M100', 'Mouse', 'Logitech', 'M100', 'SN-MS-001', 'CL-101', '2024-06-01', 350, 'active', 'USB Optical'],
        ['MS-002', 'Logitech M100', 'Mouse', 'Logitech', 'M100', 'SN-MS-002', 'CL-101', '2024-06-01', 350, 'active', ''],
        ['MS-003', 'Logitech M100', 'Mouse', 'Logitech', 'M100', 'SN-MS-003', 'CL-101', '2024-06-01', 350, 'active', ''],
        ['MS-004', 'HP Optical Mouse', 'Mouse', 'HP', 'Optical', 'SN-MS-H01', 'CL-102', '2024-06-15', 400, 'active', ''],
        ['PRJ-001', 'Epson Projector', 'Projector', 'Epson', 'EB-X50', 'SN-PRJ-001', 'CL-101', '2024-06-01', 35000, 'active', 'HDMI Input'],
        ['PRJ-002', 'Epson Projector', 'Projector', 'Epson', 'EB-X50', 'SN-PRJ-002', 'CL-102', '2024-06-15', 35000, 'under_repair', 'Bulb replacement needed'],
        ['NTW-001', 'Cisco Switch 2960', 'Network Device', 'Cisco', 'Catalyst 2960', 'SN-NTW-001', 'CL-101', '2024-06-01', 25000, 'active', '24-port Gigabit'],
        ['NTW-002', 'TP-Link Router', 'Network Device', 'TP-Link', 'Archer AX6000', 'SN-NTW-002', 'NL-201', '2024-07-01', 8500, 'active', 'WiFi 6'],
        ['PRN-001', 'HP LaserJet Pro', 'Printer', 'HP', 'M404dn', 'SN-PRN-001', 'CL-101', '2024-06-01', 18000, 'active', 'Network ready'],
        ['PRN-002', 'Epson L3110', 'Printer', 'Epson', 'L3110', 'SN-PRN-002', 'CL-102', '2024-06-15', 9500, 'active', 'Ink tank printer'],
        ['GPU-001', 'NVIDIA GTX 1650', 'GPU', 'NVIDIA', 'GTX 1650', 'SN-GPU-001', 'CL-103', '2024-07-01', 12000, 'active', 'For multimedia lab'],
        ['GPU-002', 'NVIDIA GTX 1650', 'GPU', 'NVIDIA', 'GTX 1650', 'SN-GPU-002', 'CL-103', '2024-07-01', 12000, 'missing', ''],
        ['SSD-001', 'Samsung 870 EVO', 'SSD', 'Samsung', '870 EVO', 'SN-SSD-001', 'CL-101', '2024-06-01', 3500, 'active', '500GB SATA'],
        ['SSD-002', 'Samsung 870 EVO', 'SSD', 'Samsung', '870 EVO', 'SN-SSD-002', 'CL-101', '2024-06-01', 3500, 'active', '500GB SATA'],
        ['RM-001', 'Kingston 16GB DDR4', 'RAM', 'Kingston', 'KVR26N19S8/16', 'SN-RM-001', 'CL-101', '2024-06-01', 2800, 'active', ''],
        ['RM-002', 'Kingston 16GB DDR4', 'RAM', 'Kingston', 'KVR26N19S8/16', 'SN-RM-002', 'CL-101', '2024-06-01', 2800, 'active', ''],
    ];

    $checkEq = $pdo->prepare("SELECT COUNT(*) FROM equipment WHERE asset_tag = ?");
    $insertEq = $pdo->prepare("INSERT INTO equipment (asset_tag, name, category_id, brand, model, serial_number, laboratory_id, acquisition_date, purchase_cost, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $updateQr = $pdo->prepare("UPDATE equipment SET qr_code = ? WHERE id = ?");

    echo "\n--- Equipment ---\n";
    $created = 0;
    $skipped = 0;
    foreach ($equipmentList as $eq) {
        $checkEq->execute([$eq[0]]);
        if ($checkEq->fetchColumn() > 0) {
            $skipped++;
            continue;
        }

        $catId = getCategoryId($pdo, $eq[2]);
        if (!$catId) {
            echo "  Warning: Category '{$eq[2]}' not found, skipping.\n";
            continue;
        }

        $labRoom = $eq[6];
        $labId = $labIds[$labRoom] ?? null;
        if (!$labId) {
            echo "  Warning: Lab room '{$labRoom}' not found, skipping.\n";
            continue;
        }

        $insertEq->execute([
            $eq[0], $eq[1], $catId, $eq[3], $eq[4], $eq[5],
            $labId, $eq[7], $eq[8], $eq[9], $eq[10],
        ]);
        $eqId = (int)$pdo->lastInsertId();

        $qrPath = $qrService->generateEquipmentQR($eqId, $eq[0]);
        $updateQr->execute([$qrPath, $eqId]);

        echo "  Created: {$eq[0]} - {$eq[1]}\n";
        $created++;
    }

    echo "\n=== Summary ===\n";
    echo "Laboratories: " . count($laboratories) . " processed\n";
    echo "Equipment: {$created} created, {$skipped} skipped\n";
    echo "\nDone!\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
