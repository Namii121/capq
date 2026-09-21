<?php
/**
 * Module: Doctor
 * File: actions.php
 * Native PHP Actions for Doctor Consultation Station & Electronic Health Records
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'login':
        doctorLogin();
        break;

    case 'logout':
        doctorLogout();
        break;

    case 'get_session':
        getDoctorSession();
        break;

    case 'get_state':
        getDoctorState();
        break;

    case 'call_next':
        callNextPatient();
        break;

    case 'call_patient':
        callSpecificPatient();
        break;

    case 'save_consultation':
        saveConsultation();
        break;

    case 'complete_consultation':
        completeConsultation();
        break;

    case 'skip_patient':
        skipPatient();
        break;

    default:
        jsonResponse(false, null, 'Invalid doctor action requested', 400);
}

/**
 * Doctor authentication login
 */
function doctorLogin() {
    $pdo = getDBConnection();

    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $doctorId = intval($_POST['doctor_id'] ?? 0);

    // Quick Select mode by doctor_id for demo convenience
    if ($doctorId > 0) {
        $stmt = $pdo->prepare("SELECT id, username, full_name, role, department, room_number FROM users WHERE id = ? AND role = 'DOCTOR'");
        $stmt->execute([$doctorId]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['doctor'] = [
                'id'          => $user['id'],
                'username'    => $user['username'],
                'full_name'   => $user['full_name'],
                'department'  => $user['department'] ?: 'General Medicine',
                'room_number' => sprintf('%02d', intval($user['room_number'] ?: 3))
            ];
            jsonResponse(true, $_SESSION['doctor'], "Logged in as {$user['full_name']}");
            return;
        }
    } else if ($doctorId < 0) {
        // Fallback demo doctors
        $defaults = [
            -1 => ['username' => 'dr.santos', 'full_name' => 'Dr. Sarah Santos, MD', 'department' => 'General Medicine', 'room_number' => '03'],
            -2 => ['username' => 'dr.reyes', 'full_name' => 'Dr. Alexander Reyes, MD', 'department' => 'Pediatrics', 'room_number' => '02'],
            -3 => ['username' => 'dr.cruz', 'full_name' => 'Dr. Raymond Cruz, MD', 'department' => 'Dental Clinic', 'room_number' => '04'],
        ];
        if (isset($defaults[$doctorId])) {
            $u = $defaults[$doctorId];
            $_SESSION['doctor'] = [
                'id'          => $doctorId,
                'username'    => $u['username'],
                'full_name'   => $u['full_name'],
                'department'  => $u['department'],
                'room_number' => sprintf('%02d', intval($u['room_number']))
            ];
            jsonResponse(true, $_SESSION['doctor'], "Logged in as {$u['full_name']}");
            return;
        }
    }

    if (empty($username) || empty($password)) {
        jsonResponse(false, null, 'Username and Password are required', 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'DOCTOR'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['doctor'] = [
            'id'          => $user['id'],
            'username'    => $user['username'],
            'full_name'   => $user['full_name'],
            'department'  => $user['department'] ?: 'General Medicine',
            'room_number' => sprintf('%02d', intval($user['room_number'] ?: 3))
        ];
        jsonResponse(true, $_SESSION['doctor'], "Welcome back, {$user['full_name']}");
    } else {
        jsonResponse(false, null, 'Invalid username or password', 401);
    }
}

/**
 * Logout active doctor session
 */
function doctorLogout() {
    unset($_SESSION['doctor']);
    jsonResponse(true, null, 'Doctor session logged out');
}

/**
 * Get active doctor session & list registered doctors
 */
function getDoctorSession() {
    $pdo = getDBConnection();
    $active = $_SESSION['doctor'] ?? null;

    $stmt = $pdo->query("SELECT id, username, full_name, department, room_number FROM users WHERE role = 'DOCTOR' ORDER BY full_name ASC");
    $allDoctors = $stmt->fetchAll();

    if (empty($allDoctors)) {
        $allDoctors = [
            ['id' => -1, 'username' => 'dr.santos', 'full_name' => 'Dr. Sarah Santos, MD', 'department' => 'General Medicine', 'room_number' => '03'],
            ['id' => -2, 'username' => 'dr.reyes', 'full_name' => 'Dr. Alexander Reyes, MD', 'department' => 'Pediatrics', 'room_number' => '02'],
            ['id' => -3, 'username' => 'dr.cruz', 'full_name' => 'Dr. Raymond Cruz, MD', 'department' => 'Dental Clinic', 'room_number' => '04'],
        ];
    }

    jsonResponse(true, [
        'activeDoctor' => $active,
        'allDoctors'   => $allDoctors
    ], 'Session state retrieved');
}

/**
 * Helper to fetch active doctor session or fallback default
 */
function getActiveDoctor() {
    if (!empty($_SESSION['doctor'])) {
        return $_SESSION['doctor'];
    }
    return [
        'id'          => 0,
        'username'    => 'santos.md',
        'full_name'   => 'Dr. Sarah Santos, MD',
        'department'  => 'General Medicine',
        'room_number' => '03'
    ];
}

/**
 * Get current consultation state for Doctor Console
 */
function getDoctorState() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomTitle = "Room " . sprintf('%02d', intval($doc['room_number'] ?: 3));

    // Current patient in consultation for this doctor's room
    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE status = 'CALLING' AND (room_assigned = ? OR room_assigned IS NULL) ORDER BY called_at DESC LIMIT 1");
    $stmt->execute([$roomTitle]);
    $nowCalling = $stmt->fetch();

    if ($nowCalling) {
        $nowCalling['vitals'] = [
            'bp'     => $nowCalling['vitals_bp'] ?? '120/80',
            'temp'   => $nowCalling['vitals_temp'] ?? '36.8',
            'pulse'  => $nowCalling['vitals_pulse'] ?? '76',
            'spo2'   => $nowCalling['vitals_spo2'] ?? '99',
            'weight' => $nowCalling['vitals_weight'] ?? '65'
        ];
    }

    // Upcoming queue list matching department or all waiting
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'WAITING' ORDER BY is_priority DESC, created_at ASC");
    $upcomingRaw = $stmt->fetchAll();

    $upcomingQueue = [];
    foreach ($upcomingRaw as $item) {
        $item['ticket'] = $item['ticket_number'];
        $item['name'] = $item['patient_name'];
        $item['patientId'] = $item['patient_id'];
        $item['time'] = $item['time_slot'];
        $item['isPriority'] = (bool)$item['is_priority'];
        $item['vitals'] = [
            'bp'     => $item['vitals_bp'] ?? '120/80',
            'temp'   => $item['vitals_temp'] ?? '36.8',
            'pulse'  => $item['vitals_pulse'] ?? '76',
            'spo2'   => $item['vitals_spo2'] ?? '99',
            'weight' => $item['vitals_weight'] ?? '65'
        ];
        $upcomingQueue[] = $item;
    }

    // Completed count for active doctor's room today
    $compStmt = $pdo->prepare("SELECT COUNT(*) FROM queue_tickets WHERE status = 'COMPLETED' AND (room_assigned = ? OR doctor_name = ?)");
    $compStmt->execute([$roomTitle, $doc['full_name']]);
    $completedToday = (int)$compStmt->fetchColumn();

    jsonResponse(true, [
        'activeDoctor'   => $doc,
        'nowCalling'     => $nowCalling,
        'upcomingQueue'  => $upcomingQueue,
        'completedCount' => $completedToday
    ], 'Doctor state retrieved');
}

/**
 * Call next waiting patient in line
 */
function callNextPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 3));
    $roomTitle = "Room " . $roomNum;
    $docName = $doc['full_name'];

    // 1. Fetch next waiting patient (matching doctor's department or room first, priority first)
    $dept = $doc['department'] ?? 'General Medicine';
    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE status = 'WAITING' AND (department = ? OR room_assigned = ?) ORDER BY is_priority DESC, created_at ASC LIMIT 1");
    $stmt->execute([$dept, $roomTitle]);
    $nextPatient = $stmt->fetch();

    if (!$nextPatient) {
        // Fallback to any waiting patient across all departments
        $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'WAITING' ORDER BY is_priority DESC, created_at ASC LIMIT 1");
        $nextPatient = $stmt->fetch();
    }

    if (!$nextPatient) {
        jsonResponse(false, null, 'No waiting patients in queue', 404);
        return;
    }

    // Auto-complete any active ticket currently in consultation for this doctor's room
    $autoComp = $pdo->prepare("UPDATE queue_tickets SET status = 'COMPLETED', completed_at = NOW() WHERE status = 'CALLING' AND (room_assigned = ? OR doctor_name = ?)");
    $autoComp->execute([$roomTitle, $docName]);

    // 2. Update status to CALLING
    $updateStmt = $pdo->prepare("UPDATE queue_tickets SET status = 'CALLING', room_assigned = ?, doctor_name = ?, called_at = NOW() WHERE id = ?");
    $updateStmt->execute([$roomTitle, $docName, $nextPatient['id']]);

    // 3. Update room in clinic_rooms
    $roomStmt = $pdo->prepare("UPDATE clinic_rooms SET doctor_name = ?, department = ?, current_ticket = ?, status = 'BUSY' WHERE room_number = ?");
    $roomStmt->execute([$docName, $doc['department'], $nextPatient['ticket_number'], $roomNum]);

    // Log call event
    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('CALL_NEXT', ?, ?)");
    $logStmt->execute([$nextPatient['ticket_number'], "$docName called patient to $roomTitle"]);

    // Re-fetch updated record
    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE id = ?");
    $stmt->execute([$nextPatient['id']]);
    $calling = $stmt->fetch();

    $calling['vitals'] = [
        'bp'     => $calling['vitals_bp'] ?? '120/80',
        'temp'   => $calling['vitals_temp'] ?? '36.8',
        'pulse'  => $calling['vitals_pulse'] ?? '76',
        'spo2'   => $calling['vitals_spo2'] ?? '99',
        'weight' => $calling['vitals_weight'] ?? '65'
    ];

    jsonResponse(true, $calling, "Patient {$calling['ticket_number']} called to $roomTitle");
}

/**
 * Call a specific ticket number
 */
function callSpecificPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 3));
    $roomTitle = "Room " . $roomNum;
    $docName = $doc['full_name'];

    $ticket = sanitizeInput($_REQUEST['ticket'] ?? '');

    if (empty($ticket)) {
        jsonResponse(false, null, 'Ticket parameter required', 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE ticket_number = ?");
    $stmt->execute([$ticket]);
    $patient = $stmt->fetch();

    if (!$patient) {
        jsonResponse(false, null, "Ticket $ticket not found", 404);
    }

    // Auto-complete any active ticket currently in consultation for this doctor's room
    $autoComp = $pdo->prepare("UPDATE queue_tickets SET status = 'COMPLETED', completed_at = NOW() WHERE status = 'CALLING' AND (room_assigned = ? OR doctor_name = ?)");
    $autoComp->execute([$roomTitle, $docName]);

    $updateStmt = $pdo->prepare("UPDATE queue_tickets SET status = 'CALLING', room_assigned = ?, doctor_name = ?, called_at = NOW() WHERE id = ?");
    $updateStmt->execute([$roomTitle, $docName, $patient['id']]);

    $roomStmt = $pdo->prepare("UPDATE clinic_rooms SET doctor_name = ?, department = ?, current_ticket = ?, status = 'BUSY' WHERE room_number = ?");
    $roomStmt->execute([$docName, $doc['department'], $patient['ticket_number'], $roomNum]);

    jsonResponse(true, $patient, "Ticket $ticket called to $roomTitle");
}

/**
 * Save doctor consultation diagnosis & prescription
 */
function saveConsultation() {
    $pdo = getDBConnection();

    $ticketId     = intval($_POST['ticket_id'] ?? 0);
    $ticketNum    = sanitizeInput($_POST['ticket_number'] ?? '');
    $diagnosis    = sanitizeInput($_POST['diagnosis'] ?? '');
    $prescription = sanitizeInput($_POST['prescription'] ?? '');
    $bp           = sanitizeInput($_POST['vitals_bp'] ?? '120/80');
    $temp         = sanitizeInput($_POST['vitals_temp'] ?? '36.8');
    $pulse        = sanitizeInput($_POST['vitals_pulse'] ?? '76');

    if (!$ticketId && !$ticketNum) {
        jsonResponse(false, null, 'Ticket ID or number required to save consultation', 400);
    }

    $sql = "UPDATE queue_tickets SET 
            diagnosis = :diag, 
            prescription = :rx, 
            vitals_bp = :bp, 
            vitals_temp = :temp, 
            vitals_pulse = :pulse 
            WHERE id = :id OR ticket_number = :num";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':diag' => $diagnosis,
        ':rx'   => $prescription,
        ':bp'   => $bp,
        ':temp' => $temp,
        ':pulse'=> $pulse,
        ':id'   => $ticketId,
        ':num'  => $ticketNum
    ]);

    jsonResponse(true, null, 'Consultation diagnosis and prescription saved');
}

/**
 * Complete current consultation
 */
function completeConsultation() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 3));
    $roomTitle = "Room " . $roomNum;

    $ticketNum = sanitizeInput($_REQUEST['ticket_number'] ?? '');

    if (empty($ticketNum)) {
        // Grab current calling patient for this room/doctor
        $stmt = $pdo->prepare("SELECT ticket_number FROM queue_tickets WHERE status = 'CALLING' AND (room_assigned = ? OR doctor_name = ?) ORDER BY called_at DESC LIMIT 1");
        $stmt->execute([$roomTitle, $doc['full_name']]);
        $ticketNum = $stmt->fetchColumn();

        if (empty($ticketNum)) {
            $stmt = $pdo->query("SELECT ticket_number FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
            $ticketNum = $stmt->fetchColumn();
        }
    }

    if ($ticketNum) {
        $stmt = $pdo->prepare("UPDATE queue_tickets SET status = 'COMPLETED', completed_at = NOW() WHERE ticket_number = ?");
        $stmt->execute([$ticketNum]);

        $roomStmt = $pdo->prepare("UPDATE clinic_rooms SET current_ticket = '---', status = 'AVAILABLE' WHERE room_number = ?");
        $roomStmt->execute([$roomNum]);

        $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('COMPLETE', ?, ?)");
        $logStmt->execute([$ticketNum, "Consultation completed by {$doc['full_name']} in $roomTitle"]);

        jsonResponse(true, ['ticket_number' => $ticketNum], "Consultation completed for $ticketNum");
    } else {
        jsonResponse(false, null, 'No active patient in consultation to complete', 400);
    }
}

/**
 * Mark patient as Skipped / No Show
 */
function skipPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 3));
    $roomTitle = "Room " . $roomNum;

    $ticketNum = sanitizeInput($_REQUEST['ticket_number'] ?? '');

    if (empty($ticketNum)) {
        $stmt = $pdo->prepare("SELECT ticket_number FROM queue_tickets WHERE status = 'CALLING' AND (room_assigned = ? OR doctor_name = ?) ORDER BY called_at DESC LIMIT 1");
        $stmt->execute([$roomTitle, $doc['full_name']]);
        $ticketNum = $stmt->fetchColumn();

        if (empty($ticketNum)) {
            $stmt = $pdo->query("SELECT ticket_number FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
            $ticketNum = $stmt->fetchColumn();
        }
    }

    if ($ticketNum) {
        $stmt = $pdo->prepare("UPDATE queue_tickets SET status = 'CANCELLED' WHERE ticket_number = ?");
        $stmt->execute([$ticketNum]);

        $roomStmt = $pdo->prepare("UPDATE clinic_rooms SET current_ticket = '---', status = 'AVAILABLE' WHERE room_number = ?");
        $roomStmt->execute([$roomNum]);

        $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('SKIPPED', ?, ?)");
        $logStmt->execute([$ticketNum, "Patient skipped by {$doc['full_name']} in $roomTitle"]);

        jsonResponse(true, ['ticket_number' => $ticketNum], "Ticket $ticketNum marked as Skipped / No-Show");
    } else {
        jsonResponse(false, null, 'No active patient to skip', 400);
    }
}
