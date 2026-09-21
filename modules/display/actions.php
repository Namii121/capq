<?php
/**
 * Module: Display
 * File: actions.php
 * Native PHP Actions for Live TV Queue Display Screen
 */

require_once __DIR__ . '/../../config/db.php';

$action = $_REQUEST['action'] ?? 'get_live_state';

switch ($action) {
    case 'get_live_state':
        getLiveState();
        break;

    default:
        jsonResponse(false, null, 'Invalid display action requested', 400);
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

    // Format vitals JSON array
    if ($nowCalling) {
        $nowCalling['vitals'] = [
            'bp'     => $nowCalling['vitals_bp'] ?? '120/80',
            'temp'   => $nowCalling['vitals_temp'] ?? '36.8',
            'pulse'  => $nowCalling['vitals_pulse'] ?? '76',
            'spo2'   => $nowCalling['vitals_spo2'] ?? '99',
            'weight' => $nowCalling['vitals_weight'] ?? '65'
        ];
    }

    // 2. Fetch all clinic rooms & calculate their next waiting queue
    $stmt = $pdo->query("SELECT * FROM clinic_rooms ORDER BY room_number ASC");
    $rooms = $stmt->fetchAll();

    foreach ($rooms as &$room) {
        $room['room'] = $room['room_number'];
        $room['name'] = $room['room_name'];
        $room['doctor'] = $room['doctor_name'];
        $room['ticket'] = (!empty($room['current_ticket']) && $room['current_ticket'] !== '---') ? $room['current_ticket'] : '---';
        $room['color'] = $room['color_theme'];

        // Get actual waiting tickets for this department or room
        $dept = $room['department'];
        $nextStmt = $pdo->prepare("SELECT ticket_number FROM queue_tickets WHERE status = 'WAITING' AND (department = ? OR room_assigned = ?) ORDER BY is_priority DESC, created_at ASC LIMIT 2");
        $nextStmt->execute([$dept, "Room {$room['room_number']}"]);
        $nextTickets = $nextStmt->fetchAll(PDO::FETCH_COLUMN);

        $room['next'] = !empty($nextTickets) ? $nextTickets : [];
    }

    // 3. Fetch upcoming waiting queue
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

    // Completed tickets count
    $compStmt = $pdo->query("SELECT COUNT(*) FROM queue_tickets WHERE status = 'COMPLETED'");
    $completedCount = (int)$compStmt->fetchColumn();

    jsonResponse(true, [
        'nowCalling'     => $nowCalling,
        'rooms'          => $rooms,
        'upcomingQueue'  => $upcomingQueue,
        'completedCount' => $completedCount,
        'server_time'    => date('h:i A')
    ], 'Live TV queue state loaded');
}
