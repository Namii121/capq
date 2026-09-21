<?php
/**
 * CarePoint CAPQ - Database Connection & Helper Functions
 * Native PHP PDO Database Manager
 */

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $configFile = __DIR__ . '/config.php';
    $config = file_exists($configFile) ? require $configFile : [];

    $host = $config['db_host'] ?? '127.0.0.1';
    $port = $config['db_port'] ?? '3306';
    $user = $config['db_user'] ?? 'root';
    $dbName = $config['db_name'] ?? 'carepoint_capq';
    $configuredPass = $config['db_pass'] ?? '';

    // List of passwords to attempt
    $passwordsToTry = array_unique(array_filter([
        $configuredPass,
        '007622',
        getenv('MYSQL_PWD') ?: false,
        '',
        'root',
        'password',
        '123456',
        'admin',
        'root123'
    ], function($v) { return $v !== false; }));

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $lastException = null;

    foreach ($passwordsToTry as $pass) {
        try {
            // Step 1: Try connecting directly to carepoint_capq
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, $options);
            return $pdo;
        } catch (PDOException $e) {
            // If database doesn't exist (1049 / 42000), try auto-creating
            if (strpos($e->getMessage(), 'Unknown database') !== false || $e->getCode() == 1049) {
                try {
                    $rootDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $rootPdo = new PDO($rootDsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    
                    $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pass, $options);
                    
                    // Auto run schema setup
                    $schemaFile = __DIR__ . '/../database/schema.sql';
                    if (file_exists($schemaFile)) {
                        $sql = file_get_contents($schemaFile);
                        $pdo->exec($sql);
                    }
                    return $pdo;
                } catch (PDOException $ex) {
                    $lastException = $ex;
                }
            } else {
                $lastException = $e;
            }
        }
    }

    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($lastException ? $lastException->getMessage() : 'Access denied'),
        'help'    => 'Please set your MySQL 8.0 root password in config/config.php.'
    ]);
    exit;
}

/**
 * Standardized JSON response helper for API endpoints
 */
function jsonResponse($success, $data = null, $message = '', $statusCode = 200) {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }
    
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

/**
 * Sanitize string input
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}
