<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inscription — MediFlow</title>
    <meta name="description" content="Créez votre compte MediFlow gratuitement."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#004d99", "primary-container": "#1565c0",
                    "primary-fixed": "#d6e3ff", "surface": "#f7f9fb",
                    "tertiary": "#005851", "error": "#ba1a1a",
                }
            }
        }
    };
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3 { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }

        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1.4fr;
        }
        @media(max-width: 900px) { .auth-page { grid-template-columns: 1fr; } }

        /* Left panel */
        .auth-left {
            background: linear-gradient(145deg, #004d99 0%, #1565c0 40%, #005851 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 48px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before { content:''; position:absolute; width:400px; height:400px; background:rgba(255,255,255,.06); border-radius:50%; top:-100px; left:-100px; }
        .auth-left::after  { content:''; position:absolute; width:300px; height:300px; background:rgba(255,255,255,.05); border-radius:50%; bottom:-80px; right:-80px; }
        .auth-left-content { position:relative; z-index:1; text-align:center; max-width:340px; }
        .auth-left-logo { display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:40px; }
        .auth-left-logo-icon { width:52px; height:52px; background:rgba(255,255,255,.2); border-radius:16px; display:flex; align-items:center; justify-content:center; border:2px solid rgba(255,255,255,.3); }
        .auth-left-logo-text { font-family:'Manrope',sans-serif; font-size:28px; font-weight:900; letter-spacing:-0.5px; }
        .auth-left h1 { font-family:'Manrope',sans-serif; font-size:30px; font-weight:900; line-height:1.2; margin-bottom:14px; }
        .auth-left p  { font-size:14px; opacity:.8; line-height:1.6; }
        .auth-feature { display:flex; align-items:center; gap:12px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); border-radius:12px; padding:12px 16px; margin-top:12px; text-align:left; }
        .auth-feature .material-symbols-outlined { font-size:18px; flex-shrink:0; color:#84f5e8; }
        .auth-feature-text { font-size:12.5px; font-weight:600; }

        /* Right panel */
        .auth-right { background:#fff; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 48px; overflow-y:auto; }
        @media(max-width:900px) { .auth-left{display:none;} .auth-right{padding:32px 20px;} }
        .auth-form-wrap { width:100%; max-width:460px; }

        .auth-title { font-family:'Manrope',sans-serif; font-size:26px; font-weight:900; color:#111827; margin-bottom:4px; }
        .auth-subtitle { font-size:13px; color:#6b7280; margin-bottom:24px; }

        /* Two column grid for name fields */
        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        @media(max-width:500px) { .field-row { grid-template-columns:1fr; } }

        .field-group { margin-bottom:14px; }
        .field-label { display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em; }
        .field-wrap { position:relative; display:flex; align-items:center; }
        .field-icon { position:absolute; left:13px; color:#9ca3af; display:flex; align-items:center; pointer-events:none; }
        .field-icon .material-symbols-outlined { font-size:17px; }
        .field-input {
            width:100%;
            padding:11px 13px 11px 42px;
            background:#f9fafb;
            border:1.5px solid #e5e7eb;
            border-radius:11px;
            font-size:13.5px;
            font-family:'Inter',sans-serif;
            color:#111827;
            outline:none;
            transition:border-color .15s,box-shadow .15s,background .15s;
            box-sizing:border-box;
        }
        .field-input:focus { border-color:#004d99; background:#fff; box-shadow:0 0 0 3px rgba(0,77,153,.10); }
        .field-input::placeholder { color:#d1d5db; }
        .field-hint { font-size:11.5px; color:#9ca3af; margin-top:4px; }
        .field-toggle-btn { position:absolute; right:12px; background:none; border:none; cursor:pointer; color:#9ca3af; padding:3px; display:flex; align-items:center; }
        .field-toggle-btn:hover { color:#004d99; }
        .field-toggle-btn .material-symbols-outlined { font-size:17px; }

        /* Checkbox row — FIXED: proper inline alignment */
        .checkbox-row { display:flex; align-items:flex-start; gap:10px; margin-bottom:16px; }
        .checkbox-row input[type="checkbox"] { width:17px; height:17px; flex-shrink:0; accent-color:#004d99; cursor:pointer; margin-top:2px; }
        .checkbox-row label { font-size:12.5px; color:#374151; cursor:pointer; line-height:1.5; }
        .checkbox-row a { color:#004d99; font-weight:700; text-decoration:none; }
        .checkbox-row a:hover { text-decoration:underline; }

        /* Submit button */
        .btn-submit {
            width:100%;
            padding:13px;
            background:linear-gradient(135deg,#004d99,#1565c0);
            color:#fff;
            border:none;
            border-radius:12px;
            font-size:14px;
            font-weight:700;
            font-family:'Inter',sans-serif;
            cursor:pointer;
            transition:opacity .15s,transform .1s;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
        }
        .btn-submit:hover { opacity:.92; transform:translateY(-1px); }

        /* Error alert */
        .auth-alert-error {
            display:flex; align-items:flex-start; gap:10px;
            background:#fef2f2; border:1px solid #fecaca; border-radius:10px;
            padding:12px 16px; margin-bottom:18px; font-size:13px; color:#dc2626;
        }
        .auth-alert-error .material-symbols-outlined { font-size:18px; flex-shrink:0; margin-top:1px; }

        /* reCAPTCHA wrapper to constrain width */
        .recaptcha-wrap { margin-bottom:16px; transform:scale(0.95); transform-origin:left center; }
        @media(max-width:380px){ .recaptcha-wrap { transform:scale(0.8); } }

        .auth-divider { display:flex; align-items:center; gap:12px; margin:20px 0; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e5e7eb; }
        .auth-divider span { font-size:11px; color:#9ca3af; font-weight:600; }
        .auth-footer { text-align:center; font-size:13px; color:#6b7280; }
        .auth-footer a { color:#004d99; font-weight:700; text-decoration:none; }
        .auth-footer a:hover { text-decoration:underline; }
    </style>
</head>
<body class="bg-surface">

<div class="auth-page">
    <!-- Left Panel -->
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-left-logo">
                <div class="auth-left-logo-icon">
                    <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1">local_hospital</span>
                </div>
                <span class="auth-left-logo-text">Medi<span style="color:#84f5e8;">Flow</span></span>
            </div>
            <h1>Rejoignez MediFlow</h1>
            <p>Créez votre compte en moins d'une minute et accédez à tous nos services de santé connectée.</p>

            <div class="auth-feature">
                <span class="material-symbols-outlined">medical_services</span>
                <span class="auth-feature-text">Location d'équipements médicaux certifiés</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">newspaper</span>
                <span class="auth-feature-text">Magazine médical avec articles d'experts</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">shield</span>
                <span class="auth-feature-text">Compte sécurisé, données confidentielles</span>
            </div>
        </div>
    </div>

    <!-- Right Panel — Registration Form -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <h2 class="auth-title">Créer un compte Patient</h2>
            <p class="auth-subtitle">Rejoignez la communauté MediFlow — c'est gratuit</p>

            <?php if (!empty($errors)): ?>
            <div class="auth-alert-error">
                <span class="material-symbols-outlined">error</span>
                <div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            </div>
            <?php endif; ?>

            <div id="registerErrors" class="auth-alert-error" style="display:none;"></div>

            <form id="registerForm" method="POST" novalidate>
                <!-- Name row -->
                <div class="field-row">
                    <div class="field-group">
                        <label class="field-label" for="firstName">Prénom</label>
                        <div class="field-wrap">
                            <span class="field-icon"><span class="material-symbols-outlined">person</span></span>
                            <input class="field-input" type="text" id="firstName" name="firstName" placeholder="Jean" required/>
                        </div>
                        <span class="field-hint" id="errorFirstName" style="color:#dc2626;display:none;"></span>
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="lastName">Nom</label>
                        <div class="field-wrap">
                            <span class="field-icon"><span class="material-symbols-outlined">person</span></span>
                            <input class="field-input" type="text" id="lastName" name="lastName" placeholder="Dupont" required/>
                        </div>
                        <span class="field-hint" id="errorLastName" style="color:#dc2626;display:none;"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="field-group">
                    <label class="field-label" for="registerEmail">Adresse e-mail</label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">mail</span></span>
                        <input class="field-input" type="email" id="registerEmail" name="email" placeholder="vous@exemple.com" required/>
                    </div>
                    <span class="field-hint" id="errorEmail" style="color:#dc2626;display:none;"></span>
                </div>

                <!-- Phone -->
                <div class="field-group">
                    <label class="field-label" for="phone">Téléphone <span style="color:#9ca3af;font-weight:400;">(optionnel)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">phone</span></span>
                        <input class="field-input" type="tel" id="phone" name="phone" placeholder="+216 XX XXX XXX"/>
                    </div>
                    <span class="field-hint" id="errorPhone" style="color:#dc2626;display:none;"></span>
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label class="field-label" for="registerPassword">Mot de passe</label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">lock</span></span>
                        <input class="field-input" type="password" id="registerPassword" name="password"
                               placeholder="••••••••" required style="padding-right:42px;"/>
                        <button type="button" class="field-toggle-btn" onclick="togglePwd('registerPassword','eye-pass')">
                            <span class="material-symbols-outlined" id="eye-pass">visibility</span>
                        </button>
                    </div>
                    <p class="field-hint">Minimum 8 caractères avec majuscules et chiffres</p>
                    <span class="field-hint" id="errorPassword" style="color:#dc2626;display:none;"></span>
                </div>

                <!-- Confirm Password -->
                <div class="field-group">
                    <label class="field-label" for="confirmPassword">Confirmer le mot de passe</label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">lock</span></span>
                        <input class="field-input" type="password" id="confirmPassword" name="confirmPassword"
                               placeholder="••••••••" required style="padding-right:42px;"/>
                        <button type="button" class="field-toggle-btn" onclick="togglePwd('confirmPassword','eye-conf')">
                            <span class="material-symbols-outlined" id="eye-conf">visibility</span>
                        </button>
                    </div>
                    <span class="field-hint" id="errorConfirmPassword" style="color:#dc2626;display:none;"></span>
                </div>

                <!-- Terms — FIXED checkbox layout -->
                <div class="checkbox-row">
                    <input type="checkbox" id="terms" name="terms" required/>
                    <label for="terms">
                        J'accepte les <a href="/integration/terms" target="_blank">Conditions d'Utilisation</a> et la politique de confidentialité
                    </label>
                </div>
                <span class="field-hint" id="errorTerms" style="color:#dc2626;display:none;margin-bottom:12px;display:none;"></span>

                <!-- reCAPTCHA -->
                <?php
                if (!class_exists('config')) require_once __DIR__ . '/../../config.php';
                $siteKey = \config::getRecaptchaSiteKey();
                ?>
                <div class="recaptcha-wrap">
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="material-symbols-outlined" style="font-size:18px;">how_to_reg</span>
                    S'inscrire en tant que Patient
                </button>
            </form>

            <div class="auth-divider"><span>ou</span></div>

            <!-- Google Sign-up Button -->
            <a href="<?php
                $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                    'client_id' => \config::getGoogleClientId(),
                    'redirect_uri' => \config::getGoogleRedirectUri(),
                    'response_type' => 'code',
                    'scope' => 'openid email profile',
                    'access_type' => 'offline'
                ]);
                echo htmlspecialchars($googleAuthUrl);
            ?>" class="btn-google" style="width:100%;padding:13px;background:#f3f4f6;color:#374151;border:1.5px solid #e5e7eb;border-radius:12px;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s,transform .1s;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;margin-bottom:16px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032 c0-3.331,2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.461,2.268,15.365,1,12.545,1 C6.477,1,1.54,5.937,1.54,12s4.938,11,11.005,11c6.068,0,11.066-4.941,11.066-11c0-0.713-0.063-1.42-0.186-2.121H12.545z" fill="#4285F4"/>
                </svg>
                S'inscrire avec Google
            </a>

            <div class="auth-divider"><span>déjà inscrit ?</span></div>
            <p class="auth-footer"><a href="/integration/login">← Se connecter à mon compte</a></p>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="assets/js/register.js"></script>
<script>
function togglePwd(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(iconId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
}
</script>
</body>
</html>
