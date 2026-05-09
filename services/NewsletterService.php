<?php
/**
 * NewsletterService — MediFlow Magazine
 * Handles subscriber welcome emails and new-post broadcast notifications.
 * Uses the same PHPMailer + Gmail SMTP setup as MailService.
 */

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NewsletterService
{
    private const SMTP_HOST  = 'smtp.gmail.com';
    private const SMTP_PORT  = 587;
    private const SMTP_USER  = 'malouche.ayouta@gmail.com';
    private const SMTP_PASS  = 'zndw kwht czhw szkz';
    //cpcf cgla tqxv pftq
    private const FROM_NAME  = 'MediFlow Magazine';
    private const BASE_URL   = 'http://localhost/integration';

    // ──────────────────────────────────────────────────────────
    //  Core sender
    // ──────────────────────────────────────────────────────────
    private static function send(string $toEmail, string $subject, string $htmlBody): bool
    {
        $base = __DIR__ . '/../lib/PHPMailer/';
        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USER;
            $mail->Password   = self::SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(self::SMTP_USER, self::FROM_NAME);
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $htmlBody));

            $mail->send();
            error_log('[MediFlow Newsletter] Sent to: ' . $toEmail . ' | ' . $subject);
            return true;
        } catch (Exception $e) {
            error_log('[MediFlow Newsletter] Error sending to ' . $toEmail . ': ' . $mail->ErrorInfo);
            return false;
        }
    }

    // ──────────────────────────────────────────────────────────
    //  1. Welcome email — sent immediately on subscribe
    // ──────────────────────────────────────────────────────────
    public static function welcome(string $email, string $token): bool
    {
        $unsubUrl   = self::BASE_URL . '/magazine/newsletter/unsubscribe?email=' . urlencode($email) . '&token=' . $token;
        $magazineUrl = self::BASE_URL . '/magazine';

        $body = "
            <!-- Hero -->
            <div style='text-align:center;padding:8px 0 28px;'>
                <div style='display:inline-flex;align-items:center;justify-content:center;
                            width:72px;height:72px;border-radius:50%;
                            background:linear-gradient(135deg,#004d99,#0284c7);
                            margin-bottom:20px;font-size:32px;'>📰</div>
                <h1 style='margin:0 0 10px;font-size:26px;font-weight:800;color:#0f172a;letter-spacing:-0.5px;'>
                    You're now subscribed!
                </h1>
                <p style='margin:0;font-size:16px;color:#64748b;line-height:1.6;'>
                    Welcome to the <strong style='color:#004d99;'>MediFlow Magazine</strong> newsletter.
                </p>
            </div>

            <!-- Divider -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:28px;'></div>

            <!-- What to expect -->
            <div style='background:linear-gradient(135deg,#eff6ff 0%,#f0f9ff 100%);
                        border:1px solid #bfdbfe;border-radius:14px;padding:24px;margin-bottom:28px;'>
                <p style='margin:0 0 16px;font-size:13px;font-weight:700;color:#1e40af;
                           letter-spacing:0.08em;text-transform:uppercase;'>What you'll receive</p>
                <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td style='padding:8px 0;vertical-align:top;width:32px;font-size:20px;'>🩺</td>
                        <td style='padding:8px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            <strong>Instant new-article alerts</strong> — be the first to read our latest health insights
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:8px 0;vertical-align:top;width:32px;font-size:20px;'>🔬</td>
                        <td style='padding:8px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            <strong>Evidence-based content</strong> written and reviewed by healthcare professionals
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:8px 0;vertical-align:top;width:32px;font-size:20px;'>💡</td>
                        <td style='padding:8px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            <strong>Practical wellness tips</strong> and the latest medical research highlights
                        </td>
                    </tr>
                </table>
            </div>

            <!-- CTA -->
            <div style='text-align:center;margin-bottom:8px;'>
                <a href='{$magazineUrl}'
                   style='display:inline-block;
                          background:linear-gradient(135deg,#004d99,#1565c0);
                          color:#ffffff;padding:15px 44px;border-radius:12px;
                          font-weight:700;font-size:15px;text-decoration:none;
                          letter-spacing:0.03em;
                          box-shadow:0 4px 16px rgba(0,77,153,0.3);'>
                    Browse the Magazine &rarr;
                </a>
            </div>
            <p style='text-align:center;font-size:12px;color:#94a3b8;margin-top:16px;'>
                No spam, ever. Only new article notifications.
            </p>
        ";

        $html = self::baseTemplate('Welcome to MediFlow Magazine', $body, $unsubUrl);
        return self::send($email, '📰 Welcome to MediFlow Magazine Newsletter', $html);
    }

    // ──────────────────────────────────────────────────────────
    //  2. New-post notification — sent to all active subscribers
    //     when a post is published
    // ──────────────────────────────────────────────────────────
    public static function newPost(string $email, string $token, array $post): bool
    {
        $unsubUrl  = self::BASE_URL . '/magazine/newsletter/unsubscribe?email=' . urlencode($email) . '&token=' . $token;
        $postUrl   = self::BASE_URL . '/magazine/article?id=' . (int)$post['id'];

        $titre     = htmlspecialchars($post['titre'] ?? '');
        $categorie = htmlspecialchars($post['categorie'] ?? 'General Health');
        $author    = htmlspecialchars(trim(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')));
        $date      = date('F d, Y', strtotime($post['date_publication'] ?? $post['date_creation'] ?? 'now'));

        // Excerpt — first ~220 chars of plain text content
        $excerpt = strip_tags($post['contenu'] ?? '');
        $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
        $excerpt = htmlspecialchars(mb_strimwidth($excerpt, 0, 220, '…'));

        // Cover image (only if URL is absolute-safe)
        $imageBlock = '';
        if (!empty($post['image_url'])) {
            $imgSrc = htmlspecialchars($post['image_url']);
            // Prepend host if URL starts with /
            if (str_starts_with($imgSrc, '/')) {
                $imgSrc = 'http://localhost' . $imgSrc;
            }
            $imageBlock = "
                <div style='margin-bottom:28px;border-radius:14px;overflow:hidden;
                            box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
                    <img src='{$imgSrc}' alt='' width='536'
                         style='width:100%;max-height:280px;object-fit:cover;display:block;'/>
                </div>
            ";
        }

        $body = "
            <p style='font-size:14px;color:#64748b;margin:0 0 20px;'>
                A new article just landed on <strong style='color:#004d99;'>MediFlow Magazine</strong>.
            </p>

            {$imageBlock}

            <!-- Category pill -->
            <div style='margin-bottom:14px;'>
                <span style='display:inline-block;background:#eff6ff;color:#1d4ed8;
                             font-size:11px;font-weight:700;letter-spacing:0.08em;
                             text-transform:uppercase;padding:5px 14px;border-radius:20px;'>
                    {$categorie}
                </span>
            </div>

            <!-- Title -->
            <h2 style='margin:0 0 10px;font-size:24px;font-weight:800;color:#0f172a;
                       line-height:1.25;letter-spacing:-0.3px;'>
                {$titre}
            </h2>

            <!-- Author / date -->
            <p style='margin:0 0 18px;font-size:13px;color:#94a3b8;'>
                By <strong style='color:#475569;'>{$author}</strong> &bull; {$date}
            </p>

            <!-- Excerpt -->
            <p style='margin:0 0 28px;font-size:15px;color:#475569;line-height:1.75;'>
                {$excerpt}
            </p>

            <!-- CTA -->
            <div style='text-align:center;margin-bottom:8px;'>
                <a href='{$postUrl}'
                   style='display:inline-block;
                          background:linear-gradient(135deg,#004d99,#1565c0);
                          color:#ffffff;padding:15px 44px;border-radius:12px;
                          font-weight:700;font-size:15px;text-decoration:none;
                          letter-spacing:0.03em;
                          box-shadow:0 4px 16px rgba(0,77,153,0.3);'>
                    Read Full Article &rarr;
                </a>
            </div>

            <!-- Separator -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin:28px 0 20px;'></div>

            <!-- Unsubscribe note -->
            <p style='margin:0;font-size:12px;color:#94a3b8;text-align:center;line-height:1.7;'>
                You're receiving this because you subscribed to MediFlow Magazine updates.<br>
                <a href='{$unsubUrl}' style='color:#94a3b8;text-decoration:underline;'>
                    Unsubscribe from these emails
                </a>
            </p>
        ";

        $html = self::baseTemplate('New Article — ' . $categorie, $body, null);
        return self::send($email, '📰 New on MediFlow: ' . $post['titre'], $html);
    }

    // ──────────────────────────────────────────────────────────
    //  Broadcast new post to all active subscribers
    // ──────────────────────────────────────────────────────────
    public static function broadcastNewPost(array $post): void
    {
        require_once __DIR__ . '/../Models/NewsletterSubscriber.php';
        $model       = new \NewsletterSubscriber();
        $subscribers = $model->getAllActive();

        foreach ($subscribers as $sub) {
            self::newPost($sub['email'], $sub['token'], $post);
        }
    }

    // ──────────────────────────────────────────────────────────
    //  Shared HTML base template
    // ──────────────────────────────────────────────────────────
    private static function baseTemplate(string $label, string $body, ?string $unsubUrl): string
    {
        $year = date('Y');
        $unsubLine = $unsubUrl
            ? "<p style='margin:8px 0 0;font-size:11px;color:#94a3b8;text-align:center;'>
                   <a href='{$unsubUrl}' style='color:#94a3b8;text-decoration:underline;'>Unsubscribe</a>
               </p>"
            : '';

        return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <meta name='color-scheme' content='light'>
</head>
<body style='margin:0;padding:0;background:#f1f5f9;font-family:Inter,-apple-system,Arial,sans-serif;'>

<table width='100%' cellpadding='0' cellspacing='0' role='presentation'
       style='background:#f1f5f9;padding:48px 16px;'>
<tr><td align='center'>

    <table width='600' cellpadding='0' cellspacing='0' role='presentation'
           style='max-width:600px;width:100%;'>

        <!-- ── HEADER ── -->
        <tr>
            <td style='background:linear-gradient(135deg,#004d99 0%,#1565c0 55%,#0284c7 100%);
                        border-radius:18px 18px 0 0;padding:26px 36px;'>
                <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
                    <tr>
                        <td>
                            <p style='margin:0 0 2px;font-size:22px;font-weight:800;color:#ffffff;
                                       letter-spacing:-0.5px;'>
                                Medi<span style='color:#84f5e8;'>Flow</span>
                            </p>
                            <p style='margin:0;font-size:11px;color:rgba(255,255,255,0.6);
                                       font-weight:600;letter-spacing:0.12em;text-transform:uppercase;'>
                                Magazine
                            </p>
                        </td>
                        <td style='text-align:right;vertical-align:middle;'>
                            <p style='margin:0;font-size:12px;color:rgba(255,255,255,0.55);
                                       font-style:italic;'>{$label}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ── BODY ── -->
        <tr>
            <td style='background:#ffffff;padding:36px 36px 28px;
                        border-radius:0 0 18px 18px;
                        border:1px solid #e2e8f0;border-top:none;'>
                {$body}
            </td>
        </tr>

        <!-- ── FOOTER ── -->
        <tr>
            <td style='padding:20px 0 4px;text-align:center;'>
                <p style='margin:0;font-size:11px;color:#94a3b8;'>
                    &copy; {$year} MediFlow &mdash; Automated message, please do not reply.
                </p>
                {$unsubLine}
            </td>
        </tr>

    </table>

</td></tr>
</table>

</body>
</html>";
    }
}
