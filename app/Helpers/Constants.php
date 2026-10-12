<?php

namespace App\Helpers;

class Constants
{
    const ROLES = [
        'super_admin' => 1,
        'lab_admin' => 2,
        'custodian' => 3,
        'faculty' => 4,
        'student' => 5,
        'hr' => 6,
    ];

    const ROLE_LABELS = [
        1 => 'Super Administrator',
        2 => 'Laboratory Administrator',
        3 => 'Laboratory Custodian',
        4 => 'Faculty',
        5 => 'Student',
        6 => 'Human Resources (HR)',
    ];

    const EQUIPMENT_CATEGORIES = [
        'System Unit', 'Monitor', 'Keyboard', 'Mouse', 'RAM', 'GPU',
        'Power Supply', 'Motherboard', 'SSD', 'HDD', 'Network Device',
        'Printer', 'Projector', 'Laboratory Apparatus', 'Others',
    ];

    const EQUIPMENT_CLASSIFICATIONS = [
        'ICT Equipment',
        'Capital Outlay / High-Value Asset',
        'Semi-Expendable Property',
        'Peripherals & Accessories',
        'Laboratory Apparatus',
        'Consumable/Parts',
    ];

    const SERVICE_TYPES = [
        'Preventive Maintenance',
        'Repair',
        'Calibration',
        'Inspection',
        'Cleaning',
        'Hardware Upgrade',
        'Software/Firmware Update',
    ];

    const SEMESTERS = [
        'First Semester',
        'Second Semester',
        'Summer Term',
    ];

    const EQUIPMENT_STATUS = [
        'active', 'missing', 'defective', 'under_repair', 'borrowed', 'retired',
    ];

    const ATTENDANCE_STATUS = ['present', 'late', 'absent'];

    const AUDIT_STATUS = ['in_progress', 'completed', 'cancelled'];

    const BORROWING_STATUS = ['pending', 'approved', 'borrowed', 'returned', 'rejected', 'overdue'];

    const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
}
