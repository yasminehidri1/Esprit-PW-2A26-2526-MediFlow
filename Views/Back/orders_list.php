<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$embeddedInLayout = $embeddedInLayout ?? false;

$stockRole      = $_SESSION['user']['role'] ?? '';
$baseProductUrl = '/integration/stock/products';
$baseOrderUrl   = '/integration/stock/orders';
$baseCartUrl    = '/integration/stock/cart';
if (in_array($stockRole, ['Admin', 'Fournisseur'])) {
    $baseProductUrl = '/integration/fournisseur/products';
    $baseOrderUrl   = '/integration/fournisseur/orders';
    $baseCartUrl    = '/integration/fournisseur/orders';
}
$canCart = in_array($stockRole, ['Admin', 'pharmacien']);

// Correct statut counts using real DB enum values
$total      = count($commandes ?? []);
$enAttente  = count(array_filter($commandes ?? [], fn($c) => $c['statut'] === 'en attente'));
$validees   = count(array_filter($commandes ?? [], fn($c) => $c['statut'] === 'validée'));
$livrees    = count(array_filter($commandes ?? [], fn($c) => $c['statut'] === 'livrée'));
$annulees   = count(array_filter($commandes ?? [], fn($c) => $c['statut'] === 'annulée'));

$statutColors = [
    'en attente' => 'bg-yellow-100 text-yellow-700',
    'validée'    => 'bg-blue-100 text-blue-700',
    'livrée'     => 'bg-green-100 text-green-700',
    'annulée'    => 'bg-red-100 text-red-700',
    'retournée'  => 'bg-gray-100 text-gray-700',
];
?>
<?php if (!$embeddedInLayout): ?>
<!DOCTYPE html><html class="light" lang="fr">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>MediFlow | Commandes</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script>tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","on-primary":"#ffffff","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-container":"#eceef0","surface-container-high":"#e6e8ea","on-surface":"#191c1e","secondary":"#5c5f61","outline-variant":"#c2c6d4","error":"#ba1a1a","error-container":"#ffdad6","primary-fixed":"#d6e3ff"}}}}</script>
<style>body{font-family:'Inter',sans-serif}.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}</style>
</head>
<body class="bg-surface text-on-surface">

<!-- Sidebar standalone -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-white flex flex-col py-6 z-50 border-r border-surface-container">
    <div class="px-6 mb-10">
        <h1 class="text-xl font-bold text-primary font-['Manrope']">MediFlow</h1>
        <p class="text-xs text-secondary font-medium tracking-wider uppercase mt-1">Stock Management</p>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 text-secondary font-['Manrope'] font-bold text-sm hover:bg-surface-container-low transition-colors" href="<?= $baseProductUrl ?>">
            <span class="material-symbols-outlined">inventory_2</span><span>Produits</span>
        </a>
        <?php if ($canCart): ?>
        <a class="flex items-center gap-3 px-4 py-3 text-secondary font-['Manrope'] font-bold text-sm hover:bg-surface-container-low transition-colors" href="<?= $baseCartUrl ?>">
            <span class="material-symbols-outlined">shopping_cart</span><span>Panier</span>
        </a>
        <?php endif; ?>
        <a class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary border-l-4 border-primary font-['Manrope'] font-bold text-sm" href="<?= $baseOrderUrl ?>">
            <span class="material-symbols-outlined">receipt</span><span>Commandes</span>
        </a>
    </nav>
</aside>

<!-- Header standalone -->
<header class="fixed top-0 right-0 left-64 z-40 flex items-center px-8 py-4 bg-white/80 backdrop-blur-md shadow-sm border-b border-surface-container">
    <div>
        <h2 class="text-2xl font-extrabold text-on-surface font-['Manrope']">Commandes</h2>
        <p class="text-secondary text-sm">Historique des commandes de médicaments</p>
    </div>
</header>

<main class="pl-64 pt-24 min-h-screen bg-surface">
<div class="max-w-7xl mx-auto px-8 pb-12">
<?php endif; ?>

<!-- ── CONTENU PARTAGÉ ────────────────────────────────────────────────────── -->

<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl flex items-center gap-2">
    <span class="material-symbols-outlined">check_circle</span>
    <?= htmlspecialchars($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl flex items-center gap-2">
    <span class="material-symbols-outlined">error</span>
    <?= htmlspecialchars($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Header section -->
<div class="flex items-center justify-between mb-8">
    <div>
        <?php if ($embeddedInLayout): ?>
        <h2 class="text-3xl font-extrabold text-on-surface font-['Manrope'] mb-1">Commandes</h2>
        <p class="text-secondary text-sm">Historique des commandes de médicaments</p>
        <?php endif; ?>
    </div>
    <?php if ($canCart): ?>
    <a href="<?= $baseCartUrl ?>" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-primary/90 transition-colors">
        <span class="material-symbols-outlined">add_shopping_cart</span>
        Nouvelle commande
    </a>
    <?php endif; ?>
</div>

<!-- Stats cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border-l-4 border-primary shadow-sm">
        <p class="text-secondary text-sm mb-1">Total</p>
        <p class="text-3xl font-bold text-on-surface"><?= $total ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-secondary text-sm mb-1">En attente</p>
        <p class="text-3xl font-bold text-yellow-600"><?= $enAttente ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border-l-4 border-blue-500 shadow-sm">
        <p class="text-secondary text-sm mb-1">Validées</p>
        <p class="text-3xl font-bold text-blue-600"><?= $validees ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border-l-4 border-green-500 shadow-sm">
        <p class="text-secondary text-sm mb-1">Livrées</p>
        <p class="text-3xl font-bold text-green-600"><?= $livrees ?></p>
    </div>
</div>

<!-- Search -->
<div class="mb-6 relative">
    <span class="material-symbols-outlined absolute left-4 top-3 text-secondary">search</span>
    <input type="text" id="searchOrders" placeholder="Rechercher une commande..."
           class="w-full pl-12 pr-4 py-3 bg-white border border-surface-container rounded-xl text-on-surface placeholder-secondary focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30">
</div>

<!-- Table -->
<?php if (empty($commandes)): ?>
<div class="text-center py-24 bg-white rounded-2xl">
    <span class="material-symbols-outlined text-6xl text-secondary/30">receipt_long</span>
    <h3 class="text-2xl font-bold text-on-surface mt-4 mb-2">Aucune commande</h3>
    <p class="text-secondary mb-6">Aucune commande enregistrée pour le moment.</p>
    <?php if ($canCart): ?>
    <a href="<?= $baseCartUrl ?>" class="inline-block bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary/90">
        🛍️ Passer une commande
    </a>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-surface-container-low border-b border-surface-container">
            <tr>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">ID</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Date</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Pharmacien</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Articles</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Montant</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Statut</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container" id="ordersTableBody">
            <?php foreach ($commandes as $commande): ?>
            <tr class="hover:bg-surface-container-lowest transition">
                <td class="px-6 py-4 font-bold text-primary">#<?= $commande['id'] ?></td>
                <td class="px-6 py-4 text-on-surface text-sm">
                    <?= $commande['date_commandes'] ? date('d/m/Y H:i', strtotime($commande['date_commandes'])) : '—' ?>
                </td>
                <td class="px-6 py-4 text-on-surface text-sm font-medium">
                    <?= !empty($commande['pharmacien_matricule']) ? htmlspecialchars($commande['pharmacien_matricule']) : '—' ?>
                </td>
                <td class="px-6 py-4 text-on-surface"><?= $commande['nombre_articles'] ?? 0 ?> article(s)</td>
                <td class="px-6 py-4 font-bold text-primary"><?= number_format($commande['total'] ?? 0, 2, ',', ' ') ?> DT</td>
                <td class="px-6 py-4">
                    <?php
                        $sc = $statutColors[$commande['statut']] ?? 'bg-surface-container text-on-surface';
                        $sl = match($commande['statut']) {
                            'en attente' => 'En attente',
                            'validée'    => 'Validée',
                            'livrée'     => 'Livrée',
                            'annulée'    => 'Annulée',
                            'retournée'  => 'Retournée',
                            default      => ucfirst($commande['statut']),
                        };
                    ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $sc ?>"><?= $sl ?></span>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="<?= $baseOrderUrl ?>/view?id=<?= $commande['id'] ?>"
                       class="inline-flex items-center justify-center w-9 h-9 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg transition-colors" title="Voir détails">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- ── FIN CONTENU ────────────────────────────────────────────────────────── -->

<?php if (!$embeddedInLayout): ?>
</div></main>
<script>
document.getElementById('searchOrders')?.addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#ordersTableBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body></html>
<?php endif; ?>
