<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MediFlow Mag | Patient Portal</title>
    <meta name="description" content="MediFlow Magazine — curated healthcare knowledge base with verified medical insights and health news for patients."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-error-container": "#93000a",
                        "secondary-fixed": "#d6e3ff",
                        "secondary": "#4a5f83",
                        "inverse-surface": "#2d3133",
                        "on-primary": "#ffffff",
                        "surface-variant": "#e0e3e5",
                        "surface-container-lowest": "#ffffff",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#87f8ea",
                        "on-primary-fixed-variant": "#00468c",
                        "error": "#ba1a1a",
                        "inverse-on-surface": "#eff1f3",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-container": "#dae5ff",
                        "tertiary-fixed": "#84f5e8",
                        "secondary-fixed-dim": "#b2c7f1",
                        "primary-container": "#1565c0",
                        "background": "#f7f9fb",
                        "surface": "#f7f9fb",
                        "outline": "#727783",
                        "inverse-primary": "#a9c7ff",
                        "error-container": "#ffdad6",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-container": "#475c80",
                        "on-tertiary-fixed-variant": "#005049",
                        "surface-container": "#eceef0",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed": "#001b3d",
                        "on-tertiary-fixed": "#00201d",
                        "tertiary": "#005851",
                        "surface-tint": "#005db7",
                        "on-secondary-fixed-variant": "#32476a",
                        "tertiary-container": "#00736a",
                        "secondary-container": "#c0d5ff",
                        "primary-fixed-dim": "#a9c7ff",
                        "on-secondary-fixed": "#021b3c",
                        "surface-container-high": "#e6e8ea",
                        "on-background": "#191c1e",
                        "on-surface": "#191c1e",
                        "outline-variant": "#c2c6d4",
                        "on-tertiary": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "tertiary-fixed-dim": "#66d9cc",
                        "primary": "#004d99",
                        "primary-fixed": "#d6e3ff",
                        "on-surface-variant": "#424752",
                        "surface-container-low": "#f2f4f6"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    fontFamily: {
                        headline: ["Manrope"],
                        body: ["Inter"],
                        label: ["Inter"]
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="text-on-surface bg-surface antialiased overflow-x-hidden">

<!-- TopNavBar -->
<nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl flex justify-between items-center px-8 py-4 shadow-[0_20px_50px_rgba(0,77,153,0.05)]">
    <?php
    $currentCat = $_GET['cat'] ?? '';
    $currentAction = $_GET['action'] ?? 'home';
    ?>
    <a href="frontOffice.php" class="text-2xl font-bold tracking-tight text-blue-900">MediFlow Mag</a>
    <div class="flex items-center gap-4">
        <button id="searchToggle" class="p-2 hover:bg-slate-50 rounded-full transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">search</span>
        </button>
        <a href="backOffice.php" class="p-2 hover:bg-slate-50 rounded-full transition-colors" title="Admin Panel">
            <span class="material-symbols-outlined text-on-surface-variant">admin_panel_settings</span>
        </a>
    </div>
</nav>

<!-- Search Overlay -->
<div id="searchOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[60] hidden">
    <div class="max-w-2xl mx-auto mt-28 px-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center px-6 py-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-slate-400 mr-3">search</span>
                <input id="searchInput" type="text" placeholder="Search articles..." 
                       class="flex-1 text-lg border-none focus:ring-0 bg-transparent text-on-surface" autofocus/>
                <button onclick="toggleSearch()" class="p-1 hover:bg-slate-50 rounded-full">
                    <span class="material-symbols-outlined text-slate-400">close</span>
                </button>
            </div>
            <div id="searchResults" class="max-h-96 overflow-y-auto p-4 hidden">
                <!-- Results populated by JS -->
            </div>
        </div>
    </div>
</div>

<div class="flex min-h-screen pt-20">
    <!-- SideNavBar -->
    <aside class="hidden lg:flex flex-col w-64 h-[calc(100vh-80px)] sticky top-20 bg-surface-container-low p-5 space-y-3 overflow-y-auto">
        <?php
        $sidebarCategories = [
            ['cat' => 'General Health', 'icon' => 'medical_services', 'label' => 'General Health'],
            ['cat' => 'Mental Wellness', 'icon' => 'psychology', 'label' => 'Mental Wellness'],
            ['cat' => 'Diet & Nutrition', 'icon' => 'nutrition', 'label' => 'Diet & Nutrition'],
            ['cat' => 'Active Living', 'icon' => 'fitness_center', 'label' => 'Active Living'],
            ['cat' => 'Research', 'icon' => 'biotech', 'label' => 'Research'],
            ['cat' => 'Journals', 'icon' => 'menu_book', 'label' => 'Journals'],
        ];
        ?>

        <!-- Magazine Toggle Button -->
        <button id="magazineToggle"
                onclick="toggleMagazineMenu()"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-primary text-on-primary font-semibold shadow-sm hover:opacity-90 transition-opacity">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">auto_stories</span>
                <span>Magazine</span>
            </div>
            <span class="material-symbols-outlined transition-transform duration-300" id="magazineChevron">expand_more</span>
        </button>

        <!-- Collapsible Category List -->
        <nav id="magazineCategories" class="flex flex-col gap-1 overflow-hidden transition-all duration-300" style="max-height: 0; opacity: 0;">
            <?php foreach ($sidebarCategories as $sc):
                $isCatActive = ($currentAction === 'category' && $currentCat === $sc['cat']);
            ?>
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors <?= $isCatActive
                ? 'bg-primary-container text-on-primary font-semibold shadow-sm'
                : 'text-on-surface-variant hover:bg-surface-container-high' ?>"
               href="frontOffice.php?action=category&cat=<?= urlencode($sc['cat']) ?>">
                <span class="material-symbols-outlined text-[20px]"><?= $sc['icon'] ?></span>
                <span class="text-sm"><?= $sc['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Main Content Area — centered with auto margins -->
    <main class="flex-1 px-6 md:px-10 py-8 max-w-5xl mx-auto">
        <!-- Flash Messages -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flex items-center gap-3 bg-tertiary-fixed text-on-tertiary-fixed px-6 py-4 rounded-xl shadow-lg mb-6 animate-slideIn">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="font-medium"><?= $_SESSION['flash_success'] ?></span>
            <button onclick="this.parentElement.remove()" class="ml-auto">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <?php unset($_SESSION['flash_success']); endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flex items-center gap-3 bg-error-container text-on-error-container px-6 py-4 rounded-xl shadow-lg mb-6 animate-slideIn">
            <span class="material-symbols-outlined">error</span>
            <span class="font-medium"><?= $_SESSION['flash_error'] ?></span>
            <button onclick="this.parentElement.remove()" class="ml-auto">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <!-- Dynamic Content -->
        <?php
        $viewFile = $currentView ?? 'home';
        $viewPath = __DIR__ . '/' . $viewFile . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            include __DIR__ . '/home.php';
        }
        ?>
    </main>
</div>

<!-- Bottom Navigation for Mobile -->
<div class="md:hidden fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-lg border-t border-slate-100 flex justify-around py-3 z-50">
    <a href="frontOffice.php" class="flex flex-col items-center gap-1 <?= $currentAction === 'home' ? 'text-blue-700' : 'text-slate-400' ?>">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-medium">Home</span>
    </a>
    <a href="frontOffice.php?action=category&cat=Research" class="flex flex-col items-center gap-1 <?= $currentCat === 'Research' ? 'text-blue-700' : 'text-slate-400' ?>">
        <span class="material-symbols-outlined">biotech</span>
        <span class="text-[10px] font-medium">Research</span>
    </a>
    <a href="frontOffice.php?action=category&cat=General Health" class="flex flex-col items-center gap-1 <?= $currentCat === 'General Health' ? 'text-blue-700' : 'text-slate-400' ?>">
        <span class="material-symbols-outlined">article</span>
        <span class="text-[10px] font-medium">News</span>
    </a>
    <button onclick="toggleSearch()" class="flex flex-col items-center gap-1 text-slate-400">
        <span class="material-symbols-outlined">search</span>
        <span class="text-[10px] font-medium">Search</span>
    </button>
</div>

<script src="assets/js/frontOffice.js"></script>
</body>
</html>
