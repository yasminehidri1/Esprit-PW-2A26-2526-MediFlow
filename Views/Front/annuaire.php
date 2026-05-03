<?php
/* ── Annuaire Médecins (Patient) ─── partial, rendered by layout.php ── */

/* Assignation stable d'une spécialité par médecin (basée sur l'ID, pas de colonne BDD) */
$specialites_pool = [
    'Médecine Générale',
    'Cardiologie',
    'Pédiatrie',
    'Dermatologie',
    'Neurologie',
    'Gynécologie',
    'Ophtalmologie',
    'Orthopédie',
];
foreach ($medecins as &$med) {
    if (empty($med['specialite'])) {
        $med['specialite'] = $specialites_pool[$med['id'] % count($specialites_pool)];
    }
}
unset($med);
?>

<style>
  .ann * { box-sizing: border-box; }

  .ann-hero {
    background: linear-gradient(135deg, #1a3f8f 0%, #2563eb 60%, #0ea5e9 100%);
    border-radius: 20px;
    padding: 2.8rem 2rem 2.4rem;
    margin-bottom: 1.8rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .ann-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 60%),
                radial-gradient(circle at 10% 80%, rgba(14,165,233,0.25) 0%, transparent 50%);
    pointer-events: none;
  }
  .ann-hero h1 { color:#fff; font-size:28px; font-weight:700; margin:0 0 8px; letter-spacing:-0.3px; }
  .ann-hero p  { color:rgba(255,255,255,0.78); font-size:14px; margin:0 0 1.6rem; }

  .ann-search-wrap {
    max-width: 640px;
    margin: 0 auto;
    display: flex;
    gap: 8px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
    justify-content: center;
  }
  .ann-search-inner { flex:1; min-width:160px; position:relative; }
  .ann-search-inner .ann-ico {
    position:absolute; left:13px; top:50%; transform:translateY(-50%);
    color:rgba(255,255,255,0.6); font-size:18px; pointer-events:none;
    font-family:'Material Symbols Outlined';
  }
  .ann-search-inner input {
    width:100%; padding:11px 16px 11px 42px;
    background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
    border-radius:12px; color:#fff; font-size:14px; outline:none;
    transition:background 0.2s, border-color 0.2s;
  }
  .ann-search-inner input::placeholder { color:rgba(255,255,255,0.55); }
  .ann-search-inner input:focus { background:rgba(255,255,255,0.22); border-color:rgba(255,255,255,0.5); }

  .ann-select-wrap { position:relative; }
  .ann-select-wrap .ann-ico {
    position:absolute; left:11px; top:50%; transform:translateY(-50%);
    color:rgba(255,255,255,0.65); font-size:16px; pointer-events:none;
    font-family:'Material Symbols Outlined';
  }
  .ann-spec-select {
    padding:11px 16px 11px 36px;
    background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
    border-radius:12px; color:#fff; font-size:14px; outline:none;
    cursor:pointer; appearance:none; -webkit-appearance:none;
    transition:background 0.2s, border-color 0.2s;
    min-width:175px;
  }
  .ann-spec-select option { color:#111827; background:#fff; }
  .ann-spec-select:focus { background:rgba(255,255,255,0.22); border-color:rgba(255,255,255,0.5); }

  .ann-search-btn {
    padding:11px 24px; background:#fff; color:#1a3f8f;
    border:none; border-radius:12px; font-weight:700; font-size:14px;
    cursor:pointer; white-space:nowrap; transition:background 0.15s, transform 0.1s;
  }
  .ann-search-btn:hover { background:#e8f0fe; transform:translateY(-1px); }

  /* Stats */
  .ann-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:1.5rem; }
  .ann-stat {
    background:#fff; border:0.5px solid rgba(0,0,0,0.08); border-radius:14px;
    padding:14px 16px; display:flex; align-items:center; gap:12px;
  }
  .ann-stat-icon {
    width:38px; height:38px; border-radius:10px; background:#e8f0fe;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; color:#1a3f8f; font-family:'Material Symbols Outlined'; font-size:20px;
  }
  .ann-stat-val { font-size:20px; font-weight:700; color:#1a3f8f; line-height:1; }
  .ann-stat-lbl { font-size:11px; color:#6b7280; margin-top:3px; }

  /* Barre résultats + filtre actif */
  .ann-results-bar {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:1rem; flex-wrap:wrap; gap:8px;
  }
  .ann-active-filter {
    display:none; align-items:center; gap:8px;
    font-size:13px; color:#374151;
  }
  .ann-active-filter.visible { display:flex; }
  .ann-active-filter strong { color:#1a3f8f; }
  .ann-clear-filter {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; border-radius:20px;
    background:#fee2e2; color:#991b1b;
    border:none; font-size:12px; font-weight:600; cursor:pointer;
    transition:background 0.15s;
  }
  .ann-clear-filter:hover { background:#fecaca; }
  .ann-results-count { font-size:13px; color:#6b7280; }
  .ann-results-count strong { color:#1a3f8f; }

  /* Grid */
  .ann-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:14px; }

  /* Card */
  .ann-card {
    background:#fff; border:0.5px solid rgba(0,0,0,0.08); border-radius:16px;
    padding:20px 16px 16px; display:flex; flex-direction:column; gap:14px;
    position:relative; overflow:hidden;
    transition:box-shadow 0.2s, transform 0.2s, border-color 0.2s;
  }
  .ann-card:hover { box-shadow:0 8px 28px rgba(37,99,235,0.12); transform:translateY(-2px); border-color:rgba(37,99,235,0.3); }
  .ann-card.hidden { display:none; }
  .ann-card-topbar {
    position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,#1a3f8f,#0ea5e9);
    border-radius:16px 16px 0 0; opacity:0; transition:opacity 0.2s;
  }
  .ann-card:hover .ann-card-topbar { opacity:1; }
  .ann-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
  .ann-avatar {
    width:50px; height:50px; border-radius:13px;
    background:linear-gradient(135deg,#dbeafe,#bfdbfe);
    display:flex; align-items:center; justify-content:center;
    font-size:22px; font-weight:700; color:#1a3f8f; flex-shrink:0;
  }
  .ann-badge-avail {
    display:flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600; color:#166534; background:#dcfce7;
    padding:4px 9px; border-radius:20px;
  }
  .ann-dot {
    width:6px; height:6px; border-radius:50%; background:#16a34a;
    animation:ann-pulse 2s infinite; flex-shrink:0;
  }
  @keyframes ann-pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  .ann-card-name { font-size:15px; font-weight:700; color:#111827; margin:0 0 3px; line-height:1.3; }
  .ann-card-spec { font-size:12px; color:#6b7280; margin:0 0 6px; }
  .ann-tag {
    display:inline-block; font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.06em;
    color:#1e40af; background:#eff6ff;
    padding:2px 9px; border-radius:20px; border:1px solid #bfdbfe;
  }
  .ann-divider { border:none; border-top:0.5px solid #f0f0f0; margin:0; }
  .ann-metrics { display:grid; grid-template-columns:1fr 1fr; gap:4px; }
  .ann-metric { text-align:center; padding:6px 0; }
  .ann-metric:first-child { border-right:0.5px solid #f0f0f0; }
  .ann-metric-val { font-size:16px; font-weight:700; color:#111827; display:flex; align-items:center; justify-content:center; gap:3px; }
  .ann-metric-val .ann-star { color:#f59e0b; font-size:13px; }
  .ann-metric-lbl { font-size:10px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin-top:2px; }
  .ann-actions { display:flex; flex-direction:column; gap:7px; }
  .ann-btn-primary {
    width:100%; padding:10px; background:#1a3f8f; color:#fff;
    border:none; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer;
    text-align:center; text-decoration:none; display:block;
    transition:background 0.15s, transform 0.1s;
  }
  .ann-btn-primary:hover { background:#1e40af; transform:translateY(-1px); }
  .ann-btn-secondary {
    width:100%; padding:9px; background:transparent; color:#6b7280;
    border:0.5px solid #d1d5db; border-radius:10px; font-size:13px; font-weight:600;
    cursor:pointer; text-align:center; text-decoration:none; display:block;
    transition:all 0.15s;
  }
  .ann-btn-secondary:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }

  .ann-empty {
    grid-column:1/-1; text-align:center; padding:4rem 2rem;
    background:#fff; border:0.5px solid rgba(0,0,0,0.08); border-radius:16px;
  }
  .ann-empty-icon { font-size:48px; color:#d1d5db; display:block; margin-bottom:12px; }
  .ann-empty h3 { font-size:16px; color:#374151; margin:0 0 6px; font-weight:700; }
  .ann-empty p { font-size:13px; color:#9ca3af; margin:0; }

  #ann-no-results {
    display:none; grid-column:1/-1; text-align:center; padding:3rem 2rem;
    background:#fff; border:0.5px solid rgba(0,0,0,0.08); border-radius:16px;
  }
  #ann-no-results.visible { display:block; }
  #ann-no-results p { font-size:14px; color:#9ca3af; margin:0; }
</style>

<div class="ann">

  <!-- Hero -->
  <div class="ann-hero">
    <h1>Trouvez votre Médecin</h1>
    <p>Prenez rendez-vous en quelques clics avec nos spécialistes qualifiés</p>

    <form method="GET" action="/integration/rdv/annuaire" class="ann-search-wrap">
      <!-- Recherche texte -->
      <div class="ann-search-inner">
        <span class="ann-ico material-symbols-outlined">search</span>
        <input type="text" name="search"
               value="<?= htmlspecialchars($search ?? '') ?>"
               placeholder="Rechercher par nom…"/>
      </div>

      <!-- Tri par spécialité (JS, pas de submit) -->
      <div class="ann-select-wrap">
        <span class="ann-ico material-symbols-outlined">medical_services</span>
        <select class="ann-spec-select" id="ann-spec-filter">
          <option value="">Toutes les spécialités</option>
          <?php foreach ($specialites_pool as $sp): ?>
            <option value="<?= htmlspecialchars($sp) ?>"><?= htmlspecialchars($sp) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="ann-search-btn">Rechercher</button>
    </form>
  </div>

  <!-- Stats -->
  <div class="ann-stats">
    <div class="ann-stat">
      <div class="ann-stat-icon"><span class="material-symbols-outlined">group</span></div>
      <div>
        <div class="ann-stat-val"><?= count($medecins) ?></div>
        <div class="ann-stat-lbl">Médecins disponibles</div>
      </div>
    </div>
    <div class="ann-stat">
      <div class="ann-stat-icon"><span class="material-symbols-outlined">verified</span></div>
      <div>
        <div class="ann-stat-val">100%</div>
        <div class="ann-stat-lbl">Certifiés MediFlow</div>
      </div>
    </div>
    <div class="ann-stat">
      <div class="ann-stat-icon"><span class="material-symbols-outlined">schedule</span></div>
      <div>
        <div class="ann-stat-val">&lt; 48h</div>
        <div class="ann-stat-lbl">Délai moyen de RDV</div>
      </div>
    </div>
  </div>

  <!-- Barre résultats -->
  <div class="ann-results-bar">
    <div class="ann-active-filter" id="ann-active-filter">
      Filtré par : <strong id="ann-filter-label"></strong>
      <button class="ann-clear-filter" id="ann-clear-btn" type="button">
        <span class="material-symbols-outlined" style="font-size:13px;vertical-align:middle;">close</span>
        Effacer
      </button>
    </div>
    <p class="ann-results-count">
      <strong id="ann-visible-count"><?= count($medecins) ?></strong>
      médecin<?= count($medecins) > 1 ? 's' : '' ?> affiché<?= count($medecins) > 1 ? 's' : '' ?>
      <?php if (!empty($search)): ?>
        pour "<strong><?= htmlspecialchars($search) ?></strong>"
      <?php endif; ?>
    </p>
  </div>

  <!-- Grid -->
  <div class="ann-grid" id="ann-grid">

    <?php if (empty($medecins)): ?>
      <div class="ann-empty">
        <span class="ann-empty-icon material-symbols-outlined">person_search</span>
        <h3>Aucun médecin trouvé</h3>
        <p>Essayez un autre nom ou spécialité.</p>
      </div>
    <?php endif; ?>

    <?php foreach ($medecins as $med):
      $initial = strtoupper(substr($med['prenom'], 0, 1));
      $nb_rdv  = $med['nb_rdv'] ?? 0;
      $spec    = htmlspecialchars($med['specialite']);
    ?>
    <div class="ann-card" data-specialite="<?= $spec ?>">
      <div class="ann-card-topbar"></div>

      <div class="ann-card-head">
        <div class="ann-avatar"><?= $initial ?></div>
        <div class="ann-badge-avail"><span class="ann-dot"></span>Disponible</div>
      </div>

      <div>
        <p class="ann-card-name">Dr. <?= htmlspecialchars($med['prenom'] . ' ' . $med['nom']) ?></p>
        <p class="ann-card-spec"><?= $spec ?></p>
        <span class="ann-tag"><?= $spec === 'Médecine Générale' ? 'Généraliste' : 'Spécialiste' ?></span>
      </div>

      <hr class="ann-divider"/>

      <div class="ann-metrics">
        <div class="ann-metric">
          <div class="ann-metric-val"><?= $nb_rdv ?>+</div>
          <div class="ann-metric-lbl">Patients</div>
        </div>
        <div class="ann-metric">
          <div class="ann-metric-val"><span class="ann-star">&#9733;</span> 4.9</div>
          <div class="ann-metric-lbl">Note</div>
        </div>
      </div>

      <div class="ann-actions">
        <a href="/integration/rdv/reserver?medecin_id=<?= $med['id'] ?>" class="ann-btn-primary">Prendre RDV</a>
        <a href="/integration/rdv/medecin/planning?medecin_id=<?= $med['id'] ?>" class="ann-btn-secondary">Voir le planning</a>
      </div>
    </div>
    <?php endforeach; ?>

    <div id="ann-no-results">
      <p>Aucun médecin ne correspond à cette spécialité.</p>
    </div>

  </div>
</div>

<script>
(function () {
  var select      = document.getElementById('ann-spec-filter');
  var cards       = document.querySelectorAll('.ann-card');
  var activeBar   = document.getElementById('ann-active-filter');
  var filterLabel = document.getElementById('ann-filter-label');
  var clearBtn    = document.getElementById('ann-clear-btn');
  var countEl     = document.getElementById('ann-visible-count');
  var noResults   = document.getElementById('ann-no-results');

  function applyFilter(spec) {
    var visible = 0;
    cards.forEach(function(card) {
      var show = !spec || card.getAttribute('data-specialite') === spec;
      card.classList.toggle('hidden', !show);
      if (show) visible++;
    });

    if (countEl) countEl.textContent = visible;
    if (noResults) noResults.classList.toggle('visible', visible === 0);
    if (activeBar) {
      if (spec) {
        activeBar.classList.add('visible');
        if (filterLabel) filterLabel.textContent = spec;
      } else {
        activeBar.classList.remove('visible');
      }
    }
  }

  if (select) {
    select.addEventListener('change', function() { applyFilter(this.value); });
  }
  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      if (select) select.value = '';
      applyFilter('');
    });
  }
})();
</script>