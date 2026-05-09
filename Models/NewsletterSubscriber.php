<?php
/**
 * NewsletterSubscriber Model — MediFlow Magazine
 */

require_once __DIR__ . '/../config.php';

class NewsletterSubscriber
{
    private $db;

    public function __construct()
    {
        $this->db = \config::getConnexion();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
                `id`            INT AUTO_INCREMENT PRIMARY KEY,
                `email`         VARCHAR(255) NOT NULL,
                `token`         VARCHAR(64)  NOT NULL,
                `subscribed_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
                `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
                UNIQUE KEY `uq_ns_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    /**
     * Subscribe an email address.
     * Returns ['status' => 'subscribed'|'resubscribed'|'already_subscribed', 'token' => string]
     */
    public function subscribe(string $email): array
    {
        $email = strtolower(trim($email));

        $stmt = $this->db->prepare("SELECT id, is_active, token FROM newsletter_subscribers WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ((int)$existing['is_active'] === 1) {
                return ['status' => 'already_subscribed', 'token' => $existing['token']];
            }
            // Reactivate a previously unsubscribed address
            $token = bin2hex(random_bytes(32));
            $this->db->prepare("UPDATE newsletter_subscribers SET is_active = 1, token = :token, subscribed_at = NOW() WHERE email = :email")
                     ->execute([':token' => $token, ':email' => $email]);
            return ['status' => 'resubscribed', 'token' => $token];
        }

        $token = bin2hex(random_bytes(32));
        $this->db->prepare("INSERT INTO newsletter_subscribers (email, token) VALUES (:email, :token)")
                 ->execute([':email' => $email, ':token' => $token]);
        return ['status' => 'subscribed', 'token' => $token];
    }

    /**
     * Unsubscribe by email + token (token is the security proof from the email link).
     */
    public function unsubscribe(string $email, string $token): bool
    {
        $email = strtolower(trim($email));
        $stmt  = $this->db->prepare("SELECT token FROM newsletter_subscribers WHERE email = :email AND is_active = 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        if (!$row || !hash_equals($row['token'], $token)) {
            return false;
        }

        $this->db->prepare("UPDATE newsletter_subscribers SET is_active = 0 WHERE email = :email")
                 ->execute([':email' => $email]);
        return true;
    }

    public function getAllActive(): array
    {
        return $this->db->query("SELECT email, token FROM newsletter_subscribers WHERE is_active = 1")
                        ->fetchAll();
    }

    public function countActive(): int
    {
        return (int)($this->db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active = 1")->fetchColumn());
    }
}
