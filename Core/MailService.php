<?php
/**
 * Mail Service — PHPMailer Email Handler
 * 
 * @package MediFlow\Core
 * @version 1.0.0
 */

namespace Core;

// Load Composer autoloader for PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mail;
    private $config;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->config = require __DIR__ . '/../config/email.php';
        
        // Set up SMTP
        $this->mail->isSMTP();
        $this->mail->Host = $this->config['smtp']['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->config['smtp']['username'];
        $this->mail->Password = $this->config['smtp']['password'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $this->config['smtp']['port'];
        $this->mail->setFrom($this->config['smtp']['from_email'], $this->config['smtp']['from_name']);
        
        // Enable debugging in development
        if ($_ENV['APP_ENV'] !== 'production') {
            $this->mail->SMTPDebug = 0; // Set to 2 for debugging
        }
    }

    /**
     * Send email to a single recipient
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string|null $replyTo Reply-to email
     * @return bool True on success
     */
    public function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            
            if ($replyTo) {
                $this->mail->addReplyTo($replyTo);
            }
            
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body);
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mail error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     * 
     * @param array $recipients Array of email addresses
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @return int Number of successful sends
     */
    public function sendBulk(array $recipients, string $subject, string $body): int
    {
        $sent = 0;
        foreach ($recipients as $email) {
            if ($this->send($email, $subject, $body)) {
                $sent++;
            }
        }
        return $sent;
    }

    /**
     * Send notification email to post author
     * 
     * @param string $authorEmail Author's email
     * @param string $userName User who liked/commented
     * @param string $postTitle Article title
     * @param string $type 'like' or 'comment'
     * @return bool
     */
    public function sendPostNotification(string $authorEmail, string $userName, string $postTitle, string $type = 'like'): bool
    {
        if ($type === 'like') {
            $subject = "❤️ {$userName} liked your MediFlow article";
            $body = $this->getTemplate('like_notification', [
                'userName' => $userName,
                'postTitle' => $postTitle
            ]);
        } else {
            $subject = "💬 {$userName} commented on your MediFlow article";
            $body = $this->getTemplate('comment_notification', [
                'userName' => $userName,
                'postTitle' => $postTitle
            ]);
        }
        
        return $this->send($authorEmail, $subject, $body);
    }

    /**
     * Send new post notification to subscribers
     * 
     * @param array $subscribers Array of subscriber emails
     * @param string $postTitle Article title
     * @param string $excerpt Article excerpt
     * @param string $postUrl Post URL
     * @return int Number of emails sent
     */
    public function sendNewPostNotification(array $subscribers, string $postTitle, string $excerpt, string $postUrl): int
    {
        $subject = "📰 New article on MediFlow: {$postTitle}";
        $body = $this->getTemplate('new_post', [
            'postTitle' => $postTitle,
            'excerpt' => $excerpt,
            'postUrl' => $postUrl
        ]);
        
        return $this->sendBulk($subscribers, $subject, $body);
    }

    /**
     * Get email template content
     * 
     * @param string $templateName Template name
     * @param array $data Template variables
     * @return string HTML content
     */
    private function getTemplate(string $templateName, array $data = []): string
    {
        extract($data);
        
        $templatePath = __DIR__ . '/../Views/emails/' . $templateName . '.php';
        if (!file_exists($templatePath)) {
            return '<p>' . htmlspecialchars($templateName) . '</p>';
        }
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
