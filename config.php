<?php
/**
 * Database Configuration
 * Singleton Pattern for PDO Connection
 * 
 * @package MediFlow
 * @version 1.0.0
 */

class config {
    private static $pdo = null;
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $dbname = "mediflow";
    
    public static function getConnexion() {
        // Validate or create connection
        if (!isset(self::$pdo) || !self::isConnected()) {
            self::$pdo = self::createConnection();
        }
        return self::$pdo;
    }
    
    /**
     * Check if current connection is valid
     * 
     * @return bool
     */
    private static function isConnected(): bool {
        try {
            if (self::$pdo === null) {
                return false;
            }
            // Test connection with a simple query
            self::$pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Create a new database connection
     * 
     * @return PDO
     * @throws Exception
     */
    private static function createConnection(): PDO {
        try {
            $dsn = "mysql:host=" . self::$servername . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            
            $pdo = new PDO($dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            return $pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion: " . $e->getMessage());
        }
    }
}
?>
