<?php

/**
 * NotificationHelper
 * 
 * Centralized notification triggering for all application events
 * Usage: NotificationHelper::notify(type, title, message, userId, icon, color)
 */

namespace Core;

use Models\Notification;

require_once __DIR__ . '/../config.php';

class NotificationHelper {
    
    /**
     * Send a notification
     * 
     * @param string $type Notification type identifier
     * @param string $title Notification title
     * @param string $message Notification message
     * @param int $userId Target user ID
     * @param string $icon Material Symbol icon name
     * @param string $color Color scheme (primary, secondary, tertiary, error, etc)
     * 
     * @return bool|int Notification ID on success
     */
    public static function notify($type, $title, $message, $userId, $icon = 'info', $color = 'primary') {
        try {
            $notification = new Notification();
            return $notification->create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'user_id' => $userId,
                'icon' => $icon,
                'color' => $color
            ]);
        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify admin(s) about new comment
     * 
     * @param string $authorName Comment author name
     * @param string $postTitle Article title
     * @param string $commentPreview Comment text preview (max 100 chars)
     * 
     * @return void
     */
    public static function notifyNewComment($authorName, $postTitle, $commentPreview) {
        // Get all admins
        $admins = self::getAdmins();
        
        foreach ($admins as $admin) {
            self::notify(
                'new_comment',
                '💬 New Comment',
                ucfirst($authorName) . " commented on \"" . substr($postTitle, 0, 40) . "...\"",
                $admin['id'],
                'comment',
                'secondary'
            );
        }
    }

    /**
     * Notify admin(s) about post liked
     * 
     * @param string $userName User who liked
     * @param string $postTitle Article title
     * 
     * @return void
     */
    public static function notifyPostLiked($userName, $postTitle) {
        // Get all admins
        $admins = self::getAdmins();
        
        foreach ($admins as $admin) {
            self::notify(
                'post_liked',
                '❤️ Post Liked',
                ucfirst($userName) . " liked your article \"" . self::shorten($postTitle, 40) . "\"",
                $admin['id'],
                'favorite',
                'error'
            );
        }
    }

    /**
     * Notify a specific post author that someone liked their post
     *
     * @param int $authorId Post author user ID
     * @param string $likerName User who liked
     * @param string $postTitle Article title
     *
     * @return void
     */
    public static function notifyPostAuthorLiked(int $authorId, string $likerName, string $postTitle): void {
        self::notify(
            'like_on_your_post',
            '❤️ New Like on Your Post',
            ucfirst($likerName) . " liked your article \"" . self::shorten($postTitle, 40) . "\"",
            $authorId,
            'favorite',
            'error'
        );
    }

    /**
     * Notify post author about new comment
     * 
     * @param int $authorId Post author user ID
     * @param string $commenterName Comment author name
     * @param string $postTitle Article title
     * 
     * @return void
     */
    public static function notifyPostAuthorComment($authorId, $commenterName, $postTitle) {
        self::notify(
            'comment_on_your_post',
            '💬 New Comment on Your Post',
            ucfirst($commenterName) . " commented on \"" . self::shorten($postTitle, 40) . "\"",
            $authorId,
            'comment',
            'secondary'
        );
    }

    /**
     * Notify user about order status change
     * 
     * @param int $userId User ID
     * @param string $orderId Order ID
     * @param string $status New status
     * 
     * @return void
     */
    public static function notifyOrderStatus($userId, $orderId, $status) {
        $statuses = [
            'pending' => ['⏳ Pending', 'primary', 'schedule'],
            'confirmed' => ['✅ Confirmed', 'tertiary', 'check_circle'],
            'shipped' => ['📦 Shipped', 'secondary', 'local_shipping'],
            'delivered' => ['🎉 Delivered', 'tertiary', 'done_all'],
            'cancelled' => ['❌ Cancelled', 'error', 'cancel']
        ];

        $statusInfo = $statuses[$status] ?? ['📝 Updated', 'primary', 'info'];
        
        self::notify(
            'order_status',
            $statusInfo[0] . ' Order #' . $orderId,
            'Your order status has been updated to: ' . ucfirst($status),
            $userId,
            $statusInfo[2],
            $statusInfo[1]
        );
    }

    /**
     * Notify user about appointment confirmation
     * 
     * @param int $userId User ID
     * @param string $doctorName Doctor name
     * @param string $appointmentDate Appointment date/time
     * 
     * @return void
     */
    public static function notifyAppointmentConfirmed($userId, $doctorName, $appointmentDate) {
        self::notify(
            'appointment_confirmed',
            '📅 Appointment Confirmed',
            "Your appointment with Dr. " . $doctorName . " is confirmed for " . date('M d, Y', strtotime($appointmentDate)),
            $userId,
            'calendar_today',
            'tertiary'
        );
    }

    /**
     * Notify user about appointment reminder
     * 
     * @param int $userId User ID
     * @param string $doctorName Doctor name
     * @param string $appointmentDate Appointment date/time
     * 
     * @return void
     */
    public static function notifyAppointmentReminder($userId, $doctorName, $appointmentDate) {
        self::notify(
            'appointment_reminder',
            '🔔 Appointment Reminder',
            "Reminder: Your appointment with Dr. " . $doctorName . " is in 24 hours",
            $userId,
            'notifications_active',
            'secondary'
        );
    }

    /**
     * Notify user about equipment reservation confirmation
     * 
     * @param int $userId User ID
     * @param string $equipmentName Equipment name
     * @param string $startDate Reservation start date
     * @param string $endDate Reservation end date
     * 
     * @return void
     */
    public static function notifyEquipmentReserved($userId, $equipmentName, $startDate, $endDate) {
        self::notify(
            'equipment_reserved',
            '✅ Equipment Reserved',
            $equipmentName . " reserved from " . date('M d', strtotime($startDate)) . " to " . date('M d', strtotime($endDate)),
            $userId,
            'check_circle',
            'tertiary'
        );
    }

    /**
     * Notify admin about new prescription request
     * 
     * @param string $patientName Patient name
     * @param string $details Prescription details
     * 
     * @return void
     */
    public static function notifyNewPrescription($patientName, $details) {
        $admins = self::getAdmins();
        
        foreach ($admins as $admin) {
            self::notify(
                'new_prescription',
                '📋 New Prescription Request',
                "Patient " . ucfirst($patientName) . " has requested a prescription",
                $admin['id'],
                'description',
                'primary'
            );
        }
    }

    /**
     * Notify user about prescription ready for pickup
     * 
     * @param int $userId User ID
     * @param string $medicineName Medicine name
     * 
     * @return void
     */
    public static function notifyPrescriptionReady($userId, $medicineName) {
        self::notify(
            'prescription_ready',
            '💊 Prescription Ready',
            $medicineName . " is ready for pickup at the pharmacy",
            $userId,
            'local_pharmacy',
            'tertiary'
        );
    }

    /**
     * Send email to post author when someone likes their post
     * 
     * @deprecated This functionality has been removed. Likes no longer trigger emails.
     * @param int $authorId Author user ID
     * @param string $authorEmail Author email
     * @param string $userName User who liked
     * @param string $postTitle Article title
     * @return void
     */
    public static function sendLikeNotificationEmail($authorId, $authorEmail, $userName, $postTitle) {
        // This method is deprecated and no longer sends emails
        // Kept for backward compatibility only
        error_log("Deprecated: sendLikeNotificationEmail called but email sending is disabled");
    }

    /**
     * Send email to post author when someone comments on their post
     * 
     * @deprecated This functionality has been removed. Comments no longer trigger emails.
     * @param int $authorId Author user ID
     * @param string $authorEmail Author email
     * @param string $commenterName Name of person who commented
     * @param string $postTitle Article title
     * @return void
     */
    public static function sendCommentNotificationEmail($authorId, $authorEmail, $commenterName, $postTitle) {
        // This method is deprecated and no longer sends emails
        // Kept for backward compatibility only
        error_log("Deprecated: sendCommentNotificationEmail called but email sending is disabled");
    }

    /**
     * Send new post notification to all email subscribers
     * 
     * @param string $postTitle Article title
     * @param string $excerpt Article excerpt
     * @param string $postId Post ID
     * @return void
     */
    public static function sendNewPostNotificationToSubscribers($postTitle, $excerpt, $postId) {
        try {
            $emailSubscriber = new \Models\EmailSubscriber();
            $subscribers = $emailSubscriber->getActiveSubscribers();
            
            if (empty($subscribers)) return;
            
            $postUrl = ($_ENV['APP_URL'] ?? 'https://mediflow.local') . '/integration/magazine/article?id=' . $postId;
            $mailService = new MailService();
            $mailService->sendNewPostNotification($subscribers, $postTitle, $excerpt, $postUrl);
        } catch (Exception $e) {
            error_log("Email notification error (new post): " . $e->getMessage());
        }
    }

    /**
     * Get all admin users
     * 
     * @return array Array of admin user data
     */
    private static function getAdmins() {
        try {
            $db = \config::getConnexion();
            $stmt = $db->query("SELECT id, email, nom FROM utilisateurs WHERE role = 'Admin' LIMIT 100");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching admins: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Shorten text without always adding trailing ellipsis.
     */
    private static function shorten(string $text, int $maxLen): string {
        $text = trim($text);
        if ($maxLen <= 0) return '';
        if (mb_strlen($text) <= $maxLen) return $text;
        return rtrim(mb_substr($text, 0, $maxLen - 1)) . '…';
    }
}
