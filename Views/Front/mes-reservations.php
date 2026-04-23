<?php
// $data injected by PatientEquipmentController::mesReservations()
$reservations = $data['reservations'] ?? [];
$user         = $data['currentUser']  ?? ($_SESSION['user'] ?? []);
$total        = count(array_filter($reservations, fn($r) => $r['statut'] === 'en_cours'));

function fmtDate($d){ return $d ? (new DateTime($d))->format('d/m/Y') : '—'; }
function getBadgeCls($s){ return ['en_cours'=>'encours','termine'=>'termine','en_retard'=>'retard'][$s]??'encours'; }
function getBadgeLbl($s){ return ['en_cours'=>'En cours','termine'=>'Terminé','en_retard'=>'En retard'][$s]??'—'; }
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mes Réservations — MediFlow</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/Mediflow/assets/css/style.css"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config={darkMode:"class",theme:{extend:{colors:{"primary":"#004d99","primary-fixed":"#d6e3ff","primary-container":"#1565c0","surface":"#f7f9fb","surface-container-low":"#f2f4f6","surface-dim":"#d8dadc","outline":"#727783","on-surface":"#191c1e","on-surface-variant":"#424752"},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"},fontFamily:{headline:["Manrope"],body:["Inter"]}}}}
  </script>
  <style>
    .resa-card{background:#fff;border:1px solid #e8eaf0;border-radius:14px;padding:20px 22px;margin-bottom:14px;display:flex;align-items:center;gap:18px;transition:box-shadow .2s;}
    .resa-card:hover{box-shadow:0 6px 24px rgba(0,77,153,.08);}
    .resa-info{flex:1;}
    .resa-ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;}
    .resa-nom{font-family:'Manrope',sans-serif;font-size:16px;font-weight:800;color:#111827;margin-bottom:2px;}
    .resa-cat{font-size:12px;color:#9ca3af;margin-bottom:6px;}
    .resa-prix{font-size:13px;font-weight:700;color:#004d99;}
    .resa-dates{display:flex;flex-direction:column;gap:3px;align-items:center;min-width:120px;}
    .resa-date-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;}
    .resa-date-val{font-size:13px;font-weight:600;color:#374151;}
    .resa-locataire{min-width:130px;font-size:13px;}
    .resa-locataire .nom{font-weight:700;color:#111827;}
    .resa-locataire .tel,.resa-locataire .ville{color:#9ca3af;font-size:12px;margin-top:1px;}
    .badge{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;}
    .badge-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
    .badge.termine{background:#dcfce7;color:#15803d;}.badge.termine .badge-dot{background:#16a34a;}
    .badge.encours{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;}.badge.encours .badge-dot{background:#1d4ed8;}
    .badge.retard{background:#fee2e2;color:#dc2626;border:1px solid #fecaca;}.badge.retard .badge-dot{background:#dc2626;}
    .resa-actions{display:flex;flex-direction:column;gap:8px;align-items:center;flex-shrink:0;}
    .btn-act{width:38px;height:38px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .18s,transform .15s;}
    .btn-act:hover{transform:scale(1.08);}
    .btn-act.edit{background:#eff6ff;border:1px solid #bfdbfe;color:#004d99;}.btn-act.edit:hover{background:#dbeafe;}
    .btn-act.del{background:#fff5f5;border:1px solid #fecaca;color:#dc2626;}.btn-act.del:hover{background:#fee2e2;}
    .btn-act .material-symbols-outlined{font-size:17px;}
    .empty-state{text-align:center;padding:80px 20px;color:#9ca3af;}
    .empty-state .material-symbols-outlined{font-size:64px;display:block;margin-bottom:16px;color:#d1d5db;}
    /* Modal */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:#fff;border-radius:16px;padding:30px;width:500px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.18);animation:mIn .22s ease;}
    @keyframes mIn{from{opacity:0;transform:translateY(-14px) scale(.97)}to{opacity:1;transform:none}}
    .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;}
    .modal-header h2{font-size:18px;font-weight:800;color:#111827;}
    .modal-close{width:30px;height:30px;border-radius:8px;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280;}
    .modal-close:hover{background:#e5e7eb;}
    .modal-field{display:flex;flex-direction:column;gap:5px;margin-bottom:13px;}
    .modal-field label{font-size:12px;font-weight:600;color:#6b7280;}
    .modal-input{width:100%;padding:10px 13px;background:#f5f7fa;border:1px solid #e5e7eb;border-radius:8px;font-size:13.5px;font-family:'Inter',sans-serif;color:#111827;outline:none;transition:border-color .18s,box-shadow .18s;}
    .modal-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.10);}
    .modal-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    .modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;}
    .btn-cancel-modal{padding:10px 20px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:'Inter',sans-serif;}
    .btn-save-modal{padding:10px 22px;border-radius:8px;background:#004d99;color:#fff;border:none;font-size:13px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;}
    .btn-save-modal:hover{background:#00357a;}
    .toast-container{position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:99999;}
    .toast{display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:10px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13.5px;font-weight:600;font-family:'Inter',sans-serif;animation:toastIn .3s ease;}
    .toast.success{border-left:4px solid #16a34a;color:#15803d;}
    .toast.error{border-left:4px solid #dc2626;color:#dc2626;}
    .toast.info{border-left:4px solid #004d99;color:#004d99;}
    @keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}
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
    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1" href="/Mediflow/dashboard">
      <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
    </a>
    <a class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300 transform hover:translate-x-1" href="/Mediflow/catalogue">
      <span class="material-symbols-outlined">medical_services</span><span class="font-medium">Location d'équipements</span>
    </a>
    <!-- active -->
    <a class="flex items-center space-x-3 text-primary bg-gradient-to-r from-primary-fixed to-primary-fixed/50 pl-4 py-3 rounded-xl transition-all duration-300 shadow-sm font-bold" href="/Mediflow/mes-reservations">
      <span class="material-symbols-outlined">shopping_cart</span><span class="font-semibold">Mes réservations</span>
    </a>
  </nav>
  <div class="px-4 border-t border-outline pt-6 flex flex-col space-y-3">
    <a href="/Mediflow/profile" class="flex items-center space-x-3 text-slate-500 hover:text-primary pl-4 py-3 rounded-xl transition-all duration-300">
      <span class="material-symbols-outlined">account_circle</span><span class="font-medium">Mon profil</span>
    </a>
    <a href="/Mediflow/logout" class="logout-btn">
      <span class="material-symbols-outlined logout-icon">logout</span><span>Déconnexion</span>
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="ml-64 min-h-screen bg-gradient-to-br from-surface via-surface-container-low to-surface-dim overflow-y-auto">
  <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-gradient-to-r from-white/80 to-primary-fixed/10 backdrop-blur-xl flex items-center justify-between px-8 z-40 shadow-xl border-b border-outline/20">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-primary">shopping_cart</span>
      <h2 class="text-lg font-bold text-on-surface">Mes Réservations</h2>
      <?php if ($total > 0): ?>
        <span class="bg-primary text-white text-xs font-bold px-2.5 py-0.5 rounded-full"><?= $total ?> en cours</span>
      <?php endif; ?>
    </div>
    <div class="flex items-center gap-4">
      <a href="/Mediflow/catalogue" class="flex items-center gap-2 text-sm font-semibold text-primary bg-primary-fixed/60 hover:bg-primary-fixed px-4 py-2 rounded-full transition-all duration-300">
        <span class="material-symbols-outlined text-base">add</span> Nouvelle réservation
      </a>
      <div class="flex items-center gap-3 pl-4 border-l border-outline/20">
        <p class="text-sm font-bold"><?= htmlspecialchars(($user['prenom']??'').(' ').($user['nom']??'')) ?></p>
        <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold text-sm">
          <?= strtoupper(substr($user['prenom']??'P',0,1)) ?>
        </div>
      </div>
    </div>
  </header>

  <div class="pt-24 pb-12 px-10">
    <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent mb-1">Mes Réservations</h2>
    <p class="text-on-surface-variant mb-8 font-medium"><?= count($reservations) ?> réservation(s) au total</p>

    <?php if (empty($reservations)): ?>
      <div class="empty-state">
        <span class="material-symbols-outlined">shopping_cart</span>
        <p class="font-bold text-xl text-slate-700 mb-2">Aucune réservation</p>
        <p class="text-sm mb-6">Vous n'avez aucune réservation en cours.</p>
        <a href="/Mediflow/catalogue" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm">
          <span class="material-symbols-outlined text-base">medical_services</span> Parcourir le catalogue
        </a>
      </div>
    <?php else: ?>

      <?php foreach ($reservations as $r):
        $prixDT   = number_format((float)($r['prix_jour'] ?? 0), 3, ',', '.');
        $badgeCls = getBadgeCls($r['statut']);
        $badgeLbl = getBadgeLbl($r['statut']);
        $rJson    = htmlspecialchars(json_encode($r), ENT_QUOTES);
      ?>
      <div class="resa-card" id="resa-<?= $r['id'] ?>">

        <div style="width:72px;height:72px;background:#f3f4f6;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
          <span class="material-symbols-outlined" style="font-size:32px;color:#d1d5db;">medical_services</span>
        </div>

        <div class="resa-info">
          <span class="resa-ref"><?= htmlspecialchars($r['reference'] ?? '') ?></span>
          <div class="resa-nom"><?= htmlspecialchars($r['equipement_nom'] ?? '—') ?></div>
          <div class="resa-cat"><?= htmlspecialchars($r['categorie'] ?? '—') ?></div>
          <div class="resa-prix"><?= $prixDT ?> DT / jour</div>
        </div>

        <div class="resa-dates">
          <span class="resa-date-label">Période</span>
          <span class="resa-date-val"><?= fmtDate($r['date_debut']) ?></span>
          <span style="color:#9ca3af;font-size:16px;">↓</span>
          <span class="resa-date-val" style="<?= $r['statut']==='en_retard'?'color:#dc2626;font-weight:700;':'' ?>">
            <?= $r['date_fin'] ? fmtDate($r['date_fin']) : 'En cours' ?>
          </span>
        </div>

        <div class="resa-locataire">
          <div class="nom"><?= htmlspecialchars($r['locataire_nom']) ?></div>
          <div class="ville"><?= htmlspecialchars($r['locataire_ville'] ?? '') ?></div>
          <div class="tel"><?= htmlspecialchars($r['telephone'] ?? '') ?></div>
        </div>

        <span class="badge <?= $badgeCls ?>">
          <span class="badge-dot"></span><?= $badgeLbl ?>
        </span>

        <div class="resa-actions">
          <button class="btn-act edit" type="button" title="Modifier" onclick='ouvrirModale(<?= $rJson ?>)'>
            <span class="material-symbols-outlined">edit</span>
          </button>
          <button class="btn-act del" type="button" title="Supprimer"
                  onclick="supprimerResa(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['equipement_nom']??'',ENT_QUOTES) ?>')">
            <span class="material-symbols-outlined">delete</span>
          </button>
        </div>
      </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>
</main>

<!-- MODAL -->
<div id="modal-modifier" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
      <h2>Modifier la Réservation</h2>
      <button class="modal-close" id="modal-close-btn" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <input type="hidden" id="mod-id"/>
    <input type="hidden" id="mod-equipement-id"/>
    <div class="modal-field">
      <label>Équipement</label>
      <div id="mod-equip-nom" style="padding:10px 13px;background:#f9fafb;border-radius:8px;font-weight:600;color:#374151;font-size:13px;border:1px solid #e5e7eb;"></div>
    </div>
    <div class="modal-row">
      <div class="modal-field"><label for="mod-debut">Date de début *</label><input id="mod-debut" class="modal-input" type="date"/></div>
      <div class="modal-field"><label for="mod-fin">Date de fin</label><input id="mod-fin" class="modal-input" type="date"/></div>
    </div>
    <div class="modal-row">
      <div class="modal-field"><label for="mod-prenom">Prénom *</label><input id="mod-prenom" class="modal-input" type="text" placeholder="Mohamed"/></div>
      <div class="modal-field"><label for="mod-nom">Nom *</label><input id="mod-nom" class="modal-input" type="text" placeholder="Ben Ali"/></div>
    </div>
    <div class="modal-field"><label for="mod-tel">Téléphone</label><input id="mod-tel" class="modal-input" type="text" placeholder="20 123 456"/></div>
    <div class="modal-field"><label for="mod-ville">Adresse / Ville</label><input id="mod-ville" class="modal-input" type="text" placeholder="Tunis"/></div>
    <div class="modal-footer">
      <button class="btn-cancel-modal" id="modal-cancel-btn" type="button">Annuler</button>
      <button class="btn-save-modal" id="modal-save-btn" type="button">
        <span class="material-symbols-outlined" style="font-size:15px;vertical-align:middle;">save</span> Enregistrer
      </button>
    </div>
  </div>
</div>

<div class="toast-container"></div>
<script>
const API_RES='/Mediflow/equipment/api/reservations';

function showToast(msg,type='info'){const c=document.querySelector('.toast-container');const t=document.createElement('div');t.className='toast '+type;const icons={success:'check_circle',error:'error',info:'info'};t.innerHTML=`<span class="material-symbols-outlined">${icons[type]||'info'}</span><span>${msg}</span>`;c.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);}

function ouvrirModale(r){
  document.getElementById('mod-id').value=r.id;
  document.getElementById('mod-equipement-id').value=r.equipement_id;
  document.getElementById('mod-equip-nom').textContent=(r.reference||'')+' — '+(r.equipement_nom||'');
  document.getElementById('mod-debut').value=r.date_debut||'';
  document.getElementById('mod-fin').value=r.date_fin||'';
  const parts=(r.locataire_nom||'').split(' ');
  document.getElementById('mod-prenom').value=parts[0]||'';
  document.getElementById('mod-nom').value=parts.slice(1).join(' ')||'';
  document.getElementById('mod-tel').value=r.telephone||'';
  document.getElementById('mod-ville').value=r.locataire_ville||'';
  document.querySelectorAll('.msg-erreur').forEach(e=>e.remove());
  document.querySelectorAll('.modal-input').forEach(i=>{i.style.borderColor='';i.style.boxShadow='';});
  document.getElementById('modal-modifier').classList.add('open');
}
function fermerModale(){document.getElementById('modal-modifier').classList.remove('open');}
document.getElementById('modal-close-btn').addEventListener('click',fermerModale);
document.getElementById('modal-cancel-btn').addEventListener('click',fermerModale);
document.getElementById('modal-modifier').addEventListener('click',function(e){if(e.target===this)fermerModale();});

function afficherErr(id,msg){const el=document.getElementById(id);if(!el)return;el.style.borderColor='#dc2626';el.style.boxShadow='0 0 0 3px rgba(220,38,38,.10)';el.parentElement?.querySelector('.msg-erreur')?.remove();const s=document.createElement('small');s.className='msg-erreur';s.textContent='⚠ '+msg;s.style.cssText='color:#dc2626;font-size:11px;font-weight:600;display:block;margin-top:4px;';el.insertAdjacentElement('afterend',s);}

function validerMod(){
  let ok=true;document.querySelectorAll('.msg-erreur').forEach(e=>e.remove());document.querySelectorAll('.modal-input').forEach(i=>{i.style.borderColor='';i.style.boxShadow='';});
  const prenom=document.getElementById('mod-prenom').value.trim(),nom=document.getElementById('mod-nom').value.trim();
  const debut=document.getElementById('mod-debut').value,fin=document.getElementById('mod-fin').value,tel=document.getElementById('mod-tel').value.trim();
  if(!prenom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(prenom)){afficherErr('mod-prenom','Prénom invalide.');ok=false;}
  if(!nom||!/^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(nom)){afficherErr('mod-nom','Nom invalide.');ok=false;}
  if(!debut){afficherErr('mod-debut','Date de début obligatoire.');ok=false;}
  if(fin&&debut&&fin<=debut){afficherErr('mod-fin','La date de fin doit être après la date de début.');ok=false;}
  if(tel&&!/^[2345789]\d{7}$/.test(tel.replace(/\s/g,''))){afficherErr('mod-tel','Format tunisien : 8 chiffres.');ok=false;}
  if(!ok)showToast('Veuillez corriger les erreurs.','error');
  return ok;
}

document.getElementById('modal-save-btn').addEventListener('click',async()=>{
  if(!validerMod())return;
  const id=document.getElementById('mod-id').value,eqId=document.getElementById('mod-equipement-id').value;
  const prenom=document.getElementById('mod-prenom').value.trim(),nom=document.getElementById('mod-nom').value.trim();
  const data={equipement_id:eqId,locataire_nom:prenom+' '+nom,locataire_ville:document.getElementById('mod-ville').value.trim(),telephone:document.getElementById('mod-tel').value.trim(),date_debut:document.getElementById('mod-debut').value,date_fin:document.getElementById('mod-fin').value||null,statut:'en_cours'};
  try{
    const res=await fetch(`${API_RES}?id=${id}`,{method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
    const json=await res.json();
    if(json.success){showToast('Réservation modifiée !','success');fermerModale();setTimeout(()=>location.reload(),1400);}
    else showToast('Erreur : '+(json.message||'Inconnue'),'error');
  }catch(e){showToast('Erreur réseau.','error');}
});

async function supprimerResa(id,nom){
  if(!confirm(`Supprimer la réservation de "${nom}" ?\nCette action est irréversible.`))return;
  try{
    const res=await fetch(`${API_RES}?id=${id}`,{method:'DELETE'});
    const json=await res.json();
    if(json.success){showToast('Réservation supprimée.','success');const card=document.getElementById('resa-'+id);if(card){card.style.transition='opacity .3s,transform .3s';card.style.opacity='0';card.style.transform='translateX(20px)';setTimeout(()=>card.remove(),300);}}
    else showToast('Erreur : '+(json.message||'Inconnue'),'error');
  }catch(e){showToast('Erreur réseau.','error');}
}
</script>
</body>
</html>