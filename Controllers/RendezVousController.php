<?php

namespace Controllers;

use Models\RendezVousModel;
use Core\SessionHelper;

class RendezVousController
{
    use SessionHelper;

    private RendezVousModel $model;

    public function __construct()
    {
        $this->model = new RendezVousModel();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $currentView = $view;
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* --- ADMIN - Dashboard --- */
    public function adminDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        if ($_SESSION['user']['id_role'] != 1) { // 1 = Admin
            header('Location: /integration/rdv/dashboard');
            exit;
        }

        $filtre_statut   = $_GET['statut']   ?? '';
        $filtre_medecin  = intval($_GET['medecin'] ?? 0);
        $recherche       = trim($_GET['search']    ?? '');

        $medecins_rdv    = $this->model->getAllRdvsGroupesMedecin($filtre_statut, $filtre_medecin, $recherche);
        $stats           = $this->model->getStatsAdmin();
        $liste_medecins  = $this->model->getMedecinsAvecRdv();

        $this->render('rdv_admin_dashboard', compact('medecins_rdv','stats','liste_medecins','filtre_statut','filtre_medecin','recherche'));
    }

    /* --- DOCTOR --- */
    public function doctorDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];
        
        // Handle actions from old dashboard
        if (isset($_GET['supprimer'])) {
            $this->model->supprimerRdv(intval($_GET['supprimer']), $medecin_id);
            header("Location: /integration/rdv/dashboard?succes=" . urlencode("Rendez-vous supprimé."));
            exit;
        }

        $filtre      = isset($_GET['statut']) && in_array($_GET['statut'],['en_attente','confirme','annule']) ? $_GET['statut'] : '';
        $data        = $this->model->getDashboardData($medecin_id, $filtre);
        $rendez_vous = $data['rendez_vous'];
        $stats       = $data['stats'];

        // Tri : du plus récent au plus ancien
        usort($rendez_vous, fn($a,$b) => strcmp($b['date_rdv'].' '.$b['heure_rdv'], $a['date_rdv'].' '.$a['heure_rdv']));

        // Pagination
        $per_page    = 8;
        $total_rdv   = count($rendez_vous);
        $total_pages = max(1, (int)ceil($total_rdv/$per_page));
        $page        = max(1, min($total_pages, intval($_GET['page'] ?? 1)));
        $offset      = ($page-1)*$per_page;
        $rdv_page    = array_slice($rendez_vous, $offset, $per_page);

        // Prochain RDV du jour
        $today    = date('Y-m-d');
        $now_time = date('H:i:s');
        $prochain = null;
        foreach (array_filter($rendez_vous, fn($r)=>$r['date_rdv']===$today) as $r) {
            if ($r['heure_rdv'] >= $now_time && $r['statut'] !== 'annule') {
                if (!$prochain || $r['heure_rdv'] < $prochain['heure_rdv']) $prochain = $r;
            }
        }

        // Taux de confirmation
        $taux_conf = $stats['total'] > 0 ? round(($stats['total']-$stats['nb_attente'])/$stats['total']*100) : 0;

        // Calcul RDV par jour de la semaine
        $rdv_par_jour = array_fill(0,7,0);
        $debut_sem = new \DateTime(); $debut_sem->setISODate((int)date('Y'),(int)date('W')); $debut_sem->setTime(0,0,0);
        foreach ($rendez_vous as $r) {
            $d = new \DateTime($r['date_rdv']);
            $diff = (int)$debut_sem->diff($d)->days;
            if ($diff >= 0 && $diff < 7) $rdv_par_jour[$diff]++;
        }
        $max_rdv_jour = max(1, max($rdv_par_jour));

        $msg_succes = $_GET['succes'] ?? '';
        $msg_erreur = $_GET['erreur'] ?? '';

        $this->render('dashboard_doctor', compact(
            'rdv_page','stats','total_rdv','total_pages','page','filtre',
            'prochain','taux_conf','rdv_par_jour','max_rdv_jour','msg_succes','msg_erreur'
        ));
    }

    /* --- DOCTOR - Planning --- */
    public function doctorPlanning(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        // ACTIONS FROM OLD PLANNING
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_evenement') {
            $this->model->ajouterEvenement($medecin_id);
            header("Location: /integration/rdv/planning?succes=1");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier_evenement') {
            $this->model->modifierEvenement($medecin_id);
            header("Location: /integration/rdv/planning?succes=1");
            exit;
        }
        if (isset($_GET['supprimer_event'])) {
            $this->model->supprimerEvenement(intval($_GET['supprimer_event']), $medecin_id);
            header("Location: /integration/rdv/planning?succes=1");
            exit;
        }

        $vue_active = in_array($_GET['vue'] ?? '', ['jour','semaine','mois']) ? $_GET['vue'] : 'semaine';
        $date_ref_str = $_GET['date'] ?? date('Y-m-d');
        $date_ref     = new \DateTime($date_ref_str);
        $date_ref->setTime(0,0,0);

        if ($vue_active === 'jour') {
            $date_debut = clone $date_ref;
            $date_fin   = clone $date_ref;
        } elseif ($vue_active === 'mois') {
            $date_debut = new \DateTime($date_ref->format('Y-m-01'));
            $date_fin   = new \DateTime($date_ref->format('Y-m-t'));
        } else { // semaine
            $semaine_param = $_GET['semaine'] ?? $date_ref->format('Y-\WW');
            $date_debut    = new \DateTime();
            $date_debut->setISODate(...explode('-W', $semaine_param));
            $date_debut->setTime(0, 0, 0);
            $date_fin = clone $date_debut;
            $date_fin->modify('+4 days');
        }
        $date_debut->setTime(0,0,0);
        $debut_str = $date_debut->format('Y-m-d');
        $fin_str   = $date_fin->format('Y-m-d');

        $par_jour = $this->model->getPlanningData($medecin_id, $debut_str, $fin_str);
        $stats_data = $this->model->getDashboardData($medecin_id);
        $stats = $stats_data['stats'];

        // Navigation
        $prev_date = clone $date_debut;
        $next_date = clone $date_debut;
        if ($vue_active === 'jour') {
            $prev_date->modify('-1 day');
            $next_date->modify('+1 day');
            $url_prec = '/integration/rdv/planning?vue=jour&date=' . $prev_date->format('Y-m-d');
            $url_suiv = '/integration/rdv/planning?vue=jour&date=' . $next_date->format('Y-m-d');
        } elseif ($vue_active === 'mois') {
            $prev_date->modify('-1 month');
            $next_date->modify('+1 month');
            $url_prec = '/integration/rdv/planning?vue=mois&date=' . $prev_date->format('Y-m-01');
            $url_suiv = '/integration/rdv/planning?vue=mois&date=' . $prev_date->format('Y-m-01');
        } else {
            $sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
            $sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
            $url_prec = '/integration/rdv/planning?vue=semaine&semaine=' . $sem_prec->format('Y-\WW');
            $url_suiv = '/integration/rdv/planning?vue=semaine&semaine=' . $sem_suiv->format('Y-\WW');
        }

        $jours = [];
        if ($vue_active === 'jour') { $jours[] = clone $date_debut; }
        elseif ($vue_active === 'mois') {
            $nb_jours_mois = (int)$date_fin->format('d');
            for ($i = 0; $i < $nb_jours_mois; $i++) { $j = clone $date_debut; $j->modify("+$i days"); $jours[] = $j; }
        } else {
            for ($i = 0; $i < 5; $i++) { $j = clone $date_debut; $j->modify("+$i days"); $jours[] = $j; }
        }

        $prochain_patient = null;
        $today_str = date('Y-m-d');
        $heure_now = date('H:i');
        if (isset($par_jour[$today_str])) {
            foreach ($par_jour[$today_str] as $ev) {
                if ($ev['source'] === 'rdv' && $ev['debut'] >= $heure_now) { $prochain_patient = $ev; break; }
            }
        }

        $mini_cal_mois_param = $_GET['mini_mois'] ?? $date_debut->format('Y-m');
        $mini_cal_date = new \DateTime($mini_cal_mois_param . '-01');
        $mini_cal_mois_prec = clone $mini_cal_date; $mini_cal_mois_prec->modify('-1 month');
        $mini_cal_mois_suiv = clone $mini_cal_date; $mini_cal_mois_suiv->modify('+1 month');
        $mini_cal_first_dow = intval($mini_cal_date->format('N'));
        $mini_cal_days_in_month = intval($mini_cal_date->format('t'));

        $this->render('planning', compact(
            'par_jour','stats','jours','url_prec','url_suiv','vue_active','date_debut','date_fin',
            'debut_str','fin_str','prochain_patient','mini_cal_date','mini_cal_mois_prec',
            'mini_cal_mois_suiv','mini_cal_first_dow','mini_cal_days_in_month','mini_cal_mois_param'
        ));
    }

    /* --- DOCTOR - Statistics --- */
    public function doctorStats(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $today       = date('Y-m-d');
        $year        = (int) date('Y');
        $month       = (int) date('m');
        $last_month  = $month === 1 ? 12 : $month - 1;
        $last_month_year = $month === 1 ? $year - 1 : $year;

        // 1. RDV par mois
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(date_rdv,'%Y-%m') AS mois, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid AND date_rdv >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY mois ORDER BY mois ASC");
        $stmt->execute([':mid' => $medecin_id]);
        $rdv_par_mois = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $mois_labels = []; $mois_data = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = new \DateTime("first day of -$i month"); $lb = $d->format('M Y');
            $mois_labels[] = $lb; $mois_data[] = 0;
        }
        foreach ($rdv_par_mois as $r) {
            $d = new \DateTime($r['mois'] . '-01'); $lb = $d->format('M Y');
            $idx = array_search($lb, $mois_labels); if ($idx !== false) $mois_data[$idx] = (int) $r['nb'];
        }

        // 2. RDV par année
        $stmt = $pdo->prepare("SELECT YEAR(date_rdv) AS annee, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv) >= :y GROUP BY annee ORDER BY annee ASC");
        $stmt->execute([':mid' => $medecin_id, ':y' => $year - 4]);
        $rdv_par_annee = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $annee_labels = []; $annee_data = [];
        for ($y = $year - 4; $y <= $year; $y++) {
            $annee_labels[] = (string) $y;
            $found = array_filter($rdv_par_annee, fn($r) => (int)$r['annee'] === $y);
            $annee_data[] = $found ? (int) array_values($found)[0]['nb'] : 0;
        }

        // 3. Genre
        $stmt = $pdo->prepare("SELECT genre, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY genre");
        $stmt->execute([':mid' => $medecin_id]);
        $genre_rows = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        $nb_homme = (int)($genre_rows['homme'] ?? 0);
        $nb_femme = (int)($genre_rows['femme'] ?? 0);
        $nb_genre_total = $nb_homme + $nb_femme;
        $pct_homme = $nb_genre_total ? round($nb_homme / $nb_genre_total * 100) : 0;
        $pct_femme = $nb_genre_total ? round($nb_femme / $nb_genre_total * 100) : 0;

        // 4. Statut
        $stmt = $pdo->prepare("SELECT statut, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY statut");
        $stmt->execute([':mid' => $medecin_id]);
        $statut_rows   = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        $nb_confirme   = (int)($statut_rows['confirme']   ?? 0);
        $nb_attente    = (int)($statut_rows['en_attente'] ?? 0);
        $nb_annule     = (int)($statut_rows['annule']     ?? 0);
        $nb_total_statut = $nb_confirme + $nb_attente + $nb_annule;

        // 5. KPIs
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND MONTH(date_rdv)=:m AND YEAR(date_rdv)=:y");
        $stmt->execute([':mid'=>$medecin_id,':m'=>$month,':y'=>$year]); $rdv_ce_mois = (int)$stmt->fetchColumn();
        $stmt->execute([':mid'=>$medecin_id,':m'=>$last_month,':y'=>$last_month_year]); $rdv_mois_prec = (int)$stmt->fetchColumn();
        $evol_mois = $rdv_mois_prec > 0 ? round(($rdv_ce_mois - $rdv_mois_prec) / $rdv_mois_prec * 100) : 0;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y");
        $stmt->execute([':mid'=>$medecin_id,':y'=>$year]); $rdv_cette_annee = (int)$stmt->fetchColumn();
        $taux_confirmation = $nb_total_statut > 0 ? round($nb_confirme / $nb_total_statut * 100) : 0;
        $taux_annulation = $nb_total_statut > 0 ? round($nb_annule / $nb_total_statut * 100) : 0;
        $stmt = $pdo->prepare("SELECT AVG(nb) FROM (SELECT MONTH(date_rdv) m, COUNT(*) nb FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y GROUP BY m) t");
        $stmt->execute([':mid'=>$medecin_id,':y'=>$year]); $moy_mois = round((float)$stmt->fetchColumn(), 1);

        $json_mois_labels = json_encode($mois_labels); $json_mois_data = json_encode($mois_data);
        $json_annee_labels = json_encode($annee_labels); $json_annee_data = json_encode($annee_data);

        // 6. Motifs distribution
        $stmt = $pdo->prepare("SELECT motif, COUNT(*) as nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY motif ORDER BY nb DESC");
        $stmt->execute([':mid' => $medecin_id]);
        $motifs_dist = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $total_rdv_stats = array_sum(array_column($motifs_dist, 'nb'));
        foreach($motifs_dist as &$m) {
            $m['pct'] = $total_rdv_stats > 0 ? round($m['nb'] / $total_rdv_stats * 100) : 0;
        }

        $this->render('statistiques', compact(
            'mois_labels','mois_data','annee_labels','annee_data','nb_homme','nb_femme','nb_genre_total',
            'pct_homme','pct_femme','nb_confirme','nb_attente','nb_annule','nb_total_statut',
            'rdv_ce_mois','rdv_mois_prec','evol_mois','rdv_cette_annee','taux_confirmation',
            'taux_annulation','moy_mois','json_mois_labels','json_mois_data','json_annee_labels','json_annee_data',
            'motifs_dist'
        ));
    }

    /* --- DOCTOR - Update --- */
    public function modifierRdvView(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];
        
        $id  = intval($_GET['id'] ?? 0);
        
        // Handle POST update from old modifier-rdv
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouvelle_date   = $_POST['date_rdv']  ?? '';
            $nouvelle_heure  = $_POST['heure_rdv'] ?? '';
            $nouveau_statut  = $_POST['statut']    ?? '';
            $rdv_id          = intval($_POST['rdv_id']);
            
            $this->model->updateRdv($rdv_id, $nouvelle_date, $nouvelle_heure, $nouveau_statut);
            header('Location: /integration/rdv/dashboard?succes=' . urlencode('Rendez-vous modifié.'));
            exit;
        }

        $rdv = $id ? $this->model->getRdvById($id) : null;
        if (!$rdv || $rdv['medecin_id'] != $medecin_id) {
            header('Location: /integration/rdv/dashboard');
            exit;
        }
        $this->render('modifier-rdv', compact('rdv','id'));
    }

    /* ══════════════════════════════════════════════
       PATIENT — Annuaire
    ══════════════════════════════════════════════ */
    public function patientAnnuaire(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $search = trim($_GET['search'] ?? '');
        $spe    = trim($_GET['specialite'] ?? '');

        $sql = "SELECT u.id_PK AS id, u.nom, u.prenom, u.mail, u.tel,
                       COUNT(r.id) AS nb_rdv
                FROM utilisateurs u
                LEFT JOIN rendez_vous r ON r.medecin_id = u.id_PK
                WHERE u.id_role = 2";
        $params = [];
        if ($search) {
            $sql .= " AND (u.nom LIKE :s OR u.prenom LIKE :s2)";
            $params[':s'] = $params[':s2'] = "%$search%";
        }
        $sql .= " GROUP BY u.id_PK ORDER BY u.nom ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $medecins = $stmt->fetchAll();

        $currentView = '../Front/annuaire';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* --- PATIENT - Planning --- */
    public function patientPlanning(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $medecin_id = intval($_GET['medecin_id'] ?? 0);
        if ($medecin_id === 0) {
            header('Location: /integration/rdv/annuaire');
            exit;
        }

        $stmt = $pdo->prepare("SELECT id_PK AS id, nom, prenom, mail FROM utilisateurs WHERE id_PK=:id AND id_role=2");
        $stmt->execute([':id'=>$medecin_id]);
        $medecin = $stmt->fetch();
        if (!$medecin) {
            header('Location: /integration/rdv/annuaire');
            exit;
        }

        $semaine_param = $_GET['semaine'] ?? date('Y-\WW');
        $date_debut = new \DateTime();
        $date_debut->setISODate(...explode('-W', $semaine_param));
        $date_debut->setTime(0, 0, 0);
        $date_fin = clone $date_debut;
        $date_fin->modify('+4 days'); // Lundi → Vendredi

        $debut_str = $date_debut->format('Y-m-d');
        $fin_str   = $date_fin->format('Y-m-d');

        $sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
        $sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
        $url_prec = "?medecin_id=$medecin_id&semaine=" . $sem_prec->format('Y-\WW');
        $url_suiv = "?medecin_id=$medecin_id&semaine=" . $sem_suiv->format('Y-\WW');

        $jours = [];
        for ($i=0; $i<5; $i++) {
            $j = clone $date_debut; $j->modify("+$i days");
            $jours[] = $j;
        }

        // Fetch real data to show "Pris" or "Bloqué"
        $par_jour_raw = $this->model->getPlanningData($medecin_id, $debut_str, $fin_str);
        $pris = [];
        $bloque = [];

        foreach ($par_jour_raw as $jour => $events) {
            foreach ($events as $ev) {
                if ($ev['source'] === 'rdv') {
                    // Check if it's confirmed or pending
                    if ($ev['type'] !== 'annule') {
                        $heure_short = substr($ev['debut'], 0, 5); // 09:00:00 -> 09:00
                        $pris[$jour][$heure_short] = true;
                    }
                } else {
                    // Planning event (indisponible)
                    // We need to map it to the 30-min slots
                    $start_ts = strtotime($ev['debut_dt']);
                    $end_ts   = strtotime($ev['fin_dt']);
                    
                    // Iterate through slots of 30 mins
                    $curr = $start_ts;
                    while ($curr < $end_ts) {
                        $slot_h = date('H:i', $curr);
                        $bloque[$jour][$slot_h] = true;
                        $curr += 1800; // +30 mins
                    }
                }
            }
        }
        
        $currentView = '../Front/planning';
        $data = compact('medecin','jours','url_prec','url_suiv','pris','bloque');
        $this->render($currentView, $data);
    }

    /* --- PATIENT - Booking --- */
    public function patientBookRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = intval($_GET['medecin_id'] ?? 0);
        $date_rdv = $_GET['date_rdv'] ?? '';
        $heure_rdv = $_GET['heure_rdv'] ?? '';

        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();
        $stmt = $pdo->prepare("SELECT id_PK AS id, nom, prenom FROM utilisateurs WHERE id_PK=:id");
        $stmt->execute([':id'=>$medecin_id]);
        $medecin = $stmt->fetch();

        // Get patient info for pre-filling
        $user = $_SESSION['user'];
        $stmt_user = $pdo->prepare("SELECT nom, prenom, cin FROM utilisateurs WHERE id_PK = :id");
        $stmt_user->execute([':id' => $user['id']]);
        $patient = $stmt_user->fetch();

        $currentView = '../Front/rdv';
        $data = compact('medecin','date_rdv','heure_rdv','patient');
        $this->render($currentView, $data);
    }

    /* --- PATIENT - Process --- */
    public function traitementRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/rdv/annuaire');
            exit;
        }
        $medecin_id = intval($_POST['medecin_id']  ?? 0);
        $nom        = htmlspecialchars(trim($_POST['nom']    ?? ''));
        $prenom     = htmlspecialchars(trim($_POST['prenom'] ?? ''));
        $cin        = trim($_POST['cin']    ?? '');
        $genre      = $_POST['genre']       ?? '';
        $date       = $_POST['date_rdv']    ?? '';
        $heure      = $_POST['heure_rdv']   ?? '';
        $erreurs    = [];

        if (!$nom || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $nom))        $erreurs[] = 'Nom invalide.';
        if (!$prenom || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $prenom))  $erreurs[] = 'Prénom invalide.';
        if (!preg_match('/^[0-9]{8}$/', $cin))                           $erreurs[] = 'CIN : 8 chiffres requis.';
        if (!in_array($genre, ['homme','femme']))                         $erreurs[] = 'Genre invalide.';
        if (!$date || $date < date('Y-m-d'))                             $erreurs[] = 'Date invalide.';
        if (!$heure)                                                      $erreurs[] = 'Heure requise.';

        if (!empty($erreurs)) {
            $_SESSION['rdv_erreurs'] = $erreurs;
            header("Location: /integration/rdv/form?medecin_id=$medecin_id&date=$date&heure=$heure");
            exit;
        }

        $res = $this->model->addRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure);
        if ($res) {
            header('Location: /integration/rdv/confirmation?id=' . $res);
        } else {
            header('Location: /integration/rdv/annuaire');
        }
        exit;
    }

    /* --- PATIENT - Confirmation --- */
    public function patientConfirmation(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $id = intval($_GET['id'] ?? 0);
        $rdv = $this->model->getRdvById($id);

        $currentView = '../Front/confirmation';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* --- PATIENT - My RDVs --- */
    public function patientMesRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();
        
        $cin = trim($_POST['cin'] ?? '');
        $is_auto = false;

        // Auto-fetch CIN if not in POST
        if (!$cin && isset($_SESSION['user']['id'])) {
            $stmt_user = $pdo->prepare("SELECT cin FROM utilisateurs WHERE id_PK = :id");
            $stmt_user->execute([':id' => $_SESSION['user']['id']]);
            $user_data = $stmt_user->fetch();
            if ($user_data && !empty($user_data['cin'])) {
                $cin = $user_data['cin'];
                $is_auto = true;
            }
        }

        $mes_rdv = [];
        if ($cin) {
            $stmt = $pdo->prepare(
                "SELECT r.*, u.nom AS medecin_nom, u.prenom AS medecin_prenom
                 FROM rendez_vous r
                 LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
                 WHERE r.cin = :cin
                 ORDER BY r.date_rdv DESC, r.heure_rdv DESC"
            );
            $stmt->execute([':cin' => $cin]);
            $mes_rdv = $stmt->fetchAll();
        }

        $currentView = '../Front/mes_rdv';
        $data = compact('mes_rdv', 'cin', 'is_auto');
        $this->render($currentView, $data);
    }

    /* ══════════════════════════════════════════════
       LEGACY — used by planning view
    ══════════════════════════════════════════════ */
    public function getPlanningData($medecin_id, $date_debut, $date_fin): array
    {
        $rdvs   = $this->model->getRdvSemaine($medecin_id, $date_debut, $date_fin);
        $events = $this->model->getPlanningByMedecin($medecin_id, $date_debut, $date_fin);
        $par_jour = [];
        foreach ($rdvs as $rdv) {
            $jour = $rdv['date_rdv'];
            $par_jour[$jour][] = ['source'=>'rdv','id'=>$rdv['id'],
                'titre'=>$rdv['patient_prenom'].' '.$rdv['patient_nom'],
                'debut'=>$rdv['heure_rdv'],'fin'=>null,'type'=>$rdv['statut'],
                'note'=>'CIN: '.$rdv['cin']];
        }
        foreach ($events as $ev) {
            $jour = date('Y-m-d', strtotime($ev['date_debut']));
            $par_jour[$jour][] = ['source'=>'planning','id'=>$ev['id'],'titre'=>$ev['titre'],
                'debut'=>date('H:i', strtotime($ev['date_debut'])),
                'fin'=>date('H:i', strtotime($ev['date_fin'])),
                'type'=>$ev['type'],'note'=>$ev['note'],'jour'=>$jour,
                'debut_dt'=>date('Y-m-d\TH:i', strtotime($ev['date_debut'])),
                'fin_dt'  =>date('Y-m-d\TH:i', strtotime($ev['date_fin']))];
        }
        return $par_jour;
    }
}