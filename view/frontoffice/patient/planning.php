<?php
// ============================================================
//  planning-patient.php — Vue patient
//  Affiche les créneaux DISPONIBLES du médecin (id=1 pour l'instant)
//  Synchronisé avec : rendez_vous (créneaux pris) + planning (blocages)
//  À placer dans : view/frontoffice/patient/
// ============================================================

require_once __DIR__ . '/../../../controller/RendezVousController.php';

// ---- Médecin affiché (hardcodé pour l'instant) ----
$medecin_id  = 1;

// Récupérer le nom du médecin via utilisateurs
$pdo = config::getConnexion();
$stmt_med = $pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_PK = :id");
$stmt_med->execute([':id' => $medecin_id]);
$med = $stmt_med->fetch();
$medecin_nom = $med ? ($med['prenom'] . ' ' . $med['nom']) : 'Médecin #' . $medecin_id;

$controller = new RendezVousController();

// ---- Calcul de la semaine affichée ----
$semaine_param = $_GET['semaine'] ?? date('Y-\WW');
$date_debut    = new DateTime();
$date_debut->setISODate(...explode('-W', $semaine_param));
$date_debut->setTime(0, 0, 0);
$date_fin = clone $date_debut;
$date_fin->modify('+4 days'); // Lundi → Vendredi

$debut_str = $date_debut->format('Y-m-d');
$fin_str   = $date_fin->format('Y-m-d');

// Navigation semaines
$sem_prec = clone $date_debut; $sem_prec->modify('-7 days');
$sem_suiv = clone $date_debut; $sem_suiv->modify('+7 days');
$url_prec = '?semaine=' . $sem_prec->format('Y-\WW');
$url_suiv = '?semaine=' . $sem_suiv->format('Y-\WW');

// Pas de semaine passée
$semaine_courante = new DateTime(); $semaine_courante->setISODate(...explode('-W', date('Y-\WW')));
$peut_aller_avant = $date_debut > $semaine_courante;

// ---- Jours de la semaine (Lun→Ven) ----
$jours = [];
for ($i = 0; $i < 5; $i++) {
    $j = clone $date_debut;
    $j->modify("+$i days");
    $jours[] = $j;
}

// ---- Créneaux horaires proposés (08:00 → 17:00, toutes les 30 min) ----
$creneaux = [];
for ($h = 8; $h < 17; $h++) {
    $creneaux[] = sprintf('%02d:00', $h);
    $creneaux[] = sprintf('%02d:30', $h);
}

// ---- Données via Controller → Model (avec JOIN utilisateurs) ----
$data       = $controller->getPlanningPatientData($medecin_id, $debut_str, $fin_str);
$rdvs_pris_raw  = $data['rdvs'];
$blocages_raw   = $data['blocages'];

// Indexer RDV pris : $pris['2026-04-15']['09:00'] = true
$pris = [];
foreach ($rdvs_pris_raw as $r) {
    $h = substr($r['heure_rdv'], 0, 5);
    $pris[$r['date_rdv']][$h] = true;
}

// Indexer les blocages : pour chaque créneau, vérifier s'il tombe dans un bloc
// $bloque['2026-04-15']['09:00'] = 'Réunion équipe'
$bloque = [];
foreach ($blocages_raw as $b) {
    $ts_debut = strtotime($b['date_debut']);
    $ts_fin   = strtotime($b['date_fin']);
    $jour_b   = date('Y-m-d', $ts_debut);

    foreach ($creneaux as $c) {
        $ts_c = strtotime("$jour_b $c:00");
        if ($ts_c >= $ts_debut && $ts_c < $ts_fin) {
            $bloque[$jour_b][$c] = $b['titre'];
        }
    }
}

// ---- Fonction : un créneau est-il disponible ? ----
function estDisponible($date, $heure, $pris, $bloque) {
    if (isset($pris[$date][$heure]))   return 'pris';
    if (isset($bloque[$date][$heure])) return 'bloque';
    return 'libre';
}

// Noms jours / mois
$noms_jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];
$noms_mois  = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$mois_court = ['','Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

$aujourd_hui = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planning Dr. <?= htmlspecialchars($medecin_nom) ?> — MediFlow</title>
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
      --bg:           #f0f4f8;
      --surface:      #ffffff;
      --surface-low:  #f5f7fa;
      --border:       #e2e8f0;
      --text:         #0f172a;
      --text-muted:   #64748b;
      --green:        #15803d;
      --green-bg:     #dcfce7;
      --green-hover:  #bbf7d0;
      --amber:        #b45309;
      --amber-bg:     #fef3c7;
      --red:          #ba1a1a;
      --red-bg:       #ffdad6;
      --shadow:       0 2px 16px rgba(0,77,153,0.08);
      --r-sm:8px; --r-md:12px; --r-lg:16px; --r-xl:20px; --r-full:9999px;
    }

    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

    /* ===== NAVBAR ===== */
    .navbar { background:rgba(255,255,255,0.95); border-bottom:1px solid var(--border); padding:0 32px; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; backdrop-filter:blur(12px); }
    .navbar-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .brand-logo { width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .brand-logo svg { width:20px; height:20px; fill:white; }
    .brand-name { font-family:'Manrope',sans-serif; font-weight:800; font-size:18px; color:#1e3a6e; }
    .btn-retour { display:flex; align-items:center; gap:7px; padding:9px 18px; background:var(--surface-low); border:1px solid var(--border); border-radius:var(--r-md); color:var(--text-muted); font-size:13px; font-weight:600; text-decoration:none; transition:all 0.15s; }
    .btn-retour:hover { background:var(--primary-light); color:var(--primary); }
    .btn-retour svg { width:16px; height:16px; }

    /* ===== PAGE ===== */
    .page-wrapper { max-width:1100px; margin:0 auto; padding:32px 24px; }

    /* ===== HERO médecin ===== */
    .medecin-hero { background:linear-gradient(135deg,#1e3a6e,var(--primary)); border-radius:var(--r-xl); padding:28px 32px; display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:28px; color:white; }
    .medecin-hero-left { display:flex; align-items:center; gap:18px; }
    .medecin-avatar { width:60px; height:60px; border-radius:var(--r-full); background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-family:'Manrope',sans-serif; font-weight:800; font-size:22px; color:white; flex-shrink:0; border:2px solid rgba(255,255,255,0.3); }
    .medecin-name { font-family:'Manrope',sans-serif; font-weight:800; font-size:20px; margin-bottom:4px; }
    .medecin-sub { font-size:13px; opacity:0.8; }
    .legende { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .legende-item { display:flex; align-items:center; gap:6px; font-size:12px; opacity:0.9; }
    .legende-dot { width:12px; height:12px; border-radius:3px; flex-shrink:0; }
    .dot-libre  { background:#84f5e8; }
    .dot-pris   { background:rgba(255,255,255,0.35); }
    .dot-bloque { background:#fbbf24; }

    /* ===== NAVIGATION SEMAINE ===== */
    .week-nav { display:flex; align-items:center; justify-content:space-between; background:var(--surface); border-radius:var(--r-lg); padding:14px 20px; box-shadow:var(--shadow); margin-bottom:20px; }
    .week-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:16px; }
    .week-sub { font-size:12px; color:var(--text-muted); margin-top:2px; }
    .nav-btns { display:flex; gap:8px; }
    .btn-nav { display:flex; align-items:center; gap:6px; padding:8px 16px; border:1px solid var(--border); border-radius:var(--r-md); background:var(--surface-low); color:var(--text-muted); font-size:13px; font-weight:600; text-decoration:none; transition:all 0.15s; cursor:pointer; }
    .btn-nav:hover:not(.disabled) { background:var(--primary-light); color:var(--primary); border-color:var(--primary-light); }
    .btn-nav.disabled { opacity:0.35; pointer-events:none; }
    .btn-nav svg { width:15px; height:15px; }
    .btn-nav-today { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border-color:transparent; }
    .btn-nav-today:hover { box-shadow:0 4px 12px rgba(0,77,153,0.3); transform:translateY(-1px); color:white; }

    /* ===== GRILLE CALENDRIER ===== */
    .calendar-wrapper { background:var(--surface); border-radius:var(--r-xl); box-shadow:var(--shadow); overflow:hidden; }

    /* Header jours */
    .cal-header { display:grid; grid-template-columns:80px repeat(5, 1fr); border-bottom:2px solid var(--border); background:var(--surface-low); }
    .cal-header-time { padding:14px 10px; text-align:center; }
    .cal-header-day { padding:14px 10px; text-align:center; border-left:1px solid var(--border); }
    .day-name { font-family:'Manrope',sans-serif; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); }
    .day-num  { font-family:'Manrope',sans-serif; font-weight:800; font-size:22px; line-height:1.1; margin-top:2px; }
    .day-mois { font-size:11px; color:var(--text-muted); margin-top:1px; }
    .cal-header-day.today { background:var(--primary-light); }
    .cal-header-day.today .day-name { color:var(--primary); }
    .cal-header-day.today .day-num  { color:var(--primary); }
    .cal-header-day.passe { opacity:0.45; }

    /* Corps calendrier */
    .cal-body { display:flex; flex-direction:column; }
    .cal-row { display:grid; grid-template-columns:80px repeat(5, 1fr); border-bottom:1px solid var(--border); }
    .cal-row:last-child { border-bottom:none; }
    .cal-row.heure-pleine { background:rgba(0,77,153,0.015); }

    /* Colonne heure */
    .cal-time { padding:8px 10px; text-align:right; font-size:11px; font-weight:600; color:var(--text-muted); display:flex; align-items:center; justify-content:flex-end; white-space:nowrap; }
    .cal-time.demi { color:transparent; font-size:9px; }
    .cal-time.demi::after { content:'·'; color:var(--border); }

    /* Cellule créneau */
    .cal-cell { border-left:1px solid var(--border); padding:4px 6px; display:flex; align-items:center; justify-content:center; min-height:36px; }

    /* États des créneaux */
    .slot { width:100%; border-radius:var(--r-sm); padding:5px 8px; font-size:12px; font-weight:600; text-align:center; transition:all 0.15s; display:flex; align-items:center; justify-content:center; gap:4px; }

    /* Libre → cliquable → redirige vers rdv.php */
    .slot.libre { background:var(--green-bg); color:var(--green); cursor:pointer; text-decoration:none; border:1px solid transparent; }
    .slot.libre:hover { background:var(--green-hover); transform:scale(1.04); box-shadow:0 2px 8px rgba(21,128,61,0.2); border-color:var(--green); }
    .slot.libre svg { width:11px; height:11px; }

    /* Pris → grisé */
    .slot.pris { background:var(--surface-low); color:var(--text-muted); cursor:not-allowed; border:1px solid var(--border); font-size:11px; }

    /* Bloqué (planning médecin) → amber */
    .slot.bloque { background:var(--amber-bg); color:var(--amber); cursor:not-allowed; border:1px solid transparent; font-size:10px; text-align:center; line-height:1.2; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }

    /* Passé → invisible */
    .slot.passe { background:transparent; cursor:default; }

    /* Jour passé → colonne entière grisée */
    .cal-cell.jour-passe .slot { opacity:0.3; pointer-events:none; }

    /* ===== POPUP sélection créneau ===== */
    .popup-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
    .popup-overlay.open { display:flex; }
    .popup { background:var(--surface); border-radius:var(--r-xl); padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:popIn 0.2s ease; }
    @keyframes popIn { from { transform:scale(0.92); opacity:0; } to { transform:scale(1); opacity:1; } }
    .popup-icon { width:52px; height:52px; border-radius:var(--r-full); background:var(--green-bg); display:flex; align-items:center; justify-content:center; margin-bottom:16px; }
    .popup-icon svg { width:26px; height:26px; color:var(--green); }
    .popup-title { font-family:'Manrope',sans-serif; font-weight:800; font-size:18px; margin-bottom:6px; }
    .popup-sub { font-size:13px; color:var(--text-muted); margin-bottom:20px; }
    .popup-recap { background:var(--surface-low); border-radius:var(--r-lg); padding:16px; margin-bottom:24px; }
    .popup-row { display:flex; justify-content:space-between; font-size:13px; padding:6px 0; border-bottom:1px solid var(--border); }
    .popup-row:last-child { border-bottom:none; }
    .popup-label { color:var(--text-muted); font-weight:500; }
    .popup-val { font-weight:700; }
    .popup-actions { display:flex; gap:10px; }
    .btn-popup-cancel { flex:1; padding:11px; background:var(--surface-low); border:1px solid var(--border); border-radius:var(--r-md); font-family:'Inter',sans-serif; font-weight:600; font-size:14px; cursor:pointer; color:var(--text-muted); transition:background 0.15s; }
    .btn-popup-cancel:hover { background:var(--border); }
    .btn-popup-confirm { flex:2; padding:11px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 3px 10px rgba(0,77,153,0.3); transition:all 0.15s; }
    .btn-popup-confirm:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(0,77,153,0.4); }

    @media (max-width:700px) {
      .cal-header, .cal-row { grid-template-columns:50px repeat(5,1fr); }
      .cal-time { font-size:9px; padding:4px 4px; }
      .medecin-hero { flex-direction:column; align-items:flex-start; }
      .legende { justify-content:flex-start; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="annuaire.php" class="navbar-brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 3a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H8a1 1 0 110-2h3V7a1 1 0 011-1z"/></svg>
    </div>
    <span class="brand-name">MediFlow</span>
  </a>
  <a href="annuaire.php" class="btn-retour">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Retour à l'annuaire
  </a>
</nav>

<div class="page-wrapper">

  <!-- HERO MÉDECIN -->
  <div class="medecin-hero">
    <div class="medecin-hero-left">
      <div class="medecin-avatar">
        <?= strtoupper(substr(explode(' ', $medecin_nom)[0], 0, 1) . substr(explode(' ', $medecin_nom)[1] ?? '', 0, 1)) ?>
      </div>
      <div>
        <div class="medecin-name">Dr. <?= htmlspecialchars($medecin_nom) ?></div>
        <div class="medecin-sub">Cliquez sur un créneau vert pour prendre rendez-vous</div>
      </div>
    </div>
    <div class="legende">
      <div class="legende-item"><div class="legende-dot dot-libre"></div> Disponible</div>
      <div class="legende-item"><div class="legende-dot dot-pris"></div> Déjà pris</div>
      <div class="legende-item"><div class="legende-dot dot-bloque"></div> Indisponible</div>
    </div>
  </div>

  <!-- NAVIGATION SEMAINE -->
  <div class="week-nav">
    <div>
      <div class="week-title">
        Semaine du <?= $date_debut->format('d') ?> au <?= $date_fin->format('d') ?>
        <?= $noms_mois[(int)$date_fin->format('m')] ?> <?= $date_fin->format('Y') ?>
      </div>
      <div class="week-sub">Horaires de consultation : 08h00 – 17h00</div>
    </div>
    <div class="nav-btns">
      <a href="<?= $url_prec ?>" class="btn-nav <?= !$peut_aller_avant ? 'disabled' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Semaine préc.
      </a>
      <a href="?" class="btn-nav btn-nav-today">Aujourd'hui</a>
      <a href="<?= $url_suiv ?>>" class="btn-nav">
        Semaine suiv.
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>

  <!-- CALENDRIER -->
  <div class="calendar-wrapper">

    <!-- Header : noms des jours -->
    <div class="cal-header">
      <div class="cal-header-time"></div>
      <?php foreach ($jours as $i => $jour):
        $date_str  = $jour->format('Y-m-d');
        $est_auj   = $date_str === $aujourd_hui;
        $est_passe = $date_str < $aujourd_hui;
        $cls = $est_auj ? 'today' : ($est_passe ? 'passe' : '');
      ?>
      <div class="cal-header-day <?= $cls ?>">
        <div class="day-name"><?= $noms_jours[$i] ?></div>
        <div class="day-num"><?= $jour->format('d') ?></div>
        <div class="day-mois"><?= $mois_court[(int)$jour->format('m')] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Corps : créneaux -->
    <div class="cal-body">
      <?php foreach ($creneaux as $c):
        $is_heure_pleine = substr($c, 3, 2) === '00';
      ?>
      <div class="cal-row <?= $is_heure_pleine ? 'heure-pleine' : '' ?>">

        <!-- Colonne heure -->
        <div class="cal-time <?= !$is_heure_pleine ? 'demi' : '' ?>">
          <?= $is_heure_pleine ? $c : '' ?>
        </div>

        <!-- 5 cellules (Lun → Ven) -->
        <?php foreach ($jours as $jour):
          $date_str  = $jour->format('Y-m-d');
          $est_passe = $date_str < $aujourd_hui;

          // Créneau passé aujourd'hui ?
          $est_passe_heure = false;
          if ($date_str === $aujourd_hui) {
            $ts_now = time();
            $ts_creneau = strtotime("$date_str $c:00");
            $est_passe_heure = $ts_creneau <= $ts_now;
          }
          $est_passe_total = $est_passe || $est_passe_heure;

          $etat = estDisponible($date_str, $c, $pris, $bloque);
        ?>
        <div class="cal-cell <?= $est_passe_total ? 'jour-passe' : '' ?>">
          <?php if ($est_passe_total): ?>
            <div class="slot passe"></div>

          <?php elseif ($etat === 'libre'): ?>
            <!-- Créneau dispo → cliquable → popup -->
            <div class="slot libre"
                 onclick="ouvrirPopup('<?= $date_str ?>', '<?= $c ?>', '<?= $jour->format('d') . ' ' . $noms_mois[(int)$jour->format('m')] . ' ' . $jour->format('Y') ?>', '<?= $noms_jours[array_search($jour, $jours)] ?>')"
                 title="Cliquer pour réserver ce créneau">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <?= $c ?>
            </div>

          <?php elseif ($etat === 'pris'): ?>
            <div class="slot pris">Réservé</div>

          <?php else: // bloqué ?>
            <div class="slot bloque" title="<?= htmlspecialchars($bloque[$date_str][$c] ?? '') ?>">
              <?= htmlspecialchars(mb_strimwidth($bloque[$date_str][$c] ?? 'Indisponible', 0, 12, '…')) ?>
            </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

      </div>
      <?php endforeach; ?>
    </div>

  </div><!-- /calendar-wrapper -->

</div><!-- /page-wrapper -->

<!-- ===== POPUP CONFIRMATION CRÉNEAU ===== -->
<div class="popup-overlay" id="popupOverlay">
  <div class="popup">
    <div class="popup-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <div class="popup-title">Confirmer ce créneau ?</div>
    <div class="popup-sub">Vous allez être redirigé vers le formulaire de rendez-vous.</div>
    <div class="popup-recap">
      <div class="popup-row">
        <span class="popup-label">Médecin</span>
        <span class="popup-val">Dr. <?= htmlspecialchars($medecin_nom) ?></span>
      </div>
      <div class="popup-row">
        <span class="popup-label">Jour</span>
        <span class="popup-val" id="popupJour">—</span>
      </div>
      <div class="popup-row">
        <span class="popup-label">Date</span>
        <span class="popup-val" id="popupDate">—</span>
      </div>
      <div class="popup-row">
        <span class="popup-label">Heure</span>
        <span class="popup-val" id="popupHeure">—</span>
      </div>
    </div>
    <div class="popup-actions">
      <button class="btn-popup-cancel" onclick="fermerPopup()">Annuler</button>
      <button class="btn-popup-confirm" id="btnConfirm">
        Remplir le formulaire →
      </button>
    </div>
  </div>
</div>

<script>
let selectedDate  = '';
let selectedHeure = '';

function ouvrirPopup(date, heure, dateFr, jourFr) {
  selectedDate  = date;
  selectedHeure = heure;

  document.getElementById('popupJour').textContent  = jourFr;
  document.getElementById('popupDate').textContent  = dateFr;
  document.getElementById('popupHeure').textContent = heure;

  // Lien vers rdv.php avec date + heure pré-remplies
  document.getElementById('btnConfirm').onclick = function() {
    window.location.href =
      'rdv.php?medecin_id=<?= $medecin_id ?>'
      + '&nom=<?= urlencode($medecin_nom) ?>'
      + '&date_rdv=' + selectedDate
      + '&heure_rdv=' + selectedHeure;
  };

  document.getElementById('popupOverlay').classList.add('open');
}

function fermerPopup() {
  document.getElementById('popupOverlay').classList.remove('open');
}

// Fermer si clic en dehors
document.getElementById('popupOverlay').addEventListener('click', function(e) {
  if (e.target === this) fermerPopup();
});
</script>
</body>
</html>