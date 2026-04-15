<?php
/**
 * Database Configuration — MediFlow
 * Uses PDO singleton pattern.
 */

class Database {
    private static ?PDO $instance = null;

    // ── Edit these to match your local environment ──────────────
    private const HOST    = 'localhost';
    private const DBNAME  = 'mediflow';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';
    // ─────────────────────────────────────────────────────────────

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::HOST,
                self::DBNAME,
                self::CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                // In production, log the error instead of displaying it
                http_response_code(500);
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }
        return self::$instance;
    }
}
