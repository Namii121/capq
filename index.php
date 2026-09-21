<?php
/**
 * CarePoint CAPQ - Online Appointment Booking & Queue Status Portal
 * Native PHP Root Route Handler
 */
require_once __DIR__ . '/config/db.php';
getDBConnection();
require __DIR__ . '/modules/booking/index.php';
