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
$_patientKey = 'u' . preg_replace('/[^a-z0-9]/i', '', strtolower(($user['prenom'] ?? '') . ($user['nom'] ?? '') . ($user['id'] ?? 'guest')));
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

  .eq-detail-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:9px 0; border-bottom:1px solid #f3f4f6;
  }
  .eq-detail-row:last-child { border-bottom:none; }
  .eq-detail-lbl { font-size:13px; color:#6b7280; display:flex; align-items:center; gap:5px; }
  .eq-detail-lbl .material-symbols-outlined { font-size:14px; color:#9ca3af; }
  .eq-detail-val { font-size:13px; font-weight:600; color:#111827; }

  .eq-prix-highlight {
    background:linear-gradient(135deg,#eff6ff,#f0f9ff);
    border:1px solid #bfdbfe; border-radius:11px;
    padding:14px 18px;
    display:flex; align-items:center; justify-content:space-between;
    margin:16px 0;
  }
  .eq-prix-highlight .lbl { font-size:13px; color:#1d4ed8; font-weight:600; }
  .eq-prix-highlight .val { font-family:'Manrope',sans-serif; font-size:22px; font-weight:900; color:#004d99; }

  /* ⭐ Rating sur carte */
  .card-rating { display:flex; align-items:center; gap:4px; margin-bottom:10px; }
  .card-rating .stars { display:flex; gap:2px; }
  .card-rating .star { font-size:14px; color:#e5e7eb; transition:color .15s; }
  .card-rating .star.filled { color:#f59e0b; }
  .card-rating .count { font-size:11px; color:#9ca3af; font-weight:600; }

  /* ⭐ Rating dans modale */
  .modal-rating-section {
    background:#fffbeb; border:1.5px solid #fde68a;
    border-radius:12px; padding:16px 18px; margin:16px 0;
  }
  .modal-rating-title {
    font-size:11px; font-weight:800; color:#92400e;
    text-transform:uppercase; letter-spacing:.07em;
    display:flex; align-items:center; gap:6px; margin-bottom:12px;
  }
  .modal-rating-title .material-symbols-outlined { font-size:15px; color:#f59e0b; }
  .modal-stars-display { display:flex; align-items:center; gap:6px; margin-bottom:12px; }
  .modal-stars-display .star-big { font-size:28px; color:#e5e7eb; cursor:pointer; transition:color .15s, transform .15s; }
  .modal-stars-display .star-big:hover,
  .modal-stars-display .star-big.hovered { color:#f59e0b; transform:scale(1.2); }
  .modal-stars-display .star-big.filled { color:#f59e0b; }
  .rating-summary { font-size:12px; color:#78350f; font-weight:600; }
  .btn-submit-rating {
    width:100%; padding:10px; border-radius:8px;
    background:#f59e0b; color:#fff; border:none;
    font-size:13px; font-weight:700; cursor:pointer;
    font-family:'Inter',sans-serif; transition:background .18s;
    display:flex; align-items:center; justify-content:center; gap:6px;
    margin-top:10px;
  }
  .btn-submit-rating:hover { background:#d97706; }
  .btn-submit-rating:disabled { opacity:.5; cursor:not-allowed; }
  .rating-done {
    display:none; align-items:center; gap:8px;
    font-size:13px; font-weight:700; color:#15803d;
    background:#f0fdf4; border:1px solid #bbf7d0;
    border-radius:8px; padding:10px 14px; margin-top:10px;
  }
  .rating-done.show { display:flex; }
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
            <!-- ⭐ Rating affiché sur la carte -->
            <div class="card-rating" id="card-rating-<?= $eq['id'] ?>">
              <div class="stars" id="stars-display-<?= $eq['id'] ?>">
                <span class="star material-symbols-outlined">star</span>
                <span class="star material-symbols-outlined">star</span>
                <span class="star material-symbols-outlined">star</span>
                <span class="star material-symbols-outlined">star</span>
                <span class="star material-symbols-outlined">star</span>
              </div>
              <span class="count" id="rating-count-<?= $eq['id'] ?>">Aucun avis</span>
            </div>
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

      <?php endif; ?>
    </div>

    <!-- PAGINATION -->
    <div id="pagination-catalogue"></div>

  </section>
</div>

<!-- MODALE DÉTAIL ÉQUIPEMENT -->
<div id="modal-equip" class="modal-equip-overlay">
  <div class="modal-equip-box">
    <div class="modal-equip-header">
      <div>
        <h2 id="eq-modal-nom">Détail équipement</h2>
        <p id="eq-modal-ref">Réf. —</p>
      </div>
      <button class="modal-equip-close" onclick="fermerDetailEquip()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="modal-equip-body">
      <div class="equip-mini-card">
        <div id="eq-img-container"></div>
        <div>
          <div id="eq-modal-cat" style="font-size:11px;color:#9ca3af;margin-bottom:3px;"></div>
          <div id="eq-modal-nom2" style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;margin-bottom:4px;"></div>
          <div id="eq-modal-badge"></div>
        </div>
      </div>
      <div id="eq-modal-desc-wrap" style="background:#fff;border:1px solid #e8eaf0;border-radius:10px;padding:12px 14px;margin-bottom:14px;display:none;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
          <span class="material-symbols-outlined" style="font-size:15px;color:#004d99;">auto_awesome</span>
          <span style="font-size:11px;font-weight:700;color:#004d99;text-transform:uppercase;letter-spacing:.06em;">Description IA</span>
        </div>
        <p id="eq-modal-desc" style="font-size:13px;color:#374151;line-height:1.6;margin:0;"></p>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl"><span class="material-symbols-outlined">tag</span>Référence</span>
        <span class="eq-detail-val" id="eq-modal-ref2" style="color:#0ea5e9;"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl"><span class="material-symbols-outlined">category</span>Catégorie</span>
        <span class="eq-detail-val" id="eq-modal-cat2"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl"><span class="material-symbols-outlined">info</span>Statut</span>
        <span class="eq-detail-val" id="eq-modal-statut"></span>
      </div>
      <div class="eq-detail-row">
        <span class="eq-detail-lbl"><span class="material-symbols-outlined">payments</span>Prix / jour</span>
        <span class="eq-detail-val" id="eq-modal-prix" style="color:#004d99;font-size:15px;"></span>
      </div>
      <div class="eq-prix-highlight">
        <span class="lbl">Tarif journalier</span>
        <span class="val" id="eq-modal-prix2"></span>
      </div>
      <!-- ⭐ Section Rating -->
      <div class="modal-rating-section">
        <div class="modal-rating-title">
          <span class="material-symbols-outlined">star</span>
          Votre avis sur cet équipement
        </div>
        <div class="modal-stars-display" id="modal-stars">
          <span class="star-big material-symbols-outlined" data-val="1">star</span>
          <span class="star-big material-symbols-outlined" data-val="2">star</span>
          <span class="star-big material-symbols-outlined" data-val="3">star</span>
          <span class="star-big material-symbols-outlined" data-val="4">star</span>
          <span class="star-big material-symbols-outlined" data-val="5">star</span>
        </div>
        <div class="rating-summary" id="rating-summary">Cliquez sur une étoile pour noter</div>
        <button class="btn-submit-rating" id="btn-submit-rating" disabled onclick="soumettreRating()">
          <span class="material-symbols-outlined" style="font-size:16px;">send</span>
          Soumettre mon avis
        </button>
        <div class="rating-done" id="rating-done">
          <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
          Merci pour votre avis !
        </div>
      </div>

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

document.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-eq]');
  if (!btn) {
    const parent = e.target.closest('.btn-eye-card');
    if (parent) {
      const raw = parent.getAttribute('data-eq');
      if (raw) { try { ouvrirDetailEquip(JSON.parse(raw)); } catch(err) { console.error(err); } }
    }
    return;
  }
  const raw = btn.getAttribute('data-eq');
  if (!raw) return;
  try { ouvrirDetailEquip(JSON.parse(raw)); } catch(err) { console.error('Erreur parsing data-eq:', err); }
});

const ITEMS_PAR_PAGE = 4;
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
  document.querySelectorAll('.product-card').forEach(c => c.style.display = 'none');
  cartes.forEach((c, i) => { c.style.display = (i >= debut && i < fin) ? '' : 'none'; });
  renderPagination(page, pages, total);
}

window.renderPagination = function(page, pages, total) {
  const container = document.getElementById('pagination-catalogue');
  if (!container) return;
  if (pages <= 1) { container.innerHTML = ''; return; }
  container.innerHTML = `
    <div style="display:flex;justify-content:flex-end;align-items:center;gap:8px;padding:16px 0;margin-top:12px;">
      <button onclick="afficherPage(${page - 1})" ${page <= 1 ? 'disabled' : ''}
              style="padding:9px 20px;border-radius:9px;border:1.5px solid ${page<=1?'#e5e7eb':'#004d99'};background:${page<=1?'#f9fafb':'#fff'};color:${page<=1?'#9ca3af':'#004d99'};font-size:13.5px;font-weight:700;cursor:${page<=1?'not-allowed':'pointer'};font-family:'Inter',sans-serif;">
        ← Précédent
      </button>
      ${Array.from({length:pages},(_,i)=>i+1).map(p=>`
        <button onclick="afficherPage(${p})"
                style="width:36px;height:36px;border-radius:9px;border:${p===page?'none':'1.5px solid #e5e7eb'};background:${p===page?'#004d99':'#fff'};color:${p===page?'#fff':'#374151'};font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;">
          ${p}
        </button>`).join('')}
      <button onclick="afficherPage(${page + 1})" ${page >= pages ? 'disabled' : ''}
              style="padding:9px 20px;border-radius:9px;border:1.5px solid transparent;background:${page>=pages?'#f9fafb':'#004d99'};color:${page>=pages?'#9ca3af':'#fff'};font-size:13.5px;font-weight:700;cursor:${page>=pages?'not-allowed':'pointer'};font-family:'Inter',sans-serif;">
        Suivant →
      </button>
    </div>`;
}

document.querySelectorAll('.btn-filter').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtreActuel = btn.dataset.filter;
    afficherPage(1);
  });
});

afficherPage(1);

function ouvrirDetailEquip(eq) {
  const prix = parseFloat(eq.prix_jour || 0);
  const prixFmt = prix.toLocaleString('fr-TN', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' DT';
  document.getElementById('eq-modal-nom').textContent  = eq.nom       || '—';
  document.getElementById('eq-modal-ref').textContent  = 'Réf. ' + (eq.reference || '');
  document.getElementById('eq-modal-cat').textContent  = eq.categorie || '—';
  document.getElementById('eq-modal-nom2').textContent = eq.nom       || '—';
  const imgContainer = document.getElementById('eq-img-container');
  const ref  = eq.reference || '';
  const name = eq.image || '';
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
    if (urlIndex < tryUrls.length) { this.src = tryUrls[urlIndex]; }
    else { imgContainer.innerHTML = '<div class="equip-mini-img-ph"><span class="material-symbols-outlined">medical_services</span></div>'; }
  };
  img.src = tryUrls[0];
  imgContainer.innerHTML = '';
  imgContainer.appendChild(img);
  const statuts = {
    disponible:  { label: 'Available',   bg: '#dcfce7', color: '#15803d' },
    loue:        { label: 'Rented',      bg: '#fef3c7', color: '#b45309' },
    maintenance: { label: 'Maintenance', bg: '#fee2e2', color: '#dc2626' }
  };
  const s = statuts[eq.statut] || { label: eq.statut, bg: '#f3f4f6', color: '#374151' };
  const badgeHtml = `<span style="background:${s.bg};color:${s.color};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">${s.label}</span>`;
  document.getElementById('eq-modal-badge').innerHTML  = badgeHtml;
  document.getElementById('eq-modal-statut').innerHTML = badgeHtml;
  document.getElementById('eq-modal-ref2').textContent  = eq.reference || '—';
  document.getElementById('eq-modal-cat2').textContent  = eq.categorie || '—';
  document.getElementById('eq-modal-prix').textContent  = prixFmt + ' / jour';
  document.getElementById('eq-modal-prix2').textContent = prixFmt;
  genererDescription(eq.nom || '', eq.categorie || '');
  const btnRes = document.getElementById('eq-modal-btn-res');
  if (eq.statut === 'disponible') {
    btnRes.href = '/integration/reservation?id=' + eq.id;
    btnRes.style.opacity = '1'; btnRes.style.pointerEvents = 'auto'; btnRes.style.background = '#004d99';
    btnRes.innerHTML = '<span class="material-symbols-outlined" style="font-size:17px;">calendar_today</span> Réserver cet équipement';
  } else {
    btnRes.href = '#'; btnRes.style.opacity = '0.5'; btnRes.style.pointerEvents = 'none'; btnRes.style.background = '#9ca3af';
    btnRes.innerHTML = '<span class="material-symbols-outlined" style="font-size:17px;">block</span> Indisponible';
  }
  // ⭐ Rating
  if (typeof initModalRating === 'function') initModalRating(eq.id);

  document.getElementById('modal-equip').classList.add('open');
}

function genererDescription(nom, categorie) {
  const wrap = document.getElementById('eq-modal-desc-wrap');
  const desc = document.getElementById('eq-modal-desc');
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
  function normaliser(str) { return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase(); }
  const catNorm = normaliser(categorie);
  const cats = Object.keys(descriptions);
  let found = cats.find(c => catNorm.includes(normaliser(c)));
  let phrase;
  if (found) { phrase = descriptions[found][nom.length % 2]; }
  else { phrase = nom + ' est un équipement médical professionnel certifié, disponible à la location pour les soins à domicile.'; }
  desc.textContent = phrase;
  wrap.style.display = 'block';
  // genererDescription ne gère que le texte — l'ouverture modale est dans ouvrirDetailEquip
}

  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
  else { init(); }
})();

function fermerDetailEquip() {
  document.getElementById('modal-equip').classList.remove('open');
}
</script>
<script>
/* ════════════════════════════════════════
   ⭐ SYSTÈME DE RATING — 5 étoiles
   Stocké en localStorage par équipement
   Visible sur cartes + dans modale
════════════════════════════════════════ */
var currentRatingEqId = null;
var selectedRating    = 0;
var RATING_LABELS = ['','Très mauvais 😞','Mauvais 😕','Correct 🙂','Bien 😊','Excellent ! 🌟'];
// ✅ Clé unique par patient — isole les ratings entre patients
var PATIENT_KEY = '<?= $_patientKey ?>';

function getRatingData(eqId) {
  try { var r = localStorage.getItem('rating_' + PATIENT_KEY + '_eq_' + eqId); return r ? JSON.parse(r) : {total:0,count:0,myRating:0}; }
  catch(e) { return {total:0,count:0,myRating:0}; }
}
function saveRatingData(eqId, data) {
  try { localStorage.setItem('rating_' + PATIENT_KEY + '_eq_' + eqId, JSON.stringify(data)); } catch(e) {}
}

function mettreAJourCarteRating(eqId, data) {
  var avg = data.count > 0 ? Math.round(data.total / data.count) : 0;
  var starsEl = document.getElementById('stars-display-' + eqId);
  var countEl = document.getElementById('rating-count-' + eqId);
  if (starsEl) {
    starsEl.querySelectorAll('.star').forEach(function(s, i) {
      s.classList.toggle('filled', i < avg);
    });
  }
  if (countEl) {
    countEl.textContent = data.count > 0 ? avg + '/5 (' + data.count + ' avis)' : 'Aucun avis';
  }
}

function chargerTousRatings() {
  document.querySelectorAll('.product-card').forEach(function(card) {
    var id = card.dataset.id;
    if (id) mettreAJourCarteRating(id, getRatingData(id));
  });
}

function initModalRating(eqId) {
  currentRatingEqId = eqId;
  selectedRating    = 0;
  var data     = getRatingData(eqId);
  var stars    = document.querySelectorAll('#modal-stars .star-big');
  var doneEl   = document.getElementById('rating-done');
  var btnEl    = document.getElementById('btn-submit-rating');
  var summaryEl= document.getElementById('rating-summary');
  if (!stars.length) return;

  stars.forEach(function(s) { s.classList.remove('filled','hovered'); });
  doneEl.classList.remove('show');
  btnEl.disabled = true;

  if (data.myRating > 0) {
    stars.forEach(function(s, i) { s.classList.toggle('filled', i < data.myRating); });
    summaryEl.textContent = 'Votre note : ' + RATING_LABELS[data.myRating];
    doneEl.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">check_circle</span> Vous avez déjà noté : ' + data.myRating + '/5 — ' + RATING_LABELS[data.myRating];
    doneEl.classList.add('show');
  } else if (data.count > 0) {
    var avg = Math.round(data.total / data.count);
    summaryEl.textContent = 'Moyenne actuelle : ' + avg + '/5 (' + data.count + ' avis) — Donnez votre avis !';
  } else {
    summaryEl.textContent = 'Soyez le premier à noter cet équipement !';
  }
}

// Hover étoiles modale
document.addEventListener('mouseover', function(e) {
  var star = e.target.closest('#modal-stars .star-big');
  if (!star) return;
  var val = parseInt(star.dataset.val);
  document.querySelectorAll('#modal-stars .star-big').forEach(function(s, i) {
    s.classList.remove('filled');
    s.classList.toggle('hovered', i < val);
  });
  var sumEl = document.getElementById('rating-summary');
  if (sumEl) sumEl.textContent = RATING_LABELS[val];
});

document.addEventListener('mouseout', function(e) {
  var container = document.getElementById('modal-stars');
  if (!container || container.contains(e.relatedTarget)) return;
  document.querySelectorAll('#modal-stars .star-big').forEach(function(s, i) {
    s.classList.remove('hovered');
    s.classList.toggle('filled', i < selectedRating);
  });
  var sumEl = document.getElementById('rating-summary');
  if (sumEl) sumEl.textContent = selectedRating > 0 ? RATING_LABELS[selectedRating] : (currentRatingEqId ? 'Cliquez pour noter' : '');
});

// Clic étoiles modale
document.addEventListener('click', function(e) {
  var star = e.target.closest('#modal-stars .star-big');
  if (!star) return;
  var data = currentRatingEqId ? getRatingData(currentRatingEqId) : null;
  if (data && data.myRating > 0) return; // déjà noté
  selectedRating = parseInt(star.dataset.val);
  document.querySelectorAll('#modal-stars .star-big').forEach(function(s, i) {
    s.classList.toggle('filled', i < selectedRating);
    s.classList.remove('hovered');
  });
  var btnEl = document.getElementById('btn-submit-rating');
  if (btnEl) btnEl.disabled = false;
  var sumEl = document.getElementById('rating-summary');
  if (sumEl) sumEl.textContent = RATING_LABELS[selectedRating];
});

function soumettreRating() {
  if (!currentRatingEqId || selectedRating === 0) return;
  var data = getRatingData(currentRatingEqId);
  if (data.myRating > 0) return;
  data.total   += selectedRating;
  data.count   += 1;
  data.myRating = selectedRating;
  saveRatingData(currentRatingEqId, data);
  var doneEl = document.getElementById('rating-done');
  doneEl.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">check_circle</span> Merci ! Votre note : ' + selectedRating + '/5 — ' + RATING_LABELS[selectedRating];
  doneEl.classList.add('show');
  var btnEl = document.getElementById('btn-submit-rating');
  if (btnEl) btnEl.disabled = true;
  mettreAJourCarteRating(currentRatingEqId, data);
}

// Charger au démarrage
chargerTousRatings();
</script>