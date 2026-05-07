<?php
/**
 * NotificationService
 *
 * Central helper for creating notifications.
 * Call NotificationService::push() from any controller when an event occurs.
 *
 * @package MediFlow\Core
 */

namespace Core;

class NotificationService
{
    // ── Event type constants ──────────────────────────────────────────────────
    const TYPE_NEW_USER        = 'new_user';
    const TYPE_PASSWORD_CHANGE = 'password_changed';
    const TYPE_USER_SUSPENDED  = 'user_suspended';
    const TYPE_USER_ACTIVATED  = 'user_activated';
    const TYPE_USER_DELETED    = 'user_deleted';
    const TYPE_USER_UPDATED    = 'user_updated';
    const TYPE_GOOGLE_SIGNUP   = 'google_signup';
    const TYPE_NEW_RDV         = 'new_rdv';
    const TYPE_NEW_ORDER       = 'new_order';
    const TYPE_LOW_STOCK       = 'low_stock';

    // ── Icon + colour palette ─────────────────────────────────────────────────
    private static array $meta = [
        self::TYPE_NEW_USER        => ['icon' => 'person_add',         'color' => 'primary'],
        self::TYPE_PASSWORD_CHANGE => ['icon' => 'lock_reset',         'color' => 'secondary'],
        self::TYPE_USER_SUSPENDED  => ['icon' => 'block',              'color' => 'error'],
        self::TYPE_USER_ACTIVATED  => ['icon' => 'check_circle',       'color' => 'tertiary'],
        self::TYPE_USER_DELETED    => ['icon' => 'person_remove',      'color' => 'error'],
        self::TYPE_USER_UPDATED    => ['icon' => 'manage_accounts',    'color' => 'secondary'],
        self::TYPE_GOOGLE_SIGNUP   => ['icon' => 'account_circle',     'color' => 'primary'],
        self::TYPE_NEW_RDV         => ['icon' => 'calendar_add_on',    'color' => 'tertiary'],
        self::TYPE_NEW_ORDER       => ['icon' => 'shopping_cart',      'color' => 'secondary'],
        self::TYPE_LOW_STOCK       => ['icon' => 'inventory_2',        'color' => 'error'],
    ];

    /**
     * Push a notification to the DB.
     *
     * @param string   $type    One of the TYPE_* constants
     * @param string   $title   Short title (shown bold in dropdown)
     * @param string   $message Longer description
     * @param int|null $userId  The user who triggered the event (optional)
     */
    public static function push(string $type, string $title, string $message, ?int $userId = null): void
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db = \config::getConnexion();

            $meta  = self::$meta[$type] ?? ['icon' => 'info', 'color' => 'primary'];
            $stmt  = $db->prepare(
                "INSERT INTO notifications (type, title, message, icon, color, user_id, created_at)
                 VALUES (:type, :title, :message, :icon, :color, :user_id, NOW())"
            );
            $stmt->execute([
                ':type'    => $type,
                ':title'   => $title,
                ':message' => $message,
                ':icon'    => $meta['icon'],
                ':color'   => $meta['color'],
                ':user_id' => $userId,
            ]);
        } catch (\Throwable $e) {
            error_log('[NotificationService] push() failed: ' . $e->getMessage());
        }
    }

    /**
     * Get recent unread notifications (admin only).
     *
     * @param int $limit
     * @return array
     */
    public static function getUnread(int $limit = 20): array
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db   = \config::getConnexion();
            $stmt = $db->prepare(
                "SELECT * FROM notifications WHERE is_read = 0
                 ORDER BY created_at DESC LIMIT :limit"
            );
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get all recent notifications (read + unread).
     *
     * @param int $limit
     * @return array
     */
    public static function getRecent(int $limit = 30): array
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db   = \config::getConnexion();
            $stmt = $db->prepare(
                "SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit"
            );
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Count unread notifications.
     */
    public static function countUnread(): int
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db   = \config::getConnexion();
            $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Mark one notification as read.
     */
    public static function markRead(int $id): void
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db = \config::getConnexion();
            $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id")
               ->execute([':id' => $id]);
        } catch (\Throwable $e) {
            error_log('[NotificationService] markRead() failed: ' . $e->getMessage());
        }
    }

    /**
     * Mark all notifications as read.
     */
    public static function markAllRead(): void
    {
        try {
            require_once __DIR__ . '/../config.php';
            $db = \config::getConnexion();
            $db->exec("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        } catch (\Throwable $e) {
            error_log('[NotificationService] markAllRead() failed: ' . $e->getMessage());
        }
    }

    /**
     * Format a timestamp as a human-readable "time ago" string.
     */
    public static function timeAgo(string $datetime): string
    {
        $now  = new \DateTime();
        $then = new \DateTime($datetime);
        $diff = $now->getTimestamp() - $then->getTimestamp();

        if ($diff < 60)        return 'À l\'instant';
        if ($diff < 3600)      return floor($diff / 60) . ' min';
        if ($diff < 86400)     return floor($diff / 3600) . 'h';
        if ($diff < 604800)    return floor($diff / 86400) . 'j';
        return $then->format('d/m/Y');
    }
}
