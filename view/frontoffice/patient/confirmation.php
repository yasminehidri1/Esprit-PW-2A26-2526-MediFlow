<?php
// ============================================================
//  confirmation.php — View (patient)
//  Appelle le Controller pour récupérer les infos du RDV
// ============================================================

require_once __DIR__ . '/../../../controller/RendezVousController.php';

$controller = new RendezVousController();

$rdv_id = isset($_GET['rdv_id']) ? intval($_GET['rdv_id']) : 0;

if ($rdv_id === 0) {
    header('Location: annuaire.php');
    exit;
}

// Le controller appelle le Model et retourne les données
$rdv = $controller->getRdvById($rdv_id);

if (!$rdv) {
    header('Location: annuaire.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RDV Confirmé — MediFlow</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,600,700,800|inter:400,500,600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary: #004d99; --primary-dark: #1565c0; --primary-light: #d6e3ff;
      --teal: #005851; --teal-light: #84f5e8;
      --bg: #f0f4f8; --surface: #ffffff;
      --border: #e2e8f0; --text: #0f172a; --text-muted: #64748b;
      --r-md: 12px; --r-lg: 16px; --r-xl: 20px;
    }
    body { font-family:'Inter',sans-serif; background:var(--bg); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .confirm-card { background:var(--surface); border-radius:var(--r-xl); padding:48px 40px; max-width:480px; width:100%; box-shadow:0 8px 32px rgba(0,77,153,0.10); text-align:center; }
    .check-circle { width:72px; height:72px; border-radius:50%; background:rgba(132,245,232,0.25); display:flex; align-items:center; justify-content:center; margin:0 auto 24px; }
    .check-circle svg { width:36px; height:36px; color:var(--teal); }
    h1 { font-family:'Manrope',sans-serif; font-size:24px; font-weight:800; margin-bottom:8px; }
    .subtitle { font-size:14px; color:var(--text-muted); margin-bottom:32px; }
    .rdv-recap { background:var(--bg); border-radius:var(--r-lg); padding:20px; text-align:left; margin-bottom:32px; }
    .recap-row { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid var(--border); font-size:14px; }
    .recap-row:last-child { border-bottom:none; }
    .recap-label { color:var(--text-muted); font-weight:500; }
    .recap-value { font-weight:700; }
    .badge-attente { display:inline-block; padding:3px 10px; border-radius:99px; background:var(--primary-light); color:var(--primary); font-size:11px; font-weight:700; text-transform:uppercase; }
    .btn-retour { display:inline-flex; align-items:center; gap:8px; padding:13px 28px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:white; border:none; border-radius:var(--r-md); font-family:'Manrope',sans-serif; font-weight:700; font-size:14px; cursor:pointer; text-decoration:none; box-shadow:0 3px 12px rgba(0,77,153,0.25); transition:all 0.18s; }
    .btn-retour:hover { transform:translateY(-1px); }
    .btn-retour svg { width:16px; height:16px; }
  </style>
</head>
<body>
<div class="confirm-card">

  <div class="check-circle">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
  </div>

  <h1>Demande envoyée !</h1>
  <p class="subtitle">Votre rendez-vous est en attente de confirmation par le médecin.</p>

  <!-- Données venant du Controller → Model → BDD -->
  <div class="rdv-recap">
    <div class="recap-row">
      <span class="recap-label">Patient</span>
      <span class="recap-value"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></span>
    </div>
    <div class="recap-row">
      <span class="recap-label">Date</span>
      <span class="recap-value"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></span>
    </div>
    <div class="recap-row">
      <span class="recap-label">Heure</span>
      <span class="recap-value"><?= date('H:i', strtotime($rdv['heure_rdv'])) ?></span>
    </div>
    <div class="recap-row">
      <span class="recap-label">Statut</span>
      <span class="recap-value"><span class="badge-attente">En attente</span></span>
    </div>
  </div>

  <a href="annuaire.php" class="btn-retour">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Retour à l'annuaire
  </a>

</div>
</body>
</html>