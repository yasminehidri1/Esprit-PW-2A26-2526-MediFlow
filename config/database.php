<?php
/**
 * Database Configuration — MediFlow Magazine Module
 * PDO connection to the mediflow database (MariaDB via XAMPP)
 */

class Database {
    private static $instance = null;
    private $connection;

    private $host = '127.0.0.1';
    private $dbname = 'mediflow';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    /**
     * Private constructor — establishes PDO connection
     */
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get the singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}
}
