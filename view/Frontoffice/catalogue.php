<?php
require_once __DIR__ . '/../../model/Equipement.php';
$model       = new Equipement();
$equipements = $model->getAll();

/* Taux BCT : 1 EUR = 3.4052 DT */
define('EUR_TO_DT', 3.4052);

$imagesDemo = [
    'EQ-9402' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
    'EQ-1108' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'EQ-7721' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuASF4CH3yRiFxmEjcycHQjg-OUbYQT77I53FBZzckUXpa2EtPtmunb7ZRxYISNkrLGEAWuscNqpec_8frZZpIDJvKZDDSwEqBnV4W29wv4-vrFh33FIYb14bKr4O-MFxWuUIBprgX7SHhODjGHfvZ7RKlWLREZY-t6I2wvxjWPNtP-01AY8eTMyBhGdsUImxYLwhsDe_y1h-cpUs8pL9OiEHV7pzOGu9gk53SNUQEbzNvGRygDOgsp7fXUzZfgOItBUyNUZsEwmKTU',
    'EQ-2256' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
    'EQ-3310' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'EQ-4401' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuD7Grmehl5yETNWyJDc-A9yRVrtJLksaZyf4sJ7joP2sjKPeU2cmKryKdP5tXghMptmSFPOx0QWe7gGuz16Kc7FzTT6_A5IslIW-sQRKX9Fhp2OtfOXUKo1LcRtW1AvYoWfEuMQm2UlLBGS3IwcgBl9d85kI0Lm8Qh6ixkLF51ZMCjLgV5LPCKJmGvvgsoS8PSbfRX73L_8MU9oovtKjftr_y0f_vBSFVo74P3d_qEL-JfA97pVTJTXk2s_9_NpMzVGD5VygLgiHE0',
];
$imagesCat = [
    'Mobilité'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuAUWAqVVgdD68nfBWg0dXKwdLWpnTlMCKLy44AgjzeTUuXyMkd6oReRSckFR3xIVCZUKdxF2Lk0X7-2m-AtWVEOImdMZ_rT5jDf4eKgzPSExAeV1ifKVSdtJHTQL9FJH0S5IjCpNyKzodadcfJ7aOJi1CaQP-wo_Nnj6IrbILxue4IOtdG6QMh4Z6fE0ahwu4m1NLaQL8ofVf38sA7xDv4dd7U_VKlBBqUxmcjv4dpiNgiHzj3Oc2uOVVXBMqKelsq37dejHb8_izA',
    'Gériatrie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuASF4CH3yRiFxmEjcycHQjg-OUbYQT77I53FBZzckUXpa2EtPtmunb7ZRxYISNkrLGEAWuscNqpec_8frZZpIDJvKZDDSwEqBnV4W29wv4-vrFh33FIYb14bKr4O-MFxWuUIBprgX7SHhODjGHfvZ7RKlWLREZY-t6I2wvxjWPNtP-01AY8eTMyBhGdsUImxYLwhsDe_y1h-cpUs8pL9OiEHV7pzOGu9gk53SNUQEbzNvGRygDOgsp7fXUzZfgOItBUyNUZsEwmKTU',
    'Respiratoire'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'Réanimation'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDlAeD12gHGgF_WxdKdWCnAZXSa9xCQL-rYTpvRvK4k6HAj06HWJEOxowr9iomXwCGx6lNc_S2xAsYu_i3eIOH6yYrJBv0eeCCNdBBqYCiAcmUtc-biVseOzUOZ4t5zxFwZrS-ywCcShu2brIZGbji1vYKjVm6pg1g0AELad-YvcnIAleTOUu9EKrzOwdB8YPuoSl7T5wMXNpx-khvlEJW62YT6eFaIFWxzcHzQIfNI2fbcveL1j75vxjT2vATgdsbvtLSskq7nVzI',
    'Radiologie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
    'Cardiologie'=>'https://lh3.googleusercontent.com/aida-public/AB6AXuDT2N9gClr4mS8O2pP-7dPsIr6ltmDKPzk7JVJMz2xoTa0cLG_8-VDEFjvc9BiuwT4LuxmFCvGRV2KCwuA6qq6DybcN3KmFeHe06k5n_xg9-84Rm0hb2N6oS6uFvWqSBnmNtExc9CqZ6pJwwuNo1LDQoNmF2V247d8vi_ET5yj6nYVkmYebvGSqvVcMq-A9VjY6dNWt3V6feLI6-ofdo04hKPb1eKNxL4C0yRSOZe0ZE9nALh8x8nBE_9R1hLcDF2MPOEbrMREYiEc',
];

function getImageUrl($eq, $imagesDemo, $imagesCat) {
    if (!empty($eq['image']))                     return '/projet web/Assets/images/' . htmlspecialchars($eq['image']);
    if (isset($imagesDemo[$eq['reference']]))     return $imagesDemo[$eq['reference']];
    if (isset($imagesCat[$eq['categorie']]))      return $imagesCat[$eq['categorie']];
    return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="160"><rect fill="%23f3f4f6" width="200" height="160"/><text fill="%239ca3af" font-family="sans-serif" font-size="13" x="50%25" y="50%25" text-anchor="middle" dy=".3em">Image non disponible</text></svg>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MediFlow Rental - Catalogue</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet web/Assets/materiel.css"/>
</head>
<body>

<nav class="topnav">
  <a class="topnav-brand" href="/projet web/view/Frontoffice/catalogue.php">MediFlow Rental</a>
  <div class="topnav-links">
    <a href="/projet web/view/Frontoffice/catalogue.php" class="active">Catalog</a>
    <a href="#">Support</a>
    <a href="#">My Rentals</a>
  </div>
  <div class="topnav-actions">
    <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
    <button class="icon-btn"><span class="material-symbols-outlined">shopping_cart</span></button>
    <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
      <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
    </div>
  </div>
</nav>

<div class="layout">
  <aside class="sidebar">
    <div class="sb-brand"><div class="name">MediFlow</div><div class="sub">Clinical Sanctuary</div></div>
    <nav class="sb-nav">
      <a href="#"><span class="material-symbols-outlined">grid_view</span>Dashboard</a>
      <a href="/projet web/view/Frontoffice/catalogue.php" class="active"><span class="material-symbols-outlined">medical_services</span>Location Matériel</a>
      <a href="#"><span class="material-symbols-outlined">inventory_2</span>Orders</a>
      <a href="#"><span class="material-symbols-outlined">build</span>Maintenance</a>
      <a href="#"><span class="material-symbols-outlined">payments</span>Billing</a>
    </nav>
    <div class="sb-bottom">
      <div class="help-card"><p>Need Help?</p><button type="button">Contact Expert</button></div>
      <div class="sb-links">
        <a href="#"><span class="material-symbols-outlined">settings</span>Settings</a>
        <a href="#"><span class="material-symbols-outlined">logout</span>Logout</a>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="page-top">
      <div>
        <h1>Catalogue de<br>Location</h1>
        <p>Parcourez notre sélection d'équipements médicaux certifiés pour un accompagnement clinique optimal à domicile.</p>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
        <div class="filter-bar">
          <button class="active" data-filter="all">Tout</button>
          <button data-filter="mobilite">Mobilité</button>
          <button data-filter="respiratoire">Respiratoire</button>
        </div>
        <small style="color:#9ca3af;font-size:11px;">Taux BCT : 1 EUR = <?= EUR_TO_DT ?> DT</small>
      </div>
    </div>

    <div class="product-grid" id="product-grid">
      <?php if (empty($equipements)): ?>
        <div class="empty-state">
          <span class="material-symbols-outlined">inventory_2</span>
          Aucun équipement disponible pour le moment.
        </div>
      <?php else: ?>
        <?php foreach ($equipements as $eq): ?>
          <?php
            $badgeClass = 'badge-available'; $badgeLabel = 'DISPONIBLE';
            if ($eq['statut'] === 'loue')        { $badgeClass = 'badge-demand';      $badgeLabel = 'LOUÉ'; }
            if ($eq['statut'] === 'maintenance') { $badgeClass = 'badge-maintenance'; $badgeLabel = 'MAINTENANCE'; }
            $catFilter = strtolower(str_replace(['é','è','ê','à','â','î','ô','û','ç'],['e','e','e','a','a','i','o','u','c'],$eq['categorie']));
            $imgUrl    = getImageUrl($eq, $imagesDemo, $imagesCat);
            $prixDT    = number_format((float)$eq['prix_jour'] * EUR_TO_DT, 3, ',', '.');
          ?>
          <div class="product-card" data-category="<?= $catFilter ?>" data-id="<?= $eq['id'] ?>">
            <div class="card-img">
              <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($eq['nom']) ?>" loading="lazy"/>
              <span class="badge-pill <?= $badgeClass ?>"><?= $badgeLabel ?></span>
            </div>
            <div class="card-body">
              <div class="card-title-row">
                <h3 class="card-title"><?= htmlspecialchars($eq['nom']) ?></h3>
                <div class="card-price"><?= $prixDT ?><span class="unit"> DT/j</span></div>
              </div>
              <p class="card-desc"><?= htmlspecialchars($eq['categorie']) ?></p>
              <div class="card-actions">
                <?php if ($eq['statut'] === 'disponible'): ?>
                  <a class="btn-reserve" href="/projet web/view/Frontoffice/reservation.php?id=<?= (int)$eq['id'] ?>">
                    <span class="material-symbols-outlined">calendar_today</span>Réserver
                  </a>
                <?php else: ?>
                  <button class="btn-reserve" disabled style="opacity:.5;cursor:not-allowed;background:#9ca3af;">
                    <span class="material-symbols-outlined">block</span>Indisponible
                  </button>
                <?php endif; ?>
                <button class="btn-history" title="Historique" onclick="showEquipHistory(<?= (int)$eq['id'] ?>, '<?= htmlspecialchars($eq['nom'], ENT_QUOTES) ?>')">
                  <span class="material-symbols-outlined">history</span>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="bento-banner" style="grid-column: span 2;">
          <div>
            <h2>Besoin d'un pack<br>complet ?</h2>
            <p>Nous proposons des solutions sur-mesure pour les hospitalisations à domicile. Bénéficiez de 15% de réduction sur les locations combinées.</p>
            <button class="btn-offers" type="button">Consulter les Offres</button>
          </div>
          <div class="banner-icon"><span class="material-symbols-outlined">medical_information</span></div>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<button class="fab" title="Chat support"><span class="material-symbols-outlined">chat_bubble</span></button>
<div class="toast-container"></div>
<script src="/projet web/Assets/materiel.js"></script>
</body>
</html>