<?php
/**
 * Subscription Controller — Email Notification Management
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Models\EmailSubscriber;

class SubscriptionController
{
    /**
     * Subscribe to email notifications
     */
    public function subscribe(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        $userId = $_SESSION['user']['id'] ?? null;
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        try {
            $subscriber = new EmailSubscriber();
            $result = $subscriber->subscribe($email, $userId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'You\'ve been subscribed to our newsletter! 📰'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Could not subscribe. Please try again.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Subscription error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Server error. Please try again later.'
            ]);
        }
        exit;
    }

    /**
     * Subscribe via AJAX (from newsletter box)
     */
    public function subscribeAjax(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? '');
        $userId = $_SESSION['user']['id'] ?? null;
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            exit;
        }
        
        try {
            $subscriber = new EmailSubscriber();
            
            // Check if already subscribed
            if ($subscriber->isSubscribed($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'This email is already subscribed! 📰'
                ]);
                exit;
            }
            
            $result = $subscriber->subscribe($email, $userId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Welcome! You\'ll now receive new article notifications 🎉'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Could not subscribe. Please try again.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Subscription error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Server error. Please try again later.'
            ]);
        }
        exit;
    }

    /**
     * Unsubscribe from email notifications
     */
    public function unsubscribe(): void
    {
        header('Content-Type: application/json');
        
        $email = trim($_GET['email'] ?? '');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        try {
            $subscriber = new EmailSubscriber();
            $result = $subscriber->unsubscribe($email);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'You have been unsubscribed.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Could not unsubscribe. Please try again.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Unsubscribe error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Server error.'
            ]);
        }
        exit;
    }

    /**
     * Get subscription count
     */
    public function getCount(): void
    {
        header('Content-Type: application/json');
        
        try {
            $subscriber = new EmailSubscriber();
            $count = $subscriber->getCount();
            
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'count' => 0
            ]);
        }
        exit;
    }
}
