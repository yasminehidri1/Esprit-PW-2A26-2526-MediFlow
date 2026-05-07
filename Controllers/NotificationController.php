<?php

/**
 * NotificationController
 * 
 * Handles all notification operations:
 * - Display notifications list
 * - Mark as read / mark all as read
 * - Delete notifications
 * - Get unread count badge
 */

namespace Controllers;

use Core\SessionHelper;
use Models\Notification;

class NotificationController {
    use SessionHelper;
    
    private $notification;

    public function __construct() {
        $this->ensureSession();
        $this->notification = new Notification();
    }

    /**
     * Display notifications page (dashboard)
     */
    public function index() {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        $page = (int)($_GET['page'] ?? 1);
        
        // Get notifications
        $notifications = $this->notification->getAll($userId, $page, 20);
        $total = $this->notification->getTotalCount($userId);
        $totalPages = ceil($total / 20);

        // Get unread count
        $unreadCount = $this->notification->getUnreadCount($userId);

        // Render view
        require 'Views/Back/notifications_list.php';
    }

    /**
     * Get unread notifications (AJAX)
     */
    public function getUnread() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = $_SESSION['user']['id'];
        $notifications = $this->notification->getUnread($userId, 10);
        $unreadCount = $this->notification->getUnreadCount($userId);

        echo json_encode([
            'success' => true,
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
        exit;
    }

    /**
     * Get unread count badge (AJAX)
     */
    public function getUnreadCount() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = $_SESSION['user']['id'];
        $count = $this->notification->getUnreadCount($userId);

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        exit;
    }

    /**
     * Mark a notification as read (AJAX)
     */
    public function markAsRead() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $notificationId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            exit;
        }

        $result = $this->notification->markAsRead($notificationId, $userId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Marked as read' : 'Not found'
        ]);
        exit;
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = $_SESSION['user']['id'];
        $result = $this->notification->markAllAsRead($userId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'All notifications marked as read' : 'Failed to mark all as read'
        ]);
        exit;
    }

    /**
     * Delete a notification (AJAX)
     */
    public function delete() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $notificationId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            exit;
        }

        $result = $this->notification->delete($notificationId, $userId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notification deleted' : 'Not found'
        ]);
        exit;
    }

    /**
     * Delete all notifications (AJAX)
     */
    public function deleteAll() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = $_SESSION['user']['id'];
        $result = $this->notification->deleteAll($userId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'All notifications deleted' : 'Failed to delete notifications'
        ]);
        exit;
    }

    /**
     * Get notifications dropdown (AJAX) - for navbar
     */
    public function getDropdown() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $userId = $_SESSION['user']['id'];
        $notifications = $this->notification->getUnread($userId, 5);
        $unreadCount = $this->notification->getUnreadCount($userId);

        echo json_encode([
            'success' => true,
            'count' => $unreadCount,
            'notifications' => array_map(function($notif) {
                return [
                    'id' => $notif['id'],
                    'type' => $notif['type'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'icon' => $notif['icon'],
                    'color' => $notif['color'],
                    'is_read' => (bool)$notif['is_read'],
                    'time_ago' => Notification::getTimeAgo($notif['created_at']),
                    'created_at' => $notif['created_at']
                ];
            }, $notifications)
        ]);
        exit;
    }
}
