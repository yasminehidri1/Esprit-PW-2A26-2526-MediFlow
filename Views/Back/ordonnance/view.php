<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Ordonnance <?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?></title>
    <meta name="description" content="Gestion d'ordonnance médicale — MediFlow"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config={darkMode:"class",theme:{extend:{colors:{"surface-container-low":"#f2f4f6","outline":"#727783","surface-tint":"#005db7","secondary":"#4a5f83","on-background":"#191c1e","surface-container-highest":"#e0e3e5","tertiary-fixed":"#84f5e8","tertiary-fixed-dim":"#66d9cc","on-secondary-fixed":"#021b3c","on-primary-fixed":"#001b3d","inverse-on-surface":"#eff1f3","surface-container-lowest":"#ffffff","on-tertiary-container":"#87f8ea","tertiary-container":"#00736a","on-secondary":"#ffffff","outline-variant":"#c2c6d4","on-primary":"#ffffff","background":"#f7f9fb","tertiary":"#005851","primary":"#004d99","secondary-fixed-dim":"#b2c7f1","surface-container-high":"#e6e8ea","secondary-fixed":"#d6e3ff","primary-fixed-dim":"#a9c7ff","on-secondary-container":"#475c80","error-container":"#ffdad6","on-tertiary-fixed-variant":"#005049","on-tertiary":"#ffffff","inverse-primary":"#a9c7ff","primary-fixed":"#d6e3ff","on-surface":"#191c1e","error":"#ba1a1a","surface-dim":"#d8dadc","surface-container":"#eceef0","surface-bright":"#f7f9fb","on-error":"#ffffff","on-primary-container":"#dae5ff","surface-variant":"#e0e3e5","on-primary-fixed-variant":"#00468c","on-tertiary-fixed":"#00201d","inverse-surface":"#2d3133","secondary-container":"#c0d5ff","surface":"#f7f9fb","primary-container":"#1565c0","on-secondary-fixed-variant":"#32476a","on-error-container":"#93000a","on-surface-variant":"#424752"},fontFamily:{headline:["Manrope"],body:["Inter"],label:["Inter"]},borderRadius:{DEFAULT:"0.25rem",lg:"0.5rem",xl:"0.75rem",full:"9999px"}}}}
    </script>
    <style>
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;vertical-align:middle;}
        body{font-family:'Inter',sans-serif;background:#f7f9fb;}
        h1,h2,h3,h4{font-family:'Manrope',sans-serif;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
        .fade-in{animation:fadeIn .35s ease forwards;}
        @media print {
            aside, header, .no-print { display: none !important; }
            main { margin-left: 0 !important; padding-top: 0 !important; }
            .print-shadow { box-shadow: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface">

<?php
$patientName = htmlspecialchars($ordonnance['nom_patient'] ?? '');
$activePage  = 'ordonnance';
$breadcrumb  = [
    ['label' => 'Mes Patients',  'url' => 'index.php?page=patients'],
    ['label' => $patientName,    'url' => 'index.php?page=dossier&patient_id=' . ($ordonnance['id_patient'] ?? 0)],
    ['label' => 'Ordonnance ' . htmlspecialchars($ordonnance['numero_ordonnance'] ?? '')],
];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 pt-24 px-8 pb-12 min-h-screen bg-surface">

    <!-- Flash -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl max-w-6xl mx-auto fade-in
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
        <span class="material-symbols-outlined"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Header -->
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4 fade-in no-print">
            <div>
                <nav class="flex items-center gap-2 text-sm font-medium text-on-surface-variant mb-2">
                    <a href="index.php?page=dossier&patient_id=<?= $ordonnance['id_patient'] ?>"
                       class="hover:text-primary transition-colors"><?= $patientName ?></a>
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span class="text-primary font-semibold"><?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?></span>
                </nav>
                <h1 class="text-4xl font-extrabold tracking-tight text-on-surface">Prescription Management</h1>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined">print</span> Imprimer
                </button>

                <!-- Statut Toggle -->
                <?php if (($ordonnance['statut'] ?? '') === 'active'): ?>
                <form method="POST" action="index.php?page=ordonnance&action=edit&id=<?= $ordonnance['id_ordonnance'] ?>">
                    <input type="hidden" name="id" value="<?= $ordonnance['id_ordonnance'] ?>"/>
                    <input type="hidden" name="date_emission" value="<?= $ordonnance['date_emission'] ?>"/>
                    <input type="hidden" name="medicaments" value="<?= htmlspecialchars($ordonnance['medicaments']) ?>"/>
                    <input type="hidden" name="note_pharmacien" value="<?= htmlspecialchars($ordonnance['note_pharmacien'] ?? '') ?>"/>
                    <input type="hidden" name="statut" value="archivee"/>
                    <button type="submit"
                            class="flex items-center gap-2 px-5 py-3 rounded-xl font-semibold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 active:scale-95 transition-all text-sm">
                        <span class="material-symbols-outlined">archive</span> Archiver
                    </button>
                </form>
                <?php endif ?>

                <!-- Delete -->
                <button onclick="confirmDeleteOrd(<?= $ordonnance['id_ordonnance'] ?>, <?= $ordonnance['id_patient'] ?>)"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold bg-error text-white shadow-lg hover:opacity-90 transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined">delete</span> Supprimer
                </button>

                <!-- Edit -->
                <a href="index.php?page=ordonnance&action=edit&id=<?= $ordonnance['id_ordonnance'] ?>"
                   class="flex items-center gap-2 px-8 py-3 rounded-xl font-bold bg-gradient-to-br from-primary to-primary-container text-white shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined">edit</span> Modifier
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ══ LEFT: Prescription Paper ══════════════════════════ -->
        <div class="lg:col-span-8 fade-in">
            <div class="bg-surface-container-lowest rounded-2xl shadow-[0_40px_80px_rgba(0,0,0,0.04)] overflow-hidden relative border-t-4 border-tertiary print-shadow">

                <!-- status badge -->
                <?php if (($ordonnance['statut'] ?? 'active') !== 'active'): ?>
                <div class="absolute top-4 right-4 px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full
                    <?= $ordonnance['statut'] === 'archivee' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600' ?>">
                    <?= ucfirst($ordonnance['statut']) ?>
                </div>
                <?php endif ?>

                <!-- Paper Header -->
                <div class="p-10 border-b border-surface-container flex justify-between items-start">
                    <div>
                        <div class="text-xl font-extrabold text-primary font-headline mb-1 uppercase tracking-widest">
                            MediFlow Clinical
                        </div>
                        <div class="text-on-surface-variant font-medium text-sm">
                            Cabinet Médical — Espace Praticien
                        </div>
                        <div class="text-on-surface-variant text-sm">
                            Dr. <?= htmlspecialchars(($ordonnance['prenom_medecin'] ?? '') . ' ' . ($ordonnance['nom_medecin_nom'] ?? '')) ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-outline uppercase tracking-wider mb-1">Date d'émission</div>
                        <div class="text-lg font-headline font-bold">
                            <?= date('d F Y', strtotime($ordonnance['date_emission'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Patient Info Bar -->
                <div class="bg-surface-container-low px-10 py-5 flex flex-wrap gap-10">
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-1">Patient</span>
                        <span class="text-lg font-bold font-headline"><?= $patientName ?></span>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-1">Email</span>
                        <span class="font-medium text-sm"><?= htmlspecialchars($ordonnance['mail_patient'] ?? '') ?></span>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-1">Diagnostic</span>
                        <span class="font-medium text-sm"><?= htmlspecialchars($ordonnance['diagnostic'] ?? '—') ?></span>
                    </div>
                    <div class="ml-auto text-right">
                        <span class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-1">N° Ordonnance</span>
                        <span class="text-lg font-bold font-headline text-primary">
                            <?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?>
                        </span>
                    </div>
                </div>

                <!-- Medication List -->
                <div class="p-10 space-y-10">
                    <?php if (empty($medicaments)): ?>
                    <p class="text-slate-400 text-sm">Aucun médicament prescrit.</p>
                    <?php else: ?>
                    <?php foreach ($medicaments as $idx => $med): ?>
                    <div class="relative pl-6 fade-in" style="animation-delay: <?= $idx * .08 ?>s">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-tertiary-fixed rounded-full"></div>
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-2xl font-bold font-headline text-on-surface">
                                <?= htmlspecialchars($med['nom'] ?? '') ?>
                                <?php if (!empty($med['dosage'])): ?>
                                <span class="text-base font-medium text-on-surface-variant"><?= htmlspecialchars($med['dosage']) ?></span>
                                <?php endif ?>
                            </h3>
                            <?php if (!empty($med['categorie'])): ?>
                            <span class="px-3 py-1 bg-tertiary-fixed/30 text-tertiary font-bold text-xs rounded-full uppercase">
                                <?= htmlspecialchars($med['categorie']) ?>
                            </span>
                            <?php endif ?>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <span class="block text-xs font-bold text-outline uppercase mb-1">Dosage</span>
                                <p class="font-medium text-sm"><?= htmlspecialchars($med['dosage'] ?? '—') ?></p>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-outline uppercase mb-1">Fréquence</span>
                                <p class="font-medium text-sm"><?= htmlspecialchars($med['frequence'] ?? '—') ?></p>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-outline uppercase mb-1">Durée</span>
                                <p class="font-medium text-sm text-primary font-bold"><?= htmlspecialchars($med['duree'] ?? '—') ?></p>
                            </div>
                        </div>
                        <?php if (!empty($med['instructions'])): ?>
                        <div class="mt-4 p-4 bg-surface-container-low rounded-lg italic text-on-surface-variant text-sm">
                            "<?= htmlspecialchars($med['instructions']) ?>"
                        </div>
                        <?php endif ?>
                    </div>
                    <?php endforeach ?>
                    <?php endif ?>
                </div>

                <!-- Signature Area -->
                <div class="mt-4 p-10 border-t border-surface-container bg-surface-container-lowest flex justify-end">
                    <div class="text-center w-64">
                        <div class="text-xs font-bold text-outline uppercase mb-4 tracking-widest">Cachet & Signature</div>
                        <div class="h-16 flex items-center justify-center mb-3">
                            <!-- Stylized signature placeholder -->
                            <svg viewBox="0 0 180 50" class="w-40 opacity-50 text-primary" fill="none">
                                <path d="M10 35 C30 10, 60 40, 80 20 C100 2, 120 38, 150 25 C160 20, 165 30, 170 28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M5 40 C20 38 170 40 175 42" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold font-headline text-on-surface">
                            Dr. <?= htmlspecialchars(($ordonnance['prenom_medecin'] ?? '') . ' ' . ($ordonnance['nom_medecin_nom'] ?? '')) ?>
                        </div>
                        <div class="text-[10px] text-outline font-medium mt-0.5">Médecin Praticien — MediFlow</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ RIGHT: Metadata ════════════════════════════════════ -->
        <div class="lg:col-span-4 space-y-6 no-print">

            <!-- Clinical Summary -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border-t-2 border-tertiary-fixed fade-in">
                <h4 class="text-lg font-bold font-headline mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary">analytics</span>
                    Résumé Clinique
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-3 border-b border-surface-container-low">
                        <span class="text-on-surface-variant text-sm font-medium">Diagnostic</span>
                        <span class="font-bold text-sm"><?= htmlspecialchars($ordonnance['diagnostic'] ?? '—') ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-surface-container-low">
                        <span class="text-on-surface-variant text-sm font-medium">Consultation</span>
                        <span class="font-bold text-sm"><?= date('d/m/Y', strtotime($ordonnance['date_consultation'])) ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-surface-container-low">
                        <span class="text-on-surface-variant text-sm font-medium">Rythme card.</span>
                        <span class="font-bold text-sm <?= (int)($ordonnance['rythme_cardiaque'] ?? 0) > 100 ? 'text-error' : 'text-on-surface' ?>">
                            <?= $ordonnance['rythme_cardiaque'] ? $ordonnance['rythme_cardiaque'] . ' BPM' : '—' ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-on-surface-variant text-sm font-medium">Statut</span>
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-full
                            <?= match($ordonnance['statut'] ?? 'active') {
                                'active'   => 'bg-emerald-100 text-emerald-700',
                                'archivee' => 'bg-amber-100 text-amber-700',
                                default    => 'bg-red-100 text-red-700'
                            } ?>">
                            <?= ucfirst($ordonnance['statut'] ?? 'active') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Adherence Card -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm overflow-hidden fade-in">
                <h4 class="text-lg font-bold font-headline mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">monitoring</span>
                    Médicaments prescrits
                </h4>
                <?php $medCount = count($medicaments); ?>
                <div class="relative h-2 bg-surface-container rounded-full mb-5">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-tertiary to-tertiary-fixed rounded-full transition-all"
                         style="width: <?= min(100, $medCount * 33) ?>%"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-surface-container-low rounded-xl">
                        <div class="text-2xl font-black font-headline text-primary"><?= $medCount ?></div>
                        <div class="text-[10px] uppercase font-bold text-outline">Médicaments</div>
                    </div>
                    <div class="p-4 bg-surface-container-low rounded-xl">
                        <div class="text-2xl font-black font-headline text-tertiary">
                            <?= date('d/m', strtotime($ordonnance['date_emission'])) ?>
                        </div>
                        <div class="text-[10px] uppercase font-bold text-outline">Date émission</div>
                    </div>
                </div>
            </div>

            <!-- Note Pharmacien -->
            <?php if (!empty($ordonnance['note_pharmacien'])): ?>
            <div class="bg-blue-900 text-white p-6 rounded-2xl shadow-xl relative overflow-hidden fade-in">
                <div class="absolute -right-8 -bottom-8 opacity-10">
                    <span class="material-symbols-outlined text-9xl">medical_information</span>
                </div>
                <h4 class="text-lg font-bold font-headline mb-2">Note au Pharmacien</h4>
                <p class="text-blue-100 text-sm leading-relaxed mb-4">
                    <?= htmlspecialchars($ordonnance['note_pharmacien']) ?>
                </p>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-blue-300">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    Directives appliquées
                </div>
            </div>
            <?php endif ?>

            <!-- Actions rapides -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm fade-in">
                <h4 class="text-sm font-bold text-blue-900 mb-3">Actions rapides</h4>
                <div class="space-y-2">
                    <a href="index.php?page=ordonnance&action=add&consult_id=<?= $ordonnance['id_consultation'] ?>"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                            <span class="material-symbols-outlined text-lg">add</span>
                        </div>
                        <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">
                            Nouvelle ordonnance
                        </span>
                    </a>
                    <a href="index.php?page=dossier&patient_id=<?= $ordonnance['id_patient'] ?>"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <div class="w-9 h-9 rounded-lg bg-tertiary/10 flex items-center justify-center text-tertiary group-hover:bg-tertiary group-hover:text-white transition-all">
                            <span class="material-symbols-outlined text-lg">folder_open</span>
                        </div>
                        <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">
                            Dossier du patient
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Form -->
<form id="delete-ord-form" method="POST" action="index.php?page=ordonnance&action=delete" style="display:none">
    <input type="hidden" name="id" id="delete-ord-id"/>
    <input type="hidden" name="patient_id" id="delete-ord-patient-id"/>
</form>

<script>
function confirmDeleteOrd(ordId, patientId) {
    if (!confirm('Supprimer cette ordonnance définitivement ?')) return;
    document.getElementById('delete-ord-id').value = ordId;
    document.getElementById('delete-ord-patient-id').value = patientId;
    document.getElementById('delete-ord-form').submit();
}
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
</body>
</html>
