<?php
namespace Controllers;

use Models\NewsletterSubscriber;

class NewsletterController {
    public function subscribe(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/magazine');
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '/integration/magazine');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email address.';
            header('Location: ' . $redirect);
            exit;
        }

        try {
            require_once __DIR__ . '/../Models/NewsletterSubscriber.php';
            $sub = new NewsletterSubscriber();
            $ok = $sub->upsert($email);
            $_SESSION['flash_success'] = $ok ? 'Subscribed! You will receive new posts by email.' : 'Subscription failed. Please try again.';
        } catch (\Exception $e) {
            error_log('Newsletter subscribe error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Subscription failed. Please try again later.';
        }

        header('Location: ' . $redirect);
        exit;
    }
}

