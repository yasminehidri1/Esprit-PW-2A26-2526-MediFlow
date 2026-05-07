<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MediFlow Mag – Editorial Excellence</title>
<meta name="description" content="MediFlow Magazine — curated healthcare knowledge base with verified medical insights and health news for patients."/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Inter:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "outline": "#727783",
          "on-primary-fixed": "#001b3d",
          "on-tertiary-fixed-variant": "#005049",
          "tertiary": "#005851",
          "on-error-container": "#93000a",
          "error": "#ba1a1a",
          "surface-container-highest": "#e0e3e5",
          "primary-fixed": "#d6e3ff",
          "inverse-surface": "#2d3133",
          "inverse-on-surface": "#eff1f3",
          "on-secondary-fixed-variant": "#32476a",
          "primary": "#004d99",
          "secondary-fixed": "#d6e3ff",
          "secondary-fixed-dim": "#b2c7f1",
          "inverse-primary": "#a9c7ff",
          "on-surface-variant": "#424752",
          "surface-variant": "#e0e3e5",
          "primary-container": "#1565c0",
          "surface-bright": "#f7f9fb",
          "primary-fixed-dim": "#a9c7ff",
          "secondary": "#4a5f83",
          "tertiary-container": "#00736a",
          "surface-tint": "#005db7",
          "on-primary-fixed-variant": "#00468c",
          "surface": "#f7f9fb",
          "on-surface": "#191c1e",
          "on-primary": "#ffffff",
          "on-background": "#191c1e",
          "surface-container-lowest": "#ffffff",
          "on-secondary-container": "#475c80",
          "on-secondary": "#ffffff",
          "on-secondary-fixed": "#021b3c",
          "on-error": "#ffffff",
          "surface-dim": "#d8dadc",
          "secondary-container": "#c0d5ff",
          "surface-container": "#eceef0",
          "on-tertiary-fixed": "#00201d",
          "on-tertiary-container": "#87f8ea",
          "on-tertiary": "#ffffff",
          "surface-container-low": "#f2f4f6",
          "tertiary-fixed-dim": "#66d9cc",
          "tertiary-fixed": "#84f5e8",
          "outline-variant": "#c2c6d4",
          "error-container": "#ffdad6",
          "surface-container-high": "#e6e8ea",
          "on-primary-container": "#dae5ff",
          "background": "#f7f9fb"
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
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  .text-editorial-gradient {
    background: linear-gradient(to right, #004d99, #1565c0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  body { font-family: 'Inter', sans-serif; }
  h1,h2,h3,h4,h5,h6,.font-headline { font-family: 'Manrope', sans-serif; }
  
  /* Custom Animations */
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes slideInRight {
    from { 
      opacity: 0;
      transform: translateX(100px);
    }
    to { 
      opacity: 1;
      transform: translateX(0);
    }
  }
  @keyframes slideOutRight {
    from { 
      opacity: 1;
      transform: translateX(0);
    }
    to { 
      opacity: 0;
      transform: translateX(100px);
    }
  }
  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  .animate-in {
    animation: fadeIn 0.2s ease-in-out forwards;
  }
  .animate-in.fade-in {
    animation: fadeIn 0.2s ease-in-out forwards;
  }
  .animate-in.slide-in-from-right-4 {
    animation: slideInRight 0.3s ease-out forwards;
  }
  .animate-out {
    animation: fadeIn 0.2s ease-in-out reverse;
  }
  .animate-out.fade-out {
    animation: fadeIn 0.2s ease-in-out reverse;
  }
  .animate-out.slide-out-to-right-4 {
    animation: slideOutRight 0.3s ease-in forwards;
  }
  .scale-in-95 {
    animation: scaleIn 0.2s ease-out forwards;
  }
</style>
<link rel="stylesheet" href="/integration/assets/css_magazine/style.css"/>
</head>
<body class="bg-surface font-body text-on-surface">

<?php
$currentCat    = $_GET['cat']    ?? '';
$currentAction = $_GET['action'] ?? 'home';
?>

<!-- ============================================================ -->
<!-- TopNavBar                                                      -->
<!-- ============================================================ -->
<nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,77,153,0.05)] h-20">
  <div class="flex justify-between items-center w-full px-8 h-full max-w-screen-2xl mx-auto">

    <!-- Brand -->
    <a href="/integration/magazine" class="text-2xl font-bold tracking-tighter text-blue-900 font-headline">
      MediFlow Editorial
    </a>

    <!-- Navigation Links -->
    <div class="hidden md:flex items-center gap-8 font-label text-sm">
      <a href="/integration/magazine"
         class="<?= $currentAction === 'home' ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-slate-500 hover:text-blue-600 transition-colors duration-300' ?>">
        Latest
      </a>
      <a href="/integration/magazine/category?cat=Research"
         class="<?= $currentCat === 'Research' ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-slate-500 hover:text-blue-600 transition-colors duration-300' ?>">
        Research
      </a>
      <a href="/integration/magazine/category?cat=Mental Wellness"
         class="<?= $currentCat === 'Mental Wellness' ? 'text-blue-700 font-bold border-b-2 border-blue-700 pb-1' : 'text-slate-500 hover:text-blue-600 transition-colors duration-300' ?>">
        Wellness
      </a>
      <!-- Categories Dropdown -->
      <div class="relative group">
        <button class="text-slate-500 hover:text-blue-600 transition-colors duration-300 flex items-center gap-1">
          Categories
          <span class="material-symbols-outlined text-base leading-none">expand_more</span>
        </button>
        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 bg-white rounded-xl shadow-2xl shadow-blue-900/10 border border-surface-container py-2 min-w-[200px]
                    opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
          <?php
          $cats = ['General Health','Mental Wellness','Diet & Nutrition','Active Living','Research','Journals'];
          foreach ($cats as $cat):
          ?>
          <a href="/integration/magazine/category?cat=<?= urlencode($cat) ?>"
             class="block px-5 py-2.5 text-sm text-slate-600 hover:bg-surface-container-low hover:text-primary transition-colors">
            <?= htmlspecialchars($cat) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Trailing Actions -->
    <div class="flex items-center gap-6">
      <!-- Search -->
      <div class="relative hidden lg:block">
        <form method="GET" action="/integration/magazine" class="flex items-center">
          <input type="hidden" name="action" value="search" id="navSearchAction"/>
          <input id="navSearchInput"
                 class="bg-surface-container-low border-none rounded-lg py-2 pl-4 pr-10 text-sm focus:ring-2 focus:ring-tertiary/30 w-64 transition-all"
                 placeholder="Search articles..." type="text" name="q"
                 value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                 autocomplete="off"/>
          <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
            <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
          </button>
        </form>
        <!-- Live results dropdown -->
        <div id="navSearchResults"
             class="absolute top-full left-0 mt-2 w-full bg-white rounded-xl shadow-2xl shadow-blue-900/10 border border-surface-container hidden z-50 overflow-hidden">
        </div>
      </div>

      <div class="flex items-center gap-4">
        <!-- Mobile search toggle -->
        <button id="searchToggle" class="text-slate-500 hover:bg-slate-50/50 p-2 rounded-lg transition-all active:scale-[0.95] lg:hidden">
          <span class="material-symbols-outlined">search</span>
        </button>
        <a href="/integration/magazine/admin" class="text-slate-500 hover:bg-slate-50/50 p-2 rounded-lg transition-all active:scale-[0.95]" title="Admin Panel">
          <span class="material-symbols-outlined">admin_panel_settings</span>
        </a>
        <!-- Saved Articles Toggle -->
        <button id="savedArticlesToggle" class="relative text-slate-500 hover:bg-slate-50/50 p-2 rounded-lg transition-all active:scale-[0.95]" title="Saved Articles">
          <span class="material-symbols-outlined">bookmarks</span>
          <span id="savedCountBadge" class="absolute top-1 right-1 w-4 h-4 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center hidden">0</span>
        </button>
        <!-- Avatar placeholder -->
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm border-2 border-primary/10">
          U
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Saved Articles Drawer -->
<div id="savedArticlesDrawer" class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-2xl z-[70] transform translate-x-full transition-transform duration-500 ease-in-out border-l border-surface-container">
    <div class="flex flex-col h-full">
        <div class="p-6 border-b border-surface-container flex justify-between items-center bg-surface-container-low">
            <h3 class="text-xl font-headline font-bold text-blue-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">bookmarks</span>
                Reading List
            </h3>
            <button id="closeSavedDrawer" class="p-2 hover:bg-surface-container rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="savedArticlesList" class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Saved items will appear here -->
            <div class="text-center py-12 opacity-50">
                <span class="material-symbols-outlined text-6xl mb-4">bookmark_border</span>
                <p class="text-sm font-medium">Your reading list is empty.</p>
            </div>
        </div>
        <div class="p-6 border-t border-surface-container bg-surface-container-low text-center">
            <p class="text-xs text-slate-400">Articles are saved to your browser local storage.</p>
        </div>
    </div>
</div>
<div id="drawerOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[65] hidden opacity-0 transition-opacity duration-500"></div>

<!-- Social Share Tooltip (Floating) -->
<div id="shareTooltip" class="fixed z-[100] hidden pointer-events-auto">
    <div class="flex items-center gap-1 bg-blue-900 text-white rounded-full px-2 py-1.5 shadow-2xl animate-tooltipIn">
        <button id="shareTwitter" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Share on Twitter">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </button>
        <button id="shareLinkedIn" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Share on LinkedIn">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
        </button>
        <div class="w-px h-4 bg-white/20 mx-1"></div>
        <button id="copySelection" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Copy selection">
            <span class="material-symbols-outlined text-[18px]">content_copy</span>
        </button>
    </div>
    <div class="w-3 h-3 bg-blue-900 rotate-45 mx-auto -mt-1.5 shadow-2xl"></div>
</div>

<style>
@keyframes tooltipIn {
    from { opacity: 0; transform: translateY(10px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slideIn { animation: slideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-tooltipIn { animation: tooltipIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

<!-- Mobile Search Overlay -->
<div id="searchOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[60] hidden">
  <div class="max-w-2xl mx-auto mt-28 px-4">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
      <div class="flex items-center px-6 py-4 border-b border-surface-container">
        <span class="material-symbols-outlined text-slate-400 mr-3">search</span>
        <input id="searchInput" type="text" placeholder="Search articles..."
               class="flex-1 text-lg border-none focus:ring-0 bg-transparent text-on-surface" autofocus/>
        <button onclick="toggleSearch()" class="p-1 hover:bg-slate-50 rounded-full">
          <span class="material-symbols-outlined text-slate-400">close</span>
        </button>
      </div>
      <div id="searchResults" class="max-h-96 overflow-y-auto p-4 hidden"></div>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- Main Content                                                   -->
<!-- ============================================================ -->
<main class="max-w-screen-2xl mx-auto px-8 pt-32 pb-20">

  <!-- Flash Messages -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
  <div id="flash-success" class="toast-notification flex items-center gap-3 bg-tertiary-fixed text-on-tertiary-fixed px-6 py-4 rounded-xl shadow-lg mb-8 animate-slideIn">
    <span class="material-symbols-outlined">check_circle</span>
    <span class="font-medium"><?= $_SESSION['flash_success'] ?></span>
    <button onclick="this.parentElement.remove()" class="ml-auto">
      <span class="material-symbols-outlined text-sm">close</span>
    </button>
  </div>
  <?php unset($_SESSION['flash_success']); endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
  <div id="flash-error" class="toast-notification flex items-center gap-3 bg-error-container text-on-error-container px-6 py-4 rounded-xl shadow-lg mb-8 animate-slideIn">
    <span class="material-symbols-outlined">error</span>
    <span class="font-medium"><?= $_SESSION['flash_error'] ?></span>
    <button onclick="this.parentElement.remove()" class="ml-auto">
      <span class="material-symbols-outlined text-sm">close</span>
    </button>
  </div>
  <?php unset($_SESSION['flash_error']); endif; ?>

  <!-- Dynamic View Content -->
  <?php
  $viewFile = $currentView ?? 'home';
  $viewPath = __DIR__ . '/' . $viewFile . '.php';
  if (file_exists($viewPath)) include $viewPath;
  else include __DIR__ . '/home.php';
  ?>
</main>

<!-- ============================================================ -->
<!-- Footer                                                         -->
<!-- ============================================================ -->
<footer class="bg-slate-100 w-full py-12">
  <div class="w-full flex flex-col md:flex-row justify-between items-center px-12 max-w-screen-2xl mx-auto gap-6">
    <div>
      <div class="font-headline font-bold text-blue-900 text-xl mb-2">MediFlow Mag</div>
      <p class="font-body text-xs tracking-wide text-slate-500 uppercase">
        &copy; <?= date('Y') ?> MediFlow Clinical Sanctuary. Editorial Excellence.
      </p>
    </div>
    <div class="flex flex-wrap justify-center gap-8 font-body text-xs tracking-wide uppercase">
      <span class="text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">Ethics Policy</span>
      <span class="text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">Peer Review Process</span>
      <a href="/integration/magazine/admin" class="text-slate-500 hover:text-blue-600 transition-colors">Admin Portal</a>
      <span class="text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">Contact</span>
      <span class="text-slate-500 hover:text-blue-600 transition-colors cursor-pointer">Terms</span>
    </div>
  </div>
</footer>

<!-- ============================================================ -->
<!-- Mobile Bottom Nav                                              -->
<!-- ============================================================ -->
<div class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-surface-container h-16 flex items-center justify-around z-50">
  <a href="/integration/magazine" class="flex flex-col items-center gap-1 <?= $currentAction === 'home' ? 'text-primary' : 'text-slate-400' ?>">
    <span class="material-symbols-outlined" <?= $currentAction === 'home' ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' ?>>home</span>
    <span class="text-[10px] font-bold">Home</span>
  </a>
  <a href="/integration/magazine/category?cat=Research" class="flex flex-col items-center gap-1 <?= $currentCat === 'Research' ? 'text-primary' : 'text-slate-400' ?>">
    <span class="material-symbols-outlined">explore</span>
    <span class="text-[10px] font-medium">Discover</span>
  </a>
  <a href="/integration/magazine/category?cat=Journals" class="flex flex-col items-center gap-1 <?= $currentCat === 'Journals' ? 'text-primary' : 'text-slate-400' ?>">
    <span class="material-symbols-outlined">menu_book</span>
    <span class="text-[10px] font-medium">Library</span>
  </a>
  <button onclick="toggleSearch()" class="flex flex-col items-center gap-1 text-slate-400">
    <span class="material-symbols-outlined">search</span>
    <span class="text-[10px] font-medium">Search</span>
  </button>
  <a href="/integration/magazine/admin" class="flex flex-col items-center gap-1 text-slate-400">
    <span class="material-symbols-outlined">settings</span>
    <span class="text-[10px] font-medium">Admin</span>
  </a>
</div>

<script src="/integration/assets/js_magazine/frontOffice.js?v=<?= time() ?>"></script>

</body>
</html>
