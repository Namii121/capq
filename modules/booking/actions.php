<?php
/**
 * Module: Booking
 * File: actions.php
 * Native PHP Actions for Patient Online Appointment & Ticket Tracking
 */

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? '';

if (!empty($action)) {
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

        case 'get_departments':
            getDepartments();
            break;

        default:
            jsonResponse(false, null, 'Invalid booking action requested', 400);
    }
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
    $dept       = 'General Consultation';
    $slot       = sanitizeInput($_POST['time_slot'] ?? '10:30 AM');
    $complaint  = sanitizeInput($_POST['complaint'] ?? 'General Consultation');
    $isPriority = !empty($_POST['is_priority']) ? 1 : 0;

    if (empty($name) || empty($phone)) {
        jsonResponse(false, null, 'Patient name and phone number are required', 400);
    }

    $prefix = 'A';
    $roomAssigned = 'Consultation Room';
    $doctorName = 'Dr. Sarah Santos, MD';

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
    $logStmt->execute([$ticketNumber, "Booked online appointment for $name"]);

    jsonResponse(true, [
        'ticket_number' => $ticketNumber,
        'patient_id'    => $patientId,
        'patient_name'  => $name,
        'department'    => $dept,
        'doctor_name'   => $doctorName,
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
 * Fetch available attending doctors
 */
function getDoctors() {
    $doctors = [
        [
            'full_name'   => 'Dr. Sarah Santos, MD',
            'department'  => 'General Consultation',
            'room_number' => '01'
        ]
    ];
    jsonResponse(true, ['type' => 'users', 'list' => $doctors], 'Attending physician retrieved');
}

/**
 * Fetch available departments that currently have an assigned doctor
 */
function getDepartments() {
    $departments = getAvailableDepartmentsList();
    jsonResponse(true, $departments, 'Available departments retrieved');
}

/**
 * Helper function to retrieve distinct departments for solo clinic
 */
function getAvailableDepartmentsList() {
    return [
        [
            'value' => 'General Consultation',
            'label' => 'General Consultation & Family Medicine'
        ]
    ];
}
