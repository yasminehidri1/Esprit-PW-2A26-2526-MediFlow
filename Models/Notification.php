<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = \config::getConnexion();
    }

    public function create(int $userId, string $type, string $title, string $message, string $icon = 'notifications', string $color = 'primary'): void {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id, type, title, message, icon, color)
             VALUES (:uid, :type, :title, :msg, :icon, :color)"
        );
        $stmt->execute([
            ':uid'   => $userId,
            ':type'  => $type,
            ':title' => $title,
            ':msg'   => $message,
            ':icon'  => $icon,
            ':color' => $color,
        ]);
    }

    public function getForUser(int $userId, int $limit = 20): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim"
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS n FROM notifications WHERE user_id = :uid AND is_read = 0"
        );
        $stmt->execute([':uid' => $userId]);
        return (int)($stmt->fetch()['n'] ?? 0);
    }

    public function markRead(int $id): void {
        $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id")
                 ->execute([':id' => $id]);
    }

    public function markAllRead(int $userId): void {
        $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid")
                 ->execute([':uid' => $userId]);
    }
}