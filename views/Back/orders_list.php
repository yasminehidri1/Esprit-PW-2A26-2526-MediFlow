<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Commandes - Mediflow | Stock Management</title>
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
        #searchOrders {
            transition: all 0.3s ease;
        }
        #searchOrders:focus {
            box-shadow: 0 0 0 3px rgba(0, 77, 153, 0.1);
        }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <!-- SideNavBar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-white dark:bg-slate-800 flex flex-col py-6 z-50 border-r border-surface-container">
        <div class="px-6 mb-10">
            <h1 class="text-2xl font-extrabold text-primary font-headline tracking-tight">MediFlow</h1>
            <p class="text-xs text-secondary font-semibold uppercase mt-1 tracking-widest">Stock Management</p>
        </div>
        <nav class="flex-1 space-y-1">
            <!-- Tab: Produits -->
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-['Manrope'] font-bold text-sm tracking-tight hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="?action=products&method=list">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Produits</span>
            </a>
            <!-- Tab: Panier -->
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 font-['Manrope'] font-bold text-sm tracking-tight hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="?action=cart&method=view">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span>Panier</span>
            </a>
            <!-- Active Tab: Commandes -->
            <a class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 text-primary dark:text-blue-400 rounded-l-none border-l-4 border-primary font-['Manrope'] font-bold text-sm tracking-tight transition-colors" href="?action=orders&method=list">
                <span class="material-symbols-outlined">receipt</span>
                <span>Commandes</span>
            </a>
        </nav>
        <div class="px-6 mt-auto">
            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/10">
                <p class="text-xs font-semibold text-secondary mb-2">Commandes</p>
                <div class="w-full bg-surface-container-high rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full" style="width: 100%"></div>
                </div>
                <p class="text-xs text-secondary mt-2">Historique commandes</p>
            </div>
        </div>
    </aside>

    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 left-64 z-40 flex justify-between items-center px-8 py-3 rounded-2xl mt-4 mx-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-[0_20px_50px_rgba(0,77,153,0.05)] font-['Manrope'] font-semibold">
        <div>
            <h2 class="text-2xl font-extrabold text-on-surface font-headline tracking-tight mb-1">Commandes</h2>
            <p class="text-secondary font-body text-sm">Historique de vos commandes de médicaments</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pl-64 pt-28 min-h-screen bg-surface">
        <div class="max-w-7xl mx-auto px-8 pb-12">
            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-3 text-secondary">search</span>
                    <input type="text" id="searchOrders" placeholder="Rechercher une commande..." class="w-full pl-12 pr-4 py-3 bg-surface-container-low border border-surface-container rounded-xl text-on-surface placeholder-secondary font-body focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30">
                </div>
            </div>

            <!-- Statistics Cards -->
            <?php 
                $total = count($commandes);
                $enAttente = count(array_filter($commandes, fn($c) => $c['statut'] === 'en_attente'));
                $validees = count(array_filter($commandes, fn($c) => $c['statut'] === 'validee'));
                $annulees = count(array_filter($commandes, fn($c) => $c['statut'] === 'annulee'));
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Commandes -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-primary shadow-sm">
                    <p class="text-secondary font-body text-sm mb-2">Total Commandes</p>
                    <h3 class="text-3xl font-bold text-on-surface font-headline"><?php echo $total; ?></h3>
                </div>
                <!-- En Attente -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-yellow-500 shadow-sm">
                    <p class="text-secondary font-body text-sm mb-2">En attente</p>
                    <h3 class="text-3xl font-bold text-yellow-600 font-headline"><?php echo $enAttente; ?></h3>
                </div>
                <!-- Validées -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-green-500 shadow-sm">
                    <p class="text-secondary font-body text-sm mb-2">Validées</p>
                    <h3 class="text-3xl font-bold text-green-600 font-headline"><?php echo $validees; ?></h3>
                </div>
                <!-- Annulées -->
                <div class="bg-white rounded-2xl p-6 border-l-4 border-error shadow-sm">
                    <p class="text-secondary font-body text-sm mb-2">Annulées</p>
                    <h3 class="text-3xl font-bold text-error font-headline"><?php echo $annulees; ?></h3>
                </div>
            </div>

            <!-- Commandes vides -->
            <?php if (empty($commandes)): ?>
            <div class="text-center py-24">
                <div class="text-6xl mb-4">📋</div>
                <h2 class="text-3xl font-extrabold text-on-surface font-headline tracking-tight mb-2">Aucune commande</h2>
                <p class="text-secondary font-body mb-6">Vous n'avez pas encore passé de commande</p>
                <a href="?action=products&method=list" class="inline-block px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 font-bold transition-colors">
                    🛍️ Aller aux produits
                </a>
            </div>
            <?php else: ?>

            <!-- Tableau des commandes -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-surface-container-low border-b border-surface-container">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Articles</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Montant</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Statut</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($commandes as $commande): ?>
                        <tr class="hover:bg-surface-container-lowest transition">
                            <td class="px-6 py-4 font-bold text-primary">#<?php echo $commande['id']; ?></td>
                            <td class="px-6 py-4 text-on-surface"><?php echo date('d/m/Y H:i'); ?></td>
                            <td class="px-6 py-4 text-on-surface"><?php echo $commande['nombre_articles'] ?? 0; ?> article(s)</td>
                            <td class="px-6 py-4 font-bold text-lg text-primary"><?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    <?php 
                                        if ($commande['statut'] === 'en_attente') echo 'bg-yellow-100 text-yellow-700';
                                        elseif ($commande['statut'] === 'validee') echo 'bg-green-100 text-green-700';
                                        elseif ($commande['statut'] === 'annulee') echo 'bg-error-container text-error';
                                        else echo 'bg-surface-container text-on-surface';
                                    ?>
                                ">
                                    <?php echo ucfirst(str_replace('_', ' ', $commande['statut'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="?action=orders&method=view&id=<?php echo $commande['id']; ?>" class="inline-flex items-center justify-center w-9 h-9 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg transition-colors" title="Voir détails">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    <a href="?action=cart&method=view&edit_order=<?php echo $commande['id']; ?>" class="inline-flex items-center justify-center w-9 h-9 bg-secondary/10 text-secondary hover:bg-secondary hover:text-white rounded-lg transition-colors" title="Modifier">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <button onclick="deleteOrder(<?php echo $commande['id']; ?>)" class="inline-flex items-center justify-center w-9 h-9 bg-error-container/50 text-error hover:bg-error hover:text-white rounded-lg transition-colors" title="Supprimer">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>
        </div>
    </main>

    <script>
        // Search functionality
        document.getElementById('searchOrders')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Delete button handler
        function deleteOrder(commandeId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer la commande #' + commandeId + '?')) {
                window.location.href = '?action=orders&method=delete&id=' + commandeId;
            }
        }
    </script>
</body>
</html>
