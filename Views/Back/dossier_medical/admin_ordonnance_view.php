<?php // admin_ordonnance_view.php
$meds = $medicaments ?? [];
$statColors=['active'=>'bg-tertiary-fixed/40 text-tertiary','archivee'=>'bg-surface-container text-on-surface-variant','annulee'=>'bg-error-container/30 text-error'];
$st = $ordonnance['statut'] ?? 'active';
?>
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/integration/dossier/admin/ordonnances" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
      </a>
      <div>
        <h1 class="font-headline text-2xl font-extrabold text-blue-900">Ordonnance <?= htmlspecialchars($ordonnance['numero_ordonnance']) ?></h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $statColors[$st]??'' ?>"><?= ucfirst($st) ?></span>
      </div>
    </div>
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">
      <span class="material-symbols-outlined text-sm">print</span>Imprimer
    </button>
  </div>

  <div class="grid grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Patient</p>
      <p class="font-semibold text-on-surface"><?= htmlspecialchars($ordonnance['patient_prenom'].' '.$ordonnance['patient_nom']) ?></p>
      <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($ordonnance['mail_patient']??'') ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Médecin · Date</p>
      <p class="font-semibold text-on-surface">Dr. <?= htmlspecialchars($ordonnance['medecin_prenom'].' '.$ordonnance['medecin_nom']) ?></p>
      <p class="text-xs text-on-surface-variant"><?= date('d/m/Y', strtotime($ordonnance['date_emission'])) ?></p>
    </div>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <h3 class="font-headline font-bold text-blue-900 mb-4 text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary">medication</span>Médicaments</h3>
    <?php if (empty($meds)): ?>
    <p class="text-on-surface-variant italic text-sm">Aucun médicament.</p>
    <?php else: ?>
    <div class="space-y-3">
      <?php foreach ($meds as $i => $m): ?>
      <div class="bg-surface-container-low rounded-xl p-4 border-l-4 border-primary/40">
        <div class="flex items-center gap-2 mb-2">
          <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center"><?=$i+1?></span>
          <p class="font-bold text-on-surface"><?= htmlspecialchars($m['nom']??'') ?> <span class="font-normal text-on-surface-variant text-xs"><?= htmlspecialchars($m['dosage']??'') ?></span></p>
          <?php if(!empty($m['categorie'])): ?><span class="ml-auto px-2 py-0.5 bg-blue-50 text-primary rounded-full text-[10px] font-bold"><?= htmlspecialchars($m['categorie']) ?></span><?php endif ?>
        </div>
        <div class="grid grid-cols-3 gap-2 text-xs text-on-surface-variant">
          <?php if(!empty($m['frequence'])): ?><span><b>Fréquence:</b> <?=htmlspecialchars($m['frequence'])?></span><?php endif ?>
          <?php if(!empty($m['duree'])): ?><span><b>Durée:</b> <?=htmlspecialchars($m['duree'])?></span><?php endif ?>
          <?php if(!empty($m['instructions'])): ?><span class="col-span-3"><b>Instructions:</b> <?=htmlspecialchars($m['instructions'])?></span><?php endif ?>
        </div>
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>

  <?php if(!empty($ordonnance['note_pharmacien'])): ?>
  <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-5">
    <h3 class="font-bold text-amber-700 mb-2 text-sm flex items-center gap-2"><span class="material-symbols-outlined text-lg">local_pharmacy</span>Note Pharmacien</h3>
    <p class="text-sm text-amber-800"><?= htmlspecialchars($ordonnance['note_pharmacien']) ?></p>
  </div>
  <?php endif ?>
</div>
<style>@media print { .ml-64, header, aside { display: none !important; } main { margin: 0 !important; } }</style>
