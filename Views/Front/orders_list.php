<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Commandes - MediFlow</title>
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
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-en_attente { background-color: #fef3c7; color: #92400e; }
        .status-validee { background-color: #dcfce7; color: #166534; }
        .status-annulee { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 flex flex-col py-6 z-50 border-r border-slate-200 dark:border-slate-800">
        <div class="px-6 mb-10">
            <h1 class="text-xl font-bold text-blue-800 dark:text-blue-300 font-['Manrope']">MediFlow</h1>
            <p class="text-xs text-slate-500 font-medium tracking-wider uppercase mt-1">Pharmacie</p>
        </div>
        <nav class="flex-1 space-y-1">
            <!-- Produits -->
            <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-l-none border-l-4 border-transparent font-['Manrope'] font-semibold text-sm tracking-tight transition-colors" href="?action=front&controller=products&method=list">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Produits</span>
            </a>
            <!-- Commandes - Active -->
            <a class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-400 rounded-l-none border-l-4 border-blue-600 font-['Manrope'] font-bold text-sm tracking-tight transition-colors" href="?action=front&controller=orders&method=list">
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
                <h2 class="text-3xl font-bold text-on-surface mb-2">📋 Historique des Commandes</h2>
                <p class="text-secondary">Consultez vos commandes précédentes et leurs détails</p>
            </div>

            <!-- Statistics Cards -->
            <?php 
                if (isset($commandes) && !empty($commandes)) {
                    $total = count($commandes);
                    $enAttente = count(array_filter($commandes, fn($c) => $c['statut'] === 'en_attente'));
                    $validees = count(array_filter($commandes, fn($c) => $c['statut'] === 'validee'));
                    $annulees = count(array_filter($commandes, fn($c) => $c['statut'] === 'annulee'));
                    
                    // Calcul du total général
                    $totalMontant = 0;
                    foreach ($commandes as $cmd) {
                        $totalMontant += $cmd['total'] ?? 0;
                    }
                } else {
                    $total = 0;
                    $enAttente = 0;
                    $validees = 0;
                    $annulees = 0;
                    $totalMontant = 0;
                }
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
                <!-- Total Commandes -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-primary shadow-sm hover:shadow-md transition-all">
                    <p class="text-secondary font-body text-sm mb-2">Total Commandes</p>
                    <h3 class="text-3xl font-bold text-on-surface font-headline"><?php echo $total; ?></h3>
                    <p class="text-xs text-secondary mt-2">Commandes passées</p>
                </div>
                <!-- En Attente -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition-all">
                    <p class="text-secondary font-body text-sm mb-2">En attente</p>
                    <h3 class="text-3xl font-bold text-yellow-600 font-headline"><?php echo $enAttente; ?></h3>
                    <p class="text-xs text-secondary mt-2">À traiter</p>
                </div>
                <!-- Validées -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-green-500 shadow-sm hover:shadow-md transition-all">
                    <p class="text-secondary font-body text-sm mb-2">Validées</p>
                    <h3 class="text-3xl font-bold text-green-600 font-headline"><?php echo $validees; ?></h3>
                    <p class="text-xs text-secondary mt-2">Confirmées</p>
                </div>
                <!-- Montant Total -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-teal-500 shadow-sm hover:shadow-md transition-all">
                    <p class="text-secondary font-body text-sm mb-2">Montant Total</p>
                    <h3 class="text-3xl font-bold text-teal-600 font-headline"><?php echo number_format($totalMontant, 2, ',', ' '); ?> DT</h3>
                    <p class="text-xs text-secondary mt-2">Toutes commandes</p>
                </div>
            </div>

            <!-- Orders Table -->
            <?php if (isset($commandes) && !empty($commandes)): ?>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-surface-container-low border-b border-surface-container">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">ID Commande</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Date</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Articles</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Montant Total</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Statut</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                        <tr class="border-b border-surface-container hover:bg-surface-container-low transition-colors">
                            <!-- ID Commande -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-on-surface">#<?php echo str_pad($commande['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                <p class="text-xs text-secondary">ID: <?php echo $commande['id']; ?></p>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4">
                                <p class="text-sm text-on-surface">
                                    <?php 
                                        $date = isset($commande['created_at']) ? $commande['created_at'] : (isset($commande['date']) ? $commande['date'] : '—');
                                        if ($date !== '—') {
                                            echo date('d/m/Y', strtotime($date));
                                        } else {
                                            echo '—';
                                        }
                                    ?>
                                </p>
                            </td>

                            <!-- Nombre Articles -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                    <?php echo $commande['nombre_articles'] ?? 0; ?>
                                </span>
                            </td>

                            <!-- Montant -->
                            <td class="px-6 py-4 text-right">
                                <p class="text-lg font-bold text-primary">
                                    <?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT
                                </p>
                            </td>

                            <!-- Statut -->
                            <td class="px-6 py-4 text-center">
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
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-center">
                                <a href="?action=front&controller=orders&method=view&id=<?php echo $commande['id']; ?>" 
                                   class="inline-block px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors flex items-center gap-2 justify-center">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                    Voir détails
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <?php else: ?>
            <div class="text-center py-24">
                <div class="text-6xl mb-4">📋</div>
                <h2 class="text-3xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Aucune commande</h2>
                <p class="text-secondary font-body mb-6">Vous n'avez pas encore passé de commande</p>
                <a href="?action=front&controller=products&method=list" class="inline-block px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 font-bold transition-colors">
                    🛍️ Parcourir nos produits
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-surface-container py-8 mt-12">
        <div class="max-w-7xl mx-auto px-8 text-center">
            <p class="text-secondary text-sm">© 2026 MediFlow - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
