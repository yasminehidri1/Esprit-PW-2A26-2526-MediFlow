<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — <?= $mode === 'edit' ? 'Modifier' : 'Créer' ?> une Ordonnance</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config={darkMode:"class",theme:{extend:{colors:{"surface-container-low":"#f2f4f6","outline":"#727783","surface-container-highest":"#e0e3e5","tertiary-fixed":"#84f5e8","surface-container-lowest":"#ffffff","on-primary":"#ffffff","background":"#f7f9fb","tertiary":"#005851","primary":"#004d99","error-container":"#ffdad6","on-tertiary":"#ffffff","primary-fixed-dim":"#a9c7ff","on-surface":"#191c1e","error":"#ba1a1a","surface-container":"#eceef0","surface":"#f7f9fb","on-error":"#ffffff","primary-container":"#1565c0","on-surface-variant":"#424752"},fontFamily:{headline:["Manrope"],body:["Inter"]},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"}}}}
    </script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;}
        body{font-family:'Inter',sans-serif;background:#f7f9fb;}
        h1,h2,h3{font-family:'Manrope',sans-serif;}
        .field-label{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#727783;margin-bottom:.35rem;}
        .field-input{display:block;width:100%;background:#f2f4f6;border:1.5px solid #e0e3e5;border-radius:.5rem;padding:.6rem .9rem;font-size:.875rem;color:#191c1e;outline:none;transition:border-color .15s,box-shadow .15s;}
        .field-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.12);}
        .med-card{background:#fff;border:1.5px solid #e0e3e5;border-radius:.75rem;padding:1.25rem;position:relative;transition:border-color .15s,box-shadow .15s;}
        .med-card:hover{border-color:#a9c7ff;box-shadow:0 4px 20px rgba(0,77,153,.06);}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .fade-in{animation:fadeIn .35s ease forwards;}
        @keyframes slideIn{from{opacity:0;transform:translateX(-10px);}to{opacity:1;transform:none;}}
        .slide-in{animation:slideIn .25s ease forwards;}
    </style>
</head>
<body class="bg-surface">

<?php
$patientName = htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? ''));
$activePage  = 'ordonnance';
$breadcrumb  = [
    ['label' => 'Mes Patients',    'url' => 'index.php?page=patients'],
    ['label' => $patientName,      'url' => 'index.php?page=dossier&patient_id=' . ($patient['id_PK'] ?? 0)],
    ['label' => $mode === 'edit' ? 'Modifier l\'ordonnance' : 'Nouvelle ordonnance'],
];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 min-h-screen pt-24 pb-16 px-10">
<div class="max-w-4xl mx-auto">

    <!-- Page Header -->
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1">
            <?= $mode === 'edit' ? 'Modifier l\'ordonnance' : 'Nouvelle ordonnance' ?>
        </h1>
        <p class="text-slate-500 text-sm">
            Patient : <span class="font-semibold text-primary"><?= $patientName ?></span>
            <?php if (!empty($consultation['diagnostic'])): ?>
            &bull; Diagnostic : <span class="font-medium"><?= htmlspecialchars($consultation['diagnostic']) ?></span>
            <?php endif ?>
        </p>
    </div>

    <?php if (!empty($validation_errors)): ?>
    <div class="mb-6 p-4 rounded-xl bg-error-container/20 border border-error/30">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-error text-2xl">error</span>
            <div>
                <h3 class="font-bold text-error mb-2">Erreurs de saisie</h3>
                <ul class="text-sm text-error space-y-1">
                    <?php foreach ($validation_errors as $field => $msg): ?>
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

    <form method="POST"
          action="<?= $mode === 'edit'
              ? 'index.php?page=ordonnance&action=edit&id=' . ($ordonnance['id_ordonnance'] ?? '')
              : 'index.php?page=ordonnance&action=add' ?>"
          class="space-y-6 fade-in">

        <input type="hidden" name="id_consultation" value="<?= $consultation['id_consultation'] ?? '' ?>"/>
        <?php if ($mode === 'edit'): ?>
        <input type="hidden" name="id" value="<?= $ordonnance['id_ordonnance'] ?? '' ?>"/>
        <?php endif ?>

        <!-- Date & Statut -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">event</span>
                Informations générales
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Date d'émission</label>
                    <input type="text" name="date_emission"
                           value="<?= $mode === 'edit' ? htmlspecialchars($ordonnance['date_emission'] ?? '') : date('Y-m-d') ?>"
                           class="field-input" placeholder="AAAA-MM-JJ"/>
                </div>
                <?php if ($mode === 'edit'): ?>
                <div>
                    <label class="field-label">Statut</label>
                    <select name="statut" class="field-input">
                        <?php foreach (['active','archivee','annulee'] as $s): ?>
                        <option <?= ($ordonnance['statut'] ?? 'active') === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <?php endif ?>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
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
                <div class="med-card slide-in" id="med-<?= $idx ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">
                                <?= $idx + 1 ?>
                            </div>
                            <span class="text-sm font-bold text-on-surface">
                                <?= htmlspecialchars($med['nom'] ?? 'Médicament ' . ($idx + 1)) ?>
                            </span>
                        </div>
                        <button type="button" onclick="this.closest('.med-card').remove(); renumberCards();"
                                class="p-1.5 text-error hover:bg-error-container/30 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="field-label">Nom du médicament *</label>
                            <input type="text" name="med_nom[]"
                                   value="<?= htmlspecialchars($med['nom'] ?? '') ?>"
                                   placeholder="Ex: Amoxicilline" class="field-input"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="field-label">Catégorie</label>
                            <input type="text" name="med_categorie[]"
                                   value="<?= htmlspecialchars($med['categorie'] ?? '') ?>"
                                   placeholder="Ex: Antibiotique" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Dosage</label>
                            <input type="text" name="med_dosage[]"
                                   value="<?= htmlspecialchars($med['dosage'] ?? '') ?>"
                                   placeholder="Ex: 500mg" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Fréquence</label>
                            <input type="text" name="med_frequence[]"
                                   value="<?= htmlspecialchars($med['frequence'] ?? '') ?>"
                                   placeholder="Ex: 3 fois par jour" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Durée</label>
                            <input type="text" name="med_duree[]"
                                   value="<?= htmlspecialchars($med['duree'] ?? '') ?>"
                                   placeholder="Ex: 7 jours" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Instructions pour le patient</label>
                            <input type="text" name="med_instructions[]"
                                   value="<?= htmlspecialchars($med['instructions'] ?? '') ?>"
                                   placeholder="Ex: Prendre pendant les repas" class="field-input"/>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
                <?php else: ?>
                <!-- Default first empty card -->
                <div class="med-card slide-in" id="med-0">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</div>
                            <span class="text-sm font-bold text-slate-500">Médicament 1</span>
                        </div>
                        <button type="button" onclick="this.closest('.med-card').remove(); renumberCards();"
                                class="p-1.5 text-error hover:bg-error-container/30 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="field-label">Nom du médicament *</label>
                            <input type="text" name="med_nom[]" placeholder="Ex: Amoxicilline" class="field-input"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="field-label">Catégorie</label>
                            <input type="text" name="med_categorie[]" placeholder="Ex: Antibiotique" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Dosage</label>
                            <input type="text" name="med_dosage[]" placeholder="Ex: 500mg" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Fréquence</label>
                            <input type="text" name="med_frequence[]" placeholder="Ex: 3 fois par jour" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Durée</label>
                            <input type="text" name="med_duree[]" placeholder="Ex: 7 jours" class="field-input"/>
                        </div>
                        <div>
                            <label class="field-label">Instructions patient</label>
                            <input type="text" name="med_instructions[]" placeholder="Ex: Prendre pendant les repas" class="field-input"/>
                        </div>
                    </div>
                </div>
                <?php endif ?>
            </div>

            <!-- Add med button (secondary) -->
            <button type="button" onclick="addMedCard()"
                    class="mt-4 w-full border-2 border-dashed border-slate-200 hover:border-primary/40 rounded-xl py-3 text-sm font-semibold text-slate-400 hover:text-primary transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">add_circle</span>
                Ajouter un médicament
            </button>
        </div>

        <!-- Note pharmacien -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">local_pharmacy</span>
                Note au Pharmacien
            </h2>
            <textarea name="note_pharmacien" rows="3" class="field-input resize-none"
                      placeholder="Instructions spéciales pour le pharmacien (substitution, marque obligatoire, etc.)..."><?= htmlspecialchars($ordonnance['note_pharmacien'] ?? '') ?></textarea>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3 pb-4">
            <a href="<?= $mode === 'edit'
                ? 'index.php?page=ordonnance&id=' . ($ordonnance['id_ordonnance'] ?? '')
                : 'index.php?page=dossier&patient_id=' . ($patient['id_PK'] ?? '') ?>"
               class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-200 transition-colors text-sm">
                Annuler
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                <?= $mode === 'edit' ? 'Mettre à jour l\'ordonnance' : 'Créer l\'ordonnance' ?>
            </button>
        </div>
    </form>
</div>
</main>

<style>
.field-input.is-invalid{border-color:#ba1a1a!important;box-shadow:0 0 0 3px rgba(186,26,26,.1)!important;}
.field-input.is-valid{border-color:#059669!important;box-shadow:0 0 0 3px rgba(5,150,105,.08)!important;}
.error-msg{display:none;font-size:0.75rem;color:#ba1a1a;margin-top:0.25rem;font-weight:600;}
.error-msg.show{display:block;}
.success-msg{display:none;font-size:0.75rem;color:#059669;margin-top:0.25rem;font-weight:600;}
.success-msg.show{display:block;}
.med-card.is-invalid{border-color:#ba1a1a!important;background:#ffdad6!important;}
.med-card.is-valid{border-color:#059669!important;background:#f0fdf4!important;}
</style>
<script>
let medCount = <?= max(count($medicaments ?? []), 1) ?>;

// Validate main form fields
const VALIDATORS = {
    date_emission: (val) => {
        if (!val) return {valid: false, msg: 'Date requise'};
        const d = new Date(val);
        return d <= new Date() ? {valid: true, msg: '✓'} : {valid: false, msg: 'Pas de date future'};
    },
    note_pharmacien: (val) => {
        if (!val) return {valid: true, msg: ''};
        return val.length <= 500 ? {valid: true, msg: `${val.length}/500`} : {valid: false, msg: 'Max 500 caractères'};
    },
};

function validateField(field) {
    const validator = VALIDATORS[field.name];
    if (!validator) return;

    const result = validator(field.value);

    field.classList.toggle('is-invalid', !result.valid && field.value.trim() !== '');
    field.classList.toggle('is-valid', result.valid && field.value.trim() !== '');

    let errorEl = field.parentElement.querySelector('.error-msg');
    let successEl = field.parentElement.querySelector('.success-msg');

    if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'error-msg';
        field.parentElement.appendChild(errorEl);
    }
    if (!successEl) {
        successEl = document.createElement('div');
        successEl.className = 'success-msg';
        field.parentElement.appendChild(successEl);
    }

    if (!result.valid && field.value.trim() !== '') {
        errorEl.textContent = result.msg;
        errorEl.classList.add('show');
        successEl.classList.remove('show');
    } else if (result.valid && field.value.trim() !== '') {
        successEl.textContent = result.msg;
        successEl.classList.add('show');
        errorEl.classList.remove('show');
    } else {
        errorEl.classList.remove('show');
        successEl.classList.remove('show');
    }
}

// Validate medications in real-time
function validateMedicaments() {
    const meds = document.querySelectorAll('.med-card');
    let hasError = false;

    meds.forEach(card => {
        const nomInput = card.querySelector('input[name="med_nom[]"]');
        if (!nomInput.value.trim()) {
            card.classList.add('is-invalid');
            hasError = true;
        } else {
            card.classList.add('is-valid');
            card.classList.remove('is-invalid');
        }

        nomInput.addEventListener('input', () => {
            if (nomInput.value.trim()) {
                card.classList.remove('is-invalid');
                card.classList.add('is-valid');
            } else {
                card.classList.add('is-invalid');
                card.classList.remove('is-valid');
            }
        });
    });

    return !hasError && meds.length > 0;
}

function addMedCard() {
    medCount++;
    const html = `
    <div class="med-card slide-in" id="med-${medCount}">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-2">
                <div class="med-num w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">${medCount}</div>
                <span class="text-sm font-bold text-slate-500">Médicament ${medCount}</span>
            </div>
            <button type="button" onclick="this.closest('.med-card').remove(); validateMedicaments();"
                    class="p-1.5 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2 md:col-span-1">
                <label class="field-label">Nom du médicament *</label>
                <input type="text" name="med_nom[]" placeholder="Ex: Ibuprofène" class="field-input"/>
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="field-label">Catégorie</label>
                <input type="text" name="med_categorie[]" placeholder="Ex: Anti-inflammatoire" class="field-input"/>
            </div>
            <div>
                <label class="field-label">Dosage</label>
                <input type="text" name="med_dosage[]" placeholder="Ex: 400mg" class="field-input"/>
            </div>
            <div>
                <label class="field-label">Fréquence</label>
                <input type="text" name="med_frequence[]" placeholder="Ex: 2 fois par jour" class="field-input"/>
            </div>
            <div>
                <label class="field-label">Durée</label>
                <input type="text" name="med_duree[]" placeholder="Ex: 5 jours" class="field-input"/>
            </div>
            <div>
                <label class="field-label">Instructions patient</label>
                <input type="text" name="med_instructions[]" placeholder="Ex: Avec les repas" class="field-input"/>
            </div>
        </div>
    </div>`;
    document.getElementById('med-list').insertAdjacentHTML('beforeend', html);
    validateMedicaments();
}

function renumberCards() {
    const cards = document.querySelectorAll('#med-list .med-card');
    cards.forEach((card, i) => {
        const num = card.querySelector('.med-num');
        if (num) num.textContent = i + 1;
    });
    medCount = cards.length;
}

// Setup validation on page load
document.addEventListener('DOMContentLoaded', () => {
    // Validate main fields
    Object.keys(VALIDATORS).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('input', () => validateField(field));
            field.addEventListener('blur', () => validateField(field));
            validateField(field);
        }
    });

    // Validate medications
    validateMedicaments();

    // Monitor form submission
    document.querySelector('form')?.addEventListener('submit', (e) => {
        if (!validateMedicaments()) {
            e.preventDefault();
            alert('❌ Au moins un médicament avec un nom est requis!');
        }
    });
});
</script>
</body>
</html>
