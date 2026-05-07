<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Mot de passe oublié — MediFlow</title>
    <meta name="description" content="Réinitialisez votre mot de passe MediFlow."/>
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

        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        @media(max-width: 900px) { .auth-page { grid-template-columns: 1fr; } }

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

        .auth-title { font-family: 'Manrope', sans-serif; font-size: 28px; font-weight: 900; color: #111827; margin-bottom: 6px; }
        .auth-subtitle { font-size: 14px; color: #6b7280; margin-bottom: 32px; }

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

        .auth-alert-success {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #16a34a;
        }
        .auth-alert-success .material-symbols-outlined { font-size: 18px; flex-shrink: 0; margin-top: 1px; }

        .auth-footer { text-align: center; font-size: 14px; color: #6b7280; margin-top: 24px; }
        .auth-footer a { color: #004d99; font-weight: 700; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; color: #004d99; text-decoration: none; font-weight: 600; margin-bottom: 24px; }
        .back-link:hover { text-decoration: underline; }
        .back-link .material-symbols-outlined { font-size: 18px; }
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
            <h1>Récupérez votre accès</h1>
            <p>Si vous avez oublié votre mot de passe, nous pouvons vous aider à le réinitialiser en quelques étapes simples.</p>

            <div class="auth-feature">
                <span class="material-symbols-outlined">lock_reset</span>
                <span class="auth-feature-text">Processus sécurisé et rapide</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">mail</span>
                <span class="auth-feature-text">Lien de réinitialisation par email</span>
            </div>
            <div class="auth-feature">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="auth-feature-text">Redéfinir votre mot de passe</span>
            </div>
        </div>
    </div>

    <!-- Right Panel — Password Reset Form -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <a href="/integration/login" class="back-link">
                <span class="material-symbols-outlined">arrow_back</span>
                Retour à la connexion
            </a>

            <h2 class="auth-title">Mot de passe oublié</h2>
            <p class="auth-subtitle">Entrez votre adresse email pour réinitialiser votre mot de passe</p>

            <?php if (!empty($errors)): ?>
            <div class="auth-alert-error">
                <span class="material-symbols-outlined">error</span>
                <div><?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="auth-alert-success">
                <span class="material-symbols-outlined">check_circle</span>
                <div>
                    <p><strong>Email envoyé avec succès!</strong></p>
                    <p>Vérifiez votre boîte email pour le lien de réinitialisation. Le lien expire dans 1 heure.</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST" action="" novalidate>
                <!-- Email -->
                <div class="field-group">
                    <label class="field-label" for="email">Adresse e-mail</label>
                    <div class="field-wrap">
                        <span class="field-icon"><span class="material-symbols-outlined">mail</span></span>
                        <input class="field-input" type="email" id="email" name="email"
                               placeholder="vous@exemple.com" value="<?= htmlspecialchars($email) ?>" required/>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="material-symbols-outlined" style="font-size:18px;">send</span>
                    Envoyer un lien de réinitialisation
                </button>
            </form>
            <?php endif; ?>

            <div class="auth-footer">
                <p>N'avez-vous pas encore de compte ? <a href="/integration/register">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
