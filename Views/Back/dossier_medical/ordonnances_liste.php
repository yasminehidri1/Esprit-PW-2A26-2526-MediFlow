<?php // Views/Back/dossier_medical/ordonnances_liste.php ?>

<div class="max-w-6xl mx-auto">

    <!-- Flash -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl fade-in
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
        <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Header -->
    <div class="flex justify-between items-end mb-10 fade-in">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 tracking-tighter mb-2">Mes Ordonnances</h1>
            <p class="text-slate-500 font-medium">
                <span class="text-primary font-bold"><?= $totalOrdo ?> ordonnance(s)</span>
                pour <span class="text-primary font-bold"><?= count($grouped) ?> patient(s)</span>
            </p>
        </div>
        <!-- Search + Filter -->
        <div class="flex gap-3 items-center">
            <div class="relative">
                <input id="search-ordo" type="text" placeholder="Rechercher un patient ou diagnostic..."
                       class="w-72 bg-surface-container-low border-none rounded-full py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                       oninput="filterOrdonnances(this.value)"/>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
            </div>
            <select id="filter-statut" onchange="filterByStatut(this.value)"
                    class="bg-surface-container-low border-none rounded-xl py-2.5 px-4 text-sm outline-none focus:ring-2 focus:ring-primary/20 font-medium">
                <option value="">Tous les statuts</option>
                <option value="active">Active</option>
                <option value="archivee">Archivée</option>
                <option value="annulee">Annulée</option>
            </select>
        </div>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-3 gap-5 mb-10 fade-in">
        <?php
        $statsActives  = 0; $statsArchivees = 0; $statsAnnulees = 0;
        foreach ($grouped as $g) {
            foreach ($g['ordonnances'] as $o) {
                match ($o['statut']) {
                    'active'   => $statsActives++,
                    'archivee' => $statsArchivees++,
                    default    => $statsAnnulees++,
                };
            }
        }
        $statsCards = [
            ['Actives',  $statsActives,   'check_circle',    'from-emerald-500 to-teal-500'],
            ['Archivées',$statsArchivees, 'inventory_2',     'from-amber-400 to-orange-400'],
            ['Annulées', $statsAnnulees,  'cancel',           'from-red-400 to-rose-500'],
        ];
        foreach ($statsCards as [$label, $count, $icon, $grad]):
        ?>
        <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.04)] flex items-center gap-4 border border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $grad ?> flex items-center justify-center text-white shadow-sm">
                <span class="material-symbols-outlined"><?= $icon ?></span>
            </div>
            <div>
                <p class="text-2xl font-black text-blue-900"><?= $count ?></p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?= $label ?></p>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <!-- Empty state -->
    <?php if (empty($grouped)): ?>
    <div class="bg-surface-container-lowest rounded-2xl p-16 text-center shadow-[0_4px_20px_rgba(0,77,153,0.04)] fade-in border border-slate-100">
        <span class="material-symbols-outlined text-5xl text-slate-300 block mb-4">prescriptions</span>
        <h3 class="text-xl font-bold text-slate-500 mb-2">Aucune ordonnance</h3>
        <p class="text-slate-400 text-sm mb-6">Aucune ordonnance n'a été créée pour vos patients.</p>
        <a href="/integration/dossier/patients"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm hover:-translate-y-0.5 transition-all shadow-md">
            <span class="material-symbols-outlined text-sm">people</span> Voir mes patients
        </a>
    </div>

    <?php else: ?>

    <!-- Patient Groups -->
    <div id="groups-container" class="space-y-6">
        <?php foreach ($grouped as $gi => $group):
            $initials = strtoupper(substr($group['prenom_patient'], 0, 1) . substr($group['nom_famille'], 0, 1));
            $colors   = ['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700',
                         'bg-amber-100 text-amber-700','bg-rose-100 text-rose-700','bg-emerald-100 text-emerald-700'];
            $color    = $colors[$gi % count($colors)];
            $groupId  = 'group-' . $group['id_patient'];
        ?>
        <div class="patient-group bg-surface-container-lowest rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.04)] overflow-hidden fade-in border border-slate-100"
             style="animation-delay: <?= $gi * .05 ?>s"
             data-patient="<?= strtolower($group['prenom_patient'] . ' ' . $group['nom_famille'] . ' ' . $group['mail_patient']) ?>"
             id="<?= $groupId ?>-wrapper">

            <!-- Patient Header (collapsible) -->
            <button class="collapse-btn w-full flex items-center gap-4 px-6 py-5 hover:bg-slate-50 transition-colors text-left"
                    onclick="toggleGroup('<?= $groupId ?>')">
                <!-- Avatar -->
                <div class="w-11 h-11 rounded-full <?= $color ?> font-bold text-sm flex items-center justify-center shrink-0">
                    <?= $initials ?>
                </div>
                <!-- Name -->
                <div class="flex-1">
                    <h3 class="font-bold text-blue-900"><?= htmlspecialchars($group['prenom_patient'] . ' ' . $group['nom_famille']) ?></h3>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($group['mail_patient']) ?></p>
                </div>
                <!-- Count badge -->
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">
                    <?= count($group['ordonnances']) ?> ordonnance<?= count($group['ordonnances']) > 1 ? 's' : '' ?>
                </span>
                <!-- Arrow -->
                <span class="material-symbols-outlined text-slate-400 arrow transition-transform duration-300">expand_more</span>
            </button>

            <!-- Ordonnances table for this patient -->
            <div id="<?= $groupId ?>" class="border-t border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/60">
                            <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">N° Ordonnance</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Diagnostic</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Médicaments</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</th>
                            <th class="px-6 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($group['ordonnances'] as $ordo):
                            $meds      = json_decode($ordo['medicaments'] ?? '[]', true) ?: [];
                            $medNames  = implode(', ', array_slice(array_column($meds, 'nom'), 0, 2));
                            if (count($meds) > 2) $medNames .= ' +' . (count($meds) - 2);
                            $statutCls = match($ordo['statut']) {
                                'active'   => 'bg-emerald-100 text-emerald-700',
                                'archivee' => 'bg-amber-100 text-amber-700',
                                default    => 'bg-red-100 text-red-600',
                            };
                        ?>
                        <tr class="ordo-row group hover:bg-slate-50/80 transition-colors"
                            data-statut="<?= $ordo['statut'] ?>"
                            data-patient="<?= strtolower($group['prenom_patient'] . ' ' . $group['nom_famille'] . ' ' . $group['mail_patient']) ?>">
                            <td class="px-6 py-4">
                                <span class="font-bold text-primary text-sm">
                                    <?= htmlspecialchars($ordo['numero_ordonnance'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-semibold text-slate-700">
                                    <?= date('d/m/Y', strtotime($ordo['date_emission'])) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-on-surface-variant max-w-[150px] truncate block" title="<?= htmlspecialchars($ordo['diagnostic'] ?? '—') ?>">
                                    <?= htmlspecialchars($ordo['diagnostic'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 max-w-[200px]">
                                <span class="text-xs text-slate-600 truncate block bg-slate-50 px-2 py-1 rounded-lg border border-slate-100" title="<?= htmlspecialchars(implode(', ', array_column($meds, 'nom'))) ?>">
                                    <?= htmlspecialchars($medNames ?: 'Aucun') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $statutCls ?>">
                                    <?= htmlspecialchars($ordo['statut']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/integration/dossier/ordonnance/view?id=<?= $ordo['id_ordonnance'] ?>"
                                       class="p-1.5 rounded-lg text-primary hover:bg-blue-50 transition-colors" title="Voir">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="/integration/dossier/ordonnance/edit?id=<?= $ordo['id_ordonnance'] ?>"
                                       class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Modifier">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div>

<style>
.patient-group{transition:all .2s ease;}
.ordo-row.hidden-row{display:none;}
.collapse-btn .arrow{transition:transform .2s ease;}
.collapsed .arrow{transform:rotate(-90deg);}
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
.fade-in { animation: fadeIn .35s ease forwards; }
</style>

<script>
function toggleGroup(groupId) {
    const content = document.getElementById(groupId);
    const wrapper = document.getElementById(groupId + '-wrapper');
    const btn = wrapper.querySelector('.collapse-btn');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        btn.classList.remove('collapsed');
    } else {
        content.style.display = 'none';
        btn.classList.add('collapsed');
    }
}

function filterOrdonnances(q) {
    q = q.toLowerCase();
    const s = document.getElementById('filter-statut').value;
    applyFilters(q, s);
}

function filterByStatut(s) {
    const q = document.getElementById('search-ordo').value.toLowerCase();
    applyFilters(q, s);
}

function applyFilters(q, s) {
    document.querySelectorAll('.patient-group').forEach(group => {
        let groupHasMatch = false;
        const groupPatient = group.dataset.patient;
        const matchesQGroup = groupPatient.includes(q);

        group.querySelectorAll('.ordo-row').forEach(row => {
            const matchesQ = matchesQGroup || row.innerText.toLowerCase().includes(q);
            const matchesS = (s === '' || row.dataset.statut === s);

            if (matchesQ && matchesS) {
                row.classList.remove('hidden-row');
                groupHasMatch = true;
            } else {
                row.classList.add('hidden-row');
            }
        });

        // Hide group entirely if no ordonnance matches AND patient doesn't match directly
        if (!groupHasMatch && !matchesQGroup) {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    });
}
</script>
