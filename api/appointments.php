<?php
/**
 * CarePoint CAPQ - Appointments API Endpoint
 * Handles appointment creation, listing, and reference code lookup
 * Methods: GET, POST
 */

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================================
// GET: List or Track Appointments
// ============================================================================
if ($method === 'GET') {
    $ref = isset($_GET['ref']) ? trim(strtoupper($_GET['ref'])) : null;
    $date = isset($_GET['date']) ? trim($_GET['date']) : null;
    $status = isset($_GET['status']) ? trim(strtoupper($_GET['status'])) : null;

    if ($pdo) {
        try {
            if ($ref) {
                // Track by reference code
                $stmt = $pdo->prepare("
                    SELECT a.*, p.full_name as patient_name, p.phone_number, p.birth_date, p.gender,
                           d.full_name as doctor_name, d.room_number, dept.name as department_name,
                           q.ticket_number, q.status as queue_status
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN departments dept ON a.department_id = dept.id
                    LEFT JOIN queues q ON a.id = q.appointment_id
                    WHERE a.reference_code = ?
                    LIMIT 1
                ");
                $stmt->execute([$ref]);
                $record = $stmt->fetch();

                if ($record) {
                    sendResponse(true, "Appointment located successfully.", $record, 200);
                } else {
                    sendResponse(false, "No appointment found matching reference '{$ref}'.", null, 404);
                }
            } else {
                // List all with optional filters
                $sql = "
                    SELECT a.*, p.full_name as patient_name, p.phone_number, p.gender,
                           d.full_name as doctor_name, d.room_number, dept.name as department_name,
                           q.ticket_number, q.status as queue_status
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN departments dept ON a.department_id = dept.id
                    LEFT JOIN queues q ON a.id = q.appointment_id
                    WHERE 1=1
                ";
                $params = [];

                if ($date) {
                    $sql .= " AND a.appointment_date = ?";
                    $params[] = $date;
                }
                if ($status) {
                    $sql .= " AND a.status = ?";
                    $params[] = $status;
                }
                $sql .= " ORDER BY a.id DESC LIMIT 50";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $records = $stmt->fetchAll();

                sendResponse(true, "Appointments retrieved successfully.", [
                    'count' => count($records),
                    'appointments' => $records
                ], 200);
            }
        } catch (PDOException $e) {
            sendResponse(false, "Database query error: " . $e->getMessage(), null, 500);
        }
    } else {
        // Fallback demo data if DB is offline
        $sampleRecords = [
            [
                'id' => 1,
                'reference_code' => 'CP-8921',
                'patient_name' => 'Juan Carlos Dela Cruz',
                'phone_number' => '0917-555-0192',
                'doctor_name' => 'Dr. Sarah Santos',
                'room_number' => 'Room 03',
                'department_name' => 'General Medicine',
                'appointment_date' => date('Y-m-d'),
                'slot_time' => '10:15 AM',
                'ticket_number' => 'A-102',
                'status' => 'IN_CONSULTATION',
                'queue_status' => 'IN_CONSULTATION'
            ],
            [
                'id' => 2,
                'reference_code' => 'CP-9014',
                'patient_name' => 'Beatriz Mendoza',
                'phone_number' => '0922-334-1188',
                'doctor_name' => 'Dr. Sarah Santos',
                'room_number' => 'Room 03',
                'department_name' => 'General Medicine',
                'appointment_date' => date('Y-m-d'),
                'slot_time' => '10:30 AM',
                'ticket_number' => 'A-103',
                'status' => 'CHECKED_IN',
                'queue_status' => 'WAITING'
            ]
        ];

        if ($ref) {
            $found = null;
            foreach ($sampleRecords as $r) {
                if ($r['reference_code'] === $ref) { $found = $r; break; }
            }
            if ($found) sendResponse(true, "Appointment located.", $found, 200);
            sendResponse(false, "Appointment reference not found.", null, 404);
        }

        sendResponse(true, "Appointments retrieved (Demo Mode).", [
            'count' => count($sampleRecords),
            'appointments' => $sampleRecords
        ], 200);
    }
}

// ============================================================================
// POST: Book New Appointment
// ============================================================================
if ($method === 'POST') {
    $input = getJsonPayload();

    // Required fields validation with aliases support
    $name = trim($input['patient_name'] ?? $input['full_name'] ?? '');
    $phone = trim($input['phone_number'] ?? $input['phone'] ?? '');
    $date = trim($input['appointment_date'] ?? $input['date'] ?? '');
    $slot = trim($input['slot_time'] ?? $input['time_slot'] ?? '');

    if (empty($name)) {
        sendResponse(false, "Missing required field: 'patient_name' or 'full_name'", null, 422);
    }
    if (empty($phone)) {
        sendResponse(false, "Missing required field: 'phone_number' or 'phone'", null, 422);
    }
    if (empty($date)) {
        sendResponse(false, "Missing required field: 'appointment_date'", null, 422);
    }
    if (empty($slot)) {
        sendResponse(false, "Missing required field: 'slot_time' or 'time_slot'", null, 422);
    }

    $email = isset($input['email']) ? trim($input['email']) : '';
    $dob = isset($input['birth_date']) ? $input['birth_date'] : ($input['dob'] ?? '1995-01-01');
    $gender = isset($input['gender']) ? $input['gender'] : 'Female';
    $deptId = isset($input['department_id']) && is_numeric($input['department_id']) ? (int)$input['department_id'] : 1;
    $docId = isset($input['doctor_id']) && is_numeric($input['doctor_id']) ? (int)$input['doctor_id'] : 1;
    $complaint = isset($input['chief_complaint']) ? trim($input['chief_complaint']) : ($input['reason_for_visit'] ?? 'Routine consultation');
    $isPriority = !empty($input['is_priority']) ? 1 : 0;

    // Generate unique reference & ticket codes
    $refCode = 'CP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    $prefix = ($deptId == 2) ? 'P' : (($deptId == 3) ? 'D' : 'A');
    $ticketNum = $prefix . '-' . rand(105, 199);

    if ($pdo) {
        try {
            $pdo->beginTransaction();

            // 1. Find or insert patient
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE phone_number = ? LIMIT 1");
            $stmt->execute([$phone]);
            $patient = $stmt->fetch();

            if ($patient) {
                $patientId = $patient['id'];
            } else {
                $patientCode = 'PT-' . rand(10000, 99999);
                $stmt = $pdo->prepare("
                    INSERT INTO patients (patient_code, full_name, phone_number, email, birth_date, gender, is_priority)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$patientCode, $name, $phone, $email, $dob, $gender, $isPriority]);
                $patientId = $pdo->lastInsertId();
            }

            // 2. Insert appointment
            $stmt = $pdo->prepare("
                INSERT INTO appointments (reference_code, patient_id, doctor_id, department_id, appointment_date, slot_time, chief_complaint, is_priority, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'SCHEDULED')
            ");
            $stmt->execute([$refCode, $patientId, $docId, $deptId, $date, $slot, $complaint, $isPriority]);
            $appointmentId = $pdo->lastInsertId();

            // 3. Issue Queue Ticket
            $stmt = $pdo->prepare("
                INSERT INTO queues (ticket_number, appointment_id, patient_id, doctor_id, department_id, room_number, queue_type, status, is_priority)
                VALUES (?, ?, ?, ?, ?, 'Room 03', 'Appointment', 'WAITING', ?)
            ");
            $stmt->execute([$ticketNum, $appointmentId, $patientId, $docId, $deptId, $isPriority]);
            $queueId = $pdo->lastInsertId();

            $pdo->commit();

            sendResponse(true, "Appointment scheduled and queue ticket issued successfully.", [
                'appointment_id' => $appointmentId,
                'reference_code' => $refCode,
                'ticket_number' => $ticketNum,
                'patient_name' => $name,
                'appointment_date' => $date,
                'slot_time' => $slot,
                'room_number' => 'Room 03',
                'status' => 'SCHEDULED',
                'is_priority' => (bool)$isPriority
            ], 201);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            sendResponse(false, "Failed to book appointment: " . $e->getMessage(), null, 500);
        }
    } else {
        // Mock success response
        sendResponse(true, "Appointment scheduled and queue ticket issued (Demo Mode).", [
            'appointment_id' => rand(100, 999),
            'reference_code' => $refCode,
            'ticket_number' => $ticketNum,
            'patient_name' => $name,
            'appointment_date' => $date,
            'slot_time' => $slot,
            'room_number' => 'Room 03',
            'status' => 'SCHEDULED',
            'is_priority' => (bool)$isPriority
        ], 201);
    }
}

sendResponse(false, "Method Not Allowed. Use GET or POST.", null, 405);
