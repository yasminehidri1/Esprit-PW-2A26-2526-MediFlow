<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Commande #<?php echo str_pad($commande['id'], 5, '0', STR_PAD_LEFT); ?> - Détails</title>
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
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-en_attente { background-color: #fef3c7; color: #92400e; }
        .status-validee { background-color: #dcfce7; color: #166534; }
        .status-annulee { background-color: #fee2e2; color: #991b1b; }
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
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

            <!-- Order Header -->
            <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
                    <!-- Order Info -->
                    <div>
                        <h1 class="text-4xl font-extrabold text-on-surface font-headline mb-2">
                            Commande #<?php echo str_pad($commande['id'], 5, '0', STR_PAD_LEFT); ?>
                        </h1>
                  <p class="text-secondary">ID: <?php echo $commande['id']; ?></p>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <span class="status-badge status-<?php echo htmlspecialchars($commande['statut']); ?>">
                            <?php 
                                $statutLabels = [
                                    'en_attente' => '⏳ En attente',
                                    'validee' => '✅ Validée',
                                    'annulee' => '❌ Annulée'
                                ];
                                echo $statutLabels[$commande['statut']] ?? htmlspecialchars($commande['statut']);
                            ?>
                        </span>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Date -->
                    <div class="p-4 rounded-lg bg-surface-container-low border border-surface-container">
                        <p class="text-xs text-secondary font-semibold mb-1">Date de Commande</p>
                        <p class="text-lg font-bold text-on-surface">
                            <?php 
                                $date = isset($commande['created_at']) ? $commande['created_at'] : (isset($commande['date']) ? $commande['date'] : '—');
                                if ($date !== '—' && $date !== '') {
                                    echo date('d/m/Y H:i', strtotime($date));
                                } else {
                                    echo '—';
                                }
                            ?>
                        </p>
                    </div>

                    <!-- Number of Articles -->
                    <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                        <p class="text-xs text-secondary font-semibold mb-1">Nombre d'Articles</p>
                        <p class="text-lg font-bold text-blue-700">
                            <?php echo $commande['nombre_articles'] ?? 0; ?>
                        </p>
                    </div>

                    <!-- Total Amount -->
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200">
                        <p class="text-xs text-secondary font-semibold mb-1">Montant Total</p>
                        <p class="text-lg font-bold text-green-700">
                            <?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Nombre de Produits Distincts -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500">
                    <p class="text-secondary text-sm mb-2">Produits Distincts</p>
                    <h3 class="text-3xl font-bold text-blue-700">
                        <?php echo count($commande['lignes'] ?? []); ?>
                    </h3>
                </div>

                <!-- Quantité Totale -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-green-500">
                    <p class="text-secondary text-sm mb-2">Quantité Totale</p>
                    <h3 class="text-3xl font-bold text-green-700">
                        <?php 
                            $qtyTotal = 0;
                            foreach ($commande['lignes'] ?? [] as $ligne) {
                                $qtyTotal += $ligne['quantite_demande'];
                            }
                            echo $qtyTotal;
                        ?>
                    </h3>
                </div>

                <!-- Prix Moyen -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-purple-500">
                    <p class="text-secondary text-sm mb-2">Prix Moyen par Unité</p>
                    <h3 class="text-3xl font-bold text-purple-700">
                        <?php 
                            $avgPrice = ($qtyTotal > 0) ? ($commande['total'] ?? 0) / $qtyTotal : 0;
                            echo number_format($avgPrice, 2, ',', ' ');
                        ?> DT
                    </h3>
                </div>
            </div>

            <!-- Order Items Table -->
            <?php if (isset($commande['lignes']) && !empty($commande['lignes'])): ?>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="p-8 border-b border-surface-container">
                    <h2 class="text-2xl font-bold text-on-surface font-headline">Détail des Articles</h2>
                </div>

                <table class="w-full">
                    <thead class="bg-surface-container-low border-b border-surface-container">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Produit</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Catégorie</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Quantité</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Prix Unitaire</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Sous-Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commande['lignes'] as $ligne): ?>
                        <tr class="border-b border-surface-container hover:bg-surface-container-low transition-colors">
                            <!-- Produit -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Product Image -->
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        <?php if (!empty($ligne['image'])): ?>
                                            <img alt="<?php echo htmlspecialchars($ligne['nom']); ?>" 
                                                 class="w-full h-full object-contain" 
                                                 src="<?php echo htmlspecialchars($ligne['image']); ?>">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-blue-300">medicine</span>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Product Info -->
                                    <div>
                                        <p class="font-semibold text-on-surface"><?php echo htmlspecialchars($ligne['nom']); ?></p>
                                        <p class="text-xs text-secondary">ID Produit: <?php echo $ligne['produit_id']; ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Catégorie -->
                            <td class="px-6 py-4 text-center">
                                <span class="category-badge category-<?php echo htmlspecialchars($ligne['categorie']); ?>">
                                    <?php 
                                        $categories = [
                                            'comprimés' => '💊 Comprimés',
                                            'sirops' => '🧴 Sirops',
                                            'injectables' => '💉 Injectables'
                                        ];
                                        echo $categories[$ligne['categorie']] ?? htmlspecialchars($ligne['categorie']);
                                    ?>
                                </span>
                            </td>

                            <!-- Quantité -->
                            <td class="px-6 py-4 text-center">
                                <p class="font-semibold text-on-surface"><?php echo $ligne['quantite_demande']; ?></p>
                                <p class="text-xs text-secondary">unités</p>
                            </td>

                            <!-- Prix Unitaire -->
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold text-on-surface">
                                    <?php echo number_format($ligne['prix'], 2, ',', ' '); ?> DT
                                </p>
                            </td>

                            <!-- Sous-Total -->
                            <td class="px-6 py-4 text-right">
                                <?php $sousTotal = $ligne['quantite_demande'] * $ligne['prix']; ?>
                                <p class="font-bold text-primary text-lg">
                                    <?php echo number_format($sousTotal, 2, ',', ' '); ?> DT
                                </p>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Totals Section -->
                <div class="bg-surface-container-low p-8">
                    <div class="flex justify-end max-w-md ml-auto">
                        <!-- Subtotal -->
                        <div class="w-full mb-4">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-white border border-surface-container mb-2">
                                <span class="text-secondary">Sous-total:</span>
                                <span class="font-semibold text-on-surface">
                                    <?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT
                                </span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="w-full">
                            <div class="flex justify-between items-center p-4 rounded-lg bg-primary text-white font-bold text-lg">
                                <span>Total Commande:</span>
                                <span><?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- No Items -->
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center mb-8">
                <span class="material-symbols-outlined text-6xl text-secondary mb-4">shopping_cart</span>
                <h3 class="text-2xl font-bold text-on-surface mb-2">Aucun article dans cette commande</h3>
                <p class="text-secondary">Les détails des articles ne sont pas disponibles.</p>
            </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="flex gap-3">
                <a href="?action=front&controller=orders&method=list" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 transition-colors font-semibold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Retour à l'historique
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
