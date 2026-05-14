<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Connexion — MediFlow</title>
    <meta name="description" content="Connectez-vous à votre espace MediFlow."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;min-height:100vh;overflow:hidden;}
        h1,h2,h3,.font-headline{font-family:'Manrope',sans-serif;}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}

        /* ── Layout ── */
        .login-shell{
            display:grid;
            grid-template-columns:1fr 1fr;
            min-height:100vh;
        }
        @media(max-width:860px){
            .login-shell{grid-template-columns:1fr;}
            .login-left{display:none;}
            .login-right{padding:32px 20px;}
            body{overflow:auto;}
        }

        /* ── Left Panel ── */
        .login-left{
            position:relative;
            background:linear-gradient(135deg,#020b18 0%,#041430 35%,#061d45 65%,#042a3a 100%);
            display:flex;flex-direction:column;
            justify-content:center;align-items:center;
            padding:60px 52px;overflow:hidden;
        }

        /* Animated mesh orbs */
        .orb{
            position:absolute;border-radius:50%;
            filter:blur(80px);pointer-events:none;
            animation:orbFloat 8s ease-in-out infinite;
        }
        .orb-1{width:380px;height:380px;background:radial-gradient(circle,rgba(0,100,255,0.22),transparent 70%);top:-80px;left:-80px;animation-delay:0s;}
        .orb-2{width:300px;height:300px;background:radial-gradient(circle,rgba(0,180,160,0.18),transparent 70%);bottom:-60px;right:-60px;animation-delay:-3s;}
        .orb-3{width:200px;height:200px;background:radial-gradient(circle,rgba(120,60,255,0.15),transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:-5s;}
        @keyframes orbFloat{
            0%,100%{transform:translateY(0) scale(1);}
            50%{transform:translateY(-24px) scale(1.06);}
        }
        .orb-3{animation:orbFloat3 10s ease-in-out infinite;}
        @keyframes orbFloat3{
            0%,100%{transform:translate(-50%,-50%) scale(1);}
            50%{transform:translate(-50%,-58%) scale(1.1);}
        }

        /* Grid overlay */
        .grid-lines{
            position:absolute;inset:0;
            background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),
                             linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
            background-size:48px 48px;pointer-events:none;
        }

        /* Particles canvas */
        #particles{position:absolute;inset:0;pointer-events:none;}

        .left-content{position:relative;z-index:2;max-width:360px;text-align:center;}

        /* Logo */
        .logo-badge{
            display:inline-flex;align-items:center;gap:14px;
            margin-bottom:44px;
        }
        .logo-icon{
            width:56px;height:56px;
            background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,.05));
            border:1.5px solid rgba(255,255,255,.2);
            border-radius:18px;display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 24px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.15);
            backdrop-filter:blur(10px);
        }
        .logo-text{font-family:'Manrope',sans-serif;font-size:30px;font-weight:900;color:#fff;letter-spacing:-0.5px;}
        .logo-text span{background:linear-gradient(135deg,#4dd9cf,#7cc8ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}

        .left-headline{
            font-family:'Manrope',sans-serif;font-size:34px;font-weight:900;
            color:#fff;line-height:1.15;margin-bottom:14px;
        }
        .left-headline em{
            font-style:normal;
            background:linear-gradient(135deg,#60d4cb,#7bb3ff);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
        }
        .left-sub{font-size:14px;color:rgba(255,255,255,.55);line-height:1.7;margin-bottom:36px;}

        /* Feature pills */
        .feature-pill{
            display:flex;align-items:center;gap:12px;
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.1);
            border-radius:14px;padding:13px 18px;
            margin-bottom:10px;text-align:left;
            transition:background .2s;
        }
        .feature-pill:hover{background:rgba(255,255,255,.1);}
        .pill-icon{
            width:36px;height:36px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            flex-shrink:0;font-size:18px;
        }
        .pill-text{font-size:13px;font-weight:600;color:rgba(255,255,255,.85);}

        /* Trust badge */
        .trust-row{
            display:flex;align-items:center;justify-content:center;gap:20px;
            margin-top:32px;padding-top:24px;
            border-top:1px solid rgba(255,255,255,.08);
        }
        .trust-item{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.4);font-weight:600;}
        .trust-dot{width:6px;height:6px;border-radius:50%;background:#22d3b1;animation:pulse 2s ease-in-out infinite;}
        @keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}

        /* ── Right Panel ── */
        .login-right{
            background:#f8fafc;
            display:flex;flex-direction:column;
            justify-content:center;align-items:center;
            padding:60px 52px;overflow-y:auto;
        }
        .form-wrap{width:100%;max-width:420px;}

        /* Form header */
        .form-eyebrow{
            display:inline-flex;align-items:center;gap:6px;
            font-size:11px;font-weight:700;color:#004d99;
            background:#e8f0fe;border:1px solid #c7d9f8;
            border-radius:20px;padding:5px 12px;
            letter-spacing:.06em;text-transform:uppercase;
            margin-bottom:18px;
        }
        .form-title{font-family:'Manrope',sans-serif;font-size:30px;font-weight:900;color:#0f172a;margin-bottom:6px;}
        .form-subtitle{font-size:14px;color:#64748b;margin-bottom:28px;}

        /* Alert */
        .alert-error{
            display:flex;align-items:flex-start;gap:10px;
            background:#fff5f5;border:1px solid #fecaca;
            border-radius:12px;padding:13px 16px;
            margin-bottom:20px;font-size:13px;color:#dc2626;
        }
        .alert-warning{
            background:#fffbeb;border-color:#fde68a;color:#92400e;
        }

        /* Field */
        .field{margin-bottom:18px;}
        .field-label-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;}
        .label{font-size:11.5px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;}
        .forgot{font-size:12px;color:#004d99;font-weight:600;text-decoration:none;}
        .forgot:hover{text-decoration:underline;}

        .input-wrap{position:relative;display:flex;align-items:center;}
        .input-icon{position:absolute;left:14px;color:#94a3b8;pointer-events:none;display:flex;}
        .input-icon .material-symbols-outlined{font-size:18px;}
        .input{
            width:100%;padding:13px 14px 13px 44px;
            background:#fff;border:1.5px solid #e2e8f0;
            border-radius:13px;font-size:14px;font-family:'Inter',sans-serif;
            color:#0f172a;outline:none;
            transition:border-color .15s,box-shadow .15s;
        }
        .input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.1);}
        .input::placeholder{color:#cbd5e1;}
        .toggle-pwd{
            position:absolute;right:14px;background:none;border:none;
            cursor:pointer;color:#94a3b8;padding:4px;display:flex;
            transition:color .15s;
        }
        .toggle-pwd:hover{color:#004d99;}
        .toggle-pwd .material-symbols-outlined{font-size:18px;}

        /* Checkbox */
        .check-row{display:flex;align-items:center;gap:10px;margin-bottom:20px;}
        .check-row input[type=checkbox]{width:17px;height:17px;flex-shrink:0;accent-color:#004d99;cursor:pointer;}
        .check-row label{font-size:13px;color:#374151;cursor:pointer;}

        /* Submit */
        .btn-primary{
            width:100%;padding:14px;
            background:linear-gradient(135deg,#004d99,#0066cc);
            color:#fff;border:none;border-radius:13px;
            font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
            cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:8px;
            box-shadow:0 4px 14px rgba(0,77,153,.28);
            transition:all .2s;
        }
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,77,153,.36);}
        .btn-primary:active{transform:translateY(0);}

        /* Divider */
        .divider{display:flex;align-items:center;gap:12px;margin:22px 0;}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#e2e8f0;}
        .divider span{font-size:12px;color:#94a3b8;font-weight:600;white-space:nowrap;}

        /* Google btn */
        .btn-google{
            width:100%;padding:13px 14px;
            background:#fff;border:1.5px solid #e2e8f0;
            border-radius:13px;font-size:14px;font-weight:700;
            color:#374151;text-decoration:none;
            display:flex;align-items:center;justify-content:center;gap:10px;
            transition:all .2s;cursor:pointer;
            box-shadow:0 1px 4px rgba(0,0,0,.06);
        }
        .btn-google:hover{border-color:#c7d9f8;background:#f8faff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.08);}

        /* Footer */
        .form-footer{text-align:center;font-size:14px;color:#64748b;margin-top:20px;}
        .form-footer a{color:#004d99;font-weight:700;text-decoration:none;}
        .form-footer a:hover{text-decoration:underline;}

        /* reCAPTCHA responsive */
        .g-recaptcha{transform-origin:left center;margin-bottom:20px;}
        @media(max-width:400px){.g-recaptcha{transform:scale(.85);}}

        /* Cookie banner */
        .cookie-banner{
            position:fixed;bottom:0;left:0;right:0;
            background:#fff;border-top:1px solid #e2e8f0;
            box-shadow:0 -8px 32px rgba(0,0,0,.08);
            padding:20px 32px;z-index:999;
            display:none;
            flex-direction:row;align-items:center;
            gap:20px;
            transform:translateY(100%);
            transition:transform .4s cubic-bezier(.16,1,.3,1);
        }
        @media(max-width:700px){.cookie-banner{flex-direction:column;padding:16px;}}
        .cookie-banner.visible{transform:translateY(0);}

        /* Slide-in animation for form */
        @keyframes slideIn{
            from{opacity:0;transform:translateY(20px);}
            to{opacity:1;transform:translateY(0);}
        }
        .form-wrap{animation:slideIn .6s cubic-bezier(.16,1,.3,1) both;}
    </style>
</head>
<body>

<div class="login-shell">

    <!-- ══════════ LEFT PANEL ══════════ -->
    <div class="login-left">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="grid-lines"></div>
        <canvas id="particles"></canvas>

        <div class="left-content">
            <!-- Logo -->
            <div class="logo-badge">
                <div class="logo-icon">
                    <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1">local_hospital</span>
                </div>
                <span class="logo-text">Medi<span>Flow</span></span>
            </div>

            <h1 class="left-headline">La santé <em>réinventée</em><br/>pour vous.</h1>
            <p class="left-sub">Gérez vos rendez-vous, explorez le magazine médical et accédez à vos services de santé — en toute sécurité.</p>

            <div class="feature-pill">
                <div class="pill-icon" style="background:rgba(34,211,177,.12);">
                    <span class="material-symbols-outlined" style="color:#22d3b1;font-size:19px;font-variation-settings:'FILL' 1">calendar_month</span>
                </div>
                <span class="pill-text">Prise de RDV en ligne simplifiée</span>
            </div>
            <div class="feature-pill">
                <div class="pill-icon" style="background:rgba(96,165,250,.12);">
                    <span class="material-symbols-outlined" style="color:#60a5fa;font-size:19px;font-variation-settings:'FILL' 1">medical_services</span>
                </div>
                <span class="pill-text">Location d'équipements médicaux certifiés</span>
            </div>
            <div class="feature-pill">
                <div class="pill-icon" style="background:rgba(167,139,250,.12);">
                    <span class="material-symbols-outlined" style="color:#a78bfa;font-size:19px;font-variation-settings:'FILL' 1">newspaper</span>
                </div>
                <span class="pill-text">Magazine médical assisté par IA</span>
            </div>

            <div class="trust-row">
                <div class="trust-item"><span class="trust-dot"></span> Système actif</div>
                <div class="trust-item">🔒 Données chiffrées</div>
                <div class="trust-item">⚡ Conformité RGPD</div>
            </div>
        </div>
    </div>

    <!-- ══════════ RIGHT PANEL ══════════ -->
    <div class="login-right">
        <div class="form-wrap">

            <div class="form-eyebrow">
                <span class="material-symbols-outlined" style="font-size:13px;">verified_user</span>
                Espace Sécurisé
            </div>
            <h2 class="form-title">Bon retour 👋</h2>
            <p class="form-subtitle">Connectez-vous à votre espace MediFlow</p>

            <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0;margin-top:1px;">error</span>
                <div><?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div>
            </div>
            <?php endif; ?>

            <?php
            $oauthErrorMessages = [
                'account_suspended'       => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.',
                'google_auth_failed'      => 'L\'authentification Google a échoué. Veuillez réessayer.',
                'token_exchange_failed'   => 'Impossible d\'obtenir le jeton Google. Veuillez réessayer.',
                'failed_to_fetch_user_info' => 'Impossible de récupérer vos informations Google.',
                'user_creation_failed'    => 'Erreur lors de la création de votre compte. Contactez l\'administrateur.',
                'google_auth_error'       => 'Une erreur est survenue avec Google. Veuillez réessayer.',
            ];
            $errorCode = $_GET['error'] ?? '';
            if ($errorCode && isset($oauthErrorMessages[$errorCode])):
            ?>
            <div class="alert-error <?= $errorCode === 'account_suspended' ? 'alert-warning' : '' ?>">
                <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0;"><?= $errorCode === 'account_suspended' ? 'block' : 'error' ?></span>
                <div><p><?= htmlspecialchars($oauthErrorMessages[$errorCode]) ?></p></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <!-- Email -->
                <div class="field">
                    <label class="label" for="email">Adresse e-mail</label>
                    <div class="input-wrap">
                        <span class="input-icon"><span class="material-symbols-outlined">mail</span></span>
                        <input class="input" type="email" id="email" name="username"
                               placeholder="vous@exemple.com" autocomplete="email" required/>
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <div class="field-label-row">
                        <label class="label" for="password">Mot de passe</label>
                        <a href="/integration/forgot-password" class="forgot">Oublié ?</a>
                    </div>
                    <div class="input-wrap">
                        <span class="input-icon"><span class="material-symbols-outlined">lock</span></span>
                        <input class="input" type="password" id="password" name="password"
                               placeholder="••••••••" autocomplete="current-password" required style="padding-right:44px;"/>
                        <button type="button" class="toggle-pwd" onclick="togglePwd()">
                            <span class="material-symbols-outlined" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="check-row">
                    <input type="checkbox" id="remember" name="remember"/>
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <!-- reCAPTCHA -->
                <?php
                if (!class_exists('config')) require_once __DIR__ . '/../../config.php';
                $siteKey = \config::getRecaptchaSiteKey();
                ?>
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>

                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined" style="font-size:18px;">login</span>
                    Se Connecter
                </button>
            </form>

            <div class="divider"><span>ou continuer avec</span></div>

            <!-- Google -->
            <?php
            $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                'client_id'     => \config::getGoogleClientId(),
                'redirect_uri'  => \config::getGoogleRedirectUri(),
                'response_type' => 'code',
                'scope'         => 'openid email profile',
                'access_type'   => 'offline'
            ]);
            ?>
            <a href="<?= htmlspecialchars($googleAuthUrl) ?>" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Connexion avec Google
            </a>

            <p class="form-footer">Pas encore de compte ? <a href="/integration/register">Créer un compte</a></p>
        </div>
    </div>
</div>

<!-- Cookie Banner -->
<div class="cookie-banner" id="cookie-banner">
    <div style="display:flex;align-items:flex-start;gap:16px;flex:1;">
        <div style="width:40px;height:40px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span class="material-symbols-outlined" style="color:#004d99;font-size:20px;">cookie</span>
        </div>
        <div>
            <p style="font-weight:700;font-size:14px;color:#0f172a;margin-bottom:4px;">Nous respectons votre vie privée</p>
            <p style="font-size:13px;color:#64748b;line-height:1.5;">MediFlow utilise des cookies pour améliorer votre expérience et analyser le trafic. Vous pouvez accepter ou refuser les cookies non essentiels.</p>
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <button onclick="handleCookie('reject')" style="padding:10px 20px;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;font-weight:600;font-size:13px;cursor:pointer;">Refuser</button>
        <button onclick="handleCookie('accept')" style="padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#004d99,#0066cc);color:#fff;border:none;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 2px 8px rgba(0,77,153,.25);">Accepter</button>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
/* Password toggle */
function togglePwd() {
    const inp = document.getElementById('password');
    const eye = document.getElementById('eye-icon');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
}

/* Cookie */
document.addEventListener('DOMContentLoaded', () => {
    if (!localStorage.getItem('mediflow_cookie')) {
        setTimeout(() => {
            const b = document.getElementById('cookie-banner');
            b.style.display = 'flex';
            requestAnimationFrame(() => b.classList.add('visible'));
        }, 900);
    }
});
function handleCookie(action) {
    localStorage.setItem('mediflow_cookie', action);
    const b = document.getElementById('cookie-banner');
    b.classList.remove('visible');
    setTimeout(() => b.style.display = 'none', 450);
}

/* Particle canvas */
(function(){
    const canvas = document.getElementById('particles');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, dots = [];

    function resize(){
        W = canvas.width = canvas.offsetWidth;
        H = canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    for(let i = 0; i < 55; i++){
        dots.push({
            x: Math.random() * 1000,
            y: Math.random() * 1000,
            r: Math.random() * 1.4 + 0.4,
            vx: (Math.random() - .5) * .35,
            vy: (Math.random() - .5) * .35,
            a: Math.random() * .5 + .1
        });
    }

    function draw(){
        ctx.clearRect(0, 0, W, H);
        dots.forEach(d => {
            d.x += d.vx; d.y += d.vy;
            if(d.x < 0) d.x = W;
            if(d.x > W) d.x = 0;
            if(d.y < 0) d.y = H;
            if(d.y > H) d.y = 0;
            ctx.beginPath();
            ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${d.a})`;
            ctx.fill();
        });
        // Draw connecting lines
        for(let i = 0; i < dots.length; i++){
            for(let j = i+1; j < dots.length; j++){
                const dx = dots[i].x - dots[j].x;
                const dy = dots[i].y - dots[j].y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if(dist < 110){
                    ctx.beginPath();
                    ctx.moveTo(dots[i].x, dots[i].y);
                    ctx.lineTo(dots[j].x, dots[j].y);
                    ctx.strokeStyle = `rgba(255,255,255,${(1 - dist/110) * 0.08})`;
                    ctx.lineWidth = .6;
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>
</body>
</html>
