<?php
/**
 * CarePoint CAPQ - Live Waiting Lounge TV Display
 * Native PHP Root Route Handler
 */
require_once __DIR__ . '/config/db.php';
getDBConnection();
require __DIR__ . '/modules/display/index.php';
