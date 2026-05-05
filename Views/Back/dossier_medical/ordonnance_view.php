<?php // Views/Back/dossier_medical/ordonnance_view.php
$pName = htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? ''));
$dName = htmlspecialchars(($ordonnance['prenom_medecin'] ?? '') . ' ' . ($ordonnance['nom_medecin'] ?? ''));
?>

<!-- Flash -->
<?php if (!empty($flash)): ?>
<div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl max-w-6xl mx-auto fade-in
    <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
    <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
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
            <nav class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-2">
                <a href="/integration/dossier/view?patient_id=<?= $ordonnance['id_patient'] ?>"
                   class="hover:text-primary transition-colors"><?= $pName ?></a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-primary font-semibold"><?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?></span>
            </nav>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-800">Prescription Management</h1>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold bg-slate-200 text-slate-800 hover:bg-slate-300 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined">print</span> Imprimer
            </button>

            <!-- Statut Toggle -->
            <?php if (($ordonnance['statut'] ?? '') === 'active'): ?>
            <form method="POST" action="/integration/dossier/ordonnance/edit?id=<?= $ordonnance['id_ordonnance'] ?>" class="m-0">
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
            <form id="delete-ord-form" method="POST" action="/integration/dossier/ordonnance/delete" class="m-0" onsubmit="return confirm('Supprimer cette ordonnance définitivement ?');">
                <input type="hidden" name="id" value="<?= $ordonnance['id_ordonnance'] ?>"/>
                <input type="hidden" name="patient_id" value="<?= $ordonnance['id_patient'] ?>"/>
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold bg-error text-white shadow-lg hover:opacity-90 transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined">delete</span> Supprimer
                </button>
            </form>

            <!-- Edit -->
            <a href="/integration/dossier/ordonnance/edit?id=<?= $ordonnance['id_ordonnance'] ?>"
               class="flex items-center gap-2 px-8 py-3 rounded-xl font-bold bg-gradient-to-br from-primary to-primary-container text-white shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined">edit</span> Modifier
            </a>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pb-12">

    <!-- ══ LEFT: Prescription Paper ══════════════════════════ -->
    <div class="lg:col-span-8 fade-in">
        <div class="bg-surface-container-lowest rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.04)] overflow-hidden relative border-t-4 border-primary print-shadow">

            <!-- status badge -->
            <?php if (($ordonnance['statut'] ?? 'active') !== 'active'): ?>
            <div class="absolute top-4 right-4 px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full
                <?= $ordonnance['statut'] === 'archivee' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600' ?>">
                <?= ucfirst($ordonnance['statut']) ?>
            </div>
            <?php endif ?>

            <!-- Paper Header -->
            <div class="p-8 md:p-10 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start gap-4">
                <div>
                    <div class="text-xl font-extrabold text-primary font-headline mb-1 uppercase tracking-widest">
                        MediFlow Clinical
                    </div>
                    <div class="text-slate-500 font-medium text-sm">
                        Cabinet Médical — Espace Praticien
                    </div>
                    <div class="text-slate-800 text-sm font-bold mt-1">
                        Dr. <?= $dName ?>
                    </div>
                </div>
                <div class="md:text-right">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Date d'émission</div>
                    <div class="text-lg font-headline font-extrabold text-slate-800">
                        <?= date('d F Y', strtotime($ordonnance['date_emission'])) ?>
                    </div>
                </div>
            </div>

            <!-- Patient Info Bar -->
            <div class="bg-slate-50 px-8 md:px-10 py-5 flex flex-wrap gap-8 md:gap-10 border-b border-slate-100">
                <div>
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Patient</span>
                    <span class="text-lg font-bold font-headline text-slate-800"><?= $pName ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Email</span>
                    <span class="font-bold text-sm text-slate-700"><?= htmlspecialchars($patient['mail'] ?? '') ?></span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Diagnostic</span>
                    <span class="font-bold text-sm text-slate-700 max-w-[200px] truncate block" title="<?= htmlspecialchars($consultation['diagnostic'] ?? '—') ?>"><?= htmlspecialchars($consultation['diagnostic'] ?? '—') ?></span>
                </div>
                <div class="md:ml-auto md:text-right">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">N° Ordonnance</span>
                    <span class="text-lg font-extrabold font-headline text-primary">
                        <?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?>
                    </span>
                </div>
            </div>

            <!-- Medication List -->
            <div class="p-8 md:p-10 space-y-8">
                <?php if (empty($medicaments)): ?>
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">medication_liquid</span>
                    <p class="text-slate-500 font-medium text-sm">Aucun médicament prescrit.</p>
                </div>
                <?php else: ?>
                <?php foreach ($medicaments as $idx => $med): ?>
                <div class="relative pl-5 md:pl-6 fade-in" style="animation-delay: <?= $idx * .08 ?>s">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary rounded-full"></div>
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 gap-2">
                        <h3 class="text-xl md:text-2xl font-bold font-headline text-slate-800">
                            <?= htmlspecialchars($med['nom'] ?? '') ?>
                            <?php if (!empty($med['dosage'])): ?>
                            <span class="text-base font-medium text-slate-500 ml-1"><?= htmlspecialchars($med['dosage']) ?></span>
                            <?php endif ?>
                        </h3>
                        <?php if (!empty($med['categorie'])): ?>
                        <span class="self-start md:self-center px-3 py-1 bg-primary/10 text-primary font-bold text-xs rounded-full uppercase border border-primary/20 whitespace-nowrap">
                            <?= htmlspecialchars($med['categorie']) ?>
                        </span>
                        <?php endif ?>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mt-4">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Dosage</span>
                            <p class="font-bold text-sm text-slate-700"><?= htmlspecialchars($med['dosage'] ?? '—') ?></p>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Fréquence</span>
                            <p class="font-bold text-sm text-slate-700"><?= htmlspecialchars($med['frequence'] ?? '—') ?></p>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Durée</span>
                            <p class="font-bold text-sm text-primary"><?= htmlspecialchars($med['duree'] ?? '—') ?></p>
                        </div>
                    </div>
                    <?php if (!empty($med['instructions'])): ?>
                    <div class="mt-4 p-4 bg-slate-50 rounded-xl font-medium text-slate-600 text-sm border border-slate-100 flex items-start gap-2">
                        <span class="material-symbols-outlined text-[18px] text-primary mt-0.5 shrink-0">info</span>
                        <span><?= htmlspecialchars($med['instructions']) ?></span>
                    </div>
                    <?php endif ?>
                </div>
                <?php endforeach ?>
                <?php endif ?>
            </div>

            <!-- Signature Area -->
            <div class="mt-4 p-8 md:p-10 border-t border-slate-100 bg-slate-50 flex justify-end">
                <div class="text-center w-full md:w-64">
                    <div class="text-xs font-bold text-slate-400 uppercase mb-4 tracking-widest">Cachet & Signature</div>
                    <div class="h-16 flex items-center justify-center mb-3">
                        <svg viewBox="0 0 180 50" class="w-40 opacity-50 text-primary" fill="none">
                            <path d="M10 35 C30 10, 60 40, 80 20 C100 2, 120 38, 150 25 C160 20, 165 30, 170 28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M5 40 C20 38 170 40 175 42" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="text-sm font-bold font-headline text-slate-800">
                        Dr. <?= $dName ?>
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Médecin Praticien — MediFlow</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT: Metadata ════════════════════════════════════ -->
    <div class="lg:col-span-4 space-y-6 no-print">

        <!-- Clinical Summary -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.04)] border-t-4 border-emerald-500 fade-in border-x border-b border-slate-100">
            <h4 class="text-lg font-bold font-headline mb-4 flex items-center gap-2 text-slate-800">
                <span class="material-symbols-outlined text-emerald-500">assignment_ind</span>
                Contexte clinique
            </h4>
            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Diagnostic principal</span>
                    <p class="font-medium text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <?= htmlspecialchars($consultation['diagnostic'] ?? 'Aucun diagnostic renseigné') ?>
                    </p>
                </div>
                <div>
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Consultation liée</span>
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="font-medium text-sm text-slate-700">
                            <?= date('d/m/Y', strtotime($consultation['date_consultation'] ?? $ordonnance['date_emission'])) ?>
                        </span>
                        <a href="/integration/dossier/view?patient_id=<?= $ordonnance['id_patient'] ?>" class="text-primary hover:text-primary-container transition-colors" title="Voir le dossier">
                            <span class="material-symbols-outlined text-[20px]">folder_open</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pharmacist Note -->
        <?php if (!empty($ordonnance['note_pharmacien'])): ?>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.04)] border-t-4 border-amber-500 fade-in border-x border-b border-slate-100" style="animation-delay: 0.1s">
            <h4 class="text-lg font-bold font-headline mb-3 flex items-center gap-2 text-slate-800">
                <span class="material-symbols-outlined text-amber-500">info</span>
                Note au pharmacien
            </h4>
            <p class="text-sm font-medium text-slate-600 bg-amber-50/50 p-4 rounded-xl border border-amber-100 italic leading-relaxed">
                <?= nl2br(htmlspecialchars($ordonnance['note_pharmacien'])) ?>
            </p>
        </div>
        <?php endif ?>

    </div>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
@media print {
    aside, header, .no-print { display: none !important; }
    main { margin-left: 0 !important; padding-top: 0 !important; }
    .print-shadow { box-shadow: none !important; border: 1px solid #e0e3e5 !important; }
    body { background: white !important; }
}
</style>
