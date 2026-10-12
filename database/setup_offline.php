<?php

/**
 * Offline Database Setup & Seeder for Lab Management System
 * Supports both SQLite and MySQL seamlessly
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$sqliteFile = __DIR__ . '/lab-management.sqlite';
$driver = $_ENV['DB_CONNECTION'] ?? 'sqlite';

echo "=== Initializing Offline Database ({$driver}) ===\n";

if ($driver === 'sqlite' || !isset($_ENV['DB_HOST'])) {
    $pdo = new PDO("sqlite:{$sqliteFile}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} else {
    try {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dbname = $_ENV['DB_NAME'] ?? 'lab-management';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbname}`;");
    } catch (PDOException $e) {
        echo "MySQL not reachable ({$e->getMessage()}). Falling back to SQLite offline database...\n";
        $pdo = new PDO("sqlite:{$sqliteFile}");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("PRAGMA foreign_keys = ON;");
        $driver = 'sqlite';
    }
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS roles (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role_id INTEGER NOT NULL,
        avatar VARCHAR(255) NULL,
        department_segment VARCHAR(100) NULL,
        status VARCHAR(20) DEFAULT 'active',
        last_login DATETIME NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        student_number VARCHAR(50) NOT NULL UNIQUE,
        full_name VARCHAR(255) NOT NULL,
        course VARCHAR(100) NULL,
        year_level VARCHAR(20) NULL,
        section VARCHAR(50) NULL,
        email VARCHAR(255) NULL,
        password VARCHAR(255) NULL,
        status VARCHAR(20) DEFAULT 'active',
        or_number VARCHAR(100) NULL,
        semester VARCHAR(50) DEFAULT 'First Semester',
        or_date DATE NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS laboratories (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        name VARCHAR(255) NOT NULL,
        room_number VARCHAR(50) NOT NULL,
        building VARCHAR(255) NULL,
        capacity INTEGER DEFAULT 0,
        description TEXT NULL,
        qr_code VARCHAR(255) NULL,
        status VARCHAR(20) DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS equipment_categories (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS equipment (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        asset_tag VARCHAR(100) NOT NULL UNIQUE,
        property_code VARCHAR(100) NULL,
        name VARCHAR(255) NOT NULL,
        category_id INTEGER NOT NULL,
        classification VARCHAR(100) DEFAULT 'ICT Equipment',
        brand VARCHAR(255) NULL,
        model VARCHAR(255) NULL,
        serial_number VARCHAR(255) NULL,
        laboratory_id INTEGER NOT NULL,
        acquisition_date DATE NULL,
        purchase_cost DECIMAL(12, 2) DEFAULT 0,
        status VARCHAR(30) DEFAULT 'active',
        service_status VARCHAR(50) DEFAULT 'operational',
        notes TEXT NULL,
        qr_code VARCHAR(255) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES equipment_categories(id) ON DELETE CASCADE,
        FOREIGN KEY (laboratory_id) REFERENCES laboratories(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS attendance (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        student_id INTEGER NOT NULL,
        laboratory_id INTEGER NOT NULL,
        attendance_date DATE NOT NULL,
        time_in TIME NOT NULL,
        time_out TIME NULL,
        ip_address VARCHAR(45) NULL,
        browser TEXT NULL,
        session_id VARCHAR(255) NULL,
        status VARCHAR(20) DEFAULT 'present',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (laboratory_id) REFERENCES laboratories(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS audits (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        laboratory_id INTEGER NOT NULL,
        conducted_by INTEGER NOT NULL,
        audit_date DATE NOT NULL,
        status VARCHAR(30) DEFAULT 'in_progress',
        completed_at DATETIME NULL,
        notes TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (laboratory_id) REFERENCES laboratories(id) ON DELETE CASCADE,
        FOREIGN KEY (conducted_by) REFERENCES users(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS audit_items (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        audit_id INTEGER NOT NULL,
        equipment_id INTEGER NOT NULL,
        status VARCHAR(30) DEFAULT 'missing',
        scanned_at DATETIME NULL,
        notes TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (audit_id) REFERENCES audits(id) ON DELETE CASCADE,
        FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS investigations (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        equipment_id INTEGER NOT NULL,
        audit_id INTEGER NULL,
        initiated_by INTEGER NOT NULL,
        status VARCHAR(30) DEFAULT 'open',
        findings TEXT NULL,
        recommendations TEXT NULL,
        resolved_at DATETIME NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
        FOREIGN KEY (initiated_by) REFERENCES users(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS borrowings (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        borrower_id INTEGER NOT NULL,
        equipment_id INTEGER NOT NULL,
        date_borrowed DATE NOT NULL,
        expected_return DATE NOT NULL,
        actual_return DATE NULL,
        status VARCHAR(30) DEFAULT 'pending',
        approved_by INTEGER NULL,
        notes TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (borrower_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS activity_logs (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        user_id INTEGER NULL,
        action VARCHAR(100) NOT NULL,
        description TEXT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS equipment_services (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        equipment_id INTEGER NOT NULL,
        service_type VARCHAR(50) NOT NULL,
        service_date DATE NOT NULL,
        technician_name VARCHAR(255) NULL,
        company_or_dept VARCHAR(255) NULL,
        cost DECIMAL(10, 2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'completed',
        notes TEXT NULL,
        next_service_date DATE NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS equipment_dispatches (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        equipment_id INTEGER NOT NULL,
        from_lab_id INTEGER NULL,
        to_lab_id INTEGER NULL,
        dispatched_to_user_id INTEGER NULL,
        segment VARCHAR(100) NULL,
        dispatch_date DATE NOT NULL,
        return_due_date DATE NULL,
        actual_return_date DATE NULL,
        purpose TEXT NULL,
        status VARCHAR(30) DEFAULT 'dispatched',
        notes TEXT NULL,
        dispatched_by INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS segment_requests (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        request_number VARCHAR(100) NOT NULL UNIQUE,
        requester_name VARCHAR(255) NOT NULL,
        requester_email VARCHAR(255) NOT NULL,
        laboratory_id INTEGER NOT NULL,
        segment_name VARCHAR(100) NOT NULL,
        purpose TEXT NOT NULL,
        request_date DATE NOT NULL,
        start_time VARCHAR(20) NULL,
        end_time VARCHAR(20) NULL,
        expected_users INTEGER DEFAULT 1,
        equipment_needed TEXT NULL,
        status VARCHAR(30) DEFAULT 'pending',
        admin_notes TEXT NULL,
        reviewed_by INTEGER NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (laboratory_id) REFERENCES laboratories(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS account_requests (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        request_number VARCHAR(100) NOT NULL UNIQUE,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        requested_role_id INTEGER DEFAULT 4,
        department_segment VARCHAR(100) NULL,
        id_number VARCHAR(100) NULL,
        reason TEXT NULL,
        status VARCHAR(30) DEFAULT 'pending',
        reviewed_by INTEGER NULL,
        admin_notes TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $t) {
    $pdo->exec($t);
}
echo "✓ All 17 tables created/verified successfully.\n";

// Seed Roles
$checkRoles = $pdo->query("SELECT COUNT(*) as c FROM roles")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkRoles == 0) {
    $roles = [
        [1, 'Super Administrator', 'Full system access and master control'],
        [2, 'Laboratory Administrator', 'Manage laboratories, inventory, and staff'],
        [3, 'Laboratory Custodian', 'Perform audits, inventory check, and maintenance'],
        [4, 'Faculty', 'Manage laboratory sessions, requests, attendance, and equipment'],
        [5, 'Student', 'Scan QR codes for laboratory attendance and access'],
        [6, 'Human Resources (HR)', 'Check new equipment, asset registers, property tags, and personnel assignments']
    ];
    $stmt = $pdo->prepare("INSERT INTO roles (id, name, description) VALUES (?, ?, ?)");
    foreach ($roles as $r) {
        $stmt->execute($r);
    }
    echo "✓ Seeded standard roles.\n";
} else {
    // Ensure Role 6 exists
    $checkHrRole = $pdo->query("SELECT COUNT(*) as c FROM roles WHERE id = 6")->fetch(PDO::FETCH_ASSOC)['c'];
    if ($checkHrRole == 0) {
        $pdo->exec("INSERT INTO roles (id, name, description) VALUES (6, 'Human Resources (HR)', 'Check new equipment, asset registers, property tags, and personnel assignments')");
        echo "✓ Added Role 6: Human Resources (HR).\n";
    }
}

// Seed Users
$checkUsers = $pdo->query("SELECT COUNT(*) as c FROM users")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkUsers == 0) {
    $pwd = password_hash('admin123', PASSWORD_BCRYPT);
    $users = [
        ['System Administrator', 'admin@labmanagement.com', $pwd, 1, 'Main Admin Office', 'active'],
        ['Laboratory Administrator', 'labadmin@labmanagement.com', $pwd, 2, 'Computer Labs Department', 'active'],
        ['Laboratory Custodian', 'custodian@labmanagement.com', $pwd, 3, 'Property & Custodial Office', 'active'],
        ['Prof. Maria Santos', 'faculty@labmanagement.com', $pwd, 4, 'College of Computer Studies', 'active'],
        ['Engr. Juan Dela Cruz', 'delacruz@labmanagement.com', $pwd, 4, 'Information Technology Dept', 'active'],
        ['HR Asset Specialist', 'hr@labmanagement.com', $pwd, 6, 'Human Resources Department', 'active'],
    ];
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id, department_segment, status) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    echo "✓ Seeded administrative, faculty, and HR accounts (password: admin123).\n";
} else {
    // Ensure HR user exists
    $checkHrUser = $pdo->query("SELECT COUNT(*) as c FROM users WHERE role_id = 6")->fetch(PDO::FETCH_ASSOC)['c'];
    if ($checkHrUser == 0) {
        $pwd = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id, department_segment, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['HR Asset Specialist', 'hr@labmanagement.com', $pwd, 6, 'Human Resources Department', 'active']);
        echo "✓ Seeded HR user (hr@labmanagement.com / admin123).\n";
    }
}

// Seed Laboratories
$checkLabs = $pdo->query("SELECT COUNT(*) as c FROM laboratories")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkLabs == 0) {
    $labs = [
        ['Computer Laboratory 1', 'CL-101', 'Tech Building 1st Floor', 45, 'Programming and Algorithms Lab', 'qr/lab_1_1783995719.png'],
        ['Computer Laboratory 2', 'CL-102', 'Tech Building 1st Floor', 40, 'Web Development & Database Lab', 'qr/lab_2_1784003202.png'],
        ['Multimedia & Design Lab', 'CL-201', 'Tech Building 2nd Floor', 35, 'Graphics, Animation and Audio/Video Lab', 'qr/lab_3_1784003202.png'],
        ['Networking & Cisco Lab', 'CL-202', 'Tech Building 2nd Floor', 30, 'Routing, Switching and Cyber Security Lab', 'qr/lab_4_1784003202.png'],
        ['Hardware & Maintenance Lab', 'CL-301', 'Tech Building 3rd Floor', 30, 'PC Assembly, Diagnostics and Electronics Lab', 'qr/lab_5_1784003202.png'],
    ];
    $stmt = $pdo->prepare("INSERT INTO laboratories (name, room_number, building, capacity, description, qr_code) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($labs as $l) {
        $stmt->execute($l);
    }
    echo "✓ Seeded 5 active computer laboratories.\n";
}

// Seed Equipment Categories
$checkCats = $pdo->query("SELECT COUNT(*) as c FROM equipment_categories")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkCats == 0) {
    $categories = [
        'System Unit', 'Monitor', 'Keyboard', 'Mouse', 'RAM', 'GPU',
        'Power Supply', 'Motherboard', 'SSD', 'HDD', 'Network Device',
        'Printer', 'Projector', 'Laboratory Apparatus', 'Others'
    ];
    $stmt = $pdo->prepare("INSERT INTO equipment_categories (name) VALUES (?)");
    foreach ($categories as $cat) {
        $stmt->execute([$cat]);
    }
    echo "✓ Seeded equipment categories.\n";
}

// Seed Students with First Semester OR
$checkStudents = $pdo->query("SELECT COUNT(*) as c FROM students")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkStudents == 0) {
    $spwd = password_hash('student123', PASSWORD_BCRYPT);
    $students = [
        ['2023-00101', 'Juan Carlos Bautista', 'BS Information Technology', '3rd Year', 'BSIT-3A', 'jbautista@uphs.edu.ph', $spwd, 'OR-2026-10492', 'First Semester', '2026-08-15'],
        ['2023-00102', 'Alyssa Nicole Reyes', 'BS Computer Science', '3rd Year', 'BSCS-3A', 'areyes@uphs.edu.ph', $spwd, 'OR-2026-10493', 'First Semester', '2026-08-16'],
        ['2024-00201', 'Christian Dave Ramos', 'BS Information Technology', '2nd Year', 'BSIT-2B', 'cramos@uphs.edu.ph', $spwd, 'OR-2026-10501', 'First Semester', '2026-08-18'],
        ['2024-00202', 'Bea Patricia Mendoza', 'BS Computer Engineering', '2nd Year', 'BSCpE-2A', 'bmendoza@uphs.edu.ph', $spwd, 'OR-2026-10515', 'First Semester', '2026-08-19'],
        ['2025-00301', 'Mark Anthony Villanueva', 'BS Information Technology', '1st Year', 'BSIT-1A', 'mvillanueva@uphs.edu.ph', $spwd, 'OR-2026-10620', 'First Semester', '2026-08-20'],
        ['2025-00302', 'Sophia Lorraine Cruz', 'BS Computer Science', '1st Year', 'BSCS-1A', 'scruz@uphs.edu.ph', $spwd, 'OR-2026-10622', 'First Semester', '2026-08-21'],
    ];
    $stmt = $pdo->prepare("INSERT INTO students (student_number, full_name, course, year_level, section, email, password, or_number, semester, or_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($students as $st) {
        $stmt->execute($st);
    }
    echo "✓ Seeded student records with First Semester OR.\n";
}

// Seed Equipment with Property Code & Classification
$checkEq = $pdo->query("SELECT COUNT(*) as c FROM equipment")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkEq == 0) {
    $recentDate1 = date('Y-m-d', strtotime('-4 days'));
    $recentDate2 = date('Y-m-d', strtotime('-10 days'));
    $equipment = [
        ['EQ-SU-001', 'PROP-2026-CL1-001', 'Dell OptiPlex 7090 Desktop', 1, 'ICT Equipment', 'Dell', 'OptiPlex 7090 Core i7', 'SN-DELL-8821901', 1, $recentDate1, 48500.00, 'active', 'operational', 'Workstation 01 in Room 101', 'qr/eq_2_SU-001.png'],
        ['EQ-SU-002', 'PROP-2026-CL1-002', 'Dell OptiPlex 7090 Desktop', 1, 'ICT Equipment', 'Dell', 'OptiPlex 7090 Core i7', 'SN-DELL-8821902', 1, $recentDate2, 48500.00, 'active', 'operational', 'Workstation 02 in Room 101', 'qr/eq_3_SU-002.png'],
        ['EQ-MON-001', 'PROP-2026-CL1-003', 'Dell UltraSharp 24" IPS Monitor', 2, 'ICT Equipment', 'Dell', 'U2422H', 'SN-MON-99120', 1, '2024-03-10', 12500.00, 'active', 'operational', 'Workstation 01 Monitor', 'qr/eq_8_MON-001.png'],
        ['EQ-MON-002', 'PROP-2026-CL1-004', 'Dell UltraSharp 24" IPS Monitor', 2, 'ICT Equipment', 'Dell', 'U2422H', 'SN-MON-99121', 1, '2024-03-10', 12500.00, 'active', 'operational', 'Workstation 02 Monitor', 'qr/eq_9_MON-002.png'],
        ['EQ-KBD-001', 'PROP-2026-CL1-005', 'Logitech Mechanical Keyboard', 3, 'Peripherals & Accessories', 'Logitech', 'K845', 'SN-KBD-11029', 1, date('Y-m-d', strtotime('-15 days')), 3200.00, 'active', 'operational', 'Lab 1 Keyboard', 'qr/eq_13_KBD-001.png'],
        ['EQ-PRJ-001', 'PROP-2026-CL2-001', 'Epson EB-X51 3LCD Projector', 13, 'Capital Outlay / High-Value Asset', 'Epson', 'EB-X51', 'SN-EPS-44910', 2, '2023-09-01', 35000.00, 'active', 'operational', 'Ceiling mounted projector in Room 102', 'qr/eq_21_PRJ-001.png'],
        ['EQ-NTW-001', 'PROP-2026-CL4-001', 'Cisco Catalyst 2960 Switch 24-Port', 11, 'Capital Outlay / High-Value Asset', 'Cisco', 'WS-C2960X-24TS-L', 'SN-CSCO-2960-91', 4, '2023-11-15', 78000.00, 'active', 'operational', 'Cisco Network Bay 1', 'qr/eq_23_NTW-001.png'],
        ['EQ-GPU-001', 'PROP-2026-CL3-001', 'NVIDIA GeForce RTX 4070 12GB', 6, 'Semi-Expendable Property', 'ASUS', 'Dual RTX 4070', 'SN-NV-4070-01', 3, '2024-05-20', 36000.00, 'active', 'operational', 'Multimedia Rendering PC 01', 'qr/eq_27_GPU-001.png'],
        ['EQ-PRN-001', 'PROP-2026-CL2-002', 'HP LaserJet Pro M404dn', 12, 'ICT Equipment', 'HP', 'M404dn', 'SN-HP-88301', 2, '2024-01-15', 18500.00, 'under_repair', 'needs_maintenance', 'Paper jam issue under review', 'qr/eq_25_PRN-001.png'],
        ['EQ-SSD-001', 'PROP-2026-CL5-001', 'Samsung 970 EVO Plus 1TB NVMe', 9, 'Semi-Expendable Property', 'Samsung', 'MZ-V7S1T0B', 'SN-SSD-9910', 5, '2024-02-18', 5200.00, 'defective', 'defective', 'S.M.A.R.T error reported during audit', 'qr/eq_29_SSD-001.png'],
    ];
    $stmt = $pdo->prepare("INSERT INTO equipment (asset_tag, property_code, name, category_id, classification, brand, model, serial_number, laboratory_id, acquisition_date, purchase_cost, status, service_status, notes, qr_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($equipment as $e) {
        $stmt->execute($e);
    }
    echo "✓ Seeded 10 equipment items with Property Codes & Classifications.\n";
}

// Seed Services
$checkServices = $pdo->query("SELECT COUNT(*) as c FROM equipment_services")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkServices == 0) {
    $services = [
        [1, 'Preventive Maintenance', '2026-09-10', 'Alex Villanueva', 'UPHS IT Tech Support', 0.00, 'completed', 'Cleaned internal fans, reapplied thermal paste, updated BIOS.', '2027-03-10'],
        [6, 'Filter Cleaning & Calibration', '2026-08-25', 'Tech Services Phils', 'AV Solutions Inc', 1500.00, 'completed', 'Optical cleaning, lamp hour inspection (420 hrs used).', '2027-02-25'],
        [7, 'Firmware Update & Port Testing', '2026-09-01', 'Engr. Juan Dela Cruz', 'Network Lab Dept', 0.00, 'completed', 'Upgraded Cisco IOS to version 15.2(7)E10. All ports tested green.', '2027-03-01'],
        [9, 'Roller Replacement & Diagnostic', '2026-10-05', 'HP Authorized Service', 'HP Care Philippines', 2400.00, 'in_progress', 'Pickup roller replacement pending parts delivery.', '2026-10-20'],
    ];
    $stmt = $pdo->prepare("INSERT INTO equipment_services (equipment_id, service_type, service_date, technician_name, company_or_dept, cost, status, notes, next_service_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($services as $srv) {
        $stmt->execute($srv);
    }
    echo "✓ Seeded equipment services and maintenance history.\n";
}

// Seed Segment Requests (Long NR format)
$checkSegReq = $pdo->query("SELECT COUNT(*) as c FROM segment_requests")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkSegReq == 0) {
    $today = date('Y-m-d');
    $segReqs = [
        ['NR-20261012-0042-SEG-A', 'Prof. Maria Santos', 'faculty@labmanagement.com', 4, 'Cisco Bay Segment A', 'Hands-on Routing Protocols Configuration for BSIT-3A', $today, '08:00 AM', '11:00 AM', 25, '24x Cisco Routers, Console Cables', 'approved', 'Approved for class session', 1],
        ['NR-20261012-0043-SEG-B', 'Engr. Juan Dela Cruz', 'delacruz@labmanagement.com', 3, 'Multimedia Bay 2', '3D Graphics Rendering Workshop for Game Dev Students', $today, '01:00 PM', '04:00 PM', 20, 'High-performance GPU workstations', 'approved', 'Segment reserved', 1],
        ['NR-20261012-0044-SEG-C', 'Student Council - CS Org', 'csorg@uphs.edu.ph', 1, 'Programming Bay 1', 'Competitive Programming Hackathon Elimination Round', date('Y-m-d', strtotime('+2 days')), '09:00 AM', '05:00 PM', 35, 'Full lab access, projector', 'pending', 'Under administrative review', null],
    ];
    $stmt = $pdo->prepare("INSERT INTO segment_requests (request_number, requester_name, requester_email, laboratory_id, segment_name, purpose, request_date, start_time, end_time, expected_users, equipment_needed, status, admin_notes, reviewed_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($segReqs as $sr) {
        $stmt->execute($sr);
    }
    echo "✓ Seeded segment requests with Long NR identifiers.\n";
}

// Seed Account Requests
$checkAccReq = $pdo->query("SELECT COUNT(*) as c FROM account_requests")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkAccReq == 0) {
    $accReqs = [
        ['NR-20261012-ACC-01', 'Dr. Roberto Mendoza', 'rmendoza@uphs.edu.ph', 4, 'Computer Engineering Department', 'FAC-2026-8812', 'Needs access to manage hardware lab sessions and schedule segment requests.', 'pending', null, null],
        ['NR-20261012-ACC-02', 'Lorna Garcia', 'lgarcia@uphs.edu.ph', 3, 'Property & Custodial Office', 'STF-2026-3391', 'Assigned as assistant custodian for CL-101 and CL-102.', 'pending', null, null],
    ];
    $stmt = $pdo->prepare("INSERT INTO account_requests (request_number, name, email, requested_role_id, department_segment, id_number, reason, status, reviewed_by, admin_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($accReqs as $ar) {
        $stmt->execute($ar);
    }
    echo "✓ Seeded account requests with Long NR identifiers.\n";
}

// Seed Settings
$checkSettings = $pdo->query("SELECT COUNT(*) as c FROM settings")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkSettings == 0) {
    $settings = [
        ['duplicate_scan_minutes', '30'],
        ['app_name', 'UPHS Computer Laboratory Management System'],
        ['session_lifetime', '120'],
        ['qr_size', '300'],
        ['current_semester', 'First Semester'],
        ['academic_year', '2026-2027'],
        ['offline_mode', 'true'],
        ['time_in_shuffle_mode', 'auto'],
    ];
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $s) {
        $stmt->execute($s);
    }
    echo "✓ Seeded system settings.\n";
}

// Seed Attendance today with active users
$checkAtt = $pdo->query("SELECT COUNT(*) as c FROM attendance")->fetch(PDO::FETCH_ASSOC)['c'];
if ($checkAtt == 0) {
    $today = date('Y-m-d');
    $timeIns = [
        [1, 1, $today, '08:15:00', null, '127.0.0.1', 'Lab Kiosk', 'sess_01', 'present'],
        [2, 1, $today, '08:20:00', null, '127.0.0.1', 'Lab Kiosk', 'sess_02', 'present'],
        [3, 2, $today, '09:05:00', null, '127.0.0.1', 'Lab Kiosk', 'sess_03', 'present'],
        [4, 3, $today, '10:00:00', '11:30:00', '127.0.0.1', 'Lab Kiosk', 'sess_04', 'present'],
        [5, 4, $today, '10:15:00', null, '127.0.0.1', 'Lab Kiosk', 'sess_05', 'present'],
    ];
    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, laboratory_id, attendance_date, time_in, time_out, ip_address, browser, session_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($timeIns as $ti) {
        $stmt->execute($ti);
    }
    echo "✓ Seeded live laboratory attendance records (active users currently in labs).\n";
}

echo "\n=== Database Setup Complete! Offline system is fully ready. ===\n";
