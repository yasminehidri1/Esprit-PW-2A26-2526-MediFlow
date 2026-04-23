<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediFlow — Demandes d'Ordonnance</title>
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
                    "on-primary-fixed":"#001b3d","inverse-on-surface":"#eff1f3","surface-container-lowest":"#ffffff",
                    "outline-variant":"#c2c6d4","on-primary":"#ffffff","background":"#f7f9fb","tertiary":"#005851",
                    "primary":"#004d99","surface-container-high":"#e6e8ea",
                    "primary-fixed-dim":"#a9c7ff","error-container":"#ffdad6",
                    "on-tertiary":"#ffffff","primary-fixed":"#d6e3ff","on-surface":"#191c1e","error":"#ba1a1a",
                    "surface-dim":"#d8dadc","surface-container":"#eceef0","surface-bright":"#f7f9fb",
                    "on-error":"#ffffff","on-primary-container":"#dae5ff","surface-variant":"#e0e3e5",
                    "primary-container":"#1565c0","surface":"#f7f9fb",
                    "tertiary-fixed":"#84f5e8","on-tertiary-fixed-variant":"#005049",
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
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeIn .3s ease forwards; }
    </style>
</head>
<body class="bg-surface text-on-surface">

<?php
$activePage = 'demandes';
$breadcrumb = [['label' => 'Demandes d\'ordonnance']];
require __DIR__ . '/../layout/sidebar.php';
require __DIR__ . '/../layout/topbar.php';
?>

<main class="ml-64 min-h-screen pt-24 px-12 pb-16">
<div class="max-w-5xl mx-auto">

    <!-- Flash -->
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

    <!-- Header -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 tracking-tighter mb-2">
                Demandes d'Ordonnance
            </h1>
            <p class="text-slate-500 font-medium">
                <?php
                $total      = count($demandes);
                $enAttente  = count(array_filter($demandes, fn($d) => $d['statut'] === 'en_attente'));
                ?>
                <span class="text-primary font-bold"><?= $total ?> demande<?= $total > 1 ? 's' : '' ?></span> reçue<?= $total > 1 ? 's' : '' ?>
                <?php if ($enAttente > 0): ?>
                — <span class="text-amber-600 font-bold"><?= $enAttente ?> en attente</span>
                <?php endif ?>
            </p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_20px_50px_rgba(0,77,153,0.05)] overflow-hidden fade-in">

        <?php if (empty($demandes)): ?>
        <div class="px-8 py-20 text-center text-slate-400">
            <span class="material-symbols-outlined text-5xl block mb-4">inbox</span>
            <p class="font-bold text-lg text-slate-500 mb-1">Aucune demande reçue</p>
            <p class="text-sm">Les demandes envoyées par vos patients apparaîtront ici.</p>
        </div>

        <?php else: ?>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Patient</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Description</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Date</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400">Statut</th>
                    <th class="px-6 py-5 text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($demandes as $d):
                $initials = strtoupper(substr($d['patient_prenom'], 0, 1) . substr($d['patient_nom'], 0, 1));
                $fullName = htmlspecialchars($d['patient_prenom'] . ' ' . $d['patient_nom']);
                $dateStr  = date('d M Y à H:i', strtotime($d['created_at']));

                $statutCfg = match($d['statut']) {
                    'en_attente' => ['bg-amber-50 text-amber-700',  'schedule',      'En attente'],
                    'traitee'    => ['bg-emerald-50 text-emerald-700', 'check_circle', 'Traitée'],
                    'refusee'    => ['bg-red-50 text-red-600',       'cancel',        'Refusée'],
                    default      => ['bg-slate-100 text-slate-500',  'help',          $d['statut']],
                };
                [$statutClass, $statutIcon, $statutLabel] = $statutCfg;

                $avatarColors = ['bg-blue-100 text-blue-700', 'bg-violet-100 text-violet-700',
                                 'bg-teal-100 text-teal-700', 'bg-rose-100 text-rose-700'];
                $avatarClass  = $avatarColors[$d['id_demande'] % count($avatarColors)];
            ?>
            <tr class="group hover:bg-slate-50/80 transition-colors fade-in">

                <!-- Patient -->
                <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full <?= $avatarClass ?> flex items-center justify-center font-bold text-sm ring-2 ring-white shadow-sm shrink-0">
                            <?= $initials ?>
                        </div>
                        <div>
                            <p class="font-bold text-blue-900 text-sm"><?= $fullName ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($d['patient_mail']) ?></p>
                        </div>
                    </div>
                </td>

                <!-- Description -->
                <td class="px-6 py-5 max-w-xs">
                    <p class="text-sm text-slate-700 line-clamp-2"><?= htmlspecialchars($d['description']) ?></p>
                </td>

                <!-- Date -->
                <td class="px-6 py-5">
                    <span class="text-sm text-slate-600 font-medium whitespace-nowrap"><?= $dateStr ?></span>
                </td>

                <!-- Statut -->
                <td class="px-6 py-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold <?= $statutClass ?>">
                        <span class="material-symbols-outlined text-sm"><?= $statutIcon ?></span>
                        <?= $statutLabel ?>
                    </span>
                </td>

                <!-- Actions -->
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <?php if ($d['statut'] === 'en_attente'): ?>
                        <form method="POST" action="index.php?page=demandes&action=update_statut" class="inline">
                            <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                            <input type="hidden" name="statut" value="traitee"/>
                            <button type="submit"
                                    class="px-3 py-1.5 text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check</span>
                                Traiter
                            </button>
                        </form>
                        <form method="POST" action="index.php?page=demandes&action=update_statut" class="inline">
                            <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                            <input type="hidden" name="statut" value="refusee"/>
                            <button type="submit"
                                    class="px-3 py-1.5 text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors flex items-center gap-1"
                                    onclick="return confirm('Refuser cette demande ?')">
                                <span class="material-symbols-outlined text-sm">close</span>
                                Refuser
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="index.php?page=demandes&action=update_statut" class="inline">
                            <input type="hidden" name="id_demande" value="<?= (int)$d['id_demande'] ?>"/>
                            <input type="hidden" name="statut" value="en_attente"/>
                            <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">
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
        <?php endif ?>
    </div>

</div>
</main>

<script>
setTimeout(() => { document.getElementById('flash-msg')?.remove(); }, 4000);
</script>
</body>
</html>
