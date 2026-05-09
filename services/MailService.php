<?php
/**
 * MailService.php
 * Service d'envoi d'emails via PHPMailer + Gmail SMTP
 * À placer dans : Services/MailService.php
 */

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    // ── Config Gmail SMTP ──────────────────────────────────────
    private const SMTP_HOST     = 'smtp.gmail.com';
    private const SMTP_PORT     = 587;
    private const SMTP_USER     = 'malouche.ayouta@gmail.com';
    private const SMTP_PASS     = 'zndw kwht czhw szkz'; // ← remplace ici
    private const FROM_NAME     = 'MediFlow Clinique';
    private const BASE_URL      = 'http://localhost'; // ← adapte si besoin

    /**
     * Envoie un email générique
     */
    private static function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $icsContent = null, ?string $icsFilename = null): bool
    {
        // Charger PHPMailer (manuel, sans Composer)
        $base = __DIR__ . '/../lib/PHPMailer/';
        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host        = self::SMTP_HOST;
            $mail->SMTPAuth    = true;
            $mail->Username    = self::SMTP_USER;
            $mail->Password    = self::SMTP_PASS;
            $mail->SMTPSecure  = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port        = self::SMTP_PORT;
            $mail->CharSet     = 'UTF-8';

            $mail->setFrom(self::SMTP_USER, self::FROM_NAME);
            $mail->addAddress($toEmail, $toName);

            // ── Pièce jointe .ics (optionnelle) ──────────────────────────
            if ($icsContent !== null && $icsFilename !== null) {
                $mail->addStringAttachment(
                    $icsContent,
                    $icsFilename,
                    PHPMailer::ENCODING_BASE64,
                    'text/calendar; charset=utf-8; method=PUBLISH'
                );
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $htmlBody));

            $mail->send();
            error_log('[MediFlow MailService] Mail envoyé à: ' . $toEmail . ' | Sujet: ' . $subject);
            return true;
        } catch (Exception $e) {
            // DEBUG TEMPORAIRE — à enlever après
            echo '<div style="position:fixed;top:0;left:0;right:0;background:#fee2e2;color:#991b1b;padding:16px;font-family:monospace;font-size:13px;z-index:9999;border-bottom:2px solid #f87171;">';
            echo '<strong>❌ MAIL ERREUR:</strong> ' . htmlspecialchars($mail->ErrorInfo);
            echo '</div>';
            error_log('[MediFlow MailService] ERREUR: ' . $mail->ErrorInfo);
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  1. RDV CONFIRMÉ — mail au patient
    // ══════════════════════════════════════════════════════════
    public static function rdvConfirme(array $rdv, array $medecin): bool
    {
        $subject = '✅ Votre rendez-vous est confirmé — MediFlow';
        $date    = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure   = substr($rdv['heure_rdv'], 0, 5);
        $patient = htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']);
        $dr      = htmlspecialchars('Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom']);

        $html = self::baseTemplate("Rendez-vous Confirmé ✅", "
            <p style='font-size:16px;color:#374151;margin-bottom:16px;'>Bonjour <strong>{$patient}</strong>,</p>
            <p style='color:#6b7280;line-height:1.7;'>Votre rendez-vous avec <strong>{$dr}</strong> a été <strong style='color:#005851;'>confirmé</strong>.</p>
            <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;margin:24px 0;'>
                <p style='margin:0 0 8px;font-size:13px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;'>Détails du rendez-vous</p>
                <p style='margin:4px 0;font-size:16px;font-weight:700;color:#111827;'>📅 {$date} à {$heure}</p>
                <p style='margin:4px 0;font-size:14px;color:#374151;'>👨‍⚕️ {$dr}</p>
            </div>
            <p style='color:#6b7280;font-size:14px;'>Merci de vous présenter 10 minutes avant votre rendez-vous.</p>
            <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;margin-top:16px;'>
                <p style='margin:0;font-size:13px;color:#1d4ed8;'>
                    📎 Un fichier <strong>.ics</strong> est joint à cet email.<br>
                    Ouvrez-le pour ajouter ce rendez-vous à votre agenda
                    (Outlook, Google Calendar, Apple Calendar).
                </p>
            </div>
        ");

        $email = $rdv['patient_email'] ?? '';
        if (empty($email)) return false;

        // ── Générer le fichier .ics en mémoire et l'attacher ──────────────
        $icsContent  = null;
        $icsFilename = null;
        try {
            $base = __DIR__ . '/../';
            if (file_exists($base . 'Services/ICalService.php')) {
                require_once $base . 'Services/ICalService.php';
                $icsContent  = \Services\ICalService::generer($rdv, $medecin);
                $icsFilename = 'rdv-mediflow-' . ($rdv['id'] ?? 'new') . '.ics';
            }
        } catch (\Exception $e) {
            error_log('[MediFlow MailService] ICS génération échouée: ' . $e->getMessage());
        }

        return self::send($email, $patient, $subject, $html, $icsContent, $icsFilename);
    }

    // ══════════════════════════════════════════════════════════
    //  2. RDV ANNULÉ — mail au patient
    // ══════════════════════════════════════════════════════════
    public static function rdvAnnule(array $rdv, array $medecin): bool
    {
        $subject = '❌ Votre rendez-vous a été annulé — MediFlow';
        $date    = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure   = substr($rdv['heure_rdv'], 0, 5);
        $patient = htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']);
        $dr      = htmlspecialchars('Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom']);

        $html = self::baseTemplate("Rendez-vous Annulé ❌", "
            <p style='font-size:16px;color:#374151;margin-bottom:16px;'>Bonjour <strong>{$patient}</strong>,</p>
            <p style='color:#6b7280;line-height:1.7;'>Nous vous informons que votre rendez-vous avec <strong>{$dr}</strong> prévu le <strong>{$date} à {$heure}</strong> a été <strong style='color:#ba1a1a;'>annulé</strong>.</p>
            <div style='background:#fff1f2;border:1px solid #fecaca;border-radius:12px;padding:20px;margin:24px 0;'>
                <p style='margin:0;font-size:14px;color:#991b1b;'>Pour reprendre un rendez-vous, connectez-vous à votre espace MediFlow.</p>
            </div>
            <p style='color:#6b7280;font-size:14px;'>Nous sommes désolés pour la gêne occasionnée.</p>
        ");

        $email = $rdv['patient_email'] ?? '';
        if (empty($email)) return false;
        return self::send($email, $patient, $subject, $html);
    }
     public static function rdvRappel(array $rdv, array $medecin): bool
    {
        $subject = '⏰ Rappel : vous avez un rendez-vous demain — MediFlow';
        $date    = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure   = substr($rdv['heure_rdv'], 0, 5);
        $patient = htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']);
        $dr      = htmlspecialchars('Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom']);
        $specialite = !empty($medecin['specialite'])
            ? '<p style="margin:4px 0;font-size:13px;color:#6b7280;">🩺 ' . htmlspecialchars($medecin['specialite']) . '</p>'
            : '';
 
        $html = self::baseTemplate("Rappel de Rendez-vous ⏰", "
            <p style='font-size:16px;color:#374151;margin-bottom:16px;'>Bonjour <strong>{$patient}</strong>,</p>
            <p style='color:#6b7280;line-height:1.7;'>Ceci est un rappel automatique : vous avez un rendez-vous <strong>demain</strong> avec <strong>{$dr}</strong>.</p>
            <div style='background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:20px;margin:24px 0;'>
                <p style='margin:0 0 10px;font-size:13px;color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;'>📋 Votre rendez-vous</p>
                <p style='margin:4px 0;font-size:18px;font-weight:800;color:#111827;'>📅 {$date} à {$heure}</p>
                <p style='margin:4px 0;font-size:14px;color:#374151;font-weight:600;'>👨‍⚕️ {$dr}</p>
                {$specialite}
            </div>
            <div style='background:#f0fdf4;border-left:4px solid #22c55e;padding:14px 18px;border-radius:0 8px 8px 0;margin-bottom:20px;'>
                <p style='margin:0;font-size:14px;color:#166534;'>
                    💡 Merci de vous présenter <strong>10 minutes avant</strong> votre rendez-vous et d'apporter votre carte d'identité.
                </p>
            </div>
            <p style='color:#9ca3af;font-size:13px;text-align:center;'>
                Si vous ne pouvez pas vous présenter, merci d'annuler votre rendez-vous depuis votre espace MediFlow.
            </p>
        ");
 
        $email = $rdv['patient_email'] ?? '';
        if (empty($email)) return false;
        return self::send($email, $patient, $subject, $html);
    }

    // ══════════════════════════════════════════════════════════
    //  3. RDV MODIFIÉ (date/heure changée) — avec boutons
    //     Confirmer ou Annuler
    // ══════════════════════════════════════════════════════════
    public static function rdvModifie(array $rdv, array $medecin, string $ancienneDate, string $ancienneHeure): bool
    {
        $subject    = '📅 Votre rendez-vous a été modifié — MediFlow';
        $nouvelleDate  = date('d/m/Y', strtotime($rdv['date_rdv']));
        $nouvelleHeure = substr($rdv['heure_rdv'], 0, 5);
        $ancDateFr     = date('d/m/Y', strtotime($ancienneDate));
        $ancHeureFr    = substr($ancienneHeure, 0, 5);
        $patient    = htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']);
        $dr         = htmlspecialchars('Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom']);

        $base       = rtrim(self::BASE_URL, '/');
        $projet     = '/integration'; // ← adapte si besoin
        $urlConfirm = $base . $projet . '/rdv/reponse-modification?rdv_id=' . $rdv['id'] . '&action=confirmer&token=' . self::generateToken($rdv['id'], 'confirmer');
        $urlAnnuler = $base . $projet . '/rdv/reponse-modification?rdv_id=' . $rdv['id'] . '&action=annuler&token='   . self::generateToken($rdv['id'], 'annuler');

        $html = self::baseTemplate("Rendez-vous Modifié 📅", "
            <p style='font-size:16px;color:#374151;margin-bottom:16px;'>Bonjour <strong>{$patient}</strong>,</p>
            <p style='color:#6b7280;line-height:1.7;'><strong>{$dr}</strong> a modifié votre rendez-vous. Veuillez confirmer ou annuler le nouveau créneau.</p>

            <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;margin:24px 0;'>
                <div style='display:flex;gap:24px;flex-wrap:wrap;'>
                    <div>
                        <p style='margin:0 0 4px;font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;'>Ancien créneau</p>
                        <p style='margin:0;font-size:15px;font-weight:600;color:#9ca3af;text-decoration:line-through;'>{$ancDateFr} à {$ancHeureFr}</p>
                    </div>
                    <div style='font-size:20px;align-self:center;'>→</div>
                    <div>
                        <p style='margin:0 0 4px;font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;'>Nouveau créneau</p>
                        <p style='margin:0;font-size:15px;font-weight:700;color:#004d99;'>📅 {$nouvelleDate} à {$nouvelleHeure}</p>
                    </div>
                </div>
            </div>

            <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:8px;'>
                <tr>
                    <td style='padding-right:8px;'>
                        <a href='{$urlConfirm}'
                           style='display:block;text-align:center;background:#005851;color:#ffffff;padding:14px 20px;border-radius:10px;font-weight:700;font-size:15px;text-decoration:none;'>
                            ✅ Confirmer le nouveau créneau
                        </a>
                    </td>
                    <td style='padding-left:8px;'>
                        <a href='{$urlAnnuler}'
                           style='display:block;text-align:center;background:#ba1a1a;color:#ffffff;padding:14px 20px;border-radius:10px;font-weight:700;font-size:15px;text-decoration:none;'>
                            ❌ Annuler le rendez-vous
                        </a>
                    </td>
                </tr>
            </table>
            <p style='color:#9ca3af;font-size:12px;margin-top:20px;text-align:center;'>Ce lien expire dans 48 heures.</p>
        ");

        $email = $rdv['patient_email'] ?? '';
        if (empty($email)) return false;
        return self::send($email, $patient, $subject, $html);
    }

    // ══════════════════════════════════════════════════════════
    //  Génère un token simple pour sécuriser les liens mail
    // ══════════════════════════════════════════════════════════
    public static function generateToken(int $rdvId, string $action): string
    {
        return hash('sha256', $rdvId . $action . 'mediflow_secret_2024');
    }

    public static function verifyToken(int $rdvId, string $action, string $token): bool
    {
        return hash_equals(self::generateToken($rdvId, $action), $token);
    }

    // ══════════════════════════════════════════════════════════
    //  Template HTML de base pour tous les mails
    // ══════════════════════════════════════════════════════════
    private static function baseTemplate(string $titre, string $contenu): string
    {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'></head>
        <body style='margin:0;padding:0;background:#f3f4f6;font-family:Inter,Arial,sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f3f4f6;padding:40px 20px;'>
                <tr><td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='max-width:600px;width:100%;'>

                        <!-- Header -->
                        <tr><td style='background:linear-gradient(135deg,#004d99,#1565c0);border-radius:16px 16px 0 0;padding:28px 32px;text-align:center;'>
                            <p style='margin:0 0 4px;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;'>Medi<span style='color:#84f5e8;'>Flow</span></p>
                            <p style='margin:0;font-size:13px;color:rgba(255,255,255,0.7);'>{$titre}</p>
                        </td></tr>

                        <!-- Body -->
                        <tr><td style='background:#ffffff;padding:32px;border-radius:0 0 16px 16px;'>
                            {$contenu}
                            <hr style='border:none;border-top:1px solid #e5e7eb;margin:28px 0;'>
                            <p style='margin:0;font-size:12px;color:#9ca3af;text-align:center;'>
                                © " . date('Y') . " MediFlow — Ce message est automatique, merci de ne pas y répondre.
                            </p>
                        </td></tr>

                    </table>
                </td></tr>
            </table>
        </body>
        </html>";
    }
}