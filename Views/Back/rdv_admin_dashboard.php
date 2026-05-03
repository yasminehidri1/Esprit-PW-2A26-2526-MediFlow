<?php
/**
 * Vue : Views/Back/rdv_admin_dashboard.php
 * Tableau de bord admin — gestion globale des rendez-vous
 */

// Couleurs avatar (rotation)
$avatar_colors = ['#2563eb','#0d9488','#7c3aed','#db2777','#ea580c','#16a34a','#dc2626'];

// Préparer les données RDV par médecin en JSON pour la modale JS
$rdvs_par_medecin_json = [];
foreach ($medecins_rdv as $mid => $data) {
    $rdvs_par_medecin_json[$mid] = [
        'nom'  => $data['info']['nom'] . ' ' . $data['info']['prenom'],
        'rdvs' => array_map(function($r) {
            return [
                'prenom' => $r['patient_prenom'],
                'nom'    => $r['patient_nom'],
                'cin'    => $r['cin'],
                'genre'  => $r['genre'],
                'date'   => $r['date_rdv'],
                'heure'  => substr($r['heure_rdv'], 0, 5),
                'statut' => $r['statut'],
            ];
        }, $data['rdvs']),
    ];
}

// Construire la liste de TOUS les médecins triés alphabétiquement
$tous_medecins = [];
foreach ($liste_medecins as $i => $m) {
    $mid   = $m['id'];
    $color = $avatar_colors[$i % count($avatar_colors)];
    $nb    = isset($rdvs_par_medecin_json[$mid]) ? count($rdvs_par_medecin_json[$mid]['rdvs']) : 0;
    $tous_medecins[] = [
        'id'     => $mid,
        'nom'    => $m['nom'],
        'prenom' => $m['prenom'],
        'mail'   => $m['mail'] ?? '',
        'color'  => $color,
        'nb'     => $nb,
    ];
}
usort($tous_medecins, fn($a, $b) => strcmp($a['nom'], $b['nom']));

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>

<div class="space-y-8">

    <!-- ── En-tête ── -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface font-headline tracking-tight">
                Rendez-vous de la Clinique
            </h1>
            <p class="text-on-surface-variant text-sm mt-1">
                Gestion globale et suivi de toutes les consultations.
            </p>
        </div>
        <span class="flex items-center gap-2 px-4 py-2 bg-primary-fixed text-on-primary-fixed rounded-xl text-sm font-bold">
            <span class="material-symbols-outlined text-lg">medical_services</span>
            <?= $stats['nb_medecins'] ?> Médecin(s) actif(s)
        </span>
    </div>

    <!-- ── Statistiques rapides ── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">calendar_today</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Aujourd'hui</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_aujourdhui'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-tertiary/10 text-tertiary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Confirmés</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_confirmes'] ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-secondary/10 text-secondary rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">pending_actions</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">En attente</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_attente'] ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-error/10 text-error rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">cancel</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Annulés</p>
                <p class="text-2xl font-black text-on-surface"><?= $stats['nb_annules'] ?></p>
            </div>
        </div>
    </div>

    <!-- ── Filtres ── -->
    <form method="GET" action="<?= $base ?>/rdv/admin"
          class="bg-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-wrap items-center gap-6">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">
                Rechercher un patient
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant">search</span>
                <input type="text" name="search"
                       value="<?= htmlspecialchars($recherche) ?>"
                       placeholder="Nom ou CIN..."
                       class="w-full pl-11 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
        </div>
        <div class="w-48">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">Médecin</label>
            <select name="medecin"
                    class="w-full px-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="0">Tous les médecins</option>
                <?php foreach ($liste_medecins as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $filtre_medecin == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom'] . ' ' . $m['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-[10px] font-black text-on-surface-variant uppercase mb-1.5 ml-1">Statut</label>
            <select name="statut"
                    class="w-full px-4 py-2.5 bg-surface-container-lowest border border-outline-variant/50 rounded-2xl text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?= $filtre_statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="confirme"   <?= $filtre_statut === 'confirme'   ? 'selected' : '' ?>>Confirmé</option>
                <option value="annule"     <?= $filtre_statut === 'annule'     ? 'selected' : '' ?>>Annulé</option>
            </select>
        </div>
        <div class="pt-5">
            <button type="submit"
                    class="px-6 py-2.5 bg-primary text-on-primary rounded-2xl text-sm font-bold shadow-md hover:opacity-90 transition-opacity">
                Appliquer
            </button>
        </div>
    </form>

    <!-- ── Tableau des médecins ── -->
    <div class="bg-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-on-surface font-headline">Liste des médecins</h2>
            <span class="text-xs text-on-surface-variant">
                <?= count($tous_medecins) ?> médecin(s) — ordre alphabétique
            </span>
        </div>

        <?php if (empty($tous_medecins)): ?>
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-outline-variant/40 mb-4 block">event_busy</span>
                <h3 class="text-lg font-bold text-on-surface">Aucun médecin trouvé</h3>
                <p class="text-on-surface-variant text-sm mt-1">Ajustez vos filtres ou votre recherche.</p>
            </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest">
                        <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Médecin</th>
                        <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Email</th>
                        <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest text-center">Nb. RDV</th>
                        <th class="px-6 py-3 text-[10px] font-black text-on-surface-variant uppercase tracking-widest text-center">Patients</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php foreach ($tous_medecins as $med): ?>
                    <?php
                        $init  = strtoupper(substr($med['prenom'], 0, 1) . substr($med['nom'], 0, 1));
                        $idFmt = 'DR-' . str_pad($med['id'], 4, '0', STR_PAD_LEFT);
                    ?>
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                     style="background:<?= $med['color'] ?>">
                                    <?= $init ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">
                                        Dr. <?= htmlspecialchars($med['prenom'] . ' ' . $med['nom']) ?>
                                    </p>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wider font-medium">Médecin</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-on-surface-variant">
                                <?= htmlspecialchars($med['mail'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($med['nb'] > 0): ?>
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-black">
                                    <?= $med['nb'] ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-surface-container text-on-surface-variant rounded-full text-sm font-bold">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($med['nb'] > 0): ?>
                            <button
                                onclick="ouvrirPatients(<?= $med['id'] ?>, '<?= htmlspecialchars($med['prenom'].' '.$med['nom'], ENT_QUOTES) ?>', '<?= $med['color'] ?>', '<?= $init ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary rounded-xl text-xs font-bold hover:bg-primary hover:text-white transition-all"
                                title="Voir les rendez-vous">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Voir
                            </button>
                            <?php else: ?>
                            <span class="text-xs text-on-surface-variant italic">Aucun RDV</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ══════════════════════════════════════════════
     MODALE — RDV d'un médecin
════════════════════════════════════════════════ -->
<div id="modalRdv"
     class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">

        <!-- En-tête -->
        <div class="px-6 py-5 border-b border-outline-variant/20 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="modalAvatar"
                     class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                </div>
                <div>
                    <p id="modalNom" class="text-base font-extrabold text-on-surface font-headline"></p>
                    <p id="modalSous" class="text-xs text-on-surface-variant"></p>
                </div>
            </div>
            <button onclick="fermerModal()"
                    class="w-9 h-9 flex items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container hover:text-error transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Corps -->
        <div id="modalBody" class="overflow-y-auto flex-1 p-2"></div>

    </div>
</div>

<script>
const rdvsData = <?= json_encode($rdvs_par_medecin_json, JSON_UNESCAPED_UNICODE) ?>;

const moisFr  = ['','Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const joursFr = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];

function formatDate(str) {
    const d = new Date(str + 'T00:00:00');
    return joursFr[d.getDay()] + ' ' + String(d.getDate()).padStart(2,'0')
         + ' ' + moisFr[d.getMonth()+1] + ' ' + d.getFullYear();
}

function badgeHtml(statut) {
    const cfg = {
        'confirme'   : ['bg-green-100 text-green-800',   'Confirmé'],
        'en_attente' : ['bg-blue-100 text-blue-800',     'En attente'],
        'annule'     : ['bg-red-100 text-red-800',       'Annulé'],
    };
    const [cls, lbl] = cfg[statut] ?? ['bg-gray-100 text-gray-700', statut];
    return `<span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${cls}">${lbl}</span>`;
}

function ouvrirPatients(mid, nom, color, init) {
    document.getElementById('modalAvatar').textContent       = init;
    document.getElementById('modalAvatar').style.background  = color;
    document.getElementById('modalNom').textContent          = 'Dr. ' + nom;

    const data = rdvsData[mid];
    const rdvs = data ? data.rdvs : [];
    document.getElementById('modalSous').textContent = rdvs.length + ' rendez-vous au total';

    const body = document.getElementById('modalBody');

    if (!rdvs || rdvs.length === 0) {
        body.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">event_busy</span>
                <p class="text-gray-500 text-sm">Aucun rendez-vous pour ce médecin.</p>
            </div>`;
    } else {
        const rows = rdvs.map(r => {
            const patInit = ((r.prenom ?? ' ')[0] + (r.nom ?? ' ')[0]).toUpperCase();
            const genreLabel = r.genre === 'femme' ? 'Femme' : 'Homme';
            return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            ${patInit}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">${r.prenom} ${r.nom}</p>
                            <p class="text-[10px] text-gray-500">CIN : ${r.cin}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <p class="text-sm font-medium text-gray-800">${formatDate(r.date)}</p>
                    <p class="text-xs text-gray-500">${r.heure}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600">${genreLabel}</span>
                </td>
                <td class="px-4 py-3">
                    ${badgeHtml(r.statut)}
                </td>
            </tr>`;
        }).join('');

        body.innerHTML = `
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Patient</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Date &amp; Heure</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Genre</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">${rows}</tbody>
            </table>`;
    }

    const modal = document.getElementById('modalRdv');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function fermerModal() {
    const modal = document.getElementById('modalRdv');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('modalRdv').addEventListener('click', function(e) {
    if (e.target === this) fermerModal();
});
</script>