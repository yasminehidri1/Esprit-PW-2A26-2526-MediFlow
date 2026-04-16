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
    
    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "mediflow";
            
            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                die("Erreur de connexion: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
