<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private string $smtpHost;
    private int    $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private string $fromEmail;
    private string $fromName;
    private string $notifyTo;

    public function __construct()
    {
        require_once __DIR__ . '/../config_mail.php';
        $this->smtpHost  = MAIL_SMTP_HOST;
        $this->smtpPort  = MAIL_SMTP_PORT;
        $this->smtpUser  = MAIL_SMTP_USER;
        $this->smtpPass  = MAIL_SMTP_PASS;
        $this->fromEmail = MAIL_FROM_EMAIL;
        $this->fromName  = MAIL_FROM_NAME;
        $this->notifyTo  = MAIL_NOTIFY_TO;
    }

    // ─── Envoi brut ───────────────────────────────────────────────────────────

    public function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<tr>', '<td>', '</td>'], ["\n", " | ", ""], $htmlBody));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailService] Erreur envoi email: ' . $mail->ErrorInfo);
            return false;
        }
    }

    // ─── Notification : commande créée ────────────────────────────────────────

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
        $body    = $this->buildTemplate(
            title:              "Nouvelle commande créée",
            badge:              "EN ATTENTE",
            badgeColor:         "#f59e0b",
            commandeId:         $commandeId,
            pharmacienMatricule: $pharmacienMatricule,
            lignesHtml:         $lignesHtml,
            total:              $total,
            note:               "Cette commande est en attente de validation par le fournisseur."
        );

        $this->send($this->notifyTo, $subject, $body);
    }

    // ─── Notification : commande validée ──────────────────────────────────────

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
        $body    = $this->buildTemplate(
            title:              "Commande validée",
            badge:              "VALIDÉE",
            badgeColor:         "#10b981",
            commandeId:         $commandeId,
            pharmacienMatricule: $commande['pharmacien_matricule'] ?? '—',
            lignesHtml:         $lignesHtml,
            total:              $total,
            note:               "La commande a été confirmée par le fournisseur et sera bientôt livrée."
        );

        $this->send($this->notifyTo, $subject, $body);
    }

    // ─── Template HTML email ──────────────────────────────────────────────────

    private function buildTemplate(
        string $title,
        string $badge,
        string $badgeColor,
        int    $commandeId,
        string $pharmacienMatricule,
        string $lignesHtml,
        float  $total,
        string $note
    ): string {
        $date       = date('d/m/Y à H:i');
        $totalStr   = number_format($total, 2, '.', ' ') . ' DT';

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

        <!-- En-tête -->
        <tr><td style="background:#1e40af;padding:28px 32px;">
          <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">MediFlow</h1>
          <p style="color:#93c5fd;margin:6px 0 0;font-size:13px;">Gestion du stock médicaments</p>
        </td></tr>

        <!-- Corps -->
        <tr><td style="padding:32px;">

          <!-- Titre + badge -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="font-size:20px;font-weight:700;color:#111827;">$title</td>
              <td align="right">
                <span style="background:$badgeColor;color:#fff;padding:4px 14px;border-radius:9999px;font-size:12px;font-weight:700;">$badge</span>
              </td>
            </tr>
          </table>

          <!-- Infos commande -->
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

          <!-- Articles -->
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

          <!-- Note -->
          <div style="background:#eff6ff;border-left:4px solid #1e40af;padding:12px 16px;border-radius:0 6px 6px 0;">
            <p style="margin:0;color:#1e40af;font-size:13px;">$note</p>
          </div>

        </td></tr>

        <!-- Pied de page -->
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
}
