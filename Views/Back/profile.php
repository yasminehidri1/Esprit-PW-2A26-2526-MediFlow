<?php
/**
 * User Profile Page — works for ALL roles
 * $currentUser, $errors, $success injected by DashboardController
 */
$user   = $currentUser ?? [];
$role   = $user['role'] ?? '';
$errors = $errors ?? [];

// Determine "back" destination per role
$backUrl = match($role) {
    'Patient'          => '/integration/catalogue',
    'Equipment'        => '/integration/equipements',
    'Admin'            => '/integration/dashboard',
    default            => '/integration/dashboard',
};
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mon Profil — MediFlow</title>
  <meta name="description" content="Mettez à jour vos informations personnelles et votre mot de passe MediFlow."/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/integration/assets/css/style.css"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode:"class",
      theme:{extend:{colors:{
        "primary":"#004d99","primary-fixed":"#d6e3ff","primary-container":"#1565c0",
        "surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-dim":"#d8dadc",
        "outline":"#727783","on-surface":"#191c1e","on-surface-variant":"#424752",
        "surface-container":"#eceef0","surface-variant":"#e0e3e5"
      },borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"},
      fontFamily:{headline:["Manrope"],body:["Inter"],label:["Inter"]}}}
    }
  </script>
  <style>
    .form-label  { font-size:12.5px; font-weight:600; color:#6b7280; display:block; margin-bottom:5px; }
    .form-input  { width:100%; padding:10px 14px; background:#f5f7fa; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; color:#111827; outline:none; transition:border-color .18s,box-shadow .18s; }
    .form-input:focus { border-color:#004d99; box-shadow:0 0 0 3px rgba(0,77,153,.10); }
    .form-group  { display:flex; flex-direction:column; margin-bottom:18px; }
    .form-row    { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .card        { background:#fff; border:1px solid #e8eaf0; border-radius:14px; padding:28px; margin-bottom:20px; }
    .card-title  { font-family:'Manrope',sans-serif; font-size:15px; font-weight:800; color:#111827; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:10px; }
    .btn-save    { display:flex; align-items:center; gap:8px; padding:12px 28px; background:#004d99; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; transition:background .18s; }
    .btn-save:hover { background:#00357a; }
    .btn-back    { display:flex; align-items:center; gap:8px; padding:12px 20px; background:#f3f4f6; color:#374151; border:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Inter',sans-serif; text-decoration:none; transition:background .18s; }
    .btn-back:hover { background:#e5e7eb; }
    .alert-ok    { display:flex; align-items:flex-start; gap:10px; padding:14px 18px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; margin-bottom:20px; }
    .alert-ok .material-symbols-outlined { color:#16a34a; font-size:20px; flex-shrink:0; }
    .alert-ok p  { font-size:13.5px; font-weight:600; color:#15803d; }
    .alert-err   { display:flex; align-items:flex-start; gap:10px; padding:14px 18px; background:#fff5f5; border:1px solid #fecaca; border-radius:10px; margin-bottom:20px; }
    .alert-err .material-symbols-outlined { color:#dc2626; font-size:20px; flex-shrink:0; }
    .alert-err ul { font-size:13px; color:#dc2626; padding-left:16px; }
    .meta-chip   { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; background:#eff6ff; border-radius:20px; font-size:12px; font-weight:700; color:#004d99; }
    .pwd-toggle  { position:relative; }
    .pwd-toggle input { padding-right:42px; }
    .pwd-toggle button { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; }
    .pwd-toggle button:hover { color:#004d99; }
  </style>
</head>
<body class="bg-surface text-on-surface overflow-hidden">

<!-- ══ SIDEBAR ══ -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
  <div class="px-8">
    <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
    <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Clinical Sanctuary</p>
  </div>
  <nav class="flex-1 flex flex-col space-y-2 px-4">
    <!-- Role-specific nav -->
    <?php if ($role === 'Admin'): ?>
      <a href="/integration/dashboard" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
      </a>
      <a href="/integration/admin" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">people</span><span class="font-medium">Gestion des utilisateurs</span>
      </a>
    <?php elseif ($role === 'Equipment'): ?>
      <a href="/integration/dashboard" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
      </a>
      <a href="/integration/equipements" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">medical_services</span><span class="font-medium">Gestion des équipements</span>
      </a>
      <a href="/integration/historique-location" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">history</span><span class="font-medium">Historique location</span>
      </a>
    <?php elseif ($role === 'Patient'): ?>
      <a href="/integration/dashboard" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
      </a>
      <a href="/integration/catalogue" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">medical_services</span><span class="font-medium">Location d'équipements</span>
      </a>
      <a href="/integration/mes-reservations" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">shopping_cart</span><span class="font-medium">Mes réservations</span>
      </a>
    <?php else: ?>
      <a href="/integration/dashboard" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
        <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
      </a>
    <?php endif; ?>

    <!-- Profile — active -->
    <a href="/integration/profile" class="flex items-center space-x-3 text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 pl-4 py-3 rounded-xl transition-all duration-300 shadow-sm font-bold">
      <span class="material-symbols-outlined">account_circle</span><span class="font-semibold">Mon profil</span>
    </a>
  </nav>
  <div class="px-4 border-t border-outline pt-6">
    <a href="/integration/logout" class="logout-btn">
      <span class="material-symbols-outlined logout-icon">logout</span><span>Déconnexion</span>
    </a>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim overflow-y-auto">

  <!-- Topbar -->
  <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
    <div class="flex items-center space-x-3">
      <span class="material-symbols-outlined text-primary">account_circle</span>
      <h2 class="text-lg font-bold text-on-surface">Mon Profil</h2>
    </div>
    <div class="flex items-center space-x-3">
      <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars(($user['prenom']??'').(' ').($user['nom']??'')) ?></p>
      <span class="text-xs text-slate-500"><?= htmlspecialchars($user['role_name'] ?? $role) ?></span>
      <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm">
        <?= strtoupper(substr($user['prenom']??'U',0,1)) ?>
      </div>
    </div>
  </header>

  <!-- Content -->
  <div class="pt-24 pb-12 px-10 max-w-2xl">

    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent mb-1">Mon Profil</h2>
    <p class="text-on-surface-variant mb-8 font-medium">Mettez à jour vos informations personnelles et votre mot de passe.</p>

    <!-- Success -->
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div class="alert-ok">
      <span class="material-symbols-outlined">check_circle</span>
      <p>Profil mis à jour avec succès !</p>
    </div>
    <?php endif; ?>

    <!-- Errors -->
    <?php if (!empty($errors)): ?>
    <div class="alert-err">
      <span class="material-symbols-outlined">error</span>
      <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="/integration/profile/update">

      <!-- Personal info -->
      <div class="card">
        <div class="card-title">
          <span class="material-symbols-outlined text-primary">person</span>
          Informations personnelles
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="prenom">Prénom <span style="color:#dc2626;">*</span></label>
            <input class="form-input" id="prenom" name="prenom" type="text" required
                   value="<?= htmlspecialchars($_POST['prenom'] ?? $user['prenom'] ?? '') ?>"/>
          </div>
          <div class="form-group">
            <label class="form-label" for="nom">Nom <span style="color:#dc2626;">*</span></label>
            <input class="form-input" id="nom" name="nom" type="text" required
                   value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom'] ?? '') ?>"/>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="mail">Adresse e-mail <span style="color:#dc2626;">*</span></label>
          <input class="form-input" id="mail" name="mail" type="email" required
                 value="<?= htmlspecialchars($_POST['mail'] ?? $user['mail'] ?? '') ?>"/>
        </div>
        <div class="form-row">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="tel">Numéro de téléphone</label>
            <input class="form-input" id="tel" name="tel" type="tel"
                   value="<?= htmlspecialchars($_POST['tel'] ?? $user['tel'] ?? '') ?>"
                   placeholder="20 123 456"/>
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="adresse">Adresse</label>
            <input class="form-input" id="adresse" name="adresse" type="text"
                   value="<?= htmlspecialchars($_POST['adresse'] ?? $user['adresse'] ?? '') ?>"
                   placeholder="Rue, Ville"/>
          </div>
        </div>
      </div>

      <!-- Account info (read-only) -->
      <div class="card">
        <div class="card-title">
          <span class="material-symbols-outlined text-primary">badge</span>
          Informations du compte
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <span class="meta-chip"><span class="material-symbols-outlined" style="font-size:15px;">tag</span><?= htmlspecialchars($user['matricule'] ?? 'N/A') ?></span>
          <span class="meta-chip"><span class="material-symbols-outlined" style="font-size:15px;">shield_person</span><?= htmlspecialchars($user['role_name'] ?? $role) ?></span>
        </div>
      </div>

      <!-- Change password -->
      <div class="card">
        <div class="card-title">
          <span class="material-symbols-outlined text-primary">lock</span>
          Changer le mot de passe <span style="font-size:11px;font-weight:500;color:#9ca3af;margin-left:6px;">(optionnel)</span>
        </div>
        <div class="form-group pwd-toggle">
          <label class="form-label" for="password">Nouveau mot de passe</label>
          <input class="form-input" id="password" name="password" type="password"
                 placeholder="Laisser vide pour conserver l'actuel"/>
          <button type="button" onclick="togglePwd('password','eye-pass')">
            <span class="material-symbols-outlined" id="eye-pass" style="font-size:18px;">visibility</span>
          </button>
        </div>
        <div class="form-group pwd-toggle" style="margin-bottom:0;">
          <label class="form-label" for="password_confirm">Confirmer le nouveau mot de passe</label>
          <input class="form-input" id="password_confirm" name="password_confirm" type="password"
                 placeholder="Répéter le nouveau mot de passe"/>
          <button type="button" onclick="togglePwd('password_confirm','eye-conf')">
            <span class="material-symbols-outlined" id="eye-conf" style="font-size:18px;">visibility</span>
          </button>
        </div>
        <p style="font-size:11.5px;color:#9ca3af;margin-top:8px;">Minimum 6 caractères.</p>
      </div>

      <!-- Actions -->
      <div style="display:flex;gap:12px;align-items:center;">
        <button class="btn-save" type="submit">
          <span class="material-symbols-outlined" style="font-size:18px;">save</span> Enregistrer les modifications
        </button>
        <a class="btn-back" href="<?= $backUrl ?>">
          <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span> Retour
        </a>
      </div>

    </form>
  </div>
</main>

<script>
function togglePwd(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = 'visibility_off';
  } else {
    input.type = 'password';
    icon.textContent = 'visibility';
  }
}
</script>
</body>
</html>
