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
        <?php if (!empty($_SESSION['user'])): ?>
        <!-- Notifications bell -->
        <div class="relative" id="notifBellWrap">
          <button id="notifBellBtn"
                  class="relative text-slate-500 hover:text-primary hover:bg-surface-container-low p-2 rounded-lg transition-all active:scale-[0.95]"
                  title="Notifications">
            <span class="material-symbols-outlined">notifications</span>
            <span id="notifBadge"
                  class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
              0
            </span>
          </button>

          <!-- Dropdown -->
          <div id="notifDropdown"
               class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl shadow-blue-900/10 border border-surface-container z-50 overflow-hidden animate-slideIn">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-surface-container">
              <p class="text-sm font-bold text-slate-800">Notifications</p>
              <button id="notifMarkAllRead" class="text-[11px] text-primary font-bold hover:underline">Mark all read</button>
            </div>
            <!-- List -->
            <div id="notifList" class="max-h-72 overflow-y-auto divide-y divide-surface-container">
              <p class="text-sm text-slate-400 text-center py-8">No notifications yet.</p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Bookmarks — always visible, dropdown only for logged-in users -->
        <div class="relative" id="bookmarkNavWrap">
          <?php if (!empty($_SESSION['user'])): ?>
          <button id="bookmarkNavBtn"
                  class="relative text-slate-500 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all active:scale-[0.95]"
                  title="My Bookmarks">
            <span class="material-symbols-outlined">bookmark</span>
            <span id="bookmarkNavBadge"
                  class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] px-1 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
              0
            </span>
          </button>

          <!-- Dropdown panel (logged-in only) -->
          <div id="bookmarkDropdown"
               class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl shadow-blue-900/10 border border-surface-container z-50 overflow-hidden animate-slideIn">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-blue-50 to-sky-50 border-b border-blue-100/60">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-sm shadow-blue-200">
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
            <div id="bookmarkDropList" class="max-h-80 overflow-y-auto divide-y divide-surface-container">
              <div class="flex items-center justify-center gap-2 py-8 text-sm text-slate-400">
                <span class="material-symbols-outlined text-xl animate-spin" style="animation-duration:1.4s">progress_activity</span>
              </div>
            </div>
            <!-- Footer -->
            <div class="px-5 py-3 border-t border-surface-container bg-surface-container-low">
              <a href="/integration/magazine/bookmarks"
                 class="flex items-center justify-center gap-2 w-full py-2 rounded-xl bg-primary text-white text-xs font-bold hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">bookmarks</span> Manage All Bookmarks
              </a>
            </div>
          </div>

          <?php else: ?>
          <!-- Not logged in — plain link, controller will redirect to login -->
          <a href="/integration/magazine/bookmarks"
             class="relative text-slate-500 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all active:scale-[0.95] flex items-center"
             title="My Bookmarks">
            <span class="material-symbols-outlined">bookmark</span>
          </a>
          <?php endif; ?>
        </div>
        <a href="/integration/magazine/admin" class="text-slate-500 hover:bg-slate-50/50 p-2 rounded-lg transition-all active:scale-[0.95]" title="Admin Panel">
          <span class="material-symbols-outlined">admin_panel_settings</span>
        </a>
        <!-- Avatar placeholder -->
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm border-2 border-primary/10">
          <?= strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1)) ?>
        </div>
      </div>
    </div>
  </div>
</nav>

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
  <a href="/integration/magazine/bookmarks"
     class="flex flex-col items-center gap-1 relative <?= ($currentView ?? '') === 'bookmarks' ? 'text-blue-600' : 'text-slate-400' ?>">
    <span class="material-symbols-outlined" <?= ($currentView ?? '') === 'bookmarks' ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' ?>>bookmark</span>
    <span class="text-[10px] font-medium">Saved</span>
    <span id="mobileBookmarkBadge" class="absolute -top-0.5 right-2 hidden min-w-[14px] h-[14px] px-0.5 bg-blue-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none"></span>
  </a>
  <a href="/integration/magazine/admin" class="flex flex-col items-center gap-1 text-slate-400">
    <span class="material-symbols-outlined">settings</span>
    <span class="text-[10px] font-medium">Admin</span>
  </a>
</div>

<script src="/integration/assets/js_magazine/frontOffice.js"></script>
<script>
// ============================================================
// Notification Bell — polls every 30s, dropdown interaction
// ============================================================
(function() {
    const bellBtn      = document.getElementById('notifBellBtn');
    const badge        = document.getElementById('notifBadge');
    const dropdown     = document.getElementById('notifDropdown');
    const list         = document.getElementById('notifList');
    const markAllBtn   = document.getElementById('notifMarkAllRead');
    if (!bellBtn) return; // not logged in

    const colorMap = {
        rose:   { bg:'#fff1f2', icon:'#f43f5e' },
        blue:   { bg:'#eff6ff', icon:'#3b82f6' },
        violet: { bg:'#f5f3ff', icon:'#8b5cf6' },
        primary:{ bg:'#eff6ff', icon:'#004d99' },
    };

    let open = false;

    function renderNotifications(notifications) {
        if (!notifications.length) {
            list.innerHTML = '<p class="text-sm text-slate-400 text-center py-8">No notifications yet.</p>';
            return;
        }
        list.innerHTML = notifications.map(n => {
            const c = colorMap[n.color] || colorMap.primary;
            const time = (() => {
                const d = (Date.now() - new Date(n.created_at.replace(' ','T')).getTime()) / 1000;
                if (d < 60)   return 'Just now';
                if (d < 3600) return Math.floor(d/60) + 'm ago';
                if (d < 86400)return Math.floor(d/3600) + 'h ago';
                return Math.floor(d/86400) + 'd ago';
            })();
            return `
            <div class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer notif-item ${n.is_read ? 'opacity-60' : ''}"
                 data-id="${n.id}">
              <div style="min-width:34px;height:34px;border-radius:10px;background:${c.bg};display:flex;align-items:center;justify-content:center;margin-top:2px">
                <span class="material-symbols-outlined" style="font-size:16px;color:${c.icon}">${esc(n.icon)}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-800 leading-snug">${esc(n.title)}</p>
                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">${esc(n.message)}</p>
                <p class="text-[10px] text-slate-400 mt-1">${time}</p>
              </div>
              ${!n.is_read ? '<span style="min-width:8px;height:8px;border-radius:50%;background:#3b82f6;margin-top:6px;flex-shrink:0"></span>' : ''}
            </div>`;
        }).join('');

        // Mark individual notification read on click
        list.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', async function() {
                const id = this.dataset.id;
                await fetch('/integration/magazine/notifications/read', {
                    method: 'POST', headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ id: parseInt(id) })
                });
                this.classList.add('opacity-60');
                this.querySelector('span[style*="border-radius:50%"]')?.remove();
            });
        });
    }

    async function fetchNotifications() {
        try {
            const res  = await fetch('/integration/magazine/notifications');
            const data = await res.json();
            renderNotifications(data.notifications || []);
            const n = data.unread || 0;
            if (n > 0) {
                badge.textContent = n > 99 ? '99+' : n;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        } catch(e) { /* silent */ }
    }

    bellBtn.addEventListener('click', e => {
        e.stopPropagation();
        open = !open;
        dropdown.classList.toggle('hidden', !open);
        if (open) fetchNotifications();
    });

    document.addEventListener('click', e => {
        if (open && !document.getElementById('notifBellWrap')?.contains(e.target)) {
            open = false;
            dropdown.classList.add('hidden');
        }
    });

    markAllBtn?.addEventListener('click', async () => {
        await fetch('/integration/magazine/notifications/read', {
            method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({})
        });
        badge.classList.add('hidden');
        list.querySelectorAll('.notif-item').forEach(el => {
            el.classList.add('opacity-60');
            el.querySelector('span[style*="border-radius:50%"]')?.remove();
        });
    });

    // Initial load + poll every 30s
    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    function esc(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();

// ============================================================
// Bookmark Dropdown — lazy-loads on open, remove support
// ============================================================
(function () {
    const navBtn    = document.getElementById('bookmarkNavBtn');
    const dropdown  = document.getElementById('bookmarkDropdown');
    const list      = document.getElementById('bookmarkDropList');
    const badge     = document.getElementById('bookmarkNavBadge');
    const wrap      = document.getElementById('bookmarkNavWrap');
    if (!navBtn) return;

    let open   = false;
    let loaded = false;

    function timeAgo(str) {
        const d = (Date.now() - new Date(str.replace(' ','T')).getTime()) / 1000;
        if (d < 60)    return 'Just now';
        if (d < 3600)  return Math.floor(d / 60)   + 'm ago';
        if (d < 86400) return Math.floor(d / 3600)  + 'h ago';
        return Math.floor(d / 86400) + 'd ago';
    }

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function renderBookmarks(items) {
        if (!items.length) {
            list.innerHTML = `
              <div class="flex flex-col items-center py-10 px-4 text-center">
                <span class="material-symbols-outlined text-4xl text-blue-200 mb-3">bookmark_border</span>
                <p class="text-sm font-semibold text-slate-600 mb-1">No saved articles yet</p>
                <p class="text-xs text-slate-400">Tap the bookmark icon on any article to save it here.</p>
              </div>`;
            badge.classList.add('hidden');
            return;
        }

        badge.textContent = items.length > 99 ? '99+' : items.length;
        badge.classList.remove('hidden');
        badge.classList.add('flex');

        list.innerHTML = items.map(b => `
          <div class="bookmark-drop-item flex items-center gap-3 px-4 py-3 hover:bg-blue-50/60 transition-colors group"
               data-id="${b.id}">
            ${b.image_url
              ? `<img src="${esc(b.image_url)}" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 group-hover:opacity-90 transition-opacity"/>`
              : `<div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-sky-100 flex items-center justify-center flex-shrink-0">
                   <span class="material-symbols-outlined text-blue-300 text-xl">article</span>
                 </div>`
            }
            <div class="flex-1 min-w-0">
              <a href="/integration/magazine/article?id=${b.id}"
                 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug hover:text-primary transition-colors">
                ${esc(b.titre)}
              </a>
              <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">${esc(b.categorie)}</span>
                <span class="text-[10px] text-slate-400">${timeAgo(b.bookmarked_at)}</span>
              </div>
            </div>
            <button class="drop-remove-btn flex-shrink-0 p-1.5 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100"
                    data-post-id="${b.id}" title="Remove">
              <span class="material-symbols-outlined text-[15px]">bookmark_remove</span>
            </button>
          </div>`).join('');

        list.querySelectorAll('.drop-remove-btn').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                const postId = this.dataset.postId;
                const row    = this.closest('.bookmark-drop-item');
                row.style.transition = 'opacity .2s, transform .2s';
                row.style.opacity    = '0.3';
                const fd = new FormData();
                fd.append('post_id', postId);
                try {
                    const res  = await fetch('/integration/magazine/bookmark', { method: 'POST', body: fd });
                    const data = await res.json();
                    if (data.success && !data.bookmarked) {
                        row.style.opacity   = '0';
                        row.style.transform = 'translateX(8px)';
                        setTimeout(() => {
                            row.remove();
                            const remaining = list.querySelectorAll('.bookmark-drop-item').length;
                            if (remaining === 0) renderBookmarks([]);
                            else {
                                badge.textContent = remaining > 99 ? '99+' : remaining;
                            }
                        }, 220);
                    } else {
                        row.style.opacity = '1';
                    }
                } catch(e) { row.style.opacity = '1'; }
            });
        });
    }

    async function loadBookmarks() {
        list.innerHTML = `<div class="flex items-center justify-center gap-2 py-8 text-sm text-slate-400">
          <span class="material-symbols-outlined text-xl animate-spin" style="animation-duration:1.4s">progress_activity</span>
        </div>`;
        try {
            const res  = await fetch('/integration/magazine/bookmarks/data');
            const data = await res.json();
            renderBookmarks(data.bookmarks || []);
            loaded = true;
        } catch(e) {
            list.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Could not load bookmarks.</p>';
        }
    }

    navBtn.addEventListener('click', e => {
        e.stopPropagation();
        open = !open;
        dropdown.classList.toggle('hidden', !open);
        if (open && !loaded) loadBookmarks();
        else if (open) loadBookmarks(); // refresh count on each open
    });

    document.addEventListener('click', e => {
        if (open && !wrap.contains(e.target)) {
            open = false;
            dropdown.classList.add('hidden');
        }
    });

    // Show badge count on page load (background fetch) — syncs desktop + mobile badges
    function syncBadge(n) {
        const mobileBadge = document.getElementById('mobileBookmarkBadge');
        if (n > 0) {
            const label = n > 99 ? '99+' : n;
            badge.textContent = label;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            if (mobileBadge) {
                mobileBadge.textContent = label;
                mobileBadge.classList.remove('hidden');
                mobileBadge.classList.add('flex');
            }
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
            if (mobileBadge) { mobileBadge.classList.add('hidden'); mobileBadge.classList.remove('flex'); }
        }
    }

    fetch('/integration/magazine/bookmarks/data')
        .then(r => r.json())
        .then(data => syncBadge((data.bookmarks || []).length))
        .catch(() => {});
})();
</script>
</body>
</html>
