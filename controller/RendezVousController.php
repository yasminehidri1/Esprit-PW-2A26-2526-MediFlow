<?php
// ============================================================
//  RendezVousController.php — Fait le lien entre Model et View
//  Il ne contient PAS de HTML
//  Il ne contient PAS de requêtes SQL directes
// ============================================================

require_once __DIR__ . '/../model/RendezVousModel.php';

class RendezVousController
{
    private $model;

    public function __construct()
    {
        $this->model = new RendezVousModel();
    }

    // ============================================================
    //  ACTION : Enregistrer un nouveau RDV (depuis traitement-rdv.php)
    // ============================================================
    public function enregistrerRdv()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../patient/annuaire.php');
            exit;
        }

        $medecin_id = isset($_POST['medecin_id']) ? intval($_POST['medecin_id'])            : 0;
        $nom        = isset($_POST['nom'])         ? htmlspecialchars(trim($_POST['nom']))   : '';
        $prenom     = isset($_POST['prenom'])      ? htmlspecialchars(trim($_POST['prenom'])): '';
        $cin        = isset($_POST['cin'])         ? trim($_POST['cin'])                     : '';
        $genre      = isset($_POST['genre'])       ? $_POST['genre']                        : '';
        $date       = isset($_POST['date_rdv'])    ? $_POST['date_rdv']                     : '';
        $heure      = isset($_POST['heure_rdv'])   ? $_POST['heure_rdv']                    : '';

        $erreurs = [];

        if (empty($nom))
            $erreurs[] = "Le nom est requis.";
        elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $nom))
            $erreurs[] = "Le nom ne doit contenir que des lettres.";

        if (empty($prenom))
            $erreurs[] = "Le prénom est requis.";
        elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $prenom))
            $erreurs[] = "Le prénom ne doit contenir que des lettres.";

        if (empty($cin))
            $erreurs[] = "Le CIN est requis.";
        elseif (!preg_match('/^[0-9]{8}$/', $cin))
            $erreurs[] = "Le CIN doit contenir exactement 8 chiffres.";

        if (!in_array($genre, ['homme', 'femme']))
            $erreurs[] = "Veuillez sélectionner un genre.";

        if (empty($date))
            $erreurs[] = "La date est requise.";
        elseif ($date < date('Y-m-d'))
            $erreurs[] = "La date ne peut pas être dans le passé.";

        if (empty($heure))
            $erreurs[] = "L'heure est requise.";

        if ($medecin_id === 0)
            $erreurs[] = "Médecin invalide.";

        if (!empty($erreurs)) {
            $msg = urlencode(implode(' | ', $erreurs));
            header("Location: ../patient/rdv.php?medecin_id=$medecin_id&erreur=$msg");
            exit;
        }

        $rdv_id = $this->model->insertRdv(
            $medecin_id, $nom, $prenom, $cin, $genre, $date, $heure
        );

        header("Location: ../patient/confirmation.php?rdv_id=$rdv_id");
        exit;
    }

    // ============================================================
    //  ACTION : Récupérer les données pour le dashboard médecin
    // ============================================================
    public function getDashboardData($medecin_id, $filtre_statut = '')
    {
        return [
            'rendez_vous' => $this->model->getRdvByMedecin($medecin_id, $filtre_statut),
            'stats'       => $this->model->getStatsMedecin($medecin_id),
        ];
    }

    // ============================================================
    //  ACTION : Modifier un RDV (depuis dashboard.php)
    // ============================================================
    public function modifierRdv($medecin_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard.php');
            exit;
        }

        $id     = intval($_POST['rdv_id']  ?? 0);
        $date   = $_POST['date_rdv']        ?? '';
        $heure  = $_POST['heure_rdv']       ?? '';
        $statut = $_POST['statut']          ?? '';

        $ok = $id && $date && $heure
              && in_array($statut, ['en_attente','confirme','annule'])
              && $date >= date('Y-m-d');

        if ($ok) {
            $this->model->updateRdv($id, $date, $heure, $statut, $medecin_id);
            header('Location: dashboard.php?succes=modifie');
        } else {
            header('Location: dashboard.php?erreur=1');
        }
        exit;
    }

    // ============================================================
    //  ACTION : Supprimer un RDV (depuis dashboard.php)
    // ============================================================
    public function supprimerRdv($id, $medecin_id)
    {
        $this->model->deleteRdv($id, $medecin_id);
        header('Location: dashboard.php?succes=supprime');
        exit;
    }

    // ============================================================
    //  ACTION : Récupérer les données pour le planning
    // ============================================================
    public function getPlanningData($medecin_id, $date_debut, $date_fin)
    {
        $rdvs   = $this->model->getRdvSemaine($medecin_id, $date_debut, $date_fin);
        $events = $this->model->getPlanningByMedecin($medecin_id, $date_debut, $date_fin);

        $par_jour = [];

        foreach ($rdvs as $rdv) {
            $jour = $rdv['date_rdv'];
            $par_jour[$jour][] = [
                'source'  => 'rdv',
                'id'      => $rdv['id'],
                'titre'   => $rdv['patient_prenom'] . ' ' . $rdv['patient_nom'],
                'debut'   => $rdv['heure_rdv'],
                'fin'     => null,
                'type'    => $rdv['statut'],
                'note'    => 'CIN : ' . $rdv['cin'],
            ];
        }

        foreach ($events as $ev) {
            $jour = date('Y-m-d', strtotime($ev['date_debut']));
            $par_jour[$jour][] = [
                'source'   => 'planning',
                'id'       => $ev['id'],
                'titre'    => $ev['titre'],
                'debut'    => date('H:i:s', strtotime($ev['date_debut'])),
                'fin'      => date('H:i:s', strtotime($ev['date_fin'])),
                'type'     => $ev['type'],
                'note'     => $ev['note'],
                'jour'     => $jour,
                'debut_dt' => date('Y-m-d\TH:i', strtotime($ev['date_debut'])),
                'fin_dt'   => date('Y-m-d\TH:i', strtotime($ev['date_fin'])),
            ];
        }

        return $par_jour;
    }

    // ============================================================
    //  ACTION : Récupérer un RDV (pour confirmation.php)
    // ============================================================
    public function getRdvById($id)
    {
        return $this->model->getRdvById($id);
    }

    // ============================================================
    //  ACTION : Ajouter un événement planning (médecin)
    // ============================================================
    public function ajouterEvenement($medecin_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: planning.php');
            exit;
        }

        $titre      = htmlspecialchars(trim($_POST['titre']      ?? ''));
        $date_debut = $_POST['date_debut'] ?? '';
        $date_fin   = $_POST['date_fin']   ?? '';
        $type       = $_POST['type']       ?? 'autre';
        $note       = htmlspecialchars(trim($_POST['note']       ?? ''));

        if (empty($titre) || empty($date_debut) || empty($date_fin)) {
            header('Location: planning.php?erreur=champs_manquants');
            exit;
        }

        $this->model->insertPlanning($medecin_id, $titre, $date_debut, $date_fin, $type, $note);
        header('Location: planning.php?succes=evenement_ajoute');
        exit;
    }

    // ============================================================
    //  ACTION : Modifier un événement planning (médecin)
    // ============================================================
    public function modifierEvenement($medecin_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: planning.php');
            exit;
        }

        $id         = intval($_POST['event_id']   ?? 0);
        $titre      = htmlspecialchars(trim($_POST['titre']      ?? ''));
        $date_debut = $_POST['date_debut'] ?? '';
        $date_fin   = $_POST['date_fin']   ?? '';
        $type       = $_POST['type']       ?? 'autre';
        $note       = htmlspecialchars(trim($_POST['note']       ?? ''));

        if ($id === 0 || empty($titre) || empty($date_debut) || empty($date_fin)) {
            header('Location: planning.php?erreur=champs_manquants');
            exit;
        }

        $this->model->updatePlanning($id, $medecin_id, $titre, $date_debut, $date_fin, $type, $note);
        header('Location: planning.php?succes=evenement_modifie');
        exit;
    }

    // ============================================================
    //  ACTION : Supprimer un événement planning (médecin)
    // ============================================================
    public function supprimerEvenement($id, $medecin_id)
    {
        $this->model->deletePlanning($id, $medecin_id);
        header('Location: planning.php?succes=evenement_supprime');
        exit;
    }

    // ============================================================
    //  ADMIN — Récupérer tous les RDV groupés par médecin
    //  Appelé par : admin_dashboard.php
    // ============================================================
    public function getAdminDashboardData($filtre_statut = '', $filtre_medecin = 0, $recherche = '')
    {
        return [
            'rdvs_grouped' => $this->model->getAllRdvsGroupesMedecin($filtre_statut, $filtre_medecin, $recherche),
            'stats'        => $this->model->getStatsAdmin(),
            'medecins'     => $this->model->getMedecinsAvecRdv(),
        ];
    }

    // ============================================================
    //  ADMIN — Changer le statut d'un RDV (depuis admin_dashboard)
    // ============================================================
    public function adminChangerStatut()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin_dashboard.php');
            exit;
        }

        $id     = intval($_POST['rdv_id'] ?? 0);
        $statut = $_POST['statut']        ?? '';

        if ($id > 0 && in_array($statut, ['en_attente','confirme','annule'])) {
            $this->model->updateStatutRdv($id, $statut);
            header('Location: admin_dashboard.php?succes=statut_modifie');
        } else {
            header('Location: admin_dashboard.php?erreur=1');
        }
        exit;
    }
}
?>