<?php
$eq     = $data['eq']     ?? null;
$erreur = $data['erreur'] ?? null;
$user   = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
$prixDT = $eq ? (float)$eq['prix_jour'] : 0;
$prixDTFmt = number_format($prixDT, 3, ',', '.');

// ✅ Même logique que catalogue.php — utilise __DIR__ relatif
function getEqImageUrl($eq): string {
    if (!$eq) return '';
    // Views/Front/ → ../../assets/ = integration/assets/
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
    // Fallback sur le champ image en BDD
    if (!empty($eq['image'])) {
        return '/integration/assets/images/equipements/' . htmlspecialchars($eq['image']);
    }
    return '';
}
$imgUrl = $eq ? getEqImageUrl($eq) : '';

?>
<html lang="fr" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservation — MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/integration/assets/css/style.css"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","primary-fixed":"#d6e3ff","primary-container":"#1565c0","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-dim":"#d8dadc","outline":"#727783","on-surface":"#191c1e","on-surface-variant":"#424752"},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"},fontFamily:{headline:["Manrope"],body:["Inter"]}}}}
  </script>
  <style>
    .form-card{background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:28px;margin-bottom:20px;}
    .section-title{display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;}
    .section-title h3{font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .form-group{display:flex;flex-direction:column;gap:5px;}
    .form-group label{font-size:12px;font-weight:600;color:#6b7280;}
    .form-input{width:100%;padding:10px 13px;background:#f5f7fa;border:1px solid #e5e7eb;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#111827;outline:none;transition:border-color .18s,box-shadow .18s;}
    .form-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.10);}
    .delivery-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .delivery-opt{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;cursor:pointer;border:2px solid transparent;transition:all .18s;}
    .delivery-opt.selected{background:#eff6ff;border-color:#004d99;}
    .delivery-opt.unselected{background:#f9fafb;border-color:#e5e7eb;}
    .delivery-opt input[type="radio"]{display:none;}
    .opt-title{font-size:13px;font-weight:700;display:block;}
    .opt-title.blue{color:#004d99;}
    .opt-title.gray{color:#374151;}
    .opt-sub{font-size:11px;color:#9ca3af;}
    .opt-icon{font-size:20px;}
    .opt-icon.filled{color:#004d99;}
    .opt-icon.unfilled{color:#d1d5db;}
    .summary-card{background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:24px;position:sticky;top:90px;}
    .summary-card h3{font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#111827;margin-bottom:18px;}
    .summary-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13.5px;}
    .summary-row .lbl{color:#6b7280;}
    .summary-row .val{font-weight:700;color:#111827;}
    .summary-row .val.free{color:#16a34a;}
    .summary-divider{border:none;border-top:2px solid #e8eaf0;margin:12px 0;}
    .total-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
    .total-amount{font-family:'Manrope',sans-serif;font-size:26px;font-weight:900;color:#004d99;margin-bottom:18px;}
    .btn-confirm{width:100%;padding:14px;border-radius:10px;background:#004d99;color:#fff;border:none;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:background .18s;}
    .btn-confirm:hover:not(:disabled){background:#00357a;}
    .btn-confirm:disabled{opacity:.6;cursor:not-allowed;}
    .confirm-legal{font-size:11px;color:#9ca3af;text-align:center;margin-top:12px;}
    .tip-card{display:flex;gap:10px;background:#eff6ff;border-radius:10px;padding:12px;margin-top:14px;}
    .tip-card .material-symbols-outlined{font-size:18px;color:#004d99;flex-shrink:0;}
    .tip-title{font-size:12px;font-weight:700;color:#004d99;display:block;}
    .tip-body{font-size:11px;color:#6b7280;}
    .equip-card{display:flex;gap:16px;align-items:center;background:#fff;border-radius:14px;border:1px solid #e8eaf0;padding:20px;margin-bottom:20px;}
    .equip-info .ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.06em;display:block;}
    .equip-info h2{font-family:'Manrope',sans-serif;font-size:18px;font-weight:900;color:#111827;}
    .equip-info .desc{font-size:13px;color:#9ca3af;}
    .equip-info .price{font-size:16px;font-weight:700;color:#004d99;margin-top:4px;}
    .stepper{display:flex;align-items:center;gap:0;margin-bottom:24px;}
    .step{display:flex;align-items:center;gap:8px;}
    .step-circle{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;}
    .step-circle.active{background:#004d99;color:#fff;}
    .step-circle.inactive{background:#e5e7eb;color:#9ca3af;}
    .step-label{font-size:12.5px;font-weight:600;}
    .step-label.active{color:#004d99;}
    .step-label.inactive{color:#9ca3af;}
    .step-line{flex:1;height:2px;background:#e5e7eb;margin:0 8px;}
    .content-grid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;}
    .toast-container{position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;}
    .toast{display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:10px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13.5px;font-weight:600;font-family:'Inter',sans-serif;animation:toastIn .3s ease;}
    .toast.success{border-left:4px solid #16a34a;color:#15803d;}
    .toast.error{border-left:4px solid #dc2626;color:#dc2626;}
    .toast.info{border-left:4px solid #004d99;color:#004d99;}
    @keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}

    /* ✅ Message disponibilité */
    .dispo-msg {
      display: none;
      margin-top: 14px;
      padding: 14px 16px;
      border-radius: 10px;
      font-size: 13px;
      line-height: 1.6;
      animation: fadeIn .3s ease;
    }
    @keyframes fadeIn{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
    .dispo-msg.checking   { background:#f0f6ff; border:1px solid #bfdbfe; color:#1d4ed8; }
    .dispo-msg.disponible { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .dispo-msg.indispo    { background:#fff7ed; border:1px solid #fed7aa; color:#c2410c; }
    .dispo-msg-header { display:flex;align-items:center;gap:8px;font-weight:700;font-size:13.5px;margin-bottom:5px; }
    .dispo-msg-header .material-symbols-outlined{font-size:18px;flex-shrink:0;}
    .dispo-msg-detail { font-size:12px; font-weight:500; opacity:.9; }
    .dispo-spinner { width:14px;height:14px;border:2px solid #bfdbfe;border-top-color:#1d4ed8;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0; }
    @keyframes spin{to{transform:rotate(360deg)}}
  </style>
</head>

<div class="pt-24 pb-12 px-10">
  <?php if ($erreur): ?>
    <div class="max-w-lg mx-auto text-center py-20">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-red-500 text-3xl">error_outline</span>
      </div>
      <h2 class="text-xl font-bold mb-2">Équipement introuvable</h2>
      <p class="text-slate-500 mb-6"><?= htmlspecialchars($erreur) ?></p>
      <a href="/integration/catalogue" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold">
        <span class="material-symbols-outlined text-base">arrow_back</span> Retour au catalogue
      </a>
    </div>
  <?php else: ?>

    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent mb-2">Réservation d'équipement</h2>
    <p class="text-on-surface-variant mb-6 font-medium">Configurez votre location en quelques étapes simples.</p>

    <div class="stepper">
      <div class="step"><div class="step-circle active">1</div><span class="step-label active">Configuration</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">2</div><span class="step-label inactive">Livraison</span></div>
      <div class="step-line"></div>
      <div class="step"><div class="step-circle inactive">3</div><span class="step-label inactive">Validation</span></div>
    </div>

    <div class="content-grid">
      <div>

        <div class="equip-card">
          <?php if ($imgUrl): ?>
            <img src="<?= $imgUrl ?>"
                 alt="<?= htmlspecialchars($eq['nom']) ?>"
                 style="width:90px;height:90px;object-fit:contain;border-radius:10px;
                        background:#f3f4f6;padding:8px;flex-shrink:0;"
                 loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"/>
            <div style="width:90px;height:90px;background:#f3f4f6;border-radius:10px;
                        display:none;align-items:center;justify-content:center;flex-shrink:0;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
          <?php else: ?>
            <div style="width:90px;height:90px;background:#f3f4f6;border-radius:10px;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
          <?php endif; ?>
          <div class="equip-info">
            <span class="ref">Réf: <?= htmlspecialchars($eq['reference']) ?></span>
            <h2><?= htmlspecialchars($eq['nom']) ?></h2>
            <p class="desc"><?= htmlspecialchars($eq['categorie']) ?></p>
            <div class="price" id="daily-rate" data-rate="<?= $prixDT ?>"><?= $prixDTFmt ?> DT / jour</div>
          </div>
        </div>

        <!-- Dates + message disponibilité -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">calendar_today</span>
            <h3>Période de location</h3>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="date-start">Date de début <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="date-start" type="date"/>
            </div>
            <div class="form-group">
              <label for="date-end">Date de fin <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="date-end" type="date"/>
            </div>
          </div>

          <!--
            ✅ MESSAGE DISPONIBILITÉ
            Apparaît dès que les 2 dates sont sélectionnées
            3 états visuels :
              - checking    → spinner bleu "Vérification..."
              - disponible  → vert  "Équipement disponible"
              - indispo     → orange "Période déjà réservée + explication"
          -->
          <div id="dispo-msg" class="dispo-msg">
            <div class="dispo-msg-header" id="dispo-header"></div>
            <div class="dispo-msg-detail" id="dispo-detail"></div>
          </div>

        </div>

        <!-- Livraison -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">local_shipping</span>
            <h3>Options de livraison</h3>
          </div>
          <div class="delivery-grid">
            <label class="delivery-opt selected" id="opt-livraison">
              <input type="radio" name="delivery" value="livraison" checked/>
              <div class="opt-text">
                <span class="opt-title blue">Livraison &amp; Installation</span>
                <span class="opt-sub">À domicile (Inclus)</span>
              </div>
              <span class="material-symbols-outlined opt-icon filled">check_circle</span>
            </label>
            <label class="delivery-opt unselected" id="opt-retrait">
              <input type="radio" name="delivery" value="retrait"/>
              <div class="opt-text">
                <span class="opt-title gray">Retrait en clinique</span>
                <span class="opt-sub">Sous 24h (Gratuit)</span>
              </div>
              <span class="material-symbols-outlined opt-icon unfilled">radio_button_unchecked</span>
            </label>
          </div>
          <div id="adresse-section" style="margin-top:16px;">
            <div class="form-group">
              <label for="adresse-livraison">Adresse de livraison <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="adresse-livraison" type="text" placeholder="Ex: 12 Rue de la République, Tunis"/>
            </div>
          </div>
        </div>

        <!-- Contact -->
        <div class="form-card">
          <div class="section-title">
            <span class="material-symbols-outlined text-primary">person</span>
            <h3>Informations de contact</h3>
          </div>
          <div class="form-row" style="margin-bottom:14px;">
            <div class="form-group">
              <label for="firstname">Prénom <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="firstname" type="text" placeholder="Mohamed" value="<?= htmlspecialchars($user['prenom']??'') ?>"/>
            </div>
            <div class="form-group">
              <label for="lastname">Nom <span style="color:#dc2626;">*</span></label>
              <input class="form-input" id="lastname" type="text" placeholder="Ben Ali" value="<?= htmlspecialchars($user['nom']??'') ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label for="phone">Téléphone <span style="color:#9ca3af;font-size:11px;">(optionnel)</span></label>
            <input class="form-input" id="phone" type="text" placeholder="20 123 456" value="<?= htmlspecialchars($user['tel']??'') ?>"/>
          </div>
        </div>

      </div>

      <aside class="summary-card">
        <h3>Récapitulatif</h3>
        <input type="hidden" id="equipement_id" value="<?= (int)$eq['id'] ?>"/>
        <div class="summary-row" id="duration-row">
          <span class="lbl">Location</span><span class="val">—</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Frais de livraison</span><span class="val free">OFFERT</span>
        </div>
        <hr class="summary-divider"/>
        <div class="total-label">TOTAL TTC</div>
        <div class="total-amount" id="total-amount">—</div>
        <button class="btn-confirm" id="btn-confirm" type="button" disabled>
          Confirmer la réservation
        </button>
        <p class="confirm-legal">En confirmant, vous acceptez nos conditions générales de location médicale.</p>
        <div class="tip-card">
          <span class="material-symbols-outlined">info</span>
          <div>
            <span class="tip-title">Prise en charge CNAM</span>
            <span class="tip-body">Ce matériel est éligible au remboursement CNAM sous réserve de prescription médicale valide.</span>
          </div>
        </div>
      </aside>
    </div>

  <?php endif; ?>
</div>

<div class="toast-container"></div>
<script>
const API_RES   = '/integration/equipment/api/reservations';
const API_DISPO = '/integration/equipment/api/disponibilite';
const prixDT    = <?= $prixDT ?>;
const equipId   = <?= (int)($eq['id'] ?? 0) ?>;

/* ── Calcul total ── */
function nbJours(d1,d2){if(!d1||!d2)return 0;return Math.ceil((new Date(d2)-new Date(d1))/86400000);}
function formatDT(v){return v.toLocaleString('fr-TN',{minimumFractionDigits:3,maximumFractionDigits:3})+' DT';}
function fmtDate(d){if(!d)return '—';const[y,m,j]=d.split('-');return`${j}/${m}/${y}`;}

function updateTotal(){
  const d1=document.getElementById('date-start').value,d2=document.getElementById('date-end').value,j=nbJours(d1,d2);
  const tot=document.getElementById('total-amount'),dr=document.getElementById('duration-row');
  if(!d1||!d2||j<=0){tot.textContent='—';dr.querySelector('.lbl').textContent='Location';dr.querySelector('.val').textContent='—';return;}
  const t=j*prixDT;tot.textContent=formatDT(t);
  dr.querySelector('.lbl').textContent=`Location (${j} jour${j>1?'s':''})`;
  dr.querySelector('.val').textContent=formatDT(t);
}

/* ══════════════════════════════════════════════════════════
   ✅ VÉRIFICATION DISPONIBILITÉ EN TEMPS RÉEL
   Dès que les 2 dates sont choisies :
   1. Affiche "Vérification..." (spinner bleu)
   2. Appelle l'API de disponibilité
   3. Affiche le résultat avec un message clair
══════════════════════════════════════════════════════════ */
let dispoOk   = false;
let timerDispo = null;

function verifierDisponibilite() {
  const debut  = document.getElementById('date-start').value;
  const fin    = document.getElementById('date-end').value;
  const msg    = document.getElementById('dispo-msg');
  const header = document.getElementById('dispo-header');
  const detail = document.getElementById('dispo-detail');
  const btn    = document.getElementById('btn-confirm');

  if (!debut || !fin || fin <= debut) {
    msg.style.display = 'none';
    dispoOk = false;
    btn.disabled = true;
    return;
  }

  /* État : vérification en cours */
  msg.className = 'dispo-msg checking';
  msg.style.display = 'block';
  header.innerHTML = '<div class="dispo-spinner"></div> Vérification de la disponibilité en cours...';
  detail.textContent = '';
  btn.disabled = true;
  dispoOk = false;

  clearTimeout(timerDispo);
  timerDispo = setTimeout(async () => {
    try {
      const res  = await fetch(`${API_DISPO}?equipement_id=${equipId}&date_debut=${debut}&date_fin=${fin}`);
      const json = await res.json();

      if (json.disponible) {
        /* ✅ Disponible */
        const j = nbJours(debut, fin);
        msg.className = 'dispo-msg disponible';
        header.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Équipement disponible sur cette période';
        detail.textContent = `L'équipement est libre du ${fmtDate(debut)} au ${fmtDate(fin)} (${j} jour${j>1?'s':''}). Vous pouvez confirmer votre réservation.`;
        btn.disabled = false;
        dispoOk = true;

      } else {
        /* ❌ Période déjà réservée */
        msg.className = 'dispo-msg indispo';
        header.innerHTML = '<span class="material-symbols-outlined">event_busy</span> Cette période est déjà réservée';

        /* Message explicatif avec les dates du conflit */
        let txt = 'Cet équipement n\'est pas disponible sur les dates sélectionnées.';
        if (json.date_debut_conflit && json.date_fin_conflit) {
          txt = `Une réservation est déjà confirmée du ${fmtDate(json.date_debut_conflit)} au ${fmtDate(json.date_fin_conflit)}. Veuillez choisir d'autres dates.`;
        } else if (json.message) {
          txt = json.message;
        }
        detail.textContent = txt;
        btn.disabled = true;
        dispoOk = false;
      }

    } catch(e) {
      /* Erreur réseau → laisser passer sans bloquer */
      msg.style.display = 'none';
      btn.disabled = false;
      dispoOk = true;
    }
  }, 600);
}

document.getElementById('date-start')?.addEventListener('change', () => { updateTotal(); verifierDisponibilite(); });
document.getElementById('date-end')?.addEventListener('change',   () => { updateTotal(); verifierDisponibilite(); });

/* ── Livraison ── */
const optLiv=document.getElementById('opt-livraison'),optRet=document.getElementById('opt-retrait');
const adrsec=document.getElementById('adresse-section'),adrinp=document.getElementById('adresse-livraison');
function setDelivery(isLiv){
  optLiv.className='delivery-opt '+(isLiv?'selected':'unselected');
  optRet.className='delivery-opt '+(isLiv?'unselected':'selected');
  optLiv.querySelector('.opt-title').className='opt-title '+(isLiv?'blue':'gray');
  optRet.querySelector('.opt-title').className='opt-title '+(isLiv?'gray':'blue');
  optLiv.querySelector('.opt-icon').textContent=isLiv?'check_circle':'radio_button_unchecked';
  optRet.querySelector('.opt-icon').textContent=isLiv?'radio_button_unchecked':'check_circle';
  optLiv.querySelector('.opt-icon').className='material-symbols-outlined opt-icon '+(isLiv?'filled':'unfilled');
  optRet.querySelector('.opt-icon').className='material-symbols-outlined opt-icon '+(isLiv?'unfilled':'filled');
  adrsec.style.display=isLiv?'block':'none';
  if(!isLiv)adrinp.value='';
}
optLiv?.addEventListener('click',()=>{document.querySelector('input[value="livraison"]').checked=true;setDelivery(true);});
optRet?.addEventListener('click',()=>{document.querySelector('input[value="retrait"]').checked=true;setDelivery(false);});
setDelivery(true);

/* ── Toast ── */
function showToast(msg,type='info'){
  const c=document.querySelector('.toast-container');
  const t=document.createElement('div');t.className='toast '+type;
  const icons={success:'check_circle',error:'error',info:'info'};
  t.innerHTML=`<span class="material-symbols-outlined">${icons[type]||'info'}</span><span>${msg}</span>`;
  c.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);
}

/* ── Erreurs ── */
function afficherErr(id,msg){const el=document.getElementById(id);if(!el)return;el.style.borderColor='#dc2626';el.style.boxShadow='0 0 0 3px rgba(220,38,38,.10)';el.parentElement.querySelector('.msg-erreur')?.remove();const s=document.createElement('small');s.className='msg-erreur';s.textContent='⚠ '+msg;s.style.cssText='color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';el.insertAdjacentElement('afterend',s);}
function effacerErr(id){const el=document.getElementById(id);if(!el)return;el.style.borderColor='';el.style.boxShadow='';el.parentElement?.querySelector('.msg-erreur')?.remove();}

/* ── Validation ── */
function valider(){
  let ok=true;
  document.querySelectorAll('.msg-erreur').forEach(e=>e.remove());
  document.querySelectorAll('.form-input').forEach(i=>{i.style.borderColor='';i.style.boxShadow='';});
  const today=new Date().toISOString().split('T')[0];
  const prenom=document.getElementById('firstname')?.value.trim();
  const nom=document.getElementById('lastname')?.value.trim();
  const debut=document.getElementById('date-start')?.value;
  const fin=document.getElementById('date-end')?.value;
  const isLiv=document.querySelector('input[name="delivery"]:checked')?.value==='livraison';
  const adresse=adrinp?.value.trim();
  if(!prenom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)){afficherErr('firstname','Prénom invalide.');ok=false;}
  if(!nom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom)){afficherErr('lastname','Nom invalide.');ok=false;}
  if(!debut){afficherErr('date-start','Date de début obligatoire.');ok=false;}
  else if(debut<today){afficherErr('date-start','La date ne peut pas être dans le passé.');ok=false;}
  if(!fin){afficherErr('date-end','Date de fin obligatoire.');ok=false;}
  else if(debut&&fin<=debut){afficherErr('date-end','La date de fin doit être après le début.');ok=false;}
  if(isLiv&&(!adresse||adresse.length<5)){afficherErr('adresse-livraison','Adresse obligatoire (min 5 caractères).');ok=false;}
  if(!dispoOk){showToast('Veuillez sélectionner des dates disponibles.','error');ok=false;}
  if(!ok)showToast('Veuillez corriger les erreurs.','error');
  return ok;
}

/* ── Soumission ── */
let enCours=false;
document.getElementById('btn-confirm')?.addEventListener('click',async function(){
  if(enCours)return;if(!valider())return;
  enCours=true;const btn=this;btn.disabled=true;btn.textContent='Envoi en cours...';
  const isLiv=document.querySelector('input[name="delivery"]:checked')?.value==='livraison';
  const payload={
    equipement_id:document.getElementById('equipement_id')?.value,
    locataire_nom:(document.getElementById('firstname')?.value.trim()+' '+document.getElementById('lastname')?.value.trim()),
    locataire_ville:isLiv?(adrinp?.value.trim()||''):'',
    date_debut:document.getElementById('date-start')?.value,
    date_fin:document.getElementById('date-end')?.value,
    telephone:document.getElementById('phone')?.value.trim(),
    statut:'en_cours',
  };
  try{
    const res=await fetch(API_RES,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
    const json=await res.json();
    if(json.success){showToast('Réservation confirmée avec succès !','success');setTimeout(()=>window.location.href='/integration/mes-reservations',1800);}
    else{showToast('Erreur : '+(json.message||'Inconnue'),'error');enCours=false;btn.disabled=false;btn.textContent='Confirmer la réservation';}
  }catch(e){showToast('Erreur réseau.','error');enCours=false;btn.disabled=false;btn.textContent='Confirmer la réservation';}
});
</script>