<?php

/**
 * Notification Model
 * 
 * Handles all notification database operations
 * Supports creating, reading, marking as read, and deleting notifications
 */

namespace Models;

require_once __DIR__ . '/../config.php';

use PDO;

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct() {
        $this->conn = \config::getConnexion();
    }

    /**
     * Create a new notification
     * 
     * @param array $data Notification data:
     *   - type (required): notification type (google_signup, new_comment, post_liked, etc)
     *   - title (required): notification title
     *   - message (required): notification message
     *   - user_id (required): recipient user ID
     *   - icon (optional): Material Symbol icon name
     *   - color (optional): color scheme (primary, secondary, tertiary, error, etc)
     * 
     * @return bool|int Returns notification ID on success, false on failure
     */
    public function create($data) {
        $type = $data['type'] ?? null;
        $title = $data['title'] ?? null;
        $message = $data['message'] ?? null;
        $user_id = $data['user_id'] ?? null;
        $icon = $data['icon'] ?? 'info';
        $color = $data['color'] ?? 'primary';

        if (!$type || !$title || !$message || !$user_id) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (type, title, message, icon, color, user_id) 
                VALUES (:type, :title, :message, :icon, :color, :user_id)";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            ':type' => $type,
            ':title' => $title,
            ':message' => $message,
            ':icon' => $icon,
            ':color' => $color,
            ':user_id' => $user_id
        ]) ? $this->conn->lastInsertId() : false;
    }

    /**
     * Get all unread notifications for a user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of notifications
     * 
     * @return array Array of notifications
     */
    public function getUnread($userId, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all notifications for a user (paginated)
     * 
     * @param int $userId User ID
     * @param int $page Page number
     * @param int $perPage Items per page
     * 
     * @return array Array of notifications
     */
    public function getAll($userId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :offset, :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of unread notifications
     * 
     * @param int $userId User ID
     * 
     * @return int Count of unread notifications
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['count'];
    }

    /**
     * Mark a notification as read
     * 
     * @param int $notificationId Notification ID
     * 
     * @return bool True on success
     */
    public function markAsRead($notificationId, $userId = null) {
        if ($userId !== null) {
            $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $notificationId, ':user_id' => (int)$userId]);
            return $stmt->rowCount() > 0;
        }

        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $notificationId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * 
     * @return bool True on success
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Delete a notification
     * 
     * @param int $notificationId Notification ID
     * 
     * @return bool True on success
     */
    public function delete($notificationId, $userId = null) {
        if ($userId !== null) {
            $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $notificationId, ':user_id' => (int)$userId]);
            return $stmt->rowCount() > 0;
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $notificationId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete all notifications for a user
     * 
     * @param int $userId User ID
     * 
     * @return bool True on success
     */
    public function deleteAll($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Get total count of all notifications for a user
     * 
     * @param int $userId User ID
     * 
     * @return int Total count
     */
    public function getTotalCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['count'];
    }

    /**
     * Get notifications by type
     * 
     * @param int $userId User ID
     * @param string $type Notification type
     * @param int $limit Maximum results
     * 
     * @return array Array of notifications
     */
    public function getByType($userId, $type, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND type = :type
                ORDER BY created_at DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get formatted time difference for display
     * 
     * @param string $datetime DateTime string
     * 
     * @return string Human-readable time difference
     */
    public static function getTimeAgo($datetime) {
        $now = new DateTime();
        $then = new DateTime($datetime);
        $diff = $now->diff($then);

        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    }
}
