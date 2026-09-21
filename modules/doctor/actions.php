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

if (!empty($action)) {
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

        case 'prepare_next':
            prepareNextPatient();
            break;

        case 'prepare_patient':
            prepareSpecificPatient();
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
        'id'          => 1,
        'username'    => 'admin',
        'full_name'   => 'Dr. Sarah Santos, MD',
        'department'  => 'General Consultation',
        'room_number' => '01'
    ];
}

/**
 * Get current consultation state for Doctor Console
 */
function getDoctorState() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();

    // Current patient in consultation
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
    $nowCalling = $stmt->fetch();



    // Patient currently preparing (On Deck)
    $prepStmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'PREPARING' ORDER BY id ASC LIMIT 1");
    $nowPreparing = $prepStmt->fetch() ?: null;
    if ($nowPreparing) {
        $nowPreparing['ticket'] = $nowPreparing['ticket_number'];
        $nowPreparing['name'] = $nowPreparing['patient_name'];
    }

    // Upcoming queue list (includes WAITING and PREPARING)
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status IN ('WAITING', 'PREPARING') ORDER BY FIELD(status, 'PREPARING', 'WAITING'), is_priority DESC, created_at ASC");
    $upcomingRaw = $stmt->fetchAll();

    $upcomingQueue = [];
    foreach ($upcomingRaw as $item) {
        $item['ticket'] = $item['ticket_number'];
        $item['name'] = $item['patient_name'];
        $item['patientId'] = $item['patient_id'];
        $item['time'] = $item['time_slot'];
        $item['isPriority'] = (bool)$item['is_priority'];
        $item['isPreparing'] = ($item['status'] === 'PREPARING');
        $upcomingQueue[] = $item;
    }

    // Completed count for today
    $compStmt = $pdo->query("SELECT COUNT(*) FROM queue_tickets WHERE status = 'COMPLETED'");
    $completedToday = (int)$compStmt->fetchColumn();

    jsonResponse(true, [
        'activeDoctor'   => $doc,
        'nowCalling'     => $nowCalling,
        'nowPreparing'   => $nowPreparing,
        'upcomingQueue'  => $upcomingQueue,
        'completedCount' => $completedToday
    ], 'Doctor state retrieved');
}

/**
 * Mark next waiting patient as PREPARING (On Deck / Standby)
 */
function prepareNextPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $docName = $doc['full_name'];
    $roomTitle = "Consultation Room";

    // Fetch next waiting patient (priority first, not yet preparing/calling)
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'WAITING' ORDER BY is_priority DESC, created_at ASC LIMIT 1");
    $nextPatient = $stmt->fetch();

    if (!$nextPatient) {
        jsonResponse(false, null, 'No waiting patients in queue to prepare', 404);
        return;
    }

    $updateStmt = $pdo->prepare("UPDATE queue_tickets SET status = 'PREPARING', room_assigned = ?, doctor_name = ? WHERE id = ?");
    $updateStmt->execute([$roomTitle, $docName, $nextPatient['id']]);

    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('PREPARE_NEXT', ?, ?)");
    $logStmt->execute([$nextPatient['ticket_number'], "$docName marked patient to prepare for $roomTitle"]);

    $nextPatient['status'] = 'PREPARING';
    $nextPatient['ticket'] = $nextPatient['ticket_number'];
    jsonResponse(true, $nextPatient, "Patient {$nextPatient['ticket_number']} set to Prepare / Standby");
}

/**
 * Mark a specific ticket as PREPARING
 */
function prepareSpecificPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomTitle = "Consultation Room";
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

    $updateStmt = $pdo->prepare("UPDATE queue_tickets SET status = 'PREPARING', room_assigned = ?, doctor_name = ? WHERE id = ?");
    $updateStmt->execute([$roomTitle, $docName, $patient['id']]);

    $patient['status'] = 'PREPARING';
    $patient['ticket'] = $patient['ticket_number'];
    jsonResponse(true, $patient, "Ticket $ticket set to Preparing / Standby");
}

/**
 * Call next waiting patient in line
 */
function callNextPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $docName = $doc['full_name'];
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 1));
    $roomTitle = "Consultation Room";

    // Fetch next patient (PREPARING on deck first, then WAITING priority first)
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status IN ('PREPARING', 'WAITING') ORDER BY FIELD(status, 'PREPARING', 'WAITING'), is_priority DESC, created_at ASC LIMIT 1");
    $nextPatient = $stmt->fetch();

    if (!$nextPatient) {
        jsonResponse(false, null, 'No waiting patients in queue', 404);
        return;
    }

    // Auto-complete any active ticket currently in consultation
    $pdo->query("UPDATE queue_tickets SET status = 'COMPLETED', completed_at = NOW() WHERE status = 'CALLING'");

    // 2. Update status to CALLING
    $updateStmt = $pdo->prepare("UPDATE queue_tickets SET status = 'CALLING', room_assigned = ?, doctor_name = ?, called_at = NOW() WHERE id = ?");
    $updateStmt->execute([$roomTitle, $docName, $nextPatient['id']]);

    // Log call event
    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('CALL_NEXT', ?, ?)");
    $logStmt->execute([$nextPatient['ticket_number'], "$docName called patient to $roomTitle"]);

    // Re-fetch updated record
    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE id = ?");
    $stmt->execute([$nextPatient['id']]);
    $calling = $stmt->fetch();



    jsonResponse(true, $calling, "Patient {$calling['ticket_number']} called to $roomTitle");
}

/**
 * Call a specific ticket number
 */
function callSpecificPatient() {
    $pdo = getDBConnection();
    $doc = getActiveDoctor();
    $roomNum = sprintf('%02d', intval($doc['room_number'] ?: 1));
    $roomTitle = "Consultation Room";
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

    if (!$ticketId && !$ticketNum) {
        jsonResponse(false, null, 'Ticket ID or number required to save consultation', 400);
    }

    $sql = "UPDATE queue_tickets SET 
            diagnosis = :diag, 
            prescription = :rx 
            WHERE id = :id OR ticket_number = :num";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':diag' => $diagnosis,
        ':rx'   => $prescription,
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
    $roomTitle = "Consultation Room";

    $ticketNum    = sanitizeInput($_REQUEST['ticket_number'] ?? '');
    $diagnosis    = sanitizeInput($_POST['diagnosis'] ?? '');
    $prescription = sanitizeInput($_POST['prescription'] ?? '');

    if (empty($ticketNum)) {
        $stmt = $pdo->query("SELECT ticket_number FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
        $ticketNum = $stmt->fetchColumn();
    }

    if ($ticketNum) {
        $sql = "UPDATE queue_tickets SET status = 'COMPLETED', completed_at = NOW()";
        $params = [];
        if (!empty($diagnosis)) {
            $sql .= ", diagnosis = ?";
            $params[] = $diagnosis;
        }
        if (!empty($prescription)) {
            $sql .= ", prescription = ?";
            $params[] = $prescription;
        }
        $sql .= " WHERE ticket_number = ?";
        $params[] = $ticketNum;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

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
    $roomTitle = "Consultation Room";

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

        $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('SKIPPED', ?, ?)");
        $logStmt->execute([$ticketNum, "Patient skipped by {$doc['full_name']} in $roomTitle"]);

        jsonResponse(true, ['ticket_number' => $ticketNum], "Ticket $ticketNum marked as Skipped / No-Show");
    } else {
        jsonResponse(false, null, 'No active patient to skip', 400);
    }
}
