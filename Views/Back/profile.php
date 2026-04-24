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
  <!-- Content -->
  <div class="pb-12 px-10 max-w-2xl">

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
