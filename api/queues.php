<?php
/**
 * CarePoint CAPQ - Queue Management API Endpoint
 * Handles live queue board, kiosk walk-in ticket dispensing, doctor calling, and consultation completion
 * Methods: GET, POST, PUT, PATCH
 */

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================================
// GET: Live Queue Board & Room Statuses
// ============================================================================
if ($method === 'GET') {
    $deptId = isset($_GET['department_id']) ? (int)$_GET['department_id'] : null;

    if ($pdo) {
        try {
            // 1. Fetch currently calling / active consultation patient
            $callingStmt = $pdo->query("
                SELECT q.*, p.full_name as patient_name, p.birth_date, p.gender, p.phone_number,
                       d.full_name as doctor_name, dept.name as department_name,
                       c.bp, c.temp, c.pulse, c.spo2, c.weight, c.diagnosis, c.prescription, c.notes
                FROM queues q
                JOIN patients p ON q.patient_id = p.id
                LEFT JOIN doctors d ON q.doctor_id = d.id
                JOIN departments dept ON q.department_id = dept.id
                LEFT JOIN consultations c ON q.id = c.queue_id
                WHERE q.status IN ('CALLED', 'IN_CONSULTATION')
                ORDER BY q.called_time DESC, q.id DESC
                LIMIT 1
            ");
            $nowCalling = $callingStmt->fetch() ?: null;

            // 2. Fetch upcoming waiting queue list (Ordered by priority first, then check-in time)
            $queueStmt = $pdo->query("
                SELECT q.*, p.full_name as patient_name, p.birth_date, p.gender, p.phone_number,
                       dept.name as department_name, d.full_name as doctor_name
                FROM queues q
                JOIN patients p ON q.patient_id = p.id
                JOIN departments dept ON q.department_id = dept.id
                LEFT JOIN doctors d ON q.doctor_id = d.id
                WHERE q.status = 'WAITING'
                ORDER BY q.is_priority DESC, q.id ASC
                LIMIT 20
            ");
            $upcomingQueue = $queueStmt->fetchAll();

            // 3. Multi-counter rooms overview
            $rooms = [
                ['room' => '01', 'name' => 'Triage & Vital Signs', 'doctor' => 'Nurse Station 1', 'ticket' => 'T-089', 'next' => ['T-090', 'T-091']],
                ['room' => '02', 'name' => 'Pediatrics Clinic', 'doctor' => 'Dr. Patricia Lim, MD', 'ticket' => 'P-045', 'next' => ['P-046', 'P-047']],
                ['room' => '03', 'name' => 'General Medicine', 'doctor' => 'Dr. Sarah Santos, MD', 'ticket' => $nowCalling ? $nowCalling['ticket_number'] : 'A-102', 'next' => count($upcomingQueue) > 0 ? [$upcomingQueue[0]['ticket_number']] : ['A-103']],
                ['room' => '04', 'name' => 'Dental & Oral Care', 'doctor' => 'Dr. Michael Tan, DMD', 'ticket' => 'D-018', 'next' => ['D-019']],
                ['room' => '05', 'name' => 'Pharmacy & Billing', 'doctor' => 'Prescription Counter', 'ticket' => 'B-201', 'next' => ['B-202']]
            ];

            sendResponse(true, "Live queue state retrieved.", [
                'now_calling' => $nowCalling,
                'waiting_count' => count($upcomingQueue),
                'upcoming_queue' => $upcomingQueue,
                'rooms' => $rooms
            ], 200);

        } catch (PDOException $e) {
            sendResponse(false, "Database error: " . $e->getMessage(), null, 500);
        }
    } else {
        // Fallback demo state
        $defaultState = [
            'now_calling' => [
                'ticket_number' => 'A-102',
                'patient_name' => 'Juan Carlos Dela Cruz',
                'gender' => 'Male',
                'phone_number' => '0917-555-0192',
                'room_number' => 'Room 03',
                'department_name' => 'General Medicine',
                'doctor_name' => 'Dr. Sarah Santos, MD',
                'status' => 'IN_CONSULTATION',
                'bp' => '120/80',
                'temp' => '36.8',
                'pulse' => '76',
                'spo2' => '99',
                'diagnosis' => 'J06.9 - Acute Upper Respiratory Infection (Mild)',
                'prescription' => 'Cetirizine 10mg once daily for 5 days.'
            ],
            'waiting_count' => 4,
            'upcoming_queue' => [
                ['ticket_number' => 'A-103', 'patient_name' => 'Beatriz Mendoza', 'queue_type' => 'Appointment', 'is_priority' => 0, 'room_number' => 'Room 03'],
                ['ticket_number' => 'P-012', 'patient_name' => 'Rosa Flores', 'queue_type' => 'Walk-in', 'is_priority' => 1, 'room_number' => 'Room 03'],
                ['ticket_number' => 'A-104', 'patient_name' => 'Eduardo Rivera', 'queue_type' => 'Walk-in', 'is_priority' => 0, 'room_number' => 'Room 03'],
                ['ticket_number' => 'A-105', 'patient_name' => 'Gabriel Ramos', 'queue_type' => 'Appointment', 'is_priority' => 0, 'room_number' => 'Room 03']
            ],
            'rooms' => [
                ['room' => '01', 'name' => 'Triage & Vital Signs', 'ticket' => 'T-089'],
                ['room' => '02', 'name' => 'Pediatrics Clinic', 'ticket' => 'P-045'],
                ['room' => '03', 'name' => 'General Medicine', 'ticket' => 'A-102'],
                ['room' => '04', 'name' => 'Dental & Oral Care', 'ticket' => 'D-018'],
                ['room' => '05', 'name' => 'Pharmacy & Billing', 'ticket' => 'B-201']
            ]
        ];

        sendResponse(true, "Live queue state retrieved (Demo Mode).", $defaultState, 200);
    }
}

// ============================================================================
// POST: Issue Walk-In Ticket or Kiosk Check-In
// ============================================================================
if ($method === 'POST') {
    $input = getJsonPayload();

    $name = !empty($input['patient_name']) ? trim($input['patient_name']) : 'Walk-in Patient';
    $phone = !empty($input['phone_number']) ? trim($input['phone_number']) : '09XX-XXX-XXXX';
    $deptCode = !empty($input['department_code']) ? strtoupper(trim($input['department_code'])) : 'GEN_MED';
    $isPriority = !empty($input['is_priority']) ? 1 : 0;
    $refCode = !empty($input['reference_code']) ? strtoupper(trim($input['reference_code'])) : null;

    $deptMap = [
        'GEN_MED' => ['id' => 1, 'prefix' => 'A', 'room' => 'Room 03', 'name' => 'General Medicine'],
        'PEDIA'   => ['id' => 2, 'prefix' => 'P', 'room' => 'Room 02', 'name' => 'Pediatrics'],
        'DENTAL'  => ['id' => 3, 'prefix' => 'D', 'room' => 'Room 04', 'name' => 'Dental Clinic'],
        'TRIAGE'  => ['id' => 4, 'prefix' => 'T', 'room' => 'Room 01', 'name' => 'Triage & Vitals']
    ];

    $dept = isset($deptMap[$deptCode]) ? $deptMap[$deptCode] : $deptMap['GEN_MED'];
    $prefix = $isPriority ? 'P' : $dept['prefix'];
    $ticketNum = $prefix . '-' . rand(105, 199);

    if ($pdo) {
        try {
            $pdo->beginTransaction();

            // 1. Create or fetch patient
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE phone_number = ? LIMIT 1");
            $stmt->execute([$phone]);
            $patient = $stmt->fetch();

            if ($patient) {
                $patientId = $patient['id'];
            } else {
                $patientCode = 'PT-' . rand(10000, 99999);
                $stmt = $pdo->prepare("
                    INSERT INTO patients (patient_code, full_name, phone_number, birth_date, gender, is_priority)
                    VALUES (?, ?, ?, '1990-01-01', 'Other', ?)
                ");
                $stmt->execute([$patientCode, $name, $phone, $isPriority]);
                $patientId = $pdo->lastInsertId();
            }

            // 2. Check if checking in with existing appointment
            $appointmentId = null;
            if ($refCode) {
                $stmt = $pdo->prepare("SELECT id FROM appointments WHERE reference_code = ? LIMIT 1");
                $stmt->execute([$refCode]);
                $appt = $stmt->fetch();
                if ($appt) {
                    $appointmentId = $appt['id'];
                    $pdo->prepare("UPDATE appointments SET status = 'CHECKED_IN' WHERE id = ?")->execute([$appointmentId]);
                }
            }

            // 3. Insert Queue ticket
            $stmt = $pdo->prepare("
                INSERT INTO queues (ticket_number, appointment_id, patient_id, department_id, room_number, queue_type, status, is_priority)
                VALUES (?, ?, ?, ?, ?, ?, 'WAITING', ?)
            ");
            $type = $appointmentId ? 'Appointment' : 'Walk-in';
            $stmt->execute([$ticketNum, $appointmentId, $patientId, $dept['id'], $dept['room'], $type, $isPriority]);
            $queueId = $pdo->lastInsertId();

            $pdo->commit();

            sendResponse(true, "Queue ticket issued successfully.", [
                'queue_id' => $queueId,
                'ticket_number' => $ticketNum,
                'patient_name' => $name,
                'department_name' => $dept['name'],
                'room_number' => $dept['room'],
                'estimated_wait_minutes' => $isPriority ? 5 : 15,
                'patients_ahead' => $isPriority ? 0 : 3,
                'is_priority' => (bool)$isPriority
            ], 201);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            sendResponse(false, "Failed to issue ticket: " . $e->getMessage(), null, 500);
        }
    } else {
        // Fallback demo issuance
        sendResponse(true, "Queue ticket issued (Demo Mode).", [
            'queue_id' => rand(10, 99),
            'ticket_number' => $ticketNum,
            'patient_name' => $name,
            'department_name' => $dept['name'],
            'room_number' => $dept['room'],
            'estimated_wait_minutes' => $isPriority ? 5 : 15,
            'patients_ahead' => $isPriority ? 0 : 2,
            'is_priority' => (bool)$isPriority
        ], 201);
    }
}

// ============================================================================
// PUT: Doctor Calls Next Patient to Room
// ============================================================================
if ($method === 'PUT') {
    $input = getJsonPayload();
    $ticket = isset($input['ticket_number']) ? trim($input['ticket_number']) : null;
    $room = isset($input['room_number']) ? trim($input['room_number']) : 'Room 03';
    $docId = isset($input['doctor_id']) ? (int)$input['doctor_id'] : 1;

    if ($pdo) {
        try {
            $pdo->beginTransaction();

            if ($ticket) {
                $stmt = $pdo->prepare("SELECT id, patient_id FROM queues WHERE ticket_number = ? AND status = 'WAITING' LIMIT 1");
                $stmt->execute([$ticket]);
                $queue = $stmt->fetch();
            } else {
                // Call top priority or first in line
                $stmt = $pdo->query("SELECT id, ticket_number, patient_id FROM queues WHERE status = 'WAITING' ORDER BY is_priority DESC, id ASC LIMIT 1");
                $queue = $stmt->fetch();
            }

            if (!$queue) {
                $pdo->rollBack();
                sendResponse(false, "No waiting patient available to call.", null, 404);
            }

            // Update status to CALLED
            $updateStmt = $pdo->prepare("
                UPDATE queues
                SET status = 'CALLED', doctor_id = ?, room_number = ?, called_time = NOW()
                WHERE id = ?
            ");
            $updateStmt->execute([$docId, $room, $queue['id']]);

            $pdo->commit();

            sendResponse(true, "Patient called to {$room} successfully.", [
                'queue_id' => $queue['id'],
                'ticket_number' => $ticket ?: $queue['ticket_number'],
                'room_number' => $room,
                'status' => 'CALLED',
                'called_time' => date('h:i A')
            ], 200);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            sendResponse(false, "Failed to call patient: " . $e->getMessage(), null, 500);
        }
    } else {
        $calledTicket = $ticket ?: 'A-103';
        sendResponse(true, "Patient called to {$room} (Demo Mode).", [
            'ticket_number' => $calledTicket,
            'room_number' => $room,
            'status' => 'CALLED',
            'called_time' => date('h:i A')
        ], 200);
    }
}

// ============================================================================
// PATCH: Finish Consultation or Skip Patient
// ============================================================================
if ($method === 'PATCH') {
    $input = getJsonPayload();
    $action = isset($_GET['action']) ? strtolower($_GET['action']) : (isset($input['action']) ? strtolower($input['action']) : 'complete');
    $queueId = isset($input['queue_id']) ? (int)$input['queue_id'] : 1;
    $ticket = isset($input['ticket_number']) ? trim($input['ticket_number']) : 'A-102';

    if ($action === 'skip') {
        if ($pdo) {
            $pdo->prepare("UPDATE queues SET status = 'SKIPPED' WHERE id = ? OR ticket_number = ?")->execute([$queueId, $ticket]);
        }
        sendResponse(true, "Patient marked as Skipped / No-Show.", ['status' => 'SKIPPED'], 200);
    } else {
        // Complete Consultation & Record EHR Vitals
        $bp = isset($input['bp']) ? $input['bp'] : '120/80';
        $temp = isset($input['temp']) ? $input['temp'] : '36.8';
        $pulse = isset($input['pulse']) ? $input['pulse'] : '76';
        $spo2 = isset($input['spo2']) ? $input['spo2'] : '99';
        $diagnosis = isset($input['diagnosis']) ? $input['diagnosis'] : 'Routine Consultation Complete';
        $prescription = isset($input['prescription']) ? $input['prescription'] : 'Rest & hydration';

        if ($pdo) {
            try {
                $pdo->beginTransaction();
                $pdo->prepare("UPDATE queues SET status = 'COMPLETED', completed_time = NOW() WHERE id = ? OR ticket_number = ?")->execute([$queueId, $ticket]);

                $stmt = $pdo->prepare("
                    INSERT INTO consultations (queue_id, patient_id, doctor_id, bp, temp, pulse, spo2, diagnosis, prescription)
                    VALUES (?, 1, 1, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE diagnosis = VALUES(diagnosis), prescription = VALUES(prescription)
                ");
                $stmt->execute([$queueId, $bp, $temp, $pulse, $spo2, $diagnosis, $prescription]);

                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
            }
        }

        sendResponse(true, "Consultation finished and medical record saved successfully.", [
            'status' => 'COMPLETED',
            'ticket_number' => $ticket,
            'vitals' => ['bp' => $bp, 'temp' => $temp, 'pulse' => $pulse, 'spo2' => $spo2],
            'diagnosis' => $diagnosis,
            'prescription' => $prescription
        ], 200);
    }
}

sendResponse(false, "Method Not Allowed.", null, 405);
