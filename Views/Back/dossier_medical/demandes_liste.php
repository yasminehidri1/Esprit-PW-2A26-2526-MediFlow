<?php // Views/Back/dossier_medical/demandes_liste.php ?>

<div class="max-w-5xl mx-auto space-y-8">
    <!-- Flash -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl fade-in
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
        <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between md:items-end mb-10 gap-4 fade-in">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 tracking-tighter mb-2 font-headline">
                Demandes d'Ordonnance
            </h1>
            <p class="text-slate-500 font-medium">
                <?php
                $total      = count($demandes);
                $enAttente  = count(array_filter($demandes, fn($d) => $d['statut'] === 'en_attente'));
                ?>
                <span class="text-blue-700 font-bold"><?= $total ?> demande<?= $total > 1 ? 's' : '' ?></span> reçue<?= $total > 1 ? 's' : '' ?>
                <?php if ($enAttente > 0): ?>
                — <span class="text-amber-600 font-bold"><?= $enAttente ?> en attente</span>
                <?php endif ?>
            </p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 overflow-hidden fade-in">

        <?php if (empty($demandes)): ?>
        <div class="px-8 py-20 text-center text-slate-400">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100">
                <span class="material-symbols-outlined text-4xl text-slate-300">inbox</span>
            </div>
            <p class="font-bold text-lg text-slate-700 mb-1 font-headline">Aucune demande reçue</p>
            <p class="text-sm font-medium">Les demandes envoyées par vos patients apparaîtront ici.</p>
        </div>

        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Patient</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Description</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Date</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Statut</th>
                        <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php foreach ($demandes as $idx => $d):
                    $initials = strtoupper(substr($d['patient_prenom'], 0, 1) . substr($d['patient_nom'], 0, 1));
                    $fullName = htmlspecialchars($d['patient_prenom'] . ' ' . $d['patient_nom']);
                    $dateStr  = date('d/m/Y à H:i', strtotime($d['created_at']));

                    $statutCfg = match($d['statut']) {
                        'en_attente' => ['bg-amber-50 text-amber-700 border-amber-200',  'schedule',      'En attente'],
                        'traitee'    => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'check_circle', 'Traitée'],
                        'refusee'    => ['bg-red-50 text-red-600 border-red-200',       'cancel',        'Refusée'],
                        default      => ['bg-slate-100 text-slate-500 border-slate-200',  'help',          $d['statut']],
                    };
                    [$statutClass, $statutIcon, $statutLabel] = $statutCfg;

                    $avatarColors = ['bg-blue-100 text-blue-700 border-blue-200', 'bg-violet-100 text-violet-700 border-violet-200',
                                     'bg-teal-100 text-teal-700 border-teal-200', 'bg-rose-100 text-rose-700 border-rose-200'];
                    $avatarClass  = $avatarColors[($d['id_demande'] ?? $idx) % count($avatarColors)];
                ?>
                <tr class="group hover:bg-slate-50/80 transition-colors">

                    <!-- Patient -->
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-full <?= $avatarClass ?> flex items-center justify-center font-bold text-sm border shadow-sm shrink-0">
                                <?= $initials ?>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm font-headline"><?= $fullName ?></p>
                                <p class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($d['patient_mail']) ?></p>
                            </div>
                        </div>
                    </td>

                    <!-- Description -->
                    <td class="px-6 py-5">
                        <div class="max-w-xs whitespace-normal">
                            <p class="text-[13px] font-medium text-slate-600 line-clamp-2" title="<?= htmlspecialchars($d['description']) ?>"><?= htmlspecialchars($d['description']) ?></p>
                        </div>
                    </td>

                    <!-- Date -->
                    <td class="px-6 py-5">
                        <span class="text-sm text-slate-700 font-semibold"><?= $dateStr ?></span>
                    </td>

                    <!-- Statut -->
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border <?= $statutClass ?>">
                            <span class="material-symbols-outlined text-[14px]"><?= $statutIcon ?></span>
                            <?= $statutLabel ?>
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-5 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if ($d['statut'] === 'en_attente'): ?>
                            <form method="POST" action="/integration/dossier/demandes/statut" class="m-0 inline">
                                <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                                <input type="hidden" name="statut" value="traitee"/>
                                <button type="submit" title="Marquer comme traitée"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">check</span>
                                </button>
                            </form>
                            <form method="POST" action="/integration/dossier/demandes/statut" class="m-0 inline" onsubmit="return confirm('Refuser cette demande ?')">
                                <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                                <input type="hidden" name="statut" value="refusee"/>
                                <button type="submit" title="Refuser"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </form>
                            <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $d['id_patient'] ?>" title="Créer une ordonnance/consultation"
                               class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors ml-2">
                                <span class="material-symbols-outlined text-[16px]">history_edu</span>
                                Créer Ordo.
                            </a>
                            <?php else: ?>
                            <form method="POST" action="/integration/dossier/demandes/statut" class="m-0 inline">
                                <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                                <input type="hidden" name="statut" value="en_attente"/>
                                <button type="submit"
                                        class="px-4 py-1.5 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 hover:text-slate-700 rounded-lg transition-colors border border-slate-200">
                                    Remettre en attente
                                </button>
                            </form>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php endif ?>
    </div>

</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
</style>
<script>
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
