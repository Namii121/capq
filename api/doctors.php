<?php
/**
 * CarePoint CAPQ - Doctors Directory API Endpoint
 * Methods: GET
 */

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, "Method Not Allowed. Use GET.", null, 405);
}

if ($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT d.id, d.full_name, d.title, d.specialty, d.room_number, d.is_active,
                   dept.name as department_name, dept.code as department_code,
                   COUNT(CASE WHEN q.status = 'WAITING' THEN 1 END) as waiting_count
            FROM doctors d
            JOIN departments dept ON d.department_id = dept.id
            LEFT JOIN queues q ON d.id = q.doctor_id
            GROUP BY d.id
            ORDER BY d.id ASC
        ");
        $doctors = $stmt->fetchAll();

        sendResponse(true, "Doctors directory retrieved successfully.", [
            'count' => count($doctors),
            'doctors' => $doctors
        ], 200);

    } catch (PDOException $e) {
        sendResponse(false, "Database query error: " . $e->getMessage(), null, 500);
    }
} else {
    // Fallback demo doctors
    $demoDoctors = [
        [
            'id' => 1,
            'full_name' => 'Dr. Sarah Santos',
            'title' => 'MD',
            'specialty' => 'Internal & General Medicine',
            'room_number' => 'Room 03',
            'department_name' => 'General Medicine',
            'waiting_count' => 5,
            'is_active' => 1
        ],
        [
            'id' => 2,
            'full_name' => 'Dr. Raymond Cruz',
            'title' => 'MD',
            'specialty' => 'Family Medicine',
            'room_number' => 'Room 01',
            'department_name' => 'General Medicine',
            'waiting_count' => 1,
            'is_active' => 1
        ],
        [
            'id' => 3,
            'full_name' => 'Dr. Patricia Lim',
            'title' => 'MD',
            'specialty' => 'Pediatrics & Neonatology',
            'room_number' => 'Room 02',
            'department_name' => 'Pediatrics',
            'waiting_count' => 3,
            'is_active' => 1
        ],
        [
            'id' => 4,
            'full_name' => 'Dr. Michael Tan',
            'title' => 'DMD',
            'specialty' => 'Dental Specialist',
            'room_number' => 'Room 04',
            'department_name' => 'Dental Clinic',
            'waiting_count' => 2,
            'is_active' => 1
        ]
    ];

    sendResponse(true, "Doctors directory retrieved (Demo Mode).", [
        'count' => count($demoDoctors),
        'doctors' => $demoDoctors
    ], 200);
}
