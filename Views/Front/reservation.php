<?php
// $data['eq'], $data['erreur'], $data['currentUser'] injected by PatientEquipmentController
$eq     = $data['eq']     ?? null;
$erreur = $data['erreur'] ?? null;
$user   = $data['currentUser'] ?? ($_SESSION['user'] ?? []);
$prixDT = $eq ? (float)$eq['prix_jour'] : 0;
$prixDTFmt = number_format($prixDT, 3, ',', '.');
?>
<!DOCTYPE html>
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
    tailwind.config = {
      darkMode:"class",
      theme:{extend:{colors:{"primary":"#004d99","primary-fixed":"#d6e3ff","primary-container":"#1565c0","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-dim":"#d8dadc","outline":"#727783","on-surface":"#191c1e","on-surface-variant":"#424752"},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"},fontFamily:{headline:["Manrope"],body:["Inter"]}}}
    }
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
  </style>
</head>
<body class="bg-surface text-on-surface overflow-hidden">

<!-- SIDEBAR -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-gradient-to-b from-slate-50 to-slate-100 flex flex-col py-8 space-y-6 z-50 border-r border-outline shadow-xl">
  <div class="px-8">
    <h1 class="text-2xl font-black tracking-tight bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">MediFlow</h1>
    <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-1">Soins de santé</p>
  </div>
  <nav class="flex-1 flex flex-col space-y-2 px-4">
    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1" href="/integration/dashboard">
      <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
    </a>
    <a class="flex items-center space-x-3 text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 pl-4 py-3 rounded-xl transition-all duration-300 shadow-sm font-bold" href="/integration/catalogue">
      <span class="material-symbols-outlined">medical_services</span><span class="font-semibold">Location d'équipements</span>
    </a>
    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1" href="/integration/mes-reservations">
      <span class="material-symbols-outlined">shopping_cart</span><span class="font-medium">Mes réservations</span>
    </a>
  </nav>
  <div class="px-4 border-t border-outline pt-6 flex flex-col space-y-3">
    <a href="/integration/profile" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300">
      <span class="material-symbols-outlined">account_circle</span><span class="font-medium">Mon profil</span>
    </a>
    <a href="/integration/logout" class="logout-btn">
      <span class="material-symbols-outlined logout-icon">logout</span><span>Déconnexion</span>
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim overflow-y-auto">
  <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
    <div class="flex items-center space-x-3">
      <a href="/integration/catalogue" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors text-sm font-medium">
        <span class="material-symbols-outlined text-base">arrow_back</span> Catalogue
      </a>
      <span class="text-slate-300">/</span>
      <span class="text-sm font-bold text-on-surface">Réservation</span>
    </div>
    <div class="flex items-center space-x-3">
      <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars(($user['prenom']??'').(' ').($user['nom']??'')) ?></p>
      <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm">
        <?= strtoupper(substr($user['prenom']??'P',0,1)) ?>
      </div>
    </div>
  </header>

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
          <!-- Equipment card -->
          <div class="equip-card">
            <div style="width:90px;height:90px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <span class="material-symbols-outlined" style="font-size:40px;color:#d1d5db;">medical_services</span>
            </div>
            <div class="equip-info">
              <span class="ref">Réf: <?= htmlspecialchars($eq['reference']) ?></span>
              <h2><?= htmlspecialchars($eq['nom']) ?></h2>
              <p class="desc"><?= htmlspecialchars($eq['categorie']) ?></p>
              <div class="price" id="daily-rate" data-rate="<?= $prixDT ?>"><?= $prixDTFmt ?> DT / jour</div>
            </div>
          </div>

          <!-- Dates -->
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
          </div>

          <!-- Delivery -->
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

        <!-- Summary -->
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
          <hr class="summary-divider"/>
          <div class="total-label">TOTAL TTC</div>
          <div class="total-amount" id="total-amount">—</div>
          <button class="btn-confirm" id="btn-confirm" type="button">Confirmer la réservation</button>
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
</main>

<div class="toast-container"></div>
<script>
const API_RES = '/integration/equipment/api/reservations';
const prixDT  = <?= $prixDT ?>;

function nbJours(d1,d2){if(!d1||!d2)return 0;return Math.ceil((new Date(d2)-new Date(d1))/86400000);}
function formatDT(v){return v.toLocaleString('fr-TN',{minimumFractionDigits:3,maximumFractionDigits:3})+' DT';}
function updateTotal(){
  const d1=document.getElementById('date-start').value,d2=document.getElementById('date-end').value,j=nbJours(d1,d2);
  const tot=document.getElementById('total-amount'),dr=document.getElementById('duration-row');
  if(!d1||!d2||j<=0){tot.textContent='—';dr.querySelector('.lbl').textContent='Location';dr.querySelector('.val').textContent='—';return;}
  const t=j*prixDT;tot.textContent=formatDT(t);
  dr.querySelector('.lbl').textContent=`Location (${j} jour${j>1?'s':''})`;dr.querySelector('.val').textContent=formatDT(t);
}
document.getElementById('date-start')?.addEventListener('change',updateTotal);
document.getElementById('date-end')?.addEventListener('change',updateTotal);

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

function showToast(msg,type='info'){
  const c=document.querySelector('.toast-container');
  const t=document.createElement('div');t.className='toast '+type;
  const icons={success:'check_circle',error:'error',info:'info'};
  t.innerHTML=`<span class="material-symbols-outlined">${icons[type]||'info'}</span><span>${msg}</span>`;
  c.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);
}

function afficherErr(id,msg){const el=document.getElementById(id);if(!el)return;el.style.borderColor='#dc2626';el.style.boxShadow='0 0 0 3px rgba(220,38,38,.10)';el.parentElement.querySelector('.msg-erreur')?.remove();const s=document.createElement('small');s.className='msg-erreur';s.textContent='⚠ '+msg;s.style.cssText='color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';el.insertAdjacentElement('afterend',s);}
function effacerErr(id){const el=document.getElementById(id);if(!el)return;el.style.borderColor='';el.style.boxShadow='';el.parentElement?.querySelector('.msg-erreur')?.remove();}

function valider(){
  let ok=true;document.querySelectorAll('.msg-erreur').forEach(e=>e.remove());document.querySelectorAll('.form-input').forEach(i=>{i.style.borderColor='';i.style.boxShadow='';});
  const today=new Date().toISOString().split('T')[0];
  const prenom=document.getElementById('firstname')?.value.trim(),nom=document.getElementById('lastname')?.value.trim();
  const debut=document.getElementById('date-start')?.value,fin=document.getElementById('date-end')?.value;
  const isLiv=document.querySelector('input[name="delivery"]:checked')?.value==='livraison';
  const adresse=adrinp?.value.trim();
  if(!prenom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)){afficherErr('firstname','Prénom invalide.');ok=false;}
  if(!nom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom)){afficherErr('lastname','Nom invalide.');ok=false;}
  if(!debut){afficherErr('date-start','Date de début obligatoire.');ok=false;}
  else if(debut<today){afficherErr('date-start','La date ne peut pas être dans le passé.');ok=false;}
  if(!fin){afficherErr('date-end','Date de fin obligatoire.');ok=false;}
  else if(debut&&fin<=debut){afficherErr('date-end','La date de fin doit être après la date de début.');ok=false;}
  if(isLiv&&(!adresse||adresse.length<5)){afficherErr('adresse-livraison',"Adresse obligatoire (min 5 caractères).");ok=false;}
  if(!ok)showToast('Veuillez corriger les erreurs.','error');
  return ok;
}

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
</body>
</html>