<?php
/**
 * Module: Kiosk
 * File: actions.php
 * Native PHP Actions for Patient Self-Service Check-In & Triage Kiosk
 */

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? '';

if (!empty($action)) {
    switch ($action) {
        case 'checkin':
            kioskCheckin();
            break;

        case 'checkin_appointment':
            kioskCheckinAppointment();
            break;

        case 'get_rooms':
            getRooms();
            break;

        default:
            jsonResponse(false, null, 'Invalid kiosk action requested', 400);
    }
}

/**
 * Handle kiosk fast patient check-in
 */
function kioskCheckin() {
    $pdo = getDBConnection();

    $name       = sanitizeInput($_POST['name'] ?? 'Walk-in Patient');
    $phone      = sanitizeInput($_POST['phone'] ?? '09XX-XXX-XXXX');
    $dept       = 'General Consultation';
    $isPriority = !empty($_POST['is_priority']) ? 1 : 0;
    $complaint  = sanitizeInput($_POST['complaint'] ?? 'Self Check-In Consultation');

    $prefix = 'W';
    $roomAssigned = 'Consultation Room';
    $doctorName = 'Dr. Sarah Santos, MD';

    // Generate unique ticket number
    $stmt = $pdo->prepare("SELECT ticket_number FROM queue_tickets WHERE ticket_number LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute(["$prefix-%"]);
    $lastTicket = $stmt->fetchColumn();
    $num = $lastTicket ? (intval(substr($lastTicket, 2)) + 1) : 101;
    $ticketNumber = sprintf("%s-%03d", $prefix, $num);
    $patientId = 'PT-' . rand(10000, 99999);
    $timeNow = date('h:i A');

    $sql = "INSERT INTO queue_tickets 
            (ticket_number, patient_id, patient_name, age, gender, phone, type, time_slot, department, room_assigned, doctor_name, status, complaint, is_priority, created_at) 
            VALUES 
            (:ticket, :pid, :name, 35, 'Female', :phone, 'Walk-in', :time, :dept, :room, :doctor, 'WAITING', :complaint, :priority, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticket'    => $ticketNumber,
        ':pid'       => $patientId,
        ':name'      => $name,
        ':phone'     => $phone,
        ':time'      => $timeNow,
        ':dept'      => $dept,
        ':room'      => $roomAssigned,
        ':doctor'    => $doctorName,
        ':complaint' => $complaint,
        ':priority'  => $isPriority
    ]);

    // Calculate patients ahead
    $aheadStmt = $pdo->prepare("SELECT COUNT(*) FROM queue_tickets WHERE status = 'WAITING' AND id < (SELECT id FROM queue_tickets WHERE ticket_number = ?)");
    $aheadStmt->execute([$ticketNumber]);
    $ahead = (int)$aheadStmt->fetchColumn();
    $estWaitMins = $ahead * 8;

    // Log event
    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('KIOSK_CHECKIN', ?, ?)");
    $logStmt->execute([$ticketNumber, "Self-checkin at kiosk for $name ($dept)"]);

    jsonResponse(true, [
        'ticket_number' => $ticketNumber,
        'patient_id'    => $patientId,
        'patient_name'  => $name,
        'department'    => $dept,
        'doctor_name'   => $doctorName,
        'room_assigned' => $roomAssigned,
        'is_priority'   => (bool)$isPriority,
        'time'          => $timeNow,
        'patients_ahead'=> $ahead,
        'est_wait_min'  => $estWaitMins,
        'status'        => 'WAITING'
    ], 'Kiosk ticket issued successfully!');
}

/**
 * Validate and trap appointment code for Kiosk check-in
 */
function kioskCheckinAppointment() {
    $pdo = getDBConnection();
    $code = sanitizeInput($_REQUEST['code'] ?? '');

    if (empty($code)) {
        jsonResponse(false, null, 'Please enter your appointment reference code', 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM queue_tickets WHERE ticket_number = ? OR patient_id = ? OR patient_name LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$code, $code, "%$code%"]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        jsonResponse(false, null, "Invalid Appointment Code: '$code'. No matching booking record found. Please verify your reference code or register as a Walk-in patient.", 404);
    }

    if ($ticket['status'] === 'COMPLETED') {
        jsonResponse(false, ['ticket' => $ticket], "Appointment {$ticket['ticket_number']} has already been completed today.", 400);
    }

    if ($ticket['status'] === 'CANCELLED') {
        jsonResponse(false, ['ticket' => $ticket], "Appointment {$ticket['ticket_number']} was cancelled or expired.", 400);
    }

    if ($ticket['status'] === 'CALLING') {
        jsonResponse(true, [
            'ticket'          => $ticket,
            'ticket_number'   => $ticket['ticket_number'],
            'patient_name'    => $ticket['patient_name'],
            'department'      => $ticket['department'],
            'doctor_name'     => $ticket['doctor_name'] ?: 'Dr. Sarah Santos, MD',
            'room_assigned'   => $ticket['room_assigned'] ?: 'Consultation Room',
            'time_slot'       => $ticket['time_slot'] ?: 'Active',
            'patients_ahead'  => 0,
            'est_wait_min'    => 0,
            'status'          => 'CALLING',
            'display_text'    => "PROCEED TO {$ticket['room_assigned']} NOW! Doctor is calling your ticket."
        ], "Ticket {$ticket['ticket_number']} is currently being called to {$ticket['room_assigned']}!");
    }

    // Calculate position in line for waiting tickets
    $ahead = 0;
    if ($ticket['status'] === 'WAITING') {
        $posStmt = $pdo->prepare("SELECT COUNT(*) FROM queue_tickets WHERE status = 'WAITING' AND (department = ? OR room_assigned = ?) AND id < ?");
        $posStmt->execute([$ticket['department'], $ticket['room_assigned'], $ticket['id']]);
        $ahead = (int)$posStmt->fetchColumn();
    }
    $estWaitMins = $ahead * 8;

    // Log kiosk check-in validation
    $logStmt = $pdo->prepare("INSERT INTO system_logs (event_type, ticket_number, details) VALUES ('KIOSK_APPOINTMENT_CHECKIN', ?, ?)");
    $logStmt->execute([$ticket['ticket_number'], "Validated appointment code '$code' for {$ticket['patient_name']} ({$ticket['department']})"]);

    jsonResponse(true, [
        'ticket'          => $ticket,
        'ticket_number'   => $ticket['ticket_number'],
        'patient_name'    => $ticket['patient_name'],
        'department'      => $ticket['department'],
        'doctor_name'     => $ticket['doctor_name'] ?: 'Dr. Sarah Santos, MD',
        'room_assigned'   => $ticket['room_assigned'] ?: 'Consultation Room',
        'time_slot'       => $ticket['time_slot'] ?: 'Checked-in',
        'patients_ahead'  => $ahead,
        'est_wait_min'    => $estWaitMins,
        'status'          => 'WAITING'
    ], "Appointment Code '$code' Validated! Checked-in as {$ticket['ticket_number']}.");
}

/**
 * Get active clinic rooms for kiosk display
 */
function getRooms() {
    $rooms = [
        [
            'id'          => 1,
            'room_number' => '01',
            'room_name'   => 'Consultation Room',
            'doctor_name' => 'Dr. Sarah Santos, MD',
            'department'  => 'General Consultation',
            'status'      => 'AVAILABLE'
        ]
    ];
    jsonResponse(true, $rooms, 'Clinic room retrieved');
}
