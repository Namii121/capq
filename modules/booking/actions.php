<?php
/**
 * Module: Booking
 * File: actions.php
 * Native PHP Actions for Patient Online Appointment & Ticket Tracking
 */

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'book_appointment':
        bookAppointment();
        break;

    case 'track_appointment':
        trackAppointment();
        break;

    case 'get_hero_serving':
        getHeroServing();
        break;

    case 'get_doctors':
        getDoctors();
        break;

    default:
        jsonResponse(false, null, 'Invalid booking action requested', 400);
}

/**
 * Handle new online appointment booking
 */
function bookAppointment() {
    $pdo = getDBConnection();
    
    $name       = sanitizeInput($_POST['name'] ?? '');
    $phone      = sanitizeInput($_POST['phone'] ?? '');
    $age        = intval($_POST['age'] ?? 35);
    $gender     = sanitizeInput($_POST['gender'] ?? 'Female');
    $dept       = sanitizeInput($_POST['department'] ?? 'General Medicine');
    $slot       = sanitizeInput($_POST['time_slot'] ?? '10:30 AM');
    $complaint  = sanitizeInput($_POST['complaint'] ?? 'General Consultation');
    $isPriority = !empty($_POST['is_priority']) ? 1 : 0;

    if (empty($name) || empty($phone)) {
        jsonResponse(false, null, 'Patient name and phone number are required', 400);
    }

    // Determine prefix and doctor assignment based on department
    $prefix = 'A';
    $roomAssigned = 'Room 03';
    $doctorName = 'Dr. Sarah Santos, MD';

    if (stripos($dept, 'Pedia') !== false) {
        $prefix = 'P';
        $roomAssigned = 'Room 02';
        $doctorName = 'Dr. Patricia Lim, MD';
    } else if (stripos($dept, 'Dental') !== false) {
        $prefix = 'D';
        $roomAssigned = 'Room 04';
        $doctorName = 'Dr. Michael Tan, DMD';
    } else if (stripos($dept, 'Triage') !== false) {
        $prefix = 'T';
        $roomAssigned = 'Room 01';
        $doctorName = 'Nurse Station 1';
    } else if (stripos($dept, 'Pharm') !== false || stripos($dept, 'Bill') !== false) {
        $prefix = 'B';
        $roomAssigned = 'Room 05';
        $doctorName = 'Prescription Counter';
    }

    // Check registered doctors in users table for matching department
    $docStmt = $pdo->prepare("SELECT full_name, room_number FROM users WHERE role = 'DOCTOR' AND department LIKE ? LIMIT 1");
    $docStmt->execute(["%$dept%"]);
    $regDoc = $docStmt->fetch();
    if ($regDoc) {
        $doctorName = $regDoc['full_name'];
        if ($regDoc['room_number']) {
            $roomAssigned = "Room " . sprintf('%02d', intval($regDoc['room_number']));
        }
    }

    // Generate unique ticket number
    $stmt = $pdo->prepare("SELECT ticket_number FROM queue_tickets WHERE ticket_number LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute(["$prefix-%"]);
    $lastTicket = $stmt->fetchColumn();
    $num = $lastTicket ? (intval(substr($lastTicket, 2)) + 1) : 101;
    $ticketNumber = sprintf("%s-%03d", $prefix, $num);
    $patientId = 'PT-' . rand(10000, 99999);

    $sql = "INSERT INTO queue_tickets 
            (ticket_number, patient_id, patient_name, age, gender, phone, type, time_slot, department, room_assigned, doctor_name, status, complaint, is_priority, created_at) 
            VALUES 
            (:ticket, :pid, :name, :age, :gender, :phone, 'Appointment', :slot, :dept, :room, :doctor, 'WAITING', :complaint, :priority, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticket'    => $ticketNumber,
        ':pid'       => $patientId,
        ':name'      => $name,
        ':age'       => $age,
        ':gender'    => $gender,
        ':phone'     => $phone,
        ':slot'      => $slot,
        ':dept'      => $dept,
        ':room'      => $roomAssigned,
        ':doctor'    => $doctorName,
        ':complaint' => $complaint,
        ':priority'  => $isPriority
    ]);

    // Log event
    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('BOOKING', ?, ?)");
    $logStmt->execute([$ticketNumber, "Booked online appointment for $name ($dept)"]);

    jsonResponse(true, [
        'ticket_number' => $ticketNumber,
        'patient_id'    => $patientId,
        'patient_name'  => $name,
        'department'    => $dept,
        'doctor_name'   => 'Dr. Sarah Santos, MD',
        'time_slot'     => $slot,
        'status'        => 'WAITING'
    ], 'Appointment booked successfully!');
}

/**
 * Track an existing appointment by ticket code or patient name
 */
function trackAppointment() {
    $pdo = getDBConnection();
    $code = sanitizeInput($_REQUEST['code'] ?? '');

    if (empty($code)) {
        jsonResponse(false, null, 'Please provide a ticket number or lookup code', 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE ticket_number = ? OR patient_id = ? OR patient_name LIKE ?");
    $stmt->execute([$code, $code, "%$code%"]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        jsonResponse(false, null, "No appointment or ticket found matching '$code'", 404);
    }

    // Calculate position in line if waiting
    $position = 0;
    $estWaitMins = 0;

    if ($ticket['status'] === 'WAITING') {
        $posStmt = $pdo->prepare("SELECT COUNT(*) FROM queue_tickets WHERE status = 'WAITING' AND id < ?");
        $posStmt->execute([$ticket['id']]);
        $position = $posStmt->fetchColumn() + 1;
        $estWaitMins = $position * 8; // Avg 8 mins per patient
    }

    jsonResponse(true, [
        'ticket'       => $ticket,
        'position'     => $position,
        'est_wait_min' => $estWaitMins,
        'display_text' => $ticket['status'] === 'CALLING' 
            ? "PROCEED TO {$ticket['room_assigned']} NOW!" 
            : ($ticket['status'] === 'COMPLETED' ? "Consultation Completed" : "In Queue (Position #$position, ~{$estWaitMins} mins wait)")
    ], 'Ticket status retrieved');
}

/**
 * Get current ticket being called
 */
function getHeroServing() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
    $calling = $stmt->fetch();

    if (!$calling) {
        $calling = null;
    }

    jsonResponse(true, $calling, 'Hero serving ticket loaded');
}

/**
 * Fetch available attending doctors by department
 */
function getDoctors() {
    $pdo = getDBConnection();
    
    // Check registered doctors in users table
    $stmt = $pdo->query("SELECT full_name, department, room_number FROM users WHERE role = 'DOCTOR'");
    $doctors = $stmt->fetchAll();

    if (empty($doctors)) {
        // Return default department room specialists from clinic_rooms table
        $stmt = $pdo->query("SELECT room_number, room_name, doctor_name, department FROM clinic_rooms ORDER BY room_number ASC");
        $rooms = $stmt->fetchAll();
        jsonResponse(true, ['type' => 'rooms', 'list' => $rooms], 'Default room specialists retrieved');
    } else {
        jsonResponse(true, ['type' => 'users', 'list' => $doctors], 'Registered doctors retrieved');
    }
}
