<?php
namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class MailHelper {
    private static function env(string $key, ?string $default = null): ?string {
        $v = getenv($key);
        return ($v === false || $v === '') ? $default : $v;
    }

    public static function sendNewsletter(string $toEmail, string $subject, string $htmlBody, string $textBody = ''): bool {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($autoload)) {
            error_log('MailHelper error: vendor/autoload.php not found. Run composer install.');
            return false;
        }
        require_once $autoload;

        $host = self::env('SMTP_HOST');
        $user = self::env('SMTP_USER');
        $pass = self::env('SMTP_PASS');
        $port = (int)self::env('SMTP_PORT', '587');
        $secure = self::env('SMTP_SECURE', 'tls'); // tls|ssl|none

        $fromEmail = self::env('MAIL_FROM_EMAIL', $user ?: 'no-reply@example.com');
        $fromName  = self::env('MAIL_FROM_NAME', 'MediFlow Magazine');

        if (!$host || !$user || !$pass) {
            error_log('MailHelper error: missing SMTP_HOST/SMTP_USER/SMTP_PASS environment variables.');
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $pass;
            $mail->Port = $port;

            if ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($secure === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            return $mail->send();
        } catch (MailException $e) {
            error_log('MailHelper send error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('MailHelper error: ' . $e->getMessage());
            return false;
        }
    }
}

