<?php
require_once __DIR__ . '/../../model/Reservation.php';
require_once __DIR__ . '/../../model/Equipement.php';

define('EUR_TO_DT', 3.4052);

$reservationModel  = new Reservation();
$equipementModel   = new Equipement();
$reservations      = $reservationModel->getAll();
$equipements       = $equipementModel->getAll();
$totalReservations = count(array_filter($reservations, fn($r) => $r['statut'] === 'en_cours'));

function fmtDate($d) {
    if (!$d) return '—';
    return (new DateTime($d))->format('d/m/Y');
}
function getBadgeClass($s) {
    return ['en_cours'=>'encours','termine'=>'termine','en_retard'=>'retard'][$s] ?? 'encours';
}
function getBadgeLabel($s) {
    return ['en_cours'=>'En cours','termine'=>'Terminé','en_retard'=>'En retard'][$s] ?? '—';
}

/**
 * getImageUrl() — cherche dans Assets/images/equipements/
 */
function getImageUrlMR($reference) {
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $localPath = __DIR__ . '/../../Assets/images/equipements/' . $reference . '.' . $ext;
        if (file_exists($localPath)) {
            return '/projet web/Assets/images/equipements/' . $reference . '.' . $ext;
        }
    }
    return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"><rect fill="%23f3f4f6" width="80" height="80"/><text fill="%239ca3af" font-family="sans-serif" font-size="10" x="50%25" y="50%25" text-anchor="middle" dy=".3em">IMG</text></svg>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mes Réservations - MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet web/Assets/materiel.css"/>
  <style>
    .reservations-wrap { max-width:900px; margin:0 auto; padding:80px 24px 60px; }
    .page-title { font-family:'Manrope',sans-serif; font-size:28px; font-weight:900; color:#1a56db; margin-bottom:6px; }
    .page-subtitle { font-size:14px; color:#6b7280; margin-bottom:32px; }

    /* Carte réservation */
    .resa-card { background:#fff; border:1px solid #e8eaf0; border-radius:14px; padding:20px 22px; margin-bottom:16px; display:flex; align-items:center; gap:18px; transition:box-shadow .2s; }
    .resa-card:hover { box-shadow:0 6px 24px rgba(26,86,219,.08); }
    .resa-img { width:80px; height:80px; object-fit:contain; border-radius:10px; background:#f3f4f6; padding:6px; flex-shrink:0; }
    .resa-info { flex:1; }
    .resa-ref  { font-size:11px; font-weight:700; color:#0ea5e9; text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:3px; }
    .resa-nom  { font-family:'Manrope',sans-serif; font-size:16px; font-weight:800; color:#111827; margin-bottom:3px; }
    .resa-cat  { font-size:12px; color:#9ca3af; margin-bottom:8px; }
    .resa-prix { font-size:14px; font-weight:700; color:#1a56db; }

    .resa-dates { display:flex; flex-direction:column; gap:4px; align-items:center; min-width:130px; }
    .resa-date-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#9ca3af; }
    .resa-date-val   { font-size:13px; font-weight:600; color:#374151; }
    .resa-date-arrow { color:#9ca3af; font-size:18px; }

    .resa-locataire { min-width:130px; font-size:13px; }
    .resa-locataire .nom  { font-weight:700; color:#111827; }
    .resa-locataire .tel  { color:#9ca3af; font-size:12px; margin-top:2px; }
    .resa-locataire .ville{ color:#9ca3af; font-size:12px; }

    /* Badges */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; }
    .badge-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .badge.termine  { background:#dcfce7; color:#15803d; }
    .badge.termine  .badge-dot { background:#16a34a; }
    .badge.encours  { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .badge.encours  .badge-dot { background:#1d4ed8; }
    .badge.retard   { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
    .badge.retard   .badge-dot { background:#dc2626; }

    /* Boutons d'action */
    .resa-actions { display:flex; flex-direction:column; gap:8px; align-items:center; flex-shrink:0; }

    .btn-edit-resa {
      width:40px; height:40px; border-radius:10px;
      background:#eff6ff; border:1px solid #bfdbfe;
      cursor:pointer; display:flex; align-items:center; justify-content:center;
      color:#1a56db; transition:background .18s, transform .15s;
    }
    .btn-edit-resa:hover { background:#dbeafe; transform:scale(1.08); }
    .btn-edit-resa .material-symbols-outlined { font-size:18px; }

    .btn-del-resa {
      width:40px; height:40px; border-radius:10px;
      background:#fff5f5; border:1px solid #fecaca;
      cursor:pointer; display:flex; align-items:center; justify-content:center;
      color:#dc2626; transition:background .18s, transform .15s;
    }
    .btn-del-resa:hover { background:#fee2e2; transform:scale(1.08); }
    .btn-del-resa .material-symbols-outlined { font-size:18px; }

    /* État vide */
    .empty-cart { text-align:center; padding:80px 20px; color:#9ca3af; }
    .empty-cart .material-symbols-outlined { font-size:64px; display:block; margin-bottom:16px; color:#d1d5db; }
    .empty-cart h3 { font-family:'Manrope',sans-serif; font-size:20px; color:#374151; margin-bottom:8px; }
    .empty-cart p  { font-size:14px; margin-bottom:24px; }
    .btn-go-catalogue {
      display:inline-flex; align-items:center; gap:8px;
      padding:11px 24px; background:#1a56db; color:#fff;
      border-radius:9px; text-decoration:none;
      font-weight:700; font-size:14px; font-family:'Inter',sans-serif;
    }
    .btn-go-catalogue:hover { background:#1648c0; }

    /* Modale */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:16px; padding:32px; width:520px; max-width:95vw; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.18); animation:modalIn .22s ease; }
    @keyframes modalIn { from{opacity:0;transform:translateY(-16px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }
    .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
    .modal-header h2 { font-size:19px; font-weight:800; color:#111827; }
    .modal-close { width:32px; height:32px; border-radius:8px; background:#f3f4f6; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#6b7280; }
    .modal-close:hover { background:#e5e7eb; }
    .modal-field { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
    .modal-field label { font-size:12px; font-weight:600; color:#6b7280; }
    .modal-input { width:100%; padding:10px 13px; background:#f5f7fa; border:1px solid #e5e7eb; border-radius:8px; font-size:13.5px; font-family:'Inter',sans-serif; color:#111827; outline:none; transition:border-color .18s,box-shadow .18s; }
    .modal-input:focus { border-color:#1a56db; box-shadow:0 0 0 3px rgba(26,86,219,.10); }
    .modal-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; }
    .btn-cancel-modal { padding:10px 20px; border-radius:8px; background:#fff; border:1px solid #e5e7eb; font-size:13.5px; font-weight:600; color:#374151; cursor:pointer; font-family:'Inter',sans-serif; }
    .btn-save-modal   { padding:10px 24px; border-radius:8px; background:#1a56db; color:#fff; border:none; font-size:13.5px; font-weight:700; font-family:'Inter',sans-serif; cursor:pointer; }
    .btn-save-modal:hover { background:#1648c0; }

    .delivery-grid-modal { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .delivery-opt-modal { display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:9px; cursor:pointer; border:2px solid transparent; transition:all .18s; background:#f5f7fa; }
    .delivery-opt-modal.selected { background:#fff; border-color:#1a56db; }
    .delivery-opt-modal input[type="radio"] { display:none; }
    .opt-title-modal { font-size:12.5px; font-weight:700; display:block; }
    .opt-title-modal.blue { color:#1a56db; }
    .opt-title-modal.gray { color:#374151; }
    .opt-sub-modal { font-size:11px; color:#9ca3af; }
  </style>
</head>
<body style="background:#f5f7fa;min-height:100vh;">

<!-- ══ TOP NAV ══ -->
<nav class="topnav">
  <a class="topnav-brand" href="/projet web/view/Frontoffice/catalogue.php">MediFlow Rental</a>
  <div class="topnav-links">
    <a href="/projet web/view/Frontoffice/catalogue.php">Catalog</a>
    <a href="#">Support</a>
    <a href="/projet web/view/Frontoffice/mes-reservations.php" class="active">My Rentals</a>
  </div>
  <div class="topnav-actions">
    <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
    <!-- Panier avec compteur -->
    <a href="/projet web/view/Frontoffice/mes-reservations.php"
       style="position:relative;display:flex;align-items:center;justify-content:center;
              width:34px;height:34px;border-radius:50%;background:#eff6ff;text-decoration:none;color:#1a56db;">
      <span class="material-symbols-outlined" style="font-size:20px;">shopping_cart</span>
      <?php if ($totalReservations > 0): ?>
        <span style="position:absolute;top:-4px;right:-4px;width:18px;height:18px;
                     background:#dc2626;color:#fff;border-radius:50%;
                     font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">
          <?= $totalReservations ?>
        </span>
      <?php endif; ?>
    </a>
    <div class="nav-avatar" style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
      <span class="material-symbols-outlined" style="font-size:20px;color:#1a56db;">person</span>
    </div>
  </div>
</nav>

<!-- ══ CONTENU ══ -->
<div class="reservations-wrap">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 class="page-title">
        <span class="material-symbols-outlined" style="font-size:28px;vertical-align:middle;margin-right:8px;">shopping_cart</span>
        Mes Réservations
      </h1>
      <p class="page-subtitle">
        Gérez vos locations — <strong><?= count($reservations) ?></strong> réservation(s) au total
      </p>
    </div>
    <a href="/projet web/view/Frontoffice/catalogue.php"
       style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;
              background:#1a56db;color:#fff;border-radius:9px;text-decoration:none;
              font-weight:700;font-size:13.5px;">
      <span class="material-symbols-outlined" style="font-size:17px;">add</span>
      Nouvelle réservation
    </a>
  </div>

  <?php if (empty($reservations)): ?>
    <div class="empty-cart">
      <span class="material-symbols-outlined">shopping_cart</span>
      <h3>Votre panier est vide</h3>
      <p>Vous n'avez aucune réservation en cours.</p>
      <a class="btn-go-catalogue" href="/projet web/view/Frontoffice/catalogue.php">
        <span class="material-symbols-outlined" style="font-size:17px;">medical_services</span>
        Parcourir le catalogue
      </a>
    </div>

  <?php else: ?>

    <?php foreach ($reservations as $r):
      $prixDT   = number_format((float)($r['prix_jour'] ?? 0) * EUR_TO_DT, 3, ',', '.');
      $badgeCls = getBadgeClass($r['statut']);
      $badgeLbl = getBadgeLabel($r['statut']);
      $rJson    = htmlspecialchars(json_encode($r), ENT_QUOTES);

      //  Image depuis Assets/images/equipements/
      $imgUrl = getImageUrlMR($r['reference'] ?? '');
    ?>

    <div class="resa-card" id="resa-<?= $r['id'] ?>">

      <!-- Image équipement -->
      <img class="resa-img"
           src="<?= $imgUrl ?>"
           alt="<?= htmlspecialchars($r['equipement_nom'] ?? '') ?>"
           onerror="this.style.background='#f3f4f6'"/>

      <!-- Infos équipement -->
      <div class="resa-info">
        <span class="resa-ref"><?= htmlspecialchars($r['reference'] ?? '') ?></span>
        <div class="resa-nom"><?= htmlspecialchars($r['equipement_nom'] ?? '—') ?></div>
        <div class="resa-cat"><?= htmlspecialchars($r['categorie'] ?? '—') ?></div>
        <div class="resa-prix"><?= $prixDT ?> DT / jour</div>
      </div>

      <!-- Dates -->
      <div class="resa-dates">
        <span class="resa-date-label">Période</span>
        <span class="resa-date-val"><?= fmtDate($r['date_debut']) ?></span>
        <span class="resa-date-arrow">↓</span>
        <span class="resa-date-val" style="<?= $r['statut']==='en_retard'?'color:#dc2626;font-weight:700;':'' ?>">
          <?= $r['date_fin'] ? fmtDate($r['date_fin']) : 'En cours' ?>
        </span>
      </div>

      <!-- Locataire -->
      <div class="resa-locataire">
        <div class="nom"><?= htmlspecialchars($r['locataire_nom']) ?></div>
        <div class="ville"><?= htmlspecialchars($r['locataire_ville'] ?? '') ?></div>
        <div class="tel"><?= htmlspecialchars($r['telephone'] ?? '') ?></div>
      </div>

      <!-- Badge statut -->
      <span class="badge <?= $badgeCls ?>">
        <span class="badge-dot"></span><?= $badgeLbl ?>
      </span>

      <!--  Boutons MODIFIER et SUPPRIMER -->
      <div class="resa-actions">

        <!-- ✏️ Modifier -->
        <button class="btn-edit-resa"
                type="button"
                title="Modifier cette réservation"
                onclick='ouvrirModaleModifier(<?= $rJson ?>)'>
          <span class="material-symbols-outlined">edit</span>
        </button>

        <!-- 🗑️ Supprimer -->
        <button class="btn-del-resa"
                type="button"
                title="Supprimer cette réservation"
                onclick="supprimerReservation(<?= (int)$r['id'] ?>, '<?= htmlspecialchars($r['equipement_nom'] ?? '', ENT_QUOTES) ?>')">
          <span class="material-symbols-outlined">delete</span>
        </button>

      </div>
    </div>

    <?php endforeach; ?>
  <?php endif; ?>

</div>

<!-- ══ MODALE MODIFIER ══ -->
<div id="modal-modifier" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>✏️ Modifier la Réservation</h2>
      <button class="modal-close" id="modal-close-btn" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <input type="hidden" id="mod-id"/>
    <input type="hidden" id="mod-equipement-id"/>

    <!-- Équipement (lecture seule) -->
    <div class="modal-field">
      <label>Équipement</label>
      <div id="mod-equip-nom"
           style="padding:10px 13px;background:#f9fafb;border-radius:8px;
                  font-weight:600;color:#374151;font-size:13.5px;border:1px solid #e5e7eb;"></div>
    </div>

    <!-- Dates -->
    <div class="modal-row">
      <div class="modal-field">
        <label for="mod-debut">Date de début <span style="color:#dc2626;">*</span></label>
        <!--  PAS de min / required → validé par JS -->
        <input id="mod-debut" class="modal-input" type="date"/>
      </div>
      <div class="modal-field">
        <label for="mod-fin">Date de fin</label>
        <input id="mod-fin" class="modal-input" type="date"/>
      </div>
    </div>

    <!-- Livraison -->
    <div class="modal-field">
      <label>Option de livraison</label>
      <div class="delivery-grid-modal">
        <label class="delivery-opt-modal selected" id="opt-livraison-modal">
          <input type="radio" name="mod-delivery" value="livraison" checked/>
          <div>
            <span class="opt-title-modal blue">Livraison &amp; Installation</span>
            <span class="opt-sub-modal">À domicile (Inclus)</span>
          </div>
        </label>
        <label class="delivery-opt-modal" id="opt-retrait-modal">
          <input type="radio" name="mod-delivery" value="retrait"/>
          <div>
            <span class="opt-title-modal gray">Retrait en clinique</span>
            <span class="opt-sub-modal">Sous 24h (Gratuit)</span>
          </div>
        </label>
      </div>
    </div>

    <!-- Prénom + Nom -->
    <div class="modal-row">
      <div class="modal-field">
        <label for="mod-prenom">Prénom <span style="color:#dc2626;">*</span></label>
        <!--  type="text" — PAS de required / pattern -->
        <input id="mod-prenom" class="modal-input" type="text" placeholder="Mohamed"/>
      </div>
      <div class="modal-field">
        <label for="mod-nom">Nom <span style="color:#dc2626;">*</span></label>
        <input id="mod-nom" class="modal-input" type="text" placeholder="Ben Ali"/>
      </div>
    </div>

    <!-- Téléphone -->
    <div class="modal-field">
      <label for="mod-tel">Téléphone <small style="color:#9ca3af;">(optionnel)</small></label>
      <!--  type="text" — PAS type="tel" -->
      <input id="mod-tel" class="modal-input" type="text" placeholder="20 123 456"/>
    </div>

    <!-- Ville -->
    <div class="modal-field">
      <label for="mod-ville">Ville</label>
      <input id="mod-ville" class="modal-input" type="text" placeholder="Tunis"/>
    </div>

    <div class="modal-footer">
      <button class="btn-cancel-modal" id="modal-cancel-btn" type="button">Annuler</button>
      <button class="btn-save-modal"   id="modal-save-btn"   type="button">
        <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;">save</span>
        Enregistrer
      </button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script src="/projet web/Assets/materiel.js"></script>
<script>
  const API_RES = '/projet web/controller/ReservationController.php';

  /* ── Ouvrir modale modifier ── */
  function ouvrirModaleModifier(r) {
    document.getElementById('mod-id').value            = r.id;
    document.getElementById('mod-equipement-id').value = r.equipement_id;
    document.getElementById('mod-equip-nom').textContent = (r.reference || '') + ' — ' + (r.equipement_nom || '');
    document.getElementById('mod-debut').value  = r.date_debut || '';
    document.getElementById('mod-fin').value    = r.date_fin   || '';
    const parts = (r.locataire_nom || '').split(' ');
    document.getElementById('mod-prenom').value = parts[0] || '';
    document.getElementById('mod-nom').value    = parts.slice(1).join(' ') || '';
    document.getElementById('mod-tel').value    = r.telephone       || '';
    document.getElementById('mod-ville').value  = r.locataire_ville || '';
    document.querySelectorAll('.msg-erreur').forEach(e => e.remove());
    document.querySelectorAll('.modal-input').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });
    document.getElementById('modal-modifier').classList.add('open');
  }

  /* ── Fermer modale ── */
  function fermerModale() {
    document.getElementById('modal-modifier').classList.remove('open');
  }
  document.getElementById('modal-close-btn').addEventListener('click', fermerModale);
  document.getElementById('modal-cancel-btn').addEventListener('click', fermerModale);
  document.getElementById('modal-modifier').addEventListener('click', function(e) {
    if (e.target === this) fermerModale();
  });

  /* ── Options livraison dans modale ── */
  document.querySelectorAll('.delivery-opt-modal').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.delivery-opt-modal').forEach(o => {
        o.classList.remove('selected');
        const t = o.querySelector('.opt-title-modal');
        if (t) { t.classList.remove('blue'); t.classList.add('gray'); }
      });
      opt.classList.add('selected');
      const t = opt.querySelector('.opt-title-modal');
      if (t) { t.classList.add('blue'); t.classList.remove('gray'); }
    });
  });

  /* ── Afficher erreur ── */
  function afficherErr(id, msg) {
    const input = document.getElementById(id);
    if (!input) return;
    input.style.borderColor = '#dc2626';
    input.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.10)';
    const old = input.parentElement.querySelector('.msg-erreur');
    if (old) old.remove();
    const span = document.createElement('small');
    span.className   = 'msg-erreur';
    span.textContent = '⚠ ' + msg;
    span.style.cssText = 'color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';
    input.insertAdjacentElement('afterend', span);
  }

  /* ── Valider formulaire modification (JS — pas HTML5) ── */
  function validerModification() {
    let ok = true;
    document.querySelectorAll('.msg-erreur').forEach(e => e.remove());
    document.querySelectorAll('.modal-input').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });

    const prenom = document.getElementById('mod-prenom').value.trim();
    const nom    = document.getElementById('mod-nom').value.trim();
    const debut  = document.getElementById('mod-debut').value;
    const fin    = document.getElementById('mod-fin').value;
    const tel    = document.getElementById('mod-tel').value.trim();

    if (!prenom || !/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)) {
      afficherErr('mod-prenom', 'Prénom invalide (lettres uniquement, min 2 caractères).'); ok = false;
    }
    if (!nom || !/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom)) {
      afficherErr('mod-nom', 'Nom invalide (lettres uniquement, min 2 caractères).'); ok = false;
    }
    if (!debut) {
      afficherErr('mod-debut', 'La date de début est obligatoire.'); ok = false;
    }
    if (fin && debut && fin <= debut) {
      afficherErr('mod-fin', 'La date de fin doit être après la date de début.'); ok = false;
    }
    if (tel && !/^[2345789]\d{7}$/.test(tel.replace(/\s/g,''))) {
      afficherErr('mod-tel', 'Format tunisien : 8 chiffres (ex: 20123456).'); ok = false;
    }
    if (!ok) showToast('Veuillez corriger les erreurs.', 'error');
    return ok;
  }

  /* ── Enregistrer modification (PUT) ── */
  document.getElementById('modal-save-btn').addEventListener('click', async () => {
    if (!validerModification()) return;
    const id     = document.getElementById('mod-id').value;
    const eqId   = document.getElementById('mod-equipement-id').value;
    const prenom = document.getElementById('mod-prenom').value.trim();
    const nom    = document.getElementById('mod-nom').value.trim();
    const data   = {
      equipement_id:   eqId,
      locataire_nom:   prenom + ' ' + nom,
      locataire_ville: document.getElementById('mod-ville').value.trim(),
      telephone:       document.getElementById('mod-tel').value.trim(),
      date_debut:      document.getElementById('mod-debut').value,
      date_fin:        document.getElementById('mod-fin').value || null,
      statut:          'en_cours',
    };
    try {
      const res  = await fetch(`${API_RES}?id=${id}`, {
        method: 'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)
      });
      const json = await res.json();
      if (json.success) {
        showToast(' Réservation modifiée avec succès !', 'success');
        fermerModale();
        setTimeout(() => location.reload(), 1400);
      } else {
        showToast('Erreur : ' + (json.message || 'Inconnue'), 'error');
      }
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  });

  /* ── Supprimer (DELETE) ── */
  async function supprimerReservation(id, nom) {
    if (!confirm(' Supprimer la réservation de "' + nom + '" ?\nCette action est irréversible.')) return;
    try {
      const res  = await fetch(`${API_RES}?id=${id}`, { method:'DELETE' });
      const json = await res.json();
      if (json.success) {
        showToast('Réservation supprimée.', 'success');
        const card = document.getElementById('resa-' + id);
        if (card) {
          card.style.transition = 'opacity .3s, transform .3s';
          card.style.opacity    = '0';
          card.style.transform  = 'translateX(20px)';
          setTimeout(() => card.remove(), 300);
        }
      } else {
        showToast('Erreur : ' + (json.message || 'Inconnue'), 'error');
      }
    } catch(e) { showToast('Erreur réseau.', 'error'); }
  }

  /* ── Effacer erreurs en temps réel ── */
  ['mod-prenom','mod-nom','mod-debut','mod-fin','mod-tel','mod-ville'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => {
      const input = document.getElementById(id);
      if (input) { input.style.borderColor=''; input.style.boxShadow=''; }
      input?.parentElement?.querySelector('.msg-erreur')?.remove();
    });
  });
</script>
</body>
</html>