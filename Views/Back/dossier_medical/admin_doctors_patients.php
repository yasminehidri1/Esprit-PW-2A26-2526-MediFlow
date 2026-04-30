<?php // admin_doctors_patients.php — patients of a specific doctor (admin view) ?>
<div class="space-y-6">
  <div class="flex items-center gap-4">
    <a href="/integration/dossier/admin/doctors" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
      <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Patients de Dr. <?= htmlspecialchars($doctor['prenom'].' '.$doctor['nom']) ?></h1>
      <p class="text-sm text-on-surface-variant mt-0.5"><?= count($patients) ?> patient(s)</p>
    </div>
  </div>
  <?php if (empty($patients)): ?>
  <div class="bg-white rounded-2xl p-16 text-center shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">people</span>
    <p class="font-semibold text-on-surface-variant">Aucun patient pour ce médecin</p>
  </div>
  <?php else: ?>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-surface-container-low">
          <tr>
            <?php foreach(['Patient','Email','Consultations','Ordonnances','Dernière visite'] as $h): ?>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant"><?= $h ?></th>
            <?php endforeach ?>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php $cols=['bg-blue-100 text-blue-700','bg-teal-100 text-teal-700','bg-violet-100 text-violet-700']; ?>
          <?php foreach ($patients as $i => $p): ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full <?= $cols[$i%3] ?> flex items-center justify-center text-xs font-bold shrink-0">
                  <?= strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1)) ?>
                </div>
                <p class="font-semibold text-on-surface"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></p>
              </div>
            </td>
            <td class="px-6 py-4 text-xs text-on-surface-variant"><?= htmlspecialchars($p['mail']) ?></td>
            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary"><?= (int)$p['nb_consultations'] ?></span></td>
            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-tertiary/10 text-tertiary"><?= (int)$p['nb_ordonnances'] ?></span></td>
            <td class="px-6 py-4 text-xs text-on-surface-variant"><?= $p['last_consultation'] ? date('d/m/Y', strtotime($p['last_consultation'])) : '—' ?></td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif ?>
</div>
