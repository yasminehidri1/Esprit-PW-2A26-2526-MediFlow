<?php
// Views/Back/dossier_medical/patient_dossier.php
// Patient's own medical record — embedded in Back/layout.php shell
$pInitials = strtoupper(substr($patient['prenom'],0,1).substr($patient['nom'],0,1));
$pName     = htmlspecialchars($patient['prenom'].' '.$patient['nom']);
$activeMeds = [];
if (!empty($prescriptions)) {
    foreach ($prescriptions as $presc) {
        if ($presc['statut']==='active') {
            $m = json_decode($presc['medicaments']??'[]',true) ?: [];
            foreach ($m as $med) $activeMeds[] = $med;
        }
    }
}
?>
<div class="space-y-6">

  <!-- Hero card -->
  <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.05)] border-l-4 border-primary relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-wrap items-start justify-between gap-4 relative z-10">
      <div class="flex items-center gap-5">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary-container text-white text-3xl font-bold flex items-center justify-center shadow-md"><?= $pInitials ?></div>
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-blue-900"><?= $pName ?></h1>
          <p class="text-sm text-on-surface-variant">#MF-<?= str_pad($patient['id_PK'],5,'0',STR_PAD_LEFT) ?> · <?= htmlspecialchars($patient['mail']) ?></p>
          <span class="mt-2 inline-block px-3 py-0.5 bg-tertiary-fixed/40 text-tertiary text-xs font-bold rounded-full">Patient Actif</span>
        </div>
      </div>
      <div class="flex gap-2">
        <button id="btnModifierProfil" class="flex items-center gap-2 px-4 py-2.5 bg-surface-container hover:bg-surface-container-high rounded-xl text-sm font-semibold text-on-surface transition-colors">
          <span class="material-symbols-outlined text-lg">edit</span>Modifier Profil
        </button>
        <button id="btndemandeOrdonnance" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all">
          <span class="material-symbols-outlined text-lg">history_edu</span>Demande Ordonnance
        </button>
      </div>
    </div>

    <!-- Vitals row -->
    <?php if ($vitals): ?>
    <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-outline-variant/20">
      <?php foreach([['Tension','tension_arterielle','mmHg'],['Rythme Cardiaque','rythme_cardiaque','BPM'],['Poids','poids','kg'],['Saturation O²','saturation_o2','%']] as [$l,$k,$u]): ?>
      <div class="bg-surface-container-low rounded-xl p-4">
        <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1"><?= $l ?></p>
        <p class="text-2xl font-extrabold text-on-surface"><?= htmlspecialchars($vitals[$k]??'—') ?> <span class="text-xs font-normal text-on-surface-variant"><?= $u ?></span></p>
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>

  <div class="grid grid-cols-12 gap-6">

    <!-- Consultation timeline (col 8) -->
    <div class="col-span-8">
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <div class="px-6 py-4 border-b border-outline-variant/20">
          <h3 class="font-headline font-bold text-blue-900 flex items-center gap-2"><span class="material-symbols-outlined text-primary">history</span>Historique Clinique</h3>
        </div>
        <?php if (empty($consultations)): ?>
        <div class="py-12 text-center text-on-surface-variant"><span class="material-symbols-outlined text-4xl block mb-2 opacity-30">event_note</span><p class="text-sm">Aucune consultation enregistrée.</p></div>
        <?php else: ?>
        <div class="p-6 space-y-0">
          <?php foreach ($consultations as $i => $c): ?>
          <div class="relative pl-8 pb-8 group <?= $i===count($consultations)-1?'pb-2':'' ?>">
            <div class="absolute left-0 top-1 w-0.5 h-full bg-outline-variant/20 group-last:hidden"></div>
            <div class="absolute left-[-5px] top-1 w-3 h-3 rounded-full <?= $i===0?'bg-primary':'bg-tertiary' ?> border-2 border-white shadow-sm"></div>
            <div class="bg-surface-container-low rounded-xl p-4 hover:bg-white hover:shadow-md transition-all">
              <div class="flex justify-between items-start mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest <?= $i===0?'text-primary':'text-tertiary' ?>"><?= date('d M Y',strtotime($c['date_consultation'])) ?> · <?= htmlspecialchars($c['type_consultation']??'Consultation') ?></span>
                <span class="px-2 py-0.5 bg-white text-[10px] font-bold rounded border border-outline-variant/20">Visite</span>
              </div>
              <?php if (!empty($c['diagnostic'])): ?><p class="font-semibold text-on-surface text-sm"><?= htmlspecialchars($c['diagnostic']) ?></p><?php endif ?>
              <?php if (!empty($c['compte_rendu'])): ?><p class="text-xs text-on-surface-variant mt-1 line-clamp-2"><?= htmlspecialchars(substr($c['compte_rendu'],0,100)) ?></p><?php endif ?>
              <div class="flex items-center gap-1.5 text-xs text-on-surface-variant mt-3">
                <span class="material-symbols-outlined text-sm">person</span>
                Dr. <?= htmlspecialchars($c['medecin_prenom'].' '.$c['medecin_nom']) ?>
              </div>
            </div>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Right col (4) -->
    <div class="col-span-4 space-y-5">

      <!-- Active treatment -->
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] p-5">
        <h3 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm"><span class="material-symbols-outlined text-tertiary">pill</span>Traitement Actuel</h3>
        <?php if (empty($activeMeds)): ?>
        <p class="text-sm text-on-surface-variant italic">Aucun traitement actif.</p>
        <?php else: ?>
        <div class="space-y-3">
          <?php foreach (array_slice($activeMeds,0,3) as $m): ?>
          <div class="bg-tertiary/5 border-l-4 border-tertiary rounded-xl p-3">
            <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($m['nom']??'') ?> <span class="font-normal text-xs text-on-surface-variant"><?= htmlspecialchars($m['dosage']??'') ?></span></p>
            <p class="text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($m['categorie']??'') ?> · <?= htmlspecialchars($m['frequence']??'') ?></p>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
        <button id="btndemandeOrdonnance2" class="mt-4 w-full border border-tertiary/20 text-tertiary text-sm font-bold rounded-xl py-2.5 hover:bg-tertiary/5 transition-colors flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-base">history_edu</span>Demande Ordonnance
        </button>
      </div>

      <!-- Care team -->
      <div class="bg-gradient-to-br from-primary to-primary-container rounded-2xl p-5 text-white shadow-xl shadow-primary/20">
        <h3 class="font-headline font-bold mb-4 text-sm">Mon Équipe Médicale</h3>
        <?php if (!empty($doctors)): ?>
        <div class="space-y-3">
          <?php foreach (array_slice($doctors,0,3) as $d): ?>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold"><?= strtoupper(substr($d['prenom'],0,1).substr($d['nom'],0,1)) ?></div>
            <div><p class="text-sm font-bold"><?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></p><p class="text-[10px] opacity-70">Médecin</p></div>
          </div>
          <?php endforeach ?>
        </div>
        <?php else: ?><p class="text-sm opacity-70">Aucun médecin assigné.</p><?php endif ?>
      </div>

      <!-- Allergies alert -->
      <?php
      $allergies = [];
      if (!empty($consultations[0])) $allergies = json_decode($consultations[0]['allergies']??'[]',true) ?: [];
      ?>
      <?php if (!empty($allergies)): ?>
      <div class="bg-error-container/20 border border-error/10 rounded-2xl p-4">
        <div class="flex items-center gap-2 text-error mb-2"><span class="material-symbols-outlined">warning</span><span class="text-sm font-bold">Allergies</span></div>
        <ul class="space-y-1">
          <?php foreach (array_slice($allergies,0,3) as $a): ?>
          <li class="flex items-start gap-2 text-xs text-on-surface-variant">
            <span class="w-1.5 h-1.5 rounded-full bg-error mt-1 shrink-0"></span>
            <?= htmlspecialchars($a['nom']??'') ?> (<?= htmlspecialchars($a['niveau']??'') ?>)
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>
    </div>
  </div>
</div>

<!-- Modal: Modifier Profil -->
<div id="modalModifierProfil" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-7">
    <div class="flex justify-between items-center mb-5">
      <h3 class="font-headline text-xl font-bold text-blue-900">Modifier Profil</h3>
      <button onclick="document.getElementById('modalModifierProfil').classList.add('hidden')" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-container-high"><span class="material-symbols-outlined text-base">close</span></button>
    </div>
    <form id="formModifierProfil" class="space-y-4">
      <?php foreach([['prenom','Prénom',$patient['prenom']??''],['nom','Nom',$patient['nom']??''],['mail','Email',$patient['mail']??'']] as [$f,$l,$v]): ?>
      <div>
        <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1.5"><?=$l?></label>
        <input type="<?=$f==='mail'?'email':'text'?>" id="modif_<?=$f?>" value="<?=htmlspecialchars($v)?>"
               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
        <span id="err_<?=$f?>" class="text-xs text-error hidden"></span>
      </div>
      <?php endforeach ?>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modalModifierProfil').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-outline-variant/30 text-on-surface-variant rounded-xl text-sm font-semibold hover:bg-surface-container transition-colors">Annuler</button>
        <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Demande Ordonnance -->
<div id="modaldemandeOrdonnance" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
    <div class="bg-gradient-to-r from-tertiary to-teal-500 px-7 py-5 flex items-center justify-between text-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><span class="material-symbols-outlined">history_edu</span></div>
        <div><h3 class="text-lg font-bold">Demande d'Ordonnance</h3><p class="text-teal-100 text-xs">Envoyée directement à votre médecin</p></div>
      </div>
      <button onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center"><span class="material-symbols-outlined text-base">close</span></button>
    </div>
    <div class="px-7 py-6 space-y-4">
      <div id="ord_success_state" class="hidden flex-col items-center text-center py-6 gap-4">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center"><span class="material-symbols-outlined text-4xl text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span></div>
        <div><p class="text-lg font-bold text-on-surface">Demande envoyée !</p><p class="text-sm text-on-surface-variant mt-1">Votre médecin vous répondra prochainement.</p></div>
        <button onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')" class="px-8 py-2.5 bg-tertiary text-white rounded-xl font-semibold text-sm">Fermer</button>
      </div>
      <form id="formdemandeOrdonnance" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-on-surface mb-2">Médecin <span class="text-error">*</span></label>
          <select id="ord_medecin" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
            <option value="">— Sélectionner —</option>
            <?php $liste = !empty($allDoctors) ? $allDoctors : ($doctors??[]); ?>
            <?php foreach ($liste as $d): ?>
            <option value="<?= (int)$d['id_PK'] ?>">Dr. <?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-on-surface mb-2">Description <span class="text-error">*</span> <span class="text-xs font-normal text-on-surface-variant">(10-500 car.)</span></label>
          <textarea id="ord_description" rows="4" placeholder="Ex: Renouvellement de mon traitement Amlodipine 5mg..."
                    class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none"></textarea>
          <p class="text-xs text-on-surface-variant text-right mt-1"><span id="ord_char_count">0</span>/500</p>
        </div>
        <div class="flex gap-3">
          <button type="button" onclick="document.getElementById('modaldemandeOrdonnance').classList.add('hidden')" class="flex-1 py-2.5 border border-outline-variant/30 text-on-surface-variant rounded-xl text-sm font-semibold hover:bg-surface-container transition-colors">Annuler</button>
          <button type="submit" id="ord_submit_btn" disabled class="flex-1 py-2.5 bg-tertiary text-white rounded-xl text-sm font-bold opacity-40 cursor-not-allowed flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">send</span>Envoyer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Open modals
['btnModifierProfil','btndemandeOrdonnance','btndemandeOrdonnance2'].forEach(id => {
  document.getElementById(id)?.addEventListener('click', () => {
    const target = id.includes('Modifier') ? 'modalModifierProfil' : 'modaldemandeOrdonnance';
    document.getElementById(target).classList.remove('hidden');
  });
});
// Char counter
document.getElementById('ord_description')?.addEventListener('input', function() {
  const l = this.value.length; document.getElementById('ord_char_count').textContent = l;
  const btn = document.getElementById('ord_submit_btn'), medecinOk = !!document.getElementById('ord_medecin').value;
  const ok = l >= 10 && l <= 500 && medecinOk;
  btn.disabled = !ok; btn.classList.toggle('opacity-40',!ok); btn.classList.toggle('cursor-not-allowed',!ok);
});
document.getElementById('ord_medecin')?.addEventListener('change', () => document.getElementById('ord_description')?.dispatchEvent(new Event('input')));
// Submit demande
document.getElementById('formdemandeOrdonnance')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('ord_submit_btn'); btn.disabled=true;
  const res = await fetch('/integration/dossier/patient/request-prescription', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({medecin_id: document.getElementById('ord_medecin').value, description: document.getElementById('ord_description').value})
  });
  if (res.ok) { this.style.display='none'; document.getElementById('ord_success_state').classList.remove('hidden'); document.getElementById('ord_success_state').style.display='flex'; }
  else { btn.disabled=false; alert('Erreur lors de l\'envoi. Réessayez.'); }
});
// Submit profile
document.getElementById('formModifierProfil')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const res = await fetch('/integration/dossier/patient/update-profile', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({prenom: document.getElementById('modif_prenom').value, nom: document.getElementById('modif_nom').value, email: document.getElementById('modif_mail').value})
  });
  if (res.ok) { document.getElementById('modalModifierProfil').classList.add('hidden'); location.reload(); }
});
</script>
