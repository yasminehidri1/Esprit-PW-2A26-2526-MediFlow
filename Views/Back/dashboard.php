<?php
/**
 * Admin Dashboard View
 * 
 * Displays the admin dashboard with KPI cards, charts, and user management data
 * 
 * @package MediFlow\Views\Back
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MediFlow Admin Dashboard</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/integration/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "background": "#f7f9fb",
                        "surface-container-high": "#e6e8ea",
                        "on-error": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "surface-container-lowest": "#ffffff",
                        "error": "#ba1a1a",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#d6e3ff",
                        "on-surface-variant": "#424752",
                        "inverse-surface": "#2d3133",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed-variant": "#00468c",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#dae5ff",
                        "on-secondary-container": "#475c80",
                        "outline": "#727783",
                        "primary": "#004d99",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed": "#021b3c",
                        "surface-variant": "#e0e3e5",
                        "primary-container": "#1565c0",
                        "on-primary-fixed": "#001b3d",
                        "secondary": "#4a5f83",
                        "on-tertiary-fixed-variant": "#005049",
                        "error-container": "#ffdad6",
                        "surface-tint": "#005db7",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#00736a",
                        "primary-fixed-dim": "#a9c7ff",
                        "outline-variant": "#c2c6d4",
                        "inverse-on-surface": "#eff1f3",
                        "on-tertiary-container": "#87f8ea",
                        "surface": "#f7f9fb",
                        "on-primary": "#ffffff",
                        "on-surface": "#191c1e",
                        "tertiary": "#005851",
                        "inverse-primary": "#a9c7ff",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed": "#84f5e8",
                        "tertiary-fixed-dim": "#66d9cc",
                        "secondary-container": "#c0d5ff",
                        "on-background": "#191c1e",
                        "secondary-fixed": "#d6e3ff",
                        "on-secondary-fixed-variant": "#32476a",
                        "surface-container-low": "#f2f4f6",
                        "on-tertiary-fixed": "#00201d",
                        "secondary-fixed-dim": "#b2c7f1"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
</head>
<body class="bg-surface text-on-surface overflow-hidden">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
    <div class="px-8">
        <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Clinical Sanctuary</p>
    </div>
    <nav class="flex-1 flex flex-col space-y-2 px-4">
        <a class="flex items-center space-x-3 text-slate-700 dark:text-slate-300 hover:text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 bg-opacity-40 pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1 shadow-sm hover:shadow-md" href="/integration/dashboard">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role']): ?>
            <?php $role = $_SESSION['user']['role']; ?>

            <?php if ($role === 'Admin'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/admin">
                    <span class="material-symbols-outlined">people</span>
                    <span class="font-medium">User Management</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine/admin">
                    <span class="material-symbols-outlined">newspaper</span>
                    <span class="font-medium">Magazine Admin</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine">
                    <span class="material-symbols-outlined">open_in_new</span>
                    <span class="font-medium">Voir le magazine</span>
                </a>
            <?php elseif ($role === 'Medecin'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/medical">
                    <span class="material-symbols-outlined">description</span>
                    <span class="font-medium">Dossier Medical</span>
                </a>
            <?php elseif ($role === 'Rendez-vous'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/appointments">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="font-medium">Rendez-vous</span>
                </a>
            <?php elseif ($role === 'Stock medicament'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/stock">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span class="font-medium">Stock Medicament</span>
                </a>

            <?php elseif ($role === 'Equipment'): ?>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/equipements">
                    <span class="material-symbols-outlined">medical_services</span>
                    <span class="font-medium">Gestion des équipements</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/historique-location">
                    <span class="material-symbols-outlined">history</span>
                    <span class="font-medium">Historique location</span>
                </a>

            <?php elseif ($role === 'Patient'): ?>
                <!-- Patient: browse catalogue + view own reservations + read magazine -->
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/catalogue">
                    <span class="material-symbols-outlined">medical_services</span>
                    <span class="font-medium">Location d'équipements</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/mes-reservations">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="font-medium">Mes réservations</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine">
                    <span class="material-symbols-outlined">newspaper</span>
                    <span class="font-medium">Magazine médical</span>
                </a>

            <?php elseif ($role === 'Magazine'): ?>
                <!-- Magazine editor -->
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine/admin">
                    <span class="material-symbols-outlined">newspaper</span>
                    <span class="font-medium">Magazine Admin</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine/admin/articles">
                    <span class="material-symbols-outlined">article</span>
                    <span class="font-medium">Articles</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine/admin/comments">
                    <span class="material-symbols-outlined">forum</span>
                    <span class="font-medium">Commentaires</span>
                </a>
                <a class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 transition-all duration-300 transform hover:translate-x-1 rounded-xl" href="/integration/magazine">
                    <span class="material-symbols-outlined">open_in_new</span>
                    <span class="font-medium">Voir le magazine</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="px-4 border-t border-outline pt-6 flex flex-col space-y-3">
        <a href="/integration/profile" class="flex items-center space-x-3 text-slate-500 dark:text-slate-400 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
            <span class="material-symbols-outlined">account_circle</span>
            <span class="font-medium">My Profile</span>
        </a>
        <a href="/integration/logout" class="logout-btn">
            <span class="material-symbols-outlined logout-icon">logout</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim">
    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
        <div class="flex items-center space-x-8">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline/20 rounded-full text-sm w-64 focus:ring-2 focus:ring-primary/30 outline-none transition-all duration-300" placeholder="Search users..." type="text"/>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a class="text-primary font-bold border-b-2 border-primary pb-1 transition-all duration-300" href="/integration/dashboard">Dashboard</a>
                <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/integration/dashboard?tab=users">Utilisateurs</a>
                <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/integration/dashboard?tab=reports">Rapports</a>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin'): ?>
                    <a class="text-slate-500 hover:text-error transition-colors duration-300 font-bold" href="/integration/admin">User Management</a>
                    <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/integration/magazine/admin">Magazine</a>
                <?php elseif (isset($_SESSION['user'])): ?>
                    <a class="text-slate-500 hover:text-primary transition-colors duration-300 font-medium" href="/integration/magazine">Magazine</a>
                <?php endif; ?>
            </nav>
        </div>
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <button class="text-slate-500 hover:text-primary relative transition-colors duration-300 hover:scale-110">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-error rounded-full animate-pulse"></span>
                </button>
                <button class="text-slate-500 hover:text-primary transition-colors duration-300 hover:scale-110">
                    <span class="material-symbols-outlined">help_outline</span>
                </button>
            </div>
            <div class="flex items-center space-x-3 pl-6 border-l border-outline/20">
                <a href="/integration/profile" class="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-200">
                    <div class="text-right">
                        <p class="text-sm font-bold text-on-surface">
                            <?php echo isset($data['currentUser']) ? htmlspecialchars($data['currentUser']['prenom'] . ' ' . $data['currentUser']['nom']) : 'Admin'; ?>
                        </p>
                        <p class="text-xs text-slate-500">
                            <?php echo isset($data['currentUser']) ? htmlspecialchars($data['currentUser']['role_name']) : 'Administrator'; ?>
                        </p>
                    </div>
                    <img alt="Admin Avatar" class="w-10 h-10 rounded-full border-2 border-primary-fixed hover:border-primary transition-all duration-300 cursor-pointer hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYPJ5lof7TyPYcDQMTIqlqy9_EnbCRQNlituOGkD8EagHkVvQd1KZpwyL1vjOT1yTSO3w3sP8VpxAMSa34d9WtQjlvKYF6XEyyWxWXitkQtvDO2cCIWJTNxIeGgRopkbP9SslNkybURTmSZhpYO9QGJKH7o4JQUM4-k_SdF_j17mD59kBsVc8Ep7h55Ju4wJOjsgODP4eGcB2hSgEuiVkPd7l2tTeKD1USMvHj9u_MgR-w_ty6lekC3w1h1LEvgZzPY-hs6uxE440"/>
                </a>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <div class="pt-24 pb-12 px-10 space-y-10">
        <?php include __DIR__ . '/dashboard_kpi.php'; ?>
    </div>
</main>
</body>
</html>
