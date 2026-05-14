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

<!-- Modern Floating Top Bar -->
<header class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-6xl bg-white/70 backdrop-blur-2xl border border-white/80 shadow-[0_8px_32px_rgba(0,77,153,0.08)] rounded-2xl transition-all duration-300 px-6 py-3 flex items-center justify-between animate-fadeInDown">
    
    <!-- Logo -->
    <a href="/integration/" class="flex items-center gap-3 group">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-blue-500 flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
            <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings:'FILL' 1">local_hospital</span>
        </div>
        <span class="text-xl font-black tracking-tight text-slate-800 font-headline group-hover:text-primary transition-colors duration-300">
            Medi<span class="text-primary">Flow</span>
        </span>
    </a>

    <!-- Center Nav Links -->
    <nav class="hidden md:flex items-center gap-1 bg-slate-100/50 p-1 rounded-xl border border-slate-200/50">
        <a href="#modules" class="relative px-4 py-1.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors rounded-lg hover:bg-white hover:shadow-sm">Modules</a>
        <a href="#specialites" class="relative px-4 py-1.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors rounded-lg hover:bg-white hover:shadow-sm">Spécialités</a>
        <a href="/integration/magazine" class="relative px-4 py-1.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors rounded-lg hover:bg-white hover:shadow-sm">Magazine</a>
    </nav>

    <!-- Call to Actions -->
    <div class="flex items-center gap-2">
        <a href="/integration/login"
           class="hidden sm:flex px-4 py-2 text-sm font-bold text-slate-600 hover:text-primary hover:bg-blue-50 rounded-xl transition-all items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">login</span> Connexion
        </a>
        <a href="/integration/register"
           class="px-5 py-2.5 bg-gradient-to-r from-primary to-blue-500 text-white text-sm font-bold rounded-xl shadow-[0_4px_14px_rgba(0,77,153,0.3)] hover:shadow-[0_6px_20px_rgba(0,77,153,0.4)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
            Créer un compte <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
</header>

<main class="pt-28">
