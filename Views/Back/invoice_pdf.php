<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Helvetica, Arial, sans-serif; font-size:11px; color:#1a1a2e; background:#fff; }

/* ── Header ── */
.header { background:#1e3a8a; padding:0; }
.header-top { padding:22px 32px 18px; }
.header-row { display:table; width:100%; }
.header-left { display:table-cell; vertical-align:middle; }
.header-right { display:table-cell; vertical-align:middle; text-align:right; }
.brand { font-size:28px; font-weight:900; color:#fff; letter-spacing:-1px; }
.brand em { color:#60a5fa; font-style:normal; }
.brand-sub { font-size:9px; color:#93c5fd; letter-spacing:3px; text-transform:uppercase; margin-top:3px; }
.invoice-label { font-size:10px; color:#93c5fd; letter-spacing:2px; text-transform:uppercase; }
.invoice-num { font-size:24px; font-weight:900; color:#fff; margin-top:2px; }
.header-bottom { background:#1e40af; padding:8px 32px; border-top:1px solid rgba(255,255,255,0.1); }
.header-bottom-row { display:table; width:100%; }
.hb-cell { display:table-cell; font-size:10px; color:#bfdbfe; }
.hb-cell strong { color:#fff; }

/* ── Bande statut ── */
.status-bar { padding:9px 32px; font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; border-bottom:3px solid; }
.s-attente { background:#fffbeb; color:#92400e; border-color:#f59e0b; }
.s-validee  { background:#eff6ff; color:#1e3a8a; border-color:#3b82f6; }
.s-livree   { background:#f0fdf4; color:#14532d; border-color:#22c55e; }
.s-annulee  { background:#fff1f2; color:#881337; border-color:#f43f5e; }

/* ── Body ── */
.body { padding:20px 32px 24px; }

/* ── Info row ── */
.info-row { display:table; width:100%; border-collapse:collapse; margin-bottom:20px; }
.info-cell { display:table-cell; width:33.33%; padding:12px 14px; background:#f8fafc;
             border:1px solid #e2e8f0; vertical-align:top; }
.info-label { font-size:8px; text-transform:uppercase; letter-spacing:1.5px; color:#64748b; margin-bottom:5px; }
.info-value { font-size:15px; font-weight:900; color:#0f172a; }
.info-sub   { font-size:10px; color:#64748b; margin-top:2px; }

/* ── Section titre ── */
.section-title { font-size:9px; font-weight:700; color:#1e40af; text-transform:uppercase;
                 letter-spacing:2px; border-bottom:2px solid #dbeafe; padding-bottom:7px;
                 margin-bottom:14px; }

/* ── Tableau produits ── */
.prod-table { width:100%; border-collapse:collapse; margin-bottom:0; }
.prod-table th { background:#1e3a8a; color:#fff; font-size:9px; text-transform:uppercase;
                 letter-spacing:0.5px; padding:9px 10px; font-weight:700; }
.prod-table th.r { text-align:right; }
.prod-table th.c { text-align:center; }
.prod-table td { padding:9px 10px; border-bottom:1px solid #f1f5f9; font-size:11px; vertical-align:middle; }
.prod-table tr:nth-child(even) td { background:#f8fafc; }
.prod-nom { font-weight:700; color:#0f172a; font-size:12px; }
.prod-ref { font-size:9px; color:#94a3b8; margin-top:1px; }
.cat-badge { display:inline-block; padding:2px 9px; background:#dbeafe; color:#1e40af;
             border-radius:12px; font-size:9px; font-weight:700; }
.r { text-align:right; }
.c { text-align:center; }
.subtotal { font-weight:900; color:#1e40af; font-size:13px; }
.qr-img { display:block; margin:0 auto; }
.qr-hint { font-size:7px; color:#94a3b8; text-align:center; margin-top:2px; }

/* ── Total ── */
.total-section { margin-top:0; border-top:2px solid #1e3a8a; }
.total-row { display:table; width:100%; }
.total-spacer { display:table-cell; }
.total-box { display:table-cell; width:220px; background:#1e3a8a; padding:14px 20px; text-align:right; }
.total-label { font-size:9px; text-transform:uppercase; letter-spacing:1px; color:#93c5fd; }
.total-amount { font-size:28px; font-weight:900; color:#fff; }
.total-cur { font-size:14px; color:#60a5fa; }

/* ── Paiement ── */
.payment-section { margin-top:14px; text-align:right; }
.pay-badge { display:inline-block; padding:6px 16px; border-radius:20px; font-size:10px; font-weight:700; }
.pay-ok  { background:#dcfce7; color:#15803d; border:1px solid #86efac; }
.pay-nok { background:#fef9c3; color:#a16207; border:1px solid #fde047; }

/* ── Divider ── */
.divider { border:none; border-top:1px solid #e2e8f0; margin:20px 0; }

/* ── Footer ── */
.footer { text-align:center; }
.footer p { font-size:9px; color:#94a3b8; line-height:1.6; }
.footer strong { color:#64748b; }
</style>
</head>
<body>

<?php
$statut     = $commande['statut'] ?? '';
$isPaid     = ($commande['paiement_statut'] ?? 'non payée') === 'payée';
$statusClass = match($statut) {
    'en attente' => 's-attente',
    'validée'    => 's-validee',
    'livrée'     => 's-livree',
    'annulée'    => 's-annulee',
    default      => 's-validee',
};
$statusText = match($statut) {
    'en attente' => 'En attente de validation',
    'validée'    => 'Commande validee',
    'livrée'     => 'Commande livree',
    'annulée'    => 'Commande annulee',
    default      => ucfirst($statut),
};
$invoiceNum = str_pad($commande['id'], 6, '0', STR_PAD_LEFT);
$dateCmd    = $commande['date_commandes'] ? date('d/m/Y', strtotime($commande['date_commandes'])) : '-';
$heureCmd   = $commande['date_commandes'] ? date('H:i', strtotime($commande['date_commandes'])) : '';
$pharmacien = htmlspecialchars($commande['pharmacien_matricule'] ?? '-');
$nbArticles = count($commande['lignes'] ?? []);
?>

<!-- Header -->
<div class="header">
    <div class="header-top">
        <div class="header-row">
            <div class="header-left">
                <div class="brand">Medi<em>Flow</em></div>
                <div class="brand-sub">Systeme de gestion pharmaceutique</div>
            </div>
            <div class="header-right">
                <div class="invoice-label">Facture</div>
                <div class="invoice-num">N° <?= $invoiceNum ?></div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="header-bottom-row">
            <div class="hb-cell">Date d'emission : <strong><?= date('d/m/Y') ?></strong></div>
            <div class="hb-cell" style="text-align:center;">Commande : <strong>#<?= $commande['id'] ?></strong></div>
            <div class="hb-cell" style="text-align:right;">Pharmacien : <strong><?= $pharmacien ?></strong></div>
        </div>
    </div>
</div>

<!-- Statut -->
<div class="status-bar <?= $statusClass ?>">
    Statut commande : <?= $statusText ?>
</div>

<!-- Corps -->
<div class="body">

    <!-- Info row -->
    <div class="info-row">
        <div class="info-cell">
            <div class="info-label">N° Commande</div>
            <div class="info-value">#<?= $commande['id'] ?></div>
        </div>
        <div class="info-cell">
            <div class="info-label">Date de commande</div>
            <div class="info-value"><?= $dateCmd ?></div>
            <div class="info-sub"><?= $heureCmd ?></div>
        </div>
        <div class="info-cell">
            <div class="info-label">Articles</div>
            <div class="info-value"><?= $nbArticles ?></div>
            <div class="info-sub">produit<?= $nbArticles > 1 ? 's' : '' ?> commande<?= $nbArticles > 1 ? 's' : '' ?></div>
        </div>
    </div>

    <!-- Tableau produits -->
    <div class="section-title">Articles commandes</div>

    <table class="prod-table">
        <thead>
            <tr>
                <th style="width:28%">Produit</th>
                <th style="width:14%">Categorie</th>
                <th class="c" style="width:8%">Qte</th>
                <th class="r" style="width:14%">Prix u.</th>
                <th class="r" style="width:14%">Sous-total</th>
                <th class="c" style="width:22%">QR Code produit</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($commande['lignes'] as $ligne):
            $subtotal = $ligne['quantite_demande'] * $ligne['prix'];
        ?>
            <tr>
                <td>
                    <div class="prod-nom"><?= htmlspecialchars($ligne['nom']) ?></div>
                    <div class="prod-ref">Ref. #<?= $ligne['produit_id'] ?></div>
                </td>
                <td><span class="cat-badge"><?= htmlspecialchars($ligne['categorie']) ?></span></td>
                <td class="c"><strong><?= (int)$ligne['quantite_demande'] ?></strong></td>
                <td class="r"><?= number_format($ligne['prix'], 2) ?> DT</td>
                <td class="r subtotal"><?= number_format($subtotal, 2) ?> DT</td>
                <td style="padding:6px 10px;">
                    <?php if (!empty($qrCodes[$ligne['produit_id']])): ?>
                    <img class="qr-img" src="<?= $qrCodes[$ligne['produit_id']] ?>" width="72" height="72" alt="QR">
                    <div class="qr-hint">Scanner pour details</div>
                    <?php else: ?>
                    <div style="text-align:center;color:#94a3b8;font-size:9px;">-</div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-section">
        <div class="total-row">
            <div class="total-spacer"></div>
            <div class="total-box">
                <div class="total-label">Total general</div>
                <div class="total-amount">
                    <?= number_format($commande['total'] ?? 0, 2) ?>
                    <span class="total-cur">DT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Paiement -->
    <div class="payment-section">
        <span class="pay-badge <?= $isPaid ? 'pay-ok' : 'pay-nok' ?>">
            <?= $isPaid ? 'Paye via Stripe' : 'Paiement en attente' ?>
        </span>
    </div>

    <hr class="divider">

    <!-- Footer -->
    <div class="footer">
        <p><strong>MediFlow</strong> — Systeme de gestion pharmaceutique</p>
        <p>Facture generee le <?= date('d/m/Y') ?> a <?= date('H:i') ?> &nbsp;|&nbsp; Document non contractuel (mode test)</p>
        <p>Les QR codes encodent les informations de chaque produit — scanner avec un lecteur QR</p>
    </div>

</div>
</body>
</html>
