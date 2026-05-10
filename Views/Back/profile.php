<?php
/**
 * User Profile Page — Enhanced & Engaging
 * Works for ALL roles with dynamic content
 */
$user   = $currentUser ?? [];
$role   = $user['role'] ?? '';
$errors = $errors ?? [];

// Profile completion percentage
$completionFields = [
    'prenom' => !empty($user['prenom']),
    'nom' => !empty($user['nom']),
    'mail' => !empty($user['mail']),
    'tel' => !empty($user['tel']),
    'adresse' => !empty($user['adresse']),
];
$completedFields = array_sum($completionFields);
$totalFields = count($completionFields);
$completionPercent = intval(($completedFields / $totalFields) * 100);

// Determine "back" destination per role
$backUrl = match($role) {
    'Patient'          => '/integration/catalogue',
    'Technicien'       => '/integration/equipements',
    'Admin'            => '/integration/dashboard',
    default            => '/integration/dashboard',
};
?>
<style>
/* ═══════════════════════════════════════════ */
/* ENHANCED PROFILE PAGE — ENGAGING DESIGN */
/* ═══════════════════════════════════════════ */

.profile-wrapper {
    max-width: 900px;
    margin: 0 auto;
}

/* ═══════ HERO SECTION ═══════ */
.profile-hero {
    background: linear-gradient(135deg, #004d99 0%, #1565c0 40%, #005851 100%);
    border-radius: 24px;
    padding: 40px;
    display: flex;
    align-items: center;
    gap: 32px;
    color: white;
    margin-bottom: 32px;
    box-shadow: 0 10px 30px rgba(0, 77, 153, 0.2);
    position: relative;
    overflow: hidden;
}

.profile-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(100px, -100px);
}

.profile-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
    z-index: 1;
}

.profile-avatar-big {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 900;
    border: 4px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    overflow: hidden;
    transition: all 0.3s ease;
}

.profile-avatar-big img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar-wrapper:hover .profile-avatar-big {
    transform: scale(1.05);
}

.upload-pic-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #1565c0, #004d99);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    border: 2px solid white;
    transition: all 0.2s ease;
    color: white;
}

.upload-pic-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0,0,0,0.4);
}

.upload-pic-btn .material-symbols-outlined {
    font-size: 18px;
}

.profile-hero-content {
    flex: 1;
    position: relative;
    z-index: 1;
}

.profile-hero h1 {
    font-size: 32px;
    font-weight: 900;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.profile-hero-email {
    font-size: 15px;
    opacity: 0.9;
    margin-bottom: 16px;
}

.profile-hero-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.hero-badge {
    background: rgba(255,255,255,0.15);
    border: 1.5px solid rgba(255,255,255,0.3);
    border-radius: 24px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(10px);
}

.hero-badge .material-symbols-outlined {
    font-size: 16px;
}

/* ═══════ STATS SECTION ═══════ */
.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: linear-gradient(135deg, #f0f5ff 0%, #f0fdf4 100%);
    border: 2px solid #c7d7f9;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 77, 153, 0.15);
}

.stat-icon {
    font-size: 32px;
    color: #004d99;
    margin-bottom: 12px;
    display: block;
}

.stat-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 700;
    margin-bottom: 6px;
}

.stat-value {
    font-size: 24px;
    font-weight: 900;
    color: #004d99;
}

/* ═══════ CARD CONTAINER ═══════ */
.card {
    background: white;
    border: 2px solid #e8eaf0;
    border-radius: 18px;
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}

.card:hover {
    border-color: #004d99;
    box-shadow: 0 4px 16px rgba(0, 77, 153, 0.08);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f3f4f6;
}

.card-title .material-symbols-outlined {
    font-size: 24px;
    color: #004d99;
}

/* ═══════ FORM ELEMENTS ═══════ */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media(max-width: 700px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #374151;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 14px;
    color: #111827;
    outline: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: inherit;
}

.form-input:focus {
    border-color: #004d99;
    background: white;
    box-shadow: 0 0 0 4px rgba(0, 77, 153, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

/* ═══════ PASSWORD TOGGLE ═══════ */
.pwd-toggle {
    position: relative;
}

.pwd-toggle button {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: 6px;
    display: flex;
    align-items: center;
    margin-top: 10px;
    transition: color 0.2s;
}

.pwd-toggle button:hover {
    color: #004d99;
}

.pwd-toggle .form-input {
    padding-right: 48px;
}

/* ═══════ ALERTS ═══════ */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 600;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #dcfce7;
    border: 2px solid #86efac;
    color: #166534;
}

.alert-error {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #7f1d1d;
}

.alert .material-symbols-outlined {
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 4px;
}

/* ═══════ BUTTONS ═══════ */
.btn-group {
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #f3f4f6;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 28px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
}

.btn-primary {
    background: linear-gradient(135deg, #004d99 0%, #1565c0 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 77, 153, 0.25);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 77, 153, 0.35);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
}

/* ═══════ COMPLETION PROGRESS ═══════ */
.completion-section {
    background: linear-gradient(135deg, #f0f5ff 0%, #fff 100%);
    border: 2px dashed #c7d7f9;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}

.completion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.completion-title {
    font-size: 14px;
    font-weight: 800;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.completion-percent {
    font-size: 18px;
    font-weight: 900;
    color: #004d99;
}

.progress-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #004d99 0%, #1565c0 100%);
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* ═══════ INFO BOX ═══════ */
.info-box {
    background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 16px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.info-box .material-symbols-outlined {
    font-size: 18px;
    flex-shrink: 0;
}
</style>

<!-- PROFILE CONTENT -->
<div class="profile-wrapper">

    <!-- HERO BANNER -->
    <div class="profile-hero">
        <div class="profile-avatar-wrapper">
            <div class="profile-avatar-big" id="avatarDisplay">
                <?php
                    // Show uploaded picture or initials
                    if (!empty($user['profile_pic'])) {
                        // Add cache-busting with timestamp
                        echo '<img src="' . htmlspecialchars($user['profile_pic']) . '?t=' . time() . '" alt="Profile" onerror="this.parentElement.innerHTML=\'K\';">';
                    } else {
                        echo strtoupper(substr($user['prenom'] ?? 'U', 0, 1));
                    }
                ?>
            </div>
            <label class="upload-pic-btn" title="Click to upload profile picture">
                <input type="file" id="profilePicInput" accept="image/*" style="display:none;">
                <span class="material-symbols-outlined">photo_camera</span>
            </label>
        </div>
        <div class="profile-hero-content">
            <h1><?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></h1>
            <p class="profile-hero-email">📧 <?= htmlspecialchars($user['mail'] ?? '') ?></p>
            <div class="profile-hero-meta">
                <span class="hero-badge">
                    <span class="material-symbols-outlined">shield_person</span>
                    <?= htmlspecialchars($user['role_name'] ?? $role) ?>
                </span>
                <span class="hero-badge">
                    <span class="material-symbols-outlined">verified_user</span>
                    Compte vérifié
                </span>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="profile-stats">
        <div class="stat-card">
            <span class="material-symbols-outlined stat-icon">task_alt</span>
            <div class="stat-label">Profil Complété</div>
            <div class="stat-value"><?= $completionPercent ?>%</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined stat-icon">security</span>
            <div class="stat-label">Sécurité</div>
            <div class="stat-value" style="color: #16a34a;">✓ Sûr</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined stat-icon">access_time</span>
            <div class="stat-label">Matricule</div>
            <div class="stat-value" style="font-size: 18px; letter-spacing: 1px;"><?= htmlspecialchars($user['matricule'] ?? 'N/A') ?></div>
        </div>
    </div>

    <!-- COMPLETION PROGRESS -->
    <div class="completion-section">
        <div class="completion-header">
            <span class="completion-title">
                <span class="material-symbols-outlined">trending_up</span>
                Progression du Profil
            </span>
            <span class="completion-percent"><?= $completionPercent ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $completionPercent ?>%;"></div>
        </div>
        <p style="font-size: 12px; color: #6b7280; margin-top: 12px;">
            <?= $completedFields ?>/<?= $totalFields ?> champs complétés • 
            <?php 
                $missing = $totalFields - $completedFields;
                echo $missing > 0 ? $missing . ' champ' . ($missing > 1 ? 's' : '') . ' à remplir' : '✓ Profil complet!';
            ?>
        </p>
    </div>

    <!-- ALERTS -->
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div class="alert alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        <div>
            <strong>Bravo!</strong> Votre profil a été mis à jour avec succès.
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <span class="material-symbols-outlined">error</span>
        <div>
            <strong>Erreur lors de la sauvegarde :</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" action="/integration/profile/update">

        <!-- PERSONAL INFO CARD -->
        <div class="card">
            <div class="card-title">
                <span class="material-symbols-outlined">person</span>
                Informations Personnelles
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom <span style="color:#dc2626;">*</span></label>
                    <input class="form-input" id="prenom" name="prenom" type="text" required
                           value="<?= htmlspecialchars($_POST['prenom'] ?? $user['prenom'] ?? '') ?>"
                           placeholder="Jean"/>
                </div>
                <div class="form-group">
                    <label class="form-label" for="nom">Nom <span style="color:#dc2626;">*</span></label>
                    <input class="form-input" id="nom" name="nom" type="text" required
                           value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom'] ?? '') ?>"
                           placeholder="Dupont"/>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="mail">📧 Adresse E-mail <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="mail" name="mail" type="email" required
                       value="<?= htmlspecialchars($_POST['mail'] ?? $user['mail'] ?? '') ?>"
                       placeholder="jean.dupont@example.com"/>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tel">📱 Téléphone</label>
                    <input class="form-input" id="tel" name="tel" type="tel"
                           value="<?= htmlspecialchars($_POST['tel'] ?? $user['tel'] ?? '') ?>"
                           placeholder="+216 20 123 456"/>
                </div>
                <div class="form-group">
                    <label class="form-label" for="adresse">📍 Adresse</label>
                    <input class="form-input" id="adresse" name="adresse" type="text"
                           value="<?= htmlspecialchars($_POST['adresse'] ?? $user['adresse'] ?? '') ?>"
                           placeholder="Rue Ibn Sina, Tunis"/>
                </div>
            </div>
        </div>

        <!-- ACCOUNT INFO CARD -->
        <div class="card">
            <div class="card-title">
                <span class="material-symbols-outlined">info</span>
                Informations du Compte
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="padding: 16px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #004d99;">
                    <div style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 8px;">Matricule</div>
                    <div style="font-size: 18px; font-weight: 900; color: #004d99; letter-spacing: 1px;"><?= htmlspecialchars($user['matricule'] ?? 'N/A') ?></div>
                </div>
                <div style="padding: 16px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #004d99;">
                    <div style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; margin-bottom: 8px;">Rôle</div>
                    <div style="font-size: 18px; font-weight: 900; color: #004d99;"><?= htmlspecialchars($user['role_name'] ?? $role) ?></div>
                </div>
            </div>

            <div class="info-box" style="margin-top: 20px;">
                <span class="material-symbols-outlined">lock</span>
                <div>Votre compte est sécurisé et vérifié. Gardez votre mot de passe confidentiel.</div>
            </div>
        </div>

        <!-- SECURITY CARD -->
        <div class="card">
            <div class="card-title">
                <span class="material-symbols-outlined">vpn_key</span>
                Sécurité & Mot de Passe
            </div>

            <div class="info-box">
                <span class="material-symbols-outlined">info</span>
                <div>Renforcez votre sécurité avec un mot de passe fort contenant au moins 8 caractères, majuscules et chiffres.</div>
            </div>

            <div class="form-group pwd-toggle">
                <label class="form-label" for="password">🔒 Nouveau Mot de Passe</label>
                <input class="form-input" id="password" name="password" type="password"
                       placeholder="Laissez vide pour conserver l'actuel"/>
                <button type="button" onclick="togglePwd('password','eye-pass')">
                    <span class="material-symbols-outlined" id="eye-pass">visibility</span>
                </button>
            </div>

            <div class="form-group pwd-toggle">
                <label class="form-label" for="password_confirm">🔒 Confirmer le Mot de Passe</label>
                <input class="form-input" id="password_confirm" name="password_confirm" type="password"
                       placeholder="Répétez le nouveau mot de passe"/>
                <button type="button" onclick="togglePwd('password_confirm','eye-conf')">
                    <span class="material-symbols-outlined" id="eye-conf">visibility</span>
                </button>
            </div>

            <p style="font-size: 12px; color: #6b7280; margin-top: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid #16a34a;">
                ✓ Minimum 8 caractères • ✓ Mélangez majuscules et minuscules • ✓ Ajoutez des chiffres
            </p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="btn-group">
            <button class="btn btn-primary" type="submit">
                <span class="material-symbols-outlined">save</span>
                Enregistrer les Modifications
            </button>
            <a class="btn btn-secondary" href="<?= $backUrl ?>">
                <span class="material-symbols-outlined">arrow_back</span>
                Retour
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

// Profile picture upload handler
document.getElementById('profilePicInput').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('❌ Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.');
        return;
    }

    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('❌ File too large. Maximum size is 5MB.');
        return;
    }

    // Show loading state
    const avatarDisplay = document.getElementById('avatarDisplay');
    const originalContent = avatarDisplay.innerHTML;
    avatarDisplay.innerHTML = '<span class="material-symbols-outlined" style="font-size: 48px; animation: spin 2s linear infinite;">hourglass_empty</span>';

    // Upload file
    const formData = new FormData();
    formData.append('profile_pic', file);

    try {
        const response = await fetch('/integration/api/upload-profile-pic.php', {
            method: 'POST',
            body: formData
        });

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        // Try to parse JSON
        let data;
        try {
            data = await response.json();
        } catch (jsonError) {
            console.error('JSON parse error:', jsonError);
            console.error('Response text:', await response.text());
            throw new Error('Invalid server response');
        }

        if (data.success) {
            // Show success message
            showAlert('✓ Profile picture uploaded successfully!', 'success');
            
            // Update avatar display
            const img = document.createElement('img');
            img.src = data.filename + '?t=' + Date.now(); // Cache bust
            img.alt = 'Profile';
            avatarDisplay.innerHTML = '';
            avatarDisplay.appendChild(img);

            // Update display in stats
            const avatarInitial = document.querySelector('.profile-avatar-big');
            if (avatarInitial && !avatarInitial.querySelector('img')) {
                const newImg = document.createElement('img');
                newImg.src = data.filename + '?t=' + Date.now();
                newImg.alt = 'Profile';
                avatarInitial.innerHTML = '';
                avatarInitial.appendChild(newImg);
            }
        } else {
            showAlert('❌ ' + (data.error || 'Upload failed'), 'error');
            avatarDisplay.innerHTML = originalContent;
        }
    } catch (error) {
        console.error('Upload error:', error.message);
        showAlert('❌ ' + (error.message || 'Network error. Please try again.'), 'error');
        avatarDisplay.innerHTML = originalContent;
    }

    // Reset input
    e.target.value = '';
});

// Helper function to show alerts
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type;
    alertDiv.innerHTML = `
        <span class="material-symbols-outlined">${type === 'success' ? 'check_circle' : 'error'}</span>
        <div>${message}</div>
    `;
    
    const container = document.querySelector('.profile-wrapper');
    const firstChild = container.querySelector('.profile-hero');
    container.insertBefore(alertDiv, firstChild.nextSibling);

    // Auto-remove after 4 seconds
    setTimeout(() => {
        alertDiv.style.animation = 'slideDown 0.3s ease-out reverse';
        setTimeout(() => alertDiv.remove(), 300);
    }, 4000);
}

// CSS animation for upload spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .profile-wrapper .alert {
        animation: slideDown 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>
