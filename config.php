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
    
    //CAPTCHAv2
    private static $recaptcha_site_key = "6LfuaMUsAAAAAIoHHjWv2avAH21eXKxFTvDxtDpT";
    private static $recaptcha_secret_key = "6LfuaMUsAAAAAI7ovH5xCdgKsZ6PB8UHg6BQpvJs";
    
    // Google OAuth 2.0
    private static $google_client_id = "569727586604-f0c5ncvdb90m1lgc845a88m8urutcr70.apps.googleusercontent.com";
    private static $google_client_secret = "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";
    private static $google_redirect_uri = "http://localhost/integration/auth/google-callback";
    
    // Gemini API (for chatbot)
    private static $gemini_api_key = "YOUR_GEMINI_API_KEY_HERE";
    
    // OpenRouter API (for chatbot)
    private static $openrouter_api_key = "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";
    
    public static function getGeminiApiKey() {
        return self::$gemini_api_key;
    }
    
    public static function getOpenRouterApiKey() {
        return self::$openrouter_api_key;
    }
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
        return self::$recaptcha_site_key;
    }
    
    /**
     * Get reCAPTCHA secret key
     * 
     * @return string
     */
    public static function getRecaptchaSecretKey(): string {
        return self::$recaptcha_secret_key;
    }
    
    /**
     * Get Google OAuth Client ID
     * 
     * @return string
     */
    public static function getGoogleClientId(): string {
        return self::$google_client_id;
    }
    
    /**
     * Get Google OAuth Client Secret
     * 
     * @return string
     */
    public static function getGoogleClientSecret(): string {
        return self::$google_client_secret;
    }
    
    /**
     * Get Google OAuth Redirect URI
     * 
     * @return string
     */
    public static function getGoogleRedirectUri(): string {
        return self::$google_redirect_uri;
    }
    
    // SMTP Configuration (Gmail)
    private static $smtp_host = "smtp.gmail.com";
    private static $smtp_port = 587;
    private static $smtp_email = "fathikhelifi0769@gmail.com";
    private static $smtp_password = "uxio dpjc slop sibc";  // 16-character App Password
    
    /**
     * Get SMTP Host
     * 
     * @return string
     */
    public static function getSmtpHost(): string {
        return self::$smtp_host;
    }
    
    /**
     * Get SMTP Port
     * 
     * @return int
     */
    public static function getSmtpPort(): int {
        return self::$smtp_port;
    }
    
    /**
     * Get SMTP Email
     * 
     * @return string
     */
    public static function getSmtpEmail(): string {
        return self::$smtp_email;
    }
    
    /**
     * Get SMTP Password
     * 
     * @return string
     */
    public static function getSmtpPassword(): string {
        return self::$smtp_password;
    }
}
?>
