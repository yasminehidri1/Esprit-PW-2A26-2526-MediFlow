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
    <title>Détails Commande - Mediflow</title>
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
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .bg-surface { background: white; }
            .bg-white { background: white !important; }
        }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <!-- Header avec boutons -->
    <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-surface-container">
        <div class="max-w-4xl mx-auto px-8 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-on-surface font-headline">Commande #<?php echo $commande['id']; ?></h1>
            <div class="flex items-center gap-3 no-print">
                <a href="?action=orders&method=list" class="px-4 py-2 bg-surface-container hover:bg-surface-container-high text-on-surface rounded-lg font-bold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Retour
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">print</span>
                    Imprimer
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen bg-surface py-8">
        <div class="max-w-4xl mx-auto px-8">
            <!-- Infos générales -->
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 border-l-4 border-primary">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-primary text-xl">calendar_today</span>
                        <p class="text-secondary font-body text-sm">Date</p>
                    </div>
                    <p class="text-3xl font-bold text-on-surface font-headline"><?php echo date('d/m/Y'); ?></p>
                    <p class="text-sm text-secondary mt-1"><?php echo date('H:i'); ?></p>
                </div>

                <div class="bg-white rounded-2xl p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-yellow-600 text-xl">inventory_2</span>
                        <p class="text-secondary font-body text-sm">Articles</p>
                    </div>
                    <p class="text-3xl font-bold text-primary font-headline"><?php echo count($commande['lignes']); ?></p>
                </div>

                <div class="bg-white rounded-2xl p-6 border-l-4 border-green-500">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-green-600 text-xl">monetization_on</span>
                        <p class="text-secondary font-body text-sm">Montant Total</p>
                    </div>
                    <p class="text-3xl font-bold text-green-600 font-headline"><?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT</p>
                </div>
            </div>

            <!-- Statut -->
            <div class="bg-white rounded-2xl p-6 mb-8">
                <p class="text-secondary font-body text-sm mb-3">Statut actuel</p>
                <span class="px-4 py-2 rounded-full text-sm font-bold inline-block
                    <?php 
                        if ($commande['statut'] === 'en_attente') echo 'bg-yellow-100 text-yellow-700';
                        elseif ($commande['statut'] === 'validee') echo 'bg-green-100 text-green-700';
                        elseif ($commande['statut'] === 'annulee') echo 'bg-error-container text-error';
                        else echo 'bg-surface-container text-on-surface';
                    ?>
                ">
                    <?php echo ucfirst(str_replace('_', ' ', $commande['statut'])); ?>
                </span>
            </div>

            <!-- Articles de la commande -->
            <div class="bg-white rounded-2xl overflow-hidden">
                <div class="px-6 py-4 bg-surface-container-low border-b border-surface-container">
                    <h2 class="text-lg font-bold text-on-surface font-headline">Articles de la commande</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-surface-container-low border-b border-surface-container">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Produit</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Catégorie</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Quantité Demandée</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Prix Unitaire</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($commande['lignes'] as $ligne): ?>
                        <tr class="hover:bg-surface-container-lowest transition">
                            <td class="px-6 py-4 font-bold text-on-surface"><?php echo htmlspecialchars($ligne['nom']); ?></td>
                            <td class="px-6 py-4 text-on-surface">
                                <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full text-xs font-bold">
                                    <?php echo htmlspecialchars($ligne['categorie']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-on-surface"><?php echo $ligne['quantite_demande']; ?></td>
                            <td class="px-6 py-4 text-right text-on-surface"><?php echo number_format($ligne['prix'], 2, ',', ' '); ?> DT</td>
                            <td class="px-6 py-4 text-right font-bold text-primary"><?php echo number_format($ligne['quantite_demande'] * $ligne['prix'], 2, ',', ' '); ?> DT</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Total -->
                <div class="px-6 py-4 bg-surface-container-low border-t border-surface-container flex justify-end">
                    <div class="text-right">
                        <div class="text-3xl font-bold text-on-surface font-headline">
                            Total: <span class="text-green-600"><?php echo number_format($commande['total'] ?? 0, 2, ',', ' '); ?> DT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function printOrder() {
            window.print();
        }

        function updateStatus(commandeId, newStatus) {
            if (confirm('Êtes-vous sûr?')) {
                // Vous pouvez ajouter une requête AJAX ici pour mettre à jour le statut
                alert('Mise à jour du statut à: ' + newStatus);
            }
        }
    </script>
</body>
</html>
