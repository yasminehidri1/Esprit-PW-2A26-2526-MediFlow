<?php
// ============================================================
//  statistiques.php — Statistiques avancées des rendez-vous
//  Données extraites directement depuis la BDD (PDO)
// ============================================================
require_once __DIR__ . '/../../../controller/RendezVousController.php';
require_once __DIR__ . '/../../../config.php';

$medecin_id = 1; // remplacer par $_SESSION['medecin_id']
$pdo        = config::getConnexion();

// ── Helpers de date ──────────────────────────────────────────
$today       = date('Y-m-d');
$year        = (int) date('Y');
$month       = (int) date('m');
$last_month  = $month === 1 ? 12 : $month - 1;
$last_month_year = $month === 1 ? $year - 1 : $year;

// ── 1. RDV par mois (12 derniers mois) ──────────────────────
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(date_rdv,'%Y-%m') AS mois, COUNT(*) AS nb
    FROM rendez_vous
    WHERE medecin_id = :mid
      AND date_rdv >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mois
    ORDER BY mois ASC
");
$stmt->execute([':mid' => $medecin_id]);
$rdv_par_mois = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Remplir les mois manquants avec 0
$mois_labels = [];
$mois_data   = [];
for ($i = 11; $i >= 0; $i--) {
    $d  = new DateTime("first day of -$i month");
    $k  = $d->format('Y-m');
    $lb = $d->format('M Y');    // ex: Jan 2025
    $mois_labels[] = $lb;
    $mois_data[]   = 0;
}
foreach ($rdv_par_mois as $r) {
    $d  = new DateTime($r['mois'] . '-01');
    $lb = $d->format('M Y');
    $idx = array_search($lb, $mois_labels);
    if ($idx !== false) $mois_data[$idx] = (int) $r['nb'];
}

// ── 2. RDV par année (5 dernières années) ───────────────────
$stmt = $pdo->prepare("
    SELECT YEAR(date_rdv) AS annee, COUNT(*) AS nb
    FROM rendez_vous
    WHERE medecin_id = :mid
      AND YEAR(date_rdv) >= :y
    GROUP BY annee
    ORDER BY annee ASC
");
$stmt->execute([':mid' => $medecin_id, ':y' => $year - 4]);
$rdv_par_annee = $stmt->fetchAll(PDO::FETCH_ASSOC);

$annee_labels = [];
$annee_data   = [];
for ($y = $year - 4; $y <= $year; $y++) {
    $annee_labels[] = (string) $y;
    $found = array_filter($rdv_par_annee, fn($r) => (int)$r['annee'] === $y);
    $annee_data[] = $found ? (int) array_values($found)[0]['nb'] : 0;
}

// ── 3. Répartition par genre ─────────────────────────────────
$stmt = $pdo->prepare("
    SELECT genre, COUNT(*) AS nb
    FROM rendez_vous
    WHERE medecin_id = :mid
    GROUP BY genre
");
$stmt->execute([':mid' => $medecin_id]);
$genre_rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$nb_homme = (int)($genre_rows['homme'] ?? 0);
$nb_femme = (int)($genre_rows['femme'] ?? 0);
$nb_genre_total = $nb_homme + $nb_femme;
$pct_homme = $nb_genre_total ? round($nb_homme / $nb_genre_total * 100) : 0;
$pct_femme = $nb_genre_total ? round($nb_femme / $nb_genre_total * 100) : 0;

// ── 4. Répartition par statut ────────────────────────────────
$stmt = $pdo->prepare("
    SELECT statut, COUNT(*) AS nb
    FROM rendez_vous
    WHERE medecin_id = :mid
    GROUP BY statut
");
$stmt->execute([':mid' => $medecin_id]);
$statut_rows   = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$nb_confirme   = (int)($statut_rows['confirme']   ?? 0);
$nb_attente    = (int)($statut_rows['en_attente'] ?? 0);
$nb_annule     = (int)($statut_rows['annule']     ?? 0);
$nb_total_statut = $nb_confirme + $nb_attente + $nb_annule;

// ── 5. KPIs rapides ──────────────────────────────────────────
// RDV mois courant
$stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND MONTH(date_rdv)=:m AND YEAR(date_rdv)=:y");
$stmt->execute([':mid'=>$medecin_id,':m'=>$month,':y'=>$year]);
$rdv_ce_mois = (int)$stmt->fetchColumn();

// RDV mois précédent
$stmt->execute([':mid'=>$medecin_id,':m'=>$last_month,':y'=>$last_month_year]);
$rdv_mois_prec = (int)$stmt->fetchColumn();

// Évolution mois
$evol_mois = $rdv_mois_prec > 0 ? round(($rdv_ce_mois - $rdv_mois_prec) / $rdv_mois_prec * 100) : 0;

// RDV année courante
$stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y");
$stmt->execute([':mid'=>$medecin_id,':y'=>$year]);
$rdv_cette_annee = (int)$stmt->fetchColumn();

// Taux de confirmation
$taux_confirmation = $nb_total_statut > 0 ? round($nb_confirme / $nb_total_statut * 100) : 0;

// Taux annulation
$taux_annulation = $nb_total_statut > 0 ? round($nb_annule / $nb_total_statut * 100) : 0;

// Moyenne par mois
$stmt = $pdo->prepare("SELECT AVG(nb) FROM (SELECT MONTH(date_rdv) m, COUNT(*) nb FROM rendez_vous WHERE medecin_id=:mid AND YEAR(date_rdv)=:y GROUP BY m) t");
$stmt->execute([':mid'=>$medecin_id,':y'=>$year]);
$moy_mois = round((float)$stmt->fetchColumn(), 1);

// ── JSON pour Chart.js ───────────────────────────────────────
$json_mois_labels = json_encode($mois_labels);
$json_mois_data   = json_encode($mois_data);
$json_annee_labels = json_encode($annee_labels);
$json_annee_data   = json_encode($annee_data);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statistiques — MediFlow Pro</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <!-- Chart.js CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root {
      --primary:       #004d99;
      --primary-dark:  #1565c0;
      --primary-light: #d6e3ff;
      --teal:          #005851;
      --teal-light:    #84f5e8;
      --teal-bg:       rgba(0,88,81,0.10);
      --rose:          #be185d;
      --rose-bg:       #fce7f3;
      --amber:         #b45309;
      --amber-bg:      #fef3c7;
      --bg:            #f0f4f8;
      --surface:       #ffffff;
      --surface-low:   #f5f7fa;
      --border:        #e2e8f0;
      --text:          #0f172a;
      --text-muted:    #64748b;
      --error:         #ba1a1a;
      --error-bg:      #ffdad6;
      --shadow:        0 2px 16px rgba(0,77,153,0.08);
      --shadow-hover:  0 8px 32px rgba(0,77,153,0.15);
      --sidebar-w:     220px;
      --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
    }
    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

    /* ── SIDEBAR ───────────────────────────────────────────── */
    .sidebar { width:var(--sidebar-w); min-height:100vh; position:fixed; top:0; left:0; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; padding:20px 12px; z-index:100; }
    .sidebar-brand { display:flex; align-items:center; gap:10px; padding:6px 8px 16px; border-bottom:1px solid var(--border); margin-bottom:12px; }
    .brand-logo { width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .brand-logo svg { width:20px; height:20px; fill:white; }
    .brand-text .name { font-family:'Manrope',sans-serif; font-weight:800; font-size:15px; color:#1e3a6e; display:block; line-height:1.1; }
    .brand-text .sub  { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--text-muted); display:block; }
    .sidebar-profile { display:flex; align-items:center; gap:10px; padding:10px; background:var(--surface-low); border-radius:var(--r-md); margin-bottom:10px; }
    .profile-avatar  { width:40px; height:40px; border-radius:var(--r-full); background:var(--primary-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .profile-avatar svg { width:22px; height:22px; fill:var(--primary); }
    .profile-name { font-family:'Manrope',sans-serif; font-weight:700; font-size:13px; color:var(--text); display:block; }
    .profile-spec { font-size:11px; color:var(--text-muted); display:block; }
    .sidebar-nav { display:flex; flex-direction:column; gap:2px; flex:1; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--r-md); color:var(--text-muted); font-size:14px; font-weight:500; text-decoration:none; transition:all 0.15s; border-left:3px solid transparent; }
    .nav-item svg { width:18px; height:18px; flex-shrink:0; }
    .nav-item:hover  { background:rgba(0,77,153,0.05); color:var(--primary); }
    .nav-item.active { background:var(--surface); color:var(--primary); font-weight:700; border-left-color:var(--teal); box-shadow:var(--shadow); }
    .nav-item.logout { color:var(--error); }
    .nav-item.logout:hover { background:rgba(186,26,26,0.05); }
    .sidebar-footer  { padding-top:12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

    /* ── MAIN ──────────────────────────────────────────────── */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }
    .topbar { height:64px; background:rgba(255,255,255,0.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:50; }
    .topbar-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:20px; background:linear-gradient(135deg,#1e3a6e,var(--primary)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .page-content { padding:24px 28px; flex:1; }

    /* ── PAGE HEADER ────────────────────────────────────────── */
    .page-header { margin-bottom:28px; }
    .page-header h1 { font-family:'Manrope',sans-serif; font-weight:800; font-size:26px; color:var(--text); }
    .page-header p  { font-size:14px; color:var(--text-muted); margin-top:4px; }

    /* ── KPI GRID ───────────────────────────────────────────── */
    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    .kpi-card { background:var(--surface); border-radius:var(--r-xl); padding:20px 22px; box-shadow:var(--shadow); position:relative; overflow:hidden; }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:var(--r-xl) var(--r-xl) 0 0; }
    .kpi-card.blue::before   { background:linear-gradient(90deg,var(--primary),var(--primary-dark)); }
    .kpi-card.teal::before   { background:linear-gradient(90deg,var(--teal),#007a6e); }
    .kpi-card.rose::before   { background:linear-gradient(90deg,var(--rose),#db2777); }
    .kpi-card.amber::before  { background:linear-gradient(90deg,var(--amber),#d97706); }
    .kpi-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); margin-bottom:10px; }
    .kpi-value { font-family:'Manrope',sans-serif; font-size:32px; font-weight:800; line-height:1; color:var(--text); }
    .kpi-value span { font-size:16px; font-weight:600; margin-left:2px; }
    .kpi-sub { font-size:12px; color:var(--text-muted); margin-top:6px; display:flex; align-items:center; gap:5px; }
    .kpi-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:var(--r-full); font-size:11px; font-weight:700; }
    .kpi-badge.up   { background:#dcfce7; color:#15803d; }
    .kpi-badge.down { background:#fee2e2; color:var(--error); }
    .kpi-badge.neutral { background:var(--surface-low); color:var(--text-muted); }

    /* ── CHARTS GRID ────────────────────────────────────────── */
    .charts-row { display:grid; gap:20px; margin-bottom:20px; }
    .charts-row.two-col { grid-template-columns:1fr 1fr; }
    .charts-row.full    { grid-template-columns:1fr; }

    .chart-card { background:var(--surface); border-radius:var(--r-xl); padding:24px; box-shadow:var(--shadow); }
    .chart-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; }
    .chart-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:16px; color:var(--text); }
    .chart-sub   { font-size:12px; color:var(--text-muted); margin-top:3px; }
    .chart-badge { padding:4px 12px; background:var(--primary-light); color:var(--primary); border-radius:var(--r-full); font-size:11px; font-weight:700; white-space:nowrap; }
    .chart-wrap  { position:relative; }

    /* ── GENRE CARD ─────────────────────────────────────────── */
    .genre-split { display:flex; gap:20px; margin-top:8px; }
    .genre-side  { flex:1; }
    .genre-icon-row { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .genre-icon { width:44px; height:44px; border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .genre-icon.blue { background:var(--primary-light); }
    .genre-icon.rose { background:var(--rose-bg); }
    .genre-icon svg { width:22px; height:22px; }
    .genre-name  { font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; }
    .genre-nb    { font-family:'Manrope',sans-serif; font-weight:800; font-size:28px; color:var(--text); }
    .genre-pct   { font-size:13px; color:var(--text-muted); margin-top:2px; }
    .genre-bar-bg { height:8px; background:var(--surface-low); border-radius:var(--r-full); margin-top:10px; overflow:hidden; }
    .genre-bar-fill { height:100%; border-radius:var(--r-full); transition:width 1s ease; }
    .genre-bar-fill.blue { background:linear-gradient(90deg,var(--primary),var(--primary-dark)); }
    .genre-bar-fill.rose { background:linear-gradient(90deg,var(--rose),#db2777); }
    .genre-divider { width:1px; background:var(--border); align-self:stretch; }

    /* ── STATUT DOUGHNUT ────────────────────────────────────── */
    .statut-legend { display:flex; flex-direction:column; gap:12px; margin-top:16px; }
    .statut-row  { display:flex; align-items:center; justify-content:space-between; }
    .statut-left { display:flex; align-items:center; gap:10px; }
    .statut-dot  { width:10px; height:10px; border-radius:var(--r-full); flex-shrink:0; }
    .statut-lbl  { font-size:13px; font-weight:600; color:var(--text); }
    .statut-pct  { font-family:'Manrope',sans-serif; font-size:13px; font-weight:800; color:var(--text-muted); }
  </style>
</head>
<body>

<!-- ═════════════════════════════════════════════════════════════
     SIDEBAR
═════════════════════════════════════════════════════════════ -->
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
    <a href="planning.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Planning
    </a>
    <a href="patients.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Patients
    </a>
    <a href="statistiques.php" class="nav-item active">
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

<!-- ═════════════════════════════════════════════════════════════
     MAIN
═════════════════════════════════════════════════════════════ -->
<div class="main">
  <header class="topbar">
    <h2 class="topbar-title">Statistiques</h2>
  </header>

  <div class="page-content">

    <div class="page-header">
      <h1>Vue d'ensemble analytique</h1>
      <p>Données consolidées de votre activité — rendez-vous, patients et tendances.</p>
    </div>

    <!-- ── KPIs ──────────────────────────────────────────────── -->
    <div class="kpi-grid">

      <div class="kpi-card blue">
        <div class="kpi-label">RDV ce mois-ci</div>
        <div class="kpi-value"><?= $rdv_ce_mois ?></div>
        <div class="kpi-sub">
          vs mois précédent :
          <span class="kpi-badge <?= $evol_mois > 0 ? 'up' : ($evol_mois < 0 ? 'down' : 'neutral') ?>">
            <?= $evol_mois > 0 ? '↑ +' : ($evol_mois < 0 ? '↓ ' : '') ?><?= $evol_mois ?>%
          </span>
        </div>
      </div>

      <div class="kpi-card teal">
        <div class="kpi-label">RDV cette année</div>
        <div class="kpi-value"><?= $rdv_cette_annee ?></div>
        <div class="kpi-sub">Moy. <?= $moy_mois ?> / mois</div>
      </div>

      <div class="kpi-card rose">
        <div class="kpi-label">Taux de confirmation</div>
        <div class="kpi-value"><?= $taux_confirmation ?><span>%</span></div>
        <div class="kpi-sub"><?= $nb_confirme ?> confirmés sur <?= $nb_total_statut ?></div>
      </div>

      <div class="kpi-card amber">
        <div class="kpi-label">Taux d'annulation</div>
        <div class="kpi-value"><?= $taux_annulation ?><span>%</span></div>
        <div class="kpi-sub"><?= $nb_annule ?> annulés sur <?= $nb_total_statut ?></div>
      </div>
    </div>

    <!-- ── GRAPHE : Évolution mensuelle (12 mois) ────────────── -->
    <div class="charts-row full">
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <div class="chart-title">Évolution mensuelle des rendez-vous</div>
            <div class="chart-sub">12 derniers mois</div>
          </div>
          <span class="chart-badge">Tendance</span>
        </div>
        <div class="chart-wrap" style="height:260px;">
          <canvas id="chartMois"></canvas>
        </div>
      </div>
    </div>

    <!-- ── GRAPHES : Annuel + Statuts ────────────────────────── -->
    <div class="charts-row two-col">

      <!-- Graphe annuel -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <div class="chart-title">Activité annuelle</div>
            <div class="chart-sub">5 dernières années</div>
          </div>
          <span class="chart-badge">Années</span>
        </div>
        <div class="chart-wrap" style="height:230px;">
          <canvas id="chartAnnee"></canvas>
        </div>
      </div>

      <!-- Graphe statuts (doughnut) -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <div class="chart-title">Répartition par statut</div>
            <div class="chart-sub">Tous les rendez-vous</div>
          </div>
        </div>
        <div style="display:flex; gap:20px; align-items:center;">
          <div class="chart-wrap" style="height:180px; width:180px; flex-shrink:0;">
            <canvas id="chartStatut"></canvas>
          </div>
          <div class="statut-legend" style="flex:1;">
            <div class="statut-row">
              <div class="statut-left">
                <div class="statut-dot" style="background:#00897b;"></div>
                <div class="statut-lbl">Confirmés</div>
              </div>
              <div class="statut-pct"><?= $nb_confirme ?> &nbsp;(<?= $nb_total_statut ? round($nb_confirme/$nb_total_statut*100) : 0 ?>%)</div>
            </div>
            <div class="statut-row">
              <div class="statut-left">
                <div class="statut-dot" style="background:#004d99;"></div>
                <div class="statut-lbl">En attente</div>
              </div>
              <div class="statut-pct"><?= $nb_attente ?> &nbsp;(<?= $nb_total_statut ? round($nb_attente/$nb_total_statut*100) : 0 ?>%)</div>
            </div>
            <div class="statut-row">
              <div class="statut-left">
                <div class="statut-dot" style="background:#ba1a1a;"></div>
                <div class="statut-lbl">Annulés</div>
              </div>
              <div class="statut-pct"><?= $nb_annule ?> &nbsp;(<?= $nb_total_statut ? round($nb_annule/$nb_total_statut*100) : 0 ?>%)</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── GRAPHE : Répartition par genre ───────────────────── -->
    <div class="charts-row full">
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <div class="chart-title">Répartition par genre</div>
            <div class="chart-sub"><?= $nb_genre_total ?> patients au total</div>
          </div>
        </div>

        <div class="genre-split">
          <!-- Hommes -->
          <div class="genre-side">
            <div class="genre-icon-row">
              <div class="genre-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="#004d99" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
                </svg>
              </div>
              <div>
                <div class="genre-name" style="color:var(--primary);">Hommes</div>
              </div>
            </div>
            <div class="genre-nb"><?= $nb_homme ?></div>
            <div class="genre-pct"><?= $pct_homme ?>% des patients</div>
            <div class="genre-bar-bg">
              <div class="genre-bar-fill blue" style="width:<?= $pct_homme ?>%;"></div>
            </div>
          </div>

          <div class="genre-divider"></div>

          <!-- Femmes -->
          <div class="genre-side">
            <div class="genre-icon-row">
              <div class="genre-icon rose">
                <svg viewBox="0 0 24 24" fill="none" stroke="#be185d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="4"/>
                  <path d="M12 12v8M9 18h6"/>
                </svg>
              </div>
              <div>
                <div class="genre-name" style="color:var(--rose);">Femmes</div>
              </div>
            </div>
            <div class="genre-nb"><?= $nb_femme ?></div>
            <div class="genre-pct"><?= $pct_femme ?>% des patients</div>
            <div class="genre-bar-bg">
              <div class="genre-bar-fill rose" style="width:<?= $pct_femme ?>%;"></div>
            </div>
          </div>

          <!-- Mini graphe genre -->
          <div style="width:220px; flex-shrink:0; display:flex; align-items:center;">
            <canvas id="chartGenre" style="max-height:180px;"></canvas>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /page-content -->
</div><!-- /main -->

<!-- ═════════════════════════════════════════════════════════════
     CHART.JS SCRIPTS
═════════════════════════════════════════════════════════════ -->
<script>
  // Données PHP → JS
  const moisLabels  = <?= $json_mois_labels ?>;
  const moisData    = <?= $json_mois_data ?>;
  const anneeLabels = <?= $json_annee_labels ?>;
  const anneeData   = <?= $json_annee_data ?>;

  const nbHomme  = <?= $nb_homme ?>;
  const nbFemme  = <?= $nb_femme ?>;
  const nbConf   = <?= $nb_confirme ?>;
  const nbAtt    = <?= $nb_attente ?>;
  const nbAnn    = <?= $nb_annule ?>;

  // ── Palette commune ──────────────────────────────────────────
  const BLUE  = '#004d99';
  const TEAL  = '#005851';
  const ROSE  = '#be185d';
  const AMBER = '#b45309';

  // Defaults Chart.js
  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.color       = '#64748b';

  // ── 1. Graphe mensuel (line area) ───────────────────────────
  new Chart(document.getElementById('chartMois'), {
    type: 'line',
    data: {
      labels: moisLabels,
      datasets: [{
        label: 'Rendez-vous',
        data: moisData,
        borderColor: BLUE,
        borderWidth: 2.5,
        pointBackgroundColor: BLUE,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: true,
        backgroundColor: (ctx) => {
          const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
          g.addColorStop(0, 'rgba(0,77,153,0.18)');
          g.addColorStop(1, 'rgba(0,77,153,0)');
          return g;
        },
        tension: 0.4,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display:false }, tooltip: { mode:'index', intersect:false,
        callbacks: { label: ctx => ` ${ctx.raw} RDV` }
      }},
      scales: {
        x: { grid: { display:false }, ticks: { font:{ size:11 } } },
        y: { beginAtZero:true, ticks: { stepSize:1, font:{ size:11 } }, grid: { color:'#f1f5f9' } }
      }
    }
  });

  // ── 2. Graphe annuel (bar) ───────────────────────────────────
  new Chart(document.getElementById('chartAnnee'), {
    type: 'bar',
    data: {
      labels: anneeLabels,
      datasets: [{
        label: 'RDV / an',
        data: anneeData,
        backgroundColor: anneeLabels.map((_, i, a) =>
          i === a.length - 1 ? BLUE : 'rgba(0,77,153,0.18)'
        ),
        borderRadius: 8,
        borderSkipped: false,
        hoverBackgroundColor: BLUE,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display:false }, tooltip: {
        callbacks: { label: ctx => ` ${ctx.raw} RDV` }
      }},
      scales: {
        x: { grid: { display:false }, ticks: { font:{ size:12 } } },
        y: { beginAtZero:true, ticks: { stepSize:1, font:{ size:11 } }, grid: { color:'#f1f5f9' } }
      }
    }
  });

  // ── 3. Doughnut statuts ─────────────────────────────────────
  new Chart(document.getElementById('chartStatut'), {
    type: 'doughnut',
    data: {
      labels: ['Confirmés', 'En attente', 'Annulés'],
      datasets: [{
        data: [nbConf, nbAtt, nbAnn],
        backgroundColor: ['#00897b', BLUE, '#ba1a1a'],
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: { display:false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.raw} (${Math.round(ctx.parsed / (nbConf+nbAtt+nbAnn) * 100)}%)` } }
      }
    }
  });

  // ── 4. Doughnut genre ────────────────────────────────────────
  new Chart(document.getElementById('chartGenre'), {
    type: 'doughnut',
    data: {
      labels: ['Hommes', 'Femmes'],
      datasets: [{
        data: [nbHomme, nbFemme],
        backgroundColor: [BLUE, ROSE],
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '62%',
      plugins: {
        legend: { display:false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.raw}` } }
      }
    }
  });
</script>
</body>
</html>