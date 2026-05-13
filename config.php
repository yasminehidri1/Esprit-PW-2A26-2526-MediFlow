<?php
/**
 * Database Configuration
 * Singleton Pattern for PDO Connection
 *
 * @package MediFlow
 * @version 1.0.0
 */

require_once __DIR__ . '/config_keys.php';

class config {
    private static $pdo = null;
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $dbname = "mediflow";
    
    public static function getGeminiApiKey(): string {
        return GEMINI_API_KEY;
    }

    public static function getOpenRouterApiKey(): string {
        return OPENROUTER_API_KEY;
    }

    //CAPTCHAv2
    private static $recaptcha_site_key = "6LfuaMUsAAAAAIoHHjWv2avAH21eXKxFTvDxtDpT";
    private static $recaptcha_secret_key = "6LfuaMUsAAAAAI7ovH5xCdgKsZ6PB8UHg6BQpvJs";

    // Google Gemini API — aistudio.google.com
    private static $claude_api_key = "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";

    // SMTP (PHPMailer) — configurer avec Gmail ou autre fournisseur
    private static $smtp_host     = "smtp.gmail.com";
    private static $smtp_port     = 587;
    private static $smtp_user     = "hafidhaganouni@gmail.com";
    private static $smtp_pass     = "swrf pfgl evbo nzkb";
    private static $smtp_from     = "hafidhaganouni@gmail.com";
    private static $smtp_from_name = "mediflow";
    
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
    
    /**
     * Get reCAPTCHA site key
     * 
     * @return string
     */
    public static function getRecaptchaSiteKey(): string {
        return RECAPTCHA_SITE_KEY;
    }

    public static function getRecaptchaSecretKey(): string {
        return RECAPTCHA_SECRET_KEY;
    }
    
    public static function getGoogleClientId(): string {
        return GOOGLE_CLIENT_ID;
    }

    public static function getGoogleClientSecret(): string {
        return GOOGLE_CLIENT_SECRET;
    }

    public static function getGoogleRedirectUri(): string {
        return GOOGLE_REDIRECT_URI;
    }

    public static function getSmtpHost(): string {
        return SMTP_HOST;
    }

    public static function getSmtpPort(): int {
        return SMTP_PORT;
    }

    public static function getSmtpEmail(): string {
        return SMTP_EMAIL;
    }

    public static function getSmtpPassword(): string {
        return SMTP_PASSWORD;
    }

    public static function getClaudeApiKey(): string {
        return self::$claude_api_key;
    }

    public static function getSmtpHost(): string     { return self::$smtp_host; }
    public static function getSmtpPort(): int        { return self::$smtp_port; }
    public static function getSmtpUser(): string     { return self::$smtp_user; }
    public static function getSmtpPass(): string     { return self::$smtp_pass; }
    public static function getSmtpFrom(): string     { return self::$smtp_from; }
    public static function getSmtpFromName(): string { return self::$smtp_from_name; }
}
?>
