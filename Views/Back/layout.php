<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MediFlow — Admin Portal</title>
    <meta name="description" content="MediFlow back-office portal — manage users, magazine, and equipment."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary":                  "#004d99",
                    "primary-container":        "#1565c0",
                    "primary-fixed":            "#d6e3ff",
                    "primary-fixed-dim":        "#a9c7ff",
                    "on-primary":               "#ffffff",
                    "on-primary-fixed":         "#001b3d",
                    "on-primary-fixed-variant": "#00468c",
                    "secondary":                "#4a5f83",
                    "secondary-container":      "#c0d5ff",
                    "secondary-fixed":          "#d6e3ff",
                    "secondary-fixed-dim":      "#b2c7f1",
                    "on-secondary":             "#ffffff",
                    "on-secondary-fixed":       "#021b3c",
                    "on-secondary-fixed-variant":"#32476a",
                    "on-secondary-container":   "#475c80",
                    "tertiary":                 "#005851",
                    "tertiary-container":       "#00736a",
                    "tertiary-fixed":           "#84f5e8",
                    "tertiary-fixed-dim":       "#66d9cc",
                    "on-tertiary":              "#ffffff",
                    "on-tertiary-fixed":        "#00201d",
                    "on-tertiary-fixed-variant":"#005049",
                    "on-tertiary-container":    "#87f8ea",
                    "error":                    "#ba1a1a",
                    "error-container":          "#ffdad6",
                    "on-error":                 "#ffffff",
                    "on-error-container":       "#93000a",
                    "surface":                  "#f7f9fb",
                    "surface-dim":              "#d8dadc",
                    "surface-bright":           "#f7f9fb",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low":    "#f2f4f6",
                    "surface-container":        "#eceef0",
                    "surface-container-high":   "#e6e8ea",
                    "surface-container-highest":"#e0e3e5",
                    "surface-variant":          "#e0e3e5",
                    "on-surface":               "#191c1e",
                    "on-surface-variant":       "#424752",
                    "outline":                  "#727783",
                    "outline-variant":          "#c2c6d4",
                    "inverse-surface":          "#2d3133",
                    "inverse-on-surface":       "#eff1f3",
                    "inverse-primary":          "#a9c7ff",
                    "background":               "#f7f9fb",
                    "on-background":            "#191c1e",
                    "surface-tint":             "#005db7",
                },
                fontFamily: {
                    headline: ["Manrope", "sans-serif"],
                    body:     ["Inter", "sans-serif"],
                    label:    ["Inter", "sans-serif"],
                }
            }
        }
    };
    </script>
    <link rel="stylesheet" href="/integration/assets/css_magazine/style.css"/>
    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6,.font-headline { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* ── Sidebar nav group accordion ── */
        details.nav-group summary { list-style: none; cursor: pointer; }
        details.nav-group summary::-webkit-details-marker { display: none; }
        details.nav-group[open] .chevron { transform: rotate(90deg); }
        .chevron { transition: transform 0.2s ease; }

        /* ── Active nav link ── */
        .nav-link-active {
            background: linear-gradient(90deg, #d6e3ff 0%, #f2f4f6 100%);
            color: #004d99 !important;
            font-weight: 700;
            border-right: 3px solid #84f5e8;
        }

        /* ── Toast notifications ── */
        @keyframes slideDownIn { from { opacity:0; transform: translateY(-12px); } to { opacity:1; transform: translateY(0); } }
        .toast-notification { animation: slideDownIn .35s ease-out both; }

        /* ── Sidebar scrollbar ── */
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: #c2c6d4; border-radius: 2px; }
    </style>
    
    <!-- Intro.js for onboarding tour (always loaded for Patient so Restart button works) -->
    <?php if (isset($data['show_tour'])): ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
    <link href="/integration/assets/css/tour.css" rel="stylesheet">
    <script src="/integration/assets/js/tour.js"></script>
    <?php endif; ?>
</head>
<body class="bg-surface text-on-surface" data-show-tour="<?= ($data['show_tour'] ?? false) ? 'true' : 'false' ?>">

<?php
/* ──────────────────────────────────────────────
   Navigation helpers
   ────────────────────────────────────────────── */
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$role        = trim($_SESSION['user']['role'] ?? '');
$userName    = trim(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? 'User'));
$userInitial = strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1));

// ── Notifications dynamiques ───────────────────────────────────────
$_notifUserId  = (int)($_SESSION['user']['id'] ?? 0);
$_notifUnread  = 0;
$_notifList    = [];
if ($_notifUserId > 0) {
    require_once __DIR__ . '/../../Models/NotificationModel.php';
    $_nm          = new NotificationModel();
    $_notifUnread = $_nm->countUnread($_notifUserId);
    $_notifList   = $_nm->getByMedecin($_notifUserId, 8);
}
$_notifIcons = [
    'new_demande'     => ['icon' => 'assignment',   'color' => 'text-blue-500',    'bg' => 'bg-blue-50'],
    'demande_traitee' => ['icon' => 'check_circle', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
    'demande_refusee' => ['icon' => 'cancel',       'color' => 'text-red-500',     'bg' => 'bg-red-50'],
];

/* Returns Tailwind classes for a sidebar link */
function sidebarLink(string $href, string $currentUri, array $excludes = []): string {
    $currentParts = parse_url($currentUri);
    $hrefParts = parse_url($href);
    
    $cPath = rtrim($currentParts['path'] ?? '', '/');
    $hPath = rtrim($hrefParts['path'] ?? '', '/');
    
    foreach ($excludes as $exclude) {
        $excludePath = rtrim(parse_url($exclude, PHP_URL_PATH) ?? '', '/');
        if ($cPath === $excludePath || str_starts_with($cPath, $excludePath . '/')) {
            return 'text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary';
        }
    }

    if ($cPath === $hPath) {
        parse_str($currentParts['query'] ?? '', $currentQuery);
        parse_str($hrefParts['query'] ?? '', $hrefQuery);
        
        $cAction = $currentQuery['action'] ?? '';
        $hAction = $hrefQuery['action'] ?? '';
        
        if ($cPath === '/integration/admin') {
            if ($cAction === '') $cAction = 'list';
            if ($hAction === '') $hAction = 'list';
        }
        
        if ($cAction === $hAction) {
            return 'nav-link-active';
        }
    }
    
    if (strlen($hPath) > 20 && str_starts_with($cPath, $hPath . '/')) {
        return 'nav-link-active';
    }
           
    return 'text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary';
}

/* Auto-open a details group if current path starts with given prefix */
function groupOpen(string $prefix, string $currentUri): string {
    $path = parse_url($currentUri, PHP_URL_PATH);
    if ($prefix === '/integration/admin' && str_starts_with($path, '/integration/magazine/admin')) {
        return '';
    }
    return str_starts_with($path, $prefix) ? 'open' : '';
}

// Alertes stock bas — pharmacien & Admin uniquement
$stockAlerts = [];
if (in_array($role, ['Admin', 'pharmacien'])) {
    try {
        if (!class_exists('config'))  require_once __DIR__ . '/../../config.php';
        if (!class_exists('Product')) require_once __DIR__ . '/../../Models/Product.php';
        $stockAlerts = (new \Product())->getLowStock();
    } catch (\Throwable $e) { /* ne pas casser le layout */ }
}
?>

<!-- ═══════════════════════════════════════════ -->
<!-- SIDEBAR                                      -->
<!-- ═══════════════════════════════════════════ -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-white border-r border-outline-variant/40 flex flex-col z-40 overflow-y-auto shadow-[2px_0_20px_rgba(0,77,153,0.06)]">

    <!-- Logo -->
    <div class="px-6 py-7 border-b border-outline-variant/20 flex-shrink-0">
        <a href="/integration/dashboard" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-md">
                <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1">local_hospital</span>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-on-surface font-headline">Medi<span class="text-primary">Flow</span></span>
                <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-widest leading-none">Admin Portal</p>
            </div>
        </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-1">

        <!-- Overview -->
        <a href="/integration/dashboard"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 <?= sidebarLink('/integration/dashboard', $currentPath) ?>">
            <span class="material-symbols-outlined text-xl">dashboard</span>
            <span>Overview</span>
        </a>

        <!-- ── User Management (Admin only) ── -->
        <?php if ($role === 'Admin'): ?>
        <details class="nav-group" <?= groupOpen('/integration/admin', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">manage_accounts</span>
                <span class="flex-1">Gestion des utilisateurs</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-primary-fixed space-y-0.5">
                <a href="/integration/admin"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/admin', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">people</span>
                    <span>Tous les utilisateurs</span>
                </a>
                <a href="/integration/admin?action=create"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/admin?action=create', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">person_add</span>
                    <span>Ajouter un utilisateur</span>
                </a>
                <a href="/integration/admin?action=logs"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/admin?action=logs', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">history</span>
                    <span>Logs d'activité</span>
                </a>
            </div>
        </details>

        <!-- ── Rendez-vous (Admin) ── -->
        <details class="nav-group" <?= groupOpen('/integration/rdv', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
                <span class="flex-1">Rendez-vous</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-primary-fixed space-y-0.5">
                <a href="/integration/rdv/admin"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/admin', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    <span>Tableau de bord</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Magazine (Admin + Magazine) ── -->
        <?php if (in_array($role, ['Admin', 'redacteur'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/magazine/admin', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">newspaper</span>
                <span class="flex-1">Magazine</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-tertiary-fixed space-y-0.5">
                <a href="/integration/magazine/admin"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/magazine/admin', $currentPath, ['/integration/magazine/admin/stats', '/integration/magazine/admin/articles', '/integration/magazine/admin/article-form', '/integration/magazine/admin/comments', '/integration/magazine/admin/save', '/integration/magazine/admin/delete', '/integration/magazine/admin/rephrase']) ?>">
                    <span class="material-symbols-outlined text-base">bar_chart</span>
                    <span>Tableau de bord</span>
                </a>
                <a href="/integration/magazine/admin/stats"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/magazine/admin/stats', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">analytics</span>
                    <span>Statistiques</span>
                </a>
                <a href="/integration/magazine/admin/articles"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/magazine/admin/articles', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">article</span>
                    <span>Bibliotheque de contenu</span>
                </a>
                <a href="/integration/magazine/admin/comments"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/magazine/admin/comments', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">forum</span>
                    <span>Commentaires</span>
                </a>
                <a href="/integration/magazine/admin/article-form"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/magazine/admin/article-form', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Nouvel article</span>
                </a>
                <a href="/integration/magazine"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary" target="_blank">
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                      <span>Voir le Magazine</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Equipment (Admin + Equipment) ── -->
        <?php if (in_array($role, ['Admin', 'Technicien'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/equipements', $currentPath) || groupOpen('/integration/historique', $currentPath) ? 'open' : '' ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">medical_services</span>
                  <span class="flex-1">Équipements</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-secondary-fixed space-y-0.5">
                <a href="/integration/equipements"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/equipements', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">inventory_2</span>
                      <span>Liste des équipements</span>
                  </a>
                  <a href="/integration/historique-location"
                     class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/historique-location', $currentPath) ?>">
                      <span class="material-symbols-outlined text-base">history</span>
                      <span>Historique de location</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Patient ── -->
        <?php if ($role === 'Patient'): ?>
        <details class="nav-group" <?= groupOpen('/integration/catalogue', $currentPath) || groupOpen('/integration/mes-reservations', $currentPath) || groupOpen('/integration/mes-favoris', $currentPath) ? 'open' : '' ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">medical_services</span>
                <span class="flex-1">Equipment Rental</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-secondary-fixed space-y-0.5">
                <a href="/integration/catalogue"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/catalogue', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">store</span>
                    <span>Catalogue</span>
                </a>
                <a href="/integration/mes-reservations"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/mes-reservations', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">shopping_cart</span>
                    <span>mes reservations</span>
                </a>
                <a href="/integration/mes-favoris"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/mes-favoris', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">favorite</span>
                    <span>mes favoris</span>
                </a>
            </div>
        </details>

        <!-- Magazine for Patient -->
        <a href="/integration/magazine"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 <?= sidebarLink('/integration/magazine', $currentPath) ?>">
            <span class="material-symbols-outlined text-xl">newspaper</span>
            <span>Medical Magazine</span>
        </a>
        <?php endif; ?>

        <!-- ── Medecin module ── -->
        <?php if ($role === 'Medecin'): ?>
        <details class="nav-group" <?= groupOpen('/integration/rdv', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
                <span class="flex-1">Rendez-vous</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-primary-fixed space-y-0.5">
                <a href="/integration/rdv/dashboard"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/dashboard', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    <span>Mes RDV</span>
                </a>
                <a href="/integration/rdv/planning"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/planning', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">event_note</span>
                    <span>Planning</span>
                </a>
                <a href="/integration/rdv/statistiques"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/statistiques', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">bar_chart</span>
                    <span>Statistiques</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Patient — Rendez-vous ── -->
        <?php if ($role === 'Patient'): ?>
        <details class="nav-group" <?= groupOpen('/integration/rdv', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
                <span class="flex-1">Rendez-vous</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-primary-fixed space-y-0.5">
                <a href="/integration/rdv/annuaire"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/annuaire', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">person_search</span>
                    <span>Trouver un médecin</span>
                </a>
                <a href="/integration/rdv/mes-rdv"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/rdv/mes-rdv', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">event_available</span>
                    <span>Mes rendez-vous</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Stock Médicament (Pharmacien) ── -->
        <?php if (in_array($role, ['Admin', 'pharmacien'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/stock', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">inventory_2</span>
                <span class="flex-1">Stock Médicament</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-primary-fixed space-y-0.5">
                <a href="/integration/stock/products"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/stock/products', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">medication</span>
                    <span>Produits</span>
                </a>
                <a href="/integration/stock/orders"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/stock/orders', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">receipt_long</span>
                    <span>Commandes</span>
                </a>
                <a href="/integration/stock/cart"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/stock/cart', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">shopping_cart</span>
                    <span>Panier</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Fournisseur — CRUD produits ── -->
        <?php if (in_array($role, ['Admin', 'Fournisseur'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/fournisseur', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">local_shipping</span>
                <span class="flex-1">Fournisseur</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-tertiary-fixed space-y-0.5">
                <a href="/integration/fournisseur/products"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/fournisseur/products', $currentPath, ['/integration/fournisseur/products/create']) ?>">
                    <span class="material-symbols-outlined text-base">inventory_2</span>
                    <span>Catalogue produits</span>
                </a>
                <a href="/integration/fournisseur/products/create"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/fournisseur/products/create', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">add_box</span>
                    <span>Ajouter produit</span>
                </a>
                <a href="/integration/fournisseur/orders"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/fournisseur/orders', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">receipt</span>
                    <span>Commandes reçues</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Dossier Médical — Admin ── -->
        <?php if (in_array($role, ['Admin', 'Administrateur'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/dossier/admin', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">folder_shared</span>
                <span class="flex-1">Dossier Médical</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-tertiary-fixed space-y-0.5">
                <a href="/integration/dossier/admin"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/dossier/admin', $currentPath, []) ?>">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    <span>Tableau de bord</span>
                </a>
                <a href="/integration/dossier/admin/doctors"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/dossier/admin/doctors', $currentPath, ['/integration/dossier/admin/doctors/edit','/integration/dossier/admin/doctors/patients']) ?>">
                    <span class="material-symbols-outlined text-base">group</span>
                    <span>Médecins</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Dossier Médical — Médecin ── -->
        <?php if (in_array($role, ['Medecin'])): ?>
        <details class="nav-group" <?= groupOpen('/integration/dossier', $currentPath) ?>>
            <summary class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 select-none">
                <span class="material-symbols-outlined text-xl">stethoscope</span>
                <span class="flex-1">Dossier Médical</span>
                <span class="material-symbols-outlined text-base chevron">chevron_right</span>
            </summary>
            <div class="mt-1 ml-4 pl-3 border-l-2 border-tertiary-fixed space-y-0.5">
                <a href="/integration/dossier/patients"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/dossier/patients', $currentPath, ['/integration/dossier/view','/integration/dossier/nouvelle-consultation','/integration/dossier/consultation']) ?>">
                    <span class="material-symbols-outlined text-base">groups</span>
                    <span>Mes Patients</span>
                </a>
                <a href="/integration/dossier/ordonnances"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/dossier/ordonnances', $currentPath, ['/integration/dossier/ordonnance']) ?>">
                    <span class="material-symbols-outlined text-base">receipt_long</span>
                    <span>Ordonnances</span>
                </a>
                <a href="/integration/dossier/demandes"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150 <?= sidebarLink('/integration/dossier/demandes', $currentPath) ?>">
                    <span class="material-symbols-outlined text-base">inbox</span>
                    <span>Demandes</span>
                </a>
            </div>
        </details>
        <?php endif; ?>

        <!-- ── Mon Dossier — Patient ── -->
        <?php if (in_array($role, ['Patient'])): ?>
        <a href="/integration/dossier/patient"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 <?= sidebarLink('/integration/dossier/patient', $currentPath) ?>">
            <span class="material-symbols-outlined text-xl">folder_open</span>
            <span>Mon Dossier Médical</span>
        </a>
        <?php endif; ?>

    </nav><!-- /nav -->


    <!-- Sidebar bottom: Profile widget + Logout -->
    <div class="flex-shrink-0 border-t border-outline-variant/30 p-3 space-y-1">

        <!-- Profile card -->
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-primary-fixed/20">
            <!-- Avatar: real pic or initial -->
            <div class="w-9 h-9 rounded-full flex-shrink-0 overflow-hidden bg-primary-container flex items-center justify-center text-on-primary text-sm font-bold ring-2 ring-primary/20">
                <?php
                $sidebarPic = $_SESSION['user']['profile_pic'] ?? null;
                if (!empty($sidebarPic)): ?>
                      <img src="<?= htmlspecialchars($sidebarPic) ?><?= strpos($sidebarPic, '?') === false ? '?' : '&' ?>t=<?= time() ?>" alt="Profile" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                  <?php endif; ?>
                  <span class="<?= !empty($sidebarPic) ? 'hidden' : '' ?>"><?= $userInitial ?></span>
            </div>
            <!-- Name + role -->
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-on-surface truncate"><?= htmlspecialchars($userName) ?></p>
                <p class="text-[10px] text-on-surface-variant truncate"><?= htmlspecialchars($role) ?></p>
            </div>
        </div>

        <!-- Mon Profil -->
        <a href="/integration/profile"
           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-primary-fixed/40 hover:text-primary transition-all duration-150 group">
            <span class="material-symbols-outlined text-xl group-hover:text-primary transition-colors" style="font-variation-settings:'FILL' 0">manage_accounts</span>
            <span>Mon Profil</span>
        </a>

        <!-- Logout -->
        <a href="/integration/logout"
           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-error-container/30 hover:text-error transition-all duration-150 group">
            <span class="material-symbols-outlined text-xl group-hover:text-error transition-colors">logout</span>
            <span>Déconnexion</span>
        </a>
    </div>

</aside>

<!-- ═══════════════════════════════════════════ -->
<!-- MAIN CONTENT                                 -->
<!-- ═══════════════════════════════════════════ -->
<main class="ml-64 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="sticky top-0 z-50 h-16 bg-white/80 backdrop-blur-xl border-b border-outline-variant/30 flex items-center justify-between px-8 shadow-[0_1px_12px_rgba(0,77,153,0.06)]">
        <!-- Left: page context breadcrumb / search -->
        <div class="flex items-center gap-4">
            <form method="GET" action="/integration/magazine/admin/articles" class="hidden md:flex items-center gap-2 bg-surface-container-low border border-outline-variant/30 rounded-full px-4 py-2 text-sm focus-within:ring-2 focus-within:ring-primary/20">
                <span class="material-symbols-outlined text-on-surface-variant text-base">search</span>
                <input type="text" name="search" placeholder="Search articles…"
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       class="bg-transparent border-none focus:ring-0 outline-none text-sm w-44 text-on-surface placeholder:text-on-surface-variant"/>
            </form>
        </div>

        <!-- Right: notification + user -->
        <div class="flex items-center gap-4">


            <!-- Notification bell -->
            <div class="relative" id="boNotifWrap">
              <button id="boNotifBtn"
                      class="relative p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/30 rounded-xl transition-all active:scale-95"
                      title="Notifications">
                <span class="material-symbols-outlined text-xl">notifications</span>
                <span id="boNotifBadge"
                      class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                </span>
              </button>

              <!-- Dropdown -->
              <div id="boNotifDropdown"
                   class="hidden fixed w-80 bg-white rounded-2xl shadow-2xl shadow-blue-900/10 border border-surface-container overflow-hidden"
                   style="z-index:9999;animation:slideDownIn .2s ease-out">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-surface-container">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-sm">
                      <span class="material-symbols-outlined text-white text-[15px]">notifications</span>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Notifications</p>
                  </div>
                  <button id="boNotifMarkAll" class="text-[11px] text-primary font-bold hover:underline">
                    Mark all read
                  </button>
                </div>
                <!-- List -->
                <div id="boNotifList" class="max-h-80 overflow-y-auto divide-y divide-surface-container">
                  <div class="flex justify-center py-8">
                    <span class="material-symbols-outlined text-2xl text-slate-300 animate-spin" style="animation-duration:1.4s">progress_activity</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bookmark shortcut -->
            <div class="relative" id="bkNavWrap">
              <button id="bkNavBtn"
                      class="relative p-2 text-on-surface-variant hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all active:scale-95"
                      title="My Bookmarks">
                <span class="material-symbols-outlined text-xl">bookmark</span>
                <span id="bkNavBadge"
                      class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] px-1 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                </span>
              </button>

              <!-- Dropdown -->
              <div id="bkNavDropdown"
                   class="hidden fixed w-80 bg-white rounded-2xl shadow-2xl shadow-blue-900/10 border border-surface-container overflow-hidden"
                   style="z-index:9999">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-blue-50 to-sky-50 border-b border-blue-100">
                  <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-sm">
                      <span class="material-symbols-outlined text-white text-[15px]">bookmark</span>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Saved Articles</p>
                  </div>
                  <a href="/integration/magazine/bookmarks"
                     class="text-[11px] text-primary font-bold hover:underline flex items-center gap-0.5">
                    View all <span class="material-symbols-outlined text-[13px]">chevron_right</span>
                  </a>
                </div>
                <!-- List -->
                <div id="bkNavList" class="max-h-72 overflow-y-auto divide-y divide-surface-container">
                  <div class="flex items-center justify-center py-8">
                    <span class="material-symbols-outlined text-2xl text-slate-300 animate-spin" style="animation-duration:1.4s">progress_activity</span>
                  </div>
                </div>
                <!-- Footer -->
                <div class="px-4 py-3 border-t border-surface-container bg-surface-container-low">
                  <a href="/integration/magazine/bookmarks"
                     class="flex items-center justify-center gap-2 w-full py-2 rounded-xl bg-primary text-white text-xs font-bold hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-sm">bookmarks</span> Manage All Bookmarks
                  </a>
                </div>
              </div>
            </div>

            <!-- User pill -->
            <a href="/integration/profile" class="flex items-center gap-3 pl-4 border-l border-outline-variant/30 hover:opacity-80 transition-opacity">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($userName) ?></p>
                    <p class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($role) ?></p>
                </div>
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-on-primary font-bold text-sm shadow-sm overflow-hidden">
                    <?php 
                    $profilePic = $currentUser['profile_pic'] ?? $_SESSION['user']['profile_pic'] ?? null;
                    if (!empty($profilePic)): 
                    ?>
                          <img src="<?= htmlspecialchars($profilePic) ?><?= strpos($profilePic, '?') === false ? '?' : '&' ?>t=<?= time() ?>" alt="Profile" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <?php endif; ?>
                    <span class="<?= !empty($profilePic) ? 'hidden' : '' ?>"><?= $userInitial ?></span>
                </div>
            </a>
        </div>
    </header>

    <!-- Flash toasts -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
    <div id="flash-success" class="toast-notification mx-8 mt-6 flex items-center gap-3 bg-tertiary-fixed text-on-tertiary-fixed px-5 py-3.5 rounded-xl shadow-lg text-sm font-medium">
        <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">check_circle</span>
        <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto p-1 hover:opacity-70">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
    <div id="flash-error" class="toast-notification mx-8 mt-6 flex items-center gap-3 bg-error-container text-on-error-container px-5 py-3.5 rounded-xl shadow-lg text-sm font-medium">
        <span class="material-symbols-outlined text-lg">error</span>
        <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto p-1 hover:opacity-70">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- Page content -->
    <div class="flex-1 p-8 space-y-8">
        <?php
        // ── Stock views embarquées (Admin dual-mode) ──────────────────────────
        if (isset($stockViewPath) && file_exists($stockViewPath)) {
            include $stockViewPath;
        } elseif (isset($currentView)) {
            /* Front/ views (patient) */
            if (str_starts_with($currentView, '../Front/')) {
                $frontPath = __DIR__ . '/' . $currentView . '.php';
                if (file_exists($frontPath)) {
                    include $frontPath;
                } else {
                    echo '<p class="text-red-500">Front view not found: ' . htmlspecialchars($currentView) . '</p>';
                }
            } else {
                /* Back-office views */
                $magPath = __DIR__ . '/' . $currentView . '.php';
                if (file_exists($magPath)) {
                    include $magPath;
                } else {
                    echo '<p class="text-red-500">View not found: ' . htmlspecialchars($currentView) . '</p>';
                }
            }
        } else {
            /* Dashboard */
            if (!isset($data)) $data = [];
            include __DIR__ . '/dashboard_kpi.php';
        }
        ?>
    </div>

</main>

<!-- ═══════════════════════════════════════════ -->
<!-- Delete Confirmation Modal                    -->
<!-- ═══════════════════════════════════════════ -->
<div id="deleteModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-error-container rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-error text-2xl">warning</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface font-headline">Confirm Delete</h3>
        </div>
        <p class="text-on-surface-variant mb-6 text-sm" id="deleteModalText">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">Cancel</button>
            <a id="deleteConfirmBtn" href="#"
               class="px-5 py-2.5 text-sm font-semibold text-on-error bg-error rounded-lg hover:opacity-90 transition-opacity">Delete</a>
        </div>
    </div>
</div>

<script src="/integration/assets/js_magazine/backOffice.js"></script>

<script>
/* ── Notification bell ── */
(function () {
    const btn      = document.getElementById('boNotifBtn');
    const dropdown = document.getElementById('boNotifDropdown');
    const badge    = document.getElementById('boNotifBadge');
    const list     = document.getElementById('boNotifList');
    const markAll  = document.getElementById('boNotifMarkAll');

    if (!btn || !dropdown) return;

    const colorMap = {
        primary:   'bg-primary/10 text-primary',
        secondary: 'bg-secondary/10 text-secondary',
        tertiary:  'bg-tertiary-container text-on-tertiary-fixed',
        error:     'bg-error-container text-error',
    };

    function renderItem(n) {
        const iconColor = colorMap[n.color] || colorMap.primary;
        const unreadDot = !n.is_read
            ? '<span class="w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>'
            : '<span class="w-2 h-2 flex-shrink-0"></span>';
        return `
        <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-surface-container-low transition-colors cursor-default ${!n.is_read ? 'bg-primary-fixed/10' : ''}">
            <div class="w-9 h-9 rounded-xl ${iconColor} flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-[18px]">${n.icon}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-surface truncate">${n.title}</p>
                <p class="text-xs text-on-surface-variant line-clamp-2">${n.message}</p>
                <p class="text-[10px] text-on-surface-variant/60 mt-1">${n.time_ago}</p>
            </div>
            ${unreadDot}
        </div>`;
    }

    function loadNotifications() {
        fetch('/integration/api/notifications')
            .then(r => r.ok ? r.json() : Promise.reject(r.status))
            .then(data => {
                const count = data.unread_count || 0;
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (!data.notifications || data.notifications.length === 0) {
                    list.innerHTML = '<div class="flex flex-col items-center justify-center py-10 text-on-surface-variant"><span class="material-symbols-outlined text-3xl mb-2">notifications_off</span><p class="text-sm">Aucune notification</p></div>';
                    return;
                }
                list.innerHTML = data.notifications.map(renderItem).join('');
            })
            .catch(() => {
                list.innerHTML = '<div class="flex items-center justify-center py-8 text-on-surface-variant text-sm">Erreur de chargement</div>';
            });
    }

    // Toggle dropdown — position it relative to button using fixed coords
    function positionDropdown() {
        const rect = btn.getBoundingClientRect();
        dropdown.style.top  = (rect.bottom + 8) + 'px';
        dropdown.style.right = (window.innerWidth - rect.right) + 'px';
        dropdown.style.left  = 'auto';
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isHidden = dropdown.classList.toggle('hidden');
        if (!isHidden) { positionDropdown(); loadNotifications(); }
    });

    // Mark all read
    if (markAll) {
        markAll.addEventListener('click', function () {
            fetch('/integration/api/notifications/read-all', { method: 'POST' })
                .then(r => r.json())
                .then(() => {
                    badge.classList.add('hidden');
                    loadNotifications();
                });
        });
    }

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== btn) {
            dropdown.classList.add('hidden');
        }
    });

    // Initial badge load
    fetch('/integration/api/notifications')
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.remove('hidden');
            }
        })
        .catch(() => {});
})();

/* ── Bookmark nav dropdown ── */
(function () {
    const btn      = document.getElementById('bkNavBtn');
    const dropdown = document.getElementById('bkNavDropdown');
    const badge    = document.getElementById('bkNavBadge');
    const list     = document.getElementById('bkNavList');

    if (!btn || !dropdown) return;

    function renderBookmark(b) {
        const img = b.image_url
            ? `<img src="${b.image_url}" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">`
            : `<div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-primary text-base">article</span></div>`;
        return `
        <a href="/integration/magazine/article?id=${b.id}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors">
            ${img}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-surface truncate">${b.titre}</p>
                <p class="text-[11px] text-on-surface-variant">${b.categorie || ''}</p>
            </div>
        </a>`;
    }

    function loadBookmarks() {
        fetch('/integration/magazine/bookmarks/data')
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                const items = data.bookmarks || [];
                if (badge) {
                    if (items.length > 0) {
                        badge.textContent = items.length > 99 ? '99+' : items.length;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                if (items.length === 0) {
                    list.innerHTML = '<div class="flex flex-col items-center justify-center py-10 text-on-surface-variant"><span class="material-symbols-outlined text-3xl mb-2">bookmarks</span><p class="text-sm">Aucun article sauvegardé</p></div>';
                    return;
                }
                list.innerHTML = items.slice(0, 6).map(renderBookmark).join('');
            })
            .catch(() => {
                list.innerHTML = '<div class="flex items-center justify-center py-8 text-on-surface-variant text-sm">Erreur de chargement</div>';
            });
    }

    // Position bookmark dropdown with fixed coords
    function positionBkDropdown() {
        const rect = btn.getBoundingClientRect();
        dropdown.style.top  = (rect.bottom + 8) + 'px';
        dropdown.style.right = (window.innerWidth - rect.right) + 'px';
        dropdown.style.left  = 'auto';
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isHidden = dropdown.classList.toggle('hidden');
        if (!isHidden) { positionBkDropdown(); loadBookmarks(); }
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== btn) {
            dropdown.classList.add('hidden');
        }
    });

    // Initial badge count
    fetch('/integration/magazine/bookmarks/data')
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && badge && data.bookmarks && data.bookmarks.length > 0) {
                badge.textContent = data.bookmarks.length > 99 ? '99+' : data.bookmarks.length;
                badge.classList.remove('hidden');
            }
        })
        .catch(() => {});
})();
</script>
</body>
</html>
