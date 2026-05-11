<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$embeddedInLayout = $embeddedInLayout ?? false;

$stockRole    = $_SESSION['user']['role'] ?? '';
$baseOrderUrl = '/integration/stock/orders';
if (in_array($stockRole, ['Admin', 'Fournisseur'])) {
    $baseOrderUrl = '/integration/fournisseur/orders';
}

// Statut labels & colors
$statutLabels = [
    'en attente' => ['label' => 'En attente',  'class' => 'bg-yellow-100 text-yellow-700 border border-yellow-300'],
    'validée'    => ['label' => 'Validée',      'class' => 'bg-blue-100 text-blue-700 border border-blue-300'],
    'livrée'     => ['label' => 'Livrée',       'class' => 'bg-green-100 text-green-700 border border-green-300'],
    'annulée'    => ['label' => 'Annulée',      'class' => 'bg-red-100 text-red-700 border border-red-300'],
    'retournée'  => ['label' => 'Retournée',    'class' => 'bg-gray-100 text-gray-700 border border-gray-300'],
];
$currentStatut = $commande['statut'] ?? '';
$statutInfo    = $statutLabels[$currentStatut] ?? ['label' => ucfirst($currentStatut), 'class' => 'bg-surface-container text-on-surface'];

$flashSuccess = $_SESSION['flash_success'] ?? null; unset($_SESSION['flash_success']);
$flashError   = $_SESSION['flash_error']   ?? null; unset($_SESSION['flash_error']);
?>
<?php if (!$embeddedInLayout): ?>
<!DOCTYPE html><html class="light" lang="fr">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>MediFlow | Détail Commande #<?= $commande['id'] ?? '' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script>tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","on-primary":"#ffffff","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-container":"#eceef0","surface-container-high":"#e6e8ea","on-surface":"#191c1e","secondary":"#5c5f61","outline-variant":"#c2c6d4","error":"#ba1a1a","error-container":"#ffdad6","primary-fixed":"#d6e3ff"}}}}</script>
<style>
    body{font-family:'Inter',sans-serif;background-color:#f7f9fb}
    .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
    @media print{.no-print{display:none}body{background:white}}
</style>
</head>
<body class="bg-surface text-on-surface">

<!-- Minimal header standalone -->
<header class="sticky top-0 z-50 bg-white shadow-sm border-b border-surface-container no-print">
    <div class="max-w-5xl mx-auto px-8 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="<?= $baseOrderUrl ?>" class="p-2 text-secondary hover:bg-surface-container rounded-lg transition-colors" title="Retour">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-xl font-bold text-on-surface font-['Manrope']">Commande #<?= $commande['id'] ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="/integration/stock/orders/invoice?id=<?= $commande['id'] ?>"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition-colors">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span> Télécharger PDF
            </a>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-bold hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">print</span> Imprimer
            </button>
        </div>
    </div>
</header>

<main class="min-h-screen bg-surface py-8">
<div class="max-w-5xl mx-auto px-8">
<?php endif; ?>

<!-- ── CONTENU PARTAGÉ ────────────────────────────────────────────────────── -->

<!-- Breadcrumb / retour (mode embarqué uniquement) -->
<?php if ($embeddedInLayout): ?>
<div class="flex items-center justify-between mb-6 no-print">
    <div class="flex items-center gap-3">
        <a href="<?= $baseOrderUrl ?>" class="flex items-center gap-2 text-secondary hover:text-primary transition-colors text-sm font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Retour aux commandes
        </a>
        <span class="text-outline/40">/</span>
        <span class="text-on-surface font-bold text-sm">Commande #<?= $commande['id'] ?></span>
    </div>
    <div class="flex items-center gap-2">
        <a href="/integration/stock/orders/invoice?id=<?= $commande['id'] ?>"
           class="flex items-center gap-2 px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span> PDF
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 px-3 py-1.5 bg-surface-container text-on-surface rounded-lg text-sm font-medium hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined text-base">print</span> Imprimer
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Flash messages -->
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

<!-- Info cards -->
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border-l-4 border-primary shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-primary text-xl">calendar_today</span>
            <p class="text-secondary text-sm">Date commande</p>
        </div>
        <p class="text-2xl font-bold text-on-surface">
            <?= $commande['date_commandes'] ? date('d/m/Y', strtotime($commande['date_commandes'])) : '—' ?>
        </p>
        <p class="text-sm text-secondary mt-1">
            <?= $commande['date_commandes'] ? date('H:i', strtotime($commande['date_commandes'])) : '' ?>
        </p>
    </div>
    <div class="bg-white rounded-2xl p-6 border-l-4 border-yellow-500 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-yellow-500 text-xl">inventory_2</span>
            <p class="text-secondary text-sm">Articles</p>
        </div>
        <p class="text-3xl font-bold text-on-surface"><?= count($commande['lignes'] ?? []) ?></p>
        <?php if (!empty($commande['pharmacien_matricule'])): ?>
        <p class="text-sm text-secondary mt-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">person</span>
            Pharmacien: <span class="font-bold"><?= htmlspecialchars($commande['pharmacien_matricule']) ?></span>
        </p>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-2xl p-6 border-l-4 border-green-500 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-green-600 text-xl">monetization_on</span>
            <p class="text-secondary text-sm">Montant Total</p>
        </div>
        <p class="text-3xl font-bold text-green-600"><?= number_format($commande['total'] ?? 0, 2, ',', ' ') ?> DT</p>
    </div>
</div>

<!-- Statut + actions -->
<div class="bg-white rounded-2xl p-6 mb-8 shadow-sm">
    <p class="text-secondary text-sm mb-3 font-medium">Statut actuel</p>
    <div class="flex items-center justify-between flex-wrap gap-4">
        <span class="px-4 py-2 rounded-full text-sm font-bold <?= $statutInfo['class'] ?>">
            <?= htmlspecialchars($statutInfo['label']) ?>
        </span>

        <?php if (!empty($transitionsAutorisees)): ?>
        <div class="flex gap-3 flex-wrap no-print">
            <?php foreach ($transitionsAutorisees as $newStatut => $btnLabel):
                $btnClass = match($newStatut) {
                    'validée'   => 'bg-blue-600 hover:bg-blue-700 text-white',
                    'livrée'    => 'bg-green-600 hover:bg-green-700 text-white',
                    'annulée'   => 'bg-red-600 hover:bg-red-700 text-white',
                    'retournée' => 'bg-gray-600 hover:bg-gray-700 text-white',
                    default     => 'bg-primary hover:bg-primary/90 text-white',
                };
                $btnIcon = match($newStatut) {
                    'validée'   => 'check_circle',
                    'livrée'    => 'local_shipping',
                    'annulée'   => 'cancel',
                    'retournée' => 'undo',
                    default     => 'update',
                };
                if (in_array($stockRole, ['Admin', 'Fournisseur'])) {
                    $actionUrl   = '/integration/fournisseur/orders/status';
                    $hiddenField = '<input type="hidden" name="new_statut" value="' . htmlspecialchars($newStatut) . '">';
                } else {
                    $actionUrl   = '/integration/stock/orders/cancel';
                    $hiddenField = '';
                }
                $needConfirm = in_array($newStatut, ['annulée', 'retournée']);
            ?>
            <form method="POST" action="<?= $actionUrl ?>" style="display:inline;"
                  <?= $needConfirm ? 'onsubmit="return confirm(\'Confirmer cette action ?\')"' : '' ?>>
                <input type="hidden" name="order_id" value="<?= $commande['id'] ?>">
                <?= $hiddenField ?>
                <button type="submit" class="px-4 py-2 <?= $btnClass ?> rounded-xl font-bold flex items-center gap-2 transition-colors text-sm">
                    <span class="material-symbols-outlined text-base"><?= $btnIcon ?></span>
                    <?= htmlspecialchars($btnLabel) ?>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
        <?php elseif ($currentStatut === 'livrée'): ?>
        <span class="text-green-600 text-sm font-medium flex items-center gap-1">
            <span class="material-symbols-outlined text-base">verified</span>
            Commande finalisée
        </span>
        <?php elseif ($currentStatut === 'annulée'): ?>
        <span class="text-red-500 text-sm font-medium flex items-center gap-1">
            <span class="material-symbols-outlined text-base">block</span>
            Commande annulée
        </span>
        <?php else: ?>
        <span class="text-secondary text-sm italic">En cours de traitement par le fournisseur</span>
        <?php endif; ?>
    </div>
</div>

<!-- Lignes de commande -->
<div class="bg-white rounded-2xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 bg-surface-container-low border-b border-surface-container">
        <h2 class="text-lg font-bold text-on-surface font-['Manrope']">Articles de la commande</h2>
    </div>
    <?php if (empty($commande['lignes'])): ?>
    <div class="px-6 py-10 text-center text-secondary">Aucun article dans cette commande.</div>
    <?php else: ?>
    <table class="w-full">
        <thead class="bg-surface-container-low border-b border-surface-container">
            <tr>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Produit</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-on-surface">Catégorie</th>
                <th class="px-6 py-4 text-center text-sm font-bold text-on-surface">Quantité</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Prix Unitaire</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-on-surface">Sous-total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
            <?php foreach ($commande['lignes'] as $ligne): ?>
            <tr class="hover:bg-surface-container-lowest transition">
                <td class="px-6 py-4 font-bold text-on-surface"><?= htmlspecialchars($ligne['nom']) ?></td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full text-xs font-bold">
                        <?= htmlspecialchars($ligne['categorie']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-center font-bold text-on-surface"><?= $ligne['quantite_demande'] ?></td>
                <td class="px-6 py-4 text-right text-on-surface"><?= number_format($ligne['prix'], 2, ',', ' ') ?> DT</td>
                <td class="px-6 py-4 text-right font-bold text-primary"><?= number_format($ligne['quantite_demande'] * $ligne['prix'], 2, ',', ' ') ?> DT</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Total -->
    <div class="px-6 py-4 bg-surface-container-low border-t border-surface-container flex justify-end">
        <div class="text-right">
            <p class="text-secondary text-sm mb-1">Total général</p>
            <p class="text-3xl font-bold text-on-surface font-['Manrope']">
                <?= number_format($commande['total'] ?? 0, 2, ',', ' ') ?> <span class="text-green-600">DT</span>
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── FIN CONTENU ────────────────────────────────────────────────────────── -->

<?php if (!$embeddedInLayout): ?>
</div></main>
</body></html>
<?php endif; ?>
