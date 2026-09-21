<?php
/**
 * CarePoint CAPQ - Central Clinic Administration Dashboard
 * Native PHP Root Route Handler
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';
getDBConnection();
require __DIR__ . '/modules/admin/index.php';
