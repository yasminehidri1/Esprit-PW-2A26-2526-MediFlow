<?php
require_once __DIR__ . '/../../model/Equipement.php';
$model       = new Equipement();
$equipements = $model->getAll();

/**
 * getImageUrl()
 * Cherche dans Assets/images/equipements/EQ-XXXX.jpg
 */
function getImageUrl($eq) {
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $localPath = __DIR__ . '/../../Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
        if (file_exists($localPath)) {
            return '/projet%20web/Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
        }
    }
    if (!empty($eq['image'])) {
        return '/projet%20web/Assets/images/equipements/' . htmlspecialchars($eq['image']);
    }
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
  <link rel="stylesheet" href="/projet%20web/Assets/materiel.css"/>
</head>
<body>

<!-- ══ TOP NAV ══ -->
<nav class="topnav">
  <a class="topnav-brand" href="/projet%20web/view/Frontoffice/catalogue.php">MediFlow Rental</a>
  <div class="topnav-links">
    <a href="/projet%20web/view/Frontoffice/catalogue.php" class="active">Catalog</a>
    <a href="#">Support</a>
    <a href="/projet%20web/view/Frontoffice/mes-reservations.php">My Rentals</a>
  </div>
  <div class="topnav-actions">
    <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
    <!--  Panier → mes-reservations.php -->
    <a href="/projet%20web/view/Frontoffice/mes-reservations.php"
       class="icon-btn" title="Mes Réservations" style="text-decoration:none;">
      <span class="material-symbols-outlined">shopping_cart</span>
    </a>
    <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;
         display:flex;align-items:center;justify-content:center;">
      <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
    </div>
  </div>
</nav>

<div class="layout">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <div class="sb-brand">
      <div class="name">MediFlow</div>
      <div class="sub">Clinical Sanctuary</div>
    </div>
    <nav class="sb-nav">
      <a href="#"><span class="material-symbols-outlined">grid_view</span>Dashboard</a>
      <a href="/projet%20web/view/Frontoffice/catalogue.php" class="active">
        <span class="material-symbols-outlined">medical_services</span>Location Matériel
      </a>
      <a href="#"><span class="material-symbols-outlined">inventory_2</span>Orders</a>
      <a href="#"><span class="material-symbols-outlined">build</span>Maintenance</a>
      <a href="#"><span class="material-symbols-outlined">payments</span>Billing</a>
    </nav>
    <div class="sb-bottom">
      <div class="help-card">
        <p>Need Help?</p>
        <button type="button">Contact Expert</button>
      </div>
      <div class="sb-links">
        <a href="#"><span class="material-symbols-outlined">settings</span>Settings</a>
        <a href="#"><span class="material-symbols-outlined">logout</span>Logout</a>
      </div>
    </div>
  </aside>

  <!-- ══ MAIN ══ -->
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
      </div>
    </div>

    <!-- GRILLE PRODUITS -->
    <div class="product-grid" id="product-grid">

      <?php if (empty($equipements)): ?>
        <div class="empty-state">
          <span class="material-symbols-outlined">inventory_2</span>
          Aucun équipement disponible pour le moment.
        </div>

      <?php else: ?>
        <?php foreach ($equipements as $eq): ?>
          <?php
            // Badge statut
            $badgeClass = 'badge-available'; $badgeLabel = 'DISPONIBLE';
            if ($eq['statut'] === 'loue')        { $badgeClass = 'badge-demand';      $badgeLabel = 'LOUÉ'; }
            if ($eq['statut'] === 'maintenance') { $badgeClass = 'badge-maintenance'; $badgeLabel = 'MAINTENANCE'; }

            // Filtre catégorie
            $catFilter = strtolower(str_replace(
              ['é','è','ê','à','â','î','ô','û','ç'],
              ['e','e','e','a','a','i','o','u','c'],
              $eq['categorie']
            ));

            // Image
            $imgUrl = getImageUrl($eq);

            //  Prix affiché directement en DT (même logique que equipements.php)
            // Pas de multiplication × 3.4052
            $prixDT = number_format((float)$eq['prix_jour'], 3, ',', '.');

            // URL réservation
            $urlReservation = '/projet%20web/view/Frontoffice/reservation.php?id=' . (int)$eq['id'];
          ?>

          <div class="product-card" data-category="<?= $catFilter ?>" data-id="<?= $eq['id'] ?>">
            <div class="card-img">
              <img src="<?= $imgUrl ?>"
                   alt="<?= htmlspecialchars($eq['nom']) ?>"
                   loading="lazy"
                   onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22160%22><rect fill=%22%23f3f4f6%22 width=%22200%22 height=%22160%22/><text fill=%22%239ca3af%22 font-family=%22sans-serif%22 font-size=%2213%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>Image non disponible</text></svg>'"/>
              <span class="badge-pill <?= $badgeClass ?>"><?= $badgeLabel ?></span>
            </div>
            <div class="card-body">
              <div class="card-title-row">
                <h3 class="card-title"><?= htmlspecialchars($eq['nom']) ?></h3>
                <!--  Prix en DT directement -->
                <div class="card-price"><?= $prixDT ?><span class="unit"> DT/j</span></div>
              </div>
              <p class="card-desc"><?= htmlspecialchars($eq['categorie']) ?></p>
              <div class="card-actions">

                <?php if ($eq['statut'] === 'disponible'): ?>
                  <a class="btn-reserve" href="<?= $urlReservation ?>">
                    <span class="material-symbols-outlined">calendar_today</span>
                    Réserver
                  </a>
                <?php else: ?>
                  <button class="btn-reserve" type="button" disabled
                          style="opacity:.5;cursor:not-allowed;background:#9ca3af;">
                    <span class="material-symbols-outlined">block</span>
                    Indisponible
                  </button>
                <?php endif; ?>

                <button class="btn-history" type="button" title="Historique"
                        onclick="showEquipHistory(<?= (int)$eq['id'] ?>, '<?= htmlspecialchars($eq['nom'], ENT_QUOTES) ?>')">
                  <span class="material-symbols-outlined">history</span>
                </button>

              </div>
            </div>
          </div>

        <?php endforeach; ?>

        <!-- Bento Banner -->
        <div class="bento-banner" style="grid-column: span 2;">
          <div>
            <h2>Besoin d'un pack<br>complet ?</h2>
            <p>Nous proposons des solutions sur-mesure pour les hospitalisations à domicile.
               Bénéficiez de 15% de réduction sur les locations combinées.</p>
            <button class="btn-offers" type="button">Consulter les Offres</button>
          </div>
          <div class="banner-icon">
            <span class="material-symbols-outlined">medical_information</span>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </main>
</div>

<!-- FAB -->
<button class="fab" title="Chat support" type="button">
  <span class="material-symbols-outlined">chat_bubble</span>
</button>

<div class="toast-container"></div>
<script src="/projet%20web/Assets/materiel.js"></script>
</body>
</html>