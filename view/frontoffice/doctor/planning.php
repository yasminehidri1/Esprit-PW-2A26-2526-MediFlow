<?php
// ============================================================
//  planning.php — View (médecin)
//  Appelle le Controller pour récupérer RDV + événements planning
// ============================================================
require_once __DIR__ . '/../../../controller/RendezVousController.php';

$controller = new RendezVousController();
$medecin_id = 1; // remplacer par $_SESSION['medecin_id']

// ACTION : Ajouter un événement planning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_evenement') {
    $controller->ajouterEvenement($medecin_id);
}

// ACTION : Modifier un événement planning — CORRECTION : cette ligne manquait !
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier_evenement') {
    $controller->modifierEvenement($medecin_id);
}

// ACTION : Supprimer un événement planning
if (isset($_GET['supprimer_event'])) {
    $controller->supprimerEvenement(intval($_GET['supprimer_event']), $medecin_id);
}

// Calcul de la semaine affichée
// ?semaine=2024-W42 ou semaine courante par défaut
$semaine_param = $_GET['semaine'] ?? date('Y-\WW');
$date_debut    = new DateTime();
$date_debut->setISODate(...explode('-W', $semaine_param));
$date_debut->setTime(0, 0, 0);
$date_fin = clone $date_debut;
$date_fin->modify('+4 days'); // Lun → Ven

$debut_str = $date_debut->format('Y-m-d');
$fin_str   = $date_fin->format('Y-m-d');

// Données du planning : Controller → Model → BDD
$par_jour = $controller->getPlanningData($medecin_id, $debut_str, $fin_str);

// Stats journée (aujourd'hui)
$stats_data  = $controller->getDashboardData($medecin_id);
$stats       = $stats_data['stats'];

// Navigation semaine précédente / suivante
$sem_prec = clone $date_debut;
$sem_prec->modify('-7 days');
$sem_suiv = clone $date_debut;
$sem_suiv->modify('+7 days');

$url_prec = 'planning.php?semaine=' . $sem_prec->format('Y-\WW');
$url_suiv = 'planning.php?semaine=' . $sem_suiv->format('Y-\WW');

// Jours de la semaine (Lun→Ven) pour l'affichage
$jours = [];
for ($i = 0; $i < 5; $i++) {
    $j = clone $date_debut;
    $j->modify("+$i days");
    $jours[] = $j;
}

// Noms des jours en français
$noms_jours = ['Lun','Mar','Mer','Jeu','Ven'];

// Messages
$msg_succes = isset($_GET['succes']) ? 'Opération effectuée avec succès.' : '';
$msg_erreur = isset($_GET['erreur']) ? 'Une erreur est survenue.' : '';

// Formule positionnement événement dans le calendrier
// top(px) = (heure_debut - 8) * 76
// height(px) = duree_en_heures * 76
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
    .icon-btn { width:36px; height:36px; border:none; background:transparent; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; color:var(--text-muted); cursor:pointer; transition:background 0.15s; }
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
    .view-btn { padding:6px 16px; border:none; border-radius:8px; font-family:'Manrope',sans-serif; font-weight:600; font-size:13px; cursor:pointer; color:var(--text-muted); background:transparent; transition:all 0.15s; }
    .view-btn.active { background:white; color:var(--primary); box-shadow:0 2px 8px rgba(0,77,153,0.10); }

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

    .event-blue   { background:#eff4ff; border-left:4px solid var(--primary); color:var(--primary); }
    .event-teal   { background:#f0fdfb; border-left:4px solid var(--teal); color:var(--teal); }
    .event-solid  { background:var(--primary); color:white; box-shadow:0 4px 16px rgba(0,77,153,0.30); }
    .event-grey   { background:var(--surface-low); border-left:4px solid #cbd5e1; color:#64748b; }
    .event-urgent { background:#fff5f5; border-left:4px solid var(--error); color:var(--error); }
    .badge-urgent-sm { display:inline-block; margin-top:3px; padding:1px 6px; background:rgba(186,26,26,0.12); color:var(--error); border-radius:4px; font-size:8px; font-weight:800; text-transform:uppercase; }

    /* PANNEAUX DROITE */
    .side-panels { display:flex; flex-direction:column; gap:14px; }
    .side-card { background:var(--surface); border-radius:var(--r-xl); padding:20px; box-shadow:var(--shadow); }
    .side-card-title { font-family:'Manrope',sans-serif; font-size:14px; font-weight:700; margin-bottom:14px; color:var(--text); }

    /* Stats journée */
    .day-stats { display:flex; flex-direction:column; gap:10px; }
    .stat-row { display:flex; justify-content:space-between; align-items:center; font-size:13px; }
    .stat-label { color:var(--text-muted); }
    .stat-val { font-weight:700; font-size:14px; }
    .v-primary { color:var(--primary); }
    .v-error   { color:var(--error); }
    .v-teal    { color:var(--teal); }

    .next-patient { display:flex; align-items:center; gap:10px; padding:10px; background:var(--surface-low); border-radius:var(--r-md); margin-top:14px; }
    .next-avatar { width:36px; height:36px; border-radius:var(--r-full); background:#c0d5ff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .next-avatar svg { width:18px; height:18px; fill:var(--primary); }
    .next-name { font-weight:700; font-size:13px; color:var(--text); }
    .next-time { font-size:11px; font-weight:700; color:var(--primary); margin-top:1px; }

    /* Mini calendrier */
    .mini-cal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .mini-cal-title { font-size:13px; font-weight:700; color:var(--text); }
    .mini-cal-nav { display:flex; gap:2px; }
    .mini-cal-nav button { width:22px; height:22px; border:none; background:transparent; cursor:pointer; color:#94a3b8; border-radius:4px; display:flex; align-items:center; justify-content:center; transition:background 0.12s; }
    .mini-cal-nav button:hover { background:var(--surface-low); }
    .mini-cal-nav button svg { width:14px; height:14px; }
    .mini-cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; text-align:center; }
    .mini-day-name { font-size:9px; font-weight:700; color:#94a3b8; padding:3px 0; }
    .mini-day { font-size:11px; font-weight:600; padding:5px 2px; border-radius:5px; cursor:pointer; transition:background 0.12s; color:var(--text); }
    .mini-day:hover { background:var(--surface-low); }
    .mini-day.other { color:#cbd5e1; cursor:default; }
    .mini-day.other:hover { background:transparent; }
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
    /* MODALES */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal { background:var(--surface); border-radius:var(--r-xl); padding:28px; width:440px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    .modal-title { font-family:'Manrope',sans-serif; font-size:17px; font-weight:800; margin-bottom:6px; color:var(--text); }
    .modal-sub { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
    .modal-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
    .modal-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .modal-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); }
    .modal-input { width:100%; background:var(--surface-low); border:2px solid transparent; border-radius:var(--r-md); padding:10px 14px; font-size:14px; font-family:'Inter',sans-serif; color:var(--text); outline:none; transition:all 0.18s; }
    .modal-input:focus { border-color:var(--teal); background:white; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
    .btn-modal-cancel { padding:10px 20px; background:transparent; border:1.5px solid var(--border); color:var(--text-muted); font-family:'Manrope',sans-serif; font-weight:600; font-size:13px; border-radius:var(--r-md); cursor:pointer; }
    .btn-modal-save { display:flex; align-items:center; gap:7px; padding:10px 20px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; }
    .btn-modal-delete { display:flex; align-items:center; gap:7px; padding:10px 20px; background:var(--error); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; }
    .btn-modal-save svg, .btn-modal-delete svg { width:14px; height:14px; }
    /* Boutons sur les events */
    .event-actions { display:flex; gap:4px; margin-top:5px; }
    .event-btn { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border:none; border-radius:4px; font-size:9px; font-weight:700; cursor:pointer; text-transform:uppercase; transition:all 0.15s; }
    .event-btn svg { width:10px; height:10px; }
    .event-btn-edit { background:rgba(0,77,153,0.12); color:var(--primary); }
    .event-btn-del  { background:rgba(186,26,26,0.12); color:var(--error); }
    .event-btn:hover { opacity:0.8; }
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
        // TODO : remplacer par $_SESSION['medecin']['prenom'] et ['nom'] et ['specialite']
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
      Mon Planning
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
      <div class="search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search appointments...">
      </div>
      <button class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </button>
      <button class="btn-new-rdv" onclick="ouvrirModalAjout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter événement
      </button>
    </div>
  </header>

  <div class="page-content">

    <?php if ($msg_succes): ?>
    <div style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:12px 18px;border-radius:12px;margin-bottom:16px;font-size:14px;"><?= htmlspecialchars($msg_succes) ?></div>
    <?php endif; ?>

    <!-- En-tête semaine — données dynamiques -->
    <div class="week-header">
      <div>
        <div class="week-title">Semaine <?= $date_debut->format('W') ?></div>
        <div class="week-range">
          <?= strftime('%d %B', $date_debut->getTimestamp()) ?> —
          <?= strftime('%d %B %Y', $date_fin->getTimestamp()) ?>
        </div>
      </div>
      <div class="week-controls">
        <div class="nav-week">
          <a href="<?= $url_prec ?>" class="icon-btn" title="Semaine précédente">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
          <a href="<?= $url_suiv ?>" class="icon-btn" title="Semaine suivante">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
        <div class="view-toggle">
          <button class="view-btn">Jour</button>
          <button class="view-btn active">Semaine</button>
          <button class="view-btn">Mois</button>
        </div>
      </div>
    </div>

    <div class="planning-layout">

      <!-- Calendrier — données venant du Controller → Model → BDD -->
      <div class="calendar-card">

        <!-- En-tête jours dynamique -->
        <div class="cal-days-header">
          <div style="padding:12px 8px;"></div>
          <?php foreach ($jours as $i => $jour):
            $est_aujourd = $jour->format('Y-m-d') === date('Y-m-d');
          ?>
          <div class="cal-day-col <?= $est_aujourd ? 'today' : '' ?>">
            <div class="cal-day-name"><?= $noms_jours[$i] ?></div>
            <div class="cal-day-num"><?= $jour->format('j') ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="calendar-scroll">
          <div class="calendar-grid">
            <!-- Heures -->
            <div class="time-col">
              <div class="time-slot">08:00</div><div class="time-slot">09:00</div>
              <div class="time-slot">10:00</div><div class="time-slot">11:00</div>
              <div class="time-slot">12:00</div><div class="time-slot">13:00</div>
              <div class="time-slot">14:00</div><div class="time-slot">15:00</div>
              <div class="time-slot">16:00</div><div class="time-slot">17:00</div>
            </div>

            <!-- Colonnes jours dynamiques -->
            <?php foreach ($jours as $jour):
              $jour_str    = $jour->format('Y-m-d');
              $est_aujourd = $jour_str === date('Y-m-d');
              $events_jour = $par_jour[$jour_str] ?? [];
            ?>
            <div class="day-col <?= $est_aujourd ? 'today' : '' ?>">
              <?php if ($est_aujourd): ?>
              <div class="now-line"></div>
              <?php endif; ?>
              <!-- Lignes de séparation horaire -->
              <div class="day-hour-lines">
                <?php for ($h = 0; $h < 10; $h++): ?>
                <div class="day-hour-line"></div>
                <?php endfor; ?>
              </div>

              <!-- Événements de ce jour -->
              <?php foreach ($events_jour as $ev):
                $top = calculTop($ev['debut']);

                // Classe CSS selon type
                if ($ev['source'] === 'rdv') {
                  $css = $ev['type'] === 'annule' ? 'event-grey' : 'event-blue';
                } else {
                  $css = match($ev['type']) {
                    'chirurgie'  => 'event-solid',
                    'reunion'    => 'event-teal',
                    'pause'      => 'event-grey',
                    'urgence'    => 'event-urgent',
                    default      => 'event-blue',
                  };
                }

                // Hauteur
                if ($ev['fin']) {
                  $hauteur = calculHauteur($ev['debut'], $ev['fin']);
                  $fin_fmt = substr($ev['fin'], 0, 5);
                } else {
                  $hauteur = 60; // RDV sans heure de fin = 45min par défaut
                  $fin_fmt = null;
                }
              ?>
              <div class="cal-event <?= $css ?>"
                   style="top:<?= $top ?>px;height:<?= $hauteur ?>px;"
                   title="<?= htmlspecialchars($ev['titre']) ?>">
                <div class="cal-event-time">
                  <?= substr($ev['debut'], 0, 5) ?><?= $fin_fmt ? ' – '.$fin_fmt : '' ?>
                </div>
                <div class="cal-event-name"><?= htmlspecialchars($ev['titre']) ?></div>
                <?php if ($ev['note']): ?>
                <div class="cal-event-note"><?= htmlspecialchars($ev['note']) ?></div>
                <?php endif; ?>
                <?php if ($ev['type'] === 'urgence'): ?>
                <span class="badge-urgent-sm">Urgent</span>
                <?php endif; ?>
                <!-- Boutons modifier / supprimer -->
                <div class="event-actions">
                  <?php if ($ev['source'] === 'planning'): ?>
                  <button class="event-btn event-btn-edit" onclick="ouvrirModalModif(<?= htmlspecialchars(json_encode($ev)) ?>)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Modifier
                  </button>
                  <a href="planning.php?supprimer_event=<?= $ev['id'] ?>"
                     class="event-btn event-btn-del"
                     onclick="return confirm('Supprimer cet événement ?')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                    Supprimer
                  </a>
                  <?php else: ?>
                  <!-- RDV patient : juste voir, pas modifier ici -->
                  <a href="dashboard.php" class="event-btn event-btn-edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Dashboard
                  </a>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>

            </div>
            <?php endforeach; ?>

          </div>
        </div>
      </div>

      <!-- Panneaux droite -->
      <div class="side-panels">

        <!-- Aperçu journée — données du Controller -->
        <div class="side-card">
          <div class="side-card-title">Aperçu Journée</div>
          <div class="day-stats">
            <div class="stat-row"><span class="stat-label">Rendez-vous</span><span class="stat-val v-primary"><?= $stats['total'] ?></span></div>
            <div class="stat-row"><span class="stat-label">Aujourd'hui</span><span class="stat-val v-teal"><?= $stats['nb_aujourdhui'] ?></span></div>
            <div class="stat-row"><span class="stat-label">En attente</span><span class="stat-val v-error"><?= $stats['nb_attente'] ?></span></div>
          </div>
        </div>

        <!-- Mini calendrier -->
        <div class="side-card">
          <div class="mini-cal-header">
            <span class="mini-cal-title">Octobre 2023</span>
            <div class="mini-cal-nav">
              <button><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>
              <button><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>
            </div>
          </div>
          <div class="mini-cal-grid">
            <div class="mini-day-name">L</div><div class="mini-day-name">M</div><div class="mini-day-name">M</div><div class="mini-day-name">J</div><div class="mini-day-name">V</div><div class="mini-day-name">S</div><div class="mini-day-name">D</div>
            <div class="mini-day other">25</div><div class="mini-day other">26</div><div class="mini-day other">27</div><div class="mini-day other">28</div><div class="mini-day other">29</div><div class="mini-day other">30</div><div class="mini-day">1</div>
            <div class="mini-day">2</div><div class="mini-day">3</div><div class="mini-day">4</div><div class="mini-day">5</div><div class="mini-day">6</div><div class="mini-day">7</div><div class="mini-day">8</div>
            <div class="mini-day">9</div><div class="mini-day">10</div><div class="mini-day">11</div><div class="mini-day">12</div><div class="mini-day">13</div><div class="mini-day">14</div><div class="mini-day">15</div>
            <div class="mini-day">16</div><div class="mini-day active">17</div><div class="mini-day">18</div><div class="mini-day">19</div><div class="mini-day">20</div><div class="mini-day">21</div><div class="mini-day">22</div>
          </div>
        </div>

        <!-- Efficacité -->
        <div class="efficiency-card">
          <div class="eff-label">Efficacité</div>
          <div class="eff-value">94%</div>
          <div class="eff-bar-bg"><div class="eff-bar-fill" style="width:94%;"></div></div>
          <div class="eff-note">Taux de présence patients cette semaine.</div>
          <div class="eff-bg-icon">
            <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- ===== MODALE AJOUTER ÉVÉNEMENT ===== -->
<div class="modal-overlay" id="modalAjout">
  <div class="modal">
    <h3 class="modal-title">Ajouter un événement</h3>
    <p class="modal-sub">Ajoutez un événement à votre planning (réunion, pause, chirurgie...)</p>

    <form method="POST" action="planning.php">
      <input type="hidden" name="action" value="ajouter_evenement">

      <div class="modal-group">
        <label class="modal-label">Titre</label>
        <input class="modal-input" type="text" name="titre" placeholder="Ex: Réunion staff, Pause déjeuner..." required>
      </div>

      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Date et heure de début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" required>
        </div>
        <div class="modal-group">
          <label class="modal-label">Date et heure de fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" required>
        </div>
      </div>

      <div class="modal-group">
        <label class="modal-label">Type</label>
        <select class="modal-input" name="type">
          <option value="reunion">Réunion</option>
          <option value="chirurgie">Chirurgie</option>
          <option value="pause">Pause</option>
          <option value="formation">Formation</option>
          <option value="urgence">Urgence</option>
          <option value="autre">Autre</option>
        </select>
      </div>

      <div class="modal-group">
        <label class="modal-label">Note (optionnel)</label>
        <input class="modal-input" type="text" name="note" placeholder="Description...">
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
    <h3 class="modal-title">Modifier l'événement</h3>
    <p class="modal-sub">Modifiez les informations de cet événement.</p>

    <form method="POST" action="planning.php" id="formModif">
      <input type="hidden" name="action" value="modifier_evenement">
      <input type="hidden" name="event_id" id="modifId">

      <div class="modal-group">
        <label class="modal-label">Titre</label>
        <input class="modal-input" type="text" name="titre" id="modifTitre" required>
      </div>

      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Début</label>
          <input class="modal-input" type="datetime-local" name="date_debut" id="modifDebut" required>
        </div>
        <div class="modal-group">
          <label class="modal-label">Fin</label>
          <input class="modal-input" type="datetime-local" name="date_fin" id="modifFin" required>
        </div>
      </div>

      <div class="modal-group">
        <label class="modal-label">Type</label>
        <select class="modal-input" name="type" id="modifType">
          <option value="reunion">Réunion</option>
          <option value="chirurgie">Chirurgie</option>
          <option value="pause">Pause</option>
          <option value="formation">Formation</option>
          <option value="urgence">Urgence</option>
          <option value="autre">Autre</option>
        </select>
      </div>

      <div class="modal-group">
        <label class="modal-label">Note (optionnel)</label>
        <input class="modal-input" type="text" name="note" id="modifNote">
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
  // ---- Vue (Jour/Semaine/Mois) ----
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // ---- Ouvrir modale Ajouter ----
  function ouvrirModalAjout() {
    document.getElementById('modalAjout').classList.add('open');
  }

  // ---- Ouvrir modale Modifier ----
  function ouvrirModalModif(ev) {
    document.getElementById('modifId').value    = ev.id;
    document.getElementById('modifTitre').value = ev.titre;
    document.getElementById('modifType').value  = ev.type;
    document.getElementById('modifNote').value  = ev.note || '';

    // CORRECTION : on utilise debut_dt et fin_dt qui contiennent
    // la date ET l'heure complètes au format YYYY-MM-DDTHH:MM
    // C'est ce qui permet les événements multi-jours (ex: 12 → 14)
    document.getElementById('modifDebut').value = ev.debut_dt || '';
    document.getElementById('modifFin').value   = ev.fin_dt   || '';

    document.getElementById('modalModif').classList.add('open');
  }

  // ---- Fermer toutes les modales ----
  function fermerModals() {
    document.getElementById('modalAjout').classList.remove('open');
    document.getElementById('modalModif').classList.remove('open');
  }

  // Fermer si clic en dehors
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === this) fermerModals();
    });
  });
</script>
</body>
</html>