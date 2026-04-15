<?php
// ============================================================
//  modifier-rdv.php — Modification d'un RDV par le médecin
//  GET  : affiche le formulaire pré-rempli
//  POST : enregistre les modifications (UPDATE)
// ============================================================

require_once 'config.php';

$pdo = config::getConnexion();

// --- Récupération de l'ID du RDV depuis l'URL ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header('Location: dashboard.php');
    exit;
}

// ============================================================
//  Si POST → on enregistre les modifications
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nouvelle_date   = $_POST['date_rdv']  ?? '';
    $nouvelle_heure  = $_POST['heure_rdv'] ?? '';
    $nouveau_statut  = $_POST['statut']    ?? '';
    $rdv_id          = intval($_POST['rdv_id']);

    // Validation basique
    $erreurs = [];
    if (empty($nouvelle_date))  $erreurs[] = "La date est requise.";
    if (empty($nouvelle_heure)) $erreurs[] = "L'heure est requise.";
    if (!in_array($nouveau_statut, ['en_attente', 'confirme', 'annule'])) {
        $erreurs[] = "Statut invalide.";
    }
    if ($nouvelle_date < date('Y-m-d')) $erreurs[] = "La date ne peut pas être dans le passé.";

    if (!empty($erreurs)) {
        $msg = urlencode(implode(' | ', $erreurs));
        header("Location: modifier-rdv.php?id=$rdv_id&erreur=$msg");
        exit;
    }

    try {
        // UPDATE — on modifie date, heure ET statut
        $sql = "UPDATE rendez_vous 
                SET date_rdv = :date_rdv, heure_rdv = :heure_rdv, statut = :statut
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':date_rdv'  => $nouvelle_date,
            ':heure_rdv' => $nouvelle_heure,
            ':statut'    => $nouveau_statut,
            ':id'        => $rdv_id
        ]);

        // Retour au dashboard avec message de succès
        header('Location: dashboard.php?succes=' . urlencode('Le rendez-vous a été modifié avec succès.'));
        exit;

    } catch (Exception $e) {
        die('Erreur lors de la modification : ' . $e->getMessage());
    }
}

// ============================================================
//  Si GET → on affiche le formulaire pré-rempli
// ============================================================
try {
    $stmt = $pdo->prepare("SELECT * FROM rendez_vous WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $rdv = $stmt->fetch();

    if (!$rdv) {
        header('Location: dashboard.php?erreur=' . urlencode('RDV introuvable.'));
        exit;
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$message_erreur = isset($_GET['erreur']) ? urldecode($_GET['erreur']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier RDV — MediFlow Pro</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=manrope:400,600,700,800|inter:400,500,600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary: #004d99; --primary-dark: #1565c0; --primary-light: #d6e3ff;
      --teal: #005851; --teal-light: #84f5e8;
      --bg: #f0f4f8; --surface: #ffffff; --surface-low: #f5f7fa;
      --border: #e2e8f0; --text: #0f172a; --text-muted: #64748b;
      --error: #ba1a1a;
      --r-md: 12px; --r-lg: 16px; --r-xl: 20px;
    }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

    .navbar {
      background: rgba(255,255,255,0.95); border-bottom: 1px solid var(--border);
      padding: 0 32px; height: 64px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 100; backdrop-filter: blur(12px);
    }
    .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .brand-logo {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: 10px; display: flex; align-items: center; justify-content: center;
    }
    .brand-logo svg { width: 20px; height: 20px; fill: white; }
    .brand-name { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 18px; color: #1e3a6e; }

    .page { max-width: 640px; margin: 40px auto; padding: 0 24px; }

    .breadcrumb {
      display: flex; align-items: center; gap: 8px;
      font-size: 13px; color: var(--text-muted); margin-bottom: 24px;
    }
    .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb svg { width: 14px; height: 14px; }

    .page-title { font-family: 'Manrope', sans-serif; font-size: 26px; font-weight: 800; margin-bottom: 6px; }
    .page-sub   { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; }

    .alert-error {
      padding: 14px 18px; border-radius: var(--r-md);
      background: #fee2e2; color: var(--error); border: 1px solid #fecaca;
      font-size: 14px; margin-bottom: 20px;
    }

    /* Carte info patient (lecture seule) */
    .patient-info-card {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: var(--r-xl); padding: 20px 24px; color: white;
      margin-bottom: 24px; display: flex; align-items: center; gap: 16px;
    }
    .patient-initials {
      width: 52px; height: 52px; border-radius: 50%;
      background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 18px;
      flex-shrink: 0;
    }
    .patient-details .name { font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 17px; }
    .patient-details .meta { font-size: 12px; opacity: 0.75; margin-top: 4px; }

    /* Formulaire */
    .form-card {
      background: var(--surface); border-radius: var(--r-xl);
      padding: 32px; box-shadow: 0 2px 16px rgba(0,77,153,0.08);
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-label {
      font-size: 11px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 0.08em; color: var(--text-muted);
    }
    .form-input {
      width: 100%; background: var(--surface-low);
      border: 2px solid transparent; border-radius: var(--r-md);
      padding: 12px 16px; font-size: 14px; font-family: 'Inter', sans-serif;
      color: var(--text); outline: none; transition: all 0.18s;
    }
    .form-input:focus { border-color: var(--teal); background: white; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; }
    .btn-cancel {
      padding: 12px 24px; background: transparent;
      border: 1.5px solid var(--border); color: var(--text-muted);
      font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 14px;
      border-radius: var(--r-md); cursor: pointer; text-decoration: none;
      display: inline-flex; align-items: center; transition: all 0.15s;
    }
    .btn-cancel:hover { border-color: var(--text-muted); color: var(--text); }
    .btn-save {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 12px 28px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white; border: none; border-radius: var(--r-md);
      font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 14px;
      cursor: pointer; box-shadow: 0 3px 12px rgba(0,77,153,0.25);
      transition: all 0.18s;
    }
    .btn-save:hover { box-shadow: 0 5px 20px rgba(0,77,153,0.35); transform: translateY(-1px); }
    .btn-save svg { width: 16px; height: 16px; }
  </style>
</head>
<body>

<nav class="navbar">
  <a href="dashboard.php" class="brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M19 8h-3V5a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v3H5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-1 6h-3a1 1 0 0 0-1 1v3h-4v-3a1 1 0 0 0-1-1H6v-4h3a1 1 0 0 0 1-1V6h4v3a1 1 0 0 0 1 1h3v4z"/></svg>
    </div>
    <span class="brand-name">MediFlow Pro</span>
  </a>
</nav>

<div class="page">

  <div class="breadcrumb">
    <a href="dashboard.php">Dashboard</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Modifier le RDV</span>
  </div>

  <h1 class="page-title">Modifier le Rendez-vous</h1>
  <p class="page-sub">Modifiez la date, l'heure ou le statut du rendez-vous.</p>

  <?php if ($message_erreur): ?>
  <div class="alert-error"><?= htmlspecialchars($message_erreur) ?></div>
  <?php endif; ?>

  <!-- Info patient (lecture seule) -->
  <?php
    $initiales = strtoupper(substr($rdv['patient_prenom'], 0, 1) . substr($rdv['patient_nom'], 0, 1));
  ?>
  <div class="patient-info-card">
    <div class="patient-initials"><?= $initiales ?></div>
    <div class="patient-details">
      <div class="name"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></div>
      <div class="meta">CIN : <?= htmlspecialchars($rdv['cin']) ?> &bull; <?= ucfirst($rdv['genre']) ?></div>
    </div>
  </div>

  <!-- Formulaire modification -->
  <div class="form-card">
    <form method="POST" action="modifier-rdv.php">
      <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Date du rendez-vous</label>
          <input class="form-input" type="date" name="date_rdv"
                 value="<?= htmlspecialchars($rdv['date_rdv']) ?>"
                 min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Heure</label>
          <input class="form-input" type="time" name="heure_rdv"
                 value="<?= htmlspecialchars(substr($rdv['heure_rdv'], 0, 5)) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Statut</label>
        <select class="form-input" name="statut">
          <option value="en_attente" <?= $rdv['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
          <option value="confirme"   <?= $rdv['statut'] === 'confirme'   ? 'selected' : '' ?>>Confirmé</option>
          <option value="annule"     <?= $rdv['statut'] === 'annule'     ? 'selected' : '' ?>>Annulé</option>
        </select>
      </div>

      <div class="form-actions">
        <a href="dashboard.php" class="btn-cancel">Annuler</a>
        <button type="submit" class="btn-save">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Enregistrer
        </button>
      </div>
    </form>
  </div>

</div>
</body>
</html>