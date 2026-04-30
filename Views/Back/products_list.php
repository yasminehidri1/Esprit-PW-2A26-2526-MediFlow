<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ── Dual-mode : standalone ou embarquée dans layout.php ──────────────────────
$embeddedInLayout = $embeddedInLayout ?? false;

// Base URLs selon le rôle
$stockRole      = $_SESSION['user']['role'] ?? '';
$baseProductUrl = '/integration/stock/products';
$baseOrderUrl   = '/integration/stock/orders';
$baseCartUrl    = '/integration/stock/cart';
if (in_array($stockRole, ['Admin', 'Fournisseur'])) {
    $baseProductUrl = '/integration/fournisseur/products';
    $baseOrderUrl   = '/integration/fournisseur/orders';
    $baseCartUrl    = '/integration/fournisseur/orders';
}
$canCRUD = in_array($stockRole, ['Admin', 'Fournisseur']);
$canCart = in_array($stockRole, ['Admin', 'pharmacien']);

// flash messages
$flashSuccess = $_SESSION['flash_success'] ?? null; unset($_SESSION['flash_success']);
$flashError   = $_SESSION['flash_error']   ?? null; unset($_SESSION['flash_error']);
?>
<?php if (!$embeddedInLayout): ?>
<!DOCTYPE html><html class="light" lang="fr">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>MediFlow | Stock Management — Produits</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = { darkMode:"class", theme:{ extend:{ colors:{ "primary":"#004d99","on-primary":"#ffffff","primary-fixed":"#d6e3ff","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-container":"#eceef0","surface-container-high":"#e6e8ea","surface-container-highest":"#e0e3e5","on-surface":"#191c1e","secondary":"#5c5f61","outline":"#727783","outline-variant":"#c2c6d4","error":"#ba1a1a","error-container":"#ffdad6" } } } }
</script>
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="bg-surface text-on-surface">

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 z-50">
    <div class="px-6 mb-10">
        <h1 class="text-xl font-bold text-blue-800 dark:text-blue-300 font-['Manrope']">MediFlow</h1>
        <p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Stock Management</p>
    </div>
    <?php
    $_pf_role = $_SESSION['user']['role'] ?? '';
    $_pf_back = '/integration/fournisseur/products';
    if ($_pf_role === 'pharmacien') $_pf_back = '/integration/stock/products';
    ?>
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 bg-white text-blue-700 rounded-l-none border-l-4 border-teal-600 font-['Manrope'] font-bold text-sm" href="<?= $_pf_back ?>">
            <span class="material-symbols-outlined">inventory_2</span><span>Produits</span>
        </a>
        <?php if ($canCart): ?>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 font-['Manrope'] font-bold text-sm hover:bg-slate-100 transition-colors" href="<?= $baseCartUrl ?>">
            <span class="material-symbols-outlined">shopping_cart</span><span>Panier</span>
        </a>
        <?php endif; ?>
        <a class="flex items-center gap-3 px-4 py-3 text-slate-500 font-['Manrope'] font-bold text-sm hover:bg-slate-100 transition-colors" href="<?= $baseOrderUrl ?>">
            <span class="material-symbols-outlined">receipt</span><span>Commandes</span>
        </a>
    </nav>
    <div class="px-6 mt-auto">
        <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/10">
            <p class="text-xs font-semibold text-secondary mb-2">Total Produits</p>
            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                <div class="bg-primary h-1.5 rounded-full" style="width: <?= min(100, (($totalProduits ?? 0) / 500) * 100) ?>%"></div>
            </div>
            <p class="text-xs text-secondary mt-2"><?= $totalProduits ?? 0 ?> produits</p>
        </div>
    </div>
</aside>

<!-- TopNavBar -->
<header class="fixed top-0 right-0 left-64 z-40 flex justify-between items-center px-8 py-3 rounded-2xl mt-4 mx-4 bg-white/80 backdrop-blur-md shadow-[0_20px_50px_rgba(0,77,153,0.05)] font-['Manrope'] font-semibold">
    <div class="flex items-center gap-4 flex-1">
        <div class="relative w-full max-w-md group">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <form method="GET" action="<?= $baseProductUrl ?>/search" class="w-full">
                <input class="w-full pl-10 pr-4 py-2 bg-surface-container-highest border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 outline-none" name="q" placeholder="Rechercher un médicament..." type="text" value="<?= htmlspecialchars($searchQuery ?? '') ?>">
            </form>
        </div>
    </div>
    <div class="flex items-center gap-3 pl-6 border-l border-outline-variant/20">
        <div class="text-right">
            <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($_SESSION['user']['prenom'] ?? '' . ' ' . ($_SESSION['user']['nom'] ?? '')) ?></p>
            <p class="text-[10px] text-secondary"><?= htmlspecialchars($stockRole) ?></p>
        </div>
    </div>
</header>

<main class="pl-64 pt-28 min-h-screen bg-surface">
<div class="max-w-7xl mx-auto px-8 pb-12">
<?php endif; // fin bloc standalone header ?>

<!-- ── CONTENU PARTAGÉ ────────────────────────────────────────────────────── -->

<?php if ($flashSuccess): ?>
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl flex items-center gap-2">
    <span class="material-symbols-outlined">check_circle</span>
    <?= htmlspecialchars($flashSuccess) ?>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl flex items-center gap-2">
    <span class="material-symbols-outlined">error</span>
    <?= htmlspecialchars($flashError) ?>
</div>
<?php endif; ?>
<?php if (!empty($_SESSION['errors'])): ?>
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
    <?php foreach ($_SESSION['errors'] as $e): ?>
        <p>❌ <?= htmlspecialchars($e) ?></p>
    <?php endforeach; unset($_SESSION['errors']); ?>
</div>
<?php endif; ?>

<!-- Page Header -->
<section class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        <h2 class="text-4xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Produits</h2>
        <p class="text-secondary font-body max-w-md">Gérez l'inventaire médicament — stocks en temps réel.</p>
    </div>
    <div class="flex gap-3 flex-wrap">
        <div class="flex bg-surface-container-low p-1 rounded-xl">
            <a href="<?= $baseProductUrl ?>" class="px-4 py-2 <?= !isset($filterCategory) && !isset($searchQuery) ? 'bg-white shadow-sm text-primary' : 'text-secondary hover:text-on-surface' ?> rounded-lg text-sm font-bold transition-colors">Tous</a>
            <a href="<?= $baseProductUrl ?>/filter?category=comprim%C3%A9s" class="px-4 py-2 <?= ($filterCategory ?? '') === 'comprimés' ? 'bg-white shadow-sm text-primary' : 'text-secondary hover:text-on-surface' ?> rounded-lg text-sm font-medium transition-colors">Comprimés</a>
            <a href="<?= $baseProductUrl ?>/filter?category=sirops" class="px-4 py-2 <?= ($filterCategory ?? '') === 'sirops' ? 'bg-white shadow-sm text-primary' : 'text-secondary hover:text-on-surface' ?> rounded-lg text-sm font-medium transition-colors">Sirops</a>
            <a href="<?= $baseProductUrl ?>/filter?category=injectables" class="px-4 py-2 <?= ($filterCategory ?? '') === 'injectables' ? 'bg-white shadow-sm text-primary' : 'text-secondary hover:text-on-surface' ?> rounded-lg text-sm font-medium transition-colors">Injectables</a>
        </div>
        <?php if ($canCRUD): ?>
        <a href="<?= $baseProductUrl ?>/create" class="flex items-center gap-2 bg-primary px-4 py-2 rounded-xl text-sm font-bold text-white hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Ajouter Produit</span>
        </a>
        <?php endif; ?>
    </div>
</section>

<?php if (isset($searchQuery)): ?>
<p class="mb-4 text-secondary">Résultats pour : <strong><?= htmlspecialchars($searchQuery) ?></strong></p>
<?php endif; ?>

<!-- Grid produits -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
<?php if (!empty($produits)): ?>
    <?php foreach ($produits as $produit): ?>
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
        <!-- Image -->
        <div class="relative w-full bg-blue-50 aspect-square flex items-center justify-center overflow-hidden">
            <?php if (!empty($produit['image'])): ?>
                <?php $imgSrc = (strpos($produit['image'], 'http') === 0 || strpos($produit['image'], '/') === 0) ? $produit['image'] : '/integration/' . $produit['image']; ?>
                <img alt="<?= htmlspecialchars($produit['nom']) ?>" class="w-full h-full object-contain" src="<?= htmlspecialchars($imgSrc) ?>" loading="lazy">
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full w-full bg-gradient-to-br from-blue-100 to-blue-50">
                    <span class="material-symbols-outlined text-5xl text-blue-300">medication</span>
                    <span class="text-xs text-blue-400 mt-2">Pas d'image</span>
                </div>
            <?php endif; ?>
            <!-- Badge -->
            <div class="absolute top-2 right-2">
                <span class="px-2.5 py-1 bg-primary text-white text-xs font-bold rounded-full">
                    <?php
                        if ($produit['categorie'] === 'comprimés') echo '💊 ';
                        elseif ($produit['categorie'] === 'sirops') echo '🧪 ';
                        else echo '💉 ';
                        echo ucfirst($produit['categorie']);
                    ?>
                </span>
            </div>
            <!-- Alerte stock -->
            <?php if ((int)$produit['quantite_disponible'] < (int)$produit['seuil_alerte']): ?>
            <div class="absolute top-2 left-2">
                <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">⚠️ Bas stock</span>
            </div>
            <?php endif; ?>
        </div>
        <!-- Contenu -->
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-bold text-base text-on-surface mb-1"><?= htmlspecialchars($produit['nom']) ?></h3>
            <?php if (!empty($produit['fournisseur_matricule'])): ?>
            <p class="text-xs text-secondary mb-2 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">local_shipping</span>
                Fournisseur: <span class="font-bold"><?= htmlspecialchars($produit['fournisseur_matricule']) ?></span>
            </p>
            <?php endif; ?>
            <p class="text-sm text-primary font-bold mb-3"><?= number_format($produit['prix_unitaire'], 2, ',', ' ') ?> DT</p>
            <!-- Stock -->
            <div class="mb-4 bg-surface-container-low rounded-xl p-3">
                <p class="text-xs text-secondary font-semibold uppercase mb-1">Stock disponible</p>
                <p class="text-3xl font-bold <?= ((int)$produit['quantite_disponible'] < (int)$produit['seuil_alerte']) ? 'text-red-600' : 'text-on-surface' ?>">
                    <?= $produit['quantite_disponible'] ?>
                    <span class="text-sm font-normal text-secondary">unités</span>
                </p>
            </div>
            <!-- Boutons -->
            <div class="flex gap-2 mt-auto">
                <?php if ($canCart): ?>
                <button onclick="addProductToCart(<?= $produit['id'] ?>, '<?= htmlspecialchars(addslashes($produit['nom'])) ?>', <?= $produit['prix_unitaire'] ?>, <?= $produit['prix_achat'] ?>, '<?= htmlspecialchars(addslashes($produit['categorie'])) ?>', '<?= htmlspecialchars(addslashes($produit['image'] ?? '')) ?>')"
                    class="flex-1 bg-blue-600 text-white font-bold py-2 px-2 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center gap-1" title="Ajouter au panier" type="button">
                    <span class="material-symbols-outlined text-base">shopping_cart</span>
                    <span class="text-xs hidden sm:inline">Commander</span>
                </button>
                <?php endif; ?>
                <?php if ($canCRUD): ?>
                <a href="<?= $baseProductUrl ?>/edit?id=<?= $produit['id'] ?>"
                   class="flex-1 bg-surface-container text-on-surface font-bold py-2 px-2 rounded-lg hover:bg-surface-container-high transition-all flex items-center justify-center gap-1" title="Modifier">
                    <span class="material-symbols-outlined text-base">edit</span>
                    <span class="text-xs hidden sm:inline">Éditer</span>
                </a>
                <a href="<?= $baseProductUrl ?>/delete?id=<?= $produit['id'] ?>"
                   onclick="return confirm('Supprimer ce produit ? Action irréversible.');"
                   class="flex-1 bg-error-container text-error font-bold py-2 px-2 rounded-lg hover:bg-red-200 transition-all flex items-center justify-center gap-1" title="Supprimer">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span class="text-xs hidden sm:inline">Supprimer</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-span-full text-center py-20">
        <span class="material-symbols-outlined text-6xl text-secondary/30">inventory_2</span>
        <h3 class="text-2xl font-bold text-on-surface mt-4 mb-2">Aucun produit trouvé</h3>
        <p class="text-secondary mb-6">Le catalogue est vide ou aucun résultat ne correspond.</p>
        <?php if ($canCRUD): ?>
        <a href="<?= $baseProductUrl ?>/create" class="inline-block bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 font-bold">+ Ajouter un produit</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>

<!-- ── FIN CONTENU PARTAGÉ ────────────────────────────────────────────────── -->

<script src="/integration/assets/js/cart.js"></script>

<?php if (!$embeddedInLayout): ?>
</div></main>
</body></html>
<?php endif; ?>
