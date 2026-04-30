<?php
// Views/Back/dossier_medical/patients_liste.php
// Partial: rendered inside layout.php — NO html/head/body tags
$search     = htmlspecialchars($_GET['q'] ?? '');
$totalToday = $stats['total_today'] ?? 0;
$termines   = $stats['termines']    ?? 0;
$enAttente  = $stats['en_attente']  ?? 0;
?>
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900 tracking-tight">Mes Patients</h1>
      <p class="text-sm text-on-surface-variant mt-0.5">Gérez votre liste de patients et leurs dossiers médicaux</p>
    </div>
    <a href="/integration/dossier/nouvelle-consultation"
       class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 hover:shadow-lg hover:-translate-y-0.5 transition-all">
      <span class="material-symbols-outlined text-lg">add</span>
      Nouvelle Consultation
    </a>
  </div>

  <!-- Stat cards -->
  <div class="grid grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)] flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
        <span class="material-symbols-outlined text-primary text-2xl">groups</span>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total Patients</p>
        <p class="text-3xl font-extrabold text-blue-900"><?= $totalCount ?? 0 ?></p>
      </div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)] flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-tertiary/10 flex items-center justify-center">
        <span class="material-symbols-outlined text-tertiary text-2xl">today</span>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Consultations Aujourd'hui</p>
        <p class="text-3xl font-extrabold text-blue-900"><?= $totalToday ?></p>
      </div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)] flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-amber-500 text-2xl">pending</span>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">En Attente</p>
        <p class="text-3xl font-extrabold text-blue-900"><?= $enAttente ?></p>
      </div>
    </div>
  </div>

  <!-- Search + Table -->
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-4">
      <form method="GET" action="/integration/dossier/patients" class="flex items-center gap-2 bg-surface-container-low border border-outline-variant/20 rounded-xl px-4 py-2 w-full max-w-sm">
        <span class="material-symbols-outlined text-on-surface-variant text-base">search</span>
        <input type="text" name="q" value="<?= $search ?>" placeholder="Rechercher un patient..."
               class="bg-transparent text-sm w-full outline-none text-on-surface placeholder:text-on-surface-variant/60"/>
      </form>
      <span class="ml-auto text-xs text-on-surface-variant"><?= count($patients ?? []) ?> résultats</span>
    </div>

    <?php if (empty($patients)): ?>
    <div class="py-16 text-center text-on-surface-variant">
      <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">person_search</span>
      <p class="font-semibold">Aucun patient trouvé</p>
      <p class="text-sm mt-1">Essayez un autre terme ou ajoutez votre première consultation.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-surface-container-low">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Patient</th>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Contact</th>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Dernière visite</th>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Consultations</th>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php $colors = ['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700']; ?>
          <?php foreach ($patients as $i => $p): ?>
          <?php $initials = strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1)); ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full <?= $colors[$i % count($colors)] ?> flex items-center justify-center text-xs font-bold shrink-0">
                  <?= $initials ?>
                </div>
                <div>
                  <p class="font-semibold text-on-surface"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></p>
                  <p class="text-[11px] text-on-surface-variant">#MF-<?= str_pad($p['id_PK'],5,'0',STR_PAD_LEFT) ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-on-surface-variant text-xs"><?= htmlspecialchars($p['mail']) ?></td>
            <td class="px-6 py-4 text-xs text-on-surface-variant">
              <?= $p['derniere_visite'] ? date('d/m/Y', strtotime($p['derniere_visite'])) : '<span class="italic">Jamais</span>' ?>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                <?= (int)$p['nb_consultations'] ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="/integration/dossier/view?patient_id=<?= $p['id_PK'] ?>"
                   class="flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors">
                  <span class="material-symbols-outlined text-sm">folder_open</span>
                  Dossier
                </a>
                <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $p['id_PK'] ?>"
                   class="flex items-center gap-1 px-3 py-1.5 bg-tertiary/10 text-tertiary rounded-lg text-xs font-semibold hover:bg-tertiary/20 transition-colors">
                  <span class="material-symbols-outlined text-sm">add_circle</span>
                  Consult.
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="px-6 py-4 border-t border-outline-variant/20 flex items-center justify-between">
      <p class="text-xs text-on-surface-variant">Page <?= $page ?> sur <?= $totalPages ?></p>
      <div class="flex gap-2">
        <?php for ($pg = 1; $pg <= $totalPages; $pg++): ?>
        <a href="/integration/dossier/patients?p=<?= $pg ?><?= $search ? '&q='.urlencode($search) : '' ?>"
           class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold transition-colors <?= $pg === $page ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface hover:bg-primary/10' ?>">
          <?= $pg ?>
        </a>
        <?php endfor ?>
      </div>
    </div>
    <?php endif ?>
    <?php endif ?>
  </div>
</div>
