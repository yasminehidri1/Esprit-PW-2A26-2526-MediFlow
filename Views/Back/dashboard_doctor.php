<?php
/* ── Doctor Dashboard ─── partial, rendered by layout.php ── */
?>
<div class="space-y-6">

  <!-- Header -->
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="text-2xl font-black text-on-surface font-headline">Mes Rendez-vous</h1>
      <p class="text-sm text-on-surface-variant mt-1">Total : <?= $total_rdv ?> rendez-vous</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <?php foreach ([''=>'Tous','en_attente'=>'En attente','confirme'=>'Confirmés','annule'=>'Annulés'] as $val=>$label): ?>
      <a href="/integration/rdv/dashboard<?= $val?'?statut='.$val:'' ?>"
         class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                <?= $filtre===$val ? 'bg-primary text-on-primary shadow-sm' : 'bg-white text-on-surface-variant border border-outline-variant/30 hover:bg-primary-fixed/30' ?>">
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- KPI row -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $kpis = [
      ['Total',        $stats['total']         ?? 0, 'calendar_month',  'from-primary to-primary-container'],
      ["Aujourd'hui",  $stats['nb_aujourdhui'] ?? 0, 'today',           'from-amber-400 to-amber-500'],
      ['Confirmés',    $stats['nb_confirmes']  ?? 0, 'check_circle',    'from-tertiary to-tertiary-container'],
      ['Taux confir.', $taux_conf.'%',               'percent',         'from-secondary to-secondary-container'],
    ];
    foreach ($kpis as [$l,$v,$ic,$gr]): ?>
    <div class="bg-white rounded-2xl p-5 border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)]">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?= $gr ?> flex items-center justify-center mb-3">
        <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1"><?= $ic ?></span>
      </div>
      <p class="text-2xl font-black font-headline text-on-surface"><?= $v ?></p>
      <p class="text-xs text-on-surface-variant mt-0.5"><?= $l ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Prochain RDV -->
  <?php if ($prochain): ?>
  <div class="bg-gradient-to-r from-primary to-primary-container rounded-2xl p-5 text-on-primary flex items-center gap-5">
    <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL' 1">upcoming</span>
    <div>
      <p class="text-xs font-semibold opacity-80 uppercase tracking-wider">Prochain rendez-vous</p>
      <p class="text-lg font-black font-headline"><?= htmlspecialchars($prochain['patient_prenom'].' '.$prochain['patient_nom']) ?></p>
      <p class="text-sm opacity-90"><?= date('d/m/Y', strtotime($prochain['date_rdv'])) ?> à <?= substr($prochain['heure_rdv'],0,5) ?></p>
    </div>
  </div>
  <?php endif; ?>

  <!-- RDV Table -->
  <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)] overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant/20">
      <h2 class="font-bold text-on-surface font-headline">Liste des rendez-vous</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Patient</th>
            <th class="px-5 py-3 text-left">CIN</th>
            <th class="px-5 py-3 text-left">Date</th>
            <th class="px-5 py-3 text-left">Heure</th>
            <th class="px-5 py-3 text-left">Genre</th>
            <th class="px-5 py-3 text-left">Statut</th>
            <th class="px-5 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php if (empty($rendez_vous)): ?>
          <tr><td colspan="7" class="px-5 py-10 text-center text-on-surface-variant">Aucun rendez-vous trouvé.</td></tr>
          <?php endif; ?>
          <?php foreach ($rendez_vous as $rdv):
            $sc = match($rdv['statut']) {
              'confirme' => 'bg-tertiary-fixed/40 text-on-tertiary-fixed',
              'annule'   => 'bg-error-container/40 text-on-error-container',
              default    => 'bg-secondary-fixed/40 text-on-secondary-fixed',
            };
            $sl = match($rdv['statut']) {
              'confirme' => 'Confirmé', 'annule' => 'Annulé', default => 'En attente',
            };
          ?>
          <tr class="hover:bg-surface-container-lowest/60 transition-colors">
            <td class="px-5 py-3.5 font-medium text-on-surface">
              <?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?>
            </td>
            <td class="px-5 py-3.5 text-on-surface-variant font-mono text-xs"><?= htmlspecialchars($rdv['cin']) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant"><?= substr($rdv['heure_rdv'],0,5) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant capitalize"><?= htmlspecialchars($rdv['genre']) ?></td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $sc ?>"><?= $sl ?></span>
            </td>
            <td class="px-5 py-3.5 flex items-center gap-2">
              <a href="/integration/rdv/modifier?id=<?= $rdv['id'] ?>"
                 class="p-1.5 rounded-lg text-primary hover:bg-primary-fixed/30 transition-colors" title="Modifier">
                <span class="material-symbols-outlined text-base">edit</span>
              </a>
              <a href="/integration/rdv/dashboard?supprimer=<?= $rdv['id'] ?>&<?= http_build_query(['statut'=>$filtre,'page'=>$page]) ?>"
                 onclick="return confirm('Supprimer ce RDV ?')"
                 class="p-1.5 rounded-lg text-error hover:bg-error-container/30 transition-colors" title="Supprimer">
                <span class="material-symbols-outlined text-base">delete</span>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-6 py-4 border-t border-outline-variant/20 flex items-center justify-between text-sm">
      <span class="text-on-surface-variant">Page <?= $page ?> / <?= $total_pages ?></span>
      <div class="flex gap-1">
        <?php for ($i=1; $i<=$total_pages; $i++): ?>
        <a href="/integration/rdv/dashboard?page=<?= $i ?><?= $filtre?'&statut='.$filtre:'' ?>"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors <?= $i===$page?'bg-primary text-on-primary':'bg-surface-container text-on-surface-variant hover:bg-primary-fixed/30' ?>">
          <?= $i ?>
        </a>
        <?php endfor; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>