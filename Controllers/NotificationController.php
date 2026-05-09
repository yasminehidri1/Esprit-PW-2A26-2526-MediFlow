<?php
namespace Controllers;

use Core\SessionHelper;

class NotificationController {
    use SessionHelper;

    private $notifModel;

    public function __construct() {
        $this->ensureSession();
        require_once __DIR__ . '/../Models/Notification.php';
        $this->notifModel = new \Notification();
    }

    /** GET /integration/magazine/notifications — returns JSON list + unread count */
    public function index(): void {
        header('Content-Type: application/json');
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if (!$userId) { echo json_encode(['notifications' => [], 'unread' => 0]); exit; }

        $notifications = $this->notifModel->getForUser($userId, 20);
        $unread        = $this->notifModel->countUnread($userId);

        echo json_encode(['notifications' => $notifications, 'unread' => $unread]);
        exit;
    }

    /** POST /integration/magazine/notifications/read — mark one or all as read */
    public function markRead(): void {
        header('Content-Type: application/json');
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if (!$userId) { echo json_encode(['success' => false]); exit; }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $id    = isset($input['id']) ? (int)$input['id'] : null;

        if ($id) {
            $this->notifModel->markRead($id);
        } else {
            $this->notifModel->markAllRead($userId);
        }

        echo json_encode(['success' => true]);
        exit;
    }
}
