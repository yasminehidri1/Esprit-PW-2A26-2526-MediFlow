<?php
/**
 * NotificationService.php
 * Service de gestion des notifications in-app
 * À placer dans : Services/NotificationService.php
 */

namespace Services;

class NotificationService
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ══════════════════════════════════════════════════════════
    //  Créer une notification
    // ══════════════════════════════════════════════════════════
    public function creer(int $userId, string $type, string $message, string $title = '', string $icon = 'info', string $color = 'primary'): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, icon, color, is_read, created_at)
            VALUES (:user_id, :type, :title, :message, :icon, :color, 0, NOW())
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':type'    => $type,
            ':title'   => $title,
            ':message' => $message,
            ':icon'    => $icon,
            ':color'   => $color,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  Récupérer les notifications non lues d'un utilisateur
    // ══════════════════════════════════════════════════════════
    public function getNonLues(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = :uid AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    // ══════════════════════════════════════════════════════════
    //  Récupérer toutes les notifications (lues + non lues)
    // ══════════════════════════════════════════════════════════
    public function getTout(int $userId, int $limit = 30): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications
            WHERE user_id = :uid
            ORDER BY created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    // ══════════════════════════════════════════════════════════
    //  Compter les non lues
    // ══════════════════════════════════════════════════════════
    public function compterNonLues(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = :uid AND is_read = 0
        ");
        $stmt->execute([':uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    // ══════════════════════════════════════════════════════════
    //  Marquer une notification comme lue
    // ══════════════════════════════════════════════════════════
    public function marquerLue(int $notifId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE notifications SET is_read = 1
            WHERE id = :id AND user_id = :uid
        ");
        return $stmt->execute([':id' => $notifId, ':uid' => $userId]);
    }

    // ══════════════════════════════════════════════════════════
    //  Marquer TOUTES comme lues
    // ══════════════════════════════════════════════════════════
    public function marquerToutesLues(int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE notifications SET is_read = 1
            WHERE user_id = :uid AND is_read = 0
        ");
        return $stmt->execute([':uid' => $userId]);
    }

    // ══════════════════════════════════════════════════════════
    //  Messages prédéfinis selon le type d'événement
    // ══════════════════════════════════════════════════════════

    // → Notif pour le MÉDECIN quand un patient prend RDV
    public function notifierMedecinNouveauRdv(int $medecinId, array $rdv): bool
    {
        $patient = $rdv['patient_prenom'] . ' ' . $rdv['patient_nom'];
        $date    = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure   = substr($rdv['heure_rdv'], 0, 5);
        $message = "Nouveau rendez-vous de {$patient} le {$date} à {$heure}.";
        return $this->creer($medecinId, 'nouveau_rdv', $message, 'Nouveau rendez-vous', 'calendar', 'primary');
    }

    // → Notif pour le PATIENT quand le médecin confirme
    public function notifierPatientConfirmation(int $patientId, array $rdv, array $medecin): bool
    {
        $dr    = 'Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom'];
        $date  = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure = substr($rdv['heure_rdv'], 0, 5);
        $message = "{$dr} a confirmé votre rendez-vous du {$date} à {$heure}.";
        return $this->creer($patientId, 'confirme', $message, 'Rendez-vous confirmé', 'check-circle', 'success');
    }

    // → Notif pour le PATIENT quand le médecin annule
    public function notifierPatientAnnulation(int $patientId, array $rdv, array $medecin): bool
    {
        $dr    = 'Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom'];
        $date  = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure = substr($rdv['heure_rdv'], 0, 5);
        $message = "{$dr} a annulé votre rendez-vous du {$date} à {$heure}.";
        return $this->creer($patientId, 'annule', $message, 'Rendez-vous annulé', 'x-circle', 'danger');
    }

    // → Notif pour le PATIENT quand le médecin modifie date/heure
    public function notifierPatientModification(int $patientId, array $rdv, array $medecin): bool
    {
        $dr           = 'Dr. ' . $medecin['prenom'] . ' ' . $medecin['nom'];
        $nouvelleDate = date('d/m/Y', strtotime($rdv['date_rdv']));
        $nouvelleHeure = substr($rdv['heure_rdv'], 0, 5);
        $message = "{$dr} a modifié votre rendez-vous : nouveau créneau le {$nouvelleDate} à {$nouvelleHeure}.";
        return $this->creer($patientId, 'modifie', $message, 'Rendez-vous modifié', 'edit', 'warning');
    }
}