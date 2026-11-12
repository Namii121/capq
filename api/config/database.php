<?php
/**
 * CarePoint CAPQ - Database Connection Manager (PDO)
 * Automatically detects XAMPP MySQL defaults (3306 or 3307)
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    private $isConnected = false;

    // Configurable connection parameters
    private $host = "127.0.0.1";
    private $dbname = "capq_db";
    private $username = "root";
    private $password = "";
    private $ports = [3306, 3307];

    private function __construct() {
        // Allow environment overrides if defined
        if (getenv('DB_HOST')) $this->host = getenv('DB_HOST');
        if (getenv('DB_NAME')) $this->dbname = getenv('DB_NAME');
        if (getenv('DB_USER')) $this->username = getenv('DB_USER');
        if (getenv('DB_PASS')) $this->password = getenv('DB_PASS');

        // Attempt connection across standard XAMPP ports
        foreach ($this->ports as $port) {
            try {
                $dsn = "mysql:host={$this->host};port={$port};dbname={$this->dbname};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 2
                ];
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
                $this->isConnected = true;
                break;
            } catch (PDOException $e) {
                // If database does not exist, try creating it automatically
                if (strpos($e->getMessage(), 'Unknown database') !== false) {
                    try {
                        $rootDsn = "mysql:host={$this->host};port={$port};charset=utf8mb4";
                        $rootPdo = new PDO($rootDsn, $this->username, $this->password);
                        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                        
                        // Retry connect with dbname
                        $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
                        $this->isConnected = true;
                        
                        // Auto-seed schema from database.sql if file exists
                        $sqlPath = __DIR__ . '/../../database.sql';
                        if (file_exists($sqlPath)) {
                            $sql = file_get_contents($sqlPath);
                            $this->pdo->exec($sql);
                        }
                        break;
                    } catch (Exception $initEx) {
                        // Continue to next port check
                    }
                }
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function isConnected() {
        return $this->isConnected;
    }
}
