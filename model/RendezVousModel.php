<?php
// ============================================================
//  RendezVousModel.php — TOUTES les requêtes PDO
//  C'est la seule classe qui parle à la base de données
//  Le Controller l'utilise, jamais directement la View
// ============================================================

require_once __DIR__ . '/../config.php';

class RendezVousModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    // ============================================================
    //  CREATE — Insérer un nouveau rendez-vous
    // ============================================================
    public function insertRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure)
    {
        $sql = "INSERT INTO rendez_vous
                    (medecin_id, patient_nom, patient_prenom, cin, genre, date_rdv, heure_rdv, statut)
                VALUES
                    (:medecin_id, :nom, :prenom, :cin, :genre, :date, :heure, 'en_attente')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':medecin_id' => $medecin_id,
            ':nom'        => $nom,
            ':prenom'     => $prenom,
            ':cin'        => $cin,
            ':genre'      => $genre,
            ':date'       => $date,
            ':heure'      => $heure,
        ]);

        return $this->pdo->lastInsertId();
    }

    // ============================================================
    //  READ — Récupérer un RDV par son ID
    // ============================================================
    public function getRdvById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rendez_vous WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ============================================================
    //  READ — Récupérer tous les RDV d'un médecin
    // ============================================================
    public function getRdvByMedecin($medecin_id, $statut = '')
    {
        if ($statut && in_array($statut, ['en_attente', 'confirme', 'annule'])) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM rendez_vous
                 WHERE medecin_id = :mid AND statut = :statut
                 ORDER BY date_rdv ASC, heure_rdv ASC"
            );
            $stmt->execute([':mid' => $medecin_id, ':statut' => $statut]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM rendez_vous
                 WHERE medecin_id = :mid
                 ORDER BY date_rdv ASC, heure_rdv ASC"
            );
            $stmt->execute([':mid' => $medecin_id]);
        }
        return $stmt->fetchAll();
    }

    // ============================================================
    //  READ — Récupérer les RDV d'un médecin pour une semaine
    // ============================================================
    public function getRdvSemaine($medecin_id, $date_debut, $date_fin)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM rendez_vous
             WHERE medecin_id = :mid
             AND date_rdv BETWEEN :debut AND :fin
             ORDER BY date_rdv ASC, heure_rdv ASC"
        );
        $stmt->execute([
            ':mid'   => $medecin_id,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
        ]);
        return $stmt->fetchAll();
    }

    // ============================================================
    //  READ — Récupérer les événements planning d'un médecin
    // ============================================================
    public function getPlanningByMedecin($medecin_id, $date_debut, $date_fin)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM planning
             WHERE medecin_id = :mid
             AND DATE(date_debut) BETWEEN :debut AND :fin
             ORDER BY date_debut ASC"
        );
        $stmt->execute([
            ':mid'   => $medecin_id,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
        ]);
        return $stmt->fetchAll();
    }

    // ============================================================
    //  READ — Stats du médecin (pour dashboard)
    // ============================================================
    public function getStatsMedecin($medecin_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                COUNT(*)                    AS total,
                SUM(statut='confirme')      AS nb_confirmes,
                SUM(statut='en_attente')    AS nb_attente,
                SUM(statut='annule')        AS nb_annules,
                SUM(date_rdv = CURDATE())   AS nb_aujourdhui
             FROM rendez_vous
             WHERE medecin_id = :mid"
        );
        $stmt->execute([':mid' => $medecin_id]);
        return $stmt->fetch();
    }

    // ============================================================
    //  UPDATE — Modifier date, heure et statut d'un RDV
    // ============================================================
    public function updateRdv($id, $date, $heure, $statut, $medecin_id)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE rendez_vous
             SET date_rdv = :date, heure_rdv = :heure, statut = :statut
             WHERE id = :id AND medecin_id = :mid"
        );
        $stmt->execute([
            ':date'   => $date,
            ':heure'  => $heure,
            ':statut' => $statut,
            ':id'     => $id,
            ':mid'    => $medecin_id,
        ]);
    }

    // ============================================================
    //  DELETE — Supprimer un RDV
    // ============================================================
    public function deleteRdv($id, $medecin_id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM rendez_vous WHERE id = :id AND medecin_id = :mid"
        );
        $stmt->execute([':id' => $id, ':mid' => $medecin_id]);
    }

    // ============================================================
    //  INSERT — Ajouter un événement dans le planning
    // ============================================================
    public function insertPlanning($medecin_id, $titre, $date_debut, $date_fin, $type, $note)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO planning (medecin_id, titre, date_debut, date_fin, type, note)
             VALUES (:mid, :titre, :debut, :fin, :type, :note)"
        );
        $stmt->execute([
            ':mid'   => $medecin_id,
            ':titre' => $titre,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
            ':type'  => $type,
            ':note'  => $note,
        ]);
    }

    // ============================================================
    //  UPDATE — Modifier un événement du planning
    // ============================================================
    public function updatePlanning($id, $medecin_id, $titre, $date_debut, $date_fin, $type, $note)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE planning
             SET titre = :titre, date_debut = :debut, date_fin = :fin, type = :type, note = :note
             WHERE id = :id AND medecin_id = :mid"
        );
        $stmt->execute([
            ':titre' => $titre,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
            ':type'  => $type,
            ':note'  => $note,
            ':id'    => $id,
            ':mid'   => $medecin_id,
        ]);
    }

    // ============================================================
    //  DELETE — Supprimer un événement du planning
    // ============================================================
    public function deletePlanning($id, $medecin_id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM planning WHERE id = :id AND medecin_id = :mid"
        );
        $stmt->execute([':id' => $id, ':mid' => $medecin_id]);
    }

    // ============================================================
    //  ADMIN — Tous les RDV groupés par médecin
    //  Appelé par : RendezVousController -> admin_dashboard.php
    //  Retourne : tableau [ medecin_id => ['info'=>[...], 'rdvs'=>[...]] ]
    // ============================================================
    public function getAllRdvsGroupesMedecin($filtre_statut = '', $filtre_medecin = 0, $recherche = '')
    {
        $conditions = ['1=1'];
        $params     = [];

        if ($filtre_statut && in_array($filtre_statut, ['en_attente','confirme','annule'])) {
            $conditions[] = 'r.statut = :statut';
            $params[':statut'] = $filtre_statut;
        }

        if ($filtre_medecin > 0) {
            $conditions[] = 'r.medecin_id = :mid';
            $params[':mid'] = $filtre_medecin;
        }

        if (!empty($recherche)) {
            $conditions[] = "(r.patient_nom LIKE :rech OR r.patient_prenom LIKE :rech2 OR r.cin LIKE :rech3)";
            $params[':rech']  = "%$recherche%";
            $params[':rech2'] = "%$recherche%";
            $params[':rech3'] = "%$recherche%";
        }

        $where = implode(' AND ', $conditions);

        // JOIN sur utilisateurs (pas de table medecins)
        // Les médecins ont id_role = 2 dans utilisateurs
        $sql = "SELECT r.*,
                       u.nom    AS medecin_nom,
                       u.prenom AS medecin_prenom
                FROM rendez_vous r
                LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
                WHERE $where
                ORDER BY r.medecin_id ASC, r.date_rdv ASC, r.heure_rdv ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Grouper par médecin
        $grouped = [];
        foreach ($rows as $row) {
            $mid = $row['medecin_id'];
            if (!isset($grouped[$mid])) {
                $grouped[$mid] = [
                    'info' => [
                        'id'     => $mid,
                        'nom'    => $row['medecin_nom']    ?? 'Médecin #' . $mid,
                        'prenom' => $row['medecin_prenom'] ?? '',
                    ],
                    'rdvs' => [],
                ];
            }
            $grouped[$mid]['rdvs'][] = $row;
        }

        return $grouped;
    }

    // ============================================================
    //  ADMIN — Stats globales (toutes les RDV, tous les médecins)
    //  Appelé par : RendezVousController -> admin_dashboard.php
    // ============================================================
    public function getStatsAdmin()
    {
        $stmt = $this->pdo->query(
            "SELECT
                COUNT(*)                            AS total,
                SUM(statut = 'confirme')            AS nb_confirmes,
                SUM(statut = 'en_attente')          AS nb_attente,
                SUM(statut = 'annule')              AS nb_annules,
                SUM(date_rdv = CURDATE())           AS nb_aujourdhui,
                COUNT(DISTINCT medecin_id)          AS nb_medecins
             FROM rendez_vous"
        );
        return $stmt->fetch();
    }

    // ============================================================
    //  ADMIN — Liste des médecins qui ont au moins 1 RDV (filtre)
    //  Appelé par : RendezVousController -> admin_dashboard.php
    // ============================================================
    public function getMedecinsAvecRdv()
    {
        // JOIN sur utilisateurs — les médecins ont id_role = 2
        $stmt = $this->pdo->query(
            "SELECT DISTINCT u.id_PK AS id, u.nom, u.prenom
             FROM rendez_vous r
             LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
             ORDER BY u.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ============================================================
    //  ADMIN — Changer le statut d'un RDV
    //  Appelé par : RendezVousController -> admin_dashboard.php
    // ============================================================
    public function updateStatutRdv($id, $statut)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE rendez_vous SET statut = :statut WHERE id = :id"
        );
        $stmt->execute([':statut' => $statut, ':id' => $id]);
    }
}
?>