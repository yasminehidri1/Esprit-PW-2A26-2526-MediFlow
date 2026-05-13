<?php
$user = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
$_patientKey = 'u' . preg_replace('/[^a-z0-9]/i', '', strtolower(($user['prenom'] ?? '') . ($user['nom'] ?? '') . ($user['id'] ?? 'guest')));
$allEquipements = $data['equipements'] ?? [];
?>
<style>
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

  .btn-unfav {
    width: 38px; height: 38px;
    border-radius: 8px;
    background: #fee2e2;
    border: 1px solid #fca5a5;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #dc2626;
    flex-shrink: 0;
    transition: background .18s, transform .15s;
  }
  .btn-unfav:hover { background: #fecaca; transform: scale(1.08); }
  .btn-unfav .material-symbols-outlined { font-size: 17px; font-variation-settings:'FILL' 1; }

  .empty-state { text-align:center; padding:80px 20px; color:#9ca3af; }
  .empty-state .material-symbols-outlined { font-size:64px; display:block; margin-bottom:20px; color:#fca5a5; }
</style>

<div class="pt-24 pb-12 px-10 space-y-8">

  <!-- Header -->
  <section class="flex items-start justify-between flex-wrap gap-4">
    <div>
      <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent">
        Mes Favoris
      </h2>
      <p class="text-on-surface-variant mt-1 font-medium">Your saved equipment for quick access.</p>
    </div>
    <a href="/integration/catalogue"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:#fff;border:1.5px solid #bfdbfe;color:#004d99;font-size:13.5px;font-weight:700;font-family:'Inter',sans-serif;text-decoration:none;transition:background .18s;">
      <span class="material-symbols-outlined" style="font-size:18px;">store</span>
      Browse Catalogue
    </a>
  </section>

  <!-- Grid (rendered by JS) -->
  <div id="favoris-grid" class="product-grid"></div>

  <!-- Empty state -->
  <div id="empty-favoris" style="display:none;">
    <div class="empty-state">
      <span class="material-symbols-outlined">favorite_border</span>
      <p class="font-bold text-lg text-slate-700 mb-2">No favourites yet</p>
      <p class="text-sm mb-6">Click the <strong>heart icon</strong> on any equipment in the catalogue to save it here.</p>
      <a href="/integration/catalogue"
         style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:10px;background:#004d99;color:#fff;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;text-decoration:none;">
        <span class="material-symbols-outlined" style="font-size:17px;">store</span>
        Go to Catalogue
      </a>
    </div>
  </div>

</div>

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>

<script>
(function() {
  var PATIENT_KEY = '<?= $_patientKey ?>';
  var FAV_KEY     = 'fav_' + PATIENT_KEY;
  var ALL_EQ      = <?= json_encode(array_values($allEquipements), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

  /* Build a lookup from server data so we always show fresh status/price */
  var eqById = {};
  ALL_EQ.forEach(function(e) { eqById[e.id] = e; });

  function getFavoris() {
    try { var r = localStorage.getItem(FAV_KEY); return r ? JSON.parse(r) : []; }
    catch(e) { return []; }
  }
  function saveFavoris(favs) {
    try { localStorage.setItem(FAV_KEY, JSON.stringify(favs)); } catch(e) {}
  }

  function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function getImgUrl(eq) {
    var ref  = eq.reference || '';
    var img  = eq.image     || '';
    if (img) return '/integration/assets/images/equipements/' + img;
    return '/integration/assets/images/equipements/' + ref + '.jpg';
  }

  function showToast(msg) {
    var c = document.querySelector('.toast-container');
    if (!c) return;
    var t = document.createElement('div');
    t.style.cssText = 'background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:10px 16px;font-size:13px;font-weight:600;color:#6b7280;box-shadow:0 4px 16px rgba(0,0,0,.10);display:flex;align-items:center;gap:8px;';
    t.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;color:#dc2626;">favorite_border</span>' + msg;
    c.appendChild(t);
    setTimeout(function() { t.remove(); }, 2200);
  }

  function removeCard(eqId) {
    var favs = getFavoris().filter(function(f) { return String(f.id) !== String(eqId); });
    saveFavoris(favs);
    var card = document.querySelector('.product-card[data-id="' + eqId + '"]');
    if (card) {
      card.style.transition = 'opacity .25s,transform .25s';
      card.style.opacity = '0'; card.style.transform = 'scale(.92)';
      setTimeout(function() {
        card.remove();
        if (!getFavoris().length) showEmpty();
      }, 250);
    }
    showToast('Retiré des favoris');
  }

  function showEmpty() {
    document.getElementById('favoris-grid').style.display = 'none';
    document.getElementById('empty-favoris').style.display = '';
  }

  function buildCard(fav) {
    var eq  = eqById[fav.id] || fav;
    var prix = parseFloat(eq.prix_jour || 0).toLocaleString('fr-TN', {minimumFractionDigits:3, maximumFractionDigits:3});
    var badgeClass = eq.statut === 'loue' ? 'badge-demand' : (eq.statut === 'maintenance' ? 'badge-maintenance' : 'badge-available');
    var badgeLabel = eq.statut === 'loue' ? 'RENTED'       : (eq.statut === 'maintenance' ? 'MAINTENANCE'       : 'AVAILABLE');
    var canRes = (eq.statut === 'disponible');
    var imgUrl = getImgUrl(eq);

    var btnRes = canRes
      ? '<a class="btn-reserve" href="/integration/reservation?id=' + escHtml(eq.id) + '"><span class="material-symbols-outlined" style="font-size:15px;">calendar_today</span> Reserve</a>'
      : '<button class="btn-reserve" type="button" disabled><span class="material-symbols-outlined" style="font-size:15px;">block</span> Unavailable</button>';

    return '<div class="product-card" data-id="' + escHtml(eq.id) + '">' +
      '<div class="card-img">' +
        '<img src="' + escHtml(imgUrl) + '" alt="' + escHtml(eq.nom) + '" loading="lazy"' +
          ' onerror="var r=\'' + escHtml(eq.reference) + '\';var exts=[\'jpg\',\'jpeg\',\'png\',\'webp\'];var idx=0;var self=this;(function tryNext(){idx++;if(idx<exts.length){self.src=\'/integration/assets/images/equipements/\'+r+\'.\'+exts[idx];}else{self.style.display=\'none\';}})()">' +
        '<span class="badge-pill ' + badgeClass + '">' + badgeLabel + '</span>' +
      '</div>' +
      '<div class="card-body">' +
        '<div class="card-title-row">' +
          '<h3 class="card-title">' + escHtml(eq.nom) + '</h3>' +
          '<div class="card-price">' + prix + '<span class="unit"> DT/d</span></div>' +
        '</div>' +
        '<p class="card-desc">' + escHtml(eq.categorie) + ' &mdash; R&eacute;f: ' + escHtml(eq.reference) + '</p>' +
        '<div class="card-actions">' +
          btnRes +
          '<button class="btn-unfav" type="button" title="Retirer des favoris" onclick="window._removeFav(' + escHtml(eq.id) + ')">' +
            '<span class="material-symbols-outlined">favorite</span>' +
          '</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  window._removeFav = removeCard;

  function render() {
    var favs = getFavoris();
    var grid = document.getElementById('favoris-grid');
    if (!favs.length) { showEmpty(); return; }
    document.getElementById('empty-favoris').style.display = 'none';
    grid.style.display = '';
    grid.innerHTML = favs.map(buildCard).join('');
  }

  render();
})();
</script>
