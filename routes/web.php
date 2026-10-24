<?php

use App\Helpers\Router;

// Public Routes (No Auth Required)
Router::get('/', ['AuthController', 'loginForm']);
Router::get('/login', ['AuthController', 'loginForm']);
Router::post('/login', ['AuthController', 'login']);
Router::get('/request-account', ['AccountRequestController', 'requestForm']);
Router::post('/request-account', ['AccountRequestController', 'submitRequest']);
Router::get('/scan/laboratory/{id}', ['AttendanceController', 'scanLaboratoryQR']);
Router::get('/scan/equipment/{id}', ['EquipmentController', 'scanEquipmentQR']);
Router::post('/attendance/scan/{laboratoryId:\d+}', ['AttendanceController', 'scanAttendance']);
Router::post('/attendance/scan-redirect/{laboratoryId:\d+}', ['AttendanceController', 'scanAndRedirect']);

// Student Auth Routes
Router::get('/student/login', ['AttendanceController', 'studentLoginForm']);
Router::post('/student/login', ['AttendanceController', 'studentLogin']);
Router::get('/student/logout', ['AttendanceController', 'studentLogout']);
Router::get('/student/dashboard', ['AttendanceController', 'studentDashboard']);
Router::get('/student/scan', ['AttendanceController', 'studentScan']);
Router::get('/student/history', ['AttendanceController', 'studentHistory']);
Router::get('/student/profile', ['AttendanceController', 'studentProfile']);

// Authenticated Routes
Router::group('', ['AuthMiddleware'], function () {
    // Logout
    Router::post('/logout', ['AuthController', 'logout']);

    // Profile
    Router::get('/profile', ['AuthController', 'profile']);
    Router::post('/profile', ['AuthController', 'updateProfile']);
    Router::get('/change-password', ['AuthController', 'changePasswordForm']);
    Router::post('/change-password', ['AuthController', 'changePassword']);

    // Dashboard
    Router::get('/dashboard', ['DashboardController', 'index']);
    Router::post('/dashboard/account/add', ['DashboardController', 'addAccount'], ['AdminMiddleware']);

    // Laboratories
    Router::get('/laboratories', ['LaboratoryController', 'index']);
    Router::get('/laboratories/create', ['LaboratoryController', 'create']);
    Router::post('/laboratories', ['LaboratoryController', 'store']);
    Router::get('/laboratories/print-qr', ['LaboratoryController', 'printAll']);
    Router::get('/laboratories/{id}', ['LaboratoryController', 'show']);
    Router::get('/laboratories/{id}/edit', ['LaboratoryController', 'edit']);
    Router::post('/laboratories/{id}', ['LaboratoryController', 'update']);
    Router::delete('/laboratories/{id}', ['LaboratoryController', 'destroy'], ['AdminMiddleware']);
    Router::post('/laboratories/{id}/regenerate-qr', ['LaboratoryController', 'regenerateQR']);
    Router::get('/laboratories/{id}/qr', ['LaboratoryController', 'viewQR']);

    // Students
    Router::get('/students', ['StudentController', 'index']);
    Router::get('/students/create', ['StudentController', 'create']);
    Router::post('/students', ['StudentController', 'store']);
    Router::get('/students/{id}', ['StudentController', 'show']);
    Router::get('/students/{id}/edit', ['StudentController', 'edit']);
    Router::post('/students/{id}', ['StudentController', 'update']);
    Router::delete('/students/{id}', ['StudentController', 'destroy'], ['AdminMiddleware']);
    Router::post('/students/import-csv', ['StudentController', 'importCsv']);
    Router::get('/students/export-csv', ['StudentController', 'exportCsv']);

    // Attendance
    Router::get('/attendance', ['AttendanceController', 'index']);
    Router::get('/attendance/today', ['AttendanceController', 'today']);
    Router::get('/attendance/student/{id}', ['AttendanceController', 'byStudent']);
    Router::get('/attendance/laboratory/{id}', ['AttendanceController', 'byLaboratory']);

    // Equipment & Inventory
    Router::get('/equipment', ['EquipmentController', 'index']);
    Router::get('/equipment/create', ['EquipmentController', 'create']);
    Router::post('/equipment', ['EquipmentController', 'store']);
    Router::get('/equipment/import-template', ['EquipmentController', 'importTemplate']);
    Router::get('/equipment/export-pdf', ['EquipmentController', 'exportPdf']);
    Router::post('/equipment/import-file', ['EquipmentController', 'importChoiceFile']);
    Router::get('/equipment/print-qr', ['EquipmentController', 'printQr']);
    Router::get('/equipment/{id}', ['EquipmentController', 'show']);
    Router::get('/equipment/{id}/edit', ['EquipmentController', 'edit']);
    Router::post('/equipment/{id}', ['EquipmentController', 'update']);
    Router::delete('/equipment/{id}', ['EquipmentController', 'destroy'], ['AdminMiddleware']);
    Router::post('/equipment/{id}/regenerate-qr', ['EquipmentController', 'regenerateQR']);
    Router::get('/equipment/{id}/qr', ['EquipmentController', 'viewQR']);
    Router::get('/equipment/{id}/property-card-pdf', ['EquipmentController', 'propertyCardPdf']);
    Router::post('/equipment/{id}/dispatch', ['EquipmentController', 'dispatch']);
    Router::post('/equipment/dispatch/{id}/return', ['EquipmentController', 'returnDispatch']);

    // Equipment Services & Maintenance
    Router::get('/equipment-services', ['EquipmentServiceController', 'index']);
    Router::get('/equipment-services/create', ['EquipmentServiceController', 'create']);
    Router::post('/equipment-services', ['EquipmentServiceController', 'store']);
    Router::post('/equipment-services/{id}/complete', ['EquipmentServiceController', 'complete']);

    // Segment Requests
    Router::get('/segment-requests', ['SegmentRequestController', 'index']);
    Router::get('/segment-requests/create', ['SegmentRequestController', 'create']);
    Router::post('/segment-requests', ['SegmentRequestController', 'store']);
    Router::post('/segment-requests/{id}/approve', ['SegmentRequestController', 'approve']);
    Router::post('/segment-requests/{id}/reject', ['SegmentRequestController', 'reject']);

    // Capability for Account Requests (Admin Only)
    Router::get('/account-requests', ['AccountRequestController', 'index'], ['AdminMiddleware']);
    Router::post('/account-requests/{id}/approve', ['AccountRequestController', 'approve'], ['AdminMiddleware']);
    Router::post('/account-requests/{id}/reject', ['AccountRequestController', 'reject'], ['AdminMiddleware']);

    // Audits
    Router::get('/audits', ['AuditController', 'index']);
    Router::post('/audits/start', ['AuditController', 'start']);
    Router::get('/audits/{id}', ['AuditController', 'show']);
    Router::post('/audits/{id}/scan', ['AuditController', 'scan']);
    Router::post('/audits/{id}/complete', ['AuditController', 'complete']);

    // Search (rebranded from Investigations)
    Router::get('/search', ['SearchController', 'index']);
    Router::get('/search/{id}', ['SearchController', 'show']);
    Router::post('/search', ['SearchController', 'store']);
    Router::post('/search/bulk', ['SearchController', 'bulk']);
    Router::post('/search/{id}/resolve', ['SearchController', 'resolve']);

    // Backward compatibility for Investigations
    Router::get('/investigations', ['InvestigationController', 'index']);
    Router::get('/investigations/{id}', ['InvestigationController', 'show']);
    Router::post('/investigations', ['InvestigationController', 'store']);
    Router::post('/investigations/bulk', ['InvestigationController', 'bulk']);
    Router::post('/investigations/{id}/resolve', ['InvestigationController', 'resolve']);

    // Borrowing
    Router::get('/borrowing', ['BorrowingController', 'index']);
    Router::get('/borrowing/create', ['BorrowingController', 'create']);
    Router::post('/borrowing', ['BorrowingController', 'store']);
    Router::post('/borrowing/{id}/approve', ['BorrowingController', 'approve']);
    Router::post('/borrowing/{id}/return', ['BorrowingController', 'return']);
    Router::post('/borrowing/{id}/reject', ['BorrowingController', 'reject']);

    // Reports
    Router::get('/reports', ['ReportController', 'index']);
    Router::get('/reports/attendance', ['ReportController', 'attendance']);
    Router::get('/reports/equipment', ['ReportController', 'equipment']);
    Router::get('/reports/audit', ['ReportController', 'audit']);
    Router::get('/reports/borrowing', ['ReportController', 'borrowing']);
    Router::get('/reports/export/{type}/{format}', ['ReportController', 'export']);

    // Activity Logs (Admin Only)
    Router::get('/activity-logs', ['ActivityLogController', 'index'], ['AdminMiddleware']);

    // Settings (Admin Only)
    Router::get('/settings', ['SettingsController', 'index'], ['AdminMiddleware']);
    Router::post('/settings', ['SettingsController', 'update'], ['AdminMiddleware']);
});

// API Routes (Authenticated)
Router::group('/api', ['AuthMiddleware'], function () {
    Router::get('/dashboard/stats', ['DashboardController', 'stats']);
    Router::get('/attendance/trend', ['AttendanceController', 'trend']);
    Router::get('/equipment/status', ['EquipmentController', 'statusDistribution']);
    Router::post('/equipment/find', ['EquipmentController', 'findByAssetTag']);
});
