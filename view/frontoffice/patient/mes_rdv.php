<?php
// ============================================================
//  mes-rdv.php — Vue patient
//  Le patient entre son CIN pour voir ses rendez-vous
//  À placer dans : view/frontoffice/patient/
// ============================================================

require_once __DIR__ . '/../../../config.php';

$pdo      = config::getConnexion();
$cin      = '';
$rdvs     = [];
$cherche  = false;
$erreur   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin     = trim($_POST['cin'] ?? '');
    $cherche = true;

    if (empty($cin)) {
        $erreur = 'Veuillez entrer votre numéro CIN.';
    } elseif (!preg_match('/^[0-9]{8}$/', $cin)) {
        $erreur = 'Le CIN doit contenir exactement 8 chiffres.';
    } else {
        // Récupérer les RDV + nom du médecin via JOIN utilisateurs
        $stmt = $pdo->prepare(
            "SELECT r.*,
                    CONCAT(u.prenom, ' ', u.nom) AS medecin_nom_complet
             FROM rendez_vous r
             LEFT JOIN utilisateurs u ON u.id_PK = r.medecin_id
             WHERE r.cin = :cin
             ORDER BY r.date_rdv DESC, r.heure_rdv DESC"
        );
        $stmt->execute([':cin' => $cin]);
        $rdvs = $stmt->fetchAll();
    }
}

// Noms mois FR
$mois_fr = ['','Janvier','Février','Mars','Avril','Mai','Juin',
            'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Rendez-vous — MediFlow</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root {
      --primary:      #2563eb;
      --primary-dark: #1565c0;
      --primary-light:#dbeafe;
      --teal:         #0d9488;
      --teal-light:   #ccfbf1;
      --bg:           #f8fafc;
      --surface:      #ffffff;
      --surface-alt:  #f1f5f9;
      --border:       #e2e8f0;
      --text:         #0f172a;
      --text-secondary:#475569;
      --text-muted:   #94a3b8;
      --green:        #15803d;
      --green-bg:     #dcfce7;
      --amber:        #b45309;
      --amber-bg:     #fef3c7;
      --red:          #ba1a1a;
      --red-bg:       #ffdad6;
      --shadow-md:    0 4px 20px rgba(0,0,0,0.06);
      --shadow-lg:    0 10px 40px rgba(0,0,0,0.08);
      --r:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
      --transition:   0.2s cubic-bezier(0.4,0,0.2,1);
    }

    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

    /* ===== NAVBAR ===== */
    .navbar { background:rgba(255,255,255,0.85); backdrop-filter:blur(20px); border-bottom:1px solid var(--border); padding:0 40px; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
    .navbar-left { display:flex; align-items:center; gap:40px; }
    .navbar-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .brand-logo { width:36px; height:36px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(37,99,235,0.3); }
    .brand-logo svg { width:20px; height:20px; fill:white; }
    .brand-name { font-family:'Manrope',sans-serif; font-weight:800; font-size:18px; color:var(--text); }
    .navbar-links { display:flex; gap:2px; list-style:none; }
    .navbar-links a { display:flex; align-items:center; gap:7px; padding:8px 14px; border-radius:8px; color:var(--text-secondary); font-size:13.5px; font-weight:500; text-decoration:none; transition:all var(--transition); }
    .navbar-links a svg { width:16px; height:16px; }
    .navbar-links a:hover { background:var(--surface-alt); color:var(--text); }
    .navbar-links a.active { background:var(--primary-light); color:var(--primary); font-weight:600; }

    /* ===== PAGE ===== */
    .page-wrapper { max-width:760px; margin:0 auto; padding:48px 24px; }

    /* Hero */
    .page-hero { text-align:center; margin-bottom:40px; }
    .hero-icon { width:64px; height:64px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:var(--r-xl); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 8px 24px rgba(37,99,235,0.3); }
    .hero-icon svg { width:30px; height:30px; fill:white; }
    .page-hero h1 { font-family:'Manrope',sans-serif; font-size:30px; font-weight:800; margin-bottom:8px; }
    .page-hero p { font-size:15px; color:var(--text-secondary); }

    /* Formulaire CIN */
    .cin-card { background:var(--surface); border-radius:var(--r-xl); padding:32px; box-shadow:var(--shadow-md); margin-bottom:32px; }
    .cin-card h2 { font-family:'Manrope',sans-serif; font-size:17px; font-weight:700; margin-bottom:6px; }
    .cin-card p { font-size:13px; color:var(--text-secondary); margin-bottom:20px; }
    .cin-row { display:flex; gap:12px; }
    .cin-input { flex:1; padding:13px 16px; border:2px solid var(--border); border-radius:var(--r); font-size:15px; font-family:'Inter',sans-serif; color:var(--text); outline:none; transition:border-color var(--transition); letter-spacing:0.08em; }
    .cin-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
    .cin-input.error { border-color:var(--red); }
    .btn-chercher { padding:13px 28px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r); font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; cursor:pointer; white-space:nowrap; transition:all var(--transition); box-shadow:0 3px 10px rgba(37,99,235,0.3); }
    .btn-chercher:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(37,99,235,0.4); }
    .erreur-msg { margin-top:10px; font-size:13px; color:var(--red); font-weight:500; }

    /* Résultats */
    .results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
    .results-title { font-family:'Manrope',sans-serif; font-size:18px; font-weight:800; }
    .results-count { font-size:13px; color:var(--text-muted); }
    .results-count strong { color:var(--primary); }

    /* Carte RDV */
    .rdv-card { background:var(--surface); border-radius:var(--r-lg); padding:20px 24px; box-shadow:var(--shadow-md); border:1px solid transparent; transition:all var(--transition); margin-bottom:12px; display:flex; align-items:center; gap:20px; }
    .rdv-card:hover { border-color:rgba(37,99,235,0.2); box-shadow:var(--shadow-lg); transform:translateY(-1px); }

    /* Icône date */
    .rdv-date-box { min-width:60px; background:var(--primary-light); border-radius:var(--r); padding:10px 8px; text-align:center; flex-shrink:0; }
    .rdv-date-box .day { font-family:'Manrope',sans-serif; font-weight:800; font-size:24px; color:var(--primary); line-height:1; }
    .rdv-date-box .month { font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.08em; margin-top:2px; }

    /* Infos */
    .rdv-info { flex:1; }
    .rdv-medecin { font-family:'Manrope',sans-serif; font-weight:700; font-size:15px; margin-bottom:4px; }
    .rdv-meta { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .rdv-meta-item { display:flex; align-items:center; gap:5px; font-size:13px; color:var(--text-secondary); }
    .rdv-meta-item svg { width:14px; height:14px; color:var(--text-muted); flex-shrink:0; }

    /* Badge statut */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:var(--r-full); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; }
    .badge.en_attente { background:var(--amber-bg); color:var(--amber); }
    .badge.en_attente::before { background:var(--amber); }
    .badge.confirme   { background:var(--green-bg); color:var(--green); }
    .badge.confirme::before   { background:var(--green); }
    .badge.annule     { background:var(--red-bg);   color:var(--red); }
    .badge.annule::before     { background:var(--red); }

    /* Numéro RDV */
    .rdv-num { font-size:11px; color:var(--text-muted); margin-top:4px; }

    /* Empty / no result */
    .empty-box { background:var(--surface); border-radius:var(--r-xl); padding:56px 24px; text-align:center; box-shadow:var(--shadow-md); }
    .empty-box svg { width:52px; height:52px; color:var(--border); margin-bottom:16px; }
    .empty-box h3 { font-family:'Manrope',sans-serif; font-size:18px; font-weight:700; margin-bottom:6px; }
    .empty-box p { font-size:14px; color:var(--text-muted); margin-bottom:20px; }
    .btn-prendre { display:inline-flex; align-items:center; gap:7px; padding:11px 22px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border-radius:var(--r); font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; text-decoration:none; box-shadow:0 3px 10px rgba(37,99,235,0.3); transition:all var(--transition); }
    .btn-prendre:hover { transform:translateY(-1px); }

    @media (max-width:600px) {
      .cin-row { flex-direction:column; }
      .rdv-card { flex-direction:column; align-items:flex-start; gap:12px; }
      .navbar { padding:0 16px; }
      .navbar-links { display:none; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-left">
    <a href="annuaire.php" class="navbar-brand">
      <div class="brand-logo">
        <svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg>
      </div>
      <span class="brand-name">MediFlow</span>
    </a>
    <ul class="navbar-links">
      <li><a href="profil.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mon Profil
      </a></li>
      <li><a href="mes-rdv.php" class="active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Mes RDV
      </a></li>
      <li><a href="annuaire.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Annuaire
      </a></li>
    </ul>
  </div>
</nav>

<!-- PAGE -->
<div class="page-wrapper">

  <!-- Hero -->
  <div class="page-hero">
    <div class="hero-icon">
      <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 3a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H8a1 1 0 110-2h3V7a1 1 0 011-1z"/></svg>
    </div>
    <h1>Mes Rendez-vous</h1>
    <p>Entrez votre numéro CIN pour retrouver tous vos rendez-vous.</p>
  </div>

  <!-- Formulaire CIN -->
  <div class="cin-card">
    <h2>Rechercher mes rendez-vous</h2>
    <p>Votre CIN est le numéro utilisé lors de la prise de rendez-vous.</p>
    <form method="POST" action="mes-rdv.php">
      <div class="cin-row">
        <input type="text" name="cin" class="cin-input <?= $erreur ? 'error' : '' ?>"
               placeholder="Ex : 12345678"
               value="<?= htmlspecialchars($cin) ?>"
               maxlength="8" inputmode="numeric">
        <button type="submit" class="btn-chercher">
          Rechercher
        </button>
      </div>
      <?php if ($erreur): ?>
        <div class="erreur-msg">⚠ <?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>
    </form>
  </div>

  <!-- Résultats -->
  <?php if ($cherche && !$erreur): ?>

    <?php if (empty($rdvs)): ?>
      <div class="empty-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <h3>Aucun rendez-vous trouvé</h3>
        <p>Aucun rendez-vous n'est associé au CIN <strong><?= htmlspecialchars($cin) ?></strong>.</p>
        <a href="annuaire.php" class="btn-prendre">Prendre un rendez-vous</a>
      </div>

    <?php else: ?>

      <div class="results-header">
        <div class="results-title">Vos rendez-vous</div>
        <div class="results-count"><strong><?= count($rdvs) ?></strong> RDV trouvé<?= count($rdvs) > 1 ? 's' : '' ?></div>
      </div>

      <?php foreach ($rdvs as $rdv):
        $ts       = strtotime($rdv['date_rdv']);
        $jour_num = date('d', $ts);
        $mois     = $mois_fr[(int)date('m', $ts)];
        $annee    = date('Y', $ts);
        $heure    = date('H:i', strtotime($rdv['heure_rdv']));

        $badge_labels = ['en_attente'=>'En attente','confirme'=>'Confirmé','annule'=>'Annulé'];
        $badge_label  = $badge_labels[$rdv['statut']] ?? $rdv['statut'];

        $medecin_affiche = $rdv['medecin_nom_complet']
                           ? 'Dr. ' . $rdv['medecin_nom_complet']
                           : 'Médecin #' . $rdv['medecin_id'];
      ?>
      <div class="rdv-card">

        <!-- Boîte date -->
        <div class="rdv-date-box">
          <div class="day"><?= $jour_num ?></div>
          <div class="month"><?= substr($mois, 0, 3) ?></div>
        </div>

        <!-- Infos -->
        <div class="rdv-info">
          <div class="rdv-medecin"><?= htmlspecialchars($medecin_affiche) ?></div>
          <div class="rdv-meta">
            <div class="rdv-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <?= $heure ?>
            </div>
            <div class="rdv-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <?= $jour_num ?> <?= $mois ?> <?= $annee ?>
            </div>
            <span class="badge <?= htmlspecialchars($rdv['statut']) ?>"><?= $badge_label ?></span>
          </div>
          <div class="rdv-num">Réf. RDV #<?= $rdv['id'] ?></div>
        </div>

      </div>
      <?php endforeach; ?>

    <?php endif; ?>

  <?php endif; ?>

</div>
</body>
</html>