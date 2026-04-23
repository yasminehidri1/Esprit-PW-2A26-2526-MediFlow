<?php
/**
 * Reservation.php — Model
 * Toutes les opérations BDD sur la table `reservation`
 */

require_once __DIR__ . '/../config.php';

class Reservation {

    private $pdo;

    public function __construct() {
        // Utiliser le singleton de config.php
        $this->pdo = config::getConnexion();
    }

    /* ════════════════════════════════════════════
       READ ALL — avec jointure sur equipement
    ════════════════════════════════════════════ */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT
                r.id,
                r.equipement_id,
                r.locataire_nom,
                r.matricule,
                r.locataire_ville,
                r.date_debut,
                r.date_fin,
                r.statut,
                r.telephone,
                r.created_at,
                e.nom       AS equipement_nom,
                e.reference AS reference,
                e.categorie AS categorie,
                e.prix_jour AS prix_jour
            FROM reservation r
            JOIN equipement e ON r.equipement_id = e.id
            ORDER BY r.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════════════════════
       READ BY MATRICULE — réservations d'un patient
    ════════════════════════════════════════════ */
    public function getByMatricule($matricule) {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id,
                r.equipement_id,
                r.locataire_nom,
                r.matricule,
                r.locataire_ville,
                r.date_debut,
                r.date_fin,
                r.statut,
                r.telephone,
                r.created_at,
                e.nom       AS equipement_nom,
                e.reference AS reference,
                e.categorie AS categorie,
                e.prix_jour AS prix_jour
            FROM reservation r
            JOIN equipement e ON r.equipement_id = e.id
            WHERE r.matricule = ?
            ORDER BY r.id DESC
        ");
        $stmt->execute([$matricule]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════════════════════
       READ ONE — par ID
    ════════════════════════════════════════════ */
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT
                r.*,
                e.nom       AS equipement_nom,
                e.reference AS reference,
                e.categorie AS categorie,
                e.prix_jour AS prix_jour
            FROM reservation r
            JOIN equipement e ON r.equipement_id = e.id
            WHERE r.id = ?
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ════════════════════════════════════════════
       COUNT THIS MONTH — pour la stat du backoffice
    ════════════════════════════════════════════ */
    public function countThisMonth() {
        $stmt = $this->pdo->query("
            SELECT COUNT(*) FROM reservation
            WHERE MONTH(created_at) = MONTH(NOW())
              AND YEAR(created_at)  = YEAR(NOW())
        ");
        return (int)$stmt->fetchColumn();
    }

    /* ════════════════════════════════════════════
       CREATE — Insérer une nouvelle réservation
    ════════════════════════════════════════════ */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reservation
                (equipement_id, locataire_nom, matricule, locataire_ville,
                 date_debut, date_fin, statut, telephone)
            VALUES
                (:equipement_id, :locataire_nom, :matricule, :locataire_ville,
                 :date_debut, :date_fin, :statut, :telephone)
        ");

        return $stmt->execute([
            ':equipement_id'   => $data['equipement_id'],
            ':locataire_nom'   => $data['locataire_nom'],
            ':matricule'       => $data['matricule'] ?? null,
            ':locataire_ville' => $data['locataire_ville'] ?? '',
            ':date_debut'      => $data['date_debut'],
            ':date_fin'        => $data['date_fin'] ?? null,
            ':statut'          => $data['statut'] ?? 'en_cours',
            ':telephone'       => $data['telephone'] ?? '',
        ]);
    }

    /* ════════════════════════════════════════════
       UPDATE — Modifier une réservation existante
    ════════════════════════════════════════════ */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE reservation
            SET
                equipement_id   = :equipement_id,
                locataire_nom   = :locataire_nom,
                matricule       = :matricule,
                locataire_ville = :locataire_ville,
                date_debut      = :date_debut,
                date_fin        = :date_fin,
                statut          = :statut,
                telephone       = :telephone
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'              => (int)$id,
            ':equipement_id'   => $data['equipement_id'],
            ':locataire_nom'   => $data['locataire_nom'],
            ':matricule'       => $data['matricule'] ?? null,
            ':locataire_ville' => $data['locataire_ville'] ?? '',
            ':date_debut'      => $data['date_debut'],
            ':date_fin'        => $data['date_fin'] ?? null,
            ':statut'          => $data['statut'] ?? 'en_cours',
            ':telephone'       => $data['telephone'] ?? '',
        ]);
    }

    /* ════════════════════════════════════════════
       DELETE — Supprimer une réservation
    ════════════════════════════════════════════ */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reservation WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}