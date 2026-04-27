<!DOCTYPE html><html class="light" lang="fr"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>MediFlow | Gestion Produits - Fournisseur</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-container": "#007272",
                        "on-primary-fixed": "#001b3d",
                        "on-secondary-fixed": "#191c1e",
                        "surface-container-low": "#f2f4f6",
                        "on-tertiary": "#ffffff",
                        "secondary": "#5c5f61",
                        "on-surface": "#191c1e",
                        "on-tertiary-fixed": "#002020",
                        "error-container": "#ffdad6",
                        "error": "#ba1a1a",
                        "surface": "#f7f9fb",
                        "surface-bright": "#f7f9fb",
                        "surface-variant": "#e0e3e5",
                        "outline-variant": "#c2c6d4",
                        "on-primary-fixed-variant": "#00468c",
                        "on-primary": "#ffffff",
                        "primary-container": "#1565c0",
                        "on-error-container": "#93000a",
                        "background": "#f7f9fb",
                        "on-surface-variant": "#424752",
                        "primary-fixed": "#d6e3ff",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-fixed": "#e0e3e5",
                        "inverse-surface": "#2d3133",
                        "tertiary-fixed": "#93f2f2",
                        "surface-container": "#eceef0",
                        "outline": "#727783",
                        "surface-container-high": "#e6e8ea",
                        "on-primary-container": "#dae5ff",
                        "primary": "#004d99",
                        "on-tertiary-container": "#95f5f4",
                        "tertiary": "#005858",
                        "surface-container-lowest": "#ffffff",
                        "primary-fixed-dim": "#a9c7ff",
                        "secondary-container": "#e0e3e5",
                        "surface-tint": "#005db7",
                        "on-background": "#191c1e",
                        "surface-dim": "#d8dadc",
                        "on-secondary-fixed-variant": "#444749",
                        "on-tertiary-fixed-variant": "#004f4f",
                        "on-secondary": "#ffffff",
                        "tertiary-fixed-dim": "#76d6d5",
                        "surface-container-highest": "#e0e3e5",
                        "on-secondary-container": "#626567",
                        "secondary-fixed-dim": "#c4c7c9",
                        "inverse-primary": "#a9c7ff",
                        "on-error": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .clinical-shadow { shadow-[0_20px_50px_rgba(0,77,153,0.05)] }
    </style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 z-50">
<div class="px-6 mb-10">
<h1 class="text-xl font-bold text-blue-800 dark:text-blue-300 font-['Manrope']">MediFlow</h1>
<p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Fournisseur</p>
</div>
<nav class="flex-1 space-y-1">
<!-- Produits -->
<a class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-400 rounded-l-none border-l-4 border-teal-600 font-['Manrope'] font-bold text-sm tracking-tight transition-colors" href="?action=supplier&controller=products&method=list">
<span class="material-symbols-outlined">inventory_2</span>
<span>Produits</span>
</a>

<!-- Commandes -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-['Manrope'] font-bold text-sm tracking-tight hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="?action=supplier&controller=orders&method=list">
<span class="material-symbols-outlined">receipt</span>
<span>Commandes</span>
</a>
</nav>
<div class="px-6 mt-auto">
<div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/10">
<p class="text-xs font-semibold text-secondary mb-2">Total Produits</p>
<div class="w-full bg-surface-container-high rounded-full h-1.5">
<div class="bg-primary h-1.5 rounded-full" style="width: <?php echo min(100, ($totalProduits / 500) * 100); ?>%"></div>
</div>
<p class="text-xs text-secondary mt-2"><?php echo $totalProduits; ?> produits</p>
</div>
</div>
</aside>

<!-- TopNavBar -->
<header class="fixed top-0 right-0 left-64 z-40 flex justify-between items-center px-8 py-3 rounded-2xl mt-4 mx-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-[0_20px_50px_rgba(0,77,153,0.05)] font-['Manrope'] font-semibold">
<div class="flex items-center gap-4 flex-1">
<div class="relative w-full max-w-md group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">search</span>
<form method="GET" class="w-full">
<input type="hidden" name="action" value="supplier">
<input type="hidden" name="controller" value="products">
<input type="hidden" name="method" value="search">
<input class="w-full pl-10 pr-4 py-2 bg-surface-container-highest border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none" name="q" placeholder="Rechercher un produit..." type="text">
</form>
</div>
</div>
<div class="flex items-center gap-3">
<div class="text-right">
<p class="text-sm font-bold text-on-surface">Fournisseur</p>
<p class="text-[10px] text-secondary">BackOffice</p>
</div>
</div>
</header>

<!-- Main Content -->
<main class="pl-64 pt-28 min-h-screen bg-surface">
<div class="max-w-7xl mx-auto px-8 pb-12">
<!-- Afficher les messages de succès/erreurs -->
<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
<div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
    <?php foreach ($_SESSION['success'] as $msg): ?>
        <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endforeach; ?>
    <?php unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
<div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
    <?php foreach ($_SESSION['errors'] as $err): ?>
        <p><?php echo htmlspecialchars($err); ?></p>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
</div>
<?php endif; ?>

<!-- Page Header Section -->
<section class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
<div>
<h2 class="text-4xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Mes Produits</h2>
<p class="text-secondary font-body max-w-md">Gérez votre catalogue de produits. Créez, modifiez et supprimez vos articles.</p>
</div>
<div class="flex gap-3">
<div class="flex bg-surface-container-low p-1 rounded-xl">
<a href="?action=supplier&controller=products&method=list" class="px-4 py-2 bg-white shadow-sm rounded-lg text-sm font-bold text-primary">Tous</a>
<a href="?action=supplier&controller=products&method=filter&category=comprimés" class="px-4 py-2 text-sm font-medium text-secondary hover:text-on-surface transition-colors">Comprimés</a>
<a href="?action=supplier&controller=products&method=filter&category=sirops" class="px-4 py-2 text-sm font-medium text-secondary hover:text-on-surface transition-colors">Sirops</a>
<a href="?action=supplier&controller=products&method=filter&category=injectables" class="px-4 py-2 text-sm font-medium text-secondary hover:text-on-surface transition-colors">Injectables</a>
</div>
<a href="?action=supplier&controller=products&method=create" class="flex items-center gap-2 bg-primary px-4 py-2 rounded-xl text-sm font-bold text-white hover:bg-primary/90 transition-colors">
<span class="material-symbols-outlined text-lg">add</span>
<span>Ajouter Produit</span>
</a>
</div>
</section>

<!-- Grid Content -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
<?php if (isset($produits) && !empty($produits)): ?>
    <?php foreach ($produits as $produit): ?>
    <!-- Product Card -->
    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
        <!-- Image Container -->
        <div class="relative w-full bg-blue-100 aspect-square flex items-center justify-center overflow-hidden">
            <?php if (!empty($produit['image'])): ?>
                <img alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="w-full h-full object-contain" src="<?php echo htmlspecialchars($produit['image']); ?>" loading="lazy">
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full w-full bg-gradient-to-br from-blue-100 to-blue-50">
                    <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-xs text-blue-400 mt-2">Pas d'image</span>
                </div>
            <?php endif; ?>
            
            <!-- Badge Catégorie -->
            <div class="absolute top-2 right-2">
                <span class="px-2.5 py-1 bg-blue-600 text-white text-xs font-bold rounded-full whitespace-nowrap">
                    <?php 
                        if ($produit['categorie'] === 'comprimés') echo '💊';
                        elseif ($produit['categorie'] === 'sirops') echo '🧪';
                        elseif ($produit['categorie'] === 'injectables') echo '💉';
                        echo ' ' . ucfirst($produit['categorie']);
                    ?>
                </span>
            </div>
        </div>
        
        <!-- Card Content -->
        <div class="p-4 flex flex-col flex-1">
            <!-- Name and Price -->
            <div class="mb-4">
                <h3 class="font-bold text-base text-gray-900"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                <p class="text-sm text-gray-600">Prix: <?php echo htmlspecialchars($produit['prix_unitaire']); ?> DT</p>
            </div>
            
            <!-- Stock Section -->
            <div class="mb-4 bg-gray-100 rounded-lg p-3">
                <p class="text-xs text-gray-600 font-semibold uppercase mb-2">Stock</p>
                <div class="flex items-end justify-between">
                    <p class="text-3xl font-bold <?php echo ((int)$produit['quantite_disponible'] < (int)$produit['seuil_alerte']) ? 'text-red-600' : 'text-gray-900'; ?>">
                        <?php echo $produit['quantite_disponible']; ?>
                    </p>
                    <p class="text-xs text-gray-500 mb-1">unités</p>
                </div>
                <?php if ((int)$produit['quantite_disponible'] < (int)$produit['seuil_alerte']): ?>
                <p class="text-xs text-red-600 font-bold mt-2">⚠️ Bas Stock</p>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2 mt-auto">
                <!-- Edit Button -->
                <a href="?action=supplier&controller=products&method=edit&id=<?php echo $produit['id']; ?>" class="flex-1 bg-blue-600 text-white font-bold py-2 px-2 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-all flex items-center justify-center gap-1" title="Modifier le produit">
                    <span class="material-symbols-outlined text-base">edit</span>
                    <span class="text-xs hidden sm:inline">Éditer</span>
                </a>
                
                <!-- Delete Button -->
                <a href="?action=supplier&controller=products&method=delete&id=<?php echo $produit['id']; ?>" onclick="return confirm('Êtes-vous sûr? Cette action est irréversible.');" class="flex-1 bg-red-100 text-red-600 font-bold py-2 px-2 rounded-lg hover:bg-red-200 active:bg-red-300 transition-all flex items-center justify-center gap-1" title="Supprimer le produit">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span class="text-xs hidden sm:inline">Supprimer</span>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-span-full text-center py-16">
        <div class="mb-4">
            <p class="text-gray-500 text-lg mb-4">📦 Aucun produit trouvé</p>
        </div>
        <a href="?action=supplier&controller=products&method=create" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-bold">+ Créer un produit</a>
    </div>
<?php endif; ?>
</div>

</div>
</main>
</body></html>
