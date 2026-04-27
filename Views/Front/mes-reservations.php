<?php
/**
 * mes-reservations.php — Vue Frontoffice
 * ✅ Pas de require_once ni new Reservation() ici
 * Le controller PatientEquipmentController->mesReservations()
 * injecte $data['reservations'] et $data['currentUser']
 */
$reservations = $data['reservations'] ?? [];
$user         = $data['currentUser']  ?? ($_SESSION['user'] ?? []);

function fmtDateR($d) {
    if (!$d) return '—';
    return (new DateTime($d))->format('d/m/Y');
}
function getBadgeClassR($s) {
    return ['en_cours'=>'encours','termine'=>'termine','en_retard'=>'retard'][$s] ?? 'encours';
}
function getBadgeLabelR($s) {
    return ['en_cours'=>'En cours','termine'=>'Terminé','en_retard'=>'En retard'][$s] ?? '—';
}
function getImgR($ref) {
    foreach ([__DIR__.'/../../assets/images/equipements/', __DIR__.'/../../Assets/images/equipements/'] as $base) {
        foreach (['jpg','jpeg','png','webp'] as $ext) {
            if (file_exists($base.$ref.'.'.$ext))
                return '/integration/assets/images/equipements/'.$ref.'.'.$ext;
        }
    }
    return '';
}
?>
<style>
.res-wrap{max-width:960px;margin:0 auto;padding:24px 0 60px;}
.res-card{background:#fff;border:1px solid #e8eaf0;border-radius:14px;padding:18px 20px;margin-bottom:14px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s,transform .18s;}
.res-card:hover{box-shadow:0 6px 24px rgba(0,77,153,.08);transform:translateY(-1px);}
.res-img{width:76px;height:76px;object-fit:contain;border-radius:10px;background:#f3f4f6;padding:6px;flex-shrink:0;}
.res-img-ph{width:76px;height:76px;border-radius:10px;background:#f3f4f6;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.res-img-ph .material-symbols-outlined{font-size:30px;color:#d1d5db;}
.res-info{flex:1;}
.res-ref{font-size:11px;font-weight:700;color:#0ea5e9;text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:2px;}
.res-nom{font-family:'Manrope',sans-serif;font-size:15px;font-weight:800;color:#111827;margin-bottom:2px;}
.res-cat{font-size:12px;color:#9ca3af;margin-bottom:4px;}
.res-prix{font-size:13px;font-weight:700;color:#004d99;}
.res-period{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:100px;}
.rp-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;}
.rp-date{font-size:12px;font-weight:600;color:#374151;}
.rp-arrow{color:#9ca3af;font-size:14px;}
.res-loca{min-width:130px;}
.rl-nom{font-weight:700;color:#111827;font-size:13px;}
.rl-sub{font-size:11px;color:#6b7280;margin-top:2px;display:flex;align-items:center;gap:3px;}
.rl-sub .material-symbols-outlined{font-size:13px;}
.rbadge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.rbadge-dot{width:6px;height:6px;border-radius:50%;}
.rbadge.termine{background:#dcfce7;color:#15803d;}.rbadge.termine .rbadge-dot{background:#16a34a;}
.rbadge.encours{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;}.rbadge.encours .rbadge-dot{background:#1d4ed8;}
.rbadge.retard{background:#fee2e2;color:#dc2626;border:1px solid #fecaca;}.rbadge.retard .rbadge-dot{background:#dc2626;}
.res-actions{display:flex;flex-direction:column;gap:7px;align-items:center;flex-shrink:0;}
.btn-eye-r{width:36px;height:36px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#004d99;transition:background .18s;}
.btn-eye-r:hover{background:#dbeafe;}
.btn-eye-r .material-symbols-outlined{font-size:16px;}
.btn-del-r{width:36px;height:36px;border-radius:8px;background:#fff5f5;border:1px solid #fecaca;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#dc2626;transition:background .18s;}
.btn-del-r:hover{background:#fee2e2;}
.btn-del-r .material-symbols-outlined{font-size:16px;}
.empty-r{text-align:center;padding:60px 20px;color:#9ca3af;}
.empty-r .material-symbols-outlined{font-size:52px;display:block;margin-bottom:12px;color:#d1d5db;}
/* Modale */
.mo-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999999;align-items:center;justify-content:center;}
.mo-overlay.open{display:flex;}
.mo-box{background:#fff;border-radius:18px;width:520px;max-width:95vw;max-height:90vh;overflow:hidden;box-shadow:0 24px 70px rgba(0,0,0,.18);animation:moIn .22s ease;}
@keyframes moIn{from{opacity:0;transform:translateY(-14px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.mo-head{background:linear-gradient(135deg,#004d99,#1565c0);padding:18px 22px;display:flex;align-items:flex-start;justify-content:space-between;}
.mo-head h2{font-family:'Manrope',sans-serif;font-size:17px;font-weight:900;color:#fff;margin:0 0 2px;}
.mo-head p{font-size:12px;color:rgba(255,255,255,.72);margin:0;}
.mo-close{width:28px;height:28px;border-radius:7px;background:rgba(255,255,255,.18);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;}
.mo-close:hover{background:rgba(255,255,255,.3);}
.mo-close .material-symbols-outlined{font-size:16px;}
.mo-body{padding:18px 22px;overflow-y:auto;max-height:calc(90vh - 90px);}
.eq-mo-card{display:flex;gap:12px;align-items:center;background:#f0f6ff;border:1px solid #dbeafe;border-radius:10px;padding:12px;margin-bottom:14px;}
.eq-mo-img{width:58px;height:58px;object-fit:contain;border-radius:8px;background:#fff;padding:4px;flex-shrink:0;}
.eq-mo-ph{width:58px;height:58px;border-radius:8px;background:#fff;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.eq-mo-ph .material-symbols-outlined{font-size:24px;color:#9ca3af;}
.drow{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13px;}
.drow:last-child{border-bottom:none;}
.dlbl{color:#6b7280;display:flex;align-items:center;gap:5px;}
.dlbl .material-symbols-outlined{font-size:14px;color:#9ca3af;}
.dval{font-weight:600;color:#111827;}
.total-mo{background:linear-gradient(135deg,#eff6ff,#f0f9ff);border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;margin-top:14px;}
.total-mo .tl{font-size:13px;color:#1d4ed8;font-weight:600;}
.total-mo .tv{font-family:'Manrope',sans-serif;font-size:22px;font-weight:900;color:#004d99;}
</style>

<div class="res-wrap">

  <!-- En-tête -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
    <div>
      <h2 class="text-3xl font-extrabold bg-gradient-to-r from-primary via-primary-container to-primary bg-clip-text text-transparent mb-1">
        Mes Réservations
      </h2>
      <p style="font-size:13px;color:#6b7280;">
        <strong><?= count($reservations) ?></strong> réservation(s) — connecté en tant que
        <strong><?= htmlspecialchars(trim(($user['prenom']??'').' '.($user['nom']??''))) ?></strong>
      </p>
    </div>
    <a href="/integration/catalogue"
       style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#004d99;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;">
      <span class="material-symbols-outlined" style="font-size:16px;">add</span>
      Nouvelle réservation
    </a>
  </div>

  <?php if (empty($reservations)): ?>
    <div class="empty-r">
      <span class="material-symbols-outlined">inbox</span>
      <h3 style="font-family:'Manrope',sans-serif;font-size:17px;color:#374151;margin-bottom:6px;">Aucune réservation</h3>
      <p style="font-size:13px;">Vous n'avez pas encore de réservation.</p>
      <a href="/integration/catalogue"
         style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#004d99;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;margin-top:14px;">
        <span class="material-symbols-outlined" style="font-size:16px;">medical_services</span>Voir le catalogue
      </a>
    </div>

  <?php else: ?>
    <?php foreach ($reservations as $r):
      $bc  = getBadgeClassR($r['statut']);
      $bl  = getBadgeLabelR($r['statut']);
      $img = getImgR($r['reference'] ?? '');
      $pj  = (float)($r['prix_jour'] ?? 0);
      $rj  = json_encode($r, JSON_HEX_QUOT | JSON_HEX_APOS);
    ?>
    <div class="res-card" id="rc-<?= (int)$r['id'] ?>">

      <?php if ($img): ?>
        <img class="res-img" src="<?= $img ?>" alt="<?= htmlspecialchars($r['equipement_nom']??'') ?>"/>
      <?php else: ?>
        <div class="res-img-ph"><span class="material-symbols-outlined">medical_services</span></div>
      <?php endif; ?>

      <div class="res-info">
        <span class="res-ref"><?= htmlspecialchars($r['reference']??'') ?></span>
        <div class="res-nom"><?= htmlspecialchars($r['equipement_nom']??'—') ?></div>
        <div class="res-cat"><?= htmlspecialchars($r['categorie']??'—') ?></div>
        <div class="res-prix"><?= number_format($pj,3,',','.') ?> DT / jour</div>
      </div>

      <div class="res-period">
        <span class="rp-lbl">Période</span>
        <span class="rp-date"><?= fmtDateR($r['date_debut']) ?></span>
        <span class="rp-arrow">↓</span>
        <span class="rp-date" style="<?= $r['statut']==='en_retard'?'color:#dc2626;':'' ?>">
          <?= $r['date_fin'] ? fmtDateR($r['date_fin']) : 'En cours' ?>
        </span>
      </div>

      <div class="res-loca">
        <div class="rl-nom"><?= htmlspecialchars($r['locataire_nom']??'') ?></div>
        <?php if (!empty($r['telephone'])): ?>
          <div class="rl-sub"><span class="material-symbols-outlined">phone</span><?= htmlspecialchars($r['telephone']) ?></div>
        <?php endif; ?>
        <?php if (!empty($r['locataire_ville'])): ?>
          <div class="rl-sub"><span class="material-symbols-outlined">location_on</span><?= htmlspecialchars($r['locataire_ville']) ?></div>
        <?php else: ?>
          <div class="rl-sub" style="color:#9ca3af;"><span class="material-symbols-outlined">store</span>Retrait clinique</div>
        <?php endif; ?>
      </div>

      <span class="rbadge <?= $bc ?>"><span class="rbadge-dot"></span><?= $bl ?></span>

      <div class="res-actions">
        <button class="btn-eye-r" type="button" title="Voir le détail" data-resa='<?= $rj ?>'>
          <span class="material-symbols-outlined">visibility</span>
        </button>
        <button class="btn-del-r" type="button" title="Supprimer"
                onclick="suppResa(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['equipement_nom']??'',ENT_QUOTES) ?>')">
          <span class="material-symbols-outlined">delete</span>
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- ✅ PAGINATION -->
  <div id="pagination-resas"></div>

</div>

<!-- MODALE DÉTAIL -->
<div id="mo-resa" class="mo-overlay">
  <div class="mo-box">
    <div class="mo-head">
      <div><h2 id="mo-titre">Détail</h2><p id="mo-ref">—</p></div>
      <button class="mo-close" onclick="fermerMoResa()" type="button">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="mo-body">
      <div class="eq-mo-card">
        <div id="mo-img"></div>
        <div>
          <div id="mo-cat" style="font-size:11px;color:#9ca3af;margin-bottom:2px;"></div>
          <div id="mo-nom" style="font-family:'Manrope',sans-serif;font-size:14px;font-weight:800;color:#111827;margin-bottom:4px;"></div>
          <div id="mo-badge"></div>
        </div>
      </div>
      <div class="drow"><span class="dlbl"><span class="material-symbols-outlined">calendar_today</span>Date début</span><span class="dval" id="mo-debut"></span></div>
      <div class="drow"><span class="dlbl"><span class="material-symbols-outlined">event</span>Date fin</span><span class="dval" id="mo-fin"></span></div>
      <div class="drow"><span class="dlbl"><span class="material-symbols-outlined">schedule</span>Durée</span><span class="dval" id="mo-duree"></span></div>
      <div class="drow"><span class="dlbl"><span class="material-symbols-outlined">local_shipping</span>Livraison</span><span class="dval" id="mo-liv"></span></div>
      <div class="drow" id="mo-adr-row"><span class="dlbl"><span class="material-symbols-outlined">home</span>Adresse</span><span class="dval" id="mo-adr"></span></div>
      <div class="drow"><span class="dlbl"><span class="material-symbols-outlined">phone</span>Téléphone</span><span class="dval" id="mo-tel"></span></div>
      <div class="total-mo"><span class="tl">Total estimé (TTC)</span><span class="tv" id="mo-total"></span></div>
    </div>
  </div>
</div>

<div class="toast-container" style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:999999;"></div>

<script>
const API_RES_V = '/integration/equipment/api/reservations';

/* Œil */
document.addEventListener('click', function(e) {
  const b = e.target.closest('[data-resa]');
  if (b) try { ouvrirMoResa(JSON.parse(b.getAttribute('data-resa'))); } catch(x){}
});

function fmtDR(d){if(!d)return'—';const[y,m,j]=d.split('-');return`${j}/${m}/${y}`;}

function ouvrirMoResa(r){
  const prix=parseFloat(r.prix_jour||0);
  document.getElementById('mo-titre').textContent=r.equipement_nom||'—';
  document.getElementById('mo-ref').textContent='Réf. '+(r.reference||'');
  document.getElementById('mo-nom').textContent=r.equipement_nom||'—';
  document.getElementById('mo-cat').textContent=r.categorie||'—';
  document.getElementById('mo-debut').textContent=fmtDR(r.date_debut);
  document.getElementById('mo-fin').textContent=r.date_fin?fmtDR(r.date_fin):'En cours';
  document.getElementById('mo-tel').textContent=r.telephone||'—';

  if(r.date_debut&&r.date_fin){
    const j=Math.ceil((new Date(r.date_fin)-new Date(r.date_debut))/86400000);
    document.getElementById('mo-duree').textContent=j+' jour'+(j>1?'s':'');
    document.getElementById('mo-total').textContent=(j*prix).toLocaleString('fr-TN',{minimumFractionDigits:3,maximumFractionDigits:3})+' DT';
  }else{document.getElementById('mo-duree').textContent='—';document.getElementById('mo-total').textContent='—';}

  if(r.locataire_ville){
    document.getElementById('mo-liv').textContent='Livraison à domicile';
    document.getElementById('mo-adr').textContent=r.locataire_ville;
    document.getElementById('mo-adr-row').style.display='flex';
  }else{
    document.getElementById('mo-liv').textContent='Retrait en clinique';
    document.getElementById('mo-adr-row').style.display='none';
  }

  const s={en_cours:{bg:'#eff6ff',c:'#1d4ed8',l:'En cours'},termine:{bg:'#dcfce7',c:'#15803d',l:'Terminé'},en_retard:{bg:'#fee2e2',c:'#dc2626',l:'En retard'}}[r.statut]||{bg:'#f3f4f6',c:'#374151',l:r.statut};
  document.getElementById('mo-badge').innerHTML=`<span style="background:${s.bg};color:${s.c};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">${s.l}</span>`;

  const ref=r.reference||'';
  const urls=[`/integration/assets/images/equipements/${ref}.jpg`,`/integration/assets/images/equipements/${ref}.png`,`/integration/Assets/images/equipements/${ref}.jpg`];
  let ti=0;const img=document.createElement('img');img.className='eq-mo-img';img.alt=r.equipement_nom||'';
  img.onerror=function(){ti++;if(ti<urls.length)this.src=urls[ti];else document.getElementById('mo-img').innerHTML='<div class="eq-mo-ph"><span class="material-symbols-outlined">medical_services</span></div>';};
  img.src=urls[0];const mc=document.getElementById('mo-img');mc.innerHTML='';mc.appendChild(img);

  document.getElementById('mo-resa').classList.add('open');
}

function fermerMoResa(){document.getElementById('mo-resa').classList.remove('open');}
document.getElementById('mo-resa').addEventListener('click',function(e){if(e.target===this)fermerMoResa();});

/* Supprimer */
async function suppResa(id,nom){
  if(!confirm('Supprimer la réservation de "'+nom+'" ?'))return;
  try{
    const r=await fetch(`${API_RES_V}?id=${id}`,{method:'DELETE'});
    const j=await r.json();
    if(j.success){
      const c=document.getElementById('rc-'+id);
      if(c){c.style.opacity='0';c.style.transition='opacity .3s';setTimeout(()=>c.remove(),300);}
    }else showToastR('Erreur: '+(j.message||''),'error');
  }catch(e){showToastR('Erreur réseau.','error');}
}

/* ════════════════════════════════════════
   PAGINATION — 6 réservations par page
════════════════════════════════════════ */
const RESAS_PAR_PAGE = 3;
let pageResa = 1;

window.getResasVisibles = function() {
  return [...document.querySelectorAll('.res-card')];
}

window.afficherPageResa = function(page) {
  pageResa = page;
  const cartes = getResasVisibles();
  const total  = cartes.length;
  const pages  = Math.ceil(total / RESAS_PAR_PAGE);
  const debut  = (page - 1) * RESAS_PAR_PAGE;
  const fin    = debut + RESAS_PAR_PAGE;

  cartes.forEach((c, i) => {
    c.style.display = (i >= debut && i < fin) ? '' : 'none';
  });

  renderPaginationResa(page, pages, total);
}

window.renderPaginationResa = function(page, pages, total) {
  const container = document.getElementById('pagination-resas');
  if (!container) return;
  if (pages <= 1) { container.innerHTML = ''; return; }

  const debut = (page - 1) * RESAS_PAR_PAGE + 1;
  const fin   = Math.min(page * RESAS_PAR_PAGE, total);

    container.innerHTML = `
    <div style="display:flex;justify-content:flex-end;align-items:center;gap:8px;
                padding:16px 0;margin-top:12px;">
      <button onclick="afficherPageResa(${page - 1})"
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
        <button onclick="afficherPageResa(${p})"
                style="width:36px;height:36px;border-radius:9px;
                       border:${p===page?'none':'1.5px solid #e5e7eb'};
                       background:${p===page?'#004d99':'#fff'};
                       color:${p===page?'#fff':'#374151'};
                       font-size:13px;font-weight:700;cursor:pointer;
                       font-family:'Inter',sans-serif;">
          ${p}
        </button>`).join('')}
      <button onclick="afficherPageResa(${page + 1})"
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
  `;}

// Initialisation
document.addEventListener('DOMContentLoaded', () => afficherPageResa(1));
if (document.readyState !== 'loading') afficherPageResa(1);

function showToastR(msg,type){
  const c=document.querySelector('.toast-container');
  const t=document.createElement('div');
  t.style.cssText='display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:10px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13px;font-weight:600;';
  t.style.borderLeft='4px solid '+(type==='success'?'#16a34a':'#dc2626');
  t.style.color=type==='success'?'#15803d':'#dc2626';
  t.textContent=msg;c.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},3500);
}
</script>