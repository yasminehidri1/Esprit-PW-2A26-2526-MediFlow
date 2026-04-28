<?php
namespace Controllers;
use Core\SessionHelper;

class RendezVousController
{
    use SessionHelper;
    private $model;

    public function __construct()
    {
        require_once __DIR__ . '/../Models/RendezVousModel.php';
        $this->model = new \RendezVousModel();
    }

    /* ── helpers ─────────────────────────────────── */
    private function render(string $view, array $vars = []): void
    {
        extract($vars);
        $currentView = $view;
        include __DIR__ . '/../Views/Back/layout.php';
    }

    private function flash(string $key, string $msg): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION[$key] = $msg;
    }

    /* ══════════════════════════════════════════════
       ADMIN
    ══════════════════════════════════════════════ */
    public function adminDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $filtre_statut  = trim($_GET['statut']    ?? '');
        $filtre_medecin = intval($_GET['medecin']  ?? 0);
        $recherche      = trim($_GET['recherche']  ?? '');

        // POST: change statut
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rdv_id'])) {
            $id     = intval($_POST['rdv_id']);
            $statut = $_POST['statut'] ?? '';
            if ($id > 0 && in_array($statut, ['en_attente','confirme','annule'])) {
                $this->model->updateStatutRdv($id, $statut);
                $this->flash('flash_success', 'Statut mis à jour.');
            }
            header('Location: /integration/rdv/admin');
            exit;
        }

        $stats    = $this->model->getStatsAdmin();
        $medecins = $this->model->getMedecinsAvecRdv();
        $grouped  = $this->model->getAllRdvsGroupesMedecin($filtre_statut, $filtre_medecin, $recherche);

        $this->render('admin', compact('stats','medecins','grouped','filtre_statut','filtre_medecin','recherche'));
    }

    /* ══════════════════════════════════════════════
       DOCTOR — Dashboard
    ══════════════════════════════════════════════ */
    public function doctorDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        // DELETE
        if (isset($_GET['supprimer'])) {
            $this->model->deleteRdv(intval($_GET['supprimer']), $medecin_id);
            $this->flash('flash_success', 'Rendez-vous supprimé.');
            header('Location: /integration/rdv/dashboard');
            exit;
        }
        // UPDATE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = intval($_POST['rdv_id']  ?? 0);
            $date   = $_POST['date_rdv']        ?? '';
            $heure  = $_POST['heure_rdv']       ?? '';
            $statut = $_POST['statut']          ?? '';
            if ($id && $date && $heure && in_array($statut,['en_attente','confirme','annule']) && $date >= date('Y-m-d')) {
                $this->model->updateRdv($id, $date, $heure, $statut, $medecin_id);
                $this->flash('flash_success', 'Rendez-vous modifié.');
            } else {
                $this->flash('flash_error', 'Données invalides.');
            }
            header('Location: /integration/rdv/dashboard');
            exit;
        }

        $filtre      = in_array($_GET['statut'] ?? '', ['en_attente','confirme','annule']) ? $_GET['statut'] : '';
        $page        = max(1, intval($_GET['page'] ?? 1));
        $per_page    = 8;
        $tous        = $this->model->getRdvByMedecin($medecin_id, $filtre);
        $total_rdv   = count($tous);
        $total_pages = max(1, (int)ceil($total_rdv / $per_page));
        $page        = min($page, $total_pages);
        $rendez_vous = array_slice($tous, ($page - 1) * $per_page, $per_page);
        $stats       = $this->model->getStatsMedecin($medecin_id);
        $today       = date('Y-m-d');
        $prochains   = array_filter($tous, fn($r) => $r['date_rdv'] >= $today && $r['statut'] !== 'annule');
        $prochain    = $prochains ? reset($prochains) : null;
        $taux_conf   = $stats['total'] > 0 ? round($stats['nb_confirmes'] / $stats['total'] * 100) : 0;

        $this->render('dashboard_doctor', compact(
            'rendez_vous','stats','prochain','taux_conf',
            'total_rdv','total_pages','page','per_page','filtre','today'
        ));
    }

    /* ── DOCTOR — Planning ───────────────────────── */
    public function doctorPlanning(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        // Add event
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre'])) {
            $titre      = htmlspecialchars(trim($_POST['titre']      ?? ''));
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin   = $_POST['date_fin']   ?? '';
            $type       = $_POST['type']       ?? 'autre';
            $note       = htmlspecialchars(trim($_POST['note']       ?? ''));
            if (!in_array($type,['chirurgie','reunion','pause','formation','urgence','autre'])) $type='autre';
            if ($titre && $date_debut && $date_fin) {
                $this->model->insertPlanning($medecin_id,$titre,$date_debut,$date_fin,$type,$note);
                $this->flash('flash_success','Événement ajouté.');
            } else {
                $this->flash('flash_error','Champs requis manquants.');
            }
            header('Location: /integration/rdv/planning');
            exit;
        }
        // Delete event
        if (isset($_GET['del_event'])) {
            $this->model->deletePlanning(intval($_GET['del_event']), $medecin_id);
            $this->flash('flash_success','Événement supprimé.');
            header('Location: /integration/rdv/planning');
            exit;
        }

        $semaine_param = $_GET['semaine'] ?? date('Y-\WW');
        $date_debut_obj = new \DateTime();
        $date_debut_obj->setISODate(...explode('-W', $semaine_param));
        $date_debut_obj->setTime(0,0,0);
        $date_fin_obj = clone $date_debut_obj;
        $date_fin_obj->modify('+6 days');
        $debut_str = $date_debut_obj->format('Y-m-d');
        $fin_str   = $date_fin_obj->format('Y-m-d');

        $sem_prec = clone $date_debut_obj; $sem_prec->modify('-7 days');
        $sem_suiv = clone $date_debut_obj; $sem_suiv->modify('+7 days');
        $url_prec = '/integration/rdv/planning?semaine=' . $sem_prec->format('Y-\WW');
        $url_suiv = '/integration/rdv/planning?semaine=' . $sem_suiv->format('Y-\WW');

        $jours = [];
        for ($i=0; $i<7; $i++) {
            $j = clone $date_debut_obj; $j->modify("+$i days");
            $jours[] = $j;
        }

        $par_jour = $this->model->getPlanningData($medecin_id, $debut_str, $fin_str);
        $stats    = $this->model->getStatsMedecin($medecin_id);

        $this->render('planning', compact('par_jour','stats','jours','url_prec','url_suiv','semaine_param','debut_str','fin_str'));
    }

    /* ── DOCTOR — Statistics ─────────────────────── */
    public function doctorStats(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $s = $this->model->getStatsMedecin($medecin_id);

        // Monthly trend (last 6 months)
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(date_rdv,'%Y-%m') AS mois, COUNT(*) AS nb
            FROM rendez_vous WHERE medecin_id=:mid AND date_rdv >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY mois ORDER BY mois ASC");
        $stmt->execute([':mid'=>$medecin_id]);
        $monthly = $stmt->fetchAll();

        // Genre breakdown
        $stmt2 = $pdo->prepare("SELECT genre, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY genre");
        $stmt2->execute([':mid'=>$medecin_id]);
        $genres = $stmt2->fetchAll(\PDO::FETCH_KEY_PAIR);

        $json_mois_labels = json_encode(array_column($monthly,'mois'));
        $json_mois_data   = json_encode(array_column($monthly,'nb'));
        $nb_homme   = (int)($genres['homme'] ?? 0);
        $nb_femme   = (int)($genres['femme'] ?? 0);
        $nb_total_g = $nb_homme + $nb_femme;
        $pct_homme  = $nb_total_g > 0 ? round($nb_homme/$nb_total_g*100) : 0;
        $pct_femme  = 100 - $pct_homme;
        $taux_conf  = $s['total'] > 0 ? round($s['nb_confirmes']/$s['total']*100) : 0;
        $taux_ann   = $s['total'] > 0 ? round($s['nb_annules']/$s['total']*100) : 0;

        $this->render('statistiques', compact(
            's','json_mois_labels','json_mois_data',
            'nb_homme','nb_femme','pct_homme','pct_femme','taux_conf','taux_ann'
        ));
    }

    /* ── DOCTOR — Modify RDV (separate page) ──────── */
    public function modifierRdvView(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];
        $id  = intval($_GET['id'] ?? 0);
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

    /* ── PATIENT — Planning View ─────────────────── */
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
        for ($i = 0; $i < 5; $i++) {
            $j = clone $date_debut;
            $j->modify("+$i days");
            $jours[] = $j;
        }

        // Récupérer RDVs et blocages via une version patient de getPlanningData
        $rdvs = $this->model->getRdvSemaine($medecin_id, $debut_str, $fin_str);
        $events = $this->model->getPlanningByMedecin($medecin_id, $debut_str, $fin_str);

        $pris = [];
        foreach ($rdvs as $r) {
            if ($r['statut'] !== 'annule') {
                $pris[$r['date_rdv']][substr($r['heure_rdv'], 0, 5)] = true;
            }
        }
        $bloque = [];
        foreach ($events as $ev) {
            $ts_start = strtotime($ev['date_debut']);
            $ts_end   = strtotime($ev['date_fin']);
            $day      = date('Y-m-d', $ts_start);
            // On bloque les créneaux de 30min qui tombent dans l'intervalle
            for ($h = 8; $h < 17; $h++) {
                foreach(['00','30'] as $m) {
                    $ts_c = strtotime("$day $h:$m:00");
                    if ($ts_c >= $ts_start && $ts_c < $ts_end) {
                        $bloque[$day]["$h:$m"] = $ev['titre'];
                    }
                }
            }
        }

        $currentView = '../Front/planning';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* ── PATIENT — Book RDV ──────────────────────── */
    public function patientBookRdv(): void
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
        $prefill_date  = $_GET['date_rdv']  ?? '';
        $prefill_heure = $_GET['heure_rdv'] ?? '';
        $erreurs = [];
        if (isset($_GET['erreur'])) $erreurs[] = htmlspecialchars(urldecode($_GET['erreur']));

        $currentView = '../Front/rdv';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* ── PATIENT — Process RDV form ──────────────── */
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
        if ($medecin_id === 0)                                           $erreurs[] = 'Médecin invalide.';

        if ($erreurs) {
            $msg = urlencode(implode(' | ', $erreurs));
            header("Location: /integration/rdv/reserver?medecin_id=$medecin_id&erreur=$msg");
            exit;
        }
        $rdv_id = $this->model->insertRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure);
        header("Location: /integration/rdv/confirmation?rdv_id=$rdv_id");
        exit;
    }

    /* ── PATIENT — Confirmation ──────────────────── */
    public function patientConfirmation(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $rdv_id = intval($_GET['rdv_id'] ?? 0);
        if (!$rdv_id) { header('Location: /integration/rdv/annuaire'); exit; }
        $rdv = $this->model->getRdvById($rdv_id);
        if (!$rdv)   { header('Location: /integration/rdv/annuaire'); exit; }
        $currentView = '../Front/confirmation';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* ── PATIENT — My RDVs ───────────────────────── */
    public function patientMesRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();
        
        $cin = trim($_POST['cin'] ?? '');
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
        include __DIR__ . '/../Views/Back/layout.php';
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