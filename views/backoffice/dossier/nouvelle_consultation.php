<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Nouvelle Consultation</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config={darkMode:"class",theme:{extend:{colors:{"surface-container-low":"#f2f4f6","outline":"#727783","surface-container-highest":"#e0e3e5","tertiary-fixed":"#84f5e8","surface-container-lowest":"#ffffff","on-primary":"#ffffff","background":"#f7f9fb","tertiary":"#005851","primary":"#004d99","error-container":"#ffdad6","on-tertiary":"#ffffff","primary-fixed-dim":"#a9c7ff","on-surface":"#191c1e","error":"#ba1a1a","surface-container":"#eceef0","surface":"#f7f9fb","on-error":"#ffffff","primary-container":"#1565c0","on-surface-variant":"#424752"},fontFamily:{headline:["Manrope"],body:["Inter"]},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"}}}}
    </script>
    <style>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;}
        body{font-family:'Inter',sans-serif;background:#f7f9fb;}
        h1,h2,h3{font-family:'Manrope',sans-serif;}
        /* Field styles */
        .field-label{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#727783;margin-bottom:.35rem;}
        .field-input{display:block;width:100%;background:#f2f4f6;border:1.5px solid #e0e3e5;border-radius:.5rem;padding:.6rem .9rem;font-size:.875rem;color:#191c1e;outline:none;transition:border-color .15s,box-shadow .15s;}
        .field-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.12);}
        .field-input.is-invalid{border-color:#ba1a1a;box-shadow:0 0 0 3px rgba(186,26,26,.1);}
        .field-input.is-valid{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.08);}
        .field-error{display:none;font-size:.7rem;color:#ba1a1a;font-weight:600;margin-top:.25rem;}
        .field-success{display:none;font-size:.7rem;color:#059669;font-weight:600;margin-top:.25rem;}
        .field-input.is-invalid + .field-error{display:block;}
        .field-input.is-valid + .field-success{display:block;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .fade-in{animation:fadeIn .35s ease forwards;}
        /* Patient card selector */
        .patient-card{border:2px solid #e0e3e5;border-radius:.75rem;padding:1rem;cursor:pointer;transition:all .15s;}
        .patient-card:hover{border-color:#004d99;background:#f2f4f6;}
        .patient-card.selected{border-color:#004d99;background:#dae5ff;box-shadow:0 0 0 3px rgba(0,77,153,.1);}
        .patient-card .check{display:none;}
        .patient-card.selected .check{display:inline-flex;}
    </style>
    </style>
</head>
<body class="bg-surface">

<?php
$activePage = 'patients';
$breadcrumb = [
    ['label' => 'Mes Patients', 'url' => 'index.php?page=patients'],
    ['label' => 'Nouvelle Consultation'],
];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 min-h-screen pt-24 pb-16 px-10">
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1">Nouvelle Consultation</h1>
        <p class="text-slate-500 text-sm">Sélectionnez un patient puis remplissez les détails de la consultation.</p>
    </div>

    <form id="consult-form" method="POST" action="index.php?page=nouvelle_consultation" novalidate class="space-y-6 fade-in">

        <!-- ══ STEP 1: Patient selection ═══════════════════════════ -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">1</span>
                Sélectionner le patient
            </h2>

            <!-- Hidden input that gets set when a card is clicked -->
            <input type="hidden" name="id_patient" id="selected-patient-id" required/>
            <div id="patient-select-error" class="hidden text-xs text-error font-semibold mb-3 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">error</span>
                Veuillez sélectionner un patient.
            </div>

            <!-- Patient search -->
            <div class="relative mb-4">
                <input type="text" id="patient-search-filter" placeholder="Filtrer les patients..."
                       class="field-input pl-10"/>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            </div>

            <!-- Patient cards grid -->
            <div class="grid grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1" id="patient-cards">
                <?php foreach ($allPatients as $i => $p):
                    $initials = strtoupper(substr($p['prenom'], 0, 1) . substr($p['nom'], 0, 1));
                    $colors   = ['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700',
                                 'bg-amber-100 text-amber-700','bg-rose-100 text-rose-700'];
                    $color    = $colors[$i % count($colors)];
                ?>
                <div class="patient-card flex items-center gap-3"
                     data-id="<?= $p['id_PK'] ?>"
                     data-name="<?= strtolower($p['prenom'] . ' ' . $p['nom'] . ' ' . $p['mail']) ?>"
                     onclick="selectPatient(this)">
                    <div class="w-10 h-10 rounded-full <?= $color ?> flex items-center justify-center font-bold text-sm shrink-0">
                        <?= $initials ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-on-surface truncate">
                            <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                        </p>
                        <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($p['mail']) ?></p>
                    </div>
                    <div class="check w-5 h-5 rounded-full bg-primary items-center justify-center text-white shrink-0">
                        <span class="material-symbols-outlined text-sm">check</span>
                    </div>
                </div>
                <?php endforeach ?>
                <?php if (empty($allPatients)): ?>
                <div class="col-span-2 text-center py-8 text-slate-400">
                    <span class="material-symbols-outlined text-3xl block mb-2">person_off</span>
                    Aucun patient enregistré.
                </div>
                <?php endif ?>
            </div>
        </div>

        <!-- ══ STEP 2: Consultation details ════════════════════════ -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">2</span>
                Informations de la consultation
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Date & heure *</label>
                    <input type="datetime-local" name="date_consultation" id="f-date"
                           value="<?= date('Y-m-d\TH:i') ?>" class="field-input" required/>
                    <span class="field-error">Champ requis.</span>
                </div>
                <div>
                    <label class="field-label">Type de consultation *</label>
                    <select name="type_consultation" id="f-type" class="field-input" required>
                        <option value="">-- Choisir --</option>
                        <option>Contrôle annuel</option>
                        <option>Bilan Annuel</option>
                        <option>Suivi Spécialisé</option>
                        <option>Suivi Traitement</option>
                        <option>Téléconsultation</option>
                        <option>Consultation urgente</option>
                        <option>Contrôle Post-Op</option>
                        <option>Symptômes Grippaux</option>
                    </select>
                    <span class="field-error">Veuillez sélectionner un type.</span>
                </div>
            </div>
            <div class="mt-4">
                <label class="field-label">Diagnostic</label>
                <input type="text" name="diagnostic" id="f-diagnostic"
                       class="field-input"
                       placeholder="Ex : Hypertension Artérielle"
                       data-validate="text-only"
                       maxlength="150"/>
                <span class="field-error">Lettres, espaces, tirets et apostrophes uniquement.</span>
            </div>
            <div class="mt-4">
                <label class="field-label">Compte-rendu clinique</label>
                <textarea name="compte_rendu" id="f-compterendu" rows="4" class="field-input resize-none"
                          placeholder="Observations, conclusions, recommandations..."></textarea>
            </div>
        </div>

        <!-- ══ STEP 3: Constantes vitales ══════════════════════════ -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-blue-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">3</span>
                Constantes Vitales <span class="text-xs font-normal text-slate-400 ml-1">(facultatif)</span>
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Tension artérielle</label>
                    <input type="text" name="tension_arterielle" id="f-tension"
                           class="field-input" placeholder="Ex : 12/8"
                           data-validate="tension"
                           maxlength="10"/>
                    <span class="field-error">Format attendu : chiffres/chiffres (ex: 12/8).</span>
                </div>
                <div>
                    <label class="field-label">Rythme cardiaque (bpm)</label>
                    <input type="number" name="rythme_cardiaque" id="f-rythme"
                           class="field-input" placeholder="72" min="30" max="300"
                           data-validate="number-range" data-min="30" data-max="300"/>
                    <span class="field-error">Valeur attendue entre 30 et 300.</span>
                </div>
                <div>
                    <label class="field-label">Poids (kg)</label>
                    <input type="number" step="0.1" name="poids" id="f-poids"
                           class="field-input" placeholder="75.5" min="1" max="300"
                           data-validate="number-range" data-min="1" data-max="300"/>
                    <span class="field-error">Valeur attendue entre 1 et 300 kg.</span>
                </div>
                <div>
                    <label class="field-label">Saturation O² (%)</label>
                    <input type="number" name="saturation_o2" id="f-sato2"
                           class="field-input" placeholder="98" min="50" max="100"
                           data-validate="number-range" data-min="50" data-max="100"/>
                    <span class="field-error">Valeur attendue entre 50 et 100 %.</span>
                </div>
            </div>
        </div>

        <!-- ══ STEP 4: Antécédents ══════════════════════════════════ -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">4</span>
                    Antécédents Médicaux <span class="text-xs font-normal text-slate-400 ml-1">(facultatif)</span>
                </h2>
                <button type="button" onclick="addAntRow()"
                        class="text-xs font-bold text-primary border border-primary/30 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="ant-rows" class="space-y-3"></div>
            <p id="ant-empty" class="text-sm text-slate-400">Aucun antécédent saisi.</p>
        </div>

        <!-- ══ STEP 5: Allergies ════════════════════════════════════ -->
        <div class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-error/80 text-white text-xs font-black flex items-center justify-center">5</span>
                    Allergies <span class="text-xs font-normal text-slate-400 ml-1">(facultatif)</span>
                </h2>
                <button type="button" onclick="addAlgRow()"
                        class="text-xs font-bold text-primary border border-primary/30 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ajouter
                </button>
            </div>
            <div id="alg-rows" class="space-y-3"></div>
            <p id="alg-empty" class="text-sm text-slate-400">Aucune allergie saisie.</p>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3 pb-4">
            <a href="index.php?page=patients"
               class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-200 transition-colors text-sm">
                Annuler
            </a>
            <button type="submit" id="submit-btn"
                    class="px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Enregistrer la consultation
            </button>
        </div>
    </form>
</div>
</main>

<script>
// ══════════════════════════════════════════════════════════════════
// REAL-TIME VALIDATORS
// ══════════════════════════════════════════════════════════════════
const VALIDATORS = {
    date_consultation: (val) => {
        if (!val) return {valid: false, msg: 'Date requise'};
        const dt = new Date(val.replace('T', ' '));
        return dt <= new Date() ? {valid: true, msg: '✓'} : {valid: false, msg: 'Pas de date future'};
    },
    type_consultation: (val) => {
        if (!val) return {valid: false, msg: 'Type requis'};
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

function validateField(input) {
    const validator = VALIDATORS[input.name] || VALIDATORS[input.id?.substring(2)]; // f-xxx → xxx
    if (!validator) return;

    const result = validator(input.value);

    input.classList.toggle('is-invalid', !result.valid && input.value.trim() !== '');
    input.classList.toggle('is-valid', result.valid && input.value.trim() !== '');

    let errorEl = input.parentElement.querySelector('.field-error');
    let successEl = input.parentElement.querySelector('.field-success');

    if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'field-error';
        input.parentElement.appendChild(errorEl);
    }
    if (!successEl) {
        successEl = document.createElement('div');
        successEl.className = 'field-success';
        input.parentElement.appendChild(successEl);
    }

    if (!result.valid && input.value.trim() !== '') {
        errorEl.textContent = result.msg;
        errorEl.style.display = 'block';
        successEl.style.display = 'none';
    } else if (result.valid && input.value.trim() !== '') {
        successEl.textContent = result.msg;
        successEl.style.display = 'block';
        errorEl.style.display = 'none';
    } else {
        errorEl.style.display = 'none';
        successEl.style.display = 'none';
    }
}

// ══════════════════════════════════════════════════════════════════
// Patient selection
// ══════════════════════════════════════════════════════════════════
function selectPatient(card) {
    document.querySelectorAll('.patient-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('selected-patient-id').value = card.dataset.id;
    document.getElementById('patient-select-error').classList.add('hidden');
}

// Filter patient cards
document.getElementById('patient-search-filter').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.patient-card').forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
});

// ══════════════════════════════════════════════════════════════════
// Dynamic rows for antécédents & allergies
// ══════════════════════════════════════════════════════════════════
function addAntRow() {
    document.getElementById('ant-empty').style.display = 'none';
    document.getElementById('ant-rows').insertAdjacentHTML('beforeend', `
        <div class="ant-row grid grid-cols-12 gap-2 items-center">
            <div class="col-span-2">
                <input type="number" name="ant_annee[]" placeholder="Année"
                       class="field-input text-xs" min="1900" max="2099" oninput="validateField(this)"/>
                <span class="field-error"></span>
            </div>
            <div class="col-span-4">
                <input type="text" name="ant_titre[]" placeholder="Titre"
                       class="field-input text-xs" oninput="validateField(this)"/>
                <span class="field-error"></span>
            </div>
            <div class="col-span-5">
                <input type="text" name="ant_desc[]" placeholder="Description"
                       class="field-input text-xs"/>
            </div>
            <button type="button" onclick="removeRow(this, 'ant-rows', 'ant-empty')"
                    class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>`);
}

function addAlgRow() {
    document.getElementById('alg-empty').style.display = 'none';
    document.getElementById('alg-rows').insertAdjacentHTML('beforeend', `
        <div class="alg-row grid grid-cols-12 gap-2 items-center">
            <div class="col-span-7">
                <input type="text" name="allergie_nom[]" placeholder="Allergie (ex: Pénicilline)"
                       class="field-input text-xs" oninput="validateField(this)"/>
                <span class="field-error"></span>
            </div>
            <select name="allergie_niveau[]" class="field-input col-span-4 text-xs">
                <option>Faible</option><option selected>Modéré</option><option>Élevé</option>
            </select>
            <button type="button" onclick="removeRow(this, 'alg-rows', 'alg-empty')"
                    class="col-span-1 flex justify-center p-2 text-error hover:bg-red-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">delete</span>
            </button>
        </div>`);
}

function removeRow(btn, containerId, emptyId) {
    btn.closest('div[class*="-row"]').remove();
    const container = document.getElementById(containerId);
    if (!container.querySelector('[class*="-row"]')) {
        document.getElementById(emptyId).style.display = '';
    }
}

// ══════════════════════════════════════════════════════════════════
// Setup validation on page load
// ══════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    // Attach validators to main fields
    ['f-date', 'f-type', 'f-diagnostic', 'f-compterendu', 'f-tension', 'f-rythme', 'f-poids', 'f-sato2'].forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.addEventListener('input', () => validateField(field));
            field.addEventListener('blur', () => validateField(field));
            validateField(field);
        }
    });
});

// ══════════════════════════════════════════════════════════════════
// Form submit — final validation gate
// ══════════════════════════════════════════════════════════════════
document.getElementById('consult-form').addEventListener('submit', function (e) {
    let ok = true;

    // 1. Patient selected?
    if (!document.getElementById('selected-patient-id').value) {
        document.getElementById('patient-select-error').classList.remove('hidden');
        ok = false;
    }

    // 2. Required fields validation
    ['f-date', 'f-type'].forEach(id => {
        const field = document.getElementById(id);
        const result = VALIDATORS[field.name || field.id.substring(2)](field.value);
        if (!result.valid) {
            field.classList.add('is-invalid');
            ok = false;
        }
    });

    if (!ok) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
</body>
</html>
