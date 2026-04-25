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
<style>
/* ── Profile page ── */
.profile-wrap { max-width: 700px; }

.profile-hero {
    background: linear-gradient(135deg, #004d99 0%, #1565c0 60%, #005851 100%);
    border-radius: 20px;
    padding: 28px 32px;
    display: flex;
    align-items: center;
    gap: 20px;
    color: #fff;
    margin-bottom: 28px;
}
.profile-avatar-big {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Manrope', sans-serif;
    font-size: 28px; font-weight: 900;
    border: 3px solid rgba(255,255,255,.4);
    flex-shrink: 0;
}
.profile-hero-info h2 { font-family: 'Manrope', sans-serif; font-size: 22px; font-weight: 800; margin-bottom: 4px; }
.profile-hero-info p  { font-size: 13px; opacity: .8; }
.profile-hero-badge {
    margin-left: auto;
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
}

/* ── Cards ── */
.card {
    background: #fff;
    border: 1px solid #e8eaf0;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
}
.card-title .material-symbols-outlined { font-size: 18px; }

/* ── Form elements ── */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media(max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 16px; }
.form-group:last-child { margin-bottom: 0; }

.form-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.form-input {
    width: 100%;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    color: #111827;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
}
.form-input:focus {
    border-color: #004d99;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(0,77,153,.10);
}

/* ── Password toggle ── */
.pwd-toggle {
    position: relative;
}
.pwd-toggle button {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: 4px;
    display: flex;
    align-items: center;
    margin-top: 12px;
}
.pwd-toggle button:hover { color: #004d99; }
.pwd-toggle .form-input { padding-right: 44px; }

/* ── Meta chips ── */
.meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: #f0f5ff;
    border: 1px solid #c7d7f9;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    color: #004d99;
}
.meta-chip .material-symbols-outlined { font-size: 14px; }

/* ── Alerts ── */
.alert-ok {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 600;
    color: #15803d;
}
.alert-ok .material-symbols-outlined { font-size: 20px; }
.alert-err {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #dc2626;
}
.alert-err ul { margin: 0; padding-left: 16px; }
.alert-err li { margin-bottom: 2px; }

/* ── Action buttons ── */
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #004d99;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: background .15s, transform .1s;
}
.btn-save:hover { background: #00357a; transform: translateY(-1px); }
.btn-save .material-symbols-outlined { font-size: 18px; }

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 20px;
    background: #f3f4f6;
    color: #374151;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    text-decoration: none;
    transition: background .15s;
}
.btn-back:hover { background: #e5e7eb; }
.btn-back .material-symbols-outlined { font-size: 18px; }
</style>

  <!-- Content -->
  <div class="profile-wrap">


    <!-- Profile hero banner -->
    <div class="profile-hero">
      <div class="profile-avatar-big"><?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1)) ?></div>
      <div class="profile-hero-info">
        <h2><?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></h2>
        <p><?= htmlspecialchars($user['mail'] ?? '') ?></p>
      </div>
      <span class="profile-hero-badge"><?= htmlspecialchars($user['role_name'] ?? $role) ?></span>
    </div>

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
