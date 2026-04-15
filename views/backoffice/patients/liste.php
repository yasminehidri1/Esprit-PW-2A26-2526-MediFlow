<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Ma Liste de Patients</title>
    <meta name="description" content="Liste des patients du médecin — MediFlow Espace Praticien"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: {
                colors: {
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
                fontFamily: { headline: ["Manrope"], body: ["Inter"], label: ["Inter"] },
                borderRadius: { DEFAULT:"0.25rem", lg:"0.5rem", xl:"0.75rem", full:"9999px" }
            }}
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; vertical-align:middle; }
        body { font-family:'Inter', sans-serif; }
        h1,h2,h3,h4 { font-family:'Manrope', sans-serif; }
        .patient-row { transition: all .15s ease; }
        .patient-row.hidden-row { display: none; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeIn .3s ease forwards; }
        @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:.4;} }
        .live-dot { animation: pulse-dot 1.8s infinite; }
    </style>
</head>
<body class="bg-surface text-on-surface">

<?php
$activePage = 'patients';
$breadcrumb = [['label' => 'Mes Patients']];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<!-- ═══════════════════════════════════════════════════════════ -->
<!--  Main Content                                               -->
<!-- ═══════════════════════════════════════════════════════════ -->
<main class="ml-64 min-h-screen pt-24 px-12 pb-16">
<div class="max-w-6xl mx-auto">

    <!-- Flash Message -->
    <?php if (!empty($flash)): ?>
    <div id="flash-msg" class="mb-6 flex items-center gap-3 p-4 rounded-xl
        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>
        fade-in">
        <span class="material-symbols-outlined"><?= $flash['type'] === 'success' ? 'check_circle' : 'info' ?></span>
        <span class="font-medium text-sm"><?= htmlspecialchars($flash['msg']) ?></span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    <?php endif ?>

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 tracking-tighter mb-2">Ma Liste de Patients</h1>
            <p class="text-slate-500 font-medium">
                Vous avez <span class="text-primary font-bold"><?= $totalCount ?> dossiers</span> enregistrés.
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleFilter()" id="filter-btn"
                    class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-highest text-on-surface-variant rounded-xl font-semibold text-sm hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-lg">filter_list</span>
                Filtrer
            </button>
        </div>
    </div>

    <!-- Filter Bar (hidden by default) -->
    <div id="filter-bar" class="hidden mb-6 p-4 bg-surface-container-lowest rounded-2xl border border-slate-100 shadow-sm fade-in">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="relative flex-1 min-w-[200px]">
                <input id="search-input" type="text" placeholder="Nom, prénom ou email..."
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            </div>
            <select id="filter-diagnostic" onchange="filterTable()"
                    class="bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="">Tous les diagnostics</option>
                <?php
                $diagnostics = array_unique(array_filter(array_column($patients, 'dernier_diagnostic')));
                foreach ($diagnostics as $d): ?>
                    <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                <?php endforeach ?>
            </select>
            <button onclick="clearFilters()" class="text-sm text-slate-500 hover:text-primary font-medium">
                Réinitialiser
            </button>
        </div>
    </div>

    <!-- ── Stats Bento Grid ──────────────────────────────────── -->
    <div class="grid grid-cols-12 gap-6 mb-10">

        <!-- Queue Overview -->
        <div class="col-span-8 bg-surface-container-lowest p-6 rounded-2xl shadow-[0_20px_50px_rgba(0,77,153,0.05)] border-t-2 border-tertiary-fixed fade-in">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-blue-900">Aperçu de la file d'attente</h3>
                <span class="text-xs font-bold text-tertiary bg-tertiary-fixed/30 px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1.5">
                    <span class="live-dot w-1.5 h-1.5 rounded-full bg-tertiary inline-block"></span>
                    En direct
                </span>
            </div>
            <div class="flex gap-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">schedule</span>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-blue-900"><?= str_pad((int)($stats['total_today'] ?? 0), 2, '0', STR_PAD_LEFT) ?></p>
                        <p class="text-xs font-medium text-slate-500">Aujourd'hui</p>
                    </div>
                </div>
                <div class="w-px h-12 bg-slate-100"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined text-3xl">check_circle</span>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-blue-900"><?= str_pad((int)($stats['termines'] ?? 0), 2, '0', STR_PAD_LEFT) ?></p>
                        <p class="text-xs font-medium text-slate-500">Terminés</p>
                    </div>
                </div>
                <div class="w-px h-12 bg-slate-100"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-3xl">pending_actions</span>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-blue-900"><?= str_pad((int)($stats['en_attente'] ?? 0), 2, '0', STR_PAD_LEFT) ?></p>
                        <p class="text-xs font-medium text-slate-500">En attente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Patients Card -->
        <div class="col-span-4 bg-gradient-to-br from-primary to-primary-container p-6 rounded-2xl shadow-xl flex flex-col justify-between relative overflow-hidden group fade-in">
            <div class="absolute -right-4 -top-4 opacity-10 transition-transform group-hover:scale-110 duration-500">
                <span class="material-symbols-outlined text-9xl">medical_services</span>
            </div>
            <p class="text-blue-100 text-sm font-medium relative z-10">Total Patients</p>
            <h4 class="text-white text-5xl font-extrabold relative z-10 mt-1"><?= $totalCount ?></h4>
            <div class="mt-4 relative z-10 flex items-center gap-2 text-white/80 text-xs">
                <span class="material-symbols-outlined text-sm">people</span>
                <span>Dossiers actifs dans le système</span>
            </div>
        </div>
    </div>

    <!-- ── Patient Table ─────────────────────────────────────── -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_20px_50px_rgba(0,77,153,0.05)] overflow-hidden">

        <!-- Table Header -->
        <table class="w-full text-left border-collapse" id="patient-table">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Patient</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Dernière Visite</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Diagnostic</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="patient-tbody">
                <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-16 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl block mb-3">person_off</span>
                        <p class="font-medium">Aucun patient trouvé.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php
                // Color palette cycling for diagnostic badges
                $badgeColors = [
                    'blue'  => 'bg-blue-50 text-blue-700 bg-blue-500',
                    'amber' => 'bg-amber-50 text-amber-700 bg-amber-500',
                    'teal'  => 'bg-teal-50 text-teal-700 bg-teal-500',
                    'rose'  => 'bg-rose-50 text-rose-700 bg-rose-500',
                    'violet'=> 'bg-violet-50 text-violet-700 bg-violet-500',
                ];
                $colorKeys = array_keys($badgeColors);
                foreach ($patients as $i => $patient):
                    $colorKey = $colorKeys[$i % count($colorKeys)];
                    [$bgBadge, $textBadge, $dot] = explode(' ', $badgeColors[$colorKey]);
                    $fullName     = htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']);
                    $initials     = strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1));
                    $dernVisite   = $patient['derniere_visite']
                        ? date('d M Y', strtotime($patient['derniere_visite']))
                        : '—';
                    $derType      = htmlspecialchars($patient['dernier_type'] ?? '—');
                    $diagnostic   = htmlspecialchars($patient['dernier_diagnostic'] ?? 'Non renseigné');
                    $consultId    = (int)($patient['derniere_consult_id'] ?? 0);
                ?>
                <tr class="patient-row group hover:bg-slate-50/80 transition-colors fade-in"
                    data-name="<?= strtolower($patient['prenom'] . ' ' . $patient['nom'] . ' ' . $patient['mail']) ?>"
                    data-diagnostic="<?= strtolower($patient['dernier_diagnostic'] ?? '') ?>">

                    <!-- Patient Info -->
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-full <?= $bgBadge ?> flex items-center justify-center <?= $textBadge ?> font-bold text-sm ring-2 ring-white shadow-sm">
                                <?= $initials ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 group-hover:text-primary transition-colors"><?= $fullName ?></h4>
                                <p class="text-xs text-slate-500">
                                    ID: #<?= $patient['id_PK'] ?> &bull;
                                    <?= htmlspecialchars($patient['mail']) ?>
                                </p>
                            </div>
                        </div>
                    </td>

                    <!-- Last Visit -->
                    <td class="px-6 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700"><?= $dernVisite ?></span>
                            <span class="text-[10px] text-slate-400 font-medium"><?= $derType ?></span>
                        </div>
                    </td>

                    <!-- Diagnostic Badge -->
                    <td class="px-6 py-6">
                        <?php if ($patient['dernier_diagnostic']): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $bgBadge ?> <?= $textBadge ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $dot ?> mr-2"></span>
                            <?= $diagnostic ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-400 text-xs font-medium">—</span>
                        <?php endif ?>
                    </td>

                    <!-- Actions -->
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <?php if ($consultId > 0): ?>
                            <a href="index.php?page=ordonnance&consult_id=<?= $consultId ?>"
                               class="px-4 py-2 text-xs font-bold text-primary hover:bg-blue-50 rounded-lg transition-colors">
                                Voir l'ordonnance
                            </a>
                            <?php endif ?>
                            <a href="index.php?page=dossier&patient_id=<?= $patient['id_PK'] ?>"
                               class="px-4 py-2 text-xs font-bold bg-surface-container-low text-blue-900 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">clinical_notes</span>
                                Dossier Médical
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-500 font-medium">
                Affichage de
                <span class="text-blue-900 font-bold"><?= min(($page - 1) * 10 + 1, $totalCount) ?> à <?= min($page * 10, $totalCount) ?></span>
                sur <span class="text-blue-900 font-bold"><?= $totalCount ?></span> patients
            </p>
            <div class="flex gap-1">
                <?php if ($page > 1): ?>
                <a href="index.php?page=patients&p=<?= $page - 1 ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </a>
                <?php endif ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="index.php?page=patients&p=<?= $p ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg font-bold text-xs transition-colors
                          <?= $p === $page ? 'bg-primary text-white' : 'hover:bg-slate-100 text-slate-600' ?>">
                    <?= $p ?>
                </a>
                <?php endfor ?>
                <?php if ($page < $totalPages): ?>
                <a href="index.php?page=patients&p=<?= $page + 1 ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </a>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Info tip -->
    <div class="mt-8 p-4 bg-tertiary-fixed/10 rounded-xl border border-tertiary-fixed/20 flex items-center gap-4 fade-in">
        <div class="w-10 h-10 rounded-full bg-tertiary-fixed flex items-center justify-center text-tertiary shrink-0">
            <span class="material-symbols-outlined">lightbulb</span>
        </div>
        <div>
            <p class="text-sm font-bold text-tertiary">Conseil de gestion</p>
            <p class="text-xs text-on-tertiary-fixed-variant opacity-80">
                Les dossiers n'ayant pas été consultés depuis plus de 2 ans seront archivés automatiquement le mois prochain.
            </p>
        </div>
    </div>

</div>
</main>

<script>
// ── Client-side live filter ─────────────────────────────────────
const searchInput      = document.getElementById('search-input');
const filterDiagnostic = document.getElementById('filter-diagnostic');

if (searchInput) {
    searchInput.addEventListener('input', filterTable);
    // Auto-open filter bar if search param exists
    if (searchInput.value) {
        document.getElementById('filter-bar').classList.remove('hidden');
    }
}

function filterTable() {
    const q   = (searchInput?.value || '').toLowerCase();
    const diag = (filterDiagnostic?.value || '').toLowerCase();
    document.querySelectorAll('.patient-row').forEach(row => {
        const name      = row.dataset.name     || '';
        const rowDiag   = row.dataset.diagnostic || '';
        const matchQ    = !q    || name.includes(q);
        const matchDiag = !diag || rowDiag.includes(diag);
        row.classList.toggle('hidden-row', !(matchQ && matchDiag));
    });
}

function toggleFilter() {
    const bar = document.getElementById('filter-bar');
    bar.classList.toggle('hidden');
    if (!bar.classList.contains('hidden')) searchInput?.focus();
}

function clearFilters() {
    if (searchInput) searchInput.value = '';
    if (filterDiagnostic) filterDiagnostic.value = '';
    filterTable();
}

// Auto-dismiss flash after 4s
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
</body>
</html>
