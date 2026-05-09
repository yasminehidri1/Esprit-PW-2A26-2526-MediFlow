<?php
/**
 * NewsletterController — MediFlow Magazine
 * Handles subscription (AJAX POST) and unsubscription (GET via email link).
 */

namespace Controllers;

use Core\SessionHelper;

class NewsletterController
{
    use SessionHelper;

    /**
     * POST /magazine/newsletter/subscribe
     * Expects: { email: string }  (form-data or JSON)
     * Returns: JSON { success, status, message }
     */
    public function subscribe(): void
    {
        header('Content-Type: application/json');
        $this->ensureSession();

        $email = strtolower(trim($_POST['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        require_once __DIR__ . '/../Models/NewsletterSubscriber.php';
        $model  = new \NewsletterSubscriber();
        $result = $model->subscribe($email);

        if ($result['status'] === 'already_subscribed') {
            echo json_encode([
                'success' => true,
                'status'  => 'already_subscribed',
                'message' => 'This email is already subscribed to our newsletter.',
            ]);
            exit;
        }

        // Send welcome email (non-blocking — a failure doesn't break the flow)
        require_once __DIR__ . '/../Services/NewsletterService.php';
        \Services\NewsletterService::welcome($email, $result['token']);

        echo json_encode([
            'success' => true,
            'status'  => $result['status'], // 'subscribed' or 'resubscribed'
            'message' => 'Subscribed! Check your inbox for a welcome email.',
        ]);
        exit;
    }

    /**
     * GET /magazine/newsletter/unsubscribe?email=...&token=...
     * Renders a confirmation page (no layout, standalone).
     */
    public function unsubscribe(): void
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        require_once __DIR__ . '/../Models/NewsletterSubscriber.php';
        $model = new \NewsletterSubscriber();
        $ok    = $model->unsubscribe($email, $token);

        $icon    = $ok ? '✓' : '✕';
        $bg      = $ok ? '#005851' : '#ba1a1a';
        $heading = $ok ? 'Unsubscribed successfully' : 'Invalid or expired link';
        $msg     = $ok
            ? 'You have been removed from the MediFlow Magazine newsletter. You will no longer receive new article notifications.'
            : 'This unsubscribe link is invalid or has already been used.';

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediFlow Magazine — Unsubscribe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin:0; background:#f1f5f9; font-family:'Inter',Arial,sans-serif;
               display:flex; min-height:100vh; align-items:center; justify-content:center; padding:24px; }
    </style>
</head>
<body>
    <div style="max-width:460px;width:100%;background:#fff;border-radius:20px;
                padding:44px 36px;text-align:center;
                box-shadow:0 8px 40px rgba(0,0,0,0.09);">
        <div style="width:72px;height:72px;border-radius:50%;background:{$bg};
                    color:#fff;font-size:32px;line-height:72px;margin:0 auto 24px;">
            {$icon}
        </div>
        <h2 style="margin:0 0 14px;font-size:22px;font-weight:800;color:#0f172a;">{$heading}</h2>
        <p style="margin:0 0 32px;font-size:15px;color:#64748b;line-height:1.65;">{$msg}</p>
        <a href="/integration/magazine"
           style="display:inline-block;background:linear-gradient(135deg,#004d99,#1565c0);
                  color:#fff;padding:13px 36px;border-radius:12px;
                  font-weight:700;font-size:14px;text-decoration:none;">
            Back to Magazine
        </a>
        <p style="margin:24px 0 0;font-size:11px;color:#94a3b8;">&copy; MediFlow Magazine</p>
    </div>
</body>
</html>
HTML;
        exit;
    }
}
