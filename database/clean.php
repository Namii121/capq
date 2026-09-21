<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDBConnection();
    
    // Clear queue tickets, system logs, and non-admin user accounts
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE queue_tickets;");
    $pdo->exec("TRUNCATE TABLE system_logs;");
    $pdo->exec("DELETE FROM users WHERE username != 'admin';");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    // Execute full clean schema setup
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    
    $ticketCount = $pdo->query("SELECT COUNT(*) FROM queue_tickets")->fetchColumn();
    $logCount    = $pdo->query("SELECT COUNT(*) FROM system_logs")->fetchColumn();
    $userCount   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    echo "SUCCESS: Database cleaned successfully.\n";
    echo "Summary:\n";
    echo "  - queue_tickets: {$ticketCount} records\n";
    echo "  - system_logs:   {$logCount} records\n";
    echo "  - users:         {$userCount} seeded accounts preserved\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
