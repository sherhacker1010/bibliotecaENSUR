<?php
// Detect Environment
$is_production = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'iensur.edu.co') !== false || strpos($_SERVER['HTTP_HOST'], 'wickdevs') !== false));

if ($is_production) {
    // Production Credentials
    defined('DB_HOST') or define('DB_HOST', 'localhost');
    defined('DB_USER') or define('DB_USER', 'wickdevs_biblioteca');
    defined('DB_PASS') or define('DB_PASS', 'o#plxvm6~H(2');
    defined('DB_NAME') or define('DB_NAME', 'wickdevs_biblioteca');
} else {
    // Local Credentials
    defined('DB_HOST') or define('DB_HOST', 'localhost');
    defined('DB_USER') or define('DB_USER', 'root');
    defined('DB_PASS') or define('DB_PASS', '');
    defined('DB_NAME') or define('DB_NAME', 'biblioteca_db');
}

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Proxy methods to PDO
    public function query($sql) {
        return $this->pdo->query($sql);
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
