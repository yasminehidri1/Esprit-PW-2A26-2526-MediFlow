<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>MediFlow | Produits</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
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
                        "on-secondary-container": "#191c1e"
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .stock-badge-low { @apply px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold; }
        .stock-badge-ok { @apply px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold; }
        .category-badge { @apply px-2 py-1 rounded text-xs font-semibold text-white; }
        .category-comprimés { @apply bg-blue-500; }
        .category-sirops { @apply bg-purple-500; }
        .category-injectables { @apply bg-orange-500; }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <!-- Navigation Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 z-50 border-r border-slate-200 dark:border-slate-800">
        <div class="px-6 mb-10">
            <h1 class="text-xl font-bold text-blue-800 dark:text-blue-300 font-['Manrope']">MediFlow</h1>
            <p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Pharmacie</p>
        </div>
        <nav class="flex-1 space-y-1">
            <!-- Produits - Active -->
            <a class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-400 rounded-l-none border-l-4 border-blue-600 font-['Manrope'] font-bold text-sm tracking-tight transition-colors" href="?action=front&controller=products&method=list">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Produits</span>
            </a>
            <!-- Commandes -->
            <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-l-none border-l-4 border-transparent font-['Manrope'] font-semibold text-sm tracking-tight transition-colors" href="?action=front&controller=orders&method=list">
                <span class="material-symbols-outlined">receipt</span>
                <span>Commandes</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="pl-64 pt-8 min-h-screen bg-surface">
        <div class="max-w-7xl mx-auto px-8 pb-12">
            
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-on-surface mb-2">📦 Produits Disponibles</h2>
                <p class="text-secondary">Consultez notre catalogue de produits pharmaceutiques</p>
            </div>

            <!-- Products Grid -->
            <?php if (!empty($produits)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
                    <?php foreach ($produits as $produit): ?>
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden cursor-pointer group" onclick="window.location.href='?action=front&controller=products&method=view&id=<?php echo $produit['id']; ?>'">
                            <!-- Image Container -->
                            <div class="relative h-48 bg-slate-100 overflow-hidden flex items-center justify-center group-hover:bg-slate-200 transition">
                                <?php if (!empty($produit['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="text-center">
                                        <p class="text-4xl mb-2">💊</p>
                                        <p class="text-xs text-slate-500">Image non disponible</p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3">
                                    <?php
                                        $emojis = ['comprimés' => '💊', 'sirops' => '🧪', 'injectables' => '💉'];
                                        $emoji = $emojis[$produit['categorie']] ?? '💊';
                                    ?>
                                    <span class="category-badge category-<?php echo htmlspecialchars($produit['categorie']); ?>">
                                        <?php echo $emoji; ?> <?php echo ucfirst(htmlspecialchars($produit['categorie'])); ?>
                                    </span>
                                </div>

                                <!-- Stock Status Badge -->
                                <div class="absolute top-3 right-3">
                                    <?php if ($produit['quantite_disponible'] <= $produit['seuil_alerte']): ?>
                                        <span class="stock-badge-low">⚠️ Stock Faible</span>
                                    <?php else: ?>
                                        <span class="stock-badge-ok">✅ En Stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <!-- Product Name -->
                                <h3 class="font-bold text-on-surface text-sm mb-2 line-clamp-2 group-hover:text-primary transition">
                                    <?php echo htmlspecialchars($produit['nom']); ?>
                                </h3>

                                <!-- Pricing -->
                                <div class="mb-3 space-y-1">
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-secondary">Prix de vente</p>
                                        <p class="font-bold text-primary">
                                            <?php echo number_format($produit['prix_unitaire'], 2, '.', ''); ?> DT
                                        </p>
                                    </div>
                                </div>

                                <!-- Stock Info -->
                                <div class="pt-3 border-t border-slate-200">
                                    <div class="flex justify-between items-center text-xs text-secondary">
                                        <span>📦 Quantité</span>
                                        <span class="font-semibold text-on-surface"><?php echo htmlspecialchars($produit['quantite_disponible']); ?> unités</span>
                                    </div>
                                </div>

                                <!-- View Details Link -->
                                <div class="mt-3">
                                    <p class="text-primary text-xs font-semibold cursor-pointer group-hover:underline flex items-center gap-1">
                                        Voir détails
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-4xl mb-4">📭</p>
                    <h3 class="text-lg font-bold text-on-surface mb-2">Aucun produit disponible</h3>
                    <p class="text-secondary">Le catalogue est actuellement vide.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>
