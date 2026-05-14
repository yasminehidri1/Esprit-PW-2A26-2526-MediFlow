<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
if (!class_exists('config')) require_once __DIR__ . '/../config.php';

class MailService
{
    private string $notifyTo;
    private string $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../data/mail_log.txt';
        if (file_exists(__DIR__ . '/../config_mail.php')) {
            require_once __DIR__ . '/../config_mail.php';
            $this->notifyTo = defined('MAIL_NOTIFY_TO') ? MAIL_NOTIFY_TO : \config::getSmtpFrom();
        } else {
            $this->notifyTo = \config::getSmtpFrom();
        }
    }

    // ─── Core send ────────────────────────────────────────────────────────────

    public function send(string $to, string $subject, string $htmlBody, string $toName = ''): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = \config::getSmtpHost();
            $mail->SMTPAuth   = true;
            $mail->Username   = \config::getSmtpUser();
            $mail->Password   = \config::getSmtpPass();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = \config::getSmtpPort();
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(\config::getSmtpFrom(), \config::getSmtpFromName());
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<tr>', '<td>', '</td>'], ["\n", " | ", ""], $htmlBody));

            $mail->send();
            $this->log($to, $toName, $subject, 'OK');
            return true;
        } catch (Exception $e) {
            $err = $mail->ErrorInfo ?: $e->getMessage();
            $this->log($to, $toName, $subject, 'ERREUR SMTP : ' . $err);
            error_log('[MailService] ' . $err);
            return false;
        }
    }

    // ─── Demande notifications ────────────────────────────────────────────────

    public function sendDemandeTraitee(string $toEmail, string $toName, string $description): bool
    {
        $subject = "Votre demande d'ordonnance a été acceptée — MediFlow";
        $body    = $this->templateAccepted($toName, $description);
        return $this->send($toEmail, $subject, $body, $toName);
    }

    public function sendDemandeRefusee(string $toEmail, string $toName, string $description, string $aiMessage = ''): bool
    {
        $subject = "Votre demande d'ordonnance — MediFlow";
        $body    = $this->templateRefused($toName, $description, $aiMessage);
        return $this->send($toEmail, $subject, $body, $toName);
    }

    // ─── Ordonnance email ─────────────────────────────────────────────────────

    public function sendOrdonnance(string $toEmail, string $toName, string $doctorName, string $dateEmission, array $medicaments, string $notePharmacien = ''): bool
    {
        $subject = "Votre ordonnance médicale — MediFlow";
        $body    = $this->templateOrdonnance($toName, $doctorName, $dateEmission, $medicaments, $notePharmacien);
        return $this->send($toEmail, $subject, $body, $toName);
    }

    // ─── Order notifications ──────────────────────────────────────────────────

    public function sendOrderCreated(int $commandeId, array $cart, string $pharmacienMatricule): void
    {
        $lignesHtml = '';
        $total      = 0.0;

        foreach ($cart as $item) {
            $qty      = (int)($item['quantite'] ?? 1);
            $prix     = (float)($item['prix_unitaire'] ?? 0);
            $subtotal = $qty * $prix;
            $total   += $subtotal;
            $lignesHtml .= sprintf(
                '<tr><td style="padding:8px;border-bottom:1px solid #e5e7eb;">%s</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;">%s</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;">%d</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;">%.2f DT</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:600;">%.2f DT</td></tr>',
                htmlspecialchars($item['nom'] ?? '—'),
                htmlspecialchars($item['categorie'] ?? '—'),
                $qty,
                $prix,
                $subtotal
            );
        }

        $subject = "[MediFlow] Nouvelle commande #{$commandeId} créée";
        $body    = $this->buildOrderTemplate(
            title:               "Nouvelle commande créée",
            badge:               "EN ATTENTE",
            badgeColor:          "#f59e0b",
            commandeId:          $commandeId,
            pharmacienMatricule: $pharmacienMatricule,
            lignesHtml:          $lignesHtml,
            total:               $total,
            note:                "Cette commande est en attente de validation par le fournisseur."
        );

        $this->send($this->notifyTo, $subject, $body);
    }

    public function sendOrderValidated(int $commandeId, array $commande): void
    {
        $lignesHtml = '';
        $total      = 0.0;

        foreach ($commande['lignes'] ?? [] as $ligne) {
            $qty      = (int)$ligne['quantite_demande'];
            $prix     = (float)$ligne['prix'];
            $subtotal = $qty * $prix;
            $total   += $subtotal;
            $lignesHtml .= sprintf(
                '<tr><td style="padding:8px;border-bottom:1px solid #e5e7eb;">%s</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;">%s</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;">%d</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;">%.2f DT</td>
                     <td style="padding:8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:600;">%.2f DT</td></tr>',
                htmlspecialchars($ligne['nom'] ?? '—'),
                htmlspecialchars($ligne['categorie'] ?? '—'),
                $qty,
                $prix,
                $subtotal
            );
        }

        $subject = "[MediFlow] Commande #{$commandeId} validée";
        $body    = $this->buildOrderTemplate(
            title:               "Commande validée",
            badge:               "VALIDÉE",
            badgeColor:          "#10b981",
            commandeId:          $commandeId,
            pharmacienMatricule: $commande['pharmacien_matricule'] ?? '—',
            lignesHtml:          $lignesHtml,
            total:               $total,
            note:                "La commande a été confirmée par le fournisseur et sera bientôt livrée."
        );

        $this->send($this->notifyTo, $subject, $body);
    }

    // ─── Rendez-vous notifications ───────────────────────────────────────────

    public static function rdvModifie(array $rdv, array $medecin, string $ancienneDate, string $ancienneHeure): bool
    {
        $instance = new self();
        $to = $rdv['patient_email'] ?? '';
        if (empty($to)) return false;

        $subject = "Modification de votre rendez-vous — MediFlow";
        $body = $instance->templateRdvModifie($rdv, $medecin, $ancienneDate, $ancienneHeure);
        return $instance->send($to, $subject, $body, ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''));
    }

    public static function rdvConfirme(array $rdv, array $medecin): bool
    {
        $instance = new self();
        $to = $rdv['patient_email'] ?? '';
        if (empty($to)) return false;

        $subject = "Confirmation de votre rendez-vous — MediFlow";
        $body = $instance->templateRdvConfirme($rdv, $medecin);
        return $instance->send($to, $subject, $body, ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''));
    }

    public static function rdvAnnule(array $rdv, array $medecin): bool
    {
        $instance = new self();
        $to = $rdv['patient_email'] ?? '';
        if (empty($to)) return false;

        $subject = "Annulation de votre rendez-vous — MediFlow";
        $body = $instance->templateRdvAnnule($rdv, $medecin);
        return $instance->send($to, $subject, $body, ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''));
    }

    public static function rdvRappel(array $rdv, array $medecin): bool
    {
        $instance = new self();
        $to = $rdv['patient_email'] ?? '';
        if (empty($to)) return false;

        $subject = "Rappel : Votre rendez-vous de demain — MediFlow";
        $body = $instance->templateRdvRappel($rdv, $medecin);
        return $instance->send($to, $subject, $body, ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''));
    }

    public static function verifyToken(int $rdvId, string $action, string $token): bool
    {
        return $token === self::generateToken($rdvId, $action);
    }

    public static function generateToken(int $rdvId, string $action): string
    {
        $secret = "MediFlow_RDV_Secret_2026"; 
        return hash_hmac('sha256', $rdvId . $action, $secret);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function log(string $to, string $name, string $subject, string $status): void
    {
        $entry = "[" . date('Y-m-d H:i:s') . "]\n"
               . "À      : {$name} <{$to}>\n"
               . "Sujet  : {$subject}\n"
               . str_repeat('-', 60) . "\n"
               . $status . "\n"
               . str_repeat('=', 60) . "\n\n";
        file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    private function buildOrderTemplate(
        string $title,
        string $badge,
        string $badgeColor,
        int    $commandeId,
        string $pharmacienMatricule,
        string $lignesHtml,
        float  $total,
        string $note
    ): string {
        $date     = date('d/m/Y à H:i');
        $totalStr = number_format($total, 2, '.', ' ') . ' DT';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
        <tr><td style="background:#1e40af;padding:28px 32px;">
          <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">MediFlow</h1>
          <p style="color:#93c5fd;margin:6px 0 0;font-size:13px;">Gestion du stock médicaments</p>
        </td></tr>
        <tr><td style="padding:32px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="font-size:20px;font-weight:700;color:#111827;">$title</td>
              <td align="right">
                <span style="background:$badgeColor;color:#fff;padding:4px 14px;border-radius:9999px;font-size:12px;font-weight:700;">$badge</span>
              </td>
            </tr>
          </table>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
            <tr style="background:#f9fafb;">
              <td style="padding:10px 14px;color:#6b7280;font-size:13px;width:40%;">N° Commande</td>
              <td style="padding:10px 14px;font-weight:700;color:#1e40af;font-size:15px;">#$commandeId</td>
            </tr>
            <tr>
              <td style="padding:10px 14px;color:#6b7280;font-size:13px;border-top:1px solid #e5e7eb;">Pharmacien</td>
              <td style="padding:10px 14px;color:#111827;border-top:1px solid #e5e7eb;">$pharmacienMatricule</td>
            </tr>
            <tr style="background:#f9fafb;">
              <td style="padding:10px 14px;color:#6b7280;font-size:13px;border-top:1px solid #e5e7eb;">Date</td>
              <td style="padding:10px 14px;color:#111827;border-top:1px solid #e5e7eb;">$date</td>
            </tr>
          </table>
          <p style="margin:0 0 12px;font-weight:600;color:#374151;">Articles commandés</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
            <thead>
              <tr style="background:#f9fafb;">
                <th style="text-align:left;padding:10px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;">Produit</th>
                <th style="text-align:left;padding:10px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;">Catégorie</th>
                <th style="text-align:right;padding:10px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;">Qté</th>
                <th style="text-align:right;padding:10px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;">Prix u.</th>
                <th style="text-align:right;padding:10px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;">Sous-total</th>
              </tr>
            </thead>
            <tbody>$lignesHtml</tbody>
            <tfoot>
              <tr style="background:#eff6ff;">
                <td colspan="4" style="padding:12px 8px;font-weight:700;color:#111827;border-top:2px solid #e5e7eb;">TOTAL</td>
                <td style="padding:12px 8px;font-weight:700;color:#1e40af;text-align:right;border-top:2px solid #e5e7eb;font-size:16px;">$totalStr</td>
              </tr>
            </tfoot>
          </table>
          <div style="background:#eff6ff;border-left:4px solid #1e40af;padding:12px 16px;border-radius:0 6px 6px 0;">
            <p style="margin:0;color:#1e40af;font-size:13px;">$note</p>
          </div>
        </td></tr>
        <tr><td style="background:#f9fafb;padding:20px 32px;text-align:center;border-top:1px solid #e5e7eb;">
          <p style="margin:0;color:#9ca3af;font-size:12px;">MediFlow &mdash; Système de gestion pharmaceutique &copy; 2026</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function templateAccepted(string $name, string $description): string
    {
        $desc = htmlspecialchars($description);
        $body = "
            <!-- Status badge -->
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#dcfce7;color:#15803d;
                             font-size:11px;font-weight:700;letter-spacing:0.08em;
                             text-transform:uppercase;padding:5px 16px;border-radius:20px;
                             border:1px solid #bbf7d0;'>
                    ✓ &nbsp;Demande acceptée
                </span>
            </div>

            <!-- Greeting -->
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>
                Bonjour {$name},
            </p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Bonne nouvelle ! Votre médecin a
                <strong style='color:#004d99;'>accepté et traité</strong>
                votre demande d'ordonnance.
                Vous pouvez la récupérer lors de votre prochaine consultation.
            </p>

            <!-- Divider -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:24px;'></div>

            <!-- Demande card -->
            <div style='background:linear-gradient(135deg,#f0fdf4 0%,#f0f9ff 100%);
                        border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#15803d;
                           letter-spacing:0.08em;text-transform:uppercase;'>Votre demande</p>
                <p style='margin:0;font-size:14px;color:#1e293b;line-height:1.7;'>{$desc}</p>
            </div>

            <!-- What's next -->
            <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:20px 24px;margin-bottom:28px;'>
                <p style='margin:0 0 12px;font-size:11px;font-weight:700;color:#1e40af;
                           letter-spacing:0.08em;text-transform:uppercase;'>Prochaines étapes</p>
                <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td style='padding:6px 0;vertical-align:top;width:28px;font-size:18px;'>🏥</td>
                        <td style='padding:6px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            Présentez-vous à votre prochaine consultation pour récupérer votre ordonnance.
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:6px 0;vertical-align:top;width:28px;font-size:18px;'>💊</td>
                        <td style='padding:6px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            Apportez-la en pharmacie pour obtenir vos médicaments.
                        </td>
                    </tr>
                </table>
            </div>

            <p style='margin:0;font-size:13px;color:#94a3b8;text-align:center;line-height:1.7;'>
                Pour toute question, connectez-vous à votre espace patient sur
                <strong style='color:#475569;'>MediFlow</strong>.
            </p>
        ";

        return $this->baseTemplate('Espace Patient', $body);
    }

    private function templateRefused(string $name, string $description, string $aiMessage): string
    {
        $desc       = htmlspecialchars($description);

        $msgSection = '';
        if (!empty($aiMessage)) {
            $msg        = nl2br(htmlspecialchars($aiMessage));
            $msgSection = "
            <div style='background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;
                        padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#c2410c;
                           letter-spacing:0.08em;text-transform:uppercase;'>Message de votre médecin</p>
                <p style='margin:0;font-size:14px;color:#431407;line-height:1.75;'>{$msg}</p>
            </div>
            ";
        }

        $body = "
            <!-- Status badge -->
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#fef2f2;color:#b91c1c;
                             font-size:11px;font-weight:700;letter-spacing:0.08em;
                             text-transform:uppercase;padding:5px 16px;border-radius:20px;
                             border:1px solid #fecaca;'>
                    Demande non retenue
                </span>
            </div>

            <!-- Greeting -->
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>
                Bonjour {$name},
            </p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Après examen de votre dossier, votre médecin n'a pas pu donner suite
                à votre demande d'ordonnance pour le moment.
            </p>

            <!-- Divider -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:24px;'></div>

            {$msgSection}

            <!-- Demande card -->
            <div style='background:#fef2f2;border:1px solid #fecaca;border-radius:14px;
                        padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#b91c1c;
                           letter-spacing:0.08em;text-transform:uppercase;'>Votre demande</p>
                <p style='margin:0;font-size:14px;color:#1e293b;line-height:1.7;'>{$desc}</p>
            </div>

            <!-- Advice -->
            <div style='background:linear-gradient(135deg,#eff6ff 0%,#f0f9ff 100%);
                        border:1px solid #bfdbfe;border-radius:14px;padding:20px 24px;margin-bottom:28px;'>
                <p style='margin:0 0 12px;font-size:11px;font-weight:700;color:#1e40af;
                           letter-spacing:0.08em;text-transform:uppercase;'>Que faire maintenant ?</p>
                <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td style='padding:6px 0;vertical-align:top;width:28px;font-size:18px;'>📅</td>
                        <td style='padding:6px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            Prenez rendez-vous avec votre médecin pour discuter de votre situation.
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:6px 0;vertical-align:top;width:28px;font-size:18px;'>💬</td>
                        <td style='padding:6px 0;font-size:14px;color:#334155;line-height:1.6;'>
                            Soumettez une nouvelle demande avec plus de détails si nécessaire.
                        </td>
                    </tr>
                </table>
            </div>

            <p style='margin:0;font-size:13px;color:#94a3b8;text-align:center;line-height:1.7;'>
                Pour toute question, connectez-vous à votre espace patient sur
                <strong style='color:#475569;'>MediFlow</strong>.
            </p>
        ";

        return $this->baseTemplate('Espace Patient', $body);
    }

    private function templateOrdonnance(string $name, string $doctorName, string $dateEmission, array $medicaments, string $notePharmacien): string
    {
        $dateStr = date('d/m/Y', strtotime($dateEmission));

        // Build medication rows
        $medsRows = '';
        foreach ($medicaments as $i => $med) {
            $num   = $i + 1;
            $nom   = htmlspecialchars($med['nom']          ?? '—');
            $dos   = htmlspecialchars($med['dosage']        ?? '—');
            $freq  = htmlspecialchars($med['frequence']     ?? '—');
            $dur   = htmlspecialchars($med['duree']         ?? '—');
            $instr = htmlspecialchars($med['instructions']  ?? '');
            $bg    = ($i % 2 === 0) ? '#ffffff' : '#f8fafc';
            $medsRows .= "
                <tr style='background:{$bg};'>
                    <td style='padding:12px 14px;font-size:13px;font-weight:700;color:#004d99;
                                border-bottom:1px solid #e2e8f0;width:28px;text-align:center;'>{$num}</td>
                    <td style='padding:12px 14px;font-size:14px;font-weight:700;color:#0f172a;
                                border-bottom:1px solid #e2e8f0;'>{$nom}
                        " . ($instr ? "<br><span style='font-size:12px;font-weight:400;color:#64748b;'>{$instr}</span>" : '') . "
                    </td>
                    <td style='padding:12px 14px;font-size:13px;color:#334155;
                                border-bottom:1px solid #e2e8f0;'>{$dos}</td>
                    <td style='padding:12px 14px;font-size:13px;color:#334155;
                                border-bottom:1px solid #e2e8f0;'>{$freq}</td>
                    <td style='padding:12px 14px;font-size:13px;color:#334155;
                                border-bottom:1px solid #e2e8f0;'>{$dur}</td>
                </tr>
            ";
        }

        $noteSection = '';
        if (!empty(trim($notePharmacien))) {
            $note = htmlspecialchars($notePharmacien);
            $noteSection = "
                <div style='background:#fffbeb;border:1px solid #fde68a;border-radius:14px;
                            padding:16px 20px;margin-top:24px;'>
                    <p style='margin:0 0 6px;font-size:11px;font-weight:700;color:#92400e;
                               letter-spacing:0.08em;text-transform:uppercase;'>Note pour le pharmacien</p>
                    <p style='margin:0;font-size:14px;color:#451a03;line-height:1.7;'>{$note}</p>
                </div>
            ";
        }

        $body = "
            <!-- Status badge -->
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#eff6ff;color:#1d4ed8;
                             font-size:11px;font-weight:700;letter-spacing:0.08em;
                             text-transform:uppercase;padding:5px 16px;border-radius:20px;
                             border:1px solid #bfdbfe;'>
                    Ordonnance Médicale
                </span>
            </div>

            <!-- Greeting -->
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>
                Bonjour {$name},
            </p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Votre médecin <strong style='color:#004d99;'>{$doctorName}</strong>
                vous a établi une ordonnance le <strong>{$dateStr}</strong>.
                Présentez ce document à votre pharmacien.
            </p>

            <!-- Divider -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:24px;'></div>

            <!-- Medications table -->
            <p style='margin:0 0 12px;font-size:11px;font-weight:700;color:#1e40af;
                       letter-spacing:0.08em;text-transform:uppercase;'>Médicaments prescrits</p>
            <table cellpadding='0' cellspacing='0' width='100%'
                   style='border-collapse:collapse;border:1px solid #e2e8f0;border-radius:12px;
                           overflow:hidden;margin-bottom:8px;font-family:Inter,Arial,sans-serif;'>
                <thead>
                    <tr style='background:#f1f5f9;'>
                        <th style='padding:10px 14px;font-size:10px;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:0.06em;border-bottom:2px solid #e2e8f0;
                                    text-align:center;'>#</th>
                        <th style='padding:10px 14px;font-size:10px;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:0.06em;border-bottom:2px solid #e2e8f0;
                                    text-align:left;'>Médicament</th>
                        <th style='padding:10px 14px;font-size:10px;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:0.06em;border-bottom:2px solid #e2e8f0;
                                    text-align:left;'>Dosage</th>
                        <th style='padding:10px 14px;font-size:10px;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:0.06em;border-bottom:2px solid #e2e8f0;
                                    text-align:left;'>Fréquence</th>
                        <th style='padding:10px 14px;font-size:10px;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:0.06em;border-bottom:2px solid #e2e8f0;
                                    text-align:left;'>Durée</th>
                    </tr>
                </thead>
                <tbody>{$medsRows}</tbody>
            </table>

            {$noteSection}

            <!-- Divider -->
            <div style='height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin:24px 0;'></div>

            <!-- Info box -->
            <div style='background:linear-gradient(135deg,#eff6ff 0%,#f0f9ff 100%);
                        border:1px solid #bfdbfe;border-radius:14px;padding:18px 22px;margin-bottom:8px;'>
                <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td style='padding:5px 0;vertical-align:top;width:26px;font-size:17px;'>💊</td>
                        <td style='padding:5px 0;font-size:13px;color:#334155;line-height:1.6;'>
                            Apportez cette ordonnance à votre pharmacien pour obtenir vos médicaments.
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:5px 0;vertical-align:top;font-size:17px;'>📋</td>
                        <td style='padding:5px 0;font-size:13px;color:#334155;line-height:1.6;'>
                            Conservez ce document et respectez les posologies prescrites.
                        </td>
                    </tr>
                </table>
            </div>

            <p style='margin:16px 0 0;font-size:13px;color:#94a3b8;text-align:center;line-height:1.7;'>
                Pour toute question, connectez-vous à votre espace patient sur
                <strong style='color:#475569;'>MediFlow</strong>.
            </p>
        ";

        return $this->baseTemplate('Ordonnance Médicale', $body);
    }

    private function templateRdvModifie(array $rdv, array $medecin, string $oldDate, string $oldHeure): string
    {
        $name = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
        $dr   = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');
        $date = date('d/m/Y', strtotime($rdv['date_rdv']));
        $time = substr($rdv['heure_rdv'], 0, 5);
        $oldD = date('d/m/Y', strtotime($oldDate));
        $oldH = substr($oldHeure, 0, 5);

        $tokenC = self::generateToken($rdv['id'], 'confirmer');
        $tokenA = self::generateToken($rdv['id'], 'annuler');
        $urlC = "http://localhost/integration/rdv/reponse-modification?rdv_id={$rdv['id']}&action=confirmer&token={$tokenC}";
        $urlA = "http://localhost/integration/rdv/reponse-modification?rdv_id={$rdv['id']}&action=annuler&token={$tokenA}";

        $body = "
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#fffbeb;color:#92400e;font-size:11px;font-weight:700;text-transform:uppercase;padding:5px 16px;border-radius:20px;border:1px solid #fde68a;'>
                    ⚠ Rendez-vous modifié
                </span>
            </div>
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>Bonjour {$name},</p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Votre médecin <strong>{$dr}</strong> a dû modifier l'horaire de votre rendez-vous initialement prévu le {$oldD} à {$oldH}.
            </p>
            <div style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;'>Nouvel horaire</p>
                <p style='margin:0;font-size:18px;font-weight:800;color:#004d99;'>{$date} à {$time}</p>
            </div>
            <p style='margin:0 0 20px;font-size:14px;color:#475569;'>Merci de nous confirmer si ce nouveau créneau vous convient :</p>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td>
                        <a href='{$urlC}' style='display:inline-block;padding:12px 24px;background:#004d99;color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;'>Confirmer le nouveau créneau</a>
                    </td>
                    <td style='text-align:right;'>
                        <a href='{$urlA}' style='display:inline-block;padding:12px 24px;background:#fee2e2;color:#b91c1c;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;'>Annuler le rendez-vous</a>
                    </td>
                </tr>
            </table>
        ";
        return $this->baseTemplate('Espace Patient', $body);
    }

    private function templateRdvConfirme(array $rdv, array $medecin): string
    {
        $name = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
        $dr   = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');
        $date = date('d/m/Y', strtotime($rdv['date_rdv']));
        $time = substr($rdv['heure_rdv'], 0, 5);

        $body = "
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#dcfce7;color:#15803d;font-size:11px;font-weight:700;text-transform:uppercase;padding:5px 16px;border-radius:20px;border:1px solid #bbf7d0;'>
                    ✓ Rendez-vous confirmé
                </span>
            </div>
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>Bonjour {$name},</p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Bonne nouvelle ! Votre rendez-vous avec <strong>{$dr}</strong> a été <strong>confirmé</strong>.
            </p>
            <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#15803d;text-transform:uppercase;'>Détails du rendez-vous</p>
                <p style='margin:0;font-size:18px;font-weight:800;color:#166534;'>{$date} à {$time}</p>
            </div>
            <p style='font-size:14px;color:#475569;line-height:1.6;'>
                Nous vous attendons au cabinet à l'heure prévue. En cas d'empêchement, merci de nous prévenir au plus vite.
            </p>
        ";
        return $this->baseTemplate('Espace Patient', $body);
    }

    private function templateRdvAnnule(array $rdv, array $medecin): string
    {
        $name = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
        $dr   = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');
        $date = date('d/m/Y', strtotime($rdv['date_rdv']));
        $time = substr($rdv['heure_rdv'], 0, 5);

        $body = "
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#fef2f2;color:#b91c1c;font-size:11px;font-weight:700;text-transform:uppercase;padding:5px 16px;border-radius:20px;border:1px solid #fecaca;'>
                    ❌ Rendez-vous annulé
                </span>
            </div>
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>Bonjour {$name},</p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Votre rendez-vous avec <strong>{$dr}</strong> prévu le {$date} à {$time} a été <strong>annulé</strong>.
            </p>
            <p style='font-size:14px;color:#475569;line-height:1.6;'>
                Vous pouvez reprendre rendez-vous à tout moment depuis votre espace patient sur MediFlow.
            </p>
            <div style='margin-top:28px;text-align:center;'>
                <a href='http://localhost/integration/rdv/annuaire' style='display:inline-block;padding:12px 24px;background:#004d99;color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:14px;'>Prendre un nouveau rendez-vous</a>
            </div>
        ";
        return $this->baseTemplate('Espace Patient', $body);
    }

    private function templateRdvRappel(array $rdv, array $medecin): string
    {
        $name = ($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? '');
        $dr   = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');
        $date = date('d/m/Y', strtotime($rdv['date_rdv']));
        $time = substr($rdv['heure_rdv'], 0, 5);

        $body = "
            <div style='text-align:center;padding:4px 0 24px;'>
                <span style='display:inline-block;background:#eff6ff;color:#1e40af;font-size:11px;font-weight:700;text-transform:uppercase;padding:5px 16px;border-radius:20px;border:1px solid #bfdbfe;'>
                    🔔 Rappel de rendez-vous
                </span>
            </div>
            <p style='margin:0 0 8px;font-size:16px;font-weight:700;color:#0f172a;'>Bonjour {$name},</p>
            <p style='margin:0 0 24px;font-size:15px;color:#475569;line-height:1.75;'>
                Ceci est un petit rappel pour votre rendez-vous de <strong>demain</strong> avec <strong>{$dr}</strong>.
            </p>
            <div style='background:#f0f9ff;border:1px solid #bfdbfe;border-radius:14px;padding:20px 24px;margin-bottom:24px;'>
                <p style='margin:0 0 10px;font-size:11px;font-weight:700;color:#1e40af;text-transform:uppercase;'>Demain le {$date}</p>
                <p style='margin:0;font-size:20px;font-weight:800;color:#004d99;'>À {$time}</p>
            </div>
            <p style='font-size:14px;color:#475569;line-height:1.6;'>
                Nous vous remercions de votre ponctualité.
            </p>
        ";
        return $this->baseTemplate('Espace Patient', $body);
    }

    private function baseTemplate(string $label, string $body): string
    {
        $year = date('Y');
        return "<!DOCTYPE html>
<html lang='fr'>
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

        <!-- HEADER -->
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
                                Dossier Médical
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

        <!-- BODY -->
        <tr>
            <td style='background:#ffffff;padding:36px 36px 28px;
                        border-radius:0 0 18px 18px;
                        border:1px solid #e2e8f0;border-top:none;'>
                {$body}
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style='padding:20px 0 4px;text-align:center;'>
                <p style='margin:0;font-size:11px;color:#94a3b8;'>
                    &copy; {$year} MediFlow &mdash; Message automatique, merci de ne pas y répondre.
                </p>
            </td>
        </tr>

    </table>

</td></tr>
</table>

</body>
</html>";
    }
}
