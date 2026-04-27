<?php
/**
 * Magazine Front-Office — Embedded inside the unified Back/layout.php sidebar shell
 * Used when a logged-in Patient visits /magazine, /magazine/article, or /magazine/category
 *
 * Variables injected by PostController: $currentView (home|article|category), plus view-specific data.
 */

$currentCat    = $_GET['cat']    ?? '';
$currentAction = $_GET['action'] ?? 'home';
?>
<!-- Magazine-specific fonts and CSS (injected as partial, head already provided by layout.php) -->
<link rel="stylesheet" href="/integration/assets/css_magazine/style.css"/>
<style>
  /* Override the sidebar layout's page padding for the magazine content */
  .mag-wrap { margin: -2rem; }  /* undo the p-8 from layout.php */

  /* Magazine sub-nav bar (replaces the standalone topbar) */
  .mag-subnav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: #fff;
    border: 1px solid #e8eaf0;
    border-radius: 14px;
    padding: 12px 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
  }
  .mag-subnav-links { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
  .mag-subnav-link {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    transition: background .15s, color .15s;
    font-family: 'Inter', sans-serif;
  }
  .mag-subnav-link:hover { background: #f3f4f6; color: #111827; }
  .mag-subnav-link.active { background: #004d99; color: #fff; }

  .mag-subnav-search { position: relative; }
  .mag-subnav-search input {
    padding: 7px 36px 7px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 20px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    color: #111827;
    outline: none;
    background: #f9fafb;
    width: 200px;
    transition: border-color .15s, width .2s;
  }
  .mag-subnav-search input:focus { border-color: #004d99; width: 240px; }
  .mag-subnav-search .mag-search-icon {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    font-size: 16px; color: #9ca3af;
  }

  /* Category dropdown */
  .mag-cat-dropdown { position: relative; }
  .mag-cat-dropdown .mag-cat-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    background: #fff;
    border: 1px solid #e8eaf0;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,.10);
    min-width: 180px;
    z-index: 100;
    padding: 6px 0;
  }
  .mag-cat-dropdown:hover .mag-cat-menu { display: block; }
  .mag-cat-menu a {
    display: block;
    padding: 9px 16px;
    font-size: 13px;
    color: #374151;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    transition: background .12s;
  }
  .mag-cat-menu a:hover { background: #f3f4f6; color: #004d99; }

  /* Content area — allow scrolling & proper width */
  .mag-content-area { width: 100%; }
</style>

<div class="mag-content-area">

  <!-- Magazine sub-navigation -->
  <div class="mag-subnav">
    <div class="mag-subnav-links">
      <a href="/integration/magazine"
         class="mag-subnav-link <?= $currentAction === 'home' ? 'active' : '' ?>">
        <span class="material-symbols-outlined" style="font-size:15px;vertical-align:middle;margin-right:3px;">newspaper</span>
        Derniers articles
      </a>
      <a href="/integration/magazine/category?cat=Research"
         class="mag-subnav-link <?= $currentCat === 'Research' ? 'active' : '' ?>">
        Recherche
      </a>
      <a href="/integration/magazine/category?cat=Mental+Wellness"
         class="mag-subnav-link <?= $currentCat === 'Mental Wellness' ? 'active' : '' ?>">
        Bien-être
      </a>
      <div class="mag-cat-dropdown">
        <a href="#" class="mag-subnav-link" onclick="return false;">
          Catégories
          <span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle;">expand_more</span>
        </a>
        <div class="mag-cat-menu">
          <?php
          $cats = ['General Health','Mental Wellness','Diet & Nutrition','Active Living','Research','Journals'];
          foreach ($cats as $cat): ?>
          <a href="/integration/magazine/category?cat=<?= urlencode($cat) ?>">
            <?= htmlspecialchars($cat) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <!-- Inline search -->
    <form class="mag-subnav-search" method="GET" action="/integration/magazine">
      <input type="hidden" name="action" value="search"/>
      <input type="text" name="q" placeholder="Rechercher..."
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
      <span class="material-symbols-outlined mag-search-icon">search</span>
    </form>
  </div>

  <!-- Flash messages -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
  <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
    <span class="material-symbols-outlined text-green-600">check_circle</span>
    <?= htmlspecialchars($_SESSION['flash_success']) ?>
  </div>
  <?php unset($_SESSION['flash_success']); endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
    <span class="material-symbols-outlined text-red-600">error</span>
    <?= htmlspecialchars($_SESSION['flash_error']) ?>
  </div>
  <?php unset($_SESSION['flash_error']); endif; ?>

  <!-- Dynamic magazine view content -->
  <?php
  $magView  = $GLOBALS['magazineSubView'] ?? 'home';
  $viewPath = __DIR__ . '/' . $magView . '.php';
  if (file_exists($viewPath)) {
      include $viewPath;
  } else {
      include __DIR__ . '/home.php';
  }
  ?>

</div>

<script src="/integration/assets/js_magazine/frontOffice.js"></script>
