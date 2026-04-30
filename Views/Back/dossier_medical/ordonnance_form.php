<?php
// Views/Back/dossier_medical/ordonnance_form.php — Add/Edit ordonnance
$isEdit     = ($mode ?? 'add') === 'edit';
$pName      = htmlspecialchars(($patient['prenom'] ?? '').' '.($patient['nom'] ?? ''));
$patientId  = $patient['id_PK'] ?? 0;
$consultId  = $consultation['id_consultation'] ?? 0;
$ve         = $validation_errors ?? [];
$formAction = $isEdit
    ? '/integration/dossier/ordonnance/edit?id='.(int)($ordonnance['id_ordonnance']??0)
    : '/integration/dossier/ordonnance/add?consult_id='.$consultId;
?>
<div class="max-w-3xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex items-center gap-4">
    <a href="/integration/dossier/view?patient_id=<?= $patientId ?>"
       class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
      <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900"><?= $isEdit ? "Modifier l'Ordonnance" : "Nouvelle Ordonnance" ?></h1>
      <p class="text-sm text-on-surface-variant">Patient : <strong><?= $pName ?></strong>
        <?php if (!empty($consultation['diagnostic'])): ?> · <?= htmlspecialchars($consultation['diagnostic']) ?><?php endif ?></p>
    </div>
  </div>

  <?php if (!empty($ve)): ?>
  <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 flex items-start gap-3">
    <span class="material-symbols-outlined text-error text-xl shrink-0">error</span>
    <ul class="text-sm text-error space-y-0.5">
      <?php foreach ($ve as $msg): ?><li>• <?= htmlspecialchars($msg) ?></li><?php endforeach ?>
    </ul>
  </div>
  <?php endif ?>

  <form method="POST" action="<?= $formAction ?>" class="space-y-5">
    <input type="hidden" name="id_consultation" value="<?= $consultId ?>"/>
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $ordonnance['id_ordonnance'] ?? '' ?>"/><?php endif ?>

    <!-- General info -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h2 class="font-headline font-bold text-blue-900 mb-4 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">event</span>Informations générales
      </h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Date d'émission *</label>
          <input type="date" name="date_emission"
                 value="<?= $isEdit ? htmlspecialchars($ordonnance['date_emission']??'') : date('Y-m-d') ?>"
                 max="<?= date('Y-m-d') ?>"
                 class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
        </div>
        <?php if ($isEdit): ?>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Statut</label>
          <select name="statut" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
            <?php foreach (['active','archivee','annulee'] as $s): ?>
            <option value="<?= $s ?>" <?= ($ordonnance['statut']??'active')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Medications -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <div class="flex items-center justify-between mb-5">
        <h2 class="font-headline font-bold text-blue-900 text-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">medication</span>Médicaments prescrits
        </h2>
        <button type="button" onclick="addMedCard()" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary border border-primary/30 rounded-lg hover:bg-blue-50 transition-colors">
          <span class="material-symbols-outlined text-sm">add</span>Ajouter
        </button>
      </div>
      <div id="med-list" class="space-y-4">
        <?php if (!empty($medicaments)): ?>
        <?php foreach ($medicaments as $idx => $med): ?>
        <div class="med-card bg-surface-container-low rounded-xl p-4 border border-outline-variant/20 relative">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <span class="med-num w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center"><?= $idx+1 ?></span>
              <span class="text-sm font-bold text-on-surface"><?= htmlspecialchars($med['nom']??'Médicament '.($idx+1)) ?></span>
            </div>
            <button type="button" onclick="this.closest('.med-card').remove();renumberCards()" class="p-1.5 text-error hover:bg-error-container/30 rounded-lg transition-colors">
              <span class="material-symbols-outlined text-base">delete</span>
            </button>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Nom *</label><input type="text" name="med_nom[]" value="<?= htmlspecialchars($med['nom']??'') ?>" placeholder="Amoxicilline" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Catégorie</label><input type="text" name="med_categorie[]" value="<?= htmlspecialchars($med['categorie']??'') ?>" placeholder="Antibiotique" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Dosage</label><input type="text" name="med_dosage[]" value="<?= htmlspecialchars($med['dosage']??'') ?>" placeholder="500mg" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Fréquence</label><input type="text" name="med_frequence[]" value="<?= htmlspecialchars($med['frequence']??'') ?>" placeholder="3 fois/jour" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Durée</label><input type="text" name="med_duree[]" value="<?= htmlspecialchars($med['duree']??'') ?>" placeholder="7 jours" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Instructions</label><input type="text" name="med_instructions[]" value="<?= htmlspecialchars($med['instructions']??'') ?>" placeholder="Avec les repas" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
          </div>
        </div>
        <?php endforeach ?>
        <?php else: ?>
        <div class="med-card bg-surface-container-low rounded-xl p-4 border border-outline-variant/20">
          <div class="flex items-center justify-between mb-3"><div class="flex items-center gap-2"><span class="med-num w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span><span class="text-sm font-bold text-on-surface-variant">Médicament 1</span></div><button type="button" onclick="this.closest('.med-card').remove();renumberCards()" class="p-1.5 text-error hover:bg-error-container/30 rounded-lg transition-colors"><span class="material-symbols-outlined text-base">delete</span></button></div>
          <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Nom *</label><input type="text" name="med_nom[]" placeholder="Amoxicilline" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Catégorie</label><input type="text" name="med_categorie[]" placeholder="Antibiotique" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Dosage</label><input type="text" name="med_dosage[]" placeholder="500mg" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Fréquence</label><input type="text" name="med_frequence[]" placeholder="3 fois/jour" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Durée</label><input type="text" name="med_duree[]" placeholder="7 jours" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Instructions</label><input type="text" name="med_instructions[]" placeholder="Avec les repas" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
          </div>
        </div>
        <?php endif ?>
      </div>
      <button type="button" onclick="addMedCard()" class="mt-4 w-full border-2 border-dashed border-outline-variant/40 hover:border-primary/40 rounded-xl py-3 text-sm font-semibold text-on-surface-variant hover:text-primary transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined">add_circle</span>Ajouter un médicament
      </button>
    </div>

    <!-- Note pharmacien -->
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h2 class="font-headline font-bold text-blue-900 mb-4 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">local_pharmacy</span>Note au Pharmacien
      </h2>
      <textarea name="note_pharmacien" rows="3" placeholder="Instructions spéciales (substitution, marque obligatoire...)"
                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none"><?= htmlspecialchars($ordonnance['note_pharmacien'] ?? '') ?></textarea>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3 pb-4">
      <a href="/integration/dossier/view?patient_id=<?= $patientId ?>"
         class="px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant bg-surface-container hover:bg-surface-container-high transition-colors">Annuler</a>
      <button type="submit"
              class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">save</span>
        <?= $isEdit ? "Mettre à jour" : "Créer l'ordonnance" ?>
      </button>
    </div>
  </form>
</div>
<script>
let medCount = <?= max(count($medicaments??[]),1) ?>;
function addMedCard() {
  medCount++;
  document.getElementById('med-list').insertAdjacentHTML('beforeend',`
  <div class="med-card bg-surface-container-low rounded-xl p-4 border border-outline-variant/20">
    <div class="flex items-center justify-between mb-3"><div class="flex items-center gap-2"><span class="med-num w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">${medCount}</span><span class="text-sm font-bold text-on-surface-variant">Médicament ${medCount}</span></div><button type="button" onclick="this.closest('.med-card').remove();renumberCards()" class="p-1.5 text-error hover:bg-error-container/30 rounded-lg transition-colors"><span class="material-symbols-outlined text-base">delete</span></button></div>
    <div class="grid grid-cols-2 gap-3">
      <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Nom *</label><input type="text" name="med_nom[]" placeholder="Ibuprofène" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
      <div class="col-span-2 md:col-span-1"><label class="block text-xs font-bold text-on-surface-variant mb-1">Catégorie</label><input type="text" name="med_categorie[]" placeholder="Anti-inflammatoire" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
      <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Dosage</label><input type="text" name="med_dosage[]" placeholder="400mg" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
      <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Fréquence</label><input type="text" name="med_frequence[]" placeholder="2 fois/jour" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
      <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Durée</label><input type="text" name="med_duree[]" placeholder="5 jours" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
      <div><label class="block text-xs font-bold text-on-surface-variant mb-1">Instructions</label><input type="text" name="med_instructions[]" placeholder="Avec les repas" class="w-full bg-white border border-outline-variant/30 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-primary/20"/></div>
    </div>
  </div>`);
}
function renumberCards() {
  document.querySelectorAll('#med-list .med-card').forEach((c,i) => {
    const n = c.querySelector('.med-num'); if(n) n.textContent = i+1;
    medCount = i+1;
  });
}
</script>
