<?php
// Views/Back/dossier_medical/consultation_form.php
// Used for: new consultation (mode='new') and edit (mode='edit')
// All variables come from DossierController via include
$isEdit = ($mode ?? 'new') === 'edit';
$formAction = $isEdit
    ? '/integration/dossier/consultation/edit?id='.(int)($consultation['id_consultation'] ?? 0)
    : '/integration/dossier/nouvelle-consultation';
$patientName  = !empty($patient) ? htmlspecialchars($patient['prenom'].' '.$patient['nom']) : '';
$patientIdVal = !empty($consultation) ? (int)$consultation['id_patient'] : (int)($_GET['patient_id'] ?? 0);
$fd = $form_data ?? [];
$ve = $validation_errors ?? [];
$filled = fn(string $k, string $d = '') => htmlspecialchars($fd[$k] ?? ($consultation[$k] ?? $d));
?>
<div class="max-w-3xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex items-center gap-4">
    <a href="<?= $isEdit ? '/integration/dossier/view?patient_id='.$patientIdVal : '/integration/dossier/patients' ?>"
       class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
      <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900"><?= $isEdit ? 'Modifier la Consultation' : 'Nouvelle Consultation' ?></h1>
      <?php if ($patientName): ?><p class="text-sm text-on-surface-variant">Patient : <strong><?= $patientName ?></strong></p><?php endif ?>
    </div>
  </div>

  <!-- Validation errors -->
  <?php if (!empty($ve)): ?>
  <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 flex items-start gap-3">
    <span class="material-symbols-outlined text-error text-xl shrink-0">error</span>
    <ul class="text-sm text-error space-y-0.5">
      <?php foreach ($ve as $msg): ?><li>• <?= htmlspecialchars($msg) ?></li><?php endforeach ?>
    </ul>
  </div>
  <?php endif ?>

  <form id="consult-form" method="POST" action="<?= $formAction ?>" novalidate class="space-y-5">
    <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int)($consultation['id_consultation'] ?? 0) ?>">
    <input type="hidden" name="id_patient" value="<?= $patientIdVal ?>">
    <?php endif ?>

    <!-- Step 1: Patient selection (only for new) -->
    <?php if (!$isEdit): ?>
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h2 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm">
        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs flex items-center justify-center font-black">1</span>
        Sélectionner le patient
      </h2>
      <input type="hidden" name="id_patient" id="selected-patient-id" value="<?= $patientIdVal ?: '' ?>"/>
      <div id="patient-select-error" class="hidden text-xs text-error font-semibold mb-2">Veuillez sélectionner un patient.</div>
      <div class="relative mb-3">
        <input type="text" id="patient-search-filter" placeholder="Filtrer les patients..."
               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 pl-10 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-base">search</span>
      </div>
      <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1" id="patient-cards">
        <?php $pColors=['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700']; ?>
        <?php foreach ($allPatients as $i => $p): ?>
        <?php $ini=strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1)); ?>
        <div class="patient-card flex items-center gap-3 p-3 rounded-xl border-2 border-outline-variant/20 cursor-pointer hover:border-primary/40 transition-all <?= $p['id_PK']==$patientIdVal?'border-primary bg-blue-50':'bg-white' ?>"
             data-id="<?= $p['id_PK'] ?>" data-name="<?= strtolower($p['prenom'].' '.$p['nom'].' '.$p['mail']) ?>"
             onclick="selectPatient(this)">
          <div class="w-9 h-9 rounded-full <?= $pColors[$i%5] ?> flex items-center justify-center text-xs font-bold shrink-0"><?= $ini ?></div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-on-surface truncate"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></p>
            <p class="text-[10px] text-on-surface-variant truncate"><?= htmlspecialchars($p['mail']) ?></p>
          </div>
          <span class="check hidden material-symbols-outlined text-primary text-sm">check_circle</span>
        </div>
        <?php endforeach ?>
      </div>
    </div>
    <?php endif ?>

    <!-- Step 2: Consultation info -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h2 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm">
        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs flex items-center justify-center font-black"><?= $isEdit?'1':'2' ?></span>
        Informations de la consultation
      </h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Date &amp; heure *</label>
          <input type="datetime-local" name="date_consultation" id="f-date"
                 value="<?= $isEdit ? str_replace(' ','T',substr($consultation['date_consultation']??'',0,16)) : date('Y-m-d\TH:i') ?>"
                 class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 <?= isset($ve['date_consultation'])?'border-error':'' ?>"/>
          <?php if (isset($ve['date_consultation'])): ?><p class="text-xs text-error mt-1"><?= $ve['date_consultation'] ?></p><?php endif ?>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Type de consultation *</label>
          <select name="type_consultation" id="f-type" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 <?= isset($ve['type_consultation'])?'border-error':'' ?>">
            <option value="">-- Choisir --</option>
            <?php foreach(['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement','Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'] as $t): ?>
            <option <?= $filled('type_consultation')===$t?'selected':'' ?>><?= $t ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Diagnostic</label>
          <input type="text" name="diagnostic" value="<?= $filled('diagnostic') ?>" placeholder="Ex : Hypertension Artérielle"
                 class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Compte-rendu</label>
          <textarea name="compte_rendu" rows="4" placeholder="Observations, conclusions, recommandations..."
                    class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none"><?= $filled('compte_rendu') ?></textarea>
        </div>
      </div>
    </div>

    <!-- Step 3: Vitals -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h2 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm">
        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs flex items-center justify-center font-black"><?= $isEdit?'2':'3' ?></span>
        Constantes Vitales <span class="text-xs font-normal text-on-surface-variant ml-1">(facultatif)</span>
      </h2>
      <div class="grid grid-cols-2 gap-4">
        <?php $vitFields=[['tension_arterielle','Tension artérielle','Ex: 120/80'],['rythme_cardiaque','Rythme cardiaque (bpm)','72'],['poids','Poids (kg)','75.5'],['saturation_o2','Saturation O² (%)','98']]; ?>
        <?php foreach($vitFields as [$fname,$flbl,$fph]): ?>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5"><?= $flbl ?></label>
          <input type="text" name="<?= $fname ?>" value="<?= $filled($fname) ?>" placeholder="<?= $fph ?>"
                 class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 <?= isset($ve[$fname])?'border-error':'' ?>"/>
          <?php if (isset($ve[$fname])): ?><p class="text-xs text-error mt-1"><?= $ve[$fname] ?></p><?php endif ?>
        </div>
        <?php endforeach ?>
      </div>
    </div>

    <!-- Step 4: Antécédents -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-headline font-bold text-blue-900 flex items-center gap-2 text-sm">
          <span class="w-5 h-5 rounded-full bg-primary text-white text-xs flex items-center justify-center font-black"><?= $isEdit?'3':'4' ?></span>
          Antécédents Médicaux
        </h2>
        <button type="button" onclick="addAntRow()" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary border border-primary/30 rounded-lg hover:bg-blue-50 transition-colors">
          <span class="material-symbols-outlined text-sm">add</span>Ajouter
        </button>
      </div>
      <div id="ant-rows" class="space-y-2">
        <?php foreach($antecedents as $ant): ?>
        <div class="ant-row grid grid-cols-12 gap-2">
          <input type="text" name="ant_annee[]" value="<?= htmlspecialchars($ant['annee']??'') ?>" placeholder="Année" class="col-span-2 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/>
          <input type="text" name="ant_titre[]" value="<?= htmlspecialchars($ant['titre']??'') ?>" placeholder="Titre" class="col-span-4 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/>
          <input type="text" name="ant_desc[]" value="<?= htmlspecialchars($ant['description']??'') ?>" placeholder="Description" class="col-span-5 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/>
          <button type="button" onclick="this.closest('.ant-row').remove();toggleEmpty('ant-rows','ant-empty')" class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-base">delete</span></button>
        </div>
        <?php endforeach ?>
      </div>
      <p id="ant-empty" class="text-sm text-on-surface-variant italic <?= empty($antecedents)?'':'hidden' ?>">Aucun antécédent saisi.</p>
    </div>

    <!-- Step 5: Allergies -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-headline font-bold text-blue-900 flex items-center gap-2 text-sm">
          <span class="w-5 h-5 rounded-full bg-error/80 text-white text-xs flex items-center justify-center font-black"><?= $isEdit?'4':'5' ?></span>
          Allergies
        </h2>
        <button type="button" onclick="addAlgRow()" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary border border-primary/30 rounded-lg hover:bg-blue-50 transition-colors">
          <span class="material-symbols-outlined text-sm">add</span>Ajouter
        </button>
      </div>
      <div id="alg-rows" class="space-y-2">
        <?php foreach($allergies as $alg): ?>
        <div class="alg-row grid grid-cols-12 gap-2">
          <input type="text" name="allergie_nom[]" value="<?= htmlspecialchars($alg['nom']??'') ?>" placeholder="Allergie" class="col-span-7 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/>
          <select name="allergie_niveau[]" class="col-span-4 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none">
            <?php foreach(['Faible','Modéré','Élevé'] as $niv): ?>
            <option <?= ($alg['niveau']??'')===$niv?'selected':'' ?>><?= $niv ?></option>
            <?php endforeach ?>
          </select>
          <button type="button" onclick="this.closest('.alg-row').remove();toggleEmpty('alg-rows','alg-empty')" class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-base">delete</span></button>
        </div>
        <?php endforeach ?>
      </div>
      <p id="alg-empty" class="text-sm text-on-surface-variant italic <?= empty($allergies)?'':'hidden' ?>">Aucune allergie saisie.</p>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3 pb-4">
      <a href="<?= $isEdit ? '/integration/dossier/view?patient_id='.$patientIdVal : '/integration/dossier/patients' ?>"
         class="px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant bg-surface-container hover:bg-surface-container-high transition-colors">
        Annuler
      </a>
      <button type="submit"
              class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">save</span>
        <?= $isEdit ? 'Mettre à jour' : 'Enregistrer la consultation' ?>
      </button>
    </div>
  </form>
</div>

<script>
function selectPatient(card) {
  document.querySelectorAll('.patient-card').forEach(c => { c.classList.remove('border-primary','bg-blue-50'); c.querySelector('.check')?.classList.add('hidden'); });
  card.classList.add('border-primary','bg-blue-50');
  card.querySelector('.check')?.classList.remove('hidden');
  document.getElementById('selected-patient-id').value = card.dataset.id;
  document.getElementById('patient-select-error')?.classList.add('hidden');
}
document.getElementById('patient-search-filter')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.patient-card').forEach(c => c.style.display = c.dataset.name.includes(q)?'':'none');
});
function addAntRow() {
  document.getElementById('ant-empty').classList.add('hidden');
  document.getElementById('ant-rows').insertAdjacentHTML('beforeend',
    '<div class="ant-row grid grid-cols-12 gap-2"><input type="text" name="ant_annee[]" placeholder="Année" class="col-span-2 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/><input type="text" name="ant_titre[]" placeholder="Titre" class="col-span-4 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/><input type="text" name="ant_desc[]" placeholder="Description" class="col-span-5 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/><button type="button" onclick="this.closest(\'.ant-row\').remove();toggleEmpty(\'ant-rows\',\'ant-empty\')" class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-base">delete</span></button></div>');
}
function addAlgRow() {
  document.getElementById('alg-empty').classList.add('hidden');
  document.getElementById('alg-rows').insertAdjacentHTML('beforeend',
    '<div class="alg-row grid grid-cols-12 gap-2"><input type="text" name="allergie_nom[]" placeholder="Allergie" class="col-span-7 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/><select name="allergie_niveau[]" class="col-span-4 bg-surface-container-low border border-outline-variant/20 rounded-lg px-3 py-2 text-xs outline-none"><option>Faible</option><option selected>Modéré</option><option>Élevé</option></select><button type="button" onclick="this.closest(\'.alg-row\').remove();toggleEmpty(\'alg-rows\',\'alg-empty\')" class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-base">delete</span></button></div>');
}
function toggleEmpty(containerId, emptyId) {
  const c = document.getElementById(containerId);
  const isEmpty = !c.querySelector('[class*="-row"]');
  document.getElementById(emptyId).classList.toggle('hidden', !isEmpty);
}
document.getElementById('consult-form')?.addEventListener('submit', function(e) {
  const pid = document.getElementById('selected-patient-id');
  if (pid && !pid.value) { document.getElementById('patient-select-error').classList.remove('hidden'); e.preventDefault(); }
});
// Pre-select patient if URL param
<?php if ($patientIdVal && !$isEdit): ?>
document.querySelectorAll('.patient-card').forEach(c => { if (c.dataset.id == '<?= $patientIdVal ?>') selectPatient(c); });
<?php endif ?>
</script>
