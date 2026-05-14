<?php
require_once __DIR__ . '/../config.php';

class NotificationModel {

    private \PDO $db;

    private static array $typeMap = [
        'new_demande'     => ['icon' => 'assignment',    'color' => 'blue'],
        'demande_traitee' => ['icon' => 'check_circle',  'color' => 'green'],
        'demande_refusee' => ['icon' => 'cancel',        'color' => 'red'],
    ];

    public function __construct() {
        $this->db = \config::getConnexion();
    }

    /** Crée une notification pour un utilisateur (médecin ou patient). */
    public function add(int $medecinId, string $type, string $title, string $message, int $demandeId = 0): void {
        $icon  = self::$typeMap[$type]['icon']  ?? 'notifications';
        $color = self::$typeMap[$type]['color'] ?? 'primary';

        $this->db->prepare(
            "INSERT INTO notifications (user_id, type, title, message, icon, color)
             VALUES (:uid, :type, :title, :msg, :icon, :color)"
        )->execute([
            ':uid'   => $medecinId,
            ':type'  => $type,
            ':title' => $title,
            ':msg'   => $message,
            ':icon'  => $icon,
            ':color' => $color,
        ]);
    }

    /** Retourne les notifications d'un utilisateur (les plus récentes en premier). */
    public function getByMedecin(int $medecinId, int $limit = 20): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = :uid AND type NOT IN ('new_user', 'password_changed', 'user_suspended', 'user_activated', 'user_deleted', 'user_updated', 'google_signup', 'new_rdv', 'new_order', 'low_stock') ORDER BY created_at DESC LIMIT :lim"
        );
        $stmt->bindValue(':uid', $medecinId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,     \PDO::PARAM_INT);
        $stmt->execute();
        return array_map([$this, 'normalize'], $stmt->fetchAll());
    }

    /** Compte les notifications non lues d'un utilisateur. */
    public function countUnread(int $medecinId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS n FROM notifications WHERE user_id = :uid AND type NOT IN ('new_user', 'password_changed', 'user_suspended', 'user_activated', 'user_deleted', 'user_updated', 'google_signup', 'new_rdv', 'new_order', 'low_stock') AND is_read = 0"
        );
        $stmt->execute([':uid' => $medecinId]);
        return (int)($stmt->fetch()['n'] ?? 0);
    }

    /** Marque toutes les notifications d'un utilisateur comme lues. */
    public function markAllRead(int $medecinId): void {
        $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid")
                 ->execute([':uid' => $medecinId]);
    }

    /** Normalise les noms de colonnes DB vers ce qu'attend la vue (topbar). */
    private function normalize(array $row): array {
        $row['read']       = (bool)($row['is_read'] ?? false);
        $row['medecin_id'] = $row['user_id'] ?? 0;
        return $row;
    }
}
