<?php
// ============================================================
//  admin.php — Vue administrateur
//  Vue principale : liste des médecins avec nb de consultations
//  Clic œil : modale avec liste des RDV du médecin
//  À placer dans : view/backoffice/
// ============================================================

require_once __DIR__ . '/../../config.php';

$pdo = config::getConnexion();

// ---- Récupérer tous les médecins (id_role = 2) avec nb de RDV ----
$stmt = $pdo->query(
    "SELECT
        u.id_PK,
        u.nom,
        u.prenom,
        u.mail,
        u.tel,
        r.libelle        AS role,
        COUNT(rv.id)     AS nb_consultations
     FROM utilisateurs u
     LEFT JOIN roles r        ON r.id_role    = u.id_role
     LEFT JOIN rendez_vous rv ON rv.medecin_id = u.id_PK
     WHERE u.id_role = 2
     GROUP BY u.id_PK, u.nom, u.prenom, u.mail, u.tel, r.libelle
     ORDER BY u.nom ASC"
);
$medecins = $stmt->fetchAll();

// ---- Stats globales ----
$stats = $pdo->query(
    "SELECT
        COUNT(*)                   AS total,
        SUM(statut='confirme')     AS nb_confirmes,
        SUM(statut='en_attente')   AS nb_attente,
        SUM(statut='annule')       AS nb_annules,
        COUNT(DISTINCT medecin_id) AS nb_medecins
     FROM rendez_vous"
)->fetch();

// ---- RDV de chaque médecin (pour la modale) ----
// On les charge tous en PHP et on les passe en JSON au JS
$stmt2 = $pdo->query(
    "SELECT rv.*,
            CONCAT(u.prenom,' ',u.nom) AS medecin_nom_complet
     FROM rendez_vous rv
     LEFT JOIN utilisateurs u ON u.id_PK = rv.medecin_id
     ORDER BY rv.medecin_id ASC, rv.date_rdv ASC, rv.heure_rdv ASC"
);
$tous_rdvs = $stmt2->fetchAll();

// Grouper par medecin_id en JSON pour le JS
$rdvs_par_medecin = [];
foreach ($tous_rdvs as $rdv) {
    $mid = $rdv['medecin_id'];
    if (!isset($rdvs_par_medecin[$mid])) $rdvs_par_medecin[$mid] = [];
    $rdvs_par_medecin[$mid][] = [
        'id'       => $rdv['id'],
        'prenom'   => $rdv['patient_prenom'],
        'nom'      => $rdv['patient_nom'],
        'cin'      => $rdv['cin'],
        'genre'    => $rdv['genre'],
        'date'     => $rdv['date_rdv'],
        'heure'    => substr($rdv['heure_rdv'], 0, 5),
        'statut'   => $rdv['statut'],
    ];
}

$nb_medecins = count($medecins);

// Couleurs avatar (rotation)
$avatar_colors = ['#2563eb','#0d9488','#7c3aed','#db2777','#ea580c','#16a34a','#dc2626'];
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
    .brand-text .sub  { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--text-muted); display:block; }
    .sidebar-admin-badge { display:flex; align-items:center; gap:8px; padding:10px; background:linear-gradient(135deg,var(--primary-light),rgba(0,88,81,0.08)); border-radius:var(--r-md); margin-bottom:10px; border:1px solid rgba(0,77,153,0.12); }
    .admin-icon { width:34px; height:34px; border-radius:var(--r-md); background:linear-gradient(135deg,var(--primary),var(--primary-dark)); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .admin-icon svg { width:16px; height:16px; fill:white; }
    .admin-label { font-family:'Manrope',sans-serif; font-weight:700; font-size:12px; color:var(--primary); display:block; }
    .admin-sub { font-size:10px; color:var(--text-muted); display:block; }
    .sidebar-nav { display:flex; flex-direction:column; gap:2px; flex:1; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--r-md); color:var(--text-muted); font-size:13px; font-weight:500; text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item svg { width:17px; height:17px; flex-shrink:0; }
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

    /* ===== CONTENU ===== */
    .page-content { padding:24px 28px; flex:1; }

    /* ===== STATS ===== */
    .stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:28px; }
    .stat-card { background:var(--surface); border-radius:var(--r-lg); padding:18px 16px; box-shadow:var(--shadow); transition:all 0.2s; }
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

    /* ===== SECTION TITRE ===== */
    .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .section-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:17px; }
    .section-count { font-size:13px; color:var(--text-muted); }
    .section-count strong { color:var(--primary); }

    /* ===== TABLE MÉDECINS ===== */
    .doctors-table-wrap { background:var(--surface); border-radius:var(--r-xl); box-shadow:var(--shadow); overflow:hidden; }
    .doctors-table { width:100%; border-collapse:collapse; }
    .doctors-table thead { background:var(--surface-low); border-bottom:1px solid var(--border); }
    .doctors-table th { padding:12px 20px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); white-space:nowrap; }
    .doctors-table td { padding:16px 20px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .doctors-table tbody tr:last-child td { border-bottom:none; }
    .doctors-table tbody tr { transition:background 0.15s; }
    .doctors-table tbody tr:hover { background:rgba(0,77,153,0.02); }

    /* Cellule médecin */
    .doc-cell { display:flex; align-items:center; gap:14px; }
    .doc-avatar { width:42px; height:42px; border-radius:var(--r-full); display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:15px; color:white; flex-shrink:0; }
    .doc-name  { font-weight:700; font-size:14px; }
    .doc-id    { font-size:11px; color:var(--text-muted); margin-top:1px; }

    /* Email / Tel */
    .cell-email { font-size:13px; color:var(--text-muted); }
    .cell-tel   { font-size:13px; color:var(--text-muted); }

    /* Role badge */
    .role-badge { display:inline-block; padding:4px 12px; background:var(--primary-light); color:var(--primary); border-radius:var(--r-full); font-size:12px; font-weight:600; }

    /* Consultations */
    .consult-num { font-family:'Manrope',sans-serif; font-weight:800; font-size:16px; color:var(--primary); }
    .consult-num.zero { color:var(--text-muted); font-weight:600; }

    /* Bouton œil */
    .btn-eye { width:36px; height:36px; border:none; background:transparent; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s; color:var(--text-muted); }
    .btn-eye:hover { background:var(--primary-light); color:var(--primary); }
    .btn-eye svg { width:18px; height:18px; }

    /* ===== MODALE RDV ===== */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:300; align-items:flex-start; justify-content:center; padding:40px 16px; backdrop-filter:blur(4px); overflow-y:auto; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:var(--surface); border-radius:var(--r-xl); width:100%; max-width:820px; box-shadow:0 24px 60px rgba(0,0,0,0.2); animation:slideUp 0.22s ease; overflow:hidden; margin:auto; }
    @keyframes slideUp { from { transform:translateY(16px); opacity:0; } to { transform:translateY(0); opacity:1; } }

    /* Header modale */
    .modal-head { background:linear-gradient(135deg,#1e3a6e,var(--primary)); padding:22px 28px; display:flex; align-items:center; justify-content:space-between; }
    .modal-head-left { display:flex; align-items:center; gap:14px; }
    .modal-avatar { width:46px; height:46px; border-radius:var(--r-full); background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:17px; color:white; flex-shrink:0; border:2px solid rgba(255,255,255,0.3); }
    .modal-head-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:17px; color:white; }
    .modal-head-sub { font-size:12px; color:rgba(255,255,255,0.7); margin-top:2px; }
    .modal-close { width:32px; height:32px; border:none; background:rgba(255,255,255,0.15); border-radius:var(--r-sm); display:flex; align-items:center; justify-content:center; cursor:pointer; color:white; transition:background 0.15s; }
    .modal-close:hover { background:rgba(255,255,255,0.3); }
    .modal-close svg { width:16px; height:16px; }

    /* Corps modale */
    .modal-body { padding:24px 28px; }

    /* Table RDV dans la modale */
    .rdv-table { width:100%; border-collapse:collapse; }
    .rdv-table thead { background:var(--surface-low); }
    .rdv-table th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); border-bottom:1px solid var(--border); }
    .rdv-table td { padding:13px 16px; font-size:13px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .rdv-table tbody tr:last-child td { border-bottom:none; }
    .rdv-table tbody tr:hover { background:rgba(0,77,153,0.02); }

    /* Patient cell dans modale */
    .pat-cell { display:flex; align-items:center; gap:10px; }
    .pat-init { width:34px; height:34px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:12px; flex-shrink:0; }
    .pat-init.homme { background:var(--primary-light); color:var(--primary); }
    .pat-init.femme { background:rgba(124,58,237,0.12); color:#7c3aed; }
    .pat-name { font-weight:600; font-size:13px; }
    .pat-cin  { font-size:11px; color:var(--text-muted); }

    /* Date / heure dans modale */
    .date-cell { display:flex; align-items:center; gap:5px; color:var(--text); }
    .date-cell svg { width:13px; height:13px; color:var(--text-muted); }
    .heure-cell { display:flex; align-items:center; gap:5px; }
    .heure-cell svg { width:13px; height:13px; color:var(--text-muted); }

    /* Badges statut */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:var(--r-full); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; }
    .badge.en_attente { background:var(--amber-bg); color:var(--amber); }
    .badge.en_attente::before { background:var(--amber); }
    .badge.confirme   { background:var(--green-bg); color:var(--green); }
    .badge.confirme::before   { background:var(--green); }
    .badge.annule     { background:var(--red-bg);   color:var(--red); }
    .badge.annule::before     { background:var(--red); }

    /* Vide */
    .empty-rdv { padding:40px; text-align:center; color:var(--text-muted); }
    .empty-rdv svg { width:40px; height:40px; color:var(--border); margin-bottom:10px; }

    @media (max-width:1100px) { .stats-grid { grid-template-columns:repeat(3,1fr); } }
    @media (max-width:800px)  { .sidebar { display:none; } .main { margin-left:0; } .stats-grid { grid-template-columns:repeat(2,1fr); } }
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
        <div class="stat-value"><?= $nb_medecins ?></div>
        <div class="stat-label">Médecins</div>
      </div>
    </div>

    <!-- SECTION MÉDECINS -->
    <div class="section-header">
      <div class="section-title">Liste des Médecins</div>
      <div class="section-count"><strong><?= $nb_medecins ?></strong> total</div>
    </div>

    <div class="doctors-table-wrap">
      <table class="doctors-table">
        <thead>
          <tr>
            <th>Médecin</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Spécialité</th>
            <th>Consultations</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medecins as $i => $med):
            $init   = strtoupper(substr($med['prenom'],0,1) . substr($med['nom'],0,1));
            $color  = $avatar_colors[$i % count($avatar_colors)];
            $id_fmt = 'DR-' . str_pad($med['id_PK'], 4, '0', STR_PAD_LEFT);
            $nb     = intval($med['nb_consultations']);
          ?>
          <tr>
            <!-- Médecin -->
            <td>
              <div class="doc-cell">
                <div class="doc-avatar" style="background:<?= $color ?>;"><?= $init ?></div>
                <div>
                  <div class="doc-name"><?= htmlspecialchars($med['prenom'] . ' ' . $med['nom']) ?></div>
                  <div class="doc-id">ID: #<?= $id_fmt ?></div>
                </div>
              </div>
            </td>
            <!-- Email -->
            <td class="cell-email"><?= htmlspecialchars($med['mail']) ?></td>
            <!-- Téléphone -->
            <td class="cell-tel"><?= htmlspecialchars($med['tel'] ?? '—') ?></td>
            <!-- Spécialité / Rôle -->
            <td><span class="role-badge"><?= htmlspecialchars($med['role']) ?></span></td>
            <!-- Nb consultations -->
            <td>
              <span class="consult-num <?= $nb === 0 ? 'zero' : '' ?>"><?= $nb ?></span>
            </td>
            <!-- Bouton œil -->
            <td>
              <button class="btn-eye"
                      onclick="ouvrirRdvs(<?= $med['id_PK'] ?>, '<?= htmlspecialchars($med['prenom'].' '.$med['nom'], ENT_QUOTES) ?>', '<?= $color ?>', '<?= $init ?>')"
                      title="Voir les rendez-vous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div><!-- /page-content -->
</div><!-- /main -->

<!-- ===== MODALE LISTE RDV ===== -->
<div class="modal-overlay" id="modalRdv">
  <div class="modal-box">

    <div class="modal-head">
      <div class="modal-head-left">
        <div class="modal-avatar" id="modalInit">--</div>
        <div>
          <div class="modal-head-title" id="modalNom">—</div>
          <div class="modal-head-sub" id="modalSubtitle">Rendez-vous</div>
        </div>
      </div>
      <button class="modal-close" onclick="fermerModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="modal-body" id="modalBody">
      <!-- Rempli dynamiquement par JS -->
    </div>

  </div>
</div>

<!-- Données RDV en JSON pour le JS -->
<script>
const rdvsData = <?= json_encode($rdvs_par_medecin) ?>;

const moisFr = ['','Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const joursFr = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];

function formatDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return joursFr[d.getDay()] + ' ' + String(d.getDate()).padStart(2,'0') + ' ' + moisFr[d.getMonth()+1] + ' ' + d.getFullYear();
}

function badgeHtml(statut) {
  const labels = { en_attente:'En attente', confirme:'Confirmé', annule:'Annulé' };
  return `<span class="badge ${statut}">${labels[statut] || statut}</span>`;
}

function ouvrirRdvs(mid, nom, color, init) {
  document.getElementById('modalInit').textContent  = init;
  document.getElementById('modalInit').style.background = color;
  document.getElementById('modalNom').textContent   = 'Dr. ' + nom;

  const rdvs = rdvsData[mid] || [];
  document.getElementById('modalSubtitle').textContent =
    rdvs.length + ' rendez-vous au total';

  const body = document.getElementById('modalBody');

  if (rdvs.length === 0) {
    body.innerHTML = `
      <div class="empty-rdv">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <p>Aucun rendez-vous pour ce médecin.</p>
      </div>`;
  } else {
    let rows = rdvs.map(r => {
      const init2 = (r.prenom[0] + r.nom[0]).toUpperCase();
      const genreClass = r.genre === 'femme' ? 'femme' : 'homme';
      return `
        <tr>
          <td>
            <div class="pat-cell">
              <div class="pat-init ${genreClass}">${init2}</div>
              <div>
                <div class="pat-name">${r.prenom} ${r.nom}</div>
                <div class="pat-cin">CIN : ${r.cin}</div>
              </div>
            </div>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">${r.genre.charAt(0).toUpperCase()+r.genre.slice(1)}</td>
          <td>
            <div class="date-cell">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              ${formatDate(r.date)}
            </div>
          </td>
          <td>
            <div class="heure-cell">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              ${r.heure}
            </div>
          </td>
          <td>${badgeHtml(r.statut)}</td>
        </tr>`;
    }).join('');

    body.innerHTML = `
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
        <tbody>${rows}</tbody>
      </table>`;
  }

  document.getElementById('modalRdv').classList.add('open');
}

function fermerModal() {
  document.getElementById('modalRdv').classList.remove('open');
}

document.getElementById('modalRdv').addEventListener('click', function(e) {
  if (e.target === this) fermerModal();
});
</script>
</body>
</html>