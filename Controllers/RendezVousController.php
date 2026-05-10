<?php

namespace Controllers;

use Models\RendezVousModel;
use Core\SessionHelper;
use Services\NotificationService;
use Services\MailService;

class RendezVousController
{
    use SessionHelper;

    private RendezVousModel $model;

    public function __construct()
    {
        $this->model = new RendezVousModel();
        require_once __DIR__ . '/../Services/NotificationService.php';
        require_once __DIR__ . '/../Services/MailService.php';
    }

    private function basePath(): string
    {
        return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $currentView = $view;
        include __DIR__ . '/../Views/Back/layout.php';
    }

    private function getNotifService(): NotificationService
    {
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();
        return new NotificationService($pdo);
    }

    /* ══════════════════════════════════════════════
       ADMIN — Dashboard
    ══════════════════════════════════════════════ */
    public function adminDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $role = strtolower(trim($_SESSION['user']['role'] ?? $_SESSION['user']['role_name'] ?? ''));
        if ($role !== 'admin') {
            header('Location: ' . $this->basePath() . '/rdv/dashboard');
            exit;
        }

        $filtre_statut  = $_GET['statut']  ?? '';
        $filtre_medecin = intval($_GET['medecin'] ?? 0);
        $recherche      = trim($_GET['search'] ?? '');

        $medecins_rdv   = $this->model->getAllRdvsGroupesMedecin($filtre_statut, $filtre_medecin, $recherche);
        $stats          = $this->model->getStatsAdmin();
        $liste_medecins = $this->model->getMedecinsAvecRdv();

        $this->render('rdv_admin_dashboard', compact('medecins_rdv', 'stats', 'liste_medecins', 'filtre_statut', 'filtre_medecin', 'recherche'));
    }

    /* ══════════════════════════════════════════════
       DOCTOR — Dashboard
    ══════════════════════════════════════════════ */
    public function doctorDashboard(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        // ── Pseudo-CRON : envoie les rappels du jour sans Task Scheduler ────
        // Se déclenche au 1er chargement du dashboard chaque jour
        $clePseudoCron = 'rappel_cron_' . date('Y-m-d');
        if (empty($_SESSION[$clePseudoCron])) {
            $_SESSION[$clePseudoCron] = true;
            try {
                require_once __DIR__ . '/../config.php';
                require_once __DIR__ . '/../Services/MailService.php';
                $pdoCron  = \config::getConnexion();
                $demain   = date('Y-m-d', strtotime('+1 day'));
                $stmtCron = $pdoCron->prepare("
                    SELECT r.*, u.nom AS medecin_nom, u.prenom AS medecin_prenom
                    FROM rendez_vous r
                    LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
                    WHERE r.date_rdv = :demain
                      AND r.statut = 'confirme'
                      AND (r.rappel_envoye IS NULL OR r.rappel_envoye = 0)
                      AND r.patient_email IS NOT NULL AND r.patient_email != ''
");
                $stmtCron->execute([':demain' => $demain]);
                foreach ($stmtCron->fetchAll(\PDO::FETCH_ASSOC) as $rdvCron) {
                    $med = ['nom' => $rdvCron['medecin_nom'] ?? '', 'prenom' => $rdvCron['medecin_prenom'] ?? '', 'specialite' => ''];
                    if (\Services\MailService::rdvRappel($rdvCron, $med)) {
                        $pdoCron->prepare("UPDATE rendez_vous SET rappel_envoye = 1 WHERE id = :id")
                                ->execute([':id' => $rdvCron['id']]);
                    }
                }
            } catch (\Exception $e) {
                error_log('[MediFlow pseudo-cron] ' . $e->getMessage());
            }
        }

        if (isset($_GET['supprimer'])) {
            $this->model->supprimerRdv(intval($_GET['supprimer']), $medecin_id);
            header("Location: " . $this->basePath() . "/rdv/dashboard?succes=" . urlencode("Rendez-vous supprimé."));
            exit;
        }

        $filtre      = isset($_GET['statut']) && in_array($_GET['statut'], ['en_attente', 'confirme', 'annule']) ? $_GET['statut'] : '';
        $data        = $this->model->getDashboardData($medecin_id, $filtre);
        $rendez_vous = $data['rendez_vous'];
        $stats       = $data['stats'];

        usort($rendez_vous, fn($a, $b) => strcmp($b['date_rdv'] . ' ' . $b['heure_rdv'], $a['date_rdv'] . ' ' . $a['heure_rdv']));

        $per_page    = 8;
        $total_rdv   = count($rendez_vous);
        $total_pages = max(1, (int)ceil($total_rdv / $per_page));
        $page        = max(1, min($total_pages, intval($_GET['page'] ?? 1)));
        $offset      = ($page - 1) * $per_page;
        $rdv_page    = array_slice($rendez_vous, $offset, $per_page);

        $today    = date('Y-m-d');
        $now_time = date('H:i:s');
        $prochain = null;
        foreach (array_filter($rendez_vous, fn($r) => $r['date_rdv'] === $today) as $r) {
            if ($r['heure_rdv'] >= $now_time && $r['statut'] !== 'annule') {
                if (!$prochain || $r['heure_rdv'] < $prochain['heure_rdv']) $prochain = $r;
            }
        }

        $taux_conf = $stats['total'] > 0 ? round(($stats['total'] - $stats['nb_attente']) / $stats['total'] * 100) : 0;

        $rdv_par_jour = array_fill(0, 7, 0);
        $debut_sem = new \DateTime();
        $debut_sem->setISODate((int)date('Y'), (int)date('W'));
        $debut_sem->setTime(0, 0, 0);
        foreach ($rendez_vous as $r) {
            $d    = new \DateTime($r['date_rdv']);
            $diff = (int)$debut_sem->diff($d)->days;
            if ($diff >= 0 && $diff < 7) $rdv_par_jour[$diff]++;
        }
        $max_rdv_jour = max(1, max($rdv_par_jour));

        $msg_succes = $_GET['succes'] ?? '';
        $msg_erreur = $_GET['erreur'] ?? '';

        $this->render('dashboard_doctor', compact(
            'rdv_page', 'stats', 'total_rdv', 'total_pages', 'page', 'filtre',
            'prochain', 'taux_conf', 'rdv_par_jour', 'max_rdv_jour', 'msg_succes', 'msg_erreur'
        ));
    }

    /* ══════════════════════════════════════════════
       DOCTOR — Planning
    ══════════════════════════════════════════════ */
    public function doctorPlanning(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_evenement') {
            $this->model->ajouterEvenement($medecin_id);
            header("Location: " . $this->basePath() . "/rdv/planning?succes=1");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier_evenement') {
            $this->model->modifierEvenement($medecin_id);
            header("Location: " . $this->basePath() . "/rdv/planning?succes=1");
            exit;
        }
        if (isset($_GET['supprimer_event'])) {
            $this->model->supprimerEvenement(intval($_GET['supprimer_event']), $medecin_id);
            header("Location: " . $this->basePath() . "/rdv/planning?succes=1");
            exit;
        }

        $vue_active   = in_array($_GET['vue'] ?? '', ['jour', 'semaine', 'mois']) ? $_GET['vue'] : 'semaine';
        $date_ref_str = $_GET['date'] ?? date('Y-m-d');
        $date_ref     = new \DateTime($date_ref_str);
        $date_ref->setTime(0, 0, 0);

        if ($vue_active === 'jour') {
            $date_debut = clone $date_ref;
            $date_fin   = clone $date_ref;
        } elseif ($vue_active === 'mois') {
            $date_debut = new \DateTime($date_ref->format('Y-m-01'));
            $date_fin   = new \DateTime($date_ref->format('Y-m-t'));
        } else {
            $semaine_param = $_GET['semaine'] ?? $date_ref->format('Y-\WW');
            $date_debut    = new \DateTime();
            $date_debut->setISODate(...explode('-W', $semaine_param));
            $date_debut->setTime(0, 0, 0);
            $date_fin = clone $date_debut;
            $date_fin->modify('+4 days');
        }
        $date_debut->setTime(0, 0, 0);
        $debut_str = $date_debut->format('Y-m-d');
        $fin_str   = $date_fin->format('Y-m-d');

        $par_jour   = $this->model->getPlanningData($medecin_id, $debut_str, $fin_str);
        $stats_data = $this->model->getDashboardData($medecin_id);
        $stats      = $stats_data['stats'];

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
            $url_suiv = '/integration/rdv/planning?vue=mois&date=' . $next_date->format('Y-m-01');
        } else {
            $sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
            $sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
            $url_prec = '/integration/rdv/planning?vue=semaine&semaine=' . $sem_prec->format('Y-\WW');
            $url_suiv = '/integration/rdv/planning?vue=semaine&semaine=' . $sem_suiv->format('Y-\WW');
        }

        $jours = [];
        if ($vue_active === 'jour') {
            $jours[] = clone $date_debut;
        } elseif ($vue_active === 'mois') {
            $nb_jours_mois = (int)$date_fin->format('d');
            for ($i = 0; $i < $nb_jours_mois; $i++) {
                $j = clone $date_debut; $j->modify("+$i days"); $jours[] = $j;
            }
        } else {
            for ($i = 0; $i < 5; $i++) {
                $j = clone $date_debut; $j->modify("+$i days"); $jours[] = $j;
            }
        }

        $prochain_patient = null;
        $today_str        = date('Y-m-d');
        $heure_now        = date('H:i');
        if (isset($par_jour[$today_str])) {
            foreach ($par_jour[$today_str] as $ev) {
                if ($ev['source'] === 'rdv' && $ev['debut'] >= $heure_now) {
                    $prochain_patient = $ev;
                    break;
                }
            }
        }

        $mini_cal_mois_param    = $_GET['mini_mois'] ?? $date_debut->format('Y-m');
        $mini_cal_date          = new \DateTime($mini_cal_mois_param . '-01');
        $mini_cal_mois_prec     = clone $mini_cal_date; $mini_cal_mois_prec->modify('-1 month');
        $mini_cal_mois_suiv     = clone $mini_cal_date; $mini_cal_mois_suiv->modify('+1 month');
        $mini_cal_first_dow     = intval($mini_cal_date->format('N'));
        $mini_cal_days_in_month = intval($mini_cal_date->format('t'));

        $this->render('planning', compact(
            'par_jour', 'stats', 'jours', 'url_prec', 'url_suiv', 'vue_active', 'date_debut', 'date_fin',
            'debut_str', 'fin_str', 'prochain_patient', 'mini_cal_date', 'mini_cal_mois_prec',
            'mini_cal_mois_suiv', 'mini_cal_first_dow', 'mini_cal_days_in_month', 'mini_cal_mois_param'
        ));
    }

    /* ══════════════════════════════════════════════
       DOCTOR — Statistiques
    ══════════════════════════════════════════════ */
    public function doctorStats(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];

        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $today           = date('Y-m-d');
        $year            = (int)date('Y');
        $month           = (int)date('m');
        $last_month      = $month === 1 ? 12 : $month - 1;
        $last_month_year = $month === 1 ? $year - 1 : $year;

        // RDV par mois (12 derniers mois)
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(date_rdv,'%Y-%m') AS mois, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid AND date_rdv >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY mois ORDER BY mois ASC");
        $stmt->execute([':mid' => $medecin_id]);
        $rdv_par_mois = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $mois_labels  = [];
        $mois_data    = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = new \DateTime("first day of -$i month");
            $lb = $d->format('M Y');
            $mois_labels[] = $lb;
            $mois_data[]   = 0;
        }
        foreach ($rdv_par_mois as $r) {
            $d   = new \DateTime($r['mois'] . '-01');
            $lb  = $d->format('M Y');
            $idx = array_search($lb, $mois_labels);
            if ($idx !== false) $mois_data[$idx] = (int)$r['nb'];
        }

        // RDV par année (5 dernières années)
        $stmt = $pdo->prepare("SELECT YEAR(date_rdv) AS annee, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv) >= :y GROUP BY annee ORDER BY annee ASC");
        $stmt->execute([':mid' => $medecin_id, ':y' => $year - 4]);
        $rdv_par_annee = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $annee_labels  = [];
        $annee_data    = [];
        for ($y = $year - 4; $y <= $year; $y++) {
            $annee_labels[] = (string)$y;
            $found          = array_filter($rdv_par_annee, fn($r) => (int)$r['annee'] === $y);
            $annee_data[]   = $found ? (int)array_values($found)[0]['nb'] : 0;
        }

        // Genre
        $stmt = $pdo->prepare("SELECT genre, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY genre");
        $stmt->execute([':mid' => $medecin_id]);
        $genre_rows    = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        $nb_homme      = (int)($genre_rows['homme'] ?? 0);
        $nb_femme      = (int)($genre_rows['femme'] ?? 0);
        $nb_genre_total = $nb_homme + $nb_femme;
        $pct_homme     = $nb_genre_total ? round($nb_homme / $nb_genre_total * 100) : 0;
        $pct_femme     = $nb_genre_total ? round($nb_femme / $nb_genre_total * 100) : 0;

        // Statuts
        $stmt = $pdo->prepare("SELECT statut, COUNT(*) AS nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY statut");
        $stmt->execute([':mid' => $medecin_id]);
        $statut_rows     = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        $nb_confirme     = (int)($statut_rows['confirme']   ?? 0);
        $nb_attente      = (int)($statut_rows['en_attente'] ?? 0);
        $nb_annule       = (int)($statut_rows['annule']     ?? 0);
        $nb_total_statut = $nb_confirme + $nb_attente + $nb_annule;

        // KPIs
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND MONTH(date_rdv)=:m AND YEAR(date_rdv)=:y");
        $stmt->execute([':mid' => $medecin_id, ':m' => $month, ':y' => $year]);
        $rdv_ce_mois = (int)$stmt->fetchColumn();
        $stmt->execute([':mid' => $medecin_id, ':m' => $last_month, ':y' => $last_month_year]);
        $rdv_mois_prec = (int)$stmt->fetchColumn();
        $evol_mois     = $rdv_mois_prec > 0 ? round(($rdv_ce_mois - $rdv_mois_prec) / $rdv_mois_prec * 100) : 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y");
        $stmt->execute([':mid' => $medecin_id, ':y' => $year]);
        $rdv_cette_annee = (int)$stmt->fetchColumn();

        $taux_confirmation = $nb_total_statut > 0 ? round($nb_confirme / $nb_total_statut * 100) : 0;
        $taux_annulation   = $nb_total_statut > 0 ? round($nb_annule   / $nb_total_statut * 100) : 0;

        $stmt = $pdo->prepare("SELECT AVG(nb) FROM (SELECT MONTH(date_rdv) m, COUNT(*) nb FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y GROUP BY m) t");
        $stmt->execute([':mid' => $medecin_id, ':y' => $year]);
        $moy_mois = round((float)$stmt->fetchColumn(), 1);

        $json_mois_labels  = json_encode($mois_labels);
        $json_mois_data    = json_encode($mois_data);
        $json_annee_labels = json_encode($annee_labels);
        $json_annee_data   = json_encode($annee_data);

        // Motifs
        $stmt = $pdo->prepare("SELECT motif, COUNT(*) as nb FROM rendez_vous WHERE medecin_id=:mid GROUP BY motif ORDER BY nb DESC");
        $stmt->execute([':mid' => $medecin_id]);
        $motifs_dist    = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $total_rdv_stats = array_sum(array_column($motifs_dist, 'nb'));
        foreach ($motifs_dist as &$m) {
            $m['pct'] = $total_rdv_stats > 0 ? round($m['nb'] / $total_rdv_stats * 100) : 0;
        }

        $this->render('statistiques', compact(
            'mois_labels', 'mois_data', 'annee_labels', 'annee_data', 'nb_homme', 'nb_femme', 'nb_genre_total',
            'pct_homme', 'pct_femme', 'nb_confirme', 'nb_attente', 'nb_annule', 'nb_total_statut',
            'rdv_ce_mois', 'rdv_mois_prec', 'evol_mois', 'rdv_cette_annee', 'taux_confirmation',
            'taux_annulation', 'moy_mois', 'json_mois_labels', 'json_mois_data', 'json_annee_labels', 'json_annee_data',
            'motifs_dist'
        ));
    }

    /* ══════════════════════════════════════════════
       DOCTOR — Modifier un RDV
    ══════════════════════════════════════════════ */
    public function modifierRdvView(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = (int)$_SESSION['user']['id'];
        $id         = intval($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouvelle_date  = $_POST['date_rdv']  ?? '';
            $nouvelle_heure = $_POST['heure_rdv'] ?? '';
            $nouveau_statut = $_POST['statut']     ?? '';
            $rdv_id         = intval($_POST['rdv_id']);

            $ancienRdv     = $this->model->getRdvById($rdv_id);
            $ancienneDate  = $ancienRdv['date_rdv']  ?? $nouvelle_date;
            $ancienneHeure = $ancienRdv['heure_rdv'] ?? $nouvelle_heure;
            $ancienStatut  = $ancienRdv['statut']    ?? '';

            $this->model->updateRdv($rdv_id, $nouvelle_date, $nouvelle_heure, $nouveau_statut);

            try {
                require_once __DIR__ . '/../config.php';
                $pdo = \config::getConnexion();

                $stmtRdv = $pdo->prepare("
                    SELECT r.*, r.patient_email, u.id_PK AS patient_user_id
                    FROM rendez_vous r
                    LEFT JOIN utilisateurs u ON u.mail = CONVERT(r.patient_email USING utf8mb4) COLLATE utf8mb4_unicode_ci
                    WHERE r.id = :id
                ");
                $stmtRdv->execute([':id' => $rdv_id]);
                $rdvMaj = $stmtRdv->fetch();

                $stmtMed = $pdo->prepare("SELECT id_PK AS id, nom, prenom FROM utilisateurs WHERE id_PK = :id");
                $stmtMed->execute([':id' => $medecin_id]);
                $medecinInfo = $stmtMed->fetch();

                $notif     = $this->getNotifService();
                $patientId = $rdvMaj['patient_user_id'] ?? null;
                if (!$patientId && !empty($rdvMaj['patient_email'])) {
                    $stmtPat = $pdo->prepare("SELECT id_PK FROM utilisateurs WHERE mail = CONVERT(:mail USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
                    $stmtPat->execute([':mail' => $rdvMaj['patient_email']]);
                    $patientRow = $stmtPat->fetch();
                    $patientId  = $patientRow['id_PK'] ?? null;
                }

                $dateChanged   = ($nouvelle_date !== $ancienneDate);
                $heureChanged  = (substr($nouvelle_heure, 0, 5) !== substr($ancienneHeure, 0, 5));
                $statutChanged = ($nouveau_statut !== $ancienStatut);

                if ($dateChanged || $heureChanged) {
                    MailService::rdvModifie($rdvMaj, $medecinInfo, $ancienneDate, $ancienneHeure);
                    if ($patientId) {
                        $notif->notifierPatientModification($patientId, $rdvMaj, $medecinInfo);
                    }
                } elseif ($statutChanged) {
                    if ($nouveau_statut === 'confirme') {
                        MailService::rdvConfirme($rdvMaj, $medecinInfo);
                        if ($patientId) {
                            $notif->notifierPatientConfirmation($patientId, $rdvMaj, $medecinInfo);
                        }
                        // ── Rappel automatique sans CRON ─────────────────────────────
                        // Si le RDV est demain → envoyer le rappel immédiatement
                        // Sinon → marquer rappel_a_envoyer=1, envoyé au 1er login du lendemain
                        $demain = date('Y-m-d', strtotime('+1 day'));
                        if (!empty($rdvMaj['date_rdv']) && !empty($rdvMaj['patient_email'])) {
                            if ($rdvMaj['date_rdv'] === $demain) {
                                // RDV demain → rappel immédiat
                                MailService::rdvRappel($rdvMaj, $medecinInfo);
                                $pdo->prepare("UPDATE rendez_vous SET rappel_envoye = 1 WHERE id = :id")
                                    ->execute([':id' => $rdv_id]);
                            } else {
                                // RDV futur → on marque rappel_envoye = 0 pour le pseudo-cron
                                $pdo->prepare("UPDATE rendez_vous SET rappel_envoye = 0 WHERE id = :id")
                                    ->execute([':id' => $rdv_id]);
                            }
                        }
                    } elseif ($nouveau_statut === 'annule') {
                        MailService::rdvAnnule($rdvMaj, $medecinInfo);
                        if ($patientId) {
                            $notif->notifierPatientAnnulation($patientId, $rdvMaj, $medecinInfo);
                        }
                    }
                }
            } catch (\Exception $e) {
                error_log('Mail/Notif erreur modifierRdv: ' . $e->getMessage());
            }

            header('Location: ' . $this->basePath() . '/rdv/dashboard?succes=' . urlencode('Rendez-vous modifié.'));
            exit;
        }

        $rdv = $id ? $this->model->getRdvById($id) : null;
        if (!$rdv || $rdv['medecin_id'] != $medecin_id) {
            header('Location: ' . $this->basePath() . '/rdv/dashboard');
            exit;
        }
        $this->render('modifier-rdv', compact('rdv', 'id'));
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

        $search = trim($_GET['search']    ?? '');
        $spe    = trim($_GET['specialite'] ?? '');

        $sql    = "SELECT u.id_PK AS id, u.nom, u.prenom, u.mail, u.tel,
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

    /* ══════════════════════════════════════════════
       PATIENT — Planning du médecin (choix créneau)
    ══════════════════════════════════════════════ */
    public function patientPlanning(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $medecin_id = intval($_GET['medecin_id'] ?? 0);
        if ($medecin_id === 0) {
            header('Location: ' . $this->basePath() . '/rdv/annuaire');
            exit;
        }

        $stmt = $pdo->prepare("SELECT id_PK AS id, nom, prenom, mail FROM utilisateurs WHERE id_PK=:id AND id_role=2");
        $stmt->execute([':id' => $medecin_id]);
        $medecin = $stmt->fetch();
        if (!$medecin) {
            header('Location: ' . $this->basePath() . '/rdv/annuaire');
            exit;
        }

        $semaine_param = $_GET['semaine'] ?? date('Y-\WW');
        $date_debut    = new \DateTime();
        $date_debut->setISODate(...explode('-W', $semaine_param));
        $date_debut->setTime(0, 0, 0);
        $date_fin = clone $date_debut;
        $date_fin->modify('+4 days');

        $debut_str = $date_debut->format('Y-m-d');
        $fin_str   = $date_fin->format('Y-m-d');

        $sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
        $sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
        $url_prec = "?medecin_id=$medecin_id&semaine=" . $sem_prec->format('Y-\WW');
        $url_suiv = "?medecin_id=$medecin_id&semaine=" . $sem_suiv->format('Y-\WW');

        $jours = [];
        for ($i = 0; $i < 5; $i++) {
            $j = clone $date_debut; $j->modify("+$i days");
            $jours[] = $j;
        }

        $par_jour_raw = $this->model->getPlanningData($medecin_id, $debut_str, $fin_str);
        $pris         = [];
        $bloque       = [];

        foreach ($par_jour_raw as $jour => $events) {
            foreach ($events as $ev) {
                if ($ev['source'] === 'rdv') {
                    if ($ev['type'] !== 'annule') {
                        $heure_short          = substr($ev['debut'], 0, 5);
                        $pris[$jour][$heure_short] = true;
                    }
                } else {
                    $start_ts = strtotime($ev['debut_dt']);
                    $end_ts   = strtotime($ev['fin_dt']);
                    $curr     = $start_ts;
                    while ($curr < $end_ts) {
                        $slot_h              = date('H:i', $curr);
                        $bloque[$jour][$slot_h] = true;
                        $curr += 1800;
                    }
                }
            }
        }

        $currentView = '../Front/planning';
        $data = compact('medecin', 'jours', 'url_prec', 'url_suiv', 'pris', 'bloque');
        $this->render($currentView, $data);
    }

    /* ══════════════════════════════════════════════
       PATIENT — Formulaire de réservation
    ══════════════════════════════════════════════ */
    public function patientBookRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $medecin_id = intval($_GET['medecin_id'] ?? 0);
        $date_rdv   = $_GET['date_rdv']  ?? '';
        $heure_rdv  = $_GET['heure_rdv'] ?? '';

        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $stmt = $pdo->prepare("SELECT id_PK AS id, nom, prenom FROM utilisateurs WHERE id_PK=:id");
        $stmt->execute([':id' => $medecin_id]);
        $medecin = $stmt->fetch();

        $user      = $_SESSION['user'];
        $stmt_user = $pdo->prepare("SELECT nom, prenom, cin FROM utilisateurs WHERE id_PK = :id");
        $stmt_user->execute([':id' => $user['id']]);
        $patient = $stmt_user->fetch();

        $currentView = '../Front/rdv';
        $data = compact('medecin', 'date_rdv', 'heure_rdv', 'patient');
        $this->render($currentView, $data);
    }

    /* ══════════════════════════════════════════════
       PATIENT — Traitement formulaire RDV (POST)
    ══════════════════════════════════════════════ */
    public function traitementRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->basePath() . '/rdv/annuaire');
            exit;
        }

        $medecin_id = intval($_POST['medecin_id'] ?? 0);
        $nom        = htmlspecialchars(trim($_POST['nom']    ?? ''));
        $prenom     = htmlspecialchars(trim($_POST['prenom'] ?? ''));
        $cin        = trim($_POST['cin']      ?? '');
        $genre      = $_POST['genre']         ?? '';
        $date       = $_POST['date_rdv']      ?? '';
        $heure      = $_POST['heure_rdv']     ?? '';
        $erreurs    = [];

        if (!$nom    || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $nom))   $erreurs[] = 'Nom invalide.';
        if (!$prenom || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $prenom)) $erreurs[] = 'Prénom invalide.';
        if (!preg_match('/^[0-9]{8}$/', $cin))                          $erreurs[] = 'CIN : 8 chiffres requis.';
        if (!in_array($genre, ['homme', 'femme']))                       $erreurs[] = 'Genre invalide.';
        if (!$date || $date < date('Y-m-d'))                            $erreurs[] = 'Date invalide.';
        if (!$heure)                                                     $erreurs[] = 'Heure requise.';

        if (!empty($erreurs)) {
            $_SESSION['rdv_erreurs'] = $erreurs;
            header("Location: " . $this->basePath() . "/rdv/reserver?medecin_id=$medecin_id&date_rdv=$date&heure_rdv=$heure");
            exit;
        }

        if ($this->model->creneauDejaReserve($medecin_id, $date, $heure)) {
            $_SESSION['rdv_erreurs'] = ['Ce créneau est déjà réservé. Veuillez choisir une autre heure.'];
            header("Location: " . $this->basePath() . "/rdv/reserver?medecin_id=$medecin_id&date_rdv=$date&heure_rdv=$heure");
            exit;
        }

        $patient_email = $_SESSION['user']['mail'] ?? $_SESSION['user']['email'] ?? null;

        $res = $this->model->addRdv($medecin_id, $nom, $prenom, $cin, $genre, $date, $heure, $patient_email);
        if ($res) {
            try {
                $notif       = $this->getNotifService();
                $rdvPourNotif = [
                    'id'             => $res,
                    'patient_prenom' => $prenom,
                    'patient_nom'    => $nom,
                    'date_rdv'       => $date,
                    'heure_rdv'      => $heure,
                ];
                $notif->notifierMedecinNouveauRdv($medecin_id, $rdvPourNotif);
            } catch (\Exception $e) {
                error_log('Notif erreur: ' . $e->getMessage());
            }
            header('Location: ' . $this->basePath() . '/rdv/confirmation?id=' . $res);
        } else {
            $_SESSION['rdv_erreurs'] = ['Une erreur est survenue. Veuillez réessayer.'];
            header('Location: ' . $this->basePath() . "/rdv/reserver?medecin_id=$medecin_id&date_rdv=$date&heure_rdv=$heure");
        }
        exit;
    }

    /* ══════════════════════════════════════════════
       PATIENT — Page de confirmation
    ══════════════════════════════════════════════ */
    public function patientConfirmation(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $id  = intval($_GET['id'] ?? 0);
        $rdv = $this->model->getRdvById($id);

        $currentView = '../Front/confirmation';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /* ══════════════════════════════════════════════
       PATIENT — Mes rendez-vous
    ══════════════════════════════════════════════ */
    public function patientMesRdv(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        require_once __DIR__ . '/../config.php';
        $pdo = \config::getConnexion();

        $cin     = trim($_POST['cin'] ?? '');
        $is_auto = false;

        if (!$cin && isset($_SESSION['user']['id'])) {
            $stmt_user = $pdo->prepare("SELECT cin FROM utilisateurs WHERE id_PK = :id");
            $stmt_user->execute([':id' => $_SESSION['user']['id']]);
            $user_data = $stmt_user->fetch();
            if ($user_data && !empty($user_data['cin'])) {
                $cin     = $user_data['cin'];
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
       PATIENT — Export iCal (.ics)
       GET /rdv/ical?rdv_id=X
    ══════════════════════════════════════════════ */
    public function exportIcal(): void
    {
        require_once __DIR__ . '/../config.php';
        require_once __DIR__ . '/../Services/ICalService.php';

        $rdv_id = intval($_GET['rdv_id'] ?? 0);
        $pdo    = \config::getConnexion();

        if ($rdv_id <= 0) {
            http_response_code(400);
            echo "RDV introuvable.";
            exit;
        }

        // Détection dynamique de la colonne spécialité
        $colsSpe = $pdo->query("SHOW COLUMNS FROM utilisateurs")->fetchAll(\PDO::FETCH_COLUMN);
        $champSpe = null;
        foreach (['specialite', 'specialisation', 'specialty'] as $c) {
            if (in_array($c, $colsSpe, true)) { $champSpe = $c; break; }
        }
        $selSpe = $champSpe ? "u.`{$champSpe}` AS medecin_specialite," : "NULL AS medecin_specialite,";

        $stmt = $pdo->prepare(
            "SELECT r.*,
                    u.nom    AS medecin_nom,
                    u.prenom AS medecin_prenom,
                    {$selSpe}
                    u.mail   AS medecin_email
             FROM rendez_vous r
             LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
             WHERE r.id = :id"
        );
        $stmt->execute([':id' => $rdv_id]);
        $rdv = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$rdv) {
            http_response_code(404);
            echo "RDV introuvable.";
            exit;
        }

        $medecin = [
            'nom'        => $rdv['medecin_nom']       ?? 'Médecin',
            'prenom'     => $rdv['medecin_prenom']     ?? '',
            'specialite' => $rdv['medecin_specialite'] ?? '',
        ];

        // ── DEBUG temporaire : affiche le contenu .ics brut ────────────
        // Décommente les 4 lignes ci-dessous pour voir le fichier généré
        // header('Content-Type: text/plain; charset=utf-8');
        // echo \Services\ICalService::generer($rdv, $medecin);
        // exit;
        // ────────────────────────────────────────────────────────────────

        // Vérification des données minimales avant génération
        if (empty($rdv['date_rdv']) || empty($rdv['heure_rdv'])) {
            http_response_code(422);
            echo "Impossible de générer le fichier .ics : date ou heure manquante pour ce RDV (id={$rdv_id}).";
            exit;
        }

        \Services\ICalService::telecharger($rdv, $medecin);
    }

    /* ══════════════════════════════════════════════
       PATIENT — Réponse modification depuis email
       GET /rdv/reponse-modification?rdv_id=X&action=confirmer|annuler&token=XXX
    ══════════════════════════════════════════════ */
    public function reponseModification(): void
    {
        require_once __DIR__ . '/../config.php';
        require_once __DIR__ . '/../Services/MailService.php';
        require_once __DIR__ . '/../Services/NotificationService.php';

        $rdv_id = intval($_GET['rdv_id'] ?? 0);
        $action = $_GET['action'] ?? '';
        $token  = $_GET['token']  ?? '';
        $pdo    = \config::getConnexion();

        if (!in_array($action, ['confirmer', 'annuler']) || !\Services\MailService::verifyToken($rdv_id, $action, $token)) {
            echo "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><title>MediFlow</title></head>
                  <body style='font-family:Inter,sans-serif;text-align:center;padding:60px;'>
                    <h2 style='color:#ba1a1a;'>❌ Lien invalide ou expiré.</h2>
                    <p>Connectez-vous à votre espace MediFlow pour gérer vos rendez-vous.</p>
                  </body></html>";
            exit;
        }

        $stmt = $pdo->prepare("SELECT r.*, u.id_PK AS patient_user_id FROM rendez_vous r LEFT JOIN utilisateurs u ON u.mail = CONVERT(r.patient_email USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE r.id = :id");
        $stmt->execute([':id' => $rdv_id]);
        $rdv = $stmt->fetch();

        if (!$rdv) {
            echo "<!DOCTYPE html><html><body style='font-family:Inter,sans-serif;text-align:center;padding:60px;'><h2>Rendez-vous introuvable.</h2></body></html>";
            exit;
        }

        $nouveau_statut = ($action === 'confirmer') ? 'confirme' : 'annule';
        $pdo->prepare("UPDATE rendez_vous SET statut = :s WHERE id = :id")->execute([':s' => $nouveau_statut, ':id' => $rdv_id]);

        $patientId = $rdv['patient_user_id'] ?? null;
        if (!$patientId && !empty($rdv['patient_email'])) {
            $stmtPat2 = $pdo->prepare("SELECT id_PK FROM utilisateurs WHERE mail = CONVERT(:mail USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
            $stmtPat2->execute([':mail' => $rdv['patient_email']]);
            $pr2       = $stmtPat2->fetch();
            $patientId = $pr2['id_PK'] ?? null;
        }

        if ($patientId) {
            $date  = date('d/m/Y', strtotime($rdv['date_rdv']));
            $heure = substr($rdv['heure_rdv'], 0, 5);
            $notif = new \Services\NotificationService($pdo);
            $msg   = $action === 'confirmer'
                ? "✅ Vous avez confirmé votre rendez-vous du {$date} à {$heure}."
                : "❌ Vous avez annulé votre rendez-vous du {$date} à {$heure}.";
            $notif->creer($patientId, $nouveau_statut, $msg, $rdv_id);
        }

        $base    = $this->basePath();
        $date    = date('d/m/Y', strtotime($rdv['date_rdv']));
        $heure   = substr($rdv['heure_rdv'], 0, 5);
        $couleur = $action === 'confirmer' ? '#005851' : '#ba1a1a';
        $icone   = $action === 'confirmer' ? '✅' : '❌';
        $msg     = $action === 'confirmer'
            ? "Votre rendez-vous du <strong>{$date} à {$heure}</strong> a été <strong>confirmé</strong>."
            : "Votre rendez-vous du <strong>{$date} à {$heure}</strong> a été <strong>annulé</strong>.";

        echo "<!DOCTYPE html>
        <html lang='fr'><head><meta charset='UTF-8'><title>MediFlow</title>
        <link href='https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Inter:wght@400;600&display=swap' rel='stylesheet'>
        </head>
        <body style='margin:0;background:#f3f4f6;font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;'>
            <div style='background:white;border-radius:20px;padding:48px 40px;max-width:480px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.10);'>
                <div style='font-size:56px;margin-bottom:16px;'>{$icone}</div>
                <h1 style='font-family:Manrope,sans-serif;font-size:22px;color:#111827;margin:0 0 12px;'>Merci pour votre réponse</h1>
                <p style='color:#6b7280;line-height:1.7;margin-bottom:28px;'>{$msg}</p>
                <a href='{$base}/' style='display:inline-block;background:{$couleur};color:white;padding:12px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-size:14px;'>
                    Retour à MediFlow
                </a>
            </div>
        </body></html>";
        exit;
    }

    /* ══════════════════════════════════════════════
       LEGACY — Utilisé par la vue planning
    ══════════════════════════════════════════════ */
    public function getPlanningData($medecin_id, $date_debut, $date_fin): array
    {
        $rdvs   = $this->model->getRdvSemaine($medecin_id, $date_debut, $date_fin);
        $events = $this->model->getPlanningByMedecin($medecin_id, $date_debut, $date_fin);
        $par_jour = [];

        foreach ($rdvs as $rdv) {
            $jour             = $rdv['date_rdv'];
            $par_jour[$jour][] = [
                'source' => 'rdv',
                'id'     => $rdv['id'],
                'titre'  => $rdv['patient_prenom'] . ' ' . $rdv['patient_nom'],
                'debut'  => $rdv['heure_rdv'],
                'fin'    => null,
                'type'   => $rdv['statut'],
                'note'   => 'CIN: ' . $rdv['cin'],
            ];
        }

        foreach ($events as $ev) {
            $jour             = date('Y-m-d', strtotime($ev['date_debut']));
            $par_jour[$jour][] = [
                'source'   => 'planning',
                'id'       => $ev['id'],
                'titre'    => $ev['titre'],
                'debut'    => date('H:i', strtotime($ev['date_debut'])),
                'fin'      => date('H:i', strtotime($ev['date_fin'])),
                'type'     => $ev['type'],
                'note'     => $ev['note'],
                'jour'     => $jour,
                'debut_dt' => date('Y-m-d\TH:i', strtotime($ev['date_debut'])),
                'fin_dt'   => date('Y-m-d\TH:i', strtotime($ev['date_fin'])),
            ];
        }

        return $par_jour;
    }

    /* ══════════════════════════════════════════════
       AJAX — Marquer une notification comme lue
    ══════════════════════════════════════════════ */
    public function notificationLue(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $notif_id = intval($_POST['notif_id'] ?? 0);
        $user_id  = (int)$_SESSION['user']['id'];
        $this->getNotifService()->marquerLue($notif_id, $user_id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    /* ══════════════════════════════════════════════
       AJAX — Marquer toutes les notifications lues
    ══════════════════════════════════════════════ */
    public function notificationsToutesLues(): void
    {
        $this->ensureSession();
        $this->requireAuth();
        $user_id = (int)$_SESSION['user']['id'];
        $this->getNotifService()->marquerToutesLues($user_id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    /* ══════════════════════════════════════════════
       AJAX — Compter les notifications non lues
    ══════════════════════════════════════════════ */
    public function notificationsCount(): void
    {
        $this->ensureSession();
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['count' => 0]);
            exit;
        }
        $user_id = (int)$_SESSION['user']['id'];
        $count   = $this->getNotifService()->compterNonLues($user_id);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
}