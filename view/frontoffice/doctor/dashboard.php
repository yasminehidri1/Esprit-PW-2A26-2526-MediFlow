<?php
require_once __DIR__ . '/../../../controller/RendezVousController.php';

$controller = new RendezVousController();
$medecin_id = 1; // remplacer par $_SESSION['medecin_id']

if (isset($_GET['supprimer'])) {
    $controller->supprimerRdv(intval($_GET['supprimer']), $medecin_id);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    $controller->modifierRdv($medecin_id);
}

$filtre      = isset($_GET['statut']) && in_array($_GET['statut'], ['en_attente','confirme','annule']) ? $_GET['statut'] : '';
$data        = $controller->getDashboardData($medecin_id, $filtre);
$rendez_vous = $data['rendez_vous'];
$stats       = $data['stats'];

$msg_succes = '';
$msg_erreur = '';
if (isset($_GET['succes'])) $msg_succes = $_GET['succes'] === 'modifie' ? 'Rendez-vous modifié avec succès.' : 'Rendez-vous supprimé avec succès.';
if (isset($_GET['erreur'])) $msg_erreur = 'Une erreur est survenue.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Praticien — MediFlow Pro</title>
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
      --border:       #e2e8f0;
      --text:         #0f172a;
      --text-muted:   #64748b;
      --error:        #ba1a1a;
      --error-bg:     #ffdad6;
      --shadow:       0 2px 16px rgba(0,77,153,0.08);
      --shadow-hover: 0 8px 32px rgba(0,77,153,0.15);
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
    /* Profil doc dans sidebar */
    .sidebar-profile { display:flex; align-items:center; gap:10px; padding:10px; background:var(--surface-low); border-radius:var(--r-md); margin-bottom:10px; }
    .profile-avatar { width:40px; height:40px; border-radius:var(--r-full); background:var(--primary-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .profile-avatar svg { width:22px; height:22px; fill:var(--primary); }
    .profile-avatar img { width:100%; height:100%; border-radius:var(--r-full); object-fit:cover; }
    .profile-name { font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; color:var(--text); display:block; }
    .profile-spec { font-size:11px; color:var(--text-muted); display:block; }
    .sidebar-nav { display:flex; flex-direction:column; gap:2px; flex:1; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--r-md); color:var(--text-muted); font-size:14px; font-weight:500; text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item svg { width:18px; height:18px; flex-shrink:0; }
    .nav-item:hover { background:rgba(0,77,153,0.05); color:var(--primary); }
    .nav-item.active { background:var(--surface); color:var(--primary); font-weight:700; border-left-color:var(--teal); box-shadow:var(--shadow); }
    .nav-item.logout { color:var(--error); }
    .nav-item.logout:hover { background:rgba(186,26,26,0.05); color:var(--error); }
    .sidebar-footer { padding-top:12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

    /* MAIN */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

    /* TOPBAR */
    .topbar { height:64px; background:rgba(255,255,255,0.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:50; }
    .topbar-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:20px; background:linear-gradient(135deg,#1e3a6e,var(--primary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .topbar-right { display:flex; align-items:center; gap:8px; }
    .search-bar { display:flex; align-items:center; background:var(--surface-low); border-radius:var(--r-full); padding:7px 14px; gap:7px; width:220px; border:1px solid var(--border); }
    .search-bar svg { width:15px; height:15px; color:var(--text-muted); flex-shrink:0; }
    .search-bar input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text); width:100%; font-family:'Inter',sans-serif; }
    .search-bar input::placeholder { color:#94a3b8; }
    .icon-btn { width:36px; height:36px; border:none; background:transparent; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; color:var(--text-muted); cursor:pointer; transition:background 0.15s; }
    .icon-btn:hover { background:var(--surface-low); }
    .icon-btn svg { width:20px; height:20px; }
    .btn-new-patient { display:flex; align-items:center; gap:7px; padding:8px 16px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; box-shadow:0 2px 8px rgba(0,77,153,0.25); transition:all 0.15s; }
    .btn-new-patient:hover { box-shadow:0 4px 16px rgba(0,77,153,0.35); transform:translateY(-1px); }
    .btn-new-patient svg { width:16px; height:16px; }

    /* PAGE */
    .page-content { padding:24px 28px; flex:1; }

    /* Bandeau jour */
    .day-banner { display:flex; justify-content:space-between; align-items:center; background:var(--surface); border-radius:var(--r-xl); padding:18px 24px; box-shadow:var(--shadow); border:1px solid rgba(194,198,212,0.2); margin-bottom:28px; }
    .day-banner-left { display:flex; align-items:center; gap:14px; }
    .day-banner-icon { width:44px; height:44px; background:var(--primary-light); border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; }
    .day-banner-icon svg { width:22px; height:22px; color:var(--primary); }
    .day-banner-title { font-family:'Manrope',sans-serif; font-size:15px; font-weight:700; color:var(--text); }
    .day-banner-sub { font-size:12px; color:var(--text-muted); margin-top:2px; }
    .btn-planning-link { display:flex; align-items:center; gap:7px; padding:9px 18px; background:var(--surface-low); color:var(--primary); border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; text-decoration:none; border:1px solid var(--border); transition:all 0.15s; }
    .btn-planning-link:hover { background:var(--primary-light); transform:translateX(2px); }
    .btn-planning-link svg { width:16px; height:16px; }

    /* Section controls */
    .section-controls { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .section-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:18px; color:var(--text); }
    .filter-group { display:flex; align-items:center; gap:10px; }
    .filter-label { font-size:13px; color:var(--text-muted); }
    .filter-label span { color:var(--primary); font-weight:700; cursor:pointer; }
    .btn-filter-sm { display:flex; align-items:center; gap:6px; padding:7px 13px; background:var(--surface-low); border:1px solid var(--border); border-radius:var(--r-md); color:var(--text-muted); font-size:13px; font-weight:600; cursor:pointer; transition:background 0.15s; font-family:'Inter',sans-serif; }
    .btn-filter-sm:hover { background:var(--surface); color:var(--primary); }
    .btn-filter-sm svg { width:14px; height:14px; }

    /* Liste RDV */
    .appointments-list { display:flex; flex-direction:column; gap:8px; }

    .appt-row { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:var(--surface); border-radius:var(--r-lg); border:1px solid transparent; box-shadow:var(--shadow); transition:all 0.2s; }
    .appt-row:hover { border-color:rgba(194,198,212,0.5); box-shadow:var(--shadow-hover); transform:translateY(-1px); }

    .appt-left { display:flex; align-items:center; gap:14px; flex:1; }

    .patient-avatar { position:relative; flex-shrink:0; }
    .patient-avatar img { width:44px; height:44px; border-radius:var(--r-md); object-fit:cover; display:block; }
    .patient-avatar-placeholder { width:44px; height:44px; border-radius:var(--r-md); background:var(--surface-low); display:flex; align-items:center; justify-content:center; }
    .patient-avatar-placeholder svg { width:22px; height:22px; fill:#94a3b8; }
    .status-dot { position:absolute; bottom:-3px; right:-3px; width:12px; height:12px; border-radius:var(--r-full); border:2px solid white; }
    .dot-confirmed { background:#00897b; }
    .dot-pending   { background:var(--primary); }
    .dot-cancelled { background:var(--error); }
    .dot-grey      { background:#94a3b8; }

    .appt-info { display:grid; grid-template-columns:180px 160px 120px 1fr; gap:8px; align-items:center; flex:1; }
    .patient-name { font-weight:700; font-size:14px; color:var(--text); }
    .patient-type { font-size:11px; color:#94a3b8; margin-top:2px; }
    .meta-item { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--text-muted); }
    .meta-item svg { width:15px; height:15px; flex-shrink:0; }
    .meta-item.date svg { color:var(--primary); }
    .meta-item.time svg { color:var(--teal); }

    /* Badges */
    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:var(--r-full); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
    .badge-confirmed { background:var(--teal-bg); color:var(--teal); }
    .badge-pending   { background:rgba(0,77,153,0.10); color:var(--primary); }
    .badge-cancelled { background:var(--error-bg); color:var(--error); }

    /* Actions */
    .appt-actions { display:flex; align-items:center; gap:6px; padding-left:16px; border-left:1px solid var(--border); margin-left:12px; }
    .action-btn { width:34px; height:34px; border:none; border-radius:var(--r-sm); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s; }
    .action-btn svg { width:16px; height:16px; }
    .action-edit { background:rgba(0,77,153,0.07); color:var(--primary); }
    .action-edit:hover { background:var(--primary); color:white; }
    .action-delete { background:rgba(186,26,26,0.07); color:var(--error); }
    .action-delete:hover { background:var(--error); color:white; }

    /* ALERTES */
    .alert { display:flex; align-items:center; gap:10px; padding:13px 18px; border-radius:var(--r-md); font-size:14px; font-weight:500; margin-bottom:20px; }
    .alert svg { width:18px; height:18px; flex-shrink:0; }
    .alert-success { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
    .alert-error   { background:#fee2e2; color:var(--error); border:1px solid #fecaca; }

    /* STATS CARDS */
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
    .stat-card { background:var(--surface); border-radius:var(--r-lg); padding:16px 20px; box-shadow:var(--shadow); display:flex; align-items:center; gap:12px; }
    .stat-icon { width:40px; height:40px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-icon svg { width:20px; height:20px; }
    .stat-icon.blue   { background:var(--primary-light); color:var(--primary); }
    .stat-icon.teal   { background:rgba(132,245,232,0.25); color:var(--teal); }
    .stat-icon.orange { background:#fff7ed; color:#c2410c; }
    .stat-value { font-family:'Manrope',sans-serif; font-size:24px; font-weight:800; line-height:1; }
    .stat-label { font-size:11px; color:var(--text-muted); margin-top:3px; }

    /* FILTRES STATUT */
    .filter-tabs { display:flex; gap:6px; }
    .filter-tab { padding:6px 14px; border-radius:var(--r-full); border:1.5px solid var(--border); background:var(--surface); font-size:12px; font-weight:600; color:var(--text-muted); text-decoration:none; transition:all 0.15s; }
    .filter-tab:hover { border-color:var(--primary); color:var(--primary); }
    .filter-tab.active { background:var(--primary); border-color:var(--primary); color:white; }

    /* MODALE MODIFIER */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal { background:var(--surface); border-radius:var(--r-xl); padding:32px; width:460px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    .modal-title { font-family:'Manrope',sans-serif; font-size:18px; font-weight:800; margin-bottom:6px; }
    .modal-sub { font-size:13px; color:var(--text-muted); margin-bottom:24px; }
    .modal-patient-badge { display:flex; align-items:center; gap:10px; background:var(--primary-light); border-radius:var(--r-md); padding:12px 16px; margin-bottom:24px; }
    .modal-patient-initials { width:36px; height:36px; border-radius:var(--r-full); background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:13px; flex-shrink:0; }
    .modal-patient-name { font-weight:700; font-size:14px; color:var(--primary); }
    .modal-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
    .modal-group { display:flex; flex-direction:column; gap:5px; }
    .modal-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); }
    .modal-input { width:100%; background:var(--surface-low); border:2px solid transparent; border-radius:var(--r-md); padding:11px 14px; font-size:14px; font-family:'Inter',sans-serif; color:var(--text); outline:none; transition:all 0.18s; }
    .modal-input:focus { border-color:var(--teal); background:white; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:24px; }
    .btn-modal-cancel { padding:10px 22px; background:transparent; border:1.5px solid var(--border); color:var(--text-muted); font-family:'Manrope',sans-serif; font-weight:600; font-size:13px; border-radius:var(--r-md); cursor:pointer; transition:all 0.15s; }
    .btn-modal-cancel:hover { border-color:var(--text-muted); }
    .btn-modal-save { display:flex; align-items:center; gap:7px; padding:10px 22px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; box-shadow:0 2px 8px rgba(0,77,153,0.25); transition:all 0.15s; }
    .btn-modal-save:hover { box-shadow:0 4px 16px rgba(0,77,153,0.35); transform:translateY(-1px); }
    .btn-modal-save svg { width:15px; height:15px; }

    /* Load more */
    .load-more-wrap { display:flex; justify-content:center; margin-top:20px; }
    .btn-load-more { padding:11px 28px; background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); color:var(--primary); font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; cursor:pointer; transition:all 0.15s; box-shadow:var(--shadow); }
    .btn-load-more:hover { background:var(--surface-low); }
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

  <!-- Profil docteur — injecté depuis $_SESSION['medecin'] quand la session sera active -->
  <div class="sidebar-profile">
    <div class="profile-avatar">
      <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
    </div>
    <div>
      <?php
        // TODO : remplacer par $_SESSION['medecin']['prenom'] et ['nom'] et ['specialite']
        $doc_nom       = 'Dr. Marc Laurent';
        $doc_specialite = 'Cardiologue';
      ?>
      <span class="profile-name"><?= htmlspecialchars($doc_nom) ?></span>
      <span class="profile-spec"><?= htmlspecialchars($doc_specialite) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="planning.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      My Schedule
    </a>
    <a href="patients.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Patients
    </a>
    <a href="settings.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Settings
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
    <h2 class="topbar-title">Mon Dashboard Praticien</h2>
    <div class="topbar-right">
      <div class="search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Rechercher un dossier..." id="searchRdv" oninput="filterRdv()">
      </div>
      <button class="icon-btn" title="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="icon-btn" title="Aide">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </button>

    </div>
  </header>

  <div class="page-content">

    <?php if ($msg_succes): ?>
    <div class="alert alert-success">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <?= htmlspecialchars($msg_succes) ?>
    </div>
    <?php endif; ?>

    <?php if ($msg_erreur): ?>
    <div class="alert alert-error">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($msg_erreur) ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div><div class="stat-value"><?= $stats['total'] ?></div><div class="stat-label">Total RDV</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div><div class="stat-value"><?= $stats['nb_confirmes'] ?></div><div class="stat-label">Confirmés</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div><div class="stat-value"><?= $stats['nb_attente'] ?></div><div class="stat-label">En attente</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div><div class="stat-value"><?= $stats['nb_aujourdhui'] ?></div><div class="stat-label">Aujourd'hui</div></div>
      </div>
    </div>

    <!-- Bandeau planning du jour -->
    <div class="day-banner">
      <div class="day-banner-left">
        <div class="day-banner-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="day-banner-title">Planning de la journée</div>
          <div class="day-banner-sub">
            <?= $stats['nb_aujourdhui'] ?> consultation(s) prévue(s) aujourd'hui
          </div>
        </div>
      </div>
      <a href="planning.php" class="btn-planning-link">
        Accéder au planning complet
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>

    <!-- Section RDV + filtres -->
    <div class="section-controls">
      <h2 class="section-title">Rendez-vous (<?= count($rendez_vous) ?>)</h2>
      <div class="filter-group">
        <div class="filter-tabs">
          <a href="dashboard.php"                   class="filter-tab <?= !$filtre          ? 'active':'' ?>">Tous</a>
          <a href="dashboard.php?statut=en_attente" class="filter-tab <?= $filtre==='en_attente' ? 'active':'' ?>">En attente</a>
          <a href="dashboard.php?statut=confirme"   class="filter-tab <?= $filtre==='confirme'   ? 'active':'' ?>">Confirmés</a>
          <a href="dashboard.php?statut=annule"     class="filter-tab <?= $filtre==='annule'     ? 'active':'' ?>">Annulés</a>
        </div>
        <div class="search-bar" style="margin-left:10px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" placeholder="Rechercher un patient..." id="searchRdv" oninput="filterRdv()">
        </div>
      </div>
    </div>

    <!-- Liste des RDV depuis la BDD -->
    <div class="appointments-list" id="rdvList">

      <?php if (empty($rendez_vous)): ?>
        <div style="text-align:center;padding:50px 20px;color:var(--text-muted);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.3;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <p style="font-family:'Manrope',sans-serif;font-weight:700;font-size:16px;margin-bottom:6px;">Aucun rendez-vous trouvé</p>
          <p style="font-size:13px;">Les RDV pris par les patients apparaîtront ici.</p>
        </div>

      <?php else: ?>
        <?php foreach ($rendez_vous as $rdv):
          // Initiales pour l'avatar
          $initiales = strtoupper(
            substr($rdv['patient_prenom'], 0, 1) . substr($rdv['patient_nom'], 0, 1)
          );
          // Formatage date et heure
          $date_fr  = date('d M, Y', strtotime($rdv['date_rdv']));
          $heure_fr = date('H:i', strtotime($rdv['heure_rdv']));

          // Classe du point de statut
          $dot_class = [
            'confirme'   => 'dot-confirmed',
            'en_attente' => 'dot-pending',
            'annule'     => 'dot-cancelled',
          ][$rdv['statut']] ?? 'dot-grey';

          // Classe du badge
          $badge_class = [
            'confirme'   => 'badge-confirmed',
            'en_attente' => 'badge-pending',
            'annule'     => 'badge-cancelled',
          ][$rdv['statut']] ?? '';

          // Label du badge
          $badge_label = [
            'confirme'   => 'Confirmé',
            'en_attente' => 'En attente',
            'annule'     => 'Annulé',
          ][$rdv['statut']] ?? $rdv['statut'];

          // Données JSON pour la modale modifier (passées en data-attr)
          $data = htmlspecialchars(json_encode([
            'id'      => $rdv['id'],
            'prenom'  => $rdv['patient_prenom'],
            'nom'     => $rdv['patient_nom'],
            'date'    => $rdv['date_rdv'],
            'heure'   => substr($rdv['heure_rdv'], 0, 5),
            'statut'  => $rdv['statut'],
          ]));
        ?>

        <div class="appt-row" data-patient="<?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?>">
          <div class="appt-left">

            <!-- Avatar avec initiales -->
            <div class="patient-avatar">
              <div class="patient-avatar-placeholder" style="display:flex;background:var(--primary-light);color:var(--primary);font-family:'Manrope',sans-serif;font-weight:800;font-size:14px;">
                <?= $initiales ?>
              </div>
              <div class="status-dot <?= $dot_class ?>"></div>
            </div>

            <!-- Infos RDV -->
            <div class="appt-info">
              <div>
                <div class="patient-name"><?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?></div>
                <div class="patient-type">CIN : <?= htmlspecialchars($rdv['cin']) ?> &bull; <?= ucfirst($rdv['genre']) ?></div>
              </div>
              <div class="meta-item date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= $date_fr ?>
              </div>
              <div class="meta-item time">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= $heure_fr ?>
              </div>
              <span class="badge <?= $badge_class ?>"><?= $badge_label ?></span>
            </div>
          </div>

          <!-- Boutons actions -->
          <div class="appt-actions">
            <!-- Modifier → ouvre la modale -->
            <button class="action-btn action-edit" title="Modifier"
                    onclick="ouvrirModale(<?= $data ?>)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <!-- Supprimer → confirmation puis GET -->
            <a href="dashboard.php?supprimer=<?= $rdv['id'] ?>"
               class="action-btn action-delete" title="Supprimer"
               onclick="return confirm('Supprimer le RDV de <?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?> ?')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </a>
          </div>
        </div>

        <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- /appointments-list -->

  </div>
</div><!-- /main -->

<!-- ===== MODALE MODIFIER ===== -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <h3 class="modal-title">Modifier le Rendez-vous</h3>
    <p class="modal-sub">Modifiez la date, l'heure ou le statut.</p>

    <!-- Badge patient -->
    <div class="modal-patient-badge">
      <div class="modal-patient-initials" id="modalInitiales"></div>
      <div class="modal-patient-name" id="modalNomPatient"></div>
    </div>

    <form method="POST" action="dashboard.php">
      <input type="hidden" name="action" value="modifier">
      <input type="hidden" name="rdv_id" id="modalRdvId">

      <div class="modal-row">
        <div class="modal-group">
          <label class="modal-label">Date</label>
          <input class="modal-input" type="date" name="date_rdv" id="modalDate"
                 min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="modal-group">
          <label class="modal-label">Heure</label>
          <input class="modal-input" type="time" name="heure_rdv" id="modalHeure" required>
        </div>
      </div>

      <div class="modal-group">
        <label class="modal-label">Statut</label>
        <select class="modal-input" name="statut" id="modalStatut">
          <option value="en_attente">En attente</option>
          <option value="confirme">Confirmé</option>
          <option value="annule">Annulé</option>
        </select>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="fermerModale()">Annuler</button>
        <button type="submit" class="btn-modal-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // ---- Recherche live ----
  function filterRdv() {
    const q = document.getElementById('searchRdv').value.toLowerCase();
    document.querySelectorAll('.appt-row').forEach(row => {
      row.style.display = row.dataset.patient.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  // ---- Modale modifier ----
  function ouvrirModale(rdv) {
    // Remplir les champs avec les données du RDV cliqué
    document.getElementById('modalRdvId').value   = rdv.id;
    document.getElementById('modalDate').value    = rdv.date;
    document.getElementById('modalHeure').value   = rdv.heure;
    document.getElementById('modalStatut').value  = rdv.statut;

    // Badge patient
    const initiales = (rdv.prenom[0] + rdv.nom[0]).toUpperCase();
    document.getElementById('modalInitiales').textContent  = initiales;
    document.getElementById('modalNomPatient').textContent = rdv.prenom + ' ' + rdv.nom;

    document.getElementById('modalOverlay').classList.add('open');
  }

  function fermerModale() {
    document.getElementById('modalOverlay').classList.remove('open');
  }

  // Fermer si clic en dehors de la modale
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) fermerModale();
  });
</script>
</body>
</html>