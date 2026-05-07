<?php

namespace Models;

require_once __DIR__ . '/../config.php';

class RendezVousModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = \config::getConnexion();
    }

    // ============================================================
    //  CREATE — Insérer un nouveau rendez-vous
    // ============================================================
    public function insertRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure, $patient_email = null)
    {
        $sql = "INSERT INTO rendez_vous
                    (medecin_id, patient_nom, patient_prenom, cin, genre, date_rdv, heure_rdv, statut, patient_email)
                VALUES
                    (:medecin_id, :nom, :prenom, :cin, :genre, :date, :heure, 'en_attente', :patient_email)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':medecin_id'    => $medecin_id,
            ':nom'           => $nom,
            ':prenom'        => $prenom,
            ':cin'           => $cin,
            ':genre'         => $genre,
            ':date'          => $date,
            ':heure'         => $heure,
            ':patient_email' => $patient_email,
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
        $s = $stmt->fetch();
        // Fallback for nulls
        foreach(['total','nb_confirmes','nb_attente','nb_annules','nb_aujourdhui'] as $k) {
            if(!isset($s[$k])) $s[$k] = 0;
        }
        return $s;
    }

    public function getDashboardData($medecin_id, $filtre = '')
    {
        $rendez_vous = $this->getRdvByMedecin($medecin_id, $filtre);
        $stats       = $this->getStatsMedecin($medecin_id);
        return compact('rendez_vous', 'stats');
    }

    // ============================================================
    //  UPDATE — Modifier date, heure et statut d'un RDV
    // ============================================================
    public function updateRdv($id, $date, $heure, $statut)
    {
        // Removed medecin_id check here to keep it simple as in old files, 
        // but controller should ideally provide it.
        $stmt = $this->pdo->prepare(
            "UPDATE rendez_vous
             SET date_rdv = :date, heure_rdv = :heure, statut = :statut
             WHERE id = :id"
        );
        $stmt->execute([
            ':date'   => $date,
            ':heure'  => $heure,
            ':statut' => $statut,
            ':id'     => $id
        ]);
    }

    public function supprimerRdv($id, $medecin_id)
    {
        $this->deleteRdv($id, $medecin_id);
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

    // --- OLD FILES WRAPPERS ---
    public function ajouterEvenement($medecin_id)
    {
        $titre = $_POST['titre'] ?? '';
        $debut = $_POST['date_debut'] ?? '';
        $fin   = $_POST['date_fin']   ?? '';
        $type  = $_POST['type']       ?? 'autre';
        $note  = $_POST['note']       ?? '';
        $this->insertPlanning($medecin_id, $titre, $debut, $fin, $type, $note);
    }

    public function modifierEvenement($medecin_id)
    {
        $id    = intval($_POST['event_id'] ?? 0);
        $titre = $_POST['titre'] ?? '';
        $debut = $_POST['date_debut'] ?? '';
        $fin   = $_POST['date_fin']   ?? '';
        $type  = $_POST['type']       ?? 'autre';
        $note  = $_POST['note']       ?? '';
        $this->updatePlanning($id, $medecin_id, $titre, $debut, $fin, $type, $note);
    }

    public function supprimerEvenement($id, $medecin_id)
    {
        $this->deletePlanning($id, $medecin_id);
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
            "SELECT DISTINCT u.id_PK AS id, u.nom, u.prenom, u.mail
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

    // ============================================================
    //  JOIN — RDV d'un médecin + ses blocages planning sur une semaine
    //  Retourne les RDV enrichis avec les infos du médecin (utilisateurs)
    //  Appelé par : planning-patient.php (vue complète d'un médecin)
    // ============================================================
    public function getRdvsAvecMedecin($medecin_id, $date_debut, $date_fin)
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                r.*,
                u.nom       AS medecin_nom,
                u.prenom    AS medecin_prenom,
                u.mail      AS medecin_mail
             FROM rendez_vous r
             INNER JOIN utilisateurs u ON u.id_PK = r.medecin_id
             WHERE r.medecin_id = :mid
               AND r.date_rdv BETWEEN :debut AND :fin
               AND r.statut != 'annule'
             ORDER BY r.date_rdv ASC, r.heure_rdv ASC"
        );
        $stmt->execute([
            ':mid'   => $medecin_id,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
        ]);
        return $stmt->fetchAll();
    }

    // ============================================================
    //  JOIN — Blocages planning d'un médecin sur une semaine
    //  Enrichis avec les infos du médecin (utilisateurs)
    //  Appelé par : planning-patient.php
    // ============================================================
    public function getPlanningAvecMedecin($medecin_id, $date_debut, $date_fin)
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                p.*,
                u.nom       AS medecin_nom,
                u.prenom    AS medecin_prenom
             FROM planning p
             INNER JOIN utilisateurs u ON u.id_PK = p.medecin_id
             WHERE p.medecin_id = :mid
               AND DATE(p.date_debut) BETWEEN :debut AND :fin
             ORDER BY p.date_debut ASC"
        );
        $stmt->execute([
            ':mid'   => $medecin_id,
            ':debut' => $date_debut,
            ':fin'   => $date_fin,
        ]);
        return $stmt->fetchAll();
    }

    // ============================================================
    //  JOIN — Vue admin : tous les RDV avec infos médecin + résumé planning
    //  Retourne chaque RDV avec le nb de blocages du médecin ce jour-là
    //  Appelé par : admin_dashboard (version enrichie future)
    // ============================================================
    public function getRdvsAvecInfosMedecin($filtre_statut = '', $filtre_medecin = 0, $recherche = '')
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

        $stmt = $this->pdo->prepare(
            "SELECT
                r.*,
                u.nom    AS medecin_nom,
                u.prenom AS medecin_prenom,
                -- Nombre de blocages planning du médecin ce jour-là
                (SELECT COUNT(*) FROM planning p
                 WHERE p.medecin_id = r.medecin_id
                   AND DATE(p.date_debut) = r.date_rdv) AS nb_blocages_jour
             FROM rendez_vous r
             INNER JOIN utilisateurs u ON u.id_PK = r.medecin_id
             WHERE $where
             ORDER BY r.medecin_id ASC, r.date_rdv ASC, r.heure_rdv ASC"
        );
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

    /**
     * Récupère les RDV et les événements planning pour un médecin sur une période donnée.
     * Utilisé par le calendrier hebdomadaire du médecin.
     */
    public function getPlanningData($medecin_id, $date_debut, $date_fin): array
    {
        // 1. Récupérer les RDVs
        $stmt_rdv = $this->pdo->prepare(
            "SELECT * FROM rendez_vous 
             WHERE medecin_id = :mid AND date_rdv BETWEEN :debut AND :fin"
        );
        $stmt_rdv->execute([':mid' => $medecin_id, ':debut' => $date_debut, ':fin' => $date_fin]);
        $rdvs = $stmt_rdv->fetchAll();

        // 2. Récupérer les blocages planning
        $stmt_plan = $this->pdo->prepare(
            "SELECT * FROM planning 
             WHERE medecin_id = :mid AND DATE(date_debut) BETWEEN :debut AND :fin"
        );
        $stmt_plan->execute([':mid' => $medecin_id, ':debut' => $date_debut, ':fin' => $date_fin]);
        $events = $stmt_plan->fetchAll();

        $par_jour = [];
        foreach ($rdvs as $rdv) {
            $jour = $rdv['date_rdv'];
            $par_jour[$jour][] = [
                'source' => 'rdv',
                'id'     => $rdv['id'],
                'titre'  => $rdv['patient_prenom'] . ' ' . $rdv['patient_nom'],
                'debut'  => $rdv['heure_rdv'],
                'fin'    => null,
                'type'   => $rdv['statut'],
                'note'   => 'CIN: ' . $rdv['cin']
            ];
        }
        foreach ($events as $ev) {
            $jour = date('Y-m-d', strtotime($ev['date_debut']));
            $par_jour[$jour][] = [
                'source' => 'planning',
                'id'     => $ev['id'],
                'titre'  => $ev['titre'],
                'debut'  => date('H:i', strtotime($ev['date_debut'])),
                'fin'    => date('H:i', strtotime($ev['date_fin'])),
                'type'   => $ev['type'],
                'note'   => $ev['note'],
                'jour'   => $jour,
                'debut_dt' => date('Y-m-d\TH:i', strtotime($ev['date_debut'])),
                'fin_dt'   => date('Y-m-d\TH:i', strtotime($ev['date_fin']))
            ];
        }
        return $par_jour;
    }
    public function addRdv(int $medecin_id, string $nom, string $prenom, string $cin, string $genre, string $date, string $heure, ?string $patient_email = null): int|false
    {
        $result = $this->insertRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure, $patient_email);
        return $result ? (int)$result : false;
    }
    public function creneauDejaReserve(int $medecin_id, string $date, string $heure): bool
{
    $pdo = \config::getConnexion();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM rendez_vous
        WHERE medecin_id = :mid
          AND date_rdv   = :date
          AND heure_rdv  = :heure
          AND statut    != 'annule'
    ");
    $stmt->execute([
        ':mid'   => $medecin_id,
        ':date'  => $date,
        ':heure' => $heure,
    ]);
    return (int)$stmt->fetchColumn() > 0;
}
}
?>