<?php
/**
 * Equipment Catalogue — Patient View
 * @package MediFlow\Views\Front
 */

function getImageUrl($eq): string {
    $bases = [
        __DIR__ . '/../../assets/images/equipements/',
        __DIR__ . '/../../Assets/images/equipements/',
    ];
    $exts = ['jpg','jpeg','png','webp'];
    foreach ($bases as $base) {
        foreach ($exts as $ext) {
            if (file_exists($base . $eq['reference'] . '.' . $ext)) {
                return '/integration/assets/images/equipements/' . $eq['reference'] . '.' . $ext;
            }
        }
    }
    if (!empty($eq['image'])) {
        return '/integration/assets/images/equipements/' . htmlspecialchars($eq['image']);
    }
    return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="160"><rect fill="%23f3f4f6" width="200" height="160"/><text fill="%239ca3af" font-family="sans-serif" font-size="13" x="50%" y="50%" text-anchor="middle" dy=".3em">No image</text></svg>';
}

$user = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
?>
<style>
  /* ── Product grid ── */
  .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:20px; }
  .product-card { background:#fff; border-radius:14px; border:1px solid #e8eaf0; overflow:hidden; transition:box-shadow .2s,transform .2s; display:flex; flex-direction:column; }
  .product-card:hover { box-shadow:0 8px 32px rgba(0,77,153,.10); transform:translateY(-2px); }
  .card-img { width:100%; height:160px; background:#f5f7fa; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; }
  .card-img img { width:100%; height:100%; object-fit:contain; padding:12px; }
  .badge-pill { position:absolute; top:10px; right:10px; padding:3px 10px; border-radius:20px; font-size:10.5px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
  .badge-available   { background:#dcfce7; color:#16a34a; }
  .badge-demand      { background:#fef3c7; color:#b45309; }
  .badge-maintenance { background:#fee2e2; color:#dc2626; }
  .card-body { padding:16px; display:flex; flex-direction:column; flex:1; }
  .card-title-row { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:4px; }
  .card-title { font-family:'Manrope',sans-serif; font-size:15px; font-weight:800; color:#111827; }
  .card-price { font-size:14px; font-weight:700; color:#004d99; white-space:nowrap; }
  .card-price .unit { font-size:11px; font-weight:500; color:#6b7280; }
  .card-desc { font-size:12px; color:#9ca3af; margin-bottom:14px; flex:1; }
  .card-actions { display:flex; gap:8px; margin-top:auto; }

  .btn-reserve { display:flex; align-items:center; gap:5px; padding:8px 16px; border-radius:8px; background:#004d99; color:#fff; font-size:13px; font-weight:700; font-family:'Inter',sans-serif; text-decoration:none; border:none; cursor:pointer; transition:background .18s; flex:1; justify-content:center; }
  .btn-reserve:hover { background:#00357a; }
  .btn-reserve[disabled] { opacity:.5; cursor:not-allowed; background:#9ca3af; }

  /* ✅ Bouton œil */
  .btn-eye-card {
    width: 38px; height: 38px;
    border-radius: 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #004d99;
    flex-shrink: 0;
    transition: background .18s, transform .15s;
  }
  .btn-eye-card:hover { background: #dbeafe; transform: scale(1.08); }
  .btn-eye-card .material-symbols-outlined { font-size: 17px; }

  .btn-filter { padding:7px 14px; border-radius:8px; background:#f3f4f6; border:1px solid #e5e7eb; font-size:12.5px; font-weight:600; color:#6b7280; cursor:pointer; font-family:'Inter',sans-serif; transition:all .18s; }
  .btn-filter.active, .btn-filter:hover { background:#004d99; color:#fff; border-color:#004d99; }
  .empty-state { grid-column:1/-1; text-align:center; padding:80px 20px; color:#9ca3af; }
  .empty-state .material-symbols-outlined { font-size:56px; display:block; margin-bottom:16px; color:#d1d5db; }
  .bento-banner { background:linear-gradient(135deg,#004d99 0%,#1565c0 60%,#005851 100%); border-radius:14px; padding:28px; display:flex; align-items:center; justify-content:space-between; color:#fff; }
  .bento-banner h2 { font-family:'Manrope',sans-serif; font-size:22px; font-weight:900; margin-bottom:6px; }
  .bento-banner p  { font-size:13px; opacity:.85; max-width:380px; }
  .bento-banner .btn-offers { margin-top:14px; display:inline-block; padding:9px 22px; background:rgba(255,255,255,.2); border:1.5px solid rgba(255,255,255,.4); border-radius:8px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; font-family:'Inter',sans-serif; transition:background .18s; }
  .bento-banner .btn-offers:hover { background:rgba(255,255,255,.3); }
  .banner-icon .material-symbols-outlined { font-size:72px; opacity:.25; }

  /* ✅ Modale détail équipement */
  .modal-equip-overlay {
    display: none !important; position: fixed !important;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,.5); z-index: 999999 !important;
    align-items: center; justify-content: center;
  }
  .modal-equip-overlay.open { display: flex !important; }
  .modal-equip-box {
    background: #fff; border-radius: 20px;
    width: 520px; max-width: 95vw; max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 24px 70px rgba(0,0,0,.22);
    animation: equipIn .22s ease;
  }
  @keyframes equipIn {
    from { opacity:0; transform: translateY(-18px) scale(.96); }
    to   { opacity:1; transform: translateY(0) scale(1); }
  }
  .modal-equip-header {
    background: linear-gradient(135deg, #004d99, #1565c0);
    padding: 22px 26px;
    display: flex; align-items: flex-start; justify-content: space-between;
  }
  .modal-equip-header h2 { font-family:'Manrope',sans-serif; font-size:19px; font-weight:900; color:#fff; margin:0 0 3px; }
  .modal-equip-header p  { font-size:12px; color:rgba(255,255,255,.72); margin:0; }
  .modal-equip-close {
    width:30px; height:30px; border-radius:7px;
    background:rgba(255,255,255,.18); border:none;
    cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff;
    flex-shrink:0;
  }
  .modal-equip-close:hover { background:rgba(255,255,255,.3); }
  .modal-equip-close .material-symbols-outlined { font-size:17px; }
  .modal-equip-body { padding:22px 26px; overflow-y:auto; max-height:calc(90vh - 110px); }

  /* Card équipement dans modale */
  .equip-mini-card {
    display:flex; gap:14px; align-items:center;
    background:#f0f6ff; border:1px solid #dbeafe;
    border-radius:12px; padding:14px; margin-bottom:18px;
  }
  .equip-mini-img {
    width:70px; height:70px; object-fit:contain;
    border-radius:9px; background:#fff; padding:5px; flex-shrink:0;
  }
  .equip-mini-img-ph {
    width:70px; height:70px; border-radius:9px; background:#fff; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
  }
  .equip-mini-img-ph .material-symbols-outlined { font-size:28px; color:#9ca3af; }

  /* Lignes de détail */
  .eq-detail-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:9px 0; border-bottom:1px solid #f3f4f6;
  }
  .eq-detail-row:last-child { border-bottom:none; }
  .eq-detail-lbl { font-size:13px; color:#6b7280; display:flex; align-items:center; gap:5px; }
  .eq-detail-lbl .material-symbols-outlined { font-size:14px; color:#9ca3af; }
  .eq-detail-val { font-size:13px; font-weight:600; color:#111827; }

  /* Prix total highlight */
  .eq-prix-highlight {
    background:linear-gradient(135deg,#eff6ff,#f0f9ff);
    border:1px solid #bfdbfe; border-radius:11px;
    padding:14px 18px;
    display:flex; align-items:center; justify-content:space-between;
    margin:16px 0;
  }
  .eq-prix-highlight .lbl { font-size:13px; color:#1d4ed8; font-weight:600; }
  .eq-prix-highlight .val { font-family:'Manrope',sans-serif; font-size:22px; font-weight:900; color:#004d99; }
</style>

<div class="pt-24 pb-12 px-10 space-y-8">

  <!-- Page header -->
  <section>
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent">
      Catalogue de Location
    </h2>
    <p class="text-on-surface-variant mt-1 font-medium">Certified medical equipment for home care.</p>
  </section>

  <!-- Filters -->
  <div class="flex flex-wrap gap-3 items-center">
    <button class="btn-filter active" data-filter="all">All</button>
    <button class="btn-filter" data-filter="mobilite">Mobility</button>
    <button class="btn-filter" data-filter="respiratoire">Respiratory</button>
    <button class="btn-filter" data-filter="autre">Other</button>
  </div>

  <!-- Product grid -->
  <section>
    <div class="product-grid" id="product-grid">

      <?php if (empty($equipements)): ?>
        <div class="empty-state">
          <span class="material-symbols-outlined">inventory_2</span>
          <p class="font-bold text-lg text-slate-700 mb-2">No equipment available</p>
          <p class="text-sm">Check back soon for available rentals.</p>
        </div>
      <?php else: ?>

        <?php foreach ($equipements as $eq):
          $badgeClass = 'badge-available'; $badgeLabel = 'AVAILABLE';
          if ($eq['statut'] === 'loue')        { $badgeClass = 'badge-demand';      $badgeLabel = 'RENTED'; }
          if ($eq['statut'] === 'maintenance') { $badgeClass = 'badge-maintenance'; $badgeLabel = 'MAINTENANCE'; }

          $catFilter = strtolower(str_replace(
            ['é','è','ê','à','â','î','ô','û','ç'],
            ['e','e','e','a','a','i','o','u','c'],
            $eq['categorie']
          ));

          $imgUrl = getImageUrl($eq);
          $prixDT = number_format((float)$eq['prix_jour'], 3, ',', '.');
          $urlRes = '/integration/reservation?id=' . (int)$eq['id'];

          // ✅ JSON pour data-attribute (pas de htmlspecialchars qui casse le JSON)
          $eqJson = json_encode($eq, JSON_HEX_QUOT | JSON_HEX_APOS);
        ?>

        <div class="product-card" data-category="<?= $catFilter ?>" data-id="<?= $eq['id'] ?>">
          <div class="card-img">
            <img src="<?= $imgUrl ?>"
                 alt="<?= htmlspecialchars($eq['nom']) ?>"
                 loading="lazy"
                 onerror="this.style.display='none'"/>
            <span class="badge-pill <?= $badgeClass ?>"><?= $badgeLabel ?></span>
          </div>
          <div class="card-body">
            <div class="card-title-row">
              <h3 class="card-title"><?= htmlspecialchars($eq['nom']) ?></h3>
              <div class="card-price"><?= $prixDT ?><span class="unit"> DT/d</span></div>
            </div>
            <p class="card-desc"><?= htmlspecialchars($eq['categorie']) ?> &mdash; Réf: <?= htmlspecialchars($eq['reference']) ?></p>
            <div class="card-actions">

              <?php if ($eq['statut'] === 'disponible'): ?>
                <a class="btn-reserve" href="<?= $urlRes ?>">
                  <span class="material-symbols-outlined" style="font-size:15px;">calendar_today</span>
                  Reserve
                </a>
              <?php else: ?>
                <button class="btn-reserve" type="button" disabled>
                  <span class="material-symbols-outlined" style="font-size:15px;">block</span>
                  Unavailable
                </button>
              <?php endif; ?>

              <!-- ✅ Bouton œil — data-eq évite les problèmes de quotes -->
              <button class="btn-eye-card"
                      type="button"
                      title="Voir le détail"
                      data-eq='<?= $eqJson ?>'>
                <span class="material-symbols-outlined">visibility</span>
              </button>

            </div>
          </div>
        </div>

        <?php endforeach; ?>

        <!-- Promo Banner -->
        <div class="bento-banner" style="grid-column:span 2;">
          <div>
            <h2>Need a complete pack?</h2>
            <p>We offer custom solutions for home hospitalization. Get 15% off on combined rentals.</p>
            <button class="btn-offers" type="button">View Offers</button>
          </div>
          <div class="banner-icon">
            <span class="material-symbols-outlined">medical_information</span>
          </div>
        </div>

      <?php endif; ?>
    </div>

    <!-- ✅ PAGINATION -->
    <div id="pagination-catalogue"></div>

  </section>
</div>

<!-- ════════════════════════════════════════════════
     ✅ MODALE DÉTAIL ÉQUIPEMENT
     S'ouvre quand l'admin clique sur l'œil 👁️
════════════════════════════════════════════════ -->
<div id="modal-equip" class="modal-equip-overlay">
  <div class="modal-equip-box">

    <!-- Header gradient -->
    <div class="modal-equip-header">
      <div>
        <h2 id="eq-modal-nom">Détail équipement</h2>
        <p id="eq-modal-ref">Réf. —</p>
      </div>
      <button class="modal-equip-close" onclick="fermerDetailEquip()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Corps -->
    <div class="modal-equip-body">

      <!-- Mini card équipement -->
      <div class="equip-mini-card">
        <div id="eq-img-container"></div>
        <div>
          <div id="eq-modal-cat" style="font-size:11px;color:#9ca3af;margin-bottom:3px;"></div>
          <div id="eq-modal-nom2" style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;margin-bottom:4px;"></div>
          <div id="eq-modal-badge"></div>
        </div>
      </div>

      <!-- ✅ Description IA -->
      <div id="eq-modal-desc-wrap" style="background:#fff;border:1px solid #e8eaf0;border-radius:10px;padding:12px 14px;margin-bottom:14px;display:none;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
          <span class="material-symbols-outlined" style="font-size:15px;color:#004d99;">auto_awesome</span>
          <span style="font-size:11px;font-weight:700;color:#004d99;text-transform:uppercase;letter-spacing:.06em;">Description IA</span>
        </div>
        <p id="eq-modal-desc" style="font-size:13px;color:#374151;line-height:1.6;margin:0;"></p>
      </div>

      <!-- Lignes de détail -->
      <div class="eq-detail-row">
        <span class="eq-detail-lbl">
          <span class="material-symbols-outlined">tag</span>Référence
        </span>
        <span class="eq-detail-val" id="eq-modal-ref2" style="color:#0ea5e9;"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl">
          <span class="material-symbols-outlined">category</span>Catégorie
        </span>
        <span class="eq-detail-val" id="eq-modal-cat2"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl">
          <span class="material-symbols-outlined">info</span>Statut
        </span>
        <span class="eq-detail-val" id="eq-modal-statut"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl">
          <span class="material-symbols-outlined">payments</span>Prix / jour
        </span>
        <span class="eq-detail-val" id="eq-modal-prix" style="color:#004d99;font-size:15px;"></span>
      </div>

      <!-- Prix highlight -->
      <div class="eq-prix-highlight">
        <span class="lbl">Tarif journalier</span>
        <span class="val" id="eq-modal-prix2"></span>
      </div>

      <!-- Bouton réserver -->
      <a id="eq-modal-btn-res" href="#"
         style="display:flex;align-items:center;justify-content:center;gap:8px;
                padding:12px;background:#004d99;color:#fff;border-radius:10px;
                text-decoration:none;font-weight:700;font-size:14px;font-family:'Inter',sans-serif;
                transition:background .18s;">
        <span class="material-symbols-outlined" style="font-size:17px;">calendar_today</span>
        Réserver cet équipement
      </a>
      <p style="font-size:11px;color:#9ca3af;text-align:center;margin-top:10px;">
        Disponibilité vérifiée en temps réel avant confirmation.
      </p>

    </div>
  </div>
</div>

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>

<script>
(function() {
  function init() {

/* ── Boutons œil — event delegation via data-eq ── */
document.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-eq]');
  if (!btn) {
    // Clic sur l'icône visibility à l'intérieur du bouton
    const parent = e.target.closest('.btn-eye-card');
    if (parent) {
      const raw = parent.getAttribute('data-eq');
      if (raw) {
        try { ouvrirDetailEquip(JSON.parse(raw)); } catch(err) { console.error(err); }
      }
    }
    return;
  }
  const raw = btn.getAttribute('data-eq');
  if (!raw) return;
  try {
    const eq = JSON.parse(raw);
    ouvrirDetailEquip(eq);
  } catch(err) {
    console.error('Erreur parsing data-eq:', err);
  }
});

/* ════════════════════════════════════════
   PAGINATION — 6 cartes par page
════════════════════════════════════════ */
const ITEMS_PAR_PAGE = 3;
let pageActuelle = 1;
let filtreActuel = 'all';

window.getCartesVisibles = function() {
  return [...document.querySelectorAll('.product-card')].filter(c => {
    const cat = c.dataset.category || '';
    return filtreActuel === 'all' || cat.includes(filtreActuel);
  });
}

window.afficherPage = function(page) {
  pageActuelle = page;
  const cartes = getCartesVisibles();
  const total  = cartes.length;
  const pages  = Math.ceil(total / ITEMS_PAR_PAGE);
  const debut  = (page - 1) * ITEMS_PAR_PAGE;
  const fin    = debut + ITEMS_PAR_PAGE;

  // Cacher toutes les cartes d'abord
  document.querySelectorAll('.product-card').forEach(c => c.style.display = 'none');

  // Afficher seulement celles de la page courante
  cartes.forEach((c, i) => {
    c.style.display = (i >= debut && i < fin) ? '' : 'none';
  });

  // Mettre à jour la pagination UI
  renderPagination(page, pages, total);
}

window.renderPagination = function(page, pages, total) {
  const container = document.getElementById('pagination-catalogue');
  if (!container) return;

  if (pages <= 1) { container.innerHTML = ''; return; }

  const debut = (page - 1) * ITEMS_PAR_PAGE + 1;
  const fin   = Math.min(page * ITEMS_PAR_PAGE, total);

  // ✅ Pagination FIXE en bas — toujours visible sans scroller
  container.innerHTML = `
    <div style="display:flex;justify-content:flex-end;align-items:center;gap:8px;
                padding:16px 0;margin-top:12px;">
      <button onclick="afficherPage(${page - 1})"
              ${page <= 1 ? 'disabled' : ''}
              style="padding:9px 20px;border-radius:9px;
                     border:1.5px solid ${page<=1?'#e5e7eb':'#004d99'};
                     background:${page<=1?'#f9fafb':'#fff'};
                     color:${page<=1?'#9ca3af':'#004d99'};
                     font-size:13.5px;font-weight:700;
                     cursor:${page<=1?'not-allowed':'pointer'};
                     font-family:'Inter',sans-serif;">
        ← Précédent
      </button>
      ${Array.from({length:pages},(_,i)=>i+1).map(p=>`
        <button onclick="afficherPage(${p})"
                style="width:36px;height:36px;border-radius:9px;
                       border:${p===page?'none':'1.5px solid #e5e7eb'};
                       background:${p===page?'#004d99':'#fff'};
                       color:${p===page?'#fff':'#374151'};
                       font-size:13px;font-weight:700;cursor:pointer;
                       font-family:'Inter',sans-serif;">
          ${p}
        </button>`).join('')}
      <button onclick="afficherPage(${page + 1})"
              ${page >= pages ? 'disabled' : ''}
              style="padding:9px 20px;border-radius:9px;
                     border:1.5px solid transparent;
                     background:${page>=pages?'#f9fafb':'#004d99'};
                     color:${page>=pages?'#9ca3af':'#fff'};
                     font-size:13.5px;font-weight:700;
                     cursor:${page>=pages?'not-allowed':'pointer'};
                     font-family:'Inter',sans-serif;">
        Suivant →
      </button>
    </div>
  `;
  // Espace en bas pour que le contenu ne soit pas caché par la barre fixe
}

/* ── Category filter ── */
document.querySelectorAll('.btn-filter').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtreActuel = btn.dataset.filter;
    afficherPage(1); // ✅ Reset page 1 à chaque filtre
  });
});

// Initialisation
afficherPage(1);

/* ════════════════════════════════════════
   OUVRIR MODALE DÉTAIL ÉQUIPEMENT
════════════════════════════════════════ */
function ouvrirDetailEquip(eq) {
  console.log('ouvrirDetailEquip appelé', eq); // DEBUG
  const prix = parseFloat(eq.prix_jour || 0);
  const prixFmt = prix.toLocaleString('fr-TN', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' DT';

  // Header
  document.getElementById('eq-modal-nom').textContent  = eq.nom       || '—';
  document.getElementById('eq-modal-ref').textContent  = 'Réf. ' + (eq.reference || '');

  // Mini card
  document.getElementById('eq-modal-cat').textContent  = eq.categorie || '—';
  document.getElementById('eq-modal-nom2').textContent = eq.nom       || '—';

  // Image
  const imgContainer = document.getElementById('eq-img-container');
    // ✅ Image — teste assets (minuscule) ET Assets (majuscule) + jpg/png/webp
  const ref  = eq.reference || '';
  const name = eq.image || '';
  // Liste de toutes les URLs à essayer dans l'ordre
  const tryUrls = name ? [
    '/integration/assets/images/equipements/' + name,
    '/integration/Assets/images/equipements/' + name,
  ] : [
    '/integration/assets/images/equipements/' + ref + '.jpg',
    '/integration/assets/images/equipements/' + ref + '.png',
    '/integration/assets/images/equipements/' + ref + '.webp',
    '/integration/Assets/images/equipements/' + ref + '.jpg',
    '/integration/Assets/images/equipements/' + ref + '.png',
    '/integration/Assets/images/equipements/' + ref + '.webp',
  ];

  let urlIndex = 0;
  const img = document.createElement('img');
  img.className = 'equip-mini-img';
  img.alt = eq.nom || '';
  img.style.cssText = 'width:70px;height:70px;object-fit:contain;border-radius:9px;background:#fff;padding:5px;flex-shrink:0;';
  img.onerror = function() {
    urlIndex++;
    if (urlIndex < tryUrls.length) {
      this.src = tryUrls[urlIndex];
    } else {
      imgContainer.innerHTML = '<div class="equip-mini-img-ph"><span class="material-symbols-outlined">medical_services</span></div>';
    }
  };
  img.src = tryUrls[0];
  imgContainer.innerHTML = '';
  imgContainer.appendChild(img);
  // Statut badge
  const statuts = {
    disponible:  { label: 'Available',    bg: '#dcfce7', color: '#15803d' },
    loue:        { label: 'Rented',       bg: '#fef3c7', color: '#b45309' },
    maintenance: { label: 'Maintenance',  bg: '#fee2e2', color: '#dc2626' }
  };
  const s = statuts[eq.statut] || { label: eq.statut, bg: '#f3f4f6', color: '#374151' };
  const badgeHtml = `<span style="background:${s.bg};color:${s.color};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">${s.label}</span>`;
  document.getElementById('eq-modal-badge').innerHTML  = badgeHtml;
  document.getElementById('eq-modal-statut').innerHTML = badgeHtml;

  // Détails
  document.getElementById('eq-modal-ref2').textContent  = eq.reference  || '—';
  document.getElementById('eq-modal-cat2').textContent  = eq.categorie  || '—';
  document.getElementById('eq-modal-prix').textContent  = prixFmt + ' / jour';
  document.getElementById('eq-modal-prix2').textContent = prixFmt;

  // ✅ Générer description IA selon nom + catégorie
  genererDescription(eq.nom || '', eq.categorie || '');

  // Bouton réserver
  const btnRes = document.getElementById('eq-modal-btn-res');
  if (eq.statut === 'disponible') {
    btnRes.href                 = '/integration/reservation?id=' + eq.id;
    btnRes.style.opacity        = '1';
    btnRes.style.pointerEvents  = 'auto';
    btnRes.style.background     = '#004d99';
    btnRes.innerHTML = '<span class="material-symbols-outlined" style="font-size:17px;">calendar_today</span> Réserver cet équipement';
  } else {
    btnRes.href                = '#';
    btnRes.style.opacity       = '0.5';
    btnRes.style.pointerEvents = 'none';
    btnRes.style.background    = '#9ca3af';
    btnRes.innerHTML = '<span class="material-symbols-outlined" style="font-size:17px;">block</span> Indisponible';
  }

  // Ouvrir la modale
  document.getElementById('modal-equip').classList.add('open');
}

/* ════════════════════════════════════════
   GÉNÉRATION DESCRIPTION IA
   Génère automatiquement une phrase descriptive
   selon le nom et la catégorie de l'équipement
════════════════════════════════════════ */
function genererDescription(nom, categorie) {
  const wrap = document.getElementById('eq-modal-desc-wrap');
  const desc = document.getElementById('eq-modal-desc');

  // ✅ Descriptions avec backticks — évite les apostrophes qui cassent le JS
  const descriptions = {
    'Mobilite': [
      `${nom} est un equipement de mobilite concu pour aider les patients a se deplacer en toute securite et autonomie a domicile.`,
      `Cet equipement offre stabilite et confort pour les personnes a mobilite reduite, permettant une meilleure qualite de vie.`
    ],
    'Respiratoire': [
      `${nom} est un dispositif medical respiratoire permettant un apport en oxygene optimal pour les patients souffrant de troubles respiratoires.`,
      `Cet equipement respiratoire garantit une therapie efficace et confortable pour les patients en insuffisance respiratoire.`
    ],
    'Cardiologie': [
      `${nom} est un equipement cardiologique permettant la surveillance en temps reel des parametres vitaux du patient.`,
      `Ce moniteur cardiaque assure un suivi continu et precis des fonctions cardiaques, essentiel pour la securite du patient a domicile.`
    ],
    'Reanimation': [
      `${nom} est un equipement medical de reanimation utilise pour assister les fonctions vitales des patients en etat critique.`,
      `Ce dispositif de reanimation de haute performance garantit une assistance respiratoire et circulatoire optimale.`
    ],
    'Geriatrie': [
      `${nom} est specialement concu pour le confort et la securite des personnes agees, favorisant leur autonomie et leur bien-etre.`,
      `Cet equipement geriatrique ameliore la qualite de vie des seniors en facilitant leurs activites quotidiennes.`
    ],
    'Radiologie': [
      `${nom} est un equipement d'imagerie medicale permettant des diagnostics precis directement au chevet du patient.`,
      `Ce dispositif de radiologie portable offre des images de haute qualite pour faciliter le diagnostic medical a domicile.`
    ]
  };

  // ✅ Normaliser les accents pour la comparaison
  function normaliser(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }

  const catNorm = normaliser(categorie);
  const cats    = Object.keys(descriptions);
  let found = cats.find(c => catNorm.includes(normaliser(c)));

  let phrase;
  if (found) {
    const i = nom.length % 2;
    phrase = descriptions[found][i];
  } else {
    phrase = nom + ' est un équipement médical professionnel certifié, disponible à la location pour les soins à domicile.';
  }

  desc.textContent = phrase;
  wrap.style.display = 'block';

  // Ouvrir la modale
  document.getElementById('modal-equip').classList.add('open');
}

  } // fin init()

  // Lancer quand le DOM est prêt
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init(); // DOM déjà prêt (inclus dans layout)
  }
})();

/* ✅ SCOPE GLOBAL — accessible depuis onclick="fermerDetailEquip()" dans le HTML */
function fermerDetailEquip() {
  document.getElementById('modal-equip').classList.remove('open');
}
</script>