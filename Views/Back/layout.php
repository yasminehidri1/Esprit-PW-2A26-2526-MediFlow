<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>MediFlow Mag | CMS Portal</title>
    <meta name="description" content="MediFlow Magazine admin panel — manage health articles, moderate comments, and track engagement analytics."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
          },
        },
      }
    </script>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        h1, h2, h3, h4 { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-surface text-on-surface">

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 flex flex-col border-r border-slate-100 z-40">
    <div class="px-6 py-8">
        <div class="text-lg font-bold text-blue-900">Admin Panel</div>
        <div class="text-xs text-slate-500 font-label">MediFlow CMS</div>
    </div>
    <nav class="flex-1 px-2 space-y-1">
        <?php
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $navItems = [
            ['href' => '/integration/magazine/admin',          'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['href' => '/integration/magazine/admin/articles', 'icon' => 'article',   'label' => 'Content Library'],
        ];
        foreach ($navItems as $item):
            $isActive = rtrim($currentPath, '/') === rtrim($item['href'], '/');
        ?>
        <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out <?= $isActive 
            ? 'text-blue-700 font-bold border-r-4 border-teal-500 bg-blue-50' 
            : 'text-slate-500 hover:bg-blue-50' ?>" 
           href="<?= $item['href'] ?>">
            <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
            <span class="font-label"><?= $item['label'] ?></span>
        </a>
        <?php endforeach; ?>

        <!-- Comments Tab — always visible -->
        <?php
        $isCommentsActive = str_contains($currentPath, '/magazine/admin/comments');
        $commentsHref = $isCommentsActive && !empty($_GET['post_id'])
            ? '/integration/magazine/admin/comments?post_id=' . htmlspecialchars($_GET['post_id'] ?? '')
            : '/integration/magazine/admin/comments';
        ?>
        <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out <?= $isCommentsActive
            ? 'text-blue-700 font-bold border-r-4 border-teal-500 bg-blue-50'
            : 'text-slate-500 hover:bg-blue-50' ?>"
           href="<?= $commentsHref ?>">
            <span class="material-symbols-outlined">forum</span>
            <span class="font-label">Comments</span>
        </a>
    </nav>
    <div class="p-6">
        <a href="/integration/magazine/admin/article-form" 
           class="w-full bg-gradient-to-r from-primary to-primary-container text-on-primary py-3 px-4 rounded-lg font-semibold flex items-center justify-center gap-2 shadow-sm transition-all hover:scale-[0.98]">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Create Post</span>
        </a>
    </div>
</aside>

<!-- Main Content Canvas -->
<main class="ml-64 min-h-screen p-10 space-y-10">
    <!-- Header & Action Row -->
    <header class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-on-surface">Magazine Management</h1>
            <p class="text-on-surface-variant mt-2 text-lg">Control center for articles, research papers, and community interactions.</p>
        </div>
        <div class="flex gap-4">
            <form method="GET" action="/integration/magazine/admin/articles" class="flex items-center bg-surface-container-low px-4 py-2 rounded-lg text-on-surface-variant">
                <span class="material-symbols-outlined mr-2">search</span>
                <input id="admin-search" class="bg-transparent border-none focus:ring-0 text-sm w-48" placeholder="Search posts..." type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"/>
            </form>
            <div class="flex items-center gap-3 pl-4 border-l border-outline-variant/30">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-on-surface truncate"><?= htmlspecialchars(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? 'Admin')) ?></p>
                    <p class="text-[10px] text-on-surface-variant truncate"><?= htmlspecialchars($_SESSION['user']['role'] ?? 'Magazine Editor') ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold shadow-sm">
                    <?= strtoupper(substr($_SESSION['user']['prenom'] ?? 'A', 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
    <div id="flash-success" class="toast-notification flex items-center gap-3 bg-tertiary-fixed text-on-tertiary-fixed px-6 py-4 rounded-xl shadow-lg animate-slideIn">
        <span class="material-symbols-outlined">check_circle</span>
        <span class="font-medium"><?= $_SESSION['flash_success'] ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
    <div id="flash-error" class="toast-notification flex items-center gap-3 bg-error-container text-on-error-container px-6 py-4 rounded-xl shadow-lg animate-slideIn">
        <span class="material-symbols-outlined">error</span>
        <span class="font-medium"><?= $_SESSION['flash_error'] ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- Dynamic Content Area -->
    <?php
    $viewFile = $currentView ?? 'dashboard';
    $viewPath = __DIR__ . '/' . $viewFile . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        include __DIR__ . '/dashboard.php';
    }
    ?>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-error-container rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-error text-2xl">warning</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface">Confirm Delete</h3>
        </div>
        <p class="text-on-surface-variant mb-6" id="deleteModalText">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" class="px-5 py-2.5 text-sm font-semibold text-on-surface-variant bg-surface-container rounded-lg hover:bg-surface-container-high transition-colors">Cancel</button>
            <a id="deleteConfirmBtn" href="#" class="px-5 py-2.5 text-sm font-semibold text-on-error bg-error rounded-lg hover:opacity-90 transition-opacity">Delete</a>
        </div>
    </div>
</div>

<script src="/integration/assets/js_magazine/backOffice.js"></script>
</body>
</html>
