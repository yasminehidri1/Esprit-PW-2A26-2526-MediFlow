<?php
// ============================================================
//  rdv.php — Formulaire prise de RDV + confirmation intégrée
//  GET  : affiche le formulaire
//  POST : INSERT en BDD → affiche confirmation dans la même page
// ============================================================
require_once __DIR__ . '/../../../config.php';

// Récupération des infos médecin depuis l'URL
$medecin_id  = isset($_GET['medecin_id'])  ? intval($_GET['medecin_id'])                  : 0;
$medecin_nom = isset($_GET['nom'])         ? htmlspecialchars(trim($_GET['nom']))         : '';
$medecin_spe = isset($_GET['specialite'])  ? htmlspecialchars(trim($_GET['specialite'])) : '';

// Créneau pré-sélectionné (venant de planning-patient.php)
$prefill_date  = isset($_GET['date_rdv'])  ? $_GET['date_rdv']  : '';
$prefill_heure = isset($_GET['heure_rdv']) ? $_GET['heure_rdv'] : '';
$depuis_planning = ($prefill_date !== '' && $prefill_heure !== '');

// Si pas de médecin → retour annuaire
if ($medecin_id === 0 || $medecin_nom === '') {
    header('Location: annuaire.php');
    exit;
}

// Variables pour l'état de la page
$rdv_confirme = false;   // true = afficher la confirmation
$rdv_data     = [];      // données du RDV inséré
$erreurs      = [];      // erreurs de validation

// ============================================================
//  POST → validation + INSERT PDO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo = config::getConnexion();

    // Récupération des champs
    $patient_nom    = htmlspecialchars(trim($_POST['nom']     ?? ''));
    $patient_prenom = htmlspecialchars(trim($_POST['prenom']  ?? ''));
    $cin            = trim($_POST['cin']      ?? '');
    $genre          = $_POST['genre']         ?? '';
    $date_rdv       = $_POST['date_rdv']      ?? '';
    $heure_rdv      = $_POST['heure_rdv']     ?? '';
    $mid            = intval($_POST['medecin_id'] ?? 0);

    // ---- Validation côté serveur ----
    if (empty($patient_nom))
        $erreurs[] = "Le nom est requis.";
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $patient_nom))
        $erreurs[] = "Le nom ne doit contenir que des lettres.";

    if (empty($patient_prenom))
        $erreurs[] = "Le prénom est requis.";
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $patient_prenom))
        $erreurs[] = "Le prénom ne doit contenir que des lettres.";

    if (empty($cin))
        $erreurs[] = "Le CIN est requis.";
    elseif (!preg_match('/^[0-9]{8}$/', $cin))
        $erreurs[] = "Le CIN doit contenir exactement 8 chiffres.";

    if (!in_array($genre, ['homme','femme']))
        $erreurs[] = "Veuillez sélectionner un genre.";

    if (empty($date_rdv))
        $erreurs[] = "La date est requise.";
    elseif ($date_rdv < date('Y-m-d'))
        $erreurs[] = "La date ne peut pas être dans le passé.";

    if (empty($heure_rdv))
        $erreurs[] = "L'heure est requise.";

    // ---- Si tout est valide → INSERT ----
    if (empty($erreurs)) {
        $sql = "INSERT INTO rendez_vous
                    (medecin_id, patient_nom, patient_prenom, cin, genre, date_rdv, heure_rdv, statut)
                VALUES
                    (:mid, :nom, :prenom, :cin, :genre, :date, :heure, 'en_attente')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':mid'    => $mid,
            ':nom'    => $patient_nom,
            ':prenom' => $patient_prenom,
            ':cin'    => $cin,
            ':genre'  => $genre,
            ':date'   => $date_rdv,
            ':heure'  => $heure_rdv,
        ]);

        // On passe en mode confirmation
        $rdv_confirme = true;
        $rdv_data = [
            'prenom'  => $patient_prenom,
            'nom'     => $patient_nom,
            'date'    => date('d/m/Y', strtotime($date_rdv)),
            'heure'   => date('H:i', strtotime($heure_rdv)),
            'medecin' => $medecin_nom,
            'spe'     => $medecin_spe,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouveau Rendez-vous — MediFlow</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|inter:400,500,600,700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary: #004d99;
      --primary-dark: #1565c0;
      --primary-light: #d6e3ff;
      --teal: #005851;
      --teal-light: #84f5e8;
      --teal-bg: rgba(0,88,81,0.10);
      --bg: #f0f4f8;
      --surface: #ffffff;
      --surface-low: #f5f7fa;
      --border: #e2e8f0;
      --text: #0f172a;
      --text-muted: #64748b;
      --error: #ba1a1a;
      --shadow: 0 2px 16px rgba(0,77,153,0.08);
      --shadow-hover: 0 8px 32px rgba(0,77,153,0.15);
      --r-sm: 8px; --r-md: 12px; --r-lg: 16px; --r-xl: 20px; --r-full: 9999px;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    /* ===== NAVBAR ===== */
    .navbar {
      background: rgba(255,255,255,0.95);
      border-bottom: 1px solid var(--border);
      padding: 0 32px;
      height: 68px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(12px);
    }

    .navbar-left {
      display: flex;
      align-items: center;
      gap: 32px;
    }

    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .brand-logo {
      width: 40px; height: 40px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
    }

    .brand-logo svg { width: 22px; height: 22px; fill: white; }

    .brand-name {
      font-family: 'Manrope', sans-serif;
      font-weight: 800;
      font-size: 18px;
      color: #1e3a6e;
    }

    .navbar-links {
      display: flex;
      gap: 4px;
      list-style: none;
    }

    .navbar-links a {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: var(--r-md);
      color: var(--text-muted);
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.15s;
    }

    .navbar-links a svg { width: 18px; height: 18px; }
    .navbar-links a:hover { background: rgba(0,77,153,0.05); color: var(--primary); }
    .navbar-links a.active { background: var(--primary-light); color: var(--primary); font-weight: 700; }

    .navbar-right { display: flex; align-items: center; gap: 8px; }

    .icon-btn {
      width: 38px; height: 38px;
      border: none; background: transparent;
      border-radius: var(--r-md);
      display: flex; align-items: center; justify-content: center;
      color: var(--text-muted); cursor: pointer;
      transition: background 0.15s;
    }

    .icon-btn:hover { background: var(--surface-low); }
    .icon-btn svg { width: 20px; height: 20px; }

    .avatar-btn {
      width: 36px; height: 36px;
      border-radius: var(--r-full);
      background: linear-gradient(135deg, var(--primary-light), #c0d5ff);
      border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
    }

    .avatar-btn svg { width: 18px; height: 18px; fill: var(--primary); }

    /* ===== PAGE ===== */
    .page-wrapper {
      max-width: 960px;
      margin: 0 auto;
      padding: 40px 32px;
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 28px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .breadcrumb a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb svg { width: 14px; height: 14px; }

    /* Page title */
    .page-title {
      font-family: 'Manrope', sans-serif;
      font-size: 28px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 6px;
    }

    .page-subtitle {
      font-size: 15px;
      color: var(--text-muted);
      margin-bottom: 32px;
      line-height: 1.5;
    }

    /* ===== LAYOUT ===== */
    .rdv-grid {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 28px;
      align-items: start;
    }

    /* Left column */
    .rdv-context { display: flex; flex-direction: column; gap: 18px; }

    .selected-doctor-card {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: var(--r-xl);
      padding: 22px;
      color: white;
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .doc-no-photo {
      width: 56px; height: 56px;
      border-radius: var(--r-full);
      background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .doc-no-photo svg { width: 26px; height: 26px; fill: white; }
    .doc-info .label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75; margin-bottom: 3px; }
    .doc-info .name { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 16px; }
    .doc-info .spe { font-size: 12px; opacity: 0.8; margin-top: 2px; }

    .info-card {
      background: var(--surface);
      border-radius: var(--r-xl);
      padding: 22px;
      box-shadow: var(--shadow);
      border-left: 4px solid var(--teal-light);
    }

    .info-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }

    .info-card-icon {
      width: 36px; height: 36px;
      background: var(--teal-bg);
      border-radius: var(--r-sm);
      display: flex; align-items: center; justify-content: center;
    }

    .info-card-icon svg { width: 18px; height: 18px; color: var(--teal); }
    .info-card h3 { font-family: 'Manrope', sans-serif; font-size: 15px; font-weight: 700; color: var(--text); }
    .info-card p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }

    /* ===== FORMULAIRE ===== */
    .rdv-form-card {
      background: var(--surface);
      border-radius: var(--r-xl);
      padding: 36px;
      box-shadow: var(--shadow);
    }

    .form-section { margin-bottom: 28px; }
    .form-section:last-child { margin-bottom: 0; }

    .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
    .section-bar { width: 4px; height: 24px; background: linear-gradient(to bottom, var(--teal), var(--teal-light)); border-radius: 2px; }
    .section-header h2 { font-family: 'Manrope', sans-serif; font-size: 18px; font-weight: 700; color: var(--text); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .form-row.full { grid-template-columns: 1fr; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .form-label {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-muted);
    }

    .form-input {
      width: 100%;
      background: var(--surface-low);
      border: 2px solid transparent;
      border-radius: var(--r-md);
      padding: 12px 16px;
      font-size: 14px;
      font-family: 'Inter', sans-serif;
      color: var(--text);
      outline: none;
      transition: all 0.18s;
    }

    .form-input:focus { border-color: var(--teal); background: white; box-shadow: 0 0 0 3px rgba(0,88,81,0.08); }
    .form-input::placeholder { color: #94a3b8; }
    .form-input.is-error { border-color: var(--error); }

    .field-error { font-size: 11px; color: var(--error); margin-top: 3px; display: none; }

    /* Genre radio */
    .genre-options { display: flex; gap: 12px; }
    .genre-label { flex: 1; cursor: pointer; }
    .genre-label input[type="radio"] { display: none; }

    .genre-box {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px;
      background: var(--surface-low);
      border-radius: var(--r-md);
      border: 2px solid transparent;
      font-weight: 600;
      font-size: 14px;
      color: var(--text-muted);
      transition: all 0.18s;
    }

    .genre-box svg { width: 18px; height: 18px; }
    .genre-label input[type="radio"]:checked + .genre-box {
      background: white;
      border-color: var(--primary);
      color: var(--primary);
      box-shadow: 0 2px 8px rgba(0,77,153,0.12);
    }

    .genre-box:hover { background: white; border-color: var(--border); }

    .input-wrapper { position: relative; }
    .input-wrapper .form-input { padding-right: 42px; }
    .input-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .input-icon svg { width: 16px; height: 16px; }

    .section-divider { border: none; border-top: 1px solid var(--border); margin: 28px 0; }

    /* Actions */
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 12px; }

    .btn-cancel {
      padding: 12px 24px;
      background: transparent;
      border: 1.5px solid var(--border);
      color: var(--text-muted);
      font-family: 'Manrope', sans-serif;
      font-weight: 600;
      font-size: 14px;
      border-radius: var(--r-md);
      cursor: pointer;
      transition: all 0.15s;
    }

    .btn-cancel:hover { border-color: var(--text-muted); color: var(--text); }

    .btn-confirm {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      border: none;
      border-radius: var(--r-md);
      font-family: 'Manrope', sans-serif;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      box-shadow: 0 3px 12px rgba(0,77,153,0.30);
      transition: all 0.18s;
    }

    .btn-confirm svg { width: 18px; height: 18px; }
    .btn-confirm:hover { box-shadow: 0 5px 20px rgba(0,77,153,0.40); transform: translateY(-1px); }
    .btn-confirm:active { transform: scale(0.97); }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .navbar { padding: 0 16px; }
      .navbar-links { display: none; }
      .page-wrapper { padding: 24px 16px; }
      .rdv-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .genre-options { flex-direction: column; }
      .form-actions { flex-direction: column; }
      .btn-cancel, .btn-confirm { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>

<?php if ($rdv_confirme): ?>
<!-- ===== PAGE CONFIRMATION ===== -->
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);padding:24px;">
  <div style="background:white;border-radius:var(--r-xl);padding:48px 40px;max-width:460px;width:100%;box-shadow:0 8px 32px rgba(0,77,153,0.10);text-align:center;">

    <!-- Icône succès -->
    <div style="width:72px;height:72px;border-radius:50%;background:rgba(132,245,232,0.25);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="#005851" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:36px;height:36px;">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>

    <h1 style="font-family:'Manrope',sans-serif;font-size:24px;font-weight:800;margin-bottom:8px;">Demande envoyée !</h1>
    <p style="font-size:14px;color:var(--text-muted);margin-bottom:32px;">Votre rendez-vous est en attente de confirmation par le médecin.</p>

    <!-- Récapitulatif -->
    <div style="background:var(--bg);border-radius:var(--r-lg);padding:20px;text-align:left;margin-bottom:32px;">
      <?php
        $recap = [
          'Patient'  => $rdv_data['prenom'].' '.$rdv_data['nom'],
          'Médecin'  => 'Dr. '.$rdv_data['medecin'].' ('.$rdv_data['spe'].')',
          'Date'     => $rdv_data['date'],
          'Heure'    => $rdv_data['heure'],
          'Statut'   => 'En attente',
        ];
        foreach ($recap as $label => $valeur):
      ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--border);font-size:14px;">
        <span style="color:var(--text-muted);font-weight:500;"><?= $label ?></span>
        <span style="font-weight:700;<?= $label==='Statut' ? 'color:var(--primary);' : '' ?>">
          <?= $label==='Statut' ? '<span style="background:var(--primary-light);padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;">En attente</span>' : htmlspecialchars($valeur) ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>

    <a href="annuaire.php" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;border-radius:var(--r-md);font-family:'Manrope',sans-serif;font-weight:700;font-size:14px;text-decoration:none;box-shadow:0 3px 12px rgba(0,77,153,0.25);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Retour à l'annuaire
    </a>
  </div>
</div>

<?php else: ?>
<!-- ===== PAGE FORMULAIRE ===== -->

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
  <div class="navbar-left">
    <a href="index.php" class="navbar-brand">
      <div class="brand-logo">
        <svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg>
      </div>
      <span class="brand-name">MediFlow</span>
    </a>
    <div class="navbar-links">
      <a href="profil.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mon Profil
      </a>
      <a href="mes-rdv.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Mes RDV
      </a>
      <a href="annuaire.php" class="active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Annuaire
      </a>
    </div>
  </div>
  <div class="navbar-right">
    <button class="icon-btn" title="Notifications">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </button>
    <button class="avatar-btn" title="Profil">
      <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
    </button>
  </div>
</nav>

<!-- ===== PAGE ===== -->
<div class="page-wrapper">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <a href="annuaire.php">Annuaire</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Nouveau rendez-vous</span>
  </div>

  <h1 class="page-title">Nouveau Rendez-vous</h1>
  <p class="page-subtitle">Complétez les informations pour confirmer votre consultation.</p>

  <div class="rdv-grid">

    <!-- Colonne gauche -->
    <div class="rdv-context">
      <!-- Médecin sélectionné — données reçues depuis annuaire.php via l'URL -->
      <div class="selected-doctor-card">
        <div class="doc-no-photo">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
        </div>
        <div class="doc-info">
          <div class="label">Médecin sélectionné</div>
          <div class="name">Dr. <?= $medecin_nom ?></div>
          <div class="spe"><?= $medecin_spe ?></div>
        </div>
      </div>

      <!-- Instructions -->
      <div class="info-card">
        <div class="info-card-header">
          <div class="info-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          </div>
          <h3>Instructions</h3>
        </div>
        <p>Veuillez vous assurer que les informations correspondent à votre document d'identité officiel (CIN). Les créneaux horaires sont sujets à confirmation par le praticien.</p>
      </div>
    </div>

    <!-- Colonne droite - Formulaire -->
    <div class="rdv-form-card">
      <!-- Le formulaire poste sur lui-même (même URL) -->
      <form id="rdvForm" action="rdv.php?medecin_id=<?= $medecin_id ?>&nom=<?= urlencode($medecin_nom) ?>&specialite=<?= urlencode($medecin_spe) ?>" method="POST" novalidate>

        <input type="hidden" name="medecin_id" value="<?= $medecin_id ?>">

        <!-- Affichage des erreurs serveur -->
        <?php if (!empty($erreurs)): ?>
        <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:var(--r-md);padding:13px 16px;margin-bottom:20px;font-size:13px;color:var(--error);">
          <strong>Veuillez corriger les erreurs suivantes :</strong>
          <ul style="margin-top:6px;padding-left:18px;">
            <?php foreach ($erreurs as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- Section 1 : Infos personnelles -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-bar"></div>
            <h2>Informations Personnelles</h2>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Nom</label>
              <input class="form-input" type="text" name="nom" placeholder="Entrez votre nom" required>
              <span class="field-error" id="err-nom">Ce champ est requis.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Prénom</label>
              <input class="form-input" type="text" name="prenom" placeholder="Entrez votre prénom" required>
              <span class="field-error" id="err-prenom">Ce champ est requis.</span>
            </div>
          </div>

          <div class="form-row full">
            <div class="form-group">
              <label class="form-label">CIN (Numéro d'Identité)</label>
              <input class="form-input" type="text" name="cin" placeholder="Ex: AB123456" required>
              <span class="field-error" id="err-cin">Ce champ est requis.</span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Genre</label>
            <div class="genre-options">
              <label class="genre-label">
                <input type="radio" name="genre" value="homme">
                <div class="genre-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="4"/><path d="M17 3h4v4"/><line x1="21" y1="3" x2="15" y2="9"/><line x1="12" y1="15" x2="12" y2="21"/></svg>
                  Homme
                </div>
              </label>
              <label class="genre-label">
                <input type="radio" name="genre" value="femme">
                <div class="genre-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="4"/><line x1="12" y1="13" x2="12" y2="21"/><line x1="9" y1="18" x2="15" y2="18"/></svg>
                  Femme
                </div>
              </label>
            </div>
          </div>
        </div>

        <hr class="section-divider">

        <!-- Section 2 : Planification -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-bar"></div>
            <h2>Planification</h2>
          </div>

          <?php if ($depuis_planning): ?>
          <div style="display:flex;align-items:center;gap:10px;background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:13px;font-weight:600;color:#15803d;">
              Créneau sélectionné : <strong><?= date('d/m/Y', strtotime($prefill_date)) ?> à <?= htmlspecialchars($prefill_heure) ?></strong>
              — <a href="planning-patient.php" style="color:#15803d;text-decoration:underline;">Modifier le créneau</a>
            </span>
          </div>
          <?php endif; ?>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Date du rendez-vous</label>
              <div class="input-wrapper">
                <input class="form-input" type="date" name="date_rdv" required id="dateInput"
                       value="<?= htmlspecialchars($prefill_date) ?>"
                       <?= $depuis_planning ? 'readonly style="background:#f0f4f8;cursor:not-allowed;border-color:#84f5e8;"' : '' ?>>
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
              </div>
              <span class="field-error" id="err-date">Veuillez choisir une date.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Heure</label>
              <div class="input-wrapper">
                <input class="form-input" type="time" name="heure_rdv" required id="timeInput"
                       value="<?= htmlspecialchars($prefill_heure) ?>"
                       <?= $depuis_planning ? 'readonly style="background:#f0f4f8;cursor:not-allowed;border-color:#84f5e8;"' : '' ?>>
                <span class="input-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
              </div>
              <span class="field-error" id="err-heure">Veuillez choisir une heure.</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button type="button" class="btn-cancel" onclick="history.back()">Annuler</button>
          <button type="submit" class="btn-confirm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Confirmer le RDV
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

<script>

  // ============================================================
  //  VALIDATION DU FORMULAIRE — MediFlow RDV
  //  Règles :
  //    NOM / PRÉNOM  → lettres et espaces uniquement (pas de chiffre, pas de caractère spécial)
  //    CIN           → exactement 8 chiffres (pas de lettre, pas de caractère spécial)
  //    DATE          → obligatoire + ne peut pas être dans le passé
  //    HEURE         → obligatoire
  // ============================================================


  // ---- Fonctions utilitaires ----

  // Affiche un message d'erreur sous un champ
  function afficherErreur(champElement, messageElement, message) {
    champElement.classList.add('is-error');
    messageElement.textContent = message;
    messageElement.style.display = 'block';
  }

  // Efface l'erreur d'un champ
  function effacerErreur(champElement, messageElement) {
    champElement.classList.remove('is-error');
    messageElement.style.display = 'none';
  }


  // ---- Validation du NOM ----
  function validerNom(champ, erreurElement) {

    const valeur = champ.value.trim();

    // Vide ?
    if (valeur === '') {
      afficherErreur(champ, erreurElement, 'Ce champ est requis.');
      return false;
    }

    // Contient un chiffre ou un caractère spécial ?
    // On autorise : lettres (y compris accents), espaces, tirets (ex : Ben-Ali)
    const regexNom = /^[a-zA-ZÀ-ÿ\s\-]+$/;
    if (!regexNom.test(valeur)) {
      afficherErreur(champ, erreurElement, 'Le nom ne doit contenir que des lettres (pas de chiffres ni de caractères spéciaux).');
      return false;
    }

    // Trop court ? (minimum 2 lettres)
    if (valeur.length < 2) {
      afficherErreur(champ, erreurElement, 'Le nom doit contenir au moins 2 caractères.');
      return false;
    }

    effacerErreur(champ, erreurElement);
    return true;
  }


  // ---- Validation du CIN ----
  function validerCIN(champ, erreurElement) {

    const valeur = champ.value.trim();

    // Vide ?
    if (valeur === '') {
      afficherErreur(champ, erreurElement, 'Ce champ est requis.');
      return false;
    }

    // Contient autre chose que des chiffres ?
    const regexChiffres = /^[0-9]+$/;
    if (!regexChiffres.test(valeur)) {
      afficherErreur(champ, erreurElement, 'Le CIN doit contenir uniquement des chiffres.');
      return false;
    }

    // Pas exactement 8 chiffres ?
    if (valeur.length !== 8) {
      afficherErreur(champ, erreurElement, 'Le CIN doit contenir exactement 8 chiffres.');
      return false;
    }

    effacerErreur(champ, erreurElement);
    return true;
  }


  // ---- Validation de la DATE ----
  function validerDate(champ, erreurElement) {

    const valeur = champ.value;

    // Vide ?
    if (valeur === '') {
      afficherErreur(champ, erreurElement, 'Veuillez choisir une date.');
      return false;
    }

    // Date dans le passé ?
    const dateChoisie   = new Date(valeur);
    const aujourdhui    = new Date();
    aujourdhui.setHours(0, 0, 0, 0); // on ignore l'heure pour comparer seulement les jours

    if (dateChoisie < aujourdhui) {
      afficherErreur(champ, erreurElement, 'La date ne peut pas être dans le passé.');
      return false;
    }

    effacerErreur(champ, erreurElement);
    return true;
  }


  // ---- Validation de l'HEURE ----
  function validerHeure(champ, erreurElement) {

    const valeur = champ.value;

    // Vide ?
    if (valeur === '') {
      afficherErreur(champ, erreurElement, 'Veuillez choisir une heure.');
      return false;
    }

    // Heure dans les plages de consultation : 08:00 – 18:00
    const [heures, minutes] = valeur.split(':').map(Number);
    if (heures < 8 || heures >= 18) {
      afficherErreur(champ, erreurElement, 'L\'heure doit être entre 08:00 et 18:00.');
      return false;
    }

    effacerErreur(champ, erreurElement);
    return true;
  }


  // ---- Récupération des éléments du formulaire ----
  const champNom     = document.querySelector('[name="nom"]');
  const errNom       = document.getElementById('err-nom');

  const champPrenom  = document.querySelector('[name="prenom"]');
  const errPrenom    = document.getElementById('err-prenom');

  const champCIN     = document.querySelector('[name="cin"]');
  const errCIN       = document.getElementById('err-cin');

  const champDate    = document.getElementById('dateInput');
  const errDate      = document.getElementById('err-date');

  const champHeure   = document.getElementById('timeInput');
  const errHeure     = document.getElementById('err-heure');


  // ---- Validation en temps réel (pendant la saisie) ----
  // L'erreur disparaît dès que l'utilisateur corrige le champ

  champNom.addEventListener('input', function() {
    validerNom(this, errNom);
  });

  champPrenom.addEventListener('input', function() {
    validerNom(this, errPrenom); // même règle que le nom
  });

  champCIN.addEventListener('input', function() {
    // On limite automatiquement à 8 caractères pendant la saisie
    if (this.value.length > 8) {
      this.value = this.value.slice(0, 8);
    }
    validerCIN(this, errCIN);
  });

  champDate.addEventListener('change', function() {
    validerDate(this, errDate);
  });

  champHeure.addEventListener('change', function() {
    validerHeure(this, errHeure);
  });


  // ---- Blocage des dates passées dans le calendrier natif ----
  champDate.setAttribute('min', new Date().toISOString().split('T')[0]);


  // ---- Soumission du formulaire ----
  document.getElementById('rdvForm').addEventListener('submit', function(e) {

    // On valide TOUS les champs et on stocke true/false pour chacun
    const nomOK    = validerNom(champNom,    errNom);
    const prenomOK = validerNom(champPrenom, errPrenom);
    const cinOK    = validerCIN(champCIN,    errCIN);
    const dateOK   = validerDate(champDate,  errDate);
    const heureOK  = validerHeure(champHeure, errHeure);

    // Si au moins un champ est invalide → on bloque l'envoi
    const toutEstValide = nomOK && prenomOK && cinOK && dateOK && heureOK;

    if (!toutEstValide) {
      e.preventDefault(); // empêche l'envoi du formulaire

      // On fait défiler automatiquement jusqu'au premier champ en erreur
      const premierErreur = document.querySelector('.is-error');
      if (premierErreur) {
        premierErreur.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

  });

</script>
<?php endif; // fin du else (formulaire) ?>
</body>
</html>