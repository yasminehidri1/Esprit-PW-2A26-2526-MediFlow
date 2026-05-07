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

<div class="max-w-3xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1 flex items-center gap-4">
            <a href="<?= $isEdit ? '/integration/dossier/view?patient_id='.$patientIdVal : '/integration/dossier/patients' ?>"
               class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors text-slate-500">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <?= $isEdit ? 'Modifier la consultation' : 'Nouvelle consultation' ?>
        </h1>
        <?php if ($patientName): ?>
        <p class="text-slate-500 text-sm ml-14">
            Patient : <span class="font-semibold text-primary"><?= $patientName ?></span>
        </p>
        <?php endif; ?>
    </div>

    <!-- Validation errors -->
    <?php if (!empty($ve)): ?>
    <div class="mb-6 p-4 rounded-xl bg-error-container/20 border border-error/30 fade-in">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-error text-2xl">error</span>
            <div>
                <h3 class="font-bold text-error mb-2">Erreurs de saisie</h3>
                <ul class="text-sm text-error space-y-1">
                    <?php foreach ($ve as $field => $msg): ?>
                    <li class="flex items-start gap-2">
                        <span class="text-lg">•</span>
                        <span><?= htmlspecialchars($msg) ?></span>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif ?>

    <form method="POST" action="<?= $formAction ?>" id="consult-form" class="space-y-6 fade-in">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)($consultation['id_consultation'] ?? 0) ?>">
        <input type="hidden" name="id_patient" value="<?= $patientIdVal ?>">
        <?php endif ?>

        <!-- Step 1: Patient selection (only for new) -->
        <?php if (!$isEdit): ?>
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)]">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Sélectionner le patient
            </h2>
            <input type="hidden" name="id_patient" id="selected-patient-id" value="<?= $patientIdVal ?: '' ?>"/>
            <div id="patient-select-error" class="hidden text-xs text-error font-semibold mb-2">Veuillez sélectionner un patient.</div>
            <div class="relative mb-3">
                <input type="text" id="patient-search-filter" placeholder="Filtrer les patients..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 pl-10 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
            </div>
            <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1" id="patient-cards">
                <?php $pColors=['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700']; ?>
                <?php foreach ($allPatients as $i => $p): ?>
                <?php $ini=strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1)); ?>
                <div class="patient-card flex items-center gap-3 p-3 rounded-xl border-2 border-slate-100 cursor-pointer hover:border-primary/40 transition-all <?= $p['id_PK']==$patientIdVal?'border-primary bg-blue-50':'bg-white' ?>"
                     data-id="<?= $p['id_PK'] ?>" data-name="<?= strtolower($p['prenom'].' '.$p['nom'].' '.$p['mail']) ?>"
                     onclick="selectPatient(this)">
                    <div class="w-9 h-9 rounded-full <?= $pColors[$i%5] ?> flex items-center justify-center text-xs font-bold shrink-0"><?= $ini ?></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></p>
                        <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($p['mail']) ?></p>
                    </div>
                    <span class="check <?= $p['id_PK']==$patientIdVal ? '' : 'hidden' ?> material-symbols-outlined text-primary text-sm">check_circle</span>
                </div>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>

        <!-- Section: Infos de base -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)]">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">event_note</span>
                Informations de la consultation
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Date & heure *</label>
                    <input type="text" name="date_consultation" id="date_consultation"
                           value="<?= $isEdit ? str_replace(' ','T',substr($consultation['date_consultation']??'',0,16)) : date('Y-m-d\TH:i') ?>"
                           placeholder="YYYY-MM-DDTHH:MM"
                           class="w-full bg-slate-50 border <?= isset($ve['date_consultation'])?'border-error':'border-slate-200' ?> rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <p id="date-error" class="text-error text-xs mt-1 hidden">La date est invalide (YYYY-MM-DDTHH:MM).</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Type de consultation *</label>
                    <select name="type_consultation" id="type_consultation" class="w-full bg-slate-50 border <?= isset($ve['type_consultation'])?'border-error':'border-slate-200' ?> rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">-- Choisir --</option>
                        <?php
                        $types = ['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement',
                                  'Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'];
                        foreach ($types as $t):
                        ?>
                        <option <?= $filled('type_consultation') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Diagnostic</label>
                    <input type="text" name="diagnostic"
                           value="<?= $filled('diagnostic') ?>"
                           placeholder="Ex: Hypertension Artérielle"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Compte-rendu clinique</label>
                    <textarea name="compte_rendu" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none"
                              placeholder="Observations, conclusions, recommandations..."><?= $filled('compte_rendu') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Section: Constantes vitales -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)]">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">monitor_heart</span>
                Constantes Vitales <span class="text-xs font-normal text-slate-400 ml-1">(facultatif)</span>
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <?php $vitFields=[['tension_arterielle','Tension artérielle','Ex: 120/80'],['rythme_cardiaque','Rythme cardiaque (bpm)','72'],['poids','Poids (kg)','75.5'],['saturation_o2','Saturation O² (%)','98']]; ?>
                <?php foreach($vitFields as [$fname,$flbl,$fph]): ?>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= $flbl ?></label>
                    <input type="text" name="<?= $fname ?>"
                           value="<?= $filled($fname) ?>"
                           placeholder="<?= $fph ?>" 
                           class="w-full bg-slate-50 border <?= isset($ve[$fname])?'border-error':'border-slate-200' ?> rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <!-- Section: Antécédents dynamiques -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)]">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Antécédents médicaux
                </h2>
                <button type="button" onclick="addAntRow()"
                        class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="ant-rows" class="space-y-2">
                <?php foreach ($antecedents as $ant): ?>
                <div class="ant-row flex gap-2 items-start">
                    <input type="text" name="ant_annee[]" value="<?= htmlspecialchars($ant['annee'] ?? '') ?>"
                           placeholder="Année" class="w-1/4 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <input type="text" name="ant_titre[]" value="<?= htmlspecialchars($ant['titre'] ?? '') ?>"
                           placeholder="Titre" class="w-1/3 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <input type="text" name="ant_desc[]" value="<?= htmlspecialchars($ant['description'] ?? '') ?>"
                           placeholder="Description" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <button type="button" onclick="this.closest('.ant-row').remove();toggleEmpty('ant-rows','ant-empty')"
                            class="p-2 text-error hover:bg-red-50 rounded-lg transition-colors text-center shrink-0">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
                <?php endforeach ?>
            </div>
            <p id="ant-empty" class="text-sm text-slate-400 italic <?= empty($antecedents)?'':'hidden' ?>">Aucun antécédent saisi.</p>
        </div>

        <!-- Section: Allergies dynamiques -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)]">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">warning</span>
                    Allergies
                </h2>
                <button type="button" onclick="addAlgRow()"
                        class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="alg-rows" class="space-y-2">
                <?php foreach ($allergies as $alg): ?>
                <div class="alg-row flex gap-2 items-center">
                    <input type="text" name="allergie_nom[]" value="<?= htmlspecialchars($alg['nom'] ?? '') ?>"
                           placeholder="Allergie" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <select name="allergie_niveau[]" class="w-1/3 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none">
                        <?php foreach (['Faible','Modéré','Élevé'] as $niv): ?>
                        <option <?= ($alg['niveau'] ?? '') === $niv ? 'selected' : '' ?>><?= $niv ?></option>
                        <?php endforeach ?>
                    </select>
                    <button type="button" onclick="this.closest('.alg-row').remove();toggleEmpty('alg-rows','alg-empty')"
                            class="p-2 text-error hover:bg-red-50 rounded-lg transition-colors text-center shrink-0">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
                <?php endforeach ?>
            </div>
            <p id="alg-empty" class="text-sm text-slate-400 italic <?= empty($allergies)?'':'hidden' ?>">Aucune allergie saisie.</p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="<?= $isEdit ? '/integration/dossier/view?patient_id='.$patientIdVal : '/integration/dossier/patients' ?>"
               class="px-6 py-3 rounded-lg font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                Annuler
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-lg font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                <?= $isEdit ? 'Mettre à jour' : 'Enregistrer la consultation' ?>
            </button>
        </div>

    </form>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
</style>

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
    '<div class="ant-row flex gap-2 items-start"><input type="text" name="ant_annee[]" placeholder="Année" class="w-1/4 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/><input type="text" name="ant_titre[]" placeholder="Titre" class="w-1/3 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/><input type="text" name="ant_desc[]" placeholder="Description" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/><button type="button" onclick="this.closest(\'.ant-row\').remove();toggleEmpty(\'ant-rows\',\'ant-empty\')" class="p-2 text-error hover:bg-red-50 rounded-lg transition-colors text-center shrink-0"><span class="material-symbols-outlined text-sm">close</span></button></div>');
}
function addAlgRow() {
  document.getElementById('alg-empty').classList.add('hidden');
  document.getElementById('alg-rows').insertAdjacentHTML('beforeend',
    '<div class="alg-row flex gap-2 items-center"><input type="text" name="allergie_nom[]" placeholder="Allergie" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/><select name="allergie_niveau[]" class="w-1/3 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none"><option>Faible</option><option selected>Modéré</option><option>Élevé</option></select><button type="button" onclick="this.closest(\'.alg-row\').remove();toggleEmpty(\'alg-rows\',\'alg-empty\')" class="p-2 text-error hover:bg-red-50 rounded-lg transition-colors text-center shrink-0"><span class="material-symbols-outlined text-sm">close</span></button></div>');
}
function toggleEmpty(containerId, emptyId) {
  const c = document.getElementById(containerId);
  const isEmpty = !c.querySelector('.ant-row, .alg-row');
  document.getElementById(emptyId).classList.toggle('hidden', !isEmpty);
}
document.addEventListener('DOMContentLoaded', function() {
  const dateInput = document.getElementById('date_consultation');
  if (dateInput) {
    dateInput.addEventListener('input', function() {
      const dateRegex = /^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}$/;
      const dateError = document.getElementById('date-error');
      if (!this.value.match(dateRegex)) {
          this.classList.add('border-error');
          if (dateError) dateError.classList.remove('hidden');
      } else {
          this.classList.remove('border-error');
          if (dateError) dateError.classList.add('hidden');
      }
    });
  }

  const typeSelect = document.getElementById('type_consultation');
  if (typeSelect) {
    typeSelect.addEventListener('change', function() {
      if (this.value.trim() === '') {
          this.classList.add('border-error');
          let typeError = document.getElementById('type-error');
          if (typeError) typeError.classList.remove('hidden');
      } else {
          this.classList.remove('border-error');
          let typeError = document.getElementById('type-error');
          if (typeError) typeError.classList.add('hidden');
      }
    });
  }
});

document.getElementById('consult-form')?.addEventListener('submit', function(e) {
  let isValid = true;
  
  const pid = document.getElementById('selected-patient-id');
  const patientError = document.getElementById('patient-select-error');
  if (pid && !pid.value) { 
      if (patientError) patientError.classList.remove('hidden'); 
      isValid = false; 
  }

  const dateInput = document.getElementById('date_consultation');
  const dateError = document.getElementById('date-error');
  const dateRegex = /^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}$/;
  if (dateInput && !dateInput.value.match(dateRegex)) {
      dateInput.classList.add('border-error');
      if (dateError) dateError.classList.remove('hidden');
      isValid = false;
  } else if (dateInput) {
      dateInput.classList.remove('border-error');
      if (dateError) dateError.classList.add('hidden');
  }

  const typeSelect = document.getElementById('type_consultation');
  if (typeSelect && typeSelect.value.trim() === '') {
      typeSelect.classList.add('border-error');
      let typeError = document.getElementById('type-error');
      if (!typeError) {
          typeError = document.createElement('p');
          typeError.id = 'type-error';
          typeError.className = 'text-error text-xs mt-1';
          typeError.textContent = "Veuillez sélectionner un type.";
          typeSelect.parentNode.appendChild(typeError);
      } else {
          typeError.classList.remove('hidden');
      }
      isValid = false;
  } else if (typeSelect) {
      typeSelect.classList.remove('border-error');
      const typeError = document.getElementById('type-error');
      if (typeError) typeError.classList.add('hidden');
  }

  if (!isValid) e.preventDefault();
});
// Pre-select patient if URL param
<?php if ($patientIdVal && !$isEdit): ?>
document.querySelectorAll('.patient-card').forEach(c => { if (c.dataset.id == '<?= $patientIdVal ?>') selectPatient(c); });
<?php endif ?>
</script>
