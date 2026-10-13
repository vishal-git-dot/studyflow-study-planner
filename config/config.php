<?php
declare(strict_types=1);

/*
 * StudyFlow configuration.
 * For XAMPP with its own MySQL on port 3307, change DB_PORT to 3307.
 * If using the existing MySQL80 service on port 3306, keep DB_PORT at 3306.
 */
define('APP_NAME', 'StudyFlow');
define('APP_URL', ''); // Example: http://localhost/StudyFlow/public (optional; leave blank for auto paths)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'studyflow');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SESSION_NAME', 'studyflow_session');
define('APP_TIMEZONE', 'Asia/Kolkata');

date_default_timezone_set(APP_TIMEZONE);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_name(SESSION_NAME);
    session_start();
}
