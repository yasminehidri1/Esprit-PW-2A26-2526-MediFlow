<?php
/**
 * NotificationController
 *
 * Handles two independent notification systems:
 *
 *  Admin bell  (Core\NotificationService — system-wide, admin-only)
 *    GET  /api/notifications           → list()
 *    POST /api/notifications/read      → markRead()
 *    POST /api/notifications/read-all  → markAllRead()
 *
 *  Magazine / user  (\Notification model — per-user)
 *    GET  /magazine/notifications       → index()
 *    POST /magazine/notifications/read  → markUserRead()
 *
 * @package MediFlow\Controllers
 */

namespace Controllers;

use Core\SessionHelper;
use Core\NotificationService;

class NotificationController
{
    use SessionHelper;

    private \Notification $notifModel;

    public function __construct()
    {
        $this->ensureSession();
        require_once __DIR__ . '/../Models/Notification.php';
        $this->notifModel = new \Notification();
    }

    // ── Shared helpers ────────────────────────────────────────────────────────

    private function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function requireAdmin(): void
    {
        if (($_SESSION['user']['role'] ?? '') !== 'Admin') {
            $this->json(['error' => 'Accès refusé'], 403);
        }
    }

    // ── Admin bell — GET /api/notifications ───────────────────────────────────

    public function list(): void
    {
        $role   = $_SESSION['user']['role'] ?? '';
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if ($role === 'Admin') {
            $notifications = NotificationService::getRecent(30);
            $unreadCount   = NotificationService::countUnread();
        } else {
            if (!$userId) $this->json(['notifications' => [], 'unread_count' => 0]);
            $notifications = $this->notifModel->getForUser($userId, 30);
            $unreadCount   = $this->notifModel->countUnread($userId);
        }

        foreach ($notifications as &$n) {
            $n['time_ago'] = NotificationService::timeAgo($n['created_at']);
        }
        unset($n);

        $this->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // ── Admin bell — POST /api/notifications/read ─────────────────────────────

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

    // ── Admin bell — POST /api/notifications/read-all ────────────────────────

    public function markAllRead(): void
    {
        $role   = $_SESSION['user']['role'] ?? '';
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if ($role === 'Admin') {
            NotificationService::markAllRead();
        } else {
            if ($userId) $this->notifModel->markAllRead($userId);
        }

        $this->json(['success' => true, 'unread_count' => 0]);
    }

    // ── Magazine / user — GET /magazine/notifications ─────────────────────────

    public function index(): void
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if (!$userId) {
            $this->json(['notifications' => [], 'unread' => 0]);
        }

        $notifications = $this->notifModel->getForUser($userId, 20);
        $unread        = $this->notifModel->countUnread($userId);

        $this->json(['notifications' => $notifications, 'unread' => $unread]);
    }

    // ── Magazine / user — POST /magazine/notifications/read ───────────────────

    public function markUserRead(): void
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if (!$userId) {
            $this->json(['success' => false], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $id    = isset($input['id']) ? (int)$input['id'] : null;

        if ($id) {
            $this->notifModel->markRead($id);
        } else {
            $this->notifModel->markAllRead($userId);
        }

        $this->json(['success' => true]);
    }
}
