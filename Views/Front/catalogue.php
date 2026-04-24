<?php
/**
 * Equipment Catalogue — Patient View
 * @package MediFlow\Views\Front
 */

// $equipements and $data['currentUser'] injected by PatientEquipmentController

/**
 * Resolve image URL for an equipment record
 */
function getImageUrl($eq): string {
    $bases = [
        __DIR__ . '/../../assets/images/equipements/',
        __DIR__ . '/../../Assets/images/equipements/',
    ];
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($bases as $base) {
        foreach ($exts as $ext) {
            if (file_exists($base . $eq['reference'] . '.' . $ext)) {
                return '/integration/assets/images/equipements/' . $eq['reference'] . '.' . $ext;
            }
        }
    }
    if (!empty($eq['image'])) {
        return '/integration/assets/images/equipements/' . htmlspecialchars($eq['image']);
    }
    return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="160"><rect fill="%23f3f4f6" width="200" height="160"/><text fill="%239ca3af" font-family="sans-serif" font-size="13" x="50%" y="50%" text-anchor="middle" dy=".3em">No image</text></svg>';
}

$user = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Catalogue des équipements — MediFlow</title>
  <meta name="description" content="Browse and reserve certified medical equipment for home care."/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/integration/assets/css/style.css"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "background": "#f7f9fb",
            "surface-container-high": "#e6e8ea",
            "on-error": "#ffffff",
            "surface-dim": "#d8dadc",
            "surface-container-lowest": "#ffffff",
            "error": "#ba1a1a",
            "primary-fixed": "#d6e3ff",
            "on-surface-variant": "#424752",
            "inverse-surface": "#2d3133",
            "surface-container-highest": "#e0e3e5",
            "primary": "#004d99",
            "surface-bright": "#f7f9fb",
            "outline": "#727783",
            "surface-variant": "#e0e3e5",
            "primary-container": "#1565c0",
            "secondary": "#4a5f83",
            "tertiary-container": "#00736a",
            "primary-fixed-dim": "#a9c7ff",
            "outline-variant": "#c2c6d4",
            "surface": "#f7f9fb",
            "on-primary": "#ffffff",
            "on-surface": "#191c1e",
            "tertiary": "#005851",
            "secondary-container": "#c0d5ff",
            "on-background": "#191c1e",
            "surface-container": "#eceef0",
            "surface-container-low": "#f2f4f6"
          },
          borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
          fontFamily: { headline: ["Manrope"], body: ["Inter"], label: ["Inter"] }
        }
      }
    }
  </script>
  <style>
    /* ── Product grid ── */
    .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:20px; }
    .product-card { background:#fff; border-radius:14px; border:1px solid #e8eaf0; overflow:hidden; transition:box-shadow .2s,transform .2s; display:flex; flex-direction:column; }
    .product-card:hover { box-shadow:0 8px 32px rgba(0,77,153,.10); transform:translateY(-2px); }
    .card-img { width:100%; height:160px; background:#f5f7fa; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; }
    .card-img img { width:100%; height:100%; object-fit:contain; padding:12px; }
    .badge-pill { position:absolute; top:10px; right:10px; padding:3px 10px; border-radius:20px; font-size:10.5px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
    .badge-available { background:#dcfce7; color:#16a34a; }
    .badge-demand    { background:#fef3c7; color:#b45309; }
    .badge-maintenance { background:#fee2e2; color:#dc2626; }
    .card-body { padding:16px; display:flex; flex-direction:column; flex:1; }
    .card-title-row { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:4px; }
    .card-title { font-family:'Manrope',sans-serif; font-size:15px; font-weight:800; color:#111827; }
    .card-price { font-size:14px; font-weight:700; color:#004d99; white-space:nowrap; }
    .card-price .unit { font-size:11px; font-weight:500; color:#6b7280; }
    .card-desc { font-size:12px; color:#9ca3af; margin-bottom:14px; flex:1; }
    .card-actions { display:flex; gap:8px; margin-top:auto; }
    .btn-reserve { display:flex; align-items:center; gap:5px; padding:8px 16px; border-radius:8px; background:#004d99; color:#fff; font-size:13px; font-weight:700; font-family:'Inter',sans-serif; text-decoration:none; border:none; cursor:pointer; transition:background .18s; flex:1; justify-content:center; }
    .btn-reserve:hover { background:#00357a; }
    .btn-reserve[disabled] { opacity:.5; cursor:not-allowed; background:#9ca3af; }
    .btn-filter { padding:7px 14px; border-radius:8px; background:#f3f4f6; border:1px solid #e5e7eb; font-size:12.5px; font-weight:600; color:#6b7280; cursor:pointer; font-family:'Inter',sans-serif; transition:all .18s; }
    .btn-filter.active, .btn-filter:hover { background:#004d99; color:#fff; border-color:#004d99; }
    .empty-state { grid-column:1/-1; text-align:center; padding:80px 20px; color:#9ca3af; }
    .empty-state .material-symbols-outlined { font-size:56px; display:block; margin-bottom:16px; color:#d1d5db; }
    .bento-banner { background:linear-gradient(135deg,#004d99 0%,#1565c0 60%,#005851 100%); border-radius:14px; padding:28px; display:flex; align-items:center; justify-content:space-between; color:#fff; }
    .bento-banner h2 { font-family:'Manrope',sans-serif; font-size:22px; font-weight:900; margin-bottom:6px; }
    .bento-banner p  { font-size:13px; opacity:.85; max-width:380px; }
    .bento-banner .btn-offers { margin-top:14px; display:inline-block; padding:9px 22px; background:rgba(255,255,255,.2); border:1.5px solid rgba(255,255,255,.4); border-radius:8px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; font-family:'Inter',sans-serif; transition:background .18s; }
    .bento-banner .btn-offers:hover { background:rgba(255,255,255,.3); }
    .banner-icon .material-symbols-outlined { font-size:72px; opacity:.25; }
  </style>
</head>
<body class="bg-surface text-on-surface overflow-hidden">

<!-- ══ SIDEBAR ══ -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
  <div class="px-8">
    <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
    <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Soins de santé</p>
  </div>
  <nav class="flex-1 flex flex-col space-y-2 px-4">
    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1"
       href="/integration/dashboard">
      <span class="material-symbols-outlined">dashboard</span>
      <span class="font-medium">Dashboard</span>
    </a>

    <!-- Rental Equipment — active -->
    <a class="flex items-center space-x-3 text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1 shadow-sm font-bold"
       href="/integration/catalogue">
      <span class="material-symbols-outlined">medical_services</span>
      <span class="font-semibold">Location d'équipements</span>
    </a>

    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1"
       href="/integration/mes-reservations">
      <span class="material-symbols-outlined">shopping_cart</span>
      <span class="font-medium">Mes réservations</span>
    </a>
  </nav>
  <div class="px-4 border-t border-outline pt-6 flex flex-col space-y-3">
    <a href="/integration/profile" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1">
      <span class="material-symbols-outlined">account_circle</span>
      <span class="font-medium">Mon profil</span>
    </a>
    <a href="/integration/logout" class="logout-btn">
      <span class="material-symbols-outlined logout-icon">logout</span>
      <span>Déconnexion</span>
    </a>
  </div>
</aside>

<!-- ══ MAIN WRAPPER ══ -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim">

  <!-- TopBar -->
  <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
    <div class="flex items-center space-x-4">
      <span class="material-symbols-outlined text-primary">medical_services</span>
      <h2 class="text-lg font-bold text-on-surface">Catalogue des Équipements</h2>
    </div>
    <div class="flex items-center space-x-4">
      <a href="/integration/mes-reservations"
         class="flex items-center gap-2 text-sm font-semibold text-primary bg-primary-fixed/60 hover:bg-primary-fixed px-4 py-2 rounded-full transition-all duration-300">
        <span class="material-symbols-outlined text-base">shopping_cart</span>
        Mes Réservations
      </a>
      <div class="flex items-center space-x-3 pl-4 border-l border-outline/20">
        <div class="text-right">
          <p class="text-sm font-bold text-on-surface">
            <?php echo htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')); ?>
          </p>
          <p class="text-xs text-slate-500"><?php echo htmlspecialchars($user['role_name'] ?? $user['role'] ?? 'Patient'); ?></p>
        </div>
        <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm">
          <?php echo strtoupper(substr($user['prenom'] ?? 'P', 0, 1)); ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Content -->
  <div class="pt-24 pb-12 px-10 space-y-8">

    <!-- Page header -->
    <section>
      <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent">
        Catalogue de Location
      </h2>
      <p class="text-on-surface-variant mt-1 font-medium">Certified medical equipment for home care.</p>
    </section>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
      <button class="btn-filter active" data-filter="all">All</button>
      <button class="btn-filter" data-filter="mobilite">Mobility</button>
      <button class="btn-filter" data-filter="respiratoire">Respiratory</button>
      <button class="btn-filter" data-filter="autre">Other</button>
    </div>

    <!-- Product grid -->
    <section>
      <div class="product-grid" id="product-grid">

        <?php if (empty($equipements)): ?>
          <div class="empty-state">
            <span class="material-symbols-outlined">inventory_2</span>
            <p class="font-bold text-lg text-slate-700 mb-2">No equipment available</p>
            <p class="text-sm">Check back soon for available rentals.</p>
          </div>
        <?php else: ?>

          <?php foreach ($equipements as $eq):
            $badgeClass = 'badge-available'; $badgeLabel = 'AVAILABLE';
            if ($eq['statut'] === 'loue')        { $badgeClass = 'badge-demand';       $badgeLabel = 'RENTED'; }
            if ($eq['statut'] === 'maintenance') { $badgeClass = 'badge-maintenance';  $badgeLabel = 'MAINTENANCE'; }

            $catFilter = strtolower(str_replace(
              ['é','è','ê','à','â','î','ô','û','ç'],
              ['e','e','e','a','a','i','o','u','c'],
              $eq['categorie']
            ));

            $imgUrl = getImageUrl($eq);
            $prixDT = number_format((float)$eq['prix_jour'], 3, ',', '.');
            $urlRes = '/integration/reservation?id=' . (int)$eq['id'];
          ?>

          <div class="product-card" data-category="<?= $catFilter ?>" data-id="<?= $eq['id'] ?>">
            <div class="card-img">
              <img src="<?= $imgUrl ?>"
                   alt="<?= htmlspecialchars($eq['nom']) ?>"
                   loading="lazy"
                   onerror="this.style.display='none'"/>
              <span class="badge-pill <?= $badgeClass ?>"><?= $badgeLabel ?></span>
            </div>
            <div class="card-body">
              <div class="card-title-row">
                <h3 class="card-title"><?= htmlspecialchars($eq['nom']) ?></h3>
                <div class="card-price"><?= $prixDT ?><span class="unit"> DT/d</span></div>
              </div>
              <p class="card-desc"><?= htmlspecialchars($eq['categorie']) ?> &mdash; Réf: <?= htmlspecialchars($eq['reference']) ?></p>
              <div class="card-actions">
                <?php if ($eq['statut'] === 'disponible'): ?>
                  <a class="btn-reserve" href="<?= $urlRes ?>">
                    <span class="material-symbols-outlined" style="font-size:15px;">calendar_today</span>
                    Reserve
                  </a>
                <?php else: ?>
                  <button class="btn-reserve" type="button" disabled>
                    <span class="material-symbols-outlined" style="font-size:15px;">block</span>
                    Unavailable
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <?php endforeach; ?>

          <!-- Promo Banner -->
          <div class="bento-banner" style="grid-column:span 2;">
            <div>
              <h2>Need a complete pack?</h2>
              <p>We offer custom solutions for home hospitalization. Get 15% off on combined rentals.</p>
              <button class="btn-offers" type="button">View Offers</button>
            </div>
            <div class="banner-icon">
              <span class="material-symbols-outlined">medical_information</span>
            </div>
          </div>

        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>

<script>
/* ── Category filter ── */
document.querySelectorAll('.btn-filter').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    document.querySelectorAll('.product-card').forEach(card => {
      const cat = card.dataset.category || '';
      card.style.display = (filter === 'all' || cat.includes(filter)) ? '' : 'none';
    });
  });
});
</script>
</body>
</html>