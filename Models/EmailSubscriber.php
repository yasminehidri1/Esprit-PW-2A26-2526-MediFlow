<?php
/**
 * Email Subscriber Model
 * 
 * Handles email subscription operations
 * 
 * @package MediFlow\Models
 */

namespace Models;

use PDO;

require_once __DIR__ . '/../config.php';

class EmailSubscriber
{
    private $conn;
    private $table = 'email_subscribers';

    public function __construct()
    {
        $this->conn = \config::getConnexion();
    }

    /**
     * Subscribe email to notifications
     * 
     * @param string $email Email address
     * @param int|null $userId User ID if authenticated
     * @return bool
     */
    public function subscribe(string $email, ?int $userId = null): bool
    {
        $sql = "INSERT INTO {$this->table} (email, user_id, status) 
                VALUES (:email, :user_id, 'active')
                ON DUPLICATE KEY UPDATE status = 'active', updated_at = NOW()";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':email' => $email,
            ':user_id' => $userId
        ]);
    }

    /**
     * Unsubscribe email
     * 
     * @param string $email Email address
     * @return bool
     */
    public function unsubscribe(string $email): bool
    {
        $sql = "UPDATE {$this->table} SET status = 'unsubscribed' WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }

    /**
     * Get all active subscribers
     * 
     * @return array Array of subscriber emails
     */
    public function getActiveSubscribers(): array
    {
        $sql = "SELECT email FROM {$this->table} WHERE status = 'active' ORDER BY id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $row['email'], $results);
    }

    /**
     * Get subscriber count
     * 
     * @return int Total active subscribers
     */
    public function getCount(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Check if email is subscribed
     * 
     * @param string $email Email address
     * @return bool
     */
    public function isSubscribed(string $email): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return (bool)$stmt->fetch();
    }

    /**
     * Log sent email
     * 
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $type Email type
     * @param string $status Send status
     * @param string|null $error Error message if failed
     * @return bool
     */
    public function logEmail(string $email, string $subject, string $type, string $status = 'sent', ?string $error = null): bool
    {
        $sql = "INSERT INTO email_logs (recipient_email, subject, email_type, status, error_message) 
                VALUES (:email, :subject, :type, :status, :error)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':email' => $email,
            ':subject' => $subject,
            ':type' => $type,
            ':status' => $status,
            ':error' => $error
        ]);
    }
}
