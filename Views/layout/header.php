<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Votre santé, connectée et simplifiée</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/integration/assets/css/style.css">
    <link rel="icon" href="/integration/assets/images/favicon.svg" type="image/svg+xml">
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary":                  "#004d99",
                    "primary-container":        "#1565c0",
                    "primary-fixed":            "#d6e3ff",
                    "on-primary":               "#ffffff",
                    "on-primary-fixed":         "#001b3d",
                    "secondary":                "#4a5f83",
                    "tertiary":                 "#005851",
                    "tertiary-fixed":           "#84f5e8",
                    "on-tertiary-fixed":        "#00201d",
                    "error":                    "#ba1a1a",
                    "surface":                  "#f7f9fb",
                    "surface-container-low":    "#f2f4f6",
                    "surface-container":        "#eceef0",
                    "on-surface":               "#191c1e",
                    "on-surface-variant":       "#424752",
                    "outline":                  "#727783",
                    "outline-variant":          "#c2c6d4",
                }
            }
        }
    };
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        @keyframes fadeInDown {
            from { opacity:0; transform: translateY(-16px); }
            to   { opacity:1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity:0; transform: translateY(20px); }
            to   { opacity:1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity:0; transform: translateY(-10px); }
            to   { opacity:1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<!-- Top nav bar (matches magazine front-office) -->
<header class="fixed top-0 left-0 right-0 z-40 h-16 bg-white/90 backdrop-blur-xl border-b border-outline-variant/30 shadow-[0_1px_12px_rgba(0,77,153,0.06)]">
    <div class="max-w-7xl mx-auto h-full px-6 flex items-center justify-between">

        <!-- Logo -->
        <a href="/integration/" class="flex items-center gap-3 group">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-white text-lg" style="font-variation-settings:'FILL' 1">local_hospital</span>
            </div>
            <span class="text-lg font-black tracking-tight text-on-surface font-headline group-hover:text-primary transition-colors">
                Medi<span class="text-primary">Flow</span>
            </span>
        </a>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="#services"    class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">Services</a>
            <a href="#modules"     class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">Modules</a>
            <a href="#specialites" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">Spécialités</a>
            <a href="/integration/magazine" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">Magazine</a>
        </nav>

        <!-- CTA -->
        <div class="flex items-center gap-3">
            <a href="/integration/login"
               class="text-sm font-semibold text-primary hover:text-primary-container transition-colors">
                Connexion
            </a>
            <a href="/integration/register"
               class="px-4 py-2 bg-primary text-on-primary text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                Créer un compte
            </a>
        </div>
    </div>
</header>

<main class="pt-16">
