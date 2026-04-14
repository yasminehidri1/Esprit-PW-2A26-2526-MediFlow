<?php
require_once __DIR__ . '/../../model/Equipement.php';

/* Taux BCT : 1 EUR = 3.4052 DT */
define('EUR_TO_DT', 3.4052);

$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int)$_GET['id'] : 0;
$eq = null; $erreur = null;

if ($id <= 0) {
    $erreur = "Aucun identifiant d'équipement fourni. (ex: ?id=1)";
} else {
    try {
        $eq = (new Equipement())->getById($id);
        if (!$eq) $erreur = "Équipement ID=$id introuvable.";
    } catch (Exception $e) { $erreur = "Erreur BDD : " . $e->getMessage(); }
}

$prixDT    = $eq ? (float)$eq['prix_jour'] * EUR_TO_DT : 0;
$prixDTFmt = number_format($prixDT, 3, ',', '.');

$imagesDemo = [
    'EQ-9402'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
    'EQ-1108'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'EQ-7721'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuASF4CH3yRiFxmEjcycHQjg-OUbYQT77I53FBZzckUXpa2EtPtmunb7ZRxYISNkrLGEAWuscNqpec_8frZZpIDJvKZDDSwEqBnV4W29wv4-vrFh33FIYb14bKr4O-MFxWuUIBprgX7SHhODjGHfvZ7RKlWLREZY-t6I2wvxjWPNtP-01AY8eTMyBhGdsUImxYLwhsDe_y1h-cpUs8pL9OiEHV7pzOGu9gk53SNUQEbzNvGRygDOgsp7fXUzZfgOItBUyNUZsEwmKTU',
    'EQ-2256'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
    'EQ-3310'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'EQ-4401'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuD7Grmehl5yETNWyJDc-A9yRVrtJLksaZyf4sJ7joP2sjKPeU2cmKryKdP5tXghMptmSFPOx0QWe7gGuz16Kc7FzTT6_A5IslIW-sQRKX9Fhp2OtfOXUKo1LcRtW1AvYoWfEuMQm2UlLBGS3IwcgBl9d85kI0Lm8Qh6ixkLF51ZMCjLgV5LPCKJmGvvgsoS8PSbfRX73L_8MU9oovtKjftr_y0f_vBSFVo74P3d_qEL-JfA97pVTJTXk2s_9_NpMzVGD5VygLgiHE0',
];
$imagesCat=['Mobilité'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuAUWAqVVgdD68nfBWg0dXKwdLWpnTlMCKLy44AgjzeTUuXyMkd6oReRSckFR3xIVCZUKdxF2Lk0X7-2m-AtWVEOImdMZ_rT5jDf4eKgzPSExAeV1ifKVSdtJHTQL9FJH0S5IjCpNyKzodadcfJ7aOJi1CaQP-wo_Nnj6IrbILxue4IOtdG6QMh4Z6fE0ahwu4m1NLaQL8ofVf38sA7xDv4dd7U_VKlBBqUxmcjv4dpiNgiHzj3Oc2uOVVXBMqKelsq37dejHb8_izA','Gériatrie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuASF4CH3yRiFxmEjcycHQjg-OUbYQT77I53FBZzckUXpa2EtPtmunb7ZRxYISNkrLGEAWuscNqpec_8frZZpIDJvKZDDSwEqBnV4W29wv4-vrFh33FIYb14bKr4O-MFxWuUIBprgX7SHhODjGHfvZ7RKlWLREZY-t6I2wvxjWPNtP-01AY8eTMyBhGdsUImxYLwhsDe_y1h-cpUs8pL9OiEHV7pzOGu9gk53SNUQEbzNvGRygDOgsp7fXUzZfgOItBUyNUZsEwmKTU','Respiratoire'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI','Réanimation'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI','Radiologie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc','Cardiologie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc'];

$imgUrl='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect fill="%23f3f4f6" width="120" height="120"/><text fill="%239ca3af" font-family="sans-serif" font-size="11" x="50%25" y="50%25" text-anchor="middle" dy=".3em">Image</text></svg>';
if($eq){ if(!empty($eq['image'])) $imgUrl='/projet web/Assets/images/'.htmlspecialchars($eq['image']); elseif(isset($imagesDemo[$eq['reference']])) $imgUrl=$imagesDemo[$eq['reference']]; elseif(isset($imagesCat[$eq['categorie']])) $imgUrl=$imagesCat[$eq['categorie']]; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservation<?= $eq ? ' - '.htmlspecialchars($eq['nom']) : '' ?> - MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet web/Assets/materiel.css"/>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">

<nav class="topnav">
  <a class="topnav-brand" href="/projet web/view/Frontoffice/catalogue.php">MediFlow Rental</a>
  <div class="topnav-links">
    <a href="/projet web/view/Frontoffice/catalogue.php">Catalog</a>
    <a href="#">Support</a>
    <a href="#" class="active">My Rentals</a>
  </div>
  <div class="topnav-actions">
    <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
    <button class="icon-btn"><span class="material-symbols-outlined">shopping_cart</span></button>
    <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
      <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
    </div>
  </div>
</nav>

<?php if ($erreur): ?>
<main style="flex:1;padding-top:80px;display:flex;align-items:center;justify-content:center;">
  <div style="text-align:center;max-width:480px;padding:40px;">
    <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
      <span class="material-symbols-outlined" style="font-size:32px;color:#dc2626;">error_outline</span>
    </div>
    <h2 style="font-family:'Manrope',sans-serif;font-size:22px;color:#111827;margin-bottom:10px;">Équipement introuvable</h2>
    <p style="color:#6b7280;font-size:14px;margin-bottom:24px;"><?= htmlspecialchars($erreur) ?></p>
    <a href="/projet web/view/Frontoffice/catalogue.php" style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#1a56db;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:14px;">
      <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span>Retour au Catalogue
    </a>
  </div>
</main>

<?php else: ?>

<main style="flex:1;padding-top:56px;">
  <div class="page-wrap">

    <div class="page-header">
      <h1>Réservation d'équipement</h1>
      <p>Configurez votre location en quelques étapes simples pour garantir un soin optimal à domicile.</p>
    </div>

    <div class="stepper">
      <div class="step"><div class="step-circle active">1</div><span class="step-label active">Configuration</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">2</div><span class="step-label inactive">Livraison</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">3</div><span class="step-label inactive">Validation</span></div>
    </div>

    <div class="content-grid">
      <div class="left-col">

        <div class="equip-card">
          <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($eq['nom']) ?>"
               style="width:110px;height:110px;object-fit:contain;border-radius:8px;background:#f3f4f6;padding:8px;flex-shrink:0;" loading="lazy"/>
          <div class="equip-info">
            <span class="ref">Réf: <?= htmlspecialchars($eq['reference']) ?></span>
            <h2><?= htmlspecialchars($eq['nom']) ?></h2>
            <p class="desc"><?= htmlspecialchars($eq['categorie']) ?></p>
            <div class="price" id="daily-rate"
                 data-rate-eur="<?= htmlspecialchars($eq['prix_jour']) ?>"
                 data-rate-dt="<?= $prixDT ?>"
                 data-taux="<?= EUR_TO_DT ?>">
              <?= $prixDTFmt ?> DT / jour
              <small style="color:#9ca3af;font-size:10px;display:block;margin-top:2px;">
                (<?= number_format((float)$eq['prix_jour'], 2, ',', '.') ?> € × <?= EUR_TO_DT ?>)
              </small>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════
             FORMULAIRE — PAS DE required HTML
             La validation est gérée par materiel.js
             via validerFormulaireReservation()
        ════════════════════════════════════════════ -->
        <div class="form-card">

          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">calendar_today</span>
              <h3>Période de location</h3>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="date-start">Date de début <span style="color:#dc2626;">*</span></label>
                <!-- Pas de required — validé par JS -->
                <input class="form-input" id="date-start" type="date" min="<?= date('Y-m-d') ?>"/>
              </div>
              <div class="form-group">
                <label for="date-end">Date de fin (estimée) <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="date-end" type="date" min="<?= date('Y-m-d') ?>"/>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">local_shipping</span>
              <h3>Options de livraison</h3>
            </div>
            <div class="delivery-grid">
              <label class="delivery-opt selected">
                <input type="radio" name="delivery" value="livraison" checked/>
                <div class="opt-text">
                  <span class="opt-title blue">Livraison &amp; Installation</span>
                  <span class="opt-sub">À domicile par nos techniciens (Inclus)</span>
                </div>
                <span class="material-symbols-outlined opt-icon filled">check_circle</span>
              </label>
              <label class="delivery-opt unselected">
                <input type="radio" name="delivery" value="retrait"/>
                <div class="opt-text">
                  <span class="opt-title gray">Retrait en clinique</span>
                  <span class="opt-sub">Disponible sous 24h (Gratuit)</span>
                </div>
                <span class="material-symbols-outlined opt-icon unfilled">radio_button_unchecked</span>
              </label>
            </div>
          </div>

          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">person</span>
              <h3>Informations de contact</h3>
            </div>
            <div class="form-row" style="margin-bottom:14px;">
              <div class="form-group">
                <label for="firstname">Prénom <span style="color:#dc2626;">*</span></label>
                <!-- Pas de required — validé par JS (regex lettres uniquement) -->
                <input class="form-input" id="firstname" type="text" placeholder="Mohamed"/>
              </div>
              <div class="form-group">
                <label for="lastname">Nom <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="lastname" type="text" placeholder="Ben Ali"/>
              </div>
            </div>
            <div class="form-row full">
              <div class="form-group">
                <label for="phone">Numéro de téléphone <span style="color:#6b7280;font-size:11px;">(optionnel)</span></label>
                <!-- Optionnel : validé uniquement si rempli (format tunisien 8 chiffres) -->
                <input class="form-input" id="phone" type="tel" placeholder="20 123 456"/>
              </div>
            </div>
          </div>

        </div>
      </div>

      <aside class="summary-card">
        <h3>Récapitulatif</h3>
        <input type="hidden" id="equipement_id" value="<?= (int)$eq['id'] ?>"/>

        <div class="summary-row" id="duration-row">
          <span class="lbl">Location</span>
          <span class="val">—</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Frais de livraison</span>
          <span class="val free">OFFERT</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Installation technique</span>
          <span class="val">0,000 DT</span>
        </div>
        <hr class="summary-divider"/>
        <div class="total-label">TOTAL TTC</div>
        <div class="total-amount" id="total-amount">—</div>

        <button class="btn-confirm" id="btn-confirm" type="button">
          Confirmer la réservation
        </button>

        <p class="confirm-legal">
          En confirmant, vous acceptez nos conditions générales de location médicale.
          Le paiement s'effectue après validation de votre dossier médical.
        </p>

        <div class="tip-card">
          <span class="material-symbols-outlined">info</span>
          <div class="tip-text">
            <span class="tip-title">Prise en charge CNAM</span>
            <span class="tip-body">Ce matériel est éligible au remboursement CNAM sous réserve de prescription médicale valide.</span>
          </div>
        </div>

        <div style="margin-top:14px;padding:10px;background:#f9fafb;border-radius:8px;text-align:center;">
          <small style="font-size:11px;color:#9ca3af;">Taux BCT : 1 EUR = <strong><?= EUR_TO_DT ?> DT</strong></small>
        </div>

        <a class="help-link" href="#">
          <span class="material-symbols-outlined">help</span>Besoin d'aide pour votre dossier ?
        </a>
      </aside>
    </div>
  </div>
</main>

<?php endif; ?>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-left">
      <span class="footer-brand">MediFlow</span>
      <div class="footer-sep"></div>
      <span class="footer-copy">© 2024 Clinical Sanctuary. Tous droits réservés.</span>
    </div>
    <div class="footer-links">
      <a href="#">Confidentialité</a>
      <a href="#">Conditions d'Utilisation</a>
      <a href="#">Contact</a>
    </div>
  </div>
</footer>

<div class="toast-container"></div>
<script src="/projet web/Assets/materiel.js"></script>
</body>
</html>