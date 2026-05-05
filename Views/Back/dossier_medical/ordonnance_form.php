<?php // Views/Back/dossier_medical/ordonnance_form.php
$isEdit     = ($mode ?? 'add') === 'edit';
$pName      = htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? ''));
$patientId  = $patient['id_PK'] ?? 0;
$consultId  = $consultation['id_consultation'] ?? 0;
$ve         = $validation_errors ?? [];
$formAction = $isEdit
    ? '/integration/dossier/ordonnance/edit?id=' . (int)($ordonnance['id_ordonnance'] ?? 0)
    : '/integration/dossier/ordonnance/add?consult_id=' . $consultId;
?>

<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8 fade-in flex items-center gap-4">
        <a href="/integration/dossier/view?patient_id=<?= $patientId ?>"
           class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors text-slate-500">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1">
                <?= $isEdit ? 'Modifier l\'ordonnance' : 'Nouvelle ordonnance' ?>
            </h1>
            <p class="text-slate-500 text-sm">
                Patient : <span class="font-semibold text-primary"><?= $pName ?></span>
                <?php if (!empty($consultation['diagnostic'])): ?>
                &bull; Diagnostic : <span class="font-medium"><?= htmlspecialchars($consultation['diagnostic']) ?></span>
                <?php endif ?>
            </p>
        </div>
    </div>

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

    <form method="POST" action="<?= $formAction ?>" class="space-y-6 fade-in" id="ordonnanceForm">
        <input type="hidden" name="id_consultation" value="<?= $consultId ?>"/>
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $ordonnance['id_ordonnance'] ?? '' ?>"/>
        <?php endif ?>

        <!-- Date & Statut -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] border border-slate-100">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">event</span>
                Informations générales
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Date d'émission</label>
                    <input type="text" name="date_emission" id="date_emission"
                           value="<?= $isEdit ? htmlspecialchars($ordonnance['date_emission'] ?? '') : date('Y-m-d') ?>"
                           placeholder="YYYY-MM-DD"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    <p id="date-error" class="text-error text-xs mt-1 hidden">La date d'émission est requise (YYYY-MM-DD).</p>
                </div>
                <?php if ($isEdit): ?>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Statut</label>
                    <select name="statut" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                        <?php foreach (['active','archivee','annulee'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($ordonnance['statut'] ?? 'active') === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <?php endif ?>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] border border-slate-100">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">medication</span>
                    Médicaments prescrits
                </h2>
                <button type="button" onclick="addMedCard()"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary border border-primary/30 hover:bg-blue-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Ajouter un médicament
                </button>
            </div>

            <div id="med-list" class="space-y-4">
                <?php if (!empty($medicaments)): ?>
                <?php foreach ($medicaments as $idx => $med): ?>
                <div class="bg-white border-2 border-slate-100 rounded-xl p-5 relative transition-colors hover:border-primary/40 med-card slide-in" id="med-<?= $idx ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center med-num">
                                <?= $idx + 1 ?>
                            </div>
                            <span class="text-sm font-bold text-slate-800">
                                <?= htmlspecialchars($med['nom'] ?? 'Médicament ' . ($idx + 1)) ?>
                            </span>
                        </div>
                        <button type="button" onclick="this.closest('.med-card').remove(); renumberCards();"
                                class="p-1.5 text-error hover:bg-red-50 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nom du médicament *</label>
                            <input type="text" name="med_nom[]"
                                   value="<?= htmlspecialchars($med['nom'] ?? '') ?>"
                                   placeholder="Ex: Amoxicilline" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 med-nom-input"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catégorie</label>
                            <input type="text" name="med_categorie[]"
                                   value="<?= htmlspecialchars($med['categorie'] ?? '') ?>"
                                   placeholder="Ex: Antibiotique" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Dosage</label>
                            <select name="med_dosage[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <?php foreach(['500mg', '1g', '1000mg', '200mg', '250mg', '5ml', '10ml', '1 sachet', '1 suppositoire', '1 ampoule', 'Autre'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($med['dosage'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach ?>
                                <?php if (!empty($med['dosage']) && !in_array($med['dosage'], ['500mg', '1g', '1000mg', '200mg', '250mg', '5ml', '10ml', '1 sachet', '1 suppositoire', '1 ampoule', 'Autre'])): ?>
                                <option value="<?= htmlspecialchars($med['dosage']) ?>" selected><?= htmlspecialchars($med['dosage']) ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fréquence</label>
                            <select name="med_frequence[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <?php foreach(['1 fois par jour', '2 fois par jour', '3 fois par jour', '4 fois par jour', 'Matin et soir', 'Le soir', 'Au besoin', 'Autre'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($med['frequence'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach ?>
                                <?php if (!empty($med['frequence']) && !in_array($med['frequence'], ['1 fois par jour', '2 fois par jour', '3 fois par jour', '4 fois par jour', 'Matin et soir', 'Le soir', 'Au besoin', 'Autre'])): ?>
                                <option value="<?= htmlspecialchars($med['frequence']) ?>" selected><?= htmlspecialchars($med['frequence']) ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Durée</label>
                            <select name="med_duree[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <?php foreach(['3 jours', '5 jours', '7 jours', '10 jours', '14 jours', '1 mois', 'En continu', 'Usage unique', 'Autre'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($med['duree'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach ?>
                                <?php if (!empty($med['duree']) && !in_array($med['duree'], ['3 jours', '5 jours', '7 jours', '10 jours', '14 jours', '1 mois', 'En continu', 'Usage unique', 'Autre'])): ?>
                                <option value="<?= htmlspecialchars($med['duree']) ?>" selected><?= htmlspecialchars($med['duree']) ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Instructions pour le patient</label>
                            <input type="text" name="med_instructions[]"
                                   value="<?= htmlspecialchars($med['instructions'] ?? '') ?>"
                                   placeholder="Ex: Prendre pendant les repas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
                <?php else: ?>
                <!-- Default first empty card -->
                <div class="bg-white border-2 border-slate-100 rounded-xl p-5 relative transition-colors hover:border-primary/40 med-card slide-in" id="med-0">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center med-num">1</div>
                            <span class="text-sm font-bold text-slate-500">Médicament 1</span>
                        </div>
                        <button type="button" onclick="this.closest('.med-card').remove(); renumberCards();"
                                class="p-1.5 text-error hover:bg-red-50 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nom du médicament *</label>
                            <input type="text" name="med_nom[]" placeholder="Ex: Amoxicilline" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 med-nom-input"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catégorie</label>
                            <input type="text" name="med_categorie[]" placeholder="Ex: Antibiotique" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Dosage</label>
                            <select name="med_dosage[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <option>500mg</option><option>1g</option><option>1000mg</option><option>200mg</option><option>250mg</option><option>5ml</option><option>10ml</option><option>1 sachet</option><option>1 suppositoire</option><option>1 ampoule</option><option>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fréquence</label>
                            <select name="med_frequence[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <option>1 fois par jour</option><option>2 fois par jour</option><option>3 fois par jour</option><option>4 fois par jour</option><option>Matin et soir</option><option>Le soir</option><option>Au besoin</option><option>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Durée</label>
                            <select name="med_duree[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">-- Choisir --</option>
                                <option>3 jours</option><option>5 jours</option><option>7 jours</option><option>10 jours</option><option>14 jours</option><option>1 mois</option><option>En continu</option><option>Usage unique</option><option>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Instructions pour le patient</label>
                            <input type="text" name="med_instructions[]" placeholder="Ex: Prendre pendant les repas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                        </div>
                    </div>
                </div>
                <?php endif ?>
            </div>
            
            <button type="button" onclick="addMedCard()"
                    class="mt-4 w-full border-2 border-dashed border-slate-200 hover:border-primary/40 hover:bg-blue-50 rounded-xl py-3 text-sm font-bold text-slate-500 hover:text-primary transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">add_circle</span> Ajouter un médicament
            </button>
            <p id="med-error" class="text-error text-xs font-bold mt-2 hidden">Vous devez prescrire au moins un médicament.</p>
        </div>

        <!-- Notes Pharmacien -->
        <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] border border-slate-100">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit_note</span>
                Notes pour le pharmacien <span class="text-xs font-normal text-slate-400">(Facultatif)</span>
            </h2>
            <textarea name="note_pharmacien" rows="3"
                      class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none"
                      placeholder="Instructions spécifiques, substitutions autorisées ou non..."><?= htmlspecialchars($ordonnance['note_pharmacien'] ?? '') ?></textarea>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="<?= $isEdit ? '/integration/dossier/ordonnance/view?id='.$ordonnance['id_ordonnance'] : '/integration/dossier/patients' ?>"
               class="px-6 py-3 rounded-lg font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                Annuler
            </a>
            <button type="submit" onclick="return validateMedicaments()"
                    class="px-8 py-3 rounded-lg font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                <?= $isEdit ? 'Mettre à jour l\'ordonnance' : 'Générer l\'ordonnance' ?>
            </button>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
@keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: none; } }
.slide-in { animation: slideIn .25s ease forwards; }
.modern-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.1rem;
    padding-right: 2.5rem;
}
</style>

<script>
let medCounter = <?= count($medicaments ?: [1]) ?>;

function addMedCard() {
    const list = document.getElementById('med-list');
    const newCard = document.createElement('div');
    newCard.className = 'bg-white border-2 border-slate-100 rounded-xl p-5 relative transition-colors hover:border-primary/40 med-card slide-in';
    newCard.id = `med-${medCounter}`;
    newCard.innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center med-num">
                    ${medCounter + 1}
                </div>
                <span class="text-sm font-bold text-slate-800">Médicament</span>
            </div>
            <button type="button" onclick="this.closest('.med-card').remove(); renumberCards();"
                    class="p-1.5 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nom du médicament *</label>
                <input type="text" name="med_nom[]" placeholder="Ex: Amoxicilline" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 med-nom-input"/>
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catégorie</label>
                <input type="text" name="med_categorie[]" placeholder="Ex: Antibiotique" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Dosage</label>
                <select name="med_dosage[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                    <option value="">-- Choisir --</option>
                    <option>500mg</option><option>1g</option><option>1000mg</option><option>200mg</option><option>250mg</option><option>5ml</option><option>10ml</option><option>1 sachet</option><option>1 suppositoire</option><option>1 ampoule</option><option>Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fréquence</label>
                <select name="med_frequence[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                    <option value="">-- Choisir --</option>
                    <option>1 fois par jour</option><option>2 fois par jour</option><option>3 fois par jour</option><option>4 fois par jour</option><option>Matin et soir</option><option>Le soir</option><option>Au besoin</option><option>Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Durée</label>
                <select name="med_duree[]" class="modern-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all cursor-pointer hover:bg-slate-100">
                    <option value="">-- Choisir --</option>
                    <option>3 jours</option><option>5 jours</option><option>7 jours</option><option>10 jours</option><option>14 jours</option><option>1 mois</option><option>En continu</option><option>Usage unique</option><option>Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Instructions pour le patient</label>
                <input type="text" name="med_instructions[]" placeholder="Ex: Prendre pendant les repas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
        </div>
    `;
    list.appendChild(newCard);
    medCounter++;
    renumberCards();
    document.getElementById('med-error').classList.add('hidden');
}

function renumberCards() {
    const cards = document.querySelectorAll('.med-card');
    cards.forEach((card, index) => {
        const badge = card.querySelector('.med-num');
        if (badge) badge.textContent = index + 1;
    });
}

function validateMedicaments() {
    let isValid = true;
    
    // Validate Date
    const dateInput = document.getElementById('date_emission');
    const dateError = document.getElementById('date-error');
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateInput.value.match(dateRegex)) {
        dateError.classList.remove('hidden');
        isValid = false;
    } else {
        dateError.classList.add('hidden');
    }

    // Validate Cards
    const cards = document.querySelectorAll('.med-card');
    const medError = document.getElementById('med-error');
    if (cards.length === 0) {
        medError.textContent = "Vous devez prescrire au moins un médicament.";
        medError.classList.remove('hidden');
        isValid = false;
    } else {
        let hasEmptyNom = false;
        const nomInputs = document.querySelectorAll('.med-nom-input');
        nomInputs.forEach(input => {
            if (input.value.trim() === '') {
                hasEmptyNom = true;
                input.classList.add('border-error');
                input.classList.add('ring-2');
                input.classList.add('ring-error/20');
            } else {
                input.classList.remove('border-error');
                input.classList.remove('ring-2');
                input.classList.remove('ring-error/20');
            }
        });
        
        if (hasEmptyNom) {
            medError.textContent = "Veuillez remplir le nom de tous les médicaments obligatoires.";
            medError.classList.remove('hidden');
            isValid = false;
        } else {
            medError.classList.add('hidden');
        }
    }
    
    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    // Real-time date validation
    const dateInput = document.getElementById('date_emission');
    const dateError = document.getElementById('date-error');
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    
    if (dateInput) {
        dateInput.addEventListener('input', function() {
            if (this.value === '' || !this.value.match(dateRegex)) {
                this.classList.add('border-error', 'ring-error/20');
                if (dateError) dateError.classList.remove('hidden');
            } else {
                this.classList.remove('border-error', 'ring-error/20');
                if (dateError) dateError.classList.add('hidden');
            }
        });
    }

    // Real-time med_nom and other fields validation & input control
    document.getElementById('med-list').addEventListener('input', function(e) {
        if (!e.target) return;

        // 1. Strict input control (auto-remove invalid characters AND NUMBERS)
        if (['med_nom[]', 'med_categorie[]', 'med_instructions[]'].includes(e.target.name)) {
            // Text fields: ONLY letters, spaces, hyphens, and apostrophes (NO NUMBERS, NO SYMBOLS)
            e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s\-']/g, '');
        }

        // 2. Required field validation for med_nom
        if (e.target.classList.contains('med-nom-input')) {
            const medError = document.getElementById('med-error');
            if (e.target.value.trim() === '') {
                e.target.classList.add('border-error', 'ring-2', 'ring-error/20');
            } else {
                e.target.classList.remove('border-error', 'ring-2', 'ring-error/20');
                
                let hasEmpty = false;
                document.querySelectorAll('.med-nom-input').forEach(input => {
                    if (input.value.trim() === '') hasEmpty = true;
                });
                if (!hasEmpty && medError) medError.classList.add('hidden');
            }
        }
    });

    // Real-time control for Note Pharmacien (no dangerous symbols)
    const noteArea = document.querySelector('textarea[name="note_pharmacien"]');
    if (noteArea) {
        noteArea.addEventListener('input', function() {
            this.value = this.value.replace(/[<>{}\[\]@#\$\^&\*~`=\+\|\\]/g, '');
        });
    }
});
</script>
