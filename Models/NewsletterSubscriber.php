<?php
namespace Models;

require_once __DIR__ . '/../config.php';

use PDO;

class NewsletterSubscriber {
    private $conn;
    private $table = 'newsletter_subscribers';

    public function __construct() {
        $this->conn = \config::getConnexion();
    }

    public function upsert(string $email): bool {
        $sql = "INSERT INTO {$this->table} (email, is_active)
                VALUES (:email, 1)
                ON DUPLICATE KEY UPDATE is_active = 1";
        $stmt = $this->conn->prepare($sql);
        return (bool)$stmt->execute([':email' => $email]);
    }

    public function getActive(int $limit = 2000): array {
        $limit = max(1, min(20000, $limit));
        $sql = "SELECT email
                FROM {$this->table}
                WHERE is_active = 1
                ORDER BY id DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

