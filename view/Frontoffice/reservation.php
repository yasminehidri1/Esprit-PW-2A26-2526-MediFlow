<?php
session_start();

$id     = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int)$_GET['id'] : 0;
$eq     = null;
$erreur = null;

if ($id <= 0) {
    $erreur = "Aucun identifiant d'équipement fourni. (ex: ?id=1)";
} else {
    try {
        require_once __DIR__ . '/../../model/Equipement.php';
        $eq = (new Equipement())->getById($id);
        if (!$eq) $erreur = "Équipement ID=$id introuvable.";
    } catch (Exception $e) {
        $erreur = "Erreur BDD : " . $e->getMessage();
    }
}

$prixDT    = $eq ? (float)$eq['prix_jour'] : 0;
$prixDTFmt = number_format($prixDT, 3, ',', '.');

function getImageUrl($eq) {
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $path = __DIR__ . '/../../Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
        if (file_exists($path))
            return '/projet%20web/Assets/images/equipements/' . $eq['reference'] . '.' . $ext;
    }
    if (!empty($eq['image']))
        return '/projet%20web/Assets/images/equipements/' . htmlspecialchars($eq['image']);
    return '';
}

$imgUrl = $eq ? getImageUrl($eq) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservation<?= $eq ? ' - ' . htmlspecialchars($eq['nom']) : '' ?> - MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet%20web/Assets/materiel.css"/>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">

<nav class="topnav">
  <a class="topnav-brand" href="/projet%20web/view/Frontoffice/catalogue.php">MediFlow Rental</a>
  <div class="topnav-links">
    <a href="/projet%20web/view/Frontoffice/catalogue.php">Catalog</a>
    <a href="#">Support</a>
    <a href="/projet%20web/view/Frontoffice/mes-reservations.php" class="active">My Rentals</a>
  </div>
  <div class="topnav-actions">
    <button class="icon-btn"><span class="material-symbols-outlined">notifications</span></button>
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

<?php if ($erreur): ?>
<main style="flex:1;padding-top:80px;display:flex;align-items:center;justify-content:center;">
  <div style="text-align:center;max-width:480px;padding:40px;">
    <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
      <span class="material-symbols-outlined" style="font-size:32px;color:#dc2626;">error_outline</span>
    </div>
    <h2 style="font-family:'Manrope',sans-serif;font-size:22px;color:#111827;margin-bottom:10px;">Équipement introuvable</h2>
    <p style="color:#6b7280;font-size:14px;margin-bottom:24px;"><?= htmlspecialchars($erreur) ?></p>
    <a href="/projet%20web/view/Frontoffice/catalogue.php"
       style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;
              background:#1a56db;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:14px;">
      <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span>
      Retour au Catalogue
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

        <!-- Aperçu équipement -->
        <div class="equip-card">
          <?php if ($imgUrl): ?>
            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($eq['nom']) ?>"
                 style="width:110px;height:110px;object-fit:contain;border-radius:8px;background:#f3f4f6;padding:8px;flex-shrink:0;"
                 loading="lazy"/>
          <?php else: ?>
            <div style="width:110px;height:110px;border-radius:8px;background:#f3f4f6;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
          <?php endif; ?>
          <div class="equip-info">
            <span class="ref">Réf: <?= htmlspecialchars($eq['reference']) ?></span>
            <h2><?= htmlspecialchars($eq['nom']) ?></h2>
            <p class="desc"><?= htmlspecialchars($eq['categorie']) ?></p>
            <div class="price" id="daily-rate" data-rate="<?= $prixDT ?>" data-taux="1">
              <?= $prixDTFmt ?> DT / jour
            </div>
          </div>
        </div>

        <div class="form-card">

          <!-- Dates -->
          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">calendar_today</span>
              <h3>Période de location</h3>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="date-start">Date de début <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="date-start" type="date"/>
              </div>
              <div class="form-group">
                <label for="date-end">Date de fin (estimée) <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="date-end" type="date"/>
              </div>
            </div>
          </div>

          <!-- Options livraison -->
          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">local_shipping</span>
              <h3>Options de livraison</h3>
            </div>
            <div class="delivery-grid">
              <label class="delivery-opt selected" id="opt-livraison">
                <input type="radio" name="delivery" value="livraison" checked/>
                <div class="opt-text">
                  <span class="opt-title blue">Livraison &amp; Installation</span>
                  <span class="opt-sub">À domicile par nos techniciens (Inclus)</span>
                </div>
                <span class="material-symbols-outlined opt-icon filled">check_circle</span>
              </label>
              <label class="delivery-opt unselected" id="opt-retrait">
                <input type="radio" name="delivery" value="retrait"/>
                <div class="opt-text">
                  <span class="opt-title gray">Retrait en clinique</span>
                  <span class="opt-sub">Disponible sous 24h (Gratuit)</span>
                </div>
                <span class="material-symbols-outlined opt-icon unfilled">radio_button_unchecked</span>
              </label>
            </div>
          </div>

          <!-- Adresse livraison -->
          <div class="form-section" id="adresse-section">
            <div class="section-title">
              <span class="material-symbols-outlined">location_on</span>
              <h3>Adresse de livraison <span style="color:#dc2626;">*</span></h3>
            </div>
            <div class="form-group">
              <label for="adresse-livraison">
                Votre adresse complète
                <span style="color:#dc2626;font-size:11px;"> (obligatoire pour la livraison)</span>
              </label>
              <input class="form-input" id="adresse-livraison" type="text"
                     placeholder="Ex: 12 Rue de la République, Tunis"/>
              <small style="color:#6b7280;font-size:11px;margin-top:4px;display:block;">
                <span class="material-symbols-outlined" style="font-size:13px;vertical-align:middle;">info</span>
                Nos techniciens livreront l'équipement à cette adresse.
              </small>
            </div>
          </div>

          <!-- Contact -->
          <div class="form-section">
            <div class="section-title">
              <span class="material-symbols-outlined">person</span>
              <h3>Informations de contact</h3>
            </div>
            <div class="form-row" style="margin-bottom:14px;">
              <div class="form-group">
                <label for="firstname">Prénom <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="firstname" type="text" placeholder="Mohamed"/>
              </div>
              <div class="form-group">
                <label for="lastname">Nom <span style="color:#dc2626;">*</span></label>
                <input class="form-input" id="lastname" type="text" placeholder="Ben Ali"/>
              </div>
            </div>
            <div class="form-row full">
              <div class="form-group">
                <label for="phone">Téléphone <span style="color:#6b7280;font-size:11px;">(optionnel)</span></label>
                <input class="form-input" id="phone" type="text" placeholder="20 123 456"/>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Récapitulatif -->
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
        <a class="help-link" href="#">
          <span class="material-symbols-outlined">help</span>
          Besoin d'aide pour votre dossier ?
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
<script>
/* ══════════════════════════════════════════
   showToast — copié ici pour éviter materiel.js
   qui ajoutait un 2ème listener sur btn-confirm
══════════════════════════════════════════ */
function showToast(message, type) {
    type = type || 'info';
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const icons = { success: 'check_circle', error: 'error', info: 'info' };
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML =
        '<span class="material-symbols-outlined">' + (icons[type]||'info') + '</span>' +
        '<span>' + message + '</span>';
    container.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'all .3s ease';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3500);
}
</script>
<script>
/* ═══════════════════════════════════════════════════════
   RESERVATION.PHP — Logique complète
   Corrections :
   ✅ Anti double-clic (un seul envoi possible)
   ✅ Option livraison reste sur celle cochée (pas de reset)
   ✅ Toast sans emoji tick vert
═══════════════════════════════════════════════════════ */

const API_RES = '/projet%20web/controller/ReservationController.php';
const prixDT  = <?= $prixDT ?>;

/* ── Calcul total ── */
function nbJours(d1, d2) {
    if (!d1 || !d2) return 0;
    return Math.ceil((new Date(d2) - new Date(d1)) / 86400000);
}
function formatDT(val) {
    return val.toLocaleString('fr-TN', { minimumFractionDigits:3, maximumFractionDigits:3 }) + ' DT';
}
function updateTotal() {
    const debut = document.getElementById('date-start').value;
    const fin   = document.getElementById('date-end').value;
    const jours = nbJours(debut, fin);
    const total = document.getElementById('total-amount');
    const durRow= document.getElementById('duration-row');
    if (!debut || !fin || jours <= 0) {
        total.textContent = '—';
        durRow.querySelector('.lbl').textContent = 'Location';
        durRow.querySelector('.val').textContent = '—';
        return;
    }
    const t = jours * prixDT;
    total.textContent = formatDT(t);
    durRow.querySelector('.lbl').textContent = `Location (${jours} jour${jours>1?'s':''})`;
    durRow.querySelector('.val').textContent = formatDT(t);
}
document.getElementById('date-start')?.addEventListener('change', updateTotal);
document.getElementById('date-end')?.addEventListener('change',   updateTotal);

/* ── Gestion options livraison ──
   ✅ FIX : on met à jour uniquement le style visuel
   sans toucher à l'état coché du radio input
   → l'option reste sur celle que l'utilisateur a choisie
── */
const optLiv = document.getElementById('opt-livraison');
const optRet = document.getElementById('opt-retrait');
const adresseSection = document.getElementById('adresse-section');
const adresseInput   = document.getElementById('adresse-livraison');

function appliquerStyleLivraison(isLivraison) {
    if (isLivraison) {
        // Livraison sélectionnée
        optLiv.classList.add('selected');    optLiv.classList.remove('unselected');
        optRet.classList.remove('selected'); optRet.classList.add('unselected');
        optLiv.querySelector('.opt-title').classList.replace('gray','blue');
        optRet.querySelector('.opt-title').classList.replace('blue','gray');
        optLiv.querySelector('.opt-icon').textContent = 'check_circle';
        optRet.querySelector('.opt-icon').textContent = 'radio_button_unchecked';
        // Afficher adresse
        adresseSection.style.display  = 'block';
        adresseSection.style.opacity  = '1';
    } else {
        // Retrait sélectionné
        optRet.classList.add('selected');    optRet.classList.remove('unselected');
        optLiv.classList.remove('selected'); optLiv.classList.add('unselected');
        optRet.querySelector('.opt-title').classList.replace('gray','blue');
        optLiv.querySelector('.opt-title').classList.replace('blue','gray');
        optRet.querySelector('.opt-icon').textContent = 'check_circle';
        optLiv.querySelector('.opt-icon').textContent = 'radio_button_unchecked';
        // Cacher adresse + vider
        adresseSection.style.opacity = '0';
        setTimeout(() => {
            adresseSection.style.display = 'none';
            adresseInput.value = '';
            effacerErr('adresse-livraison');
        }, 200);
    }
}

/* ✅ FIX : écouter le clic sur le LABEL (pas le radio)
   → met à jour le radio ET le style en même temps
   → évite tout désynchronisation */
optLiv.addEventListener('click', () => {
    document.querySelector('input[name="delivery"][value="livraison"]').checked = true;
    appliquerStyleLivraison(true);
});
optRet.addEventListener('click', () => {
    document.querySelector('input[name="delivery"][value="retrait"]').checked = true;
    appliquerStyleLivraison(false);
});

// Initialisation — livraison par défaut
appliquerStyleLivraison(true);

/* ── Erreurs ── */
function afficherErr(id, msg) {
    const input = document.getElementById(id);
    if (!input) return;
    input.style.borderColor = '#dc2626';
    input.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.10)';
    input.parentElement.querySelector('.msg-erreur')?.remove();
    const span = document.createElement('small');
    span.className   = 'msg-erreur';
    span.textContent = '⚠ ' + msg;
    span.style.cssText = 'color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';
    input.insertAdjacentElement('afterend', span);
}
function effacerErr(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.borderColor = '';
    el.style.boxShadow   = '';
    el.parentElement?.querySelector('.msg-erreur')?.remove();
}

['firstname','lastname','phone','date-start','date-end','adresse-livraison'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => effacerErr(id));
});

/* ── Validation ── */
function valider() {
    let ok = true;
    document.querySelectorAll('.msg-erreur').forEach(e => e.remove());
    document.querySelectorAll('.form-input').forEach(i => { i.style.borderColor=''; i.style.boxShadow=''; });

    const today   = new Date().toISOString().split('T')[0];
    const prenom  = document.getElementById('firstname').value.trim();
    const nom     = document.getElementById('lastname').value.trim();
    const tel     = document.getElementById('phone').value.trim();
    const debut   = document.getElementById('date-start').value;
    const fin     = document.getElementById('date-end').value;
    const isLiv   = document.querySelector('input[name="delivery"]:checked')?.value === 'livraison';
    const adresse = adresseInput.value.trim();

    if (!prenom || !/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)) { afficherErr('firstname','Prénom invalide (lettres uniquement, min 2).'); ok=false; }
    if (!nom    || !/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom))    { afficherErr('lastname', 'Nom invalide (lettres uniquement, min 2).'); ok=false; }
    if (tel && !/^[2345789]\d{7}$/.test(tel.replace(/\s/g,''))) { afficherErr('phone','Format tunisien : 8 chiffres (ex: 20123456).'); ok=false; }
    if (!debut) { afficherErr('date-start','La date de début est obligatoire.'); ok=false; }
    else if (debut < today) { afficherErr('date-start','La date ne peut pas être dans le passé.'); ok=false; }
    if (!fin)   { afficherErr('date-end','La date de fin est obligatoire.'); ok=false; }
    else if (debut && fin <= debut) { afficherErr('date-end','La date de fin doit être après la date de début.'); ok=false; }
    if (isLiv && adresse.length < 5) { afficherErr('adresse-livraison',"L'adresse de livraison est obligatoire (min 5 caractères)."); ok=false; }

    if (!ok) showToast('Veuillez corriger les erreurs dans le formulaire.', 'error');
    return ok;
}

/* ── Soumission — ANTI DOUBLE CLIC ──
   ✅ FIX principal : variable `enCours` empêche tout double envoi
   Si le bouton est déjà en train d'envoyer → on sort immédiatement
── */
let enCours = false; // ✅ Verrou anti double-clic

document.getElementById('btn-confirm').addEventListener('click', async function () {

    // ✅ Bloquer si déjà en cours d'envoi
    if (enCours) return;

    if (!valider()) return;

    // ✅ Activer le verrou + désactiver le bouton
    enCours = true;
    const btn = this;
    btn.disabled    = true;
    btn.textContent = 'Envoi en cours...';

    const isLivraison = document.querySelector('input[name="delivery"]:checked')?.value === 'livraison';

    const payload = {
        equipement_id:   document.getElementById('equipement_id').value,
        locataire_nom:   document.getElementById('firstname').value.trim() + ' ' + document.getElementById('lastname').value.trim(),
        locataire_ville: isLivraison ? adresseInput.value.trim() : '',
        date_debut:      document.getElementById('date-start').value,
        date_fin:        document.getElementById('date-end').value,
        telephone:       document.getElementById('phone').value.trim(),
        statut:          'en_cours',
    };

    try {
        const res  = await fetch(API_RES, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload),
        });
        const json = await res.json();

        if (json.success) {
            // ✅ Toast sans emoji tick vert
            showToast('Réservation confirmée avec succès !', 'success');
            // Redirection après 1.8s — le verrou reste actif pour éviter tout re-clic
            setTimeout(() => {
                window.location.href = '/projet%20web/view/Frontoffice/catalogue.php';
            }, 1800);
        } else {
            showToast('Erreur : ' + (json.message || 'Inconnue'), 'error');
            // Libérer le verrou seulement en cas d'erreur
            enCours         = false;
            btn.disabled    = false;
            btn.textContent = 'Confirmer la réservation';
        }
    } catch(e) {
        showToast('Erreur réseau. Vérifiez que XAMPP est actif.', 'error');
        enCours         = false;
        btn.disabled    = false;
        btn.textContent = 'Confirmer la réservation';
    }
});
</script>
</body>
</html>