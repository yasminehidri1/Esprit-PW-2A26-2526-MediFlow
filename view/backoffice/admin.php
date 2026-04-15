<?php
// ============================================================
//  admin_dashboard.php — View (administrateur)
//  Affiche tous les RDV groupés par médecin
//  À placer dans : view/backoffice/admin/
// ============================================================

require_once __DIR__ . '/../../controller/RendezVousController.php';

$controller = new RendezVousController();

// Filtres GET
$filtre_statut  = isset($_GET['statut'])  && in_array($_GET['statut'], ['en_attente','confirme','annule'])
                  ? $_GET['statut'] : '';
$filtre_medecin = isset($_GET['medecin']) ? intval($_GET['medecin']) : 0;
$recherche      = isset($_GET['q'])       ? trim($_GET['q'])         : '';

// Données via Controller → Model
$data         = $controller->getAdminDashboardData($filtre_statut, $filtre_medecin, $recherche);
$rdvs_grouped = $data['rdvs_grouped'];
$stats        = $data['stats'];
$medecins     = $data['medecins'];

// Total RDV affiché (après filtres)
$total_affiche = 0;
foreach ($rdvs_grouped as $groupe) {
    $total_affiche += count($groupe['rdvs']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — MediFlow</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root {
      --primary:       #004d99;
      --primary-dark:  #1565c0;
      --primary-light: #d6e3ff;
      --teal:          #005851;
      --teal-light:    #84f5e8;
      --teal-bg:       rgba(0,88,81,0.10);
      --amber:         #b45309;
      --amber-bg:      #fef3c7;
      --green:         #15803d;
      --green-bg:      #dcfce7;
      --red:           #ba1a1a;
      --red-bg:        #ffdad6;
      --bg:            #f0f4f8;
      --surface:       #ffffff;
      --surface-low:   #f5f7fa;
      --border:        #e2e8f0;
      --text:          #0f172a;
      --text-muted:    #64748b;
      --shadow:        0 2px 16px rgba(0,77,153,0.08);
      --shadow-hover:  0 8px 32px rgba(0,77,153,0.15);
      --sidebar-w:     220px;
      --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
    }

    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

    /* ===== SIDEBAR ===== */
    .sidebar { width:var(--sidebar-w); min-height:100vh; position:fixed; top:0; left:0; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; padding:20px 12px; z-index:100; }
    .sidebar-brand { display:flex; align-items:center; gap:10px; padding:6px 8px 16px; border-bottom:1px solid var(--border); margin-bottom:12px; }
    .brand-logo { width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .brand-logo svg { width:20px; height:20px; fill:white; }
    .brand-text .name { font-family:'Manrope',sans-serif; font-weight:800; font-size:15px; color:#1e3a6e; display:block; line-height:1.1; }
    .brand-text .sub { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--text-muted); display:block; }

    .sidebar-admin-badge { display:flex; align-items:center; gap:8px; padding:10px; background:linear-gradient(135deg,var(--primary-light),rgba(0,88,81,0.1)); border-radius:var(--r-md); margin-bottom:10px; border:1px solid rgba(0,77,153,0.15); }
    .admin-icon { width:36px; height:36px; border-radius:var(--r-md); background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .admin-icon svg { width:18px; height:18px; fill:white; }
    .admin-label { font-family:'Manrope',sans-serif; font-weight:700; font-size:12px; color:var(--primary); display:block; }
    .admin-sub { font-size:10px; color:var(--text-muted); display:block; }

    .sidebar-nav { display:flex; flex-direction:column; gap:2px; flex:1; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--r-md); color:var(--text-muted); font-size:14px; font-weight:500; text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item svg { width:18px; height:18px; flex-shrink:0; }
    .nav-item:hover { background:rgba(0,77,153,0.05); color:var(--primary); }
    .nav-item.active { background:var(--surface-low); color:var(--primary); font-weight:700; border-left-color:var(--teal); box-shadow:var(--shadow); }
    .sidebar-footer { padding-top:12px; border-top:1px solid var(--border); }

    /* ===== MAIN ===== */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

    /* ===== TOPBAR ===== */
    .topbar { height:64px; background:rgba(255,255,255,0.92); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:50; }
    .topbar-left { display:flex; align-items:center; gap:12px; }
    .topbar-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:20px; background:linear-gradient(135deg,#1e3a6e,var(--primary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .topbar-tag { padding:3px 10px; background:var(--primary-light); color:var(--primary); border-radius:var(--r-full); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; }
    .topbar-right { display:flex; align-items:center; gap:8px; }

    /* Barre de recherche */
    .search-bar { display:flex; align-items:center; background:var(--surface-low); border-radius:var(--r-full); padding:7px 14px; gap:7px; width:240px; border:1px solid var(--border); }
    .search-bar svg { width:15px; height:15px; color:var(--text-muted); flex-shrink:0; }
    .search-bar input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text); width:100%; font-family:'Inter',sans-serif; }
    .search-bar input::placeholder { color:#94a3b8; }

    /* ===== CONTENU PAGE ===== */
    .page-content { padding:24px 28px; flex:1; }

    /* ===== STATS CARDS ===== */
    .stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:28px; }
    .stat-card { background:var(--surface); border-radius:var(--r-lg); padding:18px 16px; box-shadow:var(--shadow); border:1px solid transparent; transition:all 0.2s; }
    .stat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-hover); }
    .stat-icon { width:38px; height:38px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; margin-bottom:10px; }
    .stat-icon svg { width:19px; height:19px; }
    .stat-icon.blue  { background:var(--primary-light); color:var(--primary); }
    .stat-icon.green { background:var(--green-bg);      color:var(--green); }
    .stat-icon.amber { background:var(--amber-bg);      color:var(--amber); }
    .stat-icon.red   { background:var(--red-bg);        color:var(--red); }
    .stat-icon.teal  { background:var(--teal-bg);       color:var(--teal); }
    .stat-value { font-family:'Manrope',sans-serif; font-size:28px; font-weight:800; line-height:1; margin-bottom:4px; }
    .stat-label { font-size:12px; color:var(--text-muted); font-weight:500; }

    /* ===== FILTRES ===== */
    .filters-bar { background:var(--surface); border-radius:var(--r-lg); padding:16px 20px; box-shadow:var(--shadow); display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:22px; }
    .filter-label { font-size:13px; font-weight:600; color:var(--text-muted); white-space:nowrap; }
    .filter-select { padding:7px 12px; border:1px solid var(--border); border-radius:var(--r-md); font-size:13px; font-family:'Inter',sans-serif; color:var(--text); background:var(--surface-low); outline:none; cursor:pointer; }
    .filter-select:focus { border-color:var(--primary); }
    .btn-filter { display:flex; align-items:center; gap:6px; padding:8px 16px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; cursor:pointer; transition:all 0.15s; }
    .btn-filter:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,77,153,0.3); }
    .btn-filter svg { width:14px; height:14px; }
    .btn-reset { padding:8px 14px; background:var(--surface-low); border:1px solid var(--border); border-radius:var(--r-md); font-size:13px; color:var(--text-muted); cursor:pointer; font-family:'Inter',sans-serif; transition:background 0.15s; text-decoration:none; display:flex; align-items:center; gap:5px; }
    .btn-reset:hover { background:var(--border); color:var(--text); }
    .filter-count { margin-left:auto; font-size:13px; color:var(--text-muted); white-space:nowrap; }
    .filter-count strong { color:var(--primary); }

    /* ===== MESSAGES FLASH ===== */
    .flash { padding:12px 18px; border-radius:var(--r-md); font-size:14px; font-weight:500; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
    .flash svg { width:16px; height:16px; flex-shrink:0; }
    .flash.succes { background:var(--green-bg); color:var(--green); }
    .flash.erreur { background:var(--red-bg);   color:var(--red); }

    /* ===== GROUPE MÉDECIN ===== */
    .medecin-group { margin-bottom:28px; }
    .medecin-header { display:flex; align-items:center; gap:14px; padding:14px 20px; background:var(--surface); border-radius:var(--r-lg) var(--r-lg) 0 0; border:1px solid var(--border); border-bottom:2px solid var(--primary); }
    .medecin-avatar { width:44px; height:44px; border-radius:var(--r-full); background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Manrope',sans-serif; font-weight:800; font-size:16px; color:white; }
    .medecin-name { font-family:'Manrope',sans-serif; font-weight:800; font-size:16px; color:var(--text); }
    .medecin-spec { font-size:12px; color:var(--text-muted); margin-top:2px; }
    .medecin-count { margin-left:auto; display:flex; align-items:center; gap:6px; }
    .count-badge { padding:4px 12px; border-radius:var(--r-full); font-size:12px; font-weight:700; font-family:'Manrope',sans-serif; }
    .count-badge.total   { background:var(--primary-light); color:var(--primary); }
    .count-badge.attente { background:var(--amber-bg);      color:var(--amber); }

    /* ===== TABLE ===== */
    .rdv-table { width:100%; border-collapse:collapse; background:var(--surface); border:1px solid var(--border); border-top:none; border-radius:0 0 var(--r-lg) var(--r-lg); overflow:hidden; }
    .rdv-table thead { background:var(--surface-low); }
    .rdv-table th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); border-bottom:1px solid var(--border); white-space:nowrap; }
    .rdv-table td { padding:13px 16px; font-size:14px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .rdv-table tr:last-child td { border-bottom:none; }
    .rdv-table tbody tr { transition:background 0.15s; }
    .rdv-table tbody tr:hover { background:rgba(0,77,153,0.03); }

    /* Initiales patient */
    .patient-cell { display:flex; align-items:center; gap:10px; }
    .patient-init { width:36px; height:36px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:13px; flex-shrink:0; }
    .patient-init.homme { background:var(--primary-light); color:var(--primary); }
    .patient-init.femme { background:rgba(124,58,237,0.12); color:#7c3aed; }
    .patient-name  { font-weight:600; font-size:14px; }
    .patient-cin   { font-size:11px; color:var(--text-muted); margin-top:1px; }

    /* Badges statut */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:var(--r-full); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; }
    .badge.en_attente { background:var(--amber-bg); color:var(--amber); }
    .badge.en_attente::before { background:var(--amber); }
    .badge.confirme   { background:var(--green-bg); color:var(--green); }
    .badge.confirme::before   { background:var(--green); }
    .badge.annule     { background:var(--red-bg);   color:var(--red); }
    .badge.annule::before     { background:var(--red); }

    /* Date / Heure */
    .date-cell { display:flex; align-items:center; gap:6px; color:var(--text); font-weight:500; }
    .date-cell svg { width:14px; height:14px; color:var(--text-muted); flex-shrink:0; }
    .heure-cell { display:flex; align-items:center; gap:6px; color:var(--text); }
    .heure-cell svg { width:14px; height:14px; color:var(--text-muted); flex-shrink:0; }

    /* Vide */
    .empty-group { padding:32px; text-align:center; color:var(--text-muted); font-size:14px; background:var(--surface); border:1px solid var(--border); border-top:none; border-radius:0 0 var(--r-lg) var(--r-lg); }
    .empty-main { padding:60px 20px; text-align:center; background:var(--surface); border-radius:var(--r-xl); box-shadow:var(--shadow); }
    .empty-main svg { width:56px; height:56px; color:var(--border); margin-bottom:16px; }
    .empty-main h3 { font-family:'Manrope',sans-serif; font-size:18px; font-weight:700; margin-bottom:6px; }
    .empty-main p  { font-size:14px; color:var(--text-muted); }

    /* ===== TOGGLE GROUPES ===== */
    .medecin-header { cursor:pointer; user-select:none; }
    .toggle-icon { width:22px; height:22px; border-radius:var(--r-sm); background:var(--surface-low); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; transition:transform 0.2s; flex-shrink:0; }
    .toggle-icon svg { width:12px; height:12px; color:var(--text-muted); transition:transform 0.2s; }
    .medecin-group.collapsed .toggle-icon svg { transform:rotate(-90deg); }
    .medecin-group.collapsed .rdv-table-wrapper { display:none; }

    /* responsive */
    @media (max-width:1100px) {
      .stats-grid { grid-template-columns:repeat(3,1fr); }
    }
    @media (max-width:800px) {
      .sidebar { display:none; }
      .main { margin-left:0; }
      .stats-grid { grid-template-columns:repeat(2,1fr); }
    }
  </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 3a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H8a1 1 0 110-2h3V7a1 1 0 011-1z"/></svg>
    </div>
    <div class="brand-text">
      <span class="name">MediFlow</span>
      <span class="sub">Pro Platform</span>
    </div>
  </div>

  <div class="sidebar-admin-badge">
    <div class="admin-icon">
      <svg viewBox="0 0 24 24"><path d="M12 1l3 6 7 1-5 5 1 7-6-3-6 3 1-7L2 8l7-1z"/></svg>
    </div>
    <div>
      <span class="admin-label">Administrateur</span>
      <span class="admin-sub">Accès complet</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Gestion utilisateurs
    </a>
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      Consulter commandes
    </a>
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Consulter patients
    </a>
    <a href="admin.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Consulter RDV
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="#" class="nav-item" style="color:#ba1a1a;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Déconnexion
    </a>
  </div>
</aside>

<!-- ===== MAIN ===== -->
<div class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <span class="topbar-title">Tableau de bord</span>
      <span class="topbar-tag">Admin</span>
    </div>
    <div class="topbar-right">
      <!-- Recherche rapide -->
      <form method="GET" action="admin_dashboard.php" style="display:contents;">
        <?php if ($filtre_statut):  ?><input type="hidden" name="statut"  value="<?= htmlspecialchars($filtre_statut) ?>"><?php endif; ?>
        <?php if ($filtre_medecin): ?><input type="hidden" name="medecin" value="<?= $filtre_medecin ?>"><?php endif; ?>
        <div class="search-bar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="searchLive" name="q" placeholder="Nom, prénom, CIN…" value="<?= htmlspecialchars($recherche) ?>" oninput="filterLive(this.value)">
        </div>
      </form>
    </div>
  </div>

  <div class="page-content">

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-value"><?= intval($stats['total'] ?? 0) ?></div>
        <div class="stat-label">Total RDV</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-value"><?= intval($stats['nb_attente'] ?? 0) ?></div>
        <div class="stat-label">En attente</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-value"><?= intval($stats['nb_confirmes'] ?? 0) ?></div>
        <div class="stat-label">Confirmés</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="stat-value"><?= intval($stats['nb_annules'] ?? 0) ?></div>
        <div class="stat-label">Annulés</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-value"><?= intval($stats['nb_medecins'] ?? 0) ?></div>
        <div class="stat-label">Médecins actifs</div>
      </div>
    </div>

    <!-- FILTRES -->
    <form method="GET" action="admin_dashboard.php">
      <div class="filters-bar">
        <span class="filter-label">Filtrer par :</span>

        <!-- Filtre statut -->
        <select name="statut" class="filter-select">
          <option value="">Tous les statuts</option>
          <option value="en_attente" <?= $filtre_statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
          <option value="confirme"   <?= $filtre_statut === 'confirme'   ? 'selected' : '' ?>>Confirmé</option>
          <option value="annule"     <?= $filtre_statut === 'annule'     ? 'selected' : '' ?>>Annulé</option>
        </select>

        <!-- Filtre médecin -->
        <select name="medecin" class="filter-select">
          <option value="0">Tous les médecins</option>
          <?php foreach ($medecins as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $filtre_medecin === $m['id'] ? 'selected' : '' ?>>
            Dr. <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?>
          </option>
          <?php endforeach; ?>
        </select>

        <!-- Recherche -->
        <input type="text" name="q" class="filter-select" placeholder="Nom, prénom, CIN…"
               value="<?= htmlspecialchars($recherche) ?>" style="width:180px;">

        <button type="submit" class="btn-filter">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Rechercher
        </button>

        <?php if ($filtre_statut || $filtre_medecin || $recherche): ?>
        <a href="admin_dashboard.php" class="btn-reset">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Réinitialiser
        </a>
        <?php endif; ?>

        <span class="filter-count"><strong><?= $total_affiche ?></strong> RDV affichés</span>
      </div>
    </form>

    <!-- ===== LISTE PAR MÉDECIN ===== -->
    <?php if (empty($rdvs_grouped)): ?>

      <div class="empty-main">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <h3>Aucun rendez-vous trouvé</h3>
        <p>Essayez de modifier vos filtres de recherche.</p>
      </div>

    <?php else: ?>

      <?php foreach ($rdvs_grouped as $mid => $groupe):
        $info     = $groupe['info'];
        $rdvs     = $groupe['rdvs'];
        $nb_total = count($rdvs);
        $nb_att   = count(array_filter($rdvs, fn($r) => $r['statut'] === 'en_attente'));

        // Initiales du médecin
        $init_med = strtoupper(
            substr($info['prenom'] ?? '', 0, 1) .
            substr($info['nom']    ?? '', 0, 1)
        ) ?: '#';
      ?>

      <div class="medecin-group" id="group-<?= $mid ?>">

        <!-- En-tête médecin (cliquable pour replier) -->
        <div class="medecin-header" onclick="toggleGroupe(<?= $mid ?>)">
          <div class="medecin-avatar"><?= $init_med ?></div>
          <div>
            <div class="medecin-name">Dr. <?= htmlspecialchars($info['prenom'] . ' ' . $info['nom']) ?></div>
          </div>
          <div class="medecin-count">
            <span class="count-badge total"><?= $nb_total ?> RDV</span>
            <?php if ($nb_att > 0): ?>
            <span class="count-badge attente"><?= $nb_att ?> en attente</span>
            <?php endif; ?>
          </div>
          <div class="toggle-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>

        <!-- Table des RDV -->
        <div class="rdv-table-wrapper">
          <?php if (empty($rdvs)): ?>
            <div class="empty-group">Aucun rendez-vous pour ce médecin.</div>
          <?php else: ?>
          <table class="rdv-table">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Genre</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rdvs as $rdv):
                $initiales = strtoupper(
                    substr($rdv['patient_prenom'] ?? '', 0, 1) .
                    substr($rdv['patient_nom']    ?? '', 0, 1)
                );
                $genre_class = ($rdv['genre'] ?? '') === 'femme' ? 'femme' : 'homme';

                // Formatage date
                $date_fr = '—';
                if (!empty($rdv['date_rdv'])) {
                    $jours_fr = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
                    $mois_fr  = ['','Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
                    $ts       = strtotime($rdv['date_rdv']);
                    $date_fr  = $jours_fr[date('N',$ts)-1] . ' ' . date('d',$ts) . ' ' . $mois_fr[(int)date('m',$ts)] . ' ' . date('Y',$ts);
                }

                $heure_fr  = !empty($rdv['heure_rdv'])  ? date('H:i', strtotime($rdv['heure_rdv'])) : '—';

                $badge_labels = ['en_attente'=>'En attente','confirme'=>'Confirmé','annule'=>'Annulé'];
                $badge_label  = $badge_labels[$rdv['statut']] ?? $rdv['statut'];

              ?>
              <tr class="rdv-row" data-patient="<?= strtolower($rdv['patient_prenom'].' '.$rdv['patient_nom'].' '.$rdv['cin']) ?>">
                <!-- Patient -->
                <td>
                  <div class="patient-cell">
                    <div class="patient-init <?= $genre_class ?>"><?= $initiales ?></div>
                    <div>
                      <div class="patient-name"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></div>
                      <div class="patient-cin">CIN : <?= htmlspecialchars($rdv['cin']) ?></div>
                    </div>
                  </div>
                </td>
                <!-- Genre -->
                <td style="color:var(--text-muted);font-size:13px;"><?= ucfirst($rdv['genre'] ?? '—') ?></td>
                <!-- Date -->
                <td>
                  <div class="date-cell">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= $date_fr ?>
                  </div>
                </td>
                <!-- Heure -->
                <td>
                  <div class="heure-cell">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?= $heure_fr ?>
                  </div>
                </td>
                <!-- Badge statut -->
                <td>
                  <span class="badge <?= htmlspecialchars($rdv['statut']) ?>"><?= $badge_label ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div><!-- /rdv-table-wrapper -->

      </div><!-- /medecin-group -->

      <?php endforeach; ?>

    <?php endif; ?>

  </div><!-- /page-content -->
</div><!-- /main -->

<script>
// ---- Replier / déplier un groupe médecin ----
function toggleGroupe(mid) {
  const groupe = document.getElementById('group-' + mid);
  if (groupe) groupe.classList.toggle('collapsed');
}

// ---- Recherche live côté client ----
function filterLive(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.rdv-row').forEach(row => {
    row.style.display = row.dataset.patient.includes(q) ? '' : 'none';
  });
  // Masquer les groupes entièrement vides après filtre
  document.querySelectorAll('.medecin-group').forEach(groupe => {
    const visible = groupe.querySelectorAll('.rdv-row:not([style*="none"])').length;
    groupe.style.display = visible === 0 ? 'none' : '';
  });
}

// Synchroniser la searchbar topbar avec filterLive
document.getElementById('searchLive').addEventListener('input', function() {
  filterLive(this.value);
});
</script>
</body>
</html>