<?php
/**
 * Module: Display
 * File: actions.php
 * Native PHP Actions for Live TV Queue Display Screen
 */

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? 'get_live_state';

if (!empty($action)) {
    switch ($action) {
        case 'get_live_state':
            getLiveState();
            break;

        default:
            jsonResponse(false, null, 'Invalid display action requested', 400);
    }
}

/**
 * Returns synchronized real-time state for TV Queue Display
 */
function getLiveState() {
    $pdo = getDBConnection();

    // 1. Fetch ticket currently being called
    $stmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'CALLING' ORDER BY called_at DESC LIMIT 1");
    $nowCalling = $stmt->fetch();

    if (!$nowCalling) {
        $nowCalling = null;
    }

    // Fetch ticket currently preparing (On Deck)
    $prepStmt = $pdo->query("SELECT * FROM queue_tickets WHERE status = 'PREPARING' ORDER BY id ASC LIMIT 1");
    $nowPreparing = $prepStmt->fetch() ?: null;

    // 2. Fetch next waiting queue
    $nextStmt = $pdo->query("SELECT ticket_number FROM queue_tickets WHERE status IN ('WAITING', 'PREPARING') ORDER BY FIELD(status, 'PREPARING', 'WAITING'), is_priority DESC, created_at ASC LIMIT 2");
    $nextTickets = $nextStmt->fetchAll(PDO::FETCH_COLUMN);

    $currentTicket = $nowCalling ? ($nowCalling['ticket_number'] ?? '---') : '---';

    $rooms = [
        [
            'id'             => 1,
            'room_number'    => '01',
            'room'           => '01',
            'room_name'      => 'Consultation Room',
            'name'           => 'Consultation Room',
            'doctor_name'    => 'Dr. Sarah Santos, MD',
            'doctor'         => 'Dr. Sarah Santos, MD',
            'department'     => 'General Consultation',
            'current_ticket' => $currentTicket,
            'ticket'         => $currentTicket,
            'status'         => $nowCalling ? 'BUSY' : 'AVAILABLE',
            'color'          => 'teal',
            'next'           => !empty($nextTickets) ? $nextTickets : ['--']
        ]
    ];

    // 3. Fetch upcoming waiting queue
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

    // Completed tickets count
    $compStmt = $pdo->query("SELECT COUNT(*) FROM queue_tickets WHERE status = 'COMPLETED'");
    $completedCount = (int)$compStmt->fetchColumn();

    jsonResponse(true, [
        'nowCalling'     => $nowCalling,
        'nowPreparing'   => $nowPreparing,
        'rooms'          => $rooms,
        'upcomingQueue'  => $upcomingQueue,
        'completedCount' => $completedCount,
        'server_time'    => date('h:i A')
    ], 'Live TV queue state loaded');
}
