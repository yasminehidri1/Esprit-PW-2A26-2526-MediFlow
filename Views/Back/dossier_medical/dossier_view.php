<?php
// Views/Back/dossier_medical/dossier_view.php — Patient Dossier (Doctor view)
// Partial rendered inside layout.php
$pName = htmlspecialchars($patient['prenom'].' '.$patient['nom']);
$pid   = $patient['id_PK'];
$fullName = $pName;
?>

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
                <?php if (!empty($patient['tel'])): ?>
                &bull; <span class="material-symbols-outlined text-sm">call</span>
                <?= htmlspecialchars($patient['tel']) ?>
                <?php endif ?>
            </p>
        </div>
    </div>
    <div class="flex gap-3 no-print">
        <button onclick="window.print()"
                class="px-5 py-2.5 rounded-lg font-semibold text-primary border border-primary hover:bg-primary/10 transition-colors flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined">print</span> Imprimer
        </button>
        <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $pid ?>"
                class="px-6 py-2.5 rounded-lg font-semibold text-white bg-gradient-to-r from-primary to-primary-container shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined">add</span> Nouvelle Consultation
        </a>
    </div>
</div>

<!-- Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-8">

    <!-- ══ LEFT COLUMN (4 cols) ══════════════════════════════ -->
    <div class="md:col-span-4 space-y-6">

        <!-- Personal Info -->
        <section class="bg-white rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] relative overflow-hidden fade-in">
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
        <section class="bg-white rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] border-t-2 border-tertiary fade-in">
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
        <section class="bg-white rounded-xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
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
        <section class="bg-white rounded-xl p-8 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">history</span>
                    <h3 class="text-xl font-bold text-on-surface">Antécédents Médicaux</h3>
                </div>
                <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $pid ?>"
                        class="no-print text-sm font-bold text-primary hover:underline transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Ajouter via consultation
                </a>
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
        <section class="bg-white rounded-xl p-8 shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in">
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
                            <th class="no-print py-4 px-2 text-right text-[10px] font-bold text-outline uppercase tracking-widest">Actions</th>
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
                                <span class="text-xs px-2 py-1 <?= $typColor ?> rounded-md font-medium whitespace-nowrap">
                                    <?= htmlspecialchars($c['type_consultation'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="py-5 px-2">
                                <span class="text-sm font-semibold text-on-surface truncate max-w-[120px] block" title="<?= htmlspecialchars($c['diagnostic'] ?? '—') ?>">
                                    <?= htmlspecialchars($c['diagnostic'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="py-5 px-2">
                                <p class="text-sm text-on-surface-variant truncate max-w-xs" title="<?= htmlspecialchars($c['compte_rendu'] ?? '—') ?>">
                                    <?= htmlspecialchars($c['compte_rendu'] ?? '—') ?>
                                </p>
                            </td>
                            <td class="no-print py-5 px-2 text-right">
                                <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Ordonnance -->
                                    <a href="/integration/dossier/ordonnance/view?consult_id=<?= $c['id_consultation'] ?>"
                                       title="Voir ordonnance"
                                       class="p-1.5 rounded-lg hover:bg-blue-50 text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">prescriptions</span>
                                    </a>
                                    <!-- Edit -->
                                    <a href="/integration/dossier/consultation/edit?id=<?= $c['id_consultation'] ?>"
                                       title="Modifier"
                                       class="p-1.5 rounded-lg hover:bg-slate-100 text-outline transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <!-- Delete -->
                                    <form method="POST" action="/integration/dossier/consultation/delete" class="inline"
                                          onsubmit="return confirm('Supprimer cette consultation ?')">
                                      <input type="hidden" name="id" value="<?= $c['id_consultation'] ?>">
                                      <input type="hidden" name="id_patient" value="<?= $pid ?>">
                                      <button type="submit"
                                              title="Supprimer"
                                              class="p-1.5 rounded-lg hover:bg-error-container/30 text-error/60 hover:text-error transition-colors">
                                          <span class="material-symbols-outlined text-lg">delete</span>
                                      </button>
                                    </form>
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
                <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $pid ?>" class="text-primary font-semibold hover:underline ml-1">
                    Ajouter la première consultation
                </a>
            </p>
            <?php endif ?>
        </section>
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }

/* ── Print styles ─────────────────────────────── */
@media print {
    /* Hide all chrome */
    aside,
    header,
    nav,
    #topbar,
    #flash-msg,
    .no-print { display: none !important; }

    /* Remove sidebar offset, reset background */
    main { margin-left: 0 !important; background: white !important; }
    body { background: white !important; color: #000 !important; }

    /* Hide action buttons in the patient header */
    .flex.gap-3 > button,
    .flex.gap-3 > a[href*="nouvelle-consultation"] { display: none !important; }

    /* Flatten bento grid to single column */
    .grid.grid-cols-1.md\:grid-cols-12 { display: block !important; }
    .md\:col-span-4,
    .md\:col-span-8 { width: 100% !important; margin-bottom: 1.5rem; }

    /* Remove box shadows */
    * { box-shadow: none !important; }

    /* Make cards border instead of shadow */
    section { border: 1px solid #ddd !important; break-inside: avoid; }

    /* Hide table action column */
    th:last-child,
    td:last-child { display: none !important; }

    /* Always show table row actions column? No — hide edit/delete icons */
    .group-hover\:opacity-100 { opacity: 0 !important; }

    /* Print header: keep it clean */
    .fade-in { animation: none !important; opacity: 1 !important; }

    /* Page setup */
    @page { margin: 1.5cm; size: A4 portrait; }
}
</style>
