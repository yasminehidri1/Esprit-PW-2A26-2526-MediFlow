<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - Détail Produit</title>
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
        .category-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .category-comprimés { background-color: #dbeafe; color: #1e40af; }
        .category-sirops { background-color: #fef3c7; color: #92400e; }
        .category-injectables { background-color: #fecaca; color: #991b1b; }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <!-- Main Content -->
    <main class="pt-8 min-h-screen bg-surface">
        <div class="max-w-6xl mx-auto px-8 pb-12">
            <!-- Breadcrumb -->
            <div class="mb-8 flex items-center gap-2 text-sm">
                <a href="?action=front&controller=products&method=list" class="text-primary hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">inventory_2</span>
                    Produits
                </a>
                <span class="text-secondary">/</span>
                <span class="text-secondary">Détail du produit</span>
            </div>

            <!-- Product Detail Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Image Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm p-6">
                        <!-- Product Image -->
                        <div class="w-full bg-blue-100 aspect-square flex items-center justify-center rounded-lg mb-6 overflow-hidden">
                            <?php if (!empty($produit['image'])): ?>
                                <img alt="<?php echo htmlspecialchars($produit['nom']); ?>" 
                                     class="w-full h-full object-contain" 
                                     src="<?php echo htmlspecialchars($produit['image']); ?>">
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center h-full w-full bg-gradient-to-br from-blue-100 to-blue-50">
                                    <span class="material-symbols-outlined text-6xl text-blue-300">medicine</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Category Badge -->
                        <div class="mb-6">
                            <span class="category-badge category-<?php echo htmlspecialchars($produit['categorie']); ?>">
                                <?php 
                                    $categories = [
                                        'comprimés' => '💊',
                                        'sirops' => '🧴',
                                        'injectables' => '💉'
                                    ];
                                    echo $categories[$produit['categorie']] . ' ' . htmlspecialchars($produit['categorie']);
                                ?>
                            </span>
                        </div>

                        <!-- Stock Status -->
                        <div class="p-4 rounded-lg bg-surface-container-low mb-4">
                            <?php if ($produit['quantite_disponible'] < $produit['seuil_alerte']): ?>
                                <span class="inline-block px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">⚠️ Stock Critique</span>
                            <?php elseif ($produit['quantite_disponible'] > 50): ?>
                                <span class="inline-block px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">✅ En Stock</span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">⚠️ Stock Limité</span>
                            <?php endif; ?>
                        </div>

                        <!-- Quantité Disponible -->
                        <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                            <p class="text-xs text-secondary mb-1">Quantité disponible</p>
                            <p class="text-3xl font-bold text-blue-700"><?php echo $produit['quantite_disponible']; ?></p>
                            <p class="text-xs text-secondary mt-2">unités en stock</p>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="lg:col-span-2">
                    <!-- Product Info -->
                    <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                        <!-- Title -->
                        <h1 class="text-4xl font-extrabold text-on-surface font-headline mb-2 tracking-tight">
                            <?php echo htmlspecialchars($produit['nom']); ?>
                        </h1>

                        <!-- ID and Category -->
                        <div class="flex items-center gap-4 mb-6 text-secondary">
                            <span>ID: <?php echo $produit['id']; ?></span>
                            <span>•</span>
                            <span>Catégorie: <?php echo htmlspecialchars($produit['categorie']); ?></span>
                        </div>

                        <!-- Pricing Section -->
                        <div class="border-t border-surface-container pt-6 mb-6">
                            <h2 class="text-lg font-bold text-on-surface mb-4">💰 Tarification</h2>
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Prix Unitaire -->
                                <div class="p-4 rounded-lg bg-primary/10 border border-primary/20">
                                    <p class="text-sm text-secondary mb-1">Prix de Vente</p>
                                    <p class="text-3xl font-bold text-primary">
                                        <?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> DT
                                    </p>
                                </div>

                                <!-- Prix Achat -->
                                <div class="p-4 rounded-lg bg-surface-container-low">
                                    <p class="text-sm text-secondary mb-1">Prix d'Achat</p>
                                    <p class="text-3xl font-bold text-on-surface">
                                        <?php echo number_format($produit['prix_achat'], 2, ',', ' '); ?> DT
                                    </p>
                                </div>
                            </div>

                            <!-- Marge Bénéficiaire -->
                            <?php 
                                $marge = $produit['prix_unitaire'] - $produit['prix_achat'];
                                $margePercent = ($produit['prix_achat'] > 0) ? ($marge / $produit['prix_achat']) * 100 : 0;
                            ?>
                            <div class="mt-6 p-4 rounded-lg bg-green-50 border border-green-200">
                                <p class="text-sm text-secondary mb-2">Marge Bénéficiaire</p>
                                <div class="flex items-baseline gap-3">
                                    <p class="text-2xl font-bold text-green-700">
                                        <?php echo number_format($marge, 2, ',', ' '); ?> DT
                                    </p>
                                    <p class="text-lg text-green-600">
                                        (<?php echo number_format($margePercent, 1, ',', ' '); ?>%)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Information Section -->
                        <div class="border-t border-surface-container pt-6 mb-6">
                            <h2 class="text-lg font-bold text-on-surface mb-4">📊 Informations de Stock</h2>
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Quantité Disponible -->
                                <div class="p-4 rounded-lg bg-surface-container-low">
                                    <p class="text-sm text-secondary mb-1">Quantité Disponible</p>
                                    <p class="text-3xl font-bold text-on-surface"><?php echo $produit['quantite_disponible']; ?></p>
                                    <p class="text-xs text-secondary mt-1">unités</p>
                                </div>

                                <!-- Seuil d'Alerte -->
                                <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200">
                                    <p class="text-sm text-secondary mb-1">Seuil d'Alerte</p>
                                    <p class="text-3xl font-bold text-yellow-700"><?php echo $produit['seuil_alerte']; ?></p>
                                    <p class="text-xs text-secondary mt-1">unités</p>
                                </div>
                            </div>

                            <!-- Stock Value -->
                            <?php $stockValue = $produit['quantite_disponible'] * $produit['prix_achat']; ?>
                            <div class="mt-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                                <p class="text-sm text-secondary mb-2">Valeur du Stock</p>
                                <p class="text-2xl font-bold text-blue-700">
                                    <?php echo number_format($stockValue, 2, ',', ' '); ?> DT
                                </p>
                                <p class="text-xs text-secondary mt-1">
                                    Calculé: <?php echo $produit['quantite_disponible']; ?> unités × <?php echo number_format($produit['prix_achat'], 2, ',', ' '); ?> DT
                                </p>
                            </div>
                        </div>

                        <!-- Status Indicators -->
                        <div class="border-t border-surface-container pt-6">
                            <h2 class="text-lg font-bold text-on-surface mb-4">🏷️ État du Produit</h2>
                            <div class="space-y-3">
                                <!-- Stock Status -->
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low">
                                    <span class="material-symbols-outlined text-lg">
                                        <?php echo ($produit['quantite_disponible'] >= $produit['seuil_alerte']) ? 'check_circle' : 'warning'; ?>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-on-surface">État du Stock</p>
                                        <p class="text-sm text-secondary">
                                            <?php 
                                                if ($produit['quantite_disponible'] < $produit['seuil_alerte']) {
                                                    echo 'Stock en dessous du seuil critique';
                                                } elseif ($produit['quantite_disponible'] > 50) {
                                                    echo 'Stock suffisant';
                                                } else {
                                                    echo 'Stock limité';
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8">
                <a href="?action=front&controller=products&method=list" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 transition-colors font-semibold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Retour à la liste des produits
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-surface-container py-8 mt-12">
        <div class="max-w-6xl mx-auto px-8 text-center">
            <p class="text-secondary text-sm">© 2026 MediFlow - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
