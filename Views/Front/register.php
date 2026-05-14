<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inscription — MediFlow</title>
    <meta name="description" content="Créez votre compte MediFlow gratuitement."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;min-height:100vh;}
        h1,h2,h3{font-family:'Manrope',sans-serif;}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}

        .shell{display:grid;grid-template-columns:1fr 1.3fr;min-height:100vh;}
        @media(max-width:860px){.shell{grid-template-columns:1fr;}.panel-left{display:none;}.panel-right{padding:32px 20px;}}

        /* LEFT */
        .panel-left{
            position:relative;
            background:linear-gradient(135deg,#020b18 0%,#041430 35%,#061d45 65%,#042a3a 100%);
            display:flex;flex-direction:column;justify-content:center;align-items:center;
            padding:60px 52px;overflow:hidden;
        }
        .orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;animation:orbF 8s ease-in-out infinite;}
        .o1{width:380px;height:380px;background:radial-gradient(circle,rgba(0,100,255,.22),transparent 70%);top:-80px;left:-80px;}
        .o2{width:300px;height:300px;background:radial-gradient(circle,rgba(0,180,160,.18),transparent 70%);bottom:-60px;right:-60px;animation-delay:-3s;}
        .o3{width:200px;height:200px;background:radial-gradient(circle,rgba(120,60,255,.15),transparent 70%);top:50%;left:50%;animation:orbF3 10s ease-in-out infinite;}
        @keyframes orbF{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-24px) scale(1.06);}}
        @keyframes orbF3{0%,100%{transform:translate(-50%,-50%) scale(1);}50%{transform:translate(-50%,-58%) scale(1.1);}}
        .grid-overlay{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:48px 48px;pointer-events:none;}
        #cvs{position:absolute;inset:0;pointer-events:none;}

        .lc{position:relative;z-index:2;max-width:340px;text-align:center;}
        .logo{display:inline-flex;align-items:center;gap:14px;margin-bottom:36px;}
        .logo-icon{width:56px;height:56px;background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,.05));border:1.5px solid rgba(255,255,255,.2);border-radius:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.15);}
        .logo-txt{font-family:'Manrope',sans-serif;font-size:30px;font-weight:900;color:#fff;letter-spacing:-.5px;}
        .logo-txt span{background:linear-gradient(135deg,#4dd9cf,#7cc8ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .headline{font-size:32px;font-weight:900;color:#fff;line-height:1.15;margin-bottom:12px;}
        .headline em{font-style:normal;background:linear-gradient(135deg,#60d4cb,#7bb3ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .sub{font-size:14px;color:rgba(255,255,255,.55);line-height:1.7;margin-bottom:32px;}

        .pill{display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:13px 18px;margin-bottom:10px;text-align:left;transition:background .2s;}
        .pill:hover{background:rgba(255,255,255,.1);}
        .pill-ic{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .pill-tx{font-size:13px;font-weight:600;color:rgba(255,255,255,.85);}

        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:28px;padding-top:24px;border-top:1px solid rgba(255,255,255,.08);}
        .step{text-align:center;}
        .step-n{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 6px;font-size:13px;font-weight:800;color:#fff;}
        .step-t{font-size:11px;color:rgba(255,255,255,.45);font-weight:600;}

        /* RIGHT */
        .panel-right{background:#f8fafc;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:48px 52px;overflow-y:auto;}
        .fw{width:100%;max-width:460px;animation:sIn .6s cubic-bezier(.16,1,.3,1) both;}
        @keyframes sIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

        .eyebrow{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:#004d99;background:#e8f0fe;border:1px solid #c7d9f8;border-radius:20px;padding:5px 12px;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;}
        .ftitle{font-family:'Manrope',sans-serif;font-size:26px;font-weight:900;color:#0f172a;margin-bottom:4px;}
        .fsub{font-size:13px;color:#64748b;margin-bottom:22px;}

        .alert-err{display:flex;align-items:flex-start;gap:10px;background:#fff5f5;border:1px solid #fecaca;border-radius:12px;padding:13px 16px;margin-bottom:16px;font-size:13px;color:#dc2626;}

        .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        @media(max-width:500px){.row2{grid-template-columns:1fr;}}

        .fld{margin-bottom:14px;}
        .lbl{display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
        .iw{position:relative;display:flex;align-items:center;}
        .iico{position:absolute;left:13px;color:#94a3b8;pointer-events:none;display:flex;}
        .iico .material-symbols-outlined{font-size:17px;}
        .inp{width:100%;padding:12px 13px 12px 42px;background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;font-size:13.5px;font-family:'Inter',sans-serif;color:#0f172a;outline:none;transition:border-color .15s,box-shadow .15s;}
        .inp:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.1);}
        .inp::placeholder{color:#cbd5e1;}
        .tog{position:absolute;right:12px;background:none;border:none;cursor:pointer;color:#94a3b8;padding:3px;display:flex;transition:color .15s;}
        .tog:hover{color:#004d99;}
        .tog .material-symbols-outlined{font-size:17px;}
        .hint{font-size:11.5px;color:#94a3b8;margin-top:4px;}
        .err-hint{font-size:11.5px;color:#dc2626;margin-top:4px;display:none;}

        /* Password strength bar */
        .str-bar{height:3px;border-radius:99px;background:#e2e8f0;margin-top:8px;overflow:hidden;}
        .str-fill{height:100%;border-radius:99px;width:0;transition:width .3s,background .3s;}

        .chk-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:16px;}
        .chk-row input[type=checkbox]{width:17px;height:17px;flex-shrink:0;accent-color:#004d99;cursor:pointer;margin-top:2px;}
        .chk-row label{font-size:12.5px;color:#374151;cursor:pointer;line-height:1.5;}
        .chk-row a{color:#004d99;font-weight:700;text-decoration:none;}
        .chk-row a:hover{text-decoration:underline;}

        .btn-primary{width:100%;padding:14px;background:linear-gradient(135deg,#004d99,#0066cc);color:#fff;border:none;border-radius:13px;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(0,77,153,.28);transition:all .2s;}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,77,153,.36);}

        .div{display:flex;align-items:center;gap:12px;margin:18px 0;}
        .div::before,.div::after{content:'';flex:1;height:1px;background:#e2e8f0;}
        .div span{font-size:12px;color:#94a3b8;font-weight:600;white-space:nowrap;}

        .btn-google{width:100%;padding:13px 14px;background:#fff;border:1.5px solid #e2e8f0;border-radius:13px;font-size:14px;font-weight:700;color:#374151;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:10px;transition:all .2s;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.06);}
        .btn-google:hover{border-color:#c7d9f8;background:#f8faff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.08);}

        .ffooter{text-align:center;font-size:13px;color:#64748b;margin-top:18px;}
        .ffooter a{color:#004d99;font-weight:700;text-decoration:none;}
        .ffooter a:hover{text-decoration:underline;}

        .recaptcha-wrap{margin-bottom:16px;transform:scale(.95);transform-origin:left center;}
        @media(max-width:380px){.recaptcha-wrap{transform:scale(.8);}}
    </style>
</head>
<body>
<div class="shell">

    <!-- LEFT -->
    <div class="panel-left">
        <div class="orb o1"></div>
        <div class="orb o2"></div>
        <div class="orb o3"></div>
        <div class="grid-overlay"></div>
        <canvas id="cvs"></canvas>

        <div class="lc">
            <div class="logo">
                <div class="logo-icon">
                    <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1">local_hospital</span>
                </div>
                <span class="logo-txt">Medi<span>Flow</span></span>
            </div>

            <h1 class="headline">Rejoignez<br/><em>la communauté</em><br/>MediFlow.</h1>
            <p class="sub">Créez votre compte en moins d'une minute et accédez à l'ensemble de nos services de santé connectée.</p>

            <div class="pill">
                <div class="pill-ic" style="background:rgba(34,211,177,.12);">
                    <span class="material-symbols-outlined" style="color:#22d3b1;font-size:19px;font-variation-settings:'FILL' 1">calendar_month</span>
                </div>
                <span class="pill-tx">Prise de RDV en ligne simplifiée</span>
            </div>
            <div class="pill">
                <div class="pill-ic" style="background:rgba(96,165,250,.12);">
                    <span class="material-symbols-outlined" style="color:#60a5fa;font-size:19px;font-variation-settings:'FILL' 1">medical_services</span>
                </div>
                <span class="pill-tx">Location d'équipements médicaux certifiés</span>
            </div>
            <div class="pill">
                <div class="pill-ic" style="background:rgba(167,139,250,.12);">
                    <span class="material-symbols-outlined" style="color:#a78bfa;font-size:19px;font-variation-settings:'FILL' 1">newspaper</span>
                </div>
                <span class="pill-tx">Magazine médical assisté par IA</span>
            </div>

            <div class="steps">
                <div class="step"><div class="step-n">1</div><div class="step-t">Créer un compte</div></div>
                <div class="step"><div class="step-n">2</div><div class="step-t">Compléter le profil</div></div>
                <div class="step"><div class="step-n">3</div><div class="step-t">Accéder aux services</div></div>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="panel-right">
        <div class="fw">
            <div class="eyebrow">
                <span class="material-symbols-outlined" style="font-size:13px;">person_add</span>
                Inscription Gratuite
            </div>
            <h2 class="ftitle">Créer un compte Patient ✨</h2>
            <p class="fsub">Rejoignez la communauté MediFlow — c'est gratuit</p>

            <?php if (!empty($errors)): ?>
            <div class="alert-err">
                <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0;margin-top:1px;">error</span>
                <div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            </div>
            <?php endif; ?>
            <div id="registerErrors" class="alert-err" style="display:none;"></div>

            <form id="registerForm" method="POST" novalidate>
                <!-- Name row -->
                <div class="row2">
                    <div class="fld">
                        <label class="lbl" for="firstName">Prénom</label>
                        <div class="iw">
                            <span class="iico"><span class="material-symbols-outlined">person</span></span>
                            <input class="inp" type="text" id="firstName" name="firstName" placeholder="Jean" required/>
                        </div>
                        <span class="err-hint" id="errorFirstName"></span>
                    </div>
                    <div class="fld">
                        <label class="lbl" for="lastName">Nom</label>
                        <div class="iw">
                            <span class="iico"><span class="material-symbols-outlined">person</span></span>
                            <input class="inp" type="text" id="lastName" name="lastName" placeholder="Dupont" required/>
                        </div>
                        <span class="err-hint" id="errorLastName"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="fld">
                    <label class="lbl" for="registerEmail">Adresse e-mail</label>
                    <div class="iw">
                        <span class="iico"><span class="material-symbols-outlined">mail</span></span>
                        <input class="inp" type="email" id="registerEmail" name="email" placeholder="vous@exemple.com" required/>
                    </div>
                    <span class="err-hint" id="errorEmail"></span>
                </div>

                <!-- Phone -->
                <div class="fld">
                    <label class="lbl" for="phone">Téléphone <span style="color:#94a3b8;font-weight:400;text-transform:none;">(optionnel)</span></label>
                    <div class="iw">
                        <span class="iico"><span class="material-symbols-outlined">phone</span></span>
                        <input class="inp" type="tel" id="phone" name="phone" placeholder="+216 XX XXX XXX"/>
                    </div>
                    <span class="err-hint" id="errorPhone"></span>
                </div>

                <!-- Password -->
                <div class="fld">
                    <label class="lbl" for="registerPassword">Mot de passe</label>
                    <div class="iw">
                        <span class="iico"><span class="material-symbols-outlined">lock</span></span>
                        <input class="inp" type="password" id="registerPassword" name="password"
                               placeholder="••••••••" required style="padding-right:42px;" oninput="updateStrength(this.value)"/>
                        <button type="button" class="tog" onclick="togglePwd('registerPassword','eye-pass')">
                            <span class="material-symbols-outlined" id="eye-pass">visibility</span>
                        </button>
                    </div>
                    <div class="str-bar"><div class="str-fill" id="str-fill"></div></div>
                    <p class="hint" id="str-label">Minimum 8 caractères avec majuscules et chiffres</p>
                    <span class="err-hint" id="errorPassword"></span>
                </div>

                <!-- Confirm Password -->
                <div class="fld">
                    <label class="lbl" for="confirmPassword">Confirmer le mot de passe</label>
                    <div class="iw">
                        <span class="iico"><span class="material-symbols-outlined">lock</span></span>
                        <input class="inp" type="password" id="confirmPassword" name="confirmPassword"
                               placeholder="••••••••" required style="padding-right:42px;"/>
                        <button type="button" class="tog" onclick="togglePwd('confirmPassword','eye-conf')">
                            <span class="material-symbols-outlined" id="eye-conf">visibility</span>
                        </button>
                    </div>
                    <span class="err-hint" id="errorConfirmPassword"></span>
                </div>

                <!-- Terms -->
                <div class="chk-row">
                    <input type="checkbox" id="terms" name="terms" required/>
                    <label for="terms">J'accepte les <a href="/integration/terms" target="_blank">Conditions d'Utilisation</a> et la politique de confidentialité</label>
                </div>
                <span class="err-hint" id="errorTerms"></span>

                <!-- reCAPTCHA -->
                <?php
                if (!class_exists('config')) require_once __DIR__ . '/../../config.php';
                $siteKey = \config::getRecaptchaSiteKey();
                ?>
                <div class="recaptcha-wrap">
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>
                </div>

                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined" style="font-size:18px;">how_to_reg</span>
                    S'inscrire en tant que Patient
                </button>
            </form>

            <div class="div"><span>ou s'inscrire avec</span></div>

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
                S'inscrire avec Google
            </a>

            <p class="ffooter">Déjà inscrit ? <a href="/integration/login">← Se connecter</a></p>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="assets/js/register.js"></script>
<script>
function togglePwd(id, eyeId) {
    const inp = document.getElementById(id);
    const eye = document.getElementById(eyeId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    eye.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
}

function updateStrength(val) {
    const fill = document.getElementById('str-fill');
    const label = document.getElementById('str-label');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        {w:'0%',   bg:'#e2e8f0', tx:'Minimum 8 caractères avec majuscules et chiffres'},
        {w:'25%',  bg:'#ef4444', tx:'Très faible'},
        {w:'50%',  bg:'#f97316', tx:'Faible'},
        {w:'75%',  bg:'#eab308', tx:'Moyen'},
        {w:'100%', bg:'#22c55e', tx:'Fort 💪'},
    ];
    const l = levels[val.length === 0 ? 0 : score];
    fill.style.width = l.w;
    fill.style.background = l.bg;
    label.textContent = l.tx;
    label.style.color = val.length === 0 ? '#94a3b8' : l.bg;
}

/* Particle canvas — same as login */
(function(){
    const canvas = document.getElementById('cvs');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, dots = [];
    function resize(){ W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; }
    resize();
    window.addEventListener('resize', resize);
    for (let i = 0; i < 55; i++) dots.push({x:Math.random()*1000,y:Math.random()*1000,r:Math.random()*1.4+.4,vx:(Math.random()-.5)*.35,vy:(Math.random()-.5)*.35,a:Math.random()*.5+.1});
    function draw(){
        ctx.clearRect(0,0,W,H);
        dots.forEach(d=>{
            d.x+=d.vx; d.y+=d.vy;
            if(d.x<0)d.x=W; if(d.x>W)d.x=0;
            if(d.y<0)d.y=H; if(d.y>H)d.y=0;
            ctx.beginPath(); ctx.arc(d.x,d.y,d.r,0,Math.PI*2);
            ctx.fillStyle=`rgba(255,255,255,${d.a})`; ctx.fill();
        });
        for(let i=0;i<dots.length;i++) for(let j=i+1;j<dots.length;j++){
            const dx=dots[i].x-dots[j].x, dy=dots[i].y-dots[j].y, dist=Math.sqrt(dx*dx+dy*dy);
            if(dist<110){ ctx.beginPath(); ctx.moveTo(dots[i].x,dots[i].y); ctx.lineTo(dots[j].x,dots[j].y); ctx.strokeStyle=`rgba(255,255,255,${(1-dist/110)*.08})`; ctx.lineWidth=.6; ctx.stroke(); }
        }
        requestAnimationFrame(draw);
    }
    draw();
})();
</script>
</body>
</html>
