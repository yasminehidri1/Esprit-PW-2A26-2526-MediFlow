<?php
/**
 * NotificationController
 *
 * JSON API endpoints consumed by the admin notification bell.
 * All routes require an authenticated Admin session.
 *
 * Routes (registered in App.php):
 *   GET  /api/notifications         → list recent (+ unread count)
 *   POST /api/notifications/read    → mark one as read  (?id=X)
 *   POST /api/notifications/read-all → mark all as read
 *
 * @package MediFlow\Controllers
 */

namespace Controllers;

use Core\SessionHelper;
use Core\NotificationService;

class NotificationController
{
    use SessionHelper;

    public function __construct()
    {
        $this->ensureSession();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function requireAdmin(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'Admin') {
            $this->json(['error' => 'Accès refusé'], 403);
        }
    }

    // ── GET /api/notifications ────────────────────────────────────────────────

    public function list(): void
    {
        $this->requireAdmin();

        $notifications = NotificationService::getRecent(30);
        $unreadCount   = NotificationService::countUnread();

        // Add human-readable time
        foreach ($notifications as &$n) {
            $n['time_ago'] = NotificationService::timeAgo($n['created_at']);
        }
        unset($n);

        $this->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // ── POST /api/notifications/read ─────────────────────────────────────────

    public function markRead(): void
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['error' => 'ID invalide'], 400);
        }

        NotificationService::markRead($id);
        $this->json(['success' => true, 'unread_count' => NotificationService::countUnread()]);
    }

    // ── POST /api/notifications/read-all ─────────────────────────────────────

    public function markAllRead(): void
    {
        $this->requireAdmin();
        NotificationService::markAllRead();
        $this->json(['success' => true, 'unread_count' => 0]);
    }
}
