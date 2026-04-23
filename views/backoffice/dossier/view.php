<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Dossier Médical: <?= htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? '')) ?></title>
    <meta name="description" content="Dossier médical complet du patient — MediFlow"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode:"class",
            theme:{extend:{
                colors:{
                    "surface-container-low":"#f2f4f6","outline":"#727783","surface-tint":"#005db7",
                    "secondary":"#4a5f83","on-background":"#191c1e","surface-container-highest":"#e0e3e5",
                    "tertiary-fixed":"#84f5e8","tertiary-fixed-dim":"#66d9cc","on-secondary-fixed":"#021b3c",
                    "on-primary-fixed":"#001b3d","inverse-on-surface":"#eff1f3","surface-container-lowest":"#ffffff",
                    "on-tertiary-container":"#87f8ea","tertiary-container":"#00736a","on-secondary":"#ffffff",
                    "outline-variant":"#c2c6d4","on-primary":"#ffffff","background":"#f7f9fb","tertiary":"#005851",
                    "primary":"#004d99","secondary-fixed-dim":"#b2c7f1","surface-container-high":"#e6e8ea",
                    "secondary-fixed":"#d6e3ff","primary-fixed-dim":"#a9c7ff","on-secondary-container":"#475c80",
                    "error-container":"#ffdad6","on-tertiary-fixed-variant":"#005049","on-tertiary":"#ffffff",
                    "inverse-primary":"#a9c7ff","primary-fixed":"#d6e3ff","on-surface":"#191c1e","error":"#ba1a1a",
                    "surface-dim":"#d8dadc","surface-container":"#eceef0","surface-bright":"#f7f9fb",
                    "on-error":"#ffffff","on-primary-container":"#dae5ff","surface-variant":"#e0e3e5",
                    "on-primary-fixed-variant":"#00468c","on-tertiary-fixed":"#00201d","inverse-surface":"#2d3133",
                    "secondary-container":"#c0d5ff","surface":"#f7f9fb","primary-container":"#1565c0",
                    "on-secondary-fixed-variant":"#32476a","on-error-container":"#93000a","on-surface-variant":"#424752"
                },
                fontFamily:{headline:["Manrope"],body:["Inter"],label:["Inter"]},
                borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"}
            }}
        }
    </script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;}
        body{font-family:'Inter',sans-serif;background:#f7f9fb;}
        h1,h2,h3,h4{font-family:'Manrope',sans-serif;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .fade-in{animation:fadeIn .35s ease forwards;}
        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;}
        .modal-box{background:#fff;border-radius:1.25rem;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;box-shadow:0 40px 80px rgba(0,0,0,.15);}
        .modal-hidden{display:none!important;}
    </style>
</head>
<body class="bg-surface">

<?php
$fullName   = htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? ''));
$activePage = 'patients';
$breadcrumb = [
    ['label' => 'Mes Patients', 'url' => 'index.php?page=patients'],
    ['label' => 'Dossier Médical — ' . $fullName],
];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 min-h-screen pt-24 pb-16 px-10">

    <!-- Flash Message -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl fade-in
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
        <span class="material-symbols-outlined"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Patient Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10 fade-in">
        <div class="flex items-center gap-6">
            <!-- Avatar -->
            <div class="relative">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white text-3xl font-extrabold shadow-lg">
                    <?= strtoupper(substr($patient['prenom'] ?? 'P', 0, 1) . substr($patient['nom'] ?? 'X', 0, 1)) ?>
                </div>
                <div class="absolute -bottom-2 -right-2 bg-tertiary text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                    Actif
                </div>
            </div>
            <div>
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight"><?= $fullName ?></h1>
                <p class="text-on-surface-variant flex items-center gap-2 mt-1 text-sm">
                    <span class="material-symbols-outlined text-sm">badge</span>
                    ID #<?= $patient['id_PK'] ?> &bull;
                    <span class="material-symbols-outlined text-sm">mail</span>
                    <?= htmlspecialchars($patient['mail'] ?? '') ?>
                    <?php if ($patient['tel']): ?>
                    &bull; <span class="material-symbols-outlined text-sm">call</span>
                    <?= htmlspecialchars($patient['tel']) ?>
                    <?php endif ?>
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                    class="px-5 py-2.5 rounded-lg font-semibold text-primary border border-primary hover:bg-primary-fixed-dim transition-colors flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined">print</span> Imprimer
            </button>
            <button onclick="openConsultModal('add')"
                    class="px-6 py-2.5 rounded-lg font-semibold text-white bg-gradient-to-r from-primary to-primary-container shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined">add</span> Nouvelle Consultation
            </button>
        </div>
    </div>

    <!-- Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

        <!-- ══ LEFT COLUMN (4 cols) ══════════════════════════════ -->
        <div class="md:col-span-4 space-y-6">

            <!-- Personal Info -->
            <section class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] relative overflow-hidden fade-in">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary/30 to-transparent"></div>
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-base font-bold text-on-surface">Informations Personnelles</h3>
                </div>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-[10px] uppercase tracking-wider text-outline font-bold mb-0.5">Email</dt>
                        <dd class="text-on-surface font-medium"><?= htmlspecialchars($patient['mail'] ?? '—') ?></dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-wider text-outline font-bold mb-0.5">Téléphone</dt>
                        <dd class="text-on-surface font-medium"><?= htmlspecialchars($patient['tel'] ?? '—') ?></dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-wider text-outline font-bold mb-0.5">Adresse</dt>
                        <dd class="text-on-surface font-medium"><?= htmlspecialchars($patient['adresse'] ?? '—') ?></dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-wider text-outline font-bold mb-0.5">Nb Consultations</dt>
                        <dd class="text-on-surface font-bold text-primary"><?= count($consultations) ?></dd>
                    </div>
                </dl>
            </section>

            <!-- Dernières Constantes (Vitals) -->
            <section class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] border-t-2 border-tertiary-fixed fade-in">
                <h3 class="text-base font-bold text-on-surface mb-5">Dernières Constantes</h3>
                <?php if ($vitals): ?>
                <div class="grid grid-cols-2 gap-3">
                    <?php
                    $vitalItems = [
                        ['Tension Art.', $vitals['tension_arterielle'] ?? '—', 'mmHg', 'monitor_heart'],
                        ['Rythme Card.', $vitals['rythme_cardiaque'] ?? '—', 'bpm', 'cardiology'],
                        ['Poids', $vitals['poids'] ?? '—', 'kg', 'scale'],
                        ['Saturation O²', $vitals['saturation_o2'] ?? '—', '%', 'air'],
                    ];
                    foreach ($vitalItems as [$label, $val, $unit, $icon]):
                    ?>
                    <div class="bg-surface-container-low p-4 rounded-lg">
                        <p class="text-[10px] font-bold text-tertiary uppercase tracking-tighter mb-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm"><?= $icon ?></span>
                            <?= $label ?>
                        </p>
                        <p class="text-xl font-black text-on-surface">
                            <?= htmlspecialchars((string)$val) ?>
                            <span class="text-xs font-medium text-outline"><?= $unit ?></span>
                        </p>
                    </div>
                    <?php endforeach ?>
                </div>
                <p class="text-[10px] text-slate-400 mt-3">
                    Dernière mesure : <?= date('d/m/Y', strtotime($vitals['date_consultation'])) ?>
                </p>
                <?php else: ?>
                <p class="text-sm text-slate-400">Aucune constante enregistrée.</p>
                <?php endif ?>
            </section>

            <!-- Allergies -->
            <section class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-base font-bold text-on-surface">Allergies</h3>
                    <span class="material-symbols-outlined text-error/60 text-lg">warning</span>
                </div>
                <?php if (!empty($allergies)): ?>
                <div class="space-y-2">
                    <?php foreach ($allergies as $allergie): ?>
                    <div class="flex items-center justify-between p-3
                        <?= ($allergie['niveau'] ?? '') === 'Élevé' ? 'bg-error-container/30' : 'bg-surface-container-low' ?>
                        rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full <?= ($allergie['niveau'] ?? '') === 'Élevé' ? 'bg-error' : 'bg-amber-400' ?>"></div>
                            <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($allergie['nom'] ?? '') ?></span>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                            <?= ($allergie['niveau'] ?? '') === 'Élevé' ? 'bg-error/10 text-error' : 'bg-amber-50 text-amber-600' ?>">
                            <?= htmlspecialchars($allergie['niveau'] ?? '') ?>
                        </span>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php else: ?>
                <p class="text-sm text-slate-400">Aucune allergie connue.</p>
                <?php endif ?>
            </section>
        </div>

        <!-- ══ RIGHT COLUMN (8 cols) ═════════════════════════════ -->
        <div class="md:col-span-8 space-y-6">

            <!-- Antécédents Médicaux -->
            <section class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">history</span>
                        <h3 class="text-xl font-bold text-on-surface">Antécédents Médicaux</h3>
                    </div>
                    <button onclick="openConsultModal('add')"
                            class="text-sm font-bold text-primary hover:underline transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Ajouter via consultation
                    </button>
                </div>

                <?php if (!empty($antecedents)): ?>
                <div class="space-y-6">
                    <?php foreach ($antecedents as $idx => $ant): ?>
                    <div class="flex gap-6 group">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                <?= htmlspecialchars($ant['annee'] ?? '?') ?>
                            </div>
                            <?php if ($idx < count($antecedents) - 1): ?>
                            <div class="flex-1 w-0.5 bg-surface-container-high my-2"></div>
                            <?php endif ?>
                        </div>
                        <div class="flex-1 pb-6">
                            <h4 class="font-bold text-on-surface"><?= htmlspecialchars($ant['titre'] ?? '') ?></h4>
                            <p class="text-sm text-on-surface-variant mt-1 leading-relaxed">
                                <?= htmlspecialchars($ant['description'] ?? '') ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php else: ?>
                <p class="text-sm text-slate-400 py-4">Aucun antécédent médical enregistré.</p>
                <?php endif ?>
            </section>

            <!-- Visites Récentes -->
            <section class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">event_note</span>
                        <h3 class="text-xl font-bold text-on-surface">Visites Récentes</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        <?= count($consultations) ?> consultation(s)
                    </span>
                </div>

                <?php if (!empty($consultations)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-surface-container">
                                <th class="py-4 px-2 text-[10px] font-bold text-outline uppercase tracking-widest">Date</th>
                                <th class="py-4 px-2 text-[10px] font-bold text-outline uppercase tracking-widest">Type</th>
                                <th class="py-4 px-2 text-[10px] font-bold text-outline uppercase tracking-widest">Diagnostic</th>
                                <th class="py-4 px-2 text-[10px] font-bold text-outline uppercase tracking-widest">Compte-rendu</th>
                                <th class="py-4 px-2 text-right text-[10px] font-bold text-outline uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $typeColors = [
                                'Contrôle annuel'    => 'bg-blue-50 text-blue-700',
                                'Suivi Spécialisé'   => 'bg-teal-50 text-tertiary',
                                'Téléconsultation'   => 'bg-slate-100 text-slate-600',
                                'Bilan Annuel'       => 'bg-violet-50 text-violet-700',
                                'Consultation urgente' => 'bg-rose-50 text-rose-700',
                            ];
                            foreach ($consultations as $c):
                                $typColor = $typeColors[$c['type_consultation'] ?? ''] ?? 'bg-slate-100 text-slate-600';
                            ?>
                            <tr class="group hover:bg-surface-container-low transition-colors">
                                <td class="py-5 px-2">
                                    <span class="font-bold text-on-surface text-sm">
                                        <?= date('d M Y', strtotime($c['date_consultation'])) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        <?= date('H:i', strtotime($c['date_consultation'])) ?>
                                    </span>
                                </td>
                                <td class="py-5 px-2">
                                    <span class="text-xs px-2 py-1 <?= $typColor ?> rounded-md font-medium">
                                        <?= htmlspecialchars($c['type_consultation'] ?? '—') ?>
                                    </span>
                                </td>
                                <td class="py-5 px-2">
                                    <span class="text-sm font-semibold text-on-surface">
                                        <?= htmlspecialchars($c['diagnostic'] ?? '—') ?>
                                    </span>
                                </td>
                                <td class="py-5 px-2">
                                    <p class="text-sm text-on-surface-variant truncate max-w-xs">
                                        <?= htmlspecialchars($c['compte_rendu'] ?? '—') ?>
                                    </p>
                                </td>
                                <td class="py-5 px-2 text-right">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <!-- Ordonnance -->
                                        <a href="index.php?page=ordonnance&consult_id=<?= $c['id_consultation'] ?>"
                                           title="Voir ordonnance"
                                           class="p-1.5 rounded-lg hover:bg-blue-50 text-primary transition-colors">
                                            <span class="material-symbols-outlined text-lg">prescriptions</span>
                                        </a>
                                        <!-- Edit -->
                                        <a href="index.php?page=dossier&action=edit&id=<?= $c['id_consultation'] ?>"
                                           title="Modifier"
                                           class="p-1.5 rounded-lg hover:bg-slate-100 text-outline transition-colors">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <!-- Delete -->
                                        <button onclick="confirmDelete(<?= $c['id_consultation'] ?>, <?= $patient['id_PK'] ?>)"
                                                title="Supprimer"
                                                class="p-1.5 rounded-lg hover:bg-error-container/30 text-error/60 hover:text-error transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-sm text-slate-400 py-6 text-center">
                    Aucune consultation enregistrée.
                    <button onclick="openConsultModal('add')" class="text-primary font-semibold hover:underline ml-1">
                        Ajouter la première consultation
                    </button>
                </p>
                <?php endif ?>
            </section>
        </div>
    </div>

    <footer class="mt-10 text-center">
        <p class="text-xs text-outline font-medium">
            Dossier mis à jour le <?= date('d/m/Y \à H:i') ?> par Dr. <?= htmlspecialchars($medecin['nom'] ?? '') ?>
        </p>
        <p class="text-[10px] text-outline/60 mt-1 uppercase tracking-widest">
            MediFlow Clinical • Protégé par chiffrement AES-256
        </p>
    </footer>
</main>

<!-- ═══════════════════════════════════════════════════ -->
<!-- Add Consultation Modal                              -->
<!-- ═══════════════════════════════════════════════════ -->
<div id="consult-modal" class="modal-overlay modal-hidden" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <div class="flex justify-between items-center px-8 py-6 border-b border-slate-100">
            <h2 class="text-xl font-bold text-blue-900">Nouvelle Consultation</h2>
            <button onclick="closeModal()" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-500">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="modal-consult-form" method="POST" action="index.php?page=dossier&action=add" class="px-8 py-6 space-y-5" onsubmit="return validateConsultationForm(event)">
            <input type="hidden" name="id_patient" value="<?= $patient['id_PK'] ?>"/>

            <!-- Error Messages -->
            <div id="modal-form-errors" class="hidden p-4 rounded-lg bg-error-container/20 border border-error/30">
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-error">error</span>
                    <ul id="errors-list" class="text-sm text-error space-y-1"></ul>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Date de consultation *</label>
                    <input type="datetime-local" name="date_consultation" id="modal-date-consult"
                           value="<?= date('Y-m-d\TH:i') ?>"
                           class="field-input" required/>
                    <div id="error-date-consult" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span id="error-date-text"></span>
                    </div>
                    <div id="success-date-consult" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Date valide</span>
                    </div>
                </div>
                <div>
                    <label class="field-label">Type de consultation *</label>
                    <select name="type_consultation" id="modal-type-consult" class="field-input" required>
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
                    <div id="error-type-consult" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span>Type de consultation requis</span>
                    </div>
                    <div id="success-type-consult" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Type sélectionné</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="field-label">Diagnostic</label>
                <input type="text" name="diagnostic" id="modal-diagnostic" class="field-input" 
                       placeholder="Ex: Hypertension Artérielle" maxlength="150"/>
                <div id="error-diagnostic" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <span id="error-diagnostic-text"></span>
                </div>
                <div id="info-diagnostic" class="text-xs text-slate-500 mt-1">
                    <span id="count-diagnostic">0</span>/150 caractères
                </div>
            </div>

            <div>
                <label class="field-label">Compte-rendu</label>
                <textarea name="compte_rendu" id="modal-compte-rendu" rows="3" class="field-input resize-none"
                          placeholder="Observations cliniques..." maxlength="5000"></textarea>
                <div id="error-compte-rendu" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <span id="error-compte-text"></span>
                </div>
                <div id="info-compte-rendu" class="text-xs text-slate-500 mt-1">
                    <span id="count-compte-rendu">0</span>/5000 caractères
                </div>
            </div>

            <!-- Constantes vitales -->
            <div class="bg-surface-container-low rounded-xl p-4">
                <h4 class="text-sm font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-primary">monitor_heart</span>
                    Constantes Vitales
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="field-label">Tension artérielle</label>
                        <input type="text" name="tension_arterielle" id="modal-tension" class="field-input" 
                               placeholder="120/80" maxlength="10"/>
                        <div id="error-tension" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span id="error-tension-text"></span>
                        </div>
                        <div id="success-tension" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Format valide</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Rythme cardiaque (bpm)</label>
                        <input type="number" name="rythme_cardiaque" id="modal-rythme" class="field-input" 
                               placeholder="72" min="30" max="300"/>
                        <div id="error-rythme" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span id="error-rythme-text"></span>
                        </div>
                        <div id="success-rythme" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Valide (30-300 BPM)</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Poids (kg)</label>
                        <input type="number" step="0.1" name="poids" id="modal-poids" class="field-input" 
                               placeholder="75.5"/>
                        <div id="error-poids" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span id="error-poids-text"></span>
                        </div>
                        <div id="success-poids" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Valide (2-500 kg)</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Saturation O² (%)</label>
                        <input type="number" name="saturation_o2" id="modal-saturation" class="field-input" 
                               placeholder="98" min="0" max="100"/>
                        <div id="error-saturation" class="hidden text-xs text-error font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span id="error-saturation-text"></span>
                        </div>
                        <div id="success-saturation" class="hidden text-xs text-green-600 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            <span>Valide (0-100%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Antécédents dynamiques -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <label class="field-label mb-0">Antécédents médicaux</label>
                    <button type="button" onclick="addAntRow()"
                            class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add</span> Ajouter
                    </button>
                </div>
                <div id="ant-rows" class="space-y-2">
                    <?php foreach ($antecedents as $ant): ?>
                    <div class="ant-row grid grid-cols-12 gap-2 items-start">
                        <input type="number" name="ant_annee[]" value="<?= htmlspecialchars($ant['annee'] ?? '') ?>"
                               placeholder="Année" class="field-input col-span-2 text-xs"/>
                        <input type="text" name="ant_titre[]" value="<?= htmlspecialchars($ant['titre'] ?? '') ?>"
                               placeholder="Titre" class="field-input col-span-4 text-xs"/>
                        <input type="text" name="ant_desc[]" value="<?= htmlspecialchars($ant['description'] ?? '') ?>"
                               placeholder="Description" class="field-input col-span-5 text-xs"/>
                        <button type="button" onclick="this.closest('.ant-row').remove()"
                                class="col-span-1 p-1 text-error hover:bg-red-50 rounded transition-colors text-center">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- Allergies dynamiques -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <label class="field-label mb-0">Allergies</label>
                    <button type="button" onclick="addAlgRow()"
                            class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">add</span> Ajouter
                    </button>
                </div>
                <div id="alg-rows" class="space-y-2">
                    <?php foreach ($allergies as $alg): ?>
                    <div class="alg-row grid grid-cols-12 gap-2 items-center">
                        <input type="text" name="allergie_nom[]" value="<?= htmlspecialchars($alg['nom'] ?? '') ?>"
                               placeholder="Allergie" class="field-input col-span-7 text-xs"/>
                        <select name="allergie_niveau[]" class="field-input col-span-4 text-xs">
                            <?php foreach (['Faible','Modéré','Élevé'] as $niv): ?>
                            <option <?= ($alg['niveau'] ?? '') === $niv ? 'selected' : '' ?>><?= $niv ?></option>
                            <?php endforeach ?>
                        </select>
                        <button type="button" onclick="this.closest('.alg-row').remove()"
                                class="col-span-1 p-1 text-error hover:bg-red-50 rounded transition-colors text-center">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal()"
                        class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-100 transition-colors text-sm">
                    Annuler
                </button>
                <button type="submit"
                        class="px-8 py-2.5 rounded-lg font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all text-sm">
                    Enregistrer la consultation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirm Form (hidden) -->
<form id="delete-form" method="POST" action="index.php?page=dossier&action=delete" style="display:none">
    <input type="hidden" name="id" id="delete-id"/>
    <input type="hidden" name="id_patient" id="delete-patient-id"/>
</form>

<style>
.field-label{display:block;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#727783;margin-bottom:.3rem;}
.field-input{display:block;width:100%;background:#f2f4f6;border:1px solid #e0e3e5;border-radius:.5rem;padding:.55rem .85rem;font-size:.875rem;color:#191c1e;outline:none;transition:border-color .15s,box-shadow .15s;}
.field-input:focus{border-color:#004d99;box-shadow:0 0 0 3px rgba(0,77,153,.12);}
.field-input:invalid{border-color:#ba1a1a;box-shadow:0 0 0 3px rgba(186,26,26,.1);}
.field-input.border-error{border-color:#ba1a1a !important;box-shadow:0 0 0 3px rgba(186,26,26,.1);}
</style>

<script>
function openConsultModal(mode) {
    document.getElementById('consult-modal').classList.remove('modal-hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('consult-modal').classList.add('modal-hidden');
    document.body.style.overflow = '';
    // Reset error messages
    document.getElementById('modal-form-errors').classList.add('hidden');
    document.getElementById('errors-list').innerHTML = '';
}
function closeModalOutside(e) {
    if (e.target === document.getElementById('consult-modal')) closeModal();
}

// Validation function for consultation form
function validateConsultationForm(e) {
    const errors = [];

    // 1. Validate Date
    const dateInput = document.getElementById('modal-date-consult');
    const dateValue = dateInput.value.trim();
    if (!dateValue) {
        errors.push('La date de consultation est requise');
    } else {
        const dt = new Date(dateValue.replace('T', ' '));
        if (isNaN(dt.getTime())) {
            errors.push('La date est invalide');
        } else if (dt > new Date()) {
            errors.push('La date ne peut pas être dans le futur');
        }
    }

    // 2. Validate Type
    const typeInput = document.getElementById('modal-type-consult');
    const typeValue = typeInput.value.trim();
    if (!typeValue) {
        errors.push('Veuillez sélectionner un type de consultation');
    }

    // 3. Validate Diagnostic (optional but max 150 chars)
    const diagnosticInput = document.getElementById('modal-diagnostic');
    const diagnosticValue = diagnosticInput.value.trim();
    if (diagnosticValue && diagnosticValue.length > 150) {
        errors.push('Le diagnostic ne peut pas dépasser 150 caractères');
    }

    // 4. Validate Compte-rendu (optional but max 5000 chars)
    const compteRenduInput = document.getElementById('modal-compte-rendu');
    const compteRenduValue = compteRenduInput.value.trim();
    if (compteRenduValue && compteRenduValue.length > 5000) {
        errors.push('Le compte-rendu ne peut pas dépasser 5000 caractères');
    }

    // 5. Validate Tension Artérielle (optional but format validation)
    const tensionInput = document.getElementById('modal-tension');
    const tensionValue = tensionInput.value.trim();
    if (tensionValue) {
        if (!/^\d{1,3}\/\d{1,3}$/.test(tensionValue)) {
            errors.push('Tension artérielle: format attendu XX/YY (ex: 120/80)');
        } else {
            const [sys, dia] = tensionValue.split('/').map(Number);
            if (sys < 60 || sys > 250 || dia < 30 || dia > 150 || sys <= dia) {
                errors.push('Tension artérielle: valeurs invalides (ex: 120/80)');
            }
        }
    }

    // 6. Validate Rythme Cardiaque (optional but 30-300)
    const rythmeInput = document.getElementById('modal-rythme');
    const rythmeValue = rythmeInput.value.trim();
    if (rythmeValue) {
        const rate = parseInt(rythmeValue);
        if (isNaN(rate) || rate < 30 || rate > 300) {
            errors.push('Rythme cardiaque: doit être entre 30 et 300 BPM');
        }
    }

    // 7. Validate Poids (optional but 2-500)
    const poidsInput = document.getElementById('modal-poids');
    const poidsValue = poidsInput.value.trim();
    if (poidsValue) {
        const weight = parseFloat(poidsValue);
        if (isNaN(weight) || weight <= 2 || weight >= 500) {
            errors.push('Poids: doit être entre 2 et 500 kg');
        }
    }

    // 8. Validate Saturation O2 (optional but 0-100)
    const saturationInput = document.getElementById('modal-saturation');
    const saturationValue = saturationInput.value.trim();
    if (saturationValue) {
        const sat = parseInt(saturationValue);
        if (isNaN(sat) || sat < 0 || sat > 100) {
            errors.push('Saturation O²: doit être entre 0 et 100%');
        }
    }

    // Show errors if any
    if (errors.length > 0) {
        e.preventDefault();
        const errorsList = document.getElementById('errors-list');
        const errorsDiv = document.getElementById('modal-form-errors');
        errorsList.innerHTML = errors.map(err => `<li>• ${err}</li>`).join('');
        errorsDiv.classList.remove('hidden');
        // Scroll to top of modal to show errors
        document.querySelector('.modal-box').scrollTop = 0;
        return false;
    }

    return true;
}

// Real-time validation functions
function validateDateRealtime(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('error-date-consult');
    const successDiv = document.getElementById('success-date-consult');
    const errorText = document.getElementById('error-date-text');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (!value) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'La date est requise';
        input.classList.add('border-error');
        return false;
    }
    
    const dt = new Date(value.replace('T', ' '));
    if (isNaN(dt.getTime())) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Date invalide';
        input.classList.add('border-error');
        return false;
    }
    
    if (dt > new Date()) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'La date ne peut pas être dans le futur';
        input.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

function validateTypeRealtime(select) {
    const value = select.value.trim();
    const errorDiv = document.getElementById('error-type-consult');
    const successDiv = document.getElementById('success-type-consult');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    select.classList.remove('border-error');
    
    if (!value) {
        errorDiv.classList.remove('hidden');
        select.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

function validateDiagnosticRealtime(input) {
    const value = input.value;
    const errorDiv = document.getElementById('error-diagnostic');
    const errorText = document.getElementById('error-diagnostic-text');
    const countSpan = document.getElementById('count-diagnostic');
    
    countSpan.textContent = value.length;
    errorDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (value.length > 150) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Dépassement de 150 caractères';
        input.classList.add('border-error');
        return false;
    }
    
    return true;
}

function validateCompteRenduRealtime(textarea) {
    const value = textarea.value;
    const errorDiv = document.getElementById('error-compte-rendu');
    const errorText = document.getElementById('error-compte-text');
    const countSpan = document.getElementById('count-compte-rendu');
    
    countSpan.textContent = value.length;
    errorDiv.classList.add('hidden');
    textarea.classList.remove('border-error');
    
    if (value.length > 5000) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Dépassement de 5000 caractères';
        textarea.classList.add('border-error');
        return false;
    }
    
    return true;
}

function validateTensionRealtime(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('error-tension');
    const successDiv = document.getElementById('success-tension');
    const errorText = document.getElementById('error-tension-text');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (!value) return true;
    
    if (!/^\d{1,3}\/\d{1,3}$/.test(value)) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Format: XX/YY (ex: 120/80)';
        input.classList.add('border-error');
        return false;
    }
    
    const [sys, dia] = value.split('/').map(Number);
    if (sys < 60 || sys > 250 || dia < 30 || dia > 150 || sys <= dia) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Valeurs invalides';
        input.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

function validateRythmeRealtime(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('error-rythme');
    const successDiv = document.getElementById('success-rythme');
    const errorText = document.getElementById('error-rythme-text');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (!value) return true;
    
    const rate = parseInt(value);
    if (isNaN(rate) || rate < 30 || rate > 300) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Doit être entre 30 et 300 BPM';
        input.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

function validatePoidsRealtime(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('error-poids');
    const successDiv = document.getElementById('success-poids');
    const errorText = document.getElementById('error-poids-text');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (!value) return true;
    
    const weight = parseFloat(value);
    if (isNaN(weight) || weight <= 2 || weight >= 500) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Doit être entre 2 et 500 kg';
        input.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

function validateSaturationRealtime(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('error-saturation');
    const successDiv = document.getElementById('success-saturation');
    const errorText = document.getElementById('error-saturation-text');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    input.classList.remove('border-error');
    
    if (!value) return true;
    
    const sat = parseInt(value);
    if (isNaN(sat) || sat < 0 || sat > 100) {
        errorDiv.classList.remove('hidden');
        errorText.textContent = 'Doit être entre 0 et 100%';
        input.classList.add('border-error');
        return false;
    }
    
    successDiv.classList.remove('hidden');
    return true;
}

// Validation function for consultation form
function validateConsultationForm(e) {
    const errors = [];

    // Validate all fields
    if (!validateDateRealtime(document.getElementById('modal-date-consult'))) {
        errors.push('La date de consultation est invalide');
    }
    if (!validateTypeRealtime(document.getElementById('modal-type-consult'))) {
        errors.push('Veuillez sélectionner un type de consultation');
    }
    validateDiagnosticRealtime(document.getElementById('modal-diagnostic'));
    validateCompteRenduRealtime(document.getElementById('modal-compte-rendu'));
    validateTensionRealtime(document.getElementById('modal-tension'));
    validateRythmeRealtime(document.getElementById('modal-rythme'));
    validatePoidsRealtime(document.getElementById('modal-poids'));
    validateSaturationRealtime(document.getElementById('modal-saturation'));

    // Show errors if any critical fields failed
    if (errors.length > 0) {
        e.preventDefault();
        const errorsList = document.getElementById('errors-list');
        const errorsDiv = document.getElementById('modal-form-errors');
        errorsList.innerHTML = errors.map(err => `<li>• ${err}</li>`).join('');
        errorsDiv.classList.remove('hidden');
        document.querySelector('.modal-box').scrollTop = 0;
        return false;
    }

    return true;
}

// Setup real-time validation on page load
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('modal-date-consult');
    if (dateInput) {
        dateInput.addEventListener('change', function() { validateDateRealtime(this); });
        dateInput.addEventListener('blur', function() { validateDateRealtime(this); });
    }

    const typeSelect = document.getElementById('modal-type-consult');
    if (typeSelect) {
        typeSelect.addEventListener('change', function() { validateTypeRealtime(this); });
    }

    const diagnosticInput = document.getElementById('modal-diagnostic');
    if (diagnosticInput) {
        diagnosticInput.addEventListener('input', function() { validateDiagnosticRealtime(this); });
    }

    const compteRenduInput = document.getElementById('modal-compte-rendu');
    if (compteRenduInput) {
        compteRenduInput.addEventListener('input', function() { validateCompteRenduRealtime(this); });
    }

    const tensionInput = document.getElementById('modal-tension');
    if (tensionInput) {
        tensionInput.addEventListener('input', function() { validateTensionRealtime(this); });
        tensionInput.addEventListener('blur', function() { validateTensionRealtime(this); });
    }

    const rythmeInput = document.getElementById('modal-rythme');
    if (rythmeInput) {
        rythmeInput.addEventListener('input', function() { validateRythmeRealtime(this); });
        rythmeInput.addEventListener('change', function() { validateRythmeRealtime(this); });
    }

    const poidsInput = document.getElementById('modal-poids');
    if (poidsInput) {
        poidsInput.addEventListener('input', function() { validatePoidsRealtime(this); });
        poidsInput.addEventListener('change', function() { validatePoidsRealtime(this); });
    }

    const saturationInput = document.getElementById('modal-saturation');
    if (saturationInput) {
        saturationInput.addEventListener('input', function() { validateSaturationRealtime(this); });
        saturationInput.addEventListener('change', function() { validateSaturationRealtime(this); });
    }
});

function addAntRow() {
    const rows = document.getElementById('ant-rows');
    rows.insertAdjacentHTML('beforeend', `
        <div class="ant-row grid grid-cols-12 gap-2 items-start">
            <input type="number" name="ant_annee[]" placeholder="Année" class="field-input col-span-2 text-xs"/>
            <input type="text"   name="ant_titre[]" placeholder="Titre" class="field-input col-span-4 text-xs"/>
            <input type="text"   name="ant_desc[]"  placeholder="Description" class="field-input col-span-5 text-xs"/>
            <button type="button" onclick="this.closest('.ant-row').remove()"
                    class="col-span-1 p-1 text-error hover:bg-red-50 rounded transition-colors text-center">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>`);
}

function addAlgRow() {
    const rows = document.getElementById('alg-rows');
    rows.insertAdjacentHTML('beforeend', `
        <div class="alg-row grid grid-cols-12 gap-2 items-center">
            <input type="text"   name="allergie_nom[]"   placeholder="Allergie" class="field-input col-span-7 text-xs"/>
            <select name="allergie_niveau[]" class="field-input col-span-4 text-xs">
                <option>Faible</option><option selected>Modéré</option><option>Élevé</option>
            </select>
            <button type="button" onclick="this.closest('.alg-row').remove()"
                    class="col-span-1 p-1 text-error hover:bg-red-50 rounded transition-colors text-center">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>`);
}

function confirmDelete(consultId, patientId) {
    if (!confirm('Supprimer cette consultation ? Cette action est irréversible et supprimera aussi l\'ordonnance liée.')) return;
    document.getElementById('delete-id').value = consultId;
    document.getElementById('delete-patient-id').value = patientId;
    document.getElementById('delete-form').submit();
}

setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
</body>
</html>
