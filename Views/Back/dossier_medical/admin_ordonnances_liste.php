<?php // admin_ordonnances_liste.php
$statColors=['active'=>'bg-tertiary-fixed/40 text-tertiary','archivee'=>'bg-surface-container text-on-surface-variant','annulee'=>'bg-error-container/30 text-error'];
?>
<div class="space-y-6">
  <div><h1 class="font-headline text-2xl font-extrabold text-blue-900">Toutes les Ordonnances</h1><p class="text-sm text-on-surface-variant"><?= $totalCount??0 ?> ordonnance(s)</p></div>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <?php if (empty($ordonnances)): ?>
    <div class="py-16 text-center text-on-surface-variant"><span class="material-symbols-outlined text-5xl block mb-3 opacity-30">receipt_long</span><p class="font-semibold">Aucune ordonnance</p></div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-surface-container-low"><tr><?php foreach(['Numéro','Patient','Médecin','Date','Médicaments','Statut'] as $h): ?><th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant"><?=$h?></th><?php endforeach ?></tr></thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php foreach ($ordonnances as $o):
            $meds = json_decode($o['medicaments']??'[]', true) ?: [];
            $st = $o['statut'] ?? 'active';
          ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors">
            <td class="px-5 py-3.5 text-xs font-mono text-on-surface-variant"><?= htmlspecialchars($o['numero_ordonnance']) ?></td>
            <td class="px-5 py-3.5 font-medium text-on-surface"><?= htmlspecialchars($o['patient_prenom'].' '.$o['patient_nom']) ?></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant">Dr. <?= htmlspecialchars($o['medecin_prenom'].' '.$o['medecin_nom']) ?></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant"><?= date('d/m/Y', strtotime($o['date_emission'])) ?></td>
            <td class="px-5 py-3.5 text-xs text-on-surface-variant"><?= count($meds) ?> médicament(s)</td>
            <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $statColors[$st]??'' ?>"><?= ucfirst($st) ?></span></td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <?php if (($totalPages??1)>1): ?><div class="px-6 py-4 border-t border-outline-variant/20 flex items-center justify-between"><p class="text-xs text-on-surface-variant">Page <?=$page?> / <?=$totalPages?></p><div class="flex gap-1"><?php for($pg=1;$pg<=$totalPages;$pg++): ?><a href="?p=<?=$pg?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?=$pg===$page?'bg-primary text-white':'bg-surface-container-low hover:bg-primary/10 text-on-surface'?>"><?=$pg?></a><?php endfor ?></div></div><?php endif ?>
    <?php endif ?>
  </div>
</div>
