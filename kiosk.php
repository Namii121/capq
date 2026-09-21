<?php
/**
 * CarePoint CAPQ - Self-Service Check-In Kiosk
 * Native PHP Root Route Handler
 */
require_once __DIR__ . '/config/db.php';
getDBConnection();
require __DIR__ . '/modules/kiosk/index.php';
