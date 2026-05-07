<?php
namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailService {

    public function sendDemandeTraitee(string $toEmail, string $toName, string $description): bool {
        $subject = "Votre demande d'ordonnance a été acceptée — MediFlow";
        $body    = $this->templateAccepted($toName, $description);
        return $this->send($toEmail, $toName, $subject, $body);
    }

    public function sendDemandeRefusee(string $toEmail, string $toName, string $description, string $aiMessage = ''): bool {
        $subject = "Votre demande d'ordonnance — MediFlow";
        $body    = $this->templateRefused($toName, $description, $aiMessage);
        return $this->send($toEmail, $toName, $subject, $body);
    }

    private function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = \config::getSmtpHost();
            $mail->SMTPAuth   = true;
            $mail->Username   = \config::getSmtpUser();
            $mail->Password   = \config::getSmtpPass();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = \config::getSmtpPort();
            $mail->CharSet    = 'UTF-8';

            // Expéditeur
            $mail->setFrom(\config::getSmtpFrom(), \config::getSmtpFromName());

            // Destinataire
            $mail->addAddress($toEmail, $toName);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;

        } catch (Exception $e) {
            $this->logEmail($toEmail, $toName, $subject, "ERREUR SMTP : " . $mail->ErrorInfo);
            return false;
        }
    }

    private function logEmail(string $to, string $name, string $subject, string $body): void {
        $logFile = __DIR__ . '/../data/mail_log.txt';
        $entry   = "[" . date('Y-m-d H:i:s') . "]\n"
                 . "À      : {$name} <{$to}>\n"
                 . "Sujet  : {$subject}\n"
                 . str_repeat('-', 60) . "\n"
                 . strip_tags($body) . "\n"
                 . str_repeat('=', 60) . "\n\n";
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    private function templateAccepted(string $name, string $description): string {
        $desc = htmlspecialchars($description);
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Inter',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 20px;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,77,153,0.08);">
        <tr>
          <td style="background:linear-gradient(135deg,#005851,#00897b);padding:32px 40px;text-align:center;">
            <div style="width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:28px;">✅</div>
            <h1 style="color:#fff;font-size:22px;font-weight:800;margin:0;">Demande acceptée</h1>
            <p style="color:rgba(255,255,255,0.8);font-size:13px;margin:6px 0 0;">Votre médecin a traité votre demande d'ordonnance</p>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;">
            <p style="color:#1e293b;font-size:15px;font-weight:600;margin:0 0 8px;">Bonjour {$name},</p>
            <p style="color:#475569;font-size:14px;line-height:1.7;margin:0 0 24px;">
              Bonne nouvelle ! Votre médecin a <strong style="color:#005851;">accepté et traité</strong> votre demande d'ordonnance.
              Vous pouvez récupérer votre ordonnance lors de votre prochaine consultation.
            </p>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:24px;">
              <p style="color:#15803d;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 6px;">Votre demande</p>
              <p style="color:#1e293b;font-size:13px;line-height:1.6;margin:0;">{$desc}</p>
            </div>
            <p style="color:#94a3b8;font-size:13px;line-height:1.6;margin:0;">
              Pour toute question, connectez-vous à votre espace patient sur <strong>MediFlow</strong>.
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8fafc;padding:20px 40px;border-top:1px solid #e2e8f0;text-align:center;">
            <p style="color:#94a3b8;font-size:12px;margin:0;">MediFlow — Ce message est automatique, merci de ne pas y répondre.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function templateRefused(string $name, string $description, string $aiMessage): string {
        $desc       = htmlspecialchars($description);
        $msgSection = '';
        if (!empty($aiMessage)) {
            $msg        = nl2br(htmlspecialchars($aiMessage));
            $msgSection = <<<HTML
            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:16px 20px;margin-bottom:24px;">
              <p style="color:#c2410c;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 8px;">Message de votre médecin</p>
              <p style="color:#431407;font-size:13px;line-height:1.7;margin:0;">{$msg}</p>
            </div>
HTML;
        }
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Inter',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 20px;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,77,153,0.08);">
        <tr>
          <td style="background:linear-gradient(135deg,#dc2626,#ef4444);padding:32px 40px;text-align:center;">
            <div style="width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:28px;">📋</div>
            <h1 style="color:#fff;font-size:22px;font-weight:800;margin:0;">Demande non retenue</h1>
            <p style="color:rgba(255,255,255,0.85);font-size:13px;margin:6px 0 0;">Votre médecin a examiné votre demande</p>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;">
            <p style="color:#1e293b;font-size:15px;font-weight:600;margin:0 0 8px;">Bonjour {$name},</p>
            <p style="color:#475569;font-size:14px;line-height:1.7;margin:0 0 20px;">
              Après examen, votre médecin n'a pas pu donner suite à votre demande d'ordonnance.
            </p>
            {$msgSection}
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;margin-bottom:24px;">
              <p style="color:#b91c1c;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 6px;">Votre demande</p>
              <p style="color:#1e293b;font-size:13px;line-height:1.6;margin:0;">{$desc}</p>
            </div>
            <p style="color:#94a3b8;font-size:13px;line-height:1.6;margin:0;">
              Nous vous conseillons de prendre rendez-vous avec votre médecin pour en discuter.
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8fafc;padding:20px 40px;border-top:1px solid #e2e8f0;text-align:center;">
            <p style="color:#94a3b8;font-size:12px;margin:0;">MediFlow — Ce message est automatique, merci de ne pas y répondre.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
