<?php
// ============================================================
//  planning.php — View (médecin) — VERSION AMÉLIORÉE
//  Appelle le Controller pour récupérer RDV + événements planning
// ============================================================
require_once __DIR__ . '/../../../controller/RendezVousController.php';

$controller = new RendezVousController();
$medecin_id = 1; // remplacer par $_SESSION['medecin_id']

// ACTION : Ajouter un événement planning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_evenement') {
    $controller->ajouterEvenement($medecin_id);
}

// ACTION : Modifier un événement planning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier_evenement') {
    $controller->modifierEvenement($medecin_id);
}

// ACTION : Supprimer un événement planning
if (isset($_GET['supprimer_event'])) {
    $controller->supprimerEvenement(intval($_GET['supprimer_event']), $medecin_id);
}

// ─── VUE active : jour | semaine | mois ───────────────────────
$vue_active = in_array($_GET['vue'] ?? '', ['jour','semaine','mois']) ? $_GET['vue'] : 'semaine';

// ─── Date de référence ─────────────────────────────────────────
$date_ref_str = $_GET['date'] ?? date('Y-m-d');
$date_ref     = new DateTime($date_ref_str);
$date_ref->setTime(0,0,0);

// ─── Calcul début/fin selon la vue ────────────────────────────
if ($vue_active === 'jour') {
    $date_debut = clone $date_ref;
    $date_fin   = clone $date_ref;
} elseif ($vue_active === 'mois') {
    $date_debut = new DateTime($date_ref->format('Y-m-01'));
    $date_fin   = new DateTime($date_ref->format('Y-m-t'));
} else { // semaine
    $semaine_param = $_GET['semaine'] ?? $date_ref->format('Y-\WW');
    $date_debut    = new DateTime();
    $date_debut->setISODate(...explode('-W', $semaine_param));
    $date_debut->setTime(0, 0, 0);
    $date_fin = clone $date_debut;
    $date_fin->modify('+4 days');
}
$date_debut->setTime(0,0,0);

$debut_str = $date_debut->format('Y-m-d');
$fin_str   = $date_fin->format('Y-m-d');

// ─── Données planning ──────────────────────────────────────────
$par_jour = $controller->getPlanningData($medecin_id, $debut_str, $fin_str);

// Stats journée (aujourd'hui)
$stats_data  = $controller->getDashboardData($medecin_id);
$stats       = $stats_data['stats'];

// ─── Navigation (précédent / suivant) ─────────────────────────
$prev_date = clone $date_debut;
$next_date = clone $date_debut;
if ($vue_active === 'jour') {
    $prev_date->modify('-1 day');
    $next_date->modify('+1 day');
    $url_prec = 'planning.php?vue=jour&date=' . $prev_date->format('Y-m-d');
    $url_suiv = 'planning.php?vue=jour&date=' . $next_date->format('Y-m-d');
} elseif ($vue_active === 'mois') {
    $prev_date->modify('-1 month');
    $next_date->modify('+1 month');
    $url_prec = 'planning.php?vue=mois&date=' . $prev_date->format('Y-m-01');
    $url_suiv = 'planning.php?vue=mois&date=' . $next_date->format('Y-m-01');
} else {
    $sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
    $sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
    $url_prec = 'planning.php?vue=semaine&semaine=' . $sem_prec->format('Y-\WW');
    $url_suiv = 'planning.php?vue=semaine&semaine=' . $sem_suiv->format('Y-\WW');
    $semaine_param = $semaine_param ?? $date_debut->format('Y-\WW');
}

// ─── Jours à afficher ─────────────────────────────────────────
$jours = [];
if ($vue_active === 'jour') {
    $jours[] = clone $date_debut;
} elseif ($vue_active === 'mois') {
    // Tous les jours du mois
    $nb_jours_mois = (int)$date_fin->format('d');
    for ($i = 0; $i < $nb_jours_mois; $i++) {
        $j = clone $date_debut;
        $j->modify("+$i days");
        $jours[] = $j;
    }
} else {
    for ($i = 0; $i < 5; $i++) {
        $j = clone $date_debut;
        $j->modify("+$i days");
        $jours[] = $j;
    }
}

$noms_jours = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
$semaine_param = $semaine_param ?? $date_debut->format('Y-\WW');

// Messages
$msg_succes = isset($_GET['succes']) ? 'Opération effectuée avec succès.' : '';
$msg_erreur = isset($_GET['erreur']) ? 'Une erreur est survenue.' : '';

// Prochain patient du jour depuis les RDV
$prochain_patient = null;
$today_str = date('Y-m-d');
$heure_now = date('H:i');
if (isset($par_jour[$today_str])) {
    foreach ($par_jour[$today_str] as $ev) {
        if ($ev['source'] === 'rdv' && $ev['debut'] >= $heure_now) {
            $prochain_patient = $ev;
            break;
        }
    }
}

$mini_cal_mois_param = $_GET['mini_mois'] ?? $date_debut->format('Y-m');
$mini_cal_date = new DateTime($mini_cal_mois_param . '-01');
$mini_cal_mois_prec = clone $mini_cal_date; $mini_cal_mois_prec->modify('-1 month');
$mini_cal_mois_suiv = clone $mini_cal_date; $mini_cal_mois_suiv->modify('+1 month');
$mini_cal_first_dow = intval($mini_cal_date->format('N'));
$mini_cal_days_in_month = intval($mini_cal_date->format('t'));
$mini_mois_labels = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$mini_sem_param = $semaine_param ?? date('Y-\WW');

// Formule positionnement événement dans le calendrier
function calculTop($heure) {
    list($h, $m) = explode(':', $heure);
    return round(($h + $m/60 - 8) * 76);
}
function calculHauteur($debut, $fin) {
    list($hd, $md) = explode(':', $debut);
    list($hf, $mf) = explode(':', $fin);
    $duree = ($hf + $mf/60) - ($hd + $md/60);
    return max(38, round($duree * 76));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon Planning — MediFlow Pro</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root {
      --primary:      #004d99;
      --primary-dark: #1565c0;
      --primary-light:#d6e3ff;
      --teal:         #005851;
      --teal-light:   #84f5e8;
      --teal-bg:      rgba(0,88,81,0.10);
      --bg:           #f0f4f8;
      --surface:      #ffffff;
      --surface-low:  #f5f7fa;
      --surface-high: #e6e8ea;
      --border:       #e2e8f0;
      --text:         #0f172a;
      --text-muted:   #64748b;
      --error:        #ba1a1a;
      --shadow:       0 2px 16px rgba(0,77,153,0.08);
      --sidebar-w:    220px;
      --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
    }
    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

    /* SIDEBAR */
    .sidebar { width:var(--sidebar-w); min-height:100vh; position:fixed; top:0; left:0; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; padding:20px 12px; z-index:100; }
    .sidebar-brand { display:flex; align-items:center; gap:10px; padding:6px 8px 16px; border-bottom:1px solid var(--border); margin-bottom:12px; }
    .brand-logo { width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .brand-logo svg { width:20px; height:20px; fill:white; }
    .brand-text .name { font-family:'Manrope',sans-serif; font-weight:800; font-size:15px; color:#1e3a6e; display:block; line-height:1.1; }
    .brand-text .sub { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--text-muted); display:block; }
    .sidebar-profile { display:flex; align-items:center; gap:10px; padding:10px; background:var(--surface-low); border-radius:var(--r-md); margin-bottom:10px; }
    .profile-avatar { width:40px; height:40px; border-radius:var(--r-full); background:var(--primary-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .profile-avatar svg { width:22px; height:22px; fill:var(--primary); }
    .profile-name { font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; color:var(--text); display:block; }
    .profile-spec { font-size:11px; color:var(--text-muted); display:block; }
    .sidebar-nav { display:flex; flex-direction:column; gap:2px; flex:1; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--r-md); color:var(--text-muted); font-size:14px; font-weight:500; text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item svg { width:18px; height:18px; flex-shrink:0; }
    .nav-item:hover { background:rgba(0,77,153,0.05); color:var(--primary); }
    .nav-item.active { background:var(--surface); color:var(--primary); font-weight:700; border-left-color:var(--teal); box-shadow:var(--shadow); }
    .nav-item.logout { color:var(--error); }
    .nav-item.logout:hover { background:rgba(186,26,26,0.05); }
    .sidebar-footer { padding-top:12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

    /* MAIN */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

    /* TOPBAR */
    .topbar { height:64px; background:rgba(255,255,255,0.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:50; }
    .topbar-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:20px; background:linear-gradient(135deg,#1e3a6e,var(--primary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .topbar-right { display:flex; align-items:center; gap:8px; }
    .search-bar { display:flex; align-items:center; background:var(--surface-low); border-radius:var(--r-full); padding:7px 14px; gap:7px; width:200px; border:1px solid var(--border); }
    .search-bar svg { width:15px; height:15px; color:var(--text-muted); flex-shrink:0; }
    .search-bar input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text); width:100%; font-family:'Inter',sans-serif; }
    .search-bar input::placeholder { color:#94a3b8; }
    .icon-btn { width:36px; height:36px; border:none; background:transparent; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; color:var(--text-muted); cursor:pointer; transition:background 0.15s; text-decoration:none; }
    .icon-btn:hover { background:var(--surface-low); }
    .icon-btn svg { width:20px; height:20px; }
    .btn-new-rdv { display:flex; align-items:center; gap:7px; padding:8px 16px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; box-shadow:0 2px 8px rgba(0,77,153,0.25); transition:all 0.15s; }
    .btn-new-rdv:hover { box-shadow:0 4px 16px rgba(0,77,153,0.35); transform:translateY(-1px); }
    .btn-new-rdv svg { width:16px; height:16px; }

    /* PAGE */
    .page-content { padding:24px 28px; flex:1; }

    /* En-tête semaine */
    .week-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:20px; }
    .week-title { font-family:'Manrope',sans-serif; font-size:26px; font-weight:800; color:var(--text); }
    .week-range { font-size:13px; color:var(--text-muted); margin-top:2px; }
    .week-controls { display:flex; align-items:center; gap:10px; }
    .nav-week { display:flex; gap:4px; }
    .view-toggle { display:flex; background:var(--surface-high); border-radius:var(--r-md); padding:4px; gap:2px; }
    .view-btn { padding:6px 16px; border:none; border-radius:8px; font-family:'Manrope',sans-serif; font-weight:600; font-size:13px; cursor:pointer; color:var(--text-muted); background:transparent; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; }
    .view-btn.active { background:white; color:var(--primary); box-shadow:0 2px 8px rgba(0,77,153,0.10); }
    .view-btn:hover:not(.active) { color:var(--primary); }

    /* VUE MOIS */
    .month-grid-header { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:1px solid var(--border); }
    .month-col-name { padding:10px 6px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); }
    .month-grid { display:grid; grid-template-columns:repeat(7,1fr); }
    .month-cell { min-height:100px; border-right:1px solid var(--border); border-bottom:1px solid var(--border); padding:6px; position:relative; }
    .month-cell.other { background:var(--surface-low); }
    .month-cell.today { background:#eff6ff; }
    .month-cell-num { font-size:12px; font-weight:700; color:var(--text-muted); margin-bottom:4px; width:22px; height:22px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
    .month-cell-num.today-num { background:var(--primary); color:white; }
    .month-event { font-size:10px; font-weight:600; padding:2px 5px; border-radius:4px; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:default; }
    .dot-blue { background:var(--primary-light); color:var(--primary); }
    .dot-grey { background:#f1f5f9; color:#64748b; }
    .dot-event { background:rgba(0,88,81,0.12); color:var(--teal); }
    .month-more { font-size:10px; color:var(--text-muted); font-weight:600; margin-top:2px; }

    /* LAYOUT */
    .planning-layout { display:grid; grid-template-columns:1fr 260px; gap:20px; align-items:start; }

    /* CALENDRIER */
    .calendar-card { background:var(--surface); border-radius:var(--r-xl); box-shadow:var(--shadow); overflow:hidden; }
    .cal-days-header { display:grid; grid-template-columns:56px repeat(5,1fr); background:var(--surface-low); border-bottom:1px solid rgba(194,198,212,0.3); }
    .cal-day-col { padding:12px 8px; text-align:center; border-left:1px solid rgba(194,198,212,0.2); }
    .cal-day-col:first-child { border-left:none; }
    .cal-day-name { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#94a3b8; }
    .cal-day-num { font-family:'Manrope',sans-serif; font-size:18px; font-weight:800; color:var(--text); margin-top:2px; }
    .cal-day-col.today { background:rgba(0,77,153,0.04); }
    .cal-day-col.today .cal-day-name { color:var(--primary); }
    .cal-day-col.today .cal-day-num  { color:var(--primary); }

    .calendar-scroll { height:580px; overflow-y:auto; }
    .calendar-grid { display:grid; grid-template-columns:56px repeat(5,1fr); }

    .time-col { display:flex; flex-direction:column; }
    .time-slot { height:76px; border-bottom:1px solid rgba(194,198,212,0.12); font-size:10px; font-weight:700; color:#94a3b8; padding:5px 8px 0 0; text-align:right; flex-shrink:0; }

    .day-col { border-left:1px solid rgba(194,198,212,0.12); position:relative; }
    .day-col.today { background:rgba(0,77,153,0.02); }

    .now-line { position:absolute; left:0; right:0; height:2px; background:var(--error); z-index:10; top:304px; }
    .now-line::before { content:''; width:8px; height:8px; background:var(--error); border-radius:var(--r-full); position:absolute; left:-4px; top:-3px; }

    .day-hour-lines { position:absolute; inset:0; display:flex; flex-direction:column; pointer-events:none; }
    .day-hour-line { height:76px; border-bottom:1px solid rgba(194,198,212,0.10); flex-shrink:0; }

    /* Events */
    .cal-event { position:absolute; left:4px; right:4px; border-radius:8px; padding:7px 9px; cursor:pointer; transition:all 0.15s; overflow:hidden; }
    .cal-event:hover { filter:brightness(0.95); box-shadow:0 4px 12px rgba(0,0,0,0.12); }
    .cal-event-time { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; opacity:0.75; }
    .cal-event-name { font-size:12px; font-weight:700; margin-top:2px; }
    .cal-event-note { font-size:10px; opacity:0.7; font-style:italic; margin-top:2px; }

    .event-blue    { background:#eff4ff; border-left:4px solid var(--primary); color:var(--primary); }
    .event-teal    { background:#f0fdfb; border-left:4px solid var(--teal); color:var(--teal); }
    .event-solid   { background:var(--primary); color:white; box-shadow:0 4px 16px rgba(0,77,153,0.30); }
    .event-grey    { background:var(--surface-low); border-left:4px solid #cbd5e1; color:#64748b; }
    .event-urgent  { background:#fff5f5; border-left:4px solid var(--error); color:var(--error); }
    .event-purple  { background:#f5f3ff; border-left:4px solid #7c3aed; color:#7c3aed; }
    .event-orange  { background:#fff7ed; border-left:4px solid #ea580c; color:#ea580c; }
    .event-green   { background:#f0fdf4; border-left:4px solid #16a34a; color:#16a34a; }
    .event-pink    { background:#fdf2f8; border-left:4px solid #db2777; color:#db2777; }
    .badge-urgent-sm { display:inline-block; margin-top:3px; padding:1px 6px; background:rgba(186,26,26,0.12); color:var(--error); border-radius:4px; font-size:8px; font-weight:800; text-transform:uppercase; }
    .badge-type-sm { display:inline-block; margin-top:3px; padding:1px 6px; border-radius:4px; font-size:8px; font-weight:800; text-transform:uppercase; background:rgba(0,0,0,0.08); }

    /* PANNEAUX DROITE */
    .side-panels { display:flex; flex-direction:column; gap:14px; }
    .side-card { background:var(--surface); border-radius:var(--r-xl); padding:20px; box-shadow:var(--shadow); }
    .side-card-title { font-family:'Manrope',sans-serif; font-size:14px; font-weight:700; margin-bottom:14px; color:var(--text); display:flex; align-items:center; justify-content:space-between; }

    /* Stats journée */
    .day-stats { display:flex; flex-direction:column; gap:10px; }
    .stat-row { display:flex; justify-content:space-between; align-items:center; font-size:13px; }
    .stat-label { color:var(--text-muted); }
    .stat-val { font-weight:700; font-size:14px; }
    .v-primary { color:var(--primary); }
    .v-error   { color:var(--error); }
    .v-teal    { color:var(--teal); }
    .v-orange  { color:#ea580c; }

    /* Prochain patient */
    .next-patient { display:flex; align-items:center; gap:10px; padding:12px; background:linear-gradient(135deg,var(--primary-light),#e0eaff); border-radius:var(--r-md); margin-top:14px; border:1px solid rgba(0,77,153,0.12); }
    .next-avatar { width:36px; height:36px; border-radius:var(--r-full); background:var(--primary); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Manrope',sans-serif; font-weight:800; font-size:13px; color:white; }
    .next-name { font-weight:700; font-size:13px; color:var(--text); }
    .next-time { font-size:11px; font-weight:700; color:var(--primary); margin-top:1px; }
    .next-none { font-size:13px; color:var(--text-muted); text-align:center; padding:14px 0; font-style:italic; }

    /* Mini calendrier */
    .mini-cal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .mini-cal-title { font-size:13px; font-weight:700; color:var(--text); }
    .mini-cal-nav { display:flex; gap:2px; }
    .mini-cal-nav a { width:24px; height:24px; border:none; background:transparent; cursor:pointer; color:#94a3b8; border-radius:4px; display:flex; align-items:center; justify-content:center; transition:background 0.12s; text-decoration:none; }
    .mini-cal-nav a:hover { background:var(--surface-low); color:var(--primary); }
    .mini-cal-nav a svg { width:14px; height:14px; }
    .mini-cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; text-align:center; }
    .mini-day-name { font-size:9px; font-weight:700; color:#94a3b8; padding:3px 0; }
    .mini-day { font-size:11px; font-weight:600; padding:5px 2px; border-radius:5px; cursor:pointer; transition:background 0.12s; color:var(--text); }
    .mini-day:hover { background:var(--surface-low); }
    .mini-day.other { color:#cbd5e1; cursor:default; pointer-events:none; }
    .mini-day.today-mark { background:var(--primary-light); color:var(--primary); font-weight:800; border-radius:5px; }
    .mini-day.active { background:var(--primary); color:white; font-weight:800; box-shadow:0 2px 8px rgba(0,77,153,0.25); }

    /* Carte efficacité */
    .efficiency-card { background:linear-gradient(135deg,#1e293b,#0f172a); border-radius:var(--r-xl); padding:20px; color:white; position:relative; overflow:hidden; }
    .eff-label { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.15em; color:#2dd4bf; margin-bottom:5px; }
    .eff-value { font-family:'Manrope',sans-serif; font-size:34px; font-weight:800; margin-bottom:10px; }
    .eff-bar-bg { height:5px; background:rgba(255,255,255,0.10); border-radius:3px; overflow:hidden; }
    .eff-bar-fill { height:100%; background:#2dd4bf; border-radius:3px; }
    .eff-note { font-size:10px; opacity:0.5; margin-top:8px; }
    .eff-bg-icon { position:absolute; bottom:-10px; right:-10px; opacity:0.06; }
    .eff-bg-icon svg { width:80px; height:80px; fill:white; transform:rotate(12deg); }

    /* Raccourcis rapides */
    .quick-actions { display:flex; flex-direction:column; gap:8px; }
    .quick-btn { display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--surface-low); border-radius:var(--r-md); border:1px solid var(--border); cursor:pointer; font-size:13px; font-weight:600; color:var(--text); text-decoration:none; transition:all 0.15s; }
    .quick-btn:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-light); }
    .quick-btn-icon { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .quick-btn-icon svg { width:14px; height:14px; }
    .qb-blue  { background:var(--primary-light); color:var(--primary); }
    .qb-teal  { background:#ccfaf5; color:var(--teal); }
    .qb-red   { background:#fee2e2; color:var(--error); }
    .qb-orange{ background:#ffedd5; color:#ea580c; }

    /* MODALES */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal { background:var(--surface); border-radius:var(--r-xl); padding:28px; width:480px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto; }
    .modal-title { font-family:'Manrope',sans-serif; font-size:17px; font-weight:800; margin-bottom:6px; color:var(--text); }
    .modal-sub { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
    .modal-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
    .modal-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .modal-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); display:flex; justify-content:space-between; }
    .modal-label .char-count { font-weight:500; text-transform:none; letter-spacing:0; color:#94a3b8; }
    .modal-input { width:100%; background:var(--surface-low); border:2px solid transparent; border-radius:var(--r-md); padding:10px 14px; font-size:14px; font-family:'Inter',sans-serif; color:var(--text); outline:none; transition:all 0.18s; }
    .modal-input:focus { border-color:var(--teal); background:white; }
    .modal-input.input-error { border-color:var(--error) !important; background:#fff5f5; }
    .modal-input.input-ok { border-color:#16a34a; }
    .field-error { font-size:11px; color:var(--error); font-weight:600; margin-top:4px; display:none; }
    .field-error.show { display:block; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
    .btn-modal-cancel { padding:10px 20px; background:transparent; border:1.5px solid var(--border); color:var(--text-muted); font-family:'Manrope',sans-serif; font-weight:600; font-size:13px; border-radius:var(--r-md); cursor:pointer; }
    .btn-modal-save { display:flex; align-items:center; gap:7px; padding:10px 20px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; }
    .btn-modal-save svg { width:14px; height:14px; }

    /* Type pills dans le select */
    .type-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-top:4px; }
    .type-pill { padding:8px 6px; border:2px solid var(--border); border-radius:var(--r-md); text-align:center; cursor:pointer; font-size:11px; font-weight:700; transition:all 0.15s; user-select:none; }
    .type-pill:hover { border-color:var(--primary); }
    .type-pill.selected { border-color:var(--primary); background:var(--primary-light); color:var(--primary); }
    .type-pill .type-icon { font-size:18px; display:block; margin-bottom:3px; }

    /* Boutons sur les events */
    .event-actions { display:flex; gap:4px; margin-top:5px; }
    .event-btn { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border:none; border-radius:4px; font-size:9px; font-weight:700; cursor:pointer; text-transform:uppercase; transition:all 0.15s; text-decoration:none; }
    .event-btn svg { width:10px; height:10px; }
    .event-btn-edit { background:rgba(0,77,153,0.12); color:var(--primary); }
    .event-btn-del  { background:rgba(186,26,26,0.12); color:var(--error); }
    .event-btn:hover { opacity:0.8; }

    /* Toast notification */
    .toast { position:fixed; bottom:24px; right:24px; background:#0f172a; color:white; padding:14px 20px; border-radius:var(--r-md); font-size:13px; font-weight:600; z-index:999; transform:translateY(80px); opacity:0; transition:all 0.3s; display:flex; align-items:center; gap:10px; }
    .toast.show { transform:translateY(0); opacity:1; }
    .toast-icon { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .toast-ok  { background:#16a34a; }
    .toast-err { background:var(--error); }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg>
    </div>
    <div class="brand-text">
      <span class="name">MediFlow Pro</span>
      <span class="sub">Practitioner Portal</span>
    </div>
  </div>

  <div class="sidebar-profile">
    <div class="profile-avatar">
      <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
    </div>
    <div>
      <?php
        $doc_nom        = 'Dr. Marc Laurent';
        $doc_specialite = 'Cardiologue';
      ?>
      <span class="profile-name"><?= htmlspecialchars($doc_nom) ?></span>
      <span class="profile-spec"><?= htmlspecialchars($doc_specialite) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="planning.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Planning
    </a>
    <a href="patients.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Patients
    </a>
    <a href="statistiques.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Statistiques
    </a>
    <a href="settings.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Paramètres
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="support.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Support
    </a>
    <a href="logout.php" class="nav-item logout">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Déconnexion
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <h2 class="topbar-title">Mon Planning Complet</h2>
    <div class="topbar-right">
      <button class="icon-btn" title="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="btn-new-rdv" onclick="ouvrirModalAjout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter événement
      </button>
    </div>
  </header>

  <div class="page-content">

    <?php if ($msg_succes): ?>
    <div style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:12px 18px;border-radius:12px;margin-bottom:16px;font-size:14px;font-weight:600;">
      ✓ <?= htmlspecialchars($msg_succes) ?>
    </div>
    <?php endif; ?>
    <?php if ($msg_erreur): ?>
    <div style="background:#fee2e2;color:#ba1a1a;border:1px solid #fecaca;padding:12px 18px;border-radius:12px;margin-bottom:16px;font-size:14px;font-weight:600;">
      ✗ <?= htmlspecialchars($msg_erreur) ?>
    </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="week-header">
      <div>
        <?php
          if ($vue_active === 'jour') {
            $noms_longs = ['','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
            echo '<div class="week-title">' . $noms_longs[(int)$date_debut->format('N')] . ' ' . $date_debut->format('j') . ' ' . $mini_mois_labels[(int)$date_debut->format('n')] . ' ' . $date_debut->format('Y') . '</div>';
            echo '<div class="week-range">Vue journalière</div>';
          } elseif ($vue_active === 'mois') {
            echo '<div class="week-title">' . $mini_mois_labels[(int)$date_debut->format('n')] . ' ' . $date_debut->format('Y') . '</div>';
            echo '<div class="week-range">Vue mensuelle</div>';
          } else {
            echo '<div class="week-title">Semaine ' . $date_debut->format('W') . '</div>';
            echo '<div class="week-range">' . $date_debut->format('d') . ' ' . $mini_mois_labels[(int)$date_debut->format('n')] . ' — ' . $date_fin->format('d') . ' ' . $mini_mois_labels[(int)$date_fin->format('n')] . ' ' . $date_fin->format('Y') . '</div>';
          }
        ?>
      </div>
      <div class="week-controls">
        <!-- Recherche événement fonctionnelle -->
        <div class="search-bar" style="width:200px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="searchEvent" placeholder="Rechercher..." oninput="rechercherEvenement(this.value)">
        </div>
        <div class="nav-week">
          <a href="<?= $url_prec ?>" class="icon-btn" title="Précédent">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
          <a href="<?= $url_suiv ?>" class="icon-btn" title="Suivant">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
        <div class="view-toggle">
          <a href="planning.php?vue=jour&date=<?= date('Y-m-d') ?>" class="view-btn <?= $vue_active==='jour' ?'active':'' ?>">Jour</a>
          <a href="planning.php?vue=semaine&semaine=<?= $mini_sem_param ?>" class="view-btn <?= $vue_active==='semaine' ?'active':'' ?>">Semaine</a>
          <a href="planning.php?vue=mois&date=<?= $date_debut->format('Y-m-01') ?>" class="view-btn <?= $vue_active==='mois' ?'active':'' ?>">Mois</a>
        </div>
      </div>
    </div>

    <div class="planning-layout">

      <!-- Calendrier -->
      <div class="calendar-card">

        <?php if ($vue_active === 'mois'): ?>
        <!-- ═══════ VUE MOIS ═══════ -->
        <?php
          $mois_first_dow = (int)(new DateTime($date_debut->format('Y-m-01')))->format('N');
          $nb_jours_mois  = (int)$date_fin->format('d');
        ?>
        <div class="month-grid-header">
          <?php foreach (['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $n): ?>
          <div class="month-col-name"><?= $n ?></div>
          <?php endforeach; ?>
        </div>
        <div class="month-grid">
          <?php
            for ($b = 1; $b < $mois_first_dow; $b++) echo '<div class="month-cell other"></div>';
            for ($d = 1; $d <= $nb_jours_mois; $d++):
              $cell_date = $date_debut->format('Y-m-') . sprintf('%02d', $d);
              $is_today  = ($cell_date === date('Y-m-d'));
              $events    = $par_jour[$cell_date] ?? [];
          ?>
          <div class="month-cell <?= $is_today ? 'today' : '' ?>">
            <div class="month-cell-num <?= $is_today ? 'today-num' : '' ?>"><?= $d ?></div>
            <?php foreach (array_slice($events, 0, 3) as $ev):
              $dot_class = ($ev['source'] === 'rdv') ? ($ev['type'] === 'annule' ? 'dot-grey' : 'dot-blue') : 'dot-event';
            ?>
            <div class="month-event <?= $dot_class ?> cal-event-item" data-titre="<?= htmlspecialchars($ev['titre']) ?>">
              <?= substr($ev['debut'],0,5) ?> <?= htmlspecialchars(mb_strimwidth($ev['titre'],0,12,'…')) ?>
            </div>
            <?php endforeach; ?>
            <?php if (count($events) > 3): ?>
            <div class="month-more">+<?= count($events)-3 ?> autres</div>
            <?php endif; ?>
          </div>
          <?php endfor; ?>
          <?php
            $dow_last = (int)(new DateTime($date_debut->format('Y-m-').$nb_jours_mois))->format('N');
            for ($b = $dow_last+1; $b <= 7; $b++) echo '<div class="month-cell other"></div>';
          ?>
        </div>

        <?php else: ?>
        <!-- ═══════ VUE JOUR / SEMAINE ═══════ -->
        <div class="cal-days-header">
          <div style="padding:12px 8px;"></div>
          <?php foreach ($jours as $i => $jour):
            $est_aujourd = $jour->format('Y-m-d') === date('Y-m-d');
            $dow_idx = (int)$jour->format('N') - 1;
          ?>
          <div class="cal-day-col <?= $est_aujourd ? 'today' : '' ?>">
            <div class="cal-day-name"><?= $noms_jours[$dow_idx] ?></div>
            <div class="cal-day-num"><?= $jour->format('j') ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="calendar-scroll">
          <div class="calendar-grid">
            <div class="time-col">
              <?php for ($h = 8; $h <= 17; $h++): ?>
              <div class="time-slot"><?= sprintf('%02d:00', $h) ?></div>
              <?php endfor; ?>
            </div>

            <?php foreach ($jours as $jour):
              $jour_str    = $jour->format('Y-m-d');
              $est_aujourd = $jour_str === date('Y-m-d');
              $events_jour = $par_jour[$jour_str] ?? [];
            ?>
            <div class="day-col <?= $est_aujourd ? 'today' : '' ?>">
              <?php if ($est_aujourd): ?>
              <div class="now-line" id="nowLine"></div>
              <?php endif; ?>
              <div class="day-hour-lines">
                <?php for ($h = 0; $h < 10; $h++): ?>
                <div class="day-hour-line"></div>
                <?php endfor; ?>
              </div>

              <?php foreach ($events_jour as $ev):
                $top = calculTop($ev['debut']);
                if ($ev['source'] === 'rdv') {
                  $css = $ev['type'] === 'annule' ? 'event-grey' : 'event-blue';
                } else {
                  $css = match($ev['type']) {
                    'chirurgie' => 'event-solid', 'reunion' => 'event-teal',
                    'pause' => 'event-grey', 'urgence' => 'event-urgent',
                    'formation' => 'event-purple', 'garde' => 'event-orange',
                    'consultation' => 'event-green', 'visite' => 'event-pink',
                    'telemedicine' => 'event-purple', 'administratif' => 'event-grey',
                    default => 'event-blue',
                  };
                }
                $hauteur = $ev['fin'] ? calculHauteur($ev['debut'], $ev['fin']) : 60;
                $fin_fmt = $ev['fin'] ? substr($ev['fin'], 0, 5) : null;
                $type_icons = ['rdv'=>'👤','chirurgie'=>'🔪','reunion'=>'🤝','pause'=>'☕','urgence'=>'🚨','formation'=>'📚','garde'=>'🌙','consultation'=>'💊','visite'=>'🏥','telemedicine'=>'💻','administratif'=>'📋','autre'=>'📌'];
                $icon = $type_icons[$ev['type']] ?? '📌';
              ?>
              <div class="cal-event <?= $css ?> cal-event-item"
                   style="top:<?= $top ?>px;height:<?= $hauteur ?>px;"
                   data-titre="<?= htmlspecialchars($ev['titre']) ?>">
                <div class="cal-event-time"><?= $icon ?> <?= substr($ev['debut'],0,5) ?><?= $fin_fmt?' – '.$fin_fmt:'' ?></div>
                <div class="cal-event-name"><?= htmlspecialchars($ev['titre']) ?></div>
                <?php if ($ev['note']): ?><div class="cal-event-note"><?= htmlspecialchars($ev['note']) ?></div><?php endif; ?>
                <?php if ($ev['type']==='urgence'): ?><span class="badge-urgent-sm">Urgent</span><?php endif; ?>
                <div class="event-actions">
                  <?php if ($ev['source'] === 'planning'): ?>
                  <button class="event-btn event-btn-edit" onclick="ouvrirModalModif(<?= htmlspecialchars(json_encode($ev)) ?>)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Modifier
                  </button>
                  <a href="planning.php?supprimer_event=<?= $ev['id'] ?>&vue=<?= $vue_active ?>" class="event-btn event-btn-del" onclick="return confirm('Supprimer ?')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>Supprimer
                  </a>
                  <?php else: ?>
                  <a href="modifier-rdv.php?id=<?= $ev['id'] ?>" class="event-btn event-btn-edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>Voir RDV
                  </a>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- ===== PANNEAUX DROITE ===== -->
      <div class="side-panels">

        <!-- Aperçu journée -->
        <div class="side-card">
          <div class="side-card-title">
            <span>📊 Aperçu Journée</span>
            <span style="font-size:11px;font-weight:500;color:var(--text-muted);"><?= date('d/m/Y') ?></span>
          </div>
          <div class="day-stats">
            <div class="stat-row">
              <span class="stat-label">Total RDV semaine</span>
              <span class="stat-val v-primary"><?= $stats['total'] ?></span>
            </div>
            <div class="stat-row">
              <span class="stat-label">Aujourd'hui</span>
              <span class="stat-val v-teal"><?= $stats['nb_aujourdhui'] ?></span>
            </div>
            <div class="stat-row">
              <span class="stat-label">En attente</span>
              <span class="stat-val v-error"><?= $stats['nb_attente'] ?></span>
            </div>
            <div class="stat-row">
              <span class="stat-label">Confirmés</span>
              <span class="stat-val v-teal"><?= ($stats['total'] - $stats['nb_attente']) ?></span>
            </div>
          </div>

          <!-- Prochain patient -->
          <div style="margin-top:14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:6px;">Prochain patient</div>
          <?php if ($prochain_patient): ?>
          <div class="next-patient">
            <?php
              $initiales_pp = '?';
              if (!empty($prochain_patient['titre'])) {
                $words = explode(' ', $prochain_patient['titre']);
                $initiales_pp = strtoupper(substr($words[0],0,1) . (isset($words[1]) ? substr($words[1],0,1) : ''));
              }
            ?>
            <div class="next-avatar"><?= $initiales_pp ?></div>
            <div>
              <div class="next-name"><?= htmlspecialchars($prochain_patient['titre']) ?></div>
              <div class="next-time">🕐 <?= substr($prochain_patient['debut'],0,5) ?></div>
            </div>
          </div>
          <?php else: ?>
          <div class="next-none">Aucun patient à venir aujourd'hui</div>
          <?php endif; ?>
        </div>

        <!-- Mini calendrier dynamique -->
        <div class="side-card">
          <div class="mini-cal-header">
            <span class="mini-cal-title">
              <?= $mini_mois_labels[(int)$mini_cal_date->format('n')] ?> <?= $mini_cal_date->format('Y') ?>
            </span>
            <div class="mini-cal-nav">
              <a href="planning.php?semaine=<?= $mini_sem_param ?>&mini_mois=<?= $mini_cal_mois_prec->format('Y-m') ?>" title="Mois précédent">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
              </a>
              <a href="planning.php?semaine=<?= $mini_sem_param ?>&mini_mois=<?= $mini_cal_mois_suiv->format('Y-m') ?>" title="Mois suivant">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
            </div>
          </div>
          <div class="mini-cal-grid">
            <div class="mini-day-name">L</div>
            <div class="mini-day-name">M</div>
            <div class="mini-day-name">M</div>
            <div class="mini-day-name">J</div>
            <div class="mini-day-name">V</div>
            <div class="mini-day-name">S</div>
            <div class="mini-day-name">D</div>
            <?php
              // Cases vides avant le 1er
              $dow_start = $mini_cal_first_dow; // 1=Lun, 7=Dim
              for ($blank = 1; $blank < $dow_start; $blank++) {
                echo '<div class="mini-day other">·</div>';
              }
              // Jours du mois
              for ($d = 1; $d <= $mini_cal_days_in_month; $d++) {
                $date_courante = $mini_cal_date->format('Y-m-') . sprintf('%02d', $d);
                $is_today = ($date_courante === date('Y-m-d'));
                $is_semaine_actuelle = ($date_courante >= $debut_str && $date_courante <= $fin_str);
                $classes = 'mini-day';
                if ($is_today) $classes .= ' today-mark';
                if ($is_semaine_actuelle) $classes .= ' active';
                // Calculer la semaine ISO pour le lien
                $d_obj = new DateTime($date_courante);
                $week_link = $d_obj->format('Y-\WW');
                echo "<a href='planning.php?vue=semaine&semaine={$week_link}&mini_mois={$mini_cal_mois_param}' style='text-decoration:none;'>";
                echo "<div class='$classes'>$d</div>";
                echo "</a>";
              }
              // Cases vides après le dernier jour
              $dow_last = (int)(new DateTime($mini_cal_date->format('Y-m-') . $mini_cal_days_in_month))->format('N');
              for ($blank = $dow_last + 1; $blank <= 7; $blank++) {
                echo '<div class="mini-day other">·</div>';
              }
            ?>
          </div>
        </div>

        <!-- Raccourcis rapides -->
        <div class="side-card">
          <div class="side-card-title">⚡ Actions Rapides</div>
          <div class="quick-actions">
            <button class="quick-btn" onclick="ouvrirModalAjout()">
              <span class="quick-btn-icon qb-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
              Nouvel événement
            </button>
            <a href="dashboard.php" class="quick-btn">
              <span class="quick-btn-icon qb-teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              </span>
              Voir les RDV
            </a>
            <a href="planning.php?vue=semaine&semaine=<?= date('Y-\WW') ?>" class="quick-btn">
              <span class="quick-btn-icon qb-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </span>
              Semaine actuelle
            </a>
            <a href="statistiques.php" class="quick-btn">
              <span class="quick-btn-icon qb-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              </span>
              Statistiques
            </a>
          </div>
        </div>

        <!-- Efficacité -->
        <div class="efficiency-card">
          <div class="eff-label">Efficacité Semaine</div>
          <?php
            $total = max(1, $stats['total']);
            $confirmes = max(0, $total - $stats['nb_attente']);
            $efficacite = round($confirmes / $total * 100);
          ?>
          <div class="eff-value"><?= $efficacite ?>%</div>
          <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:<?= $efficacite ?>%;"></div></div>
          <div class="eff-note">Taux de confirmation des RDV cette semaine.</div>
          <div class="eff-bg-icon">
            <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
          </div>
        </div>

      </div><!-- /side-panels -->
    </div><!-- /planning-layout -->
  </div><!-- /page-content -->
</div><!-- /main -->

<!-- Toast notification -->
<div class="toast" id="toast">
  <div class="toast-icon" id="toastIcon"></div>
  <span id="toastMsg"></span>
</div>

<!-- ===== MODALE AJOUTER ÉVÉNEMENT ===== -->
<div class="modal-overlay" id="modalAjout">
  <div class="modal">
    <h3 class="modal-title">➕ Ajouter un événement</h3>
    <p class="modal-sub">Planifiez un événement : réunion, chirurgie, garde, formation...</p>

    <form method="POST" action="planning.php" id="formAjout" novalidate>
      <input type="hidden" name="action" value="ajouter_evenement">

      <!-- Titre -->
      <div class="modal-group">
        <label class="modal-label">
          Titre
          <span class="char-count" id="ajoutTitreCount">0/10</span>
        </label>
        <input class="modal-input" type="text" name="titre" id="ajoutTitre"
               placeholder="Ex: Réunion, Pause..." maxlength="10" required>
        <span class="field-error" id="errAjoutTitre"></span>
      </div>

      <!-- Type avec icônes -->
      <div class="modal-group">
        <label class="modal-label">Type d'événement</label>
        <input type="hidden" name="type" id="ajoutTypeVal" value="reunion">
        <div class="type-grid">
          <div class="type-pill selected" data-type="reunion" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">🤝</span>Réunion
          </div>
          <div class="type-pill" data-type="chirurgie" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">🔪</span>Chirurgie
          </div>
          <div class="type-pill" data-type="pause" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">☕</span>Pause
          </div>
          <div class="type-pill" data-type="formation" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">📚</span>Formation
          </div>
          <div class="type-pill" data-type="urgence" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">🚨</span>Urgence
          </div>
          <div class="type-pill" data-type="garde" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">🌙</span>Garde
          </div>
          <div class="type-pill" data-type="consultation" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">💊</span>Consultation
          </div>
          <div class="type-pill" data-type="visite" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">🏥</span>Visite
          </div>
          <div class="type-pill" data-type="telemedicine" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">💻</span>Télémédecine
          </div>
          <div class="type-pill" data-type="administratif" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">📋</span>Administratif
          </div>
          <div class="type-pill" data-type="autre" onclick="selectType(this,'ajoutTypeVal')">
            <span class="type-icon">📌</span>Autre
          </div>
        </div>
      </div>

      <!-- Dates -->
      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Date et heure de début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" id="ajoutDebut" required>
          <span class="field-error" id="errAjoutDebut"></span>
        </div>
        <div class="modal-group">
          <label class="modal-label">Date et heure de fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" id="ajoutFin" required>
          <span class="field-error" id="errAjoutFin"></span>
        </div>
      </div>

      <!-- Remarque -->
      <div class="modal-group">
        <label class="modal-label">
          Remarque (optionnel)
          <span class="char-count" id="ajoutNoteCount">0/20</span>
        </label>
        <input class="modal-input" type="text" name="note" id="ajoutNote"
               placeholder="Courte remarque..." maxlength="20">
        <span class="field-error" id="errAjoutNote"></span>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="fermerModals()">Annuler</button>
        <button type="submit" class="btn-modal-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Ajouter
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ===== MODALE MODIFIER ÉVÉNEMENT ===== -->
<div class="modal-overlay" id="modalModif">
  <div class="modal">
    <h3 class="modal-title">✏️ Modifier l'événement</h3>
    <p class="modal-sub">Modifiez les informations de cet événement.</p>

    <form method="POST" action="planning.php" id="formModif" novalidate>
      <input type="hidden" name="action" value="modifier_evenement">
      <input type="hidden" name="event_id" id="modifId">

      <!-- Titre -->
      <div class="modal-group">
        <label class="modal-label">
          Titre
          <span class="char-count" id="modifTitreCount">0/10</span>
        </label>
        <input class="modal-input" type="text" name="titre" id="modifTitre"
               maxlength="10" required>
        <span class="field-error" id="errModifTitre"></span>
      </div>

      <!-- Type -->
      <div class="modal-group">
        <label class="modal-label">Type d'événement</label>
        <input type="hidden" name="type" id="modifTypeVal" value="reunion">
        <div class="type-grid" id="modifTypeGrid">
          <div class="type-pill" data-type="reunion" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">🤝</span>Réunion
          </div>
          <div class="type-pill" data-type="chirurgie" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">🔪</span>Chirurgie
          </div>
          <div class="type-pill" data-type="pause" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">☕</span>Pause
          </div>
          <div class="type-pill" data-type="formation" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">📚</span>Formation
          </div>
          <div class="type-pill" data-type="urgence" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">🚨</span>Urgence
          </div>
          <div class="type-pill" data-type="garde" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">🌙</span>Garde
          </div>
          <div class="type-pill" data-type="consultation" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">💊</span>Consultation
          </div>
          <div class="type-pill" data-type="visite" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">🏥</span>Visite
          </div>
          <div class="type-pill" data-type="telemedicine" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">💻</span>Télémédecine
          </div>
          <div class="type-pill" data-type="administratif" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">📋</span>Administratif
          </div>
          <div class="type-pill" data-type="autre" onclick="selectType(this,'modifTypeVal')">
            <span class="type-icon">📌</span>Autre
          </div>
        </div>
      </div>

      <!-- Dates -->
      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" id="modifDebut" required>
          <span class="field-error" id="errModifDebut"></span>
        </div>
        <div class="modal-group">
          <label class="modal-label">Fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" id="modifFin" required>
          <span class="field-error" id="errModifFin"></span>
        </div>
      </div>

      <!-- Remarque -->
      <div class="modal-group">
        <label class="modal-label">
          Remarque (optionnel)
          <span class="char-count" id="modifNoteCount">0/20</span>
        </label>
        <input class="modal-input" type="text" name="note" id="modifNote" maxlength="20">
        <span class="field-error" id="errModifNote"></span>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="fermerModals()">Annuler</button>
        <button type="submit" class="btn-modal-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// ==========================================
// VALIDATION
// ==========================================
const TITRE_MAX   = 10;
const NOTE_MAX    = 20;
// Regex : uniquement lettres, chiffres, espaces, tiret, apostrophe
const TITRE_REGEX = /^[a-zA-ZÀ-ÿ0-9\s\-']+$/;

function showError(id, msg) {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent = msg;
  el.classList.add('show');
}
function clearError(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent = '';
  el.classList.remove('show');
}
function setInputState(input, ok) {
  input.classList.remove('input-error', 'input-ok');
  if (ok === true)  input.classList.add('input-ok');
  if (ok === false) input.classList.add('input-error');
}

// Compteur de caractères
function bindCounter(inputId, countId, max) {
  const inp = document.getElementById(inputId);
  const cnt = document.getElementById(countId);
  if (!inp || !cnt) return;
  inp.addEventListener('input', () => {
    const len = inp.value.length;
    cnt.textContent = len + '/' + max;
    cnt.style.color = len > max ? 'var(--error)' : '#94a3b8';
  });
}
bindCounter('ajoutTitre', 'ajoutTitreCount', TITRE_MAX);
bindCounter('ajoutNote',  'ajoutNoteCount',  NOTE_MAX);
bindCounter('modifTitre', 'modifTitreCount', TITRE_MAX);
bindCounter('modifNote',  'modifNoteCount',  NOTE_MAX);

// Valider titre
function validerTitre(val, errId, inputEl) {
  clearError(errId);
  if (val.trim() === '') {
    showError(errId, '⚠ Le titre est obligatoire.');
    setInputState(inputEl, false);
    return false;
  }
  if (val.length > TITRE_MAX) {
    showError(errId, `⚠ Maximum ${TITRE_MAX} caractères (vous en avez ${val.length}).`);
    setInputState(inputEl, false);
    return false;
  }
  if (!TITRE_REGEX.test(val)) {
    showError(errId, '⚠ Pas de caractères spéciaux autorisés (!, @, #, $...).');
    setInputState(inputEl, false);
    return false;
  }
  setInputState(inputEl, true);
  return true;
}

// Valider date (pas dans le passé)
function validerDate(val, errId, inputEl, label) {
  clearError(errId);
  if (!val) {
    showError(errId, `⚠ La ${label} est obligatoire.`);
    setInputState(inputEl, false);
    return false;
  }
  const choisi = new Date(val);
  const maintenant = new Date();
  // Tolérance de 2 minutes
  maintenant.setSeconds(maintenant.getSeconds() - 120);
  if (choisi < maintenant) {
    showError(errId, '⚠ Impossible de choisir une date passée.');
    setInputState(inputEl, false);
    return false;
  }
  setInputState(inputEl, true);
  return true;
}

// Valider fin > début
function validerDateFin(debutVal, finVal, errFinId, inputFin) {
  if (!debutVal || !finVal) return true; // déjà géré
  if (new Date(finVal) <= new Date(debutVal)) {
    showError(errFinId, '⚠ La fin doit être après le début.');
    setInputState(inputFin, false);
    return false;
  }
  setInputState(inputFin, true);
  return true;
}

// Valider note
function validerNote(val, errId, inputEl) {
  clearError(errId);
  if (val.length > NOTE_MAX) {
    showError(errId, `⚠ Maximum ${NOTE_MAX} caractères (vous en avez ${val.length}).`);
    setInputState(inputEl, false);
    return false;
  }
  setInputState(inputEl, val.length > 0 ? true : null);
  return true;
}

// Définir le min des datetime-local à maintenant
function setMinDatetime(inputId) {
  const inp = document.getElementById(inputId);
  if (!inp) return;
  const now = new Date();
  now.setSeconds(0, 0);
  inp.min = now.toISOString().slice(0, 16);
}

// ==========================================
// SÉLECTION TYPE (pills)
// ==========================================
function selectType(pill, hiddenId) {
  const grid = pill.closest('.type-grid');
  grid.querySelectorAll('.type-pill').forEach(p => p.classList.remove('selected'));
  pill.classList.add('selected');
  document.getElementById(hiddenId).value = pill.dataset.type;
}

// ==========================================
// MODALES
// ==========================================
function ouvrirModalAjout() {
  setMinDatetime('ajoutDebut');
  setMinDatetime('ajoutFin');
  document.getElementById('modalAjout').classList.add('open');
}

function ouvrirModalModif(ev) {
  document.getElementById('modifId').value    = ev.id;
  document.getElementById('modifTitre').value = ev.titre;
  document.getElementById('modifNote').value  = ev.note || '';
  document.getElementById('modifTypeVal').value = ev.type || 'autre';
  document.getElementById('modifDebut').value = ev.debut_dt || '';
  document.getElementById('modifFin').value   = ev.fin_dt   || '';

  // Mettre à jour compteurs
  const t = document.getElementById('modifTitre');
  const n = document.getElementById('modifNote');
  document.getElementById('modifTitreCount').textContent = t.value.length + '/10';
  document.getElementById('modifNoteCount').textContent  = n.value.length + '/20';

  // Sélectionner la bonne pill de type
  const grid = document.getElementById('modifTypeGrid');
  grid.querySelectorAll('.type-pill').forEach(p => {
    p.classList.toggle('selected', p.dataset.type === ev.type);
  });

  setMinDatetime('modifDebut');
  setMinDatetime('modifFin');
  document.getElementById('modalModif').classList.add('open');
}

function fermerModals() {
  document.getElementById('modalAjout').classList.remove('open');
  document.getElementById('modalModif').classList.remove('open');
  // Nettoyer erreurs
  ['errAjoutTitre','errAjoutDebut','errAjoutFin','errAjoutNote',
   'errModifTitre','errModifDebut','errModifFin','errModifNote'].forEach(clearError);
  ['ajoutTitre','ajoutDebut','ajoutFin','ajoutNote',
   'modifTitre','modifDebut','modifFin','modifNote'].forEach(id => {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('input-error','input-ok'); }
  });
}

// Fermer si clic en dehors
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) fermerModals();
  });
});

// ==========================================
// VALIDATION SOUMISSION FORMULAIRES
// ==========================================
document.getElementById('formAjout').addEventListener('submit', function(e) {
  let ok = true;
  const titre  = document.getElementById('ajoutTitre');
  const debut  = document.getElementById('ajoutDebut');
  const fin    = document.getElementById('ajoutFin');
  const note   = document.getElementById('ajoutNote');

  if (!validerTitre(titre.value, 'errAjoutTitre', titre)) ok = false;
  if (!validerDate(debut.value, 'errAjoutDebut', debut, 'date de début')) ok = false;
  if (!validerDate(fin.value, 'errAjoutFin', fin, 'date de fin')) ok = false;
  if (ok) { if (!validerDateFin(debut.value, fin.value, 'errAjoutFin', fin)) ok = false; }
  if (!validerNote(note.value, 'errAjoutNote', note)) ok = false;

  if (!ok) {
    e.preventDefault();
    showToast('Veuillez corriger les erreurs dans le formulaire.', false);
  }
});

document.getElementById('formModif').addEventListener('submit', function(e) {
  let ok = true;
  const titre  = document.getElementById('modifTitre');
  const debut  = document.getElementById('modifDebut');
  const fin    = document.getElementById('modifFin');
  const note   = document.getElementById('modifNote');

  if (!validerTitre(titre.value, 'errModifTitre', titre)) ok = false;
  if (!validerDate(debut.value, 'errModifDebut', debut, 'date de début')) ok = false;
  if (!validerDate(fin.value, 'errModifFin', fin, 'date de fin')) ok = false;
  if (ok) { if (!validerDateFin(debut.value, fin.value, 'errModifFin', fin)) ok = false; }
  if (!validerNote(note.value, 'errModifNote', note)) ok = false;

  if (!ok) {
    e.preventDefault();
    showToast('Veuillez corriger les erreurs dans le formulaire.', false);
  }
});

// Validation en temps réel
document.getElementById('ajoutTitre').addEventListener('input', function() {
  validerTitre(this.value, 'errAjoutTitre', this);
});
document.getElementById('modifTitre').addEventListener('input', function() {
  validerTitre(this.value, 'errModifTitre', this);
});
document.getElementById('ajoutNote').addEventListener('input', function() {
  validerNote(this.value, 'errAjoutNote', this);
});
document.getElementById('modifNote').addEventListener('input', function() {
  validerNote(this.value, 'errModifNote', this);
});
document.getElementById('ajoutDebut').addEventListener('change', function() {
  validerDate(this.value, 'errAjoutDebut', this, 'date de début');
});
document.getElementById('ajoutFin').addEventListener('change', function() {
  validerDate(this.value, 'errAjoutFin', this, 'date de fin');
  validerDateFin(document.getElementById('ajoutDebut').value, this.value, 'errAjoutFin', this);
});
document.getElementById('modifDebut').addEventListener('change', function() {
  validerDate(this.value, 'errModifDebut', this, 'date de début');
});
document.getElementById('modifFin').addEventListener('change', function() {
  validerDate(this.value, 'errModifFin', this, 'date de fin');
  validerDateFin(document.getElementById('modifDebut').value, this.value, 'errModifFin', this);
});

// ==========================================
// RECHERCHE ÉVÉNEMENT (fonctionnelle)
// ==========================================
function rechercherEvenement(q) {
  q = q.toLowerCase().trim();
  document.querySelectorAll('.cal-event-item').forEach(el => {
    const titre = (el.dataset.titre || '').toLowerCase();
    if (!q || titre.includes(q)) {
      el.style.opacity = '1';
      el.style.pointerEvents = '';
    } else {
      el.style.opacity = '0.15';
      el.style.pointerEvents = 'none';
    }
  });
}

// ==========================================
// LIGNE DU TEMPS (maintenant)
// ==========================================
function updateNowLine() {
  const line = document.getElementById('nowLine');
  if (!line) return;
  const now  = new Date();
  const topPx = ((now.getHours() + now.getMinutes() / 60) - 8) * 76;
  line.style.top = Math.max(0, topPx) + 'px';
}
updateNowLine();
setInterval(updateNowLine, 60000);

// ==========================================
// TOAST NOTIFICATION
// ==========================================
function showToast(msg, success = true) {
  const toast = document.getElementById('toast');
  const icon  = document.getElementById('toastIcon');
  const txt   = document.getElementById('toastMsg');
  icon.className = 'toast-icon ' + (success ? 'toast-ok' : 'toast-err');
  icon.textContent = success ? '✓' : '✗';
  txt.textContent  = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}

// Afficher un toast si succès/erreur dans l'URL
<?php if ($msg_succes): ?>
window.addEventListener('DOMContentLoaded', () => showToast('<?= addslashes($msg_succes) ?>', true));
<?php endif; ?>

// ==========================================
// SCROLL vers l'heure actuelle au chargement
// ==========================================
window.addEventListener('DOMContentLoaded', () => {
  const scroll = document.querySelector('.calendar-scroll');
  if (!scroll) return;
  const now = new Date();
  const topPx = ((now.getHours() + now.getMinutes() / 60) - 8) * 76;
  scroll.scrollTop = Math.max(0, topPx - 120);
});
</script>
</body>
</html>