<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Connexion — MediFlow</title>
    <meta name="description" content="Connectez-vous à votre espace MediFlow."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#004d99", "primary-container": "#1565c0",
                    "primary-fixed": "#d6e3ff", "on-primary": "#ffffff",
                    "tertiary": "#005851", "tertiary-fixed": "#84f5e8",
                    "error": "#ba1a1a", "error-container": "#ffdad6",
                    "surface": "#f7f9fb", "surface-container-low": "#f2f4f6",
                    "on-surface": "#191c1e", "on-surface-variant": "#424752",
                    "outline-variant": "#c2c6d4",
                }
            }
        }
    };
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3,.font-headline { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }

        /* Auth page layout */
        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        .auth-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            top: -100px; left: -100px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            bottom: -80px; right: -80px;
        }
        .auth-left-content { position: relative; z-index: 1; text-align: center; max-width: 340px; }
        .auth-left-logo { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 48px; }
        .auth-left-logo-icon { width: 52px; height: 52px; background: rgba(255,255,255,.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,.3); }
        .auth-left-logo-text { font-family: 'Manrope', sans-serif; font-size: 28px; font-weight: 900; letter-spacing: -0.5px; }
        .auth-left h1 { font-family: 'Manrope', sans-serif; font-size: 32px; font-weight: 900; line-height: 1.2; margin-bottom: 16px; }
        .auth-left p { font-size: 15px; opacity: .8; line-height: 1.6; }
        .auth-feature { display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 12px; padding: 14px 18px; margin-top: 16px; text-align: left; }
        .auth-feature .material-symbols-outlined { font-size: 20px; flex-shrink: 0; color: #84f5e8; }
        .auth-feature-text { font-size: 13px; font-weight: 600; }

        /* Right panel */
        .auth-right {
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 48px;
            overflow-y: auto;
        }
        @media(max-width: 900px) {
            .auth-left { display: none; }
            .auth-right { padding: 40px 24px; }
        }
        .auth-form-wrap { width: 100%; max-width: 420px; }

        /* Form header */
        .auth-title { font-family: 'Manrope', sans-serif; font-size: 28px; font-weight: 900; color: #111827; margin-bottom: 6px; }
        .auth-subtitle { font-size: 14px; color: #6b7280; margin-bottom: 32px; }

        /* Form fields */
        .field-group { margin-bottom: 18px; }
        .field-label { display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 7px; text-transform: uppercase; letter-spacing: .05em; }
        .field-wrap { position: relative; display: flex; align-items: center; }
        .field-icon { position: absolute; left: 14px; color: #9ca3af; display: flex; align-items: center; pointer-events: none; }
        .field-icon .material-symbols-outlined { font-size: 18px; }
        .field-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #111827;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
            box-sizing: border-box;
        }
        .field-input:focus { border-color: #004d99; background: #fff; box-shadow: 0 0 0 3px rgba(0,77,153,.10); }
        .field-input::placeholder { color: #d1d5db; }
        .field-toggle-btn { position: absolute; right: 14px; background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px; display: flex; align-items: center; }
        .field-toggle-btn:hover { color: #004d99; }
        .field-toggle-btn .material-symbols-outlined { font-size: 18px; }

        /* Checkbox row - FIXED */
        .checkbox-row { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
        .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; flex-shrink: 0; accent-color: #004d99; cursor: pointer; }
        .checkbox-row label { font-size: 13px; color: #374151; cursor: pointer; line-height: 1.4; }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #004d99, #1565c0);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { opacity: .92; transform: translateY(-1px); }

        /* Error alert */
        .auth-alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #dc2626;
        }
        .auth-alert-error .material-symbols-outlined { font-size: 18px; flex-shrink: 0; margin-top: 1px; }

        /* Divider */
        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0; }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
        .auth-divider span { font-size: 12px; color: #9ca3af; font-weight: 600; }

        /* Footer link */
        .auth-footer { text-align: center; font-size: 14px; color: #6b7280; }
        .auth-footer a { color: #004d99; font-weight: 700; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        /* Field label row */
        .field-label-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px; }
        .field-label-row .field-label { margin-bottom: 0; }
        .field-forgot { font-size: 12px; color: #004d99; font-weight: 600; text-decoration: none; }
        .field-forgot:hover { text-decoration: underline; }
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
            <h1>Bon retour sur MediFlow</h1>
            <p>Votre plateforme de santé connectée. Gérez vos réservations, consultez le magazine médical et bien plus.</p>

            <div class="auth-feature">
                <span class="material-symbols-outlined">medical_services</span>
                <span class="auth-feature-text">Location d'équipements médicaux certifiés</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">newspaper</span>
                <span class="auth-feature-text">Magazine médical avec articles d'experts</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">security</span>
                <span class="auth-feature-text">Données sécurisées et confidentielles</span>
            </div>
        </div>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <h2 class="auth-title">Connexion</h2>
            <p class="auth-subtitle">Accédez à votre espace personnel MediFlow</p>

            <?php if (!empty($errors)): ?>
            <div class="auth-alert-error">
                <span class="material-symbols-outlined">error</span>
                <div><?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <!-- Email -->
                <div class="field-group">
                    <label class="field-label" for="email">Adresse e-mail</label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">mail</span></span>
                        <input class="field-input" type="email" id="email" name="username"
                               placeholder="vous@exemple.com" autocomplete="email" required/>
                    </div>
                </div>

                <!-- Password -->
                <div class="field-group">
                    <div class="field-label-row">
                        <label class="field-label" for="password">Mot de passe</label>
                        <a href="#" class="field-forgot">Mot de passe oublié ?</a>
                    </div>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">lock</span></span>
                        <input class="field-input" type="password" id="password" name="password"
                               placeholder="••••••••" autocomplete="current-password" required style="padding-right:44px;"/>
                        <button type="button" class="field-toggle-btn" onclick="toggleLoginPwd()">
                            <span class="material-symbols-outlined" id="login-eye">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember me — FIXED checkbox layout -->
                <div class="checkbox-row">
                    <input type="checkbox" id="remember" name="remember"/>
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <!-- reCAPTCHA -->
                <?php
                if (!class_exists('config')) require_once __DIR__ . '/../../config.php';
                $siteKey = \config::getRecaptchaSiteKey();
                ?>
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>" style="margin-bottom:20px;"></div>

                <button type="submit" class="btn-submit">
                    <span class="material-symbols-outlined" style="font-size:18px;">login</span>
                    Se Connecter
                </button>
            </form>

            <div class="auth-divider"><span>ou</span></div>
            <p class="auth-footer">Pas encore de compte ? <a href="/integration/register">Créer un compte</a></p>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function toggleLoginPwd() {
    const inp = document.getElementById('password');
    const eye = document.getElementById('login-eye');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
}

// Cookie Consent Logic
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookie-banner');
    const consent = localStorage.getItem('mediflow_cookie_consent');
    
    if (!consent) {
        // Show banner after a slight delay for smooth entry
        setTimeout(() => {
            banner.style.display = 'flex';
            // Trigger animation
            requestAnimationFrame(() => {
                banner.classList.remove('translate-y-full');
            });
        }, 800);
    }
});

function handleCookieConsent(action) {
    const banner = document.getElementById('cookie-banner');
    localStorage.setItem('mediflow_cookie_consent', action);
    
    // Animate out
    banner.classList.add('translate-y-full');
    setTimeout(() => {
        banner.style.display = 'none';
    }, 500);
}
</script>

<!-- Cookie Consent Banner -->
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] z-50 transform translate-y-full transition-transform duration-500 ease-out p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-outline-variant/30" style="display: none;">
    <div class="flex-1 flex items-start gap-5 max-w-4xl">
        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-1">
            <span class="material-symbols-outlined text-primary text-2xl">cookie</span>
        </div>
        <div>
            <h3 class="font-headline text-lg font-bold text-on-surface mb-2">Nous respectons votre vie privée</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                MediFlow utilise des cookies pour améliorer votre expérience de navigation, analyser le trafic du site et personnaliser le contenu. En cliquant sur "Accepter", vous consentez à l'utilisation de tous les cookies. Vous pouvez également refuser les cookies non essentiels.
            </p>
        </div>
    </div>
    <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0">
        <button onclick="handleCookieConsent('reject')" class="flex-1 md:flex-none px-6 py-3 rounded-xl border-2 border-outline-variant/50 text-on-surface font-semibold hover:bg-surface-container transition-all text-sm whitespace-nowrap">
            Refuser
        </button>
        <button onclick="handleCookieConsent('accept')" class="flex-1 md:flex-none px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-on-primary hover:shadow-lg font-semibold transition-all transform hover:-translate-y-0.5 text-sm whitespace-nowrap">
            Accepter les cookies
        </button>
    </div>
</div>

</body>
</html>
