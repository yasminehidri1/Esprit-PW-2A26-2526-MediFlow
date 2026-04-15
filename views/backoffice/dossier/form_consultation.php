<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — <?= $mode === 'edit' ? 'Modifier' : 'Modifier' ?> Consultation</title>
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
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .fade-in{animation:fadeIn .35s ease forwards;}
    </style>
</head>
<body class="bg-surface">

<?php
$patientName = htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? ''));
$activePage  = 'patients';
$breadcrumb  = [
    ['label' => 'Mes Patients',     'url' => 'index.php?page=patients'],
    ['label' => 'Dossier — ' . $patientName, 'url' => 'index.php?page=dossier&patient_id=' . ($patient['id_PK'] ?? 0)],
    ['label' => $mode === 'edit' ? 'Modifier consultation' : 'Nouvelle consultation'],
];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 min-h-screen pt-24 pb-16 px-10">
<div class="max-w-3xl mx-auto">

    <!-- Page Header -->
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1">
            <?= $mode === 'edit' ? 'Modifier la consultation' : 'Nouvelle consultation' ?>
        </h1>
        <p class="text-slate-500 text-sm">
            Patient : <span class="font-semibold text-primary"><?= $patientName ?></span>
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
          action="index.php?page=dossier&action=<?= $mode ?>&id=<?= $consultation['id_consultation'] ?? '' ?>"
          class="space-y-6 fade-in">

        <input type="hidden" name="id_patient" value="<?= $patient['id_PK'] ?? '' ?>"/>
        <?php if ($mode === 'edit'): ?>
        <input type="hidden" name="id" value="<?= $consultation['id_consultation'] ?? '' ?>"/>
        <?php endif ?>

        <!-- Section: Infos de base -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">event_note</span>
                Informations de la consultation
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Date & heure</label>
                    <input type="datetime-local" name="date_consultation" required
                           value="<?= $mode === 'edit' ? date('Y-m-d\TH:i', strtotime($consultation['date_consultation'])) : date('Y-m-d\TH:i') ?>"
                           class="field-input"/>
                </div>
                <div>
                    <label class="field-label">Type de consultation</label>
                    <select name="type_consultation" class="field-input">
                        <?php
                        $types = ['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement',
                                  'Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'];
                        foreach ($types as $t):
                        ?>
                        <option <?= ($consultation['type_consultation'] ?? '') === $t ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <label class="field-label">Diagnostic</label>
                    <input type="text" name="diagnostic"
                           value="<?= htmlspecialchars($consultation['diagnostic'] ?? '') ?>"
                           placeholder="Ex: Hypertension Artérielle" class="field-input"
                           data-validate="text-only" oninput="validateField(this)" maxlength="150"/>
                </div>
                <div>
                    <label class="field-label">Compte-rendu clinique</label>
                    <textarea name="compte_rendu" rows="4" class="field-input resize-none"
                              placeholder="Observations, conclusions, recommandations..."><?= htmlspecialchars($consultation['compte_rendu'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Section: Constantes vitales -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">monitor_heart</span>
                Constantes Vitales
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Tension artérielle</label>
                    <input type="text" name="tension_arterielle"
                           value="<?= htmlspecialchars($consultation['tension_arterielle'] ?? '') ?>"
                           placeholder="Ex: 12/8" class="field-input"
                           data-validate="tension" oninput="validateField(this)" maxlength="10"/>
                </div>
                <div>
                    <label class="field-label">Rythme cardiaque (bpm)</label>
                    <input type="number" name="rythme_cardiaque" min="30" max="300"
                           value="<?= htmlspecialchars($consultation['rythme_cardiaque'] ?? '') ?>"
                           placeholder="72" class="field-input"
                           data-validate="number-range" data-min="30" data-max="300"
                           oninput="validateField(this)"/>
                </div>
                <div>
                    <label class="field-label">Poids (kg)</label>
                    <input type="number" step="0.1" name="poids"
                           value="<?= htmlspecialchars($consultation['poids'] ?? '') ?>"
                           placeholder="75.5" class="field-input"/>
                </div>
                <div>
                    <label class="field-label">Saturation O² (%)</label>
                    <input type="number" name="saturation_o2" min="0" max="100"
                           value="<?= htmlspecialchars($consultation['saturation_o2'] ?? '') ?>"
                           placeholder="98" class="field-input"/>
                </div>
            </div>
        </div>

        <!-- Section: Antécédents -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Antécédents Médicaux
                </h2>
                <button type="button" onclick="addAntRow()"
                        class="text-xs font-bold text-primary border border-primary/30 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="ant-rows" class="space-y-3">
                <?php if (!empty($antecedents)): ?>
                <?php foreach ($antecedents as $ant): ?>
                <div class="ant-row grid grid-cols-12 gap-2 items-center">
                    <input type="number" name="ant_annee[]"
                           value="<?= htmlspecialchars($ant['annee'] ?? '') ?>"
                           placeholder="Année" class="field-input col-span-2"/>
                    <input type="text" name="ant_titre[]"
                           value="<?= htmlspecialchars($ant['titre'] ?? '') ?>"
                           placeholder="Titre" class="field-input col-span-4"/>
                    <input type="text" name="ant_desc[]"
                           value="<?= htmlspecialchars($ant['description'] ?? '') ?>"
                           placeholder="Description" class="field-input col-span-5"/>
                    <button type="button" onclick="this.closest('.ant-row').remove()"
                            class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
                <?php endforeach ?>
                <?php endif ?>
            </div>
            <p id="ant-empty" class="text-sm text-slate-400 mt-2 <?= !empty($antecedents) ? 'hidden' : '' ?>">
                Aucun antécédent ajouté. Cliquez sur "Ajouter" pour en saisir.
            </p>
        </div>

        <!-- Section: Allergies -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error/70">warning</span>
                    Allergies
                </h2>
                <button type="button" onclick="addAlgRow()"
                        class="text-xs font-bold text-primary border border-primary/30 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="alg-rows" class="space-y-3">
                <?php if (!empty($allergies)): ?>
                <?php foreach ($allergies as $alg): ?>
                <div class="alg-row grid grid-cols-12 gap-2 items-center">
                    <input type="text" name="allergie_nom[]"
                           value="<?= htmlspecialchars($alg['nom'] ?? '') ?>"
                           placeholder="Allergie (ex: Pénicilline)" class="field-input col-span-7"/>
                    <select name="allergie_niveau[]" class="field-input col-span-4">
                        <?php foreach (['Faible','Modéré','Élevé'] as $niv): ?>
                        <option <?= ($alg['niveau'] ?? '') === $niv ? 'selected' : '' ?>><?= $niv ?></option>
                        <?php endforeach ?>
                    </select>
                    <button type="button" onclick="this.closest('.alg-row').remove()"
                            class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
                <?php endforeach ?>
                <?php endif ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pb-4">
            <a href="index.php?page=dossier&patient_id=<?= $patient['id_PK'] ?? '' ?>"
               class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-200 transition-colors text-sm">
                Annuler
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                <?= $mode === 'edit' ? 'Mettre à jour' : 'Enregistrer' ?>
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
</style>
<script>
// ── Real-time validation rules ────────────────────────────────
const VALIDATORS = {
    date_consultation: (val) => {
        if (!val) return {valid: false, msg: 'Date requise'};
        const dt = new Date(val.replace('T', ' '));
        return dt <= new Date() ? {valid: true, msg: '✓'} : {valid: false, msg: 'Pas de date future'};
    },
    type_consultation: (val) => {
        const valid = ['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement',
                      'Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'];
        return valid.includes(val) ? {valid: true, msg: '✓'} : {valid: false, msg: 'Type invalide'};
    },
    diagnostic: (val) => {
        if (!val) return {valid: true, msg: ''};
        return val.length <= 150 ? {valid: true, msg: `${val.length}/150`} : {valid: false, msg: 'Max 150 caractères'};
    },
    compte_rendu: (val) => {
        if (!val) return {valid: true, msg: ''};
        return val.length <= 5000 ? {valid: true, msg: `${val.length}/5000`} : {valid: false, msg: 'Max 5000 caractères'};
    },
    tension_arterielle: (val) => {
        if (!val) return {valid: true, msg: ''};
        const match = val.match(/^(\d{1,3})\/(\d{1,3})$/);
        if (!match) return {valid: false, msg: 'Format: 120/80'};
        const sys = parseInt(match[1]);
        const dia = parseInt(match[2]);
        if (sys < 60 || sys > 250 || dia < 30 || dia > 150 || sys <= dia) {
            return {valid: false, msg: 'Tension invalide'};
        }
        return {valid: true, msg: '✓'};
    },
    rythme_cardiaque: (val) => {
        if (!val) return {valid: true, msg: ''};
        const rate = parseInt(val);
        return (rate >= 30 && rate <= 300) ? {valid: true, msg: '✓'} : {valid: false, msg: '30-300 BPM'};
    },
    poids: (val) => {
        if (!val) return {valid: true, msg: ''};
        const w = parseFloat(val);
        return (w > 2 && w < 500) ? {valid: true, msg: '✓'} : {valid: false, msg: '2-500 kg'};
    },
    saturation_o2: (val) => {
        if (!val) return {valid: true, msg: ''};
        const sat = parseInt(val);
        return (sat >= 0 && sat <= 100) ? {valid: true, msg: '✓'} : {valid: false, msg: '0-100%'};
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

document.addEventListener('DOMContentLoaded', () => {
    Object.keys(VALIDATORS).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('input', () => validateField(field));
            field.addEventListener('blur', () => validateField(field));
            // Initial validation
            validateField(field);
        }
    });
});

function addAntRow() {
    document.getElementById('ant-empty')?.classList.add('hidden');
    document.getElementById('ant-rows').insertAdjacentHTML('beforeend', `
        <div class="ant-row grid grid-cols-12 gap-2 items-center">
            <input type="number" name="ant_annee[]" placeholder="Année" class="field-input col-span-2" min="1900" max="2099"/>
            <input type="text"   name="ant_titre[]" placeholder="Titre"       class="field-input col-span-4"/>
            <input type="text"   name="ant_desc[]"  placeholder="Description" class="field-input col-span-5"/>
            <button type="button" onclick="this.closest('.ant-row').remove()"
                    class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>`);
}
function addAlgRow() {
    document.getElementById('alg-rows').insertAdjacentHTML('beforeend', `
        <div class="alg-row grid grid-cols-12 gap-2 items-center">
            <input type="text" name="allergie_nom[]" placeholder="Allergie" class="field-input col-span-7"/>
            <select name="allergie_niveau[]" class="field-input col-span-4">
                <option>Faible</option><option selected>Modéré</option><option>Élevé</option>
            </select>
            <button type="button" onclick="this.closest('.alg-row').remove()"
                    class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>`);
}
</script>
</body>
</html>
