<?php // admin_consultations_liste.php
$typeColors=['Consultation urgente'=>'bg-error-container/30 text-error','Suivi Traitement'=>'bg-amber-100 text-amber-700','Bilan Annuel'=>'bg-blue-50 text-primary','Contrôle annuel'=>'bg-blue-50 text-primary'];
?>
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Toutes les Consultations</h1>
      <p class="text-sm text-on-surface-variant"><?= $totalCount ?? 0 ?> consultation(s)</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <?php if (empty($consultations)): ?>
    <div class="py-16 text-center text-on-surface-variant"><span class="material-symbols-outlined text-5xl block mb-3 opacity-30">event_note</span><p class="font-semibold">Aucune consultation</p></div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-surface-container-low">
          <tr><?php foreach(['Patient','Médecin','Type','Date','Diagnostic','Actions'] as $h): ?>
          <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant"><?= $h ?></th>
          <?php endforeach ?></tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php foreach ($consultations as $c): ?>
          <?php $tc = $typeColors[$c['type_consultation']??''] ?? 'bg-surface-container text-on-surface-variant'; ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors">
            <td class="px-5 py-3.5 font-medium text-on-surface"><?= htmlspecialchars($c['patient_prenom'].' '.$c['patient_nom']) ?></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant">Dr. <?= htmlspecialchars($c['medecin_prenom'].' '.$c['medecin_nom']) ?></td>
            <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $tc ?>"><?= htmlspecialchars($c['type_consultation']??'—') ?></span></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant"><?= date('d/m/Y H:i', strtotime($c['date_consultation'])) ?></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant max-w-[200px] truncate"><?= htmlspecialchars(substr($c['diagnostic']??'—',0,50)) ?></td>
            <td class="px-5 py-3.5">
              <a href="/integration/dossier/admin/consultations/view?id=<?= $c['id_consultation'] ?>"
                 class="flex items-center gap-1 px-2.5 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors w-fit">
                <span class="material-symbols-outlined text-sm">visibility</span>Voir
              </a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <?php if (($totalPages??1) > 1): ?>
    <div class="px-6 py-4 border-t border-outline-variant/20 flex items-center justify-between">
      <p class="text-xs text-on-surface-variant">Page <?=$page?> / <?=$totalPages?></p>
      <div class="flex gap-1"><?php for($pg=1;$pg<=$totalPages;$pg++): ?>
      <a href="?p=<?=$pg?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?=$pg===$page?'bg-primary text-white':'bg-surface-container-low hover:bg-primary/10 text-on-surface'?>"><?=$pg?></a>
      <?php endfor ?></div>
    </div>
    <?php endif ?>
    <?php endif ?>
  </div>
</div>
