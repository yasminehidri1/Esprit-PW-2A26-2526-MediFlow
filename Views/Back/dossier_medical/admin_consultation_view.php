<?php // admin_consultation_view.php
$ant = $antecedents ?? []; $alg = $allergies ?? [];
?>
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="/integration/dossier/admin/consultations" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
      <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <h1 class="font-headline text-2xl font-extrabold text-blue-900">Détail Consultation</h1>
  </div>
  <div class="grid grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h3 class="font-headline font-bold text-blue-900 text-sm mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">person</span>Patient</h3>
      <p class="font-semibold text-on-surface"><?= htmlspecialchars($consultation['patient_prenom'].' '.$consultation['patient_nom']) ?></p>
      <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($consultation['mail_patient']??'') ?></p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <h3 class="font-headline font-bold text-blue-900 text-sm mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">stethoscope</span>Médecin</h3>
      <p class="font-semibold text-on-surface">Dr. <?= htmlspecialchars($consultation['medecin_prenom'].' '.$consultation['medecin_nom']) ?></p>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)] space-y-4">
    <h3 class="font-headline font-bold text-blue-900 text-sm flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">event_note</span>Informations</h3>
    <div class="grid grid-cols-2 gap-4">
      <div><p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Date</p><p class="font-medium text-on-surface text-sm"><?= date('d/m/Y H:i', strtotime($consultation['date_consultation'])) ?></p></div>
      <div><p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Type</p><p class="font-medium text-on-surface text-sm"><?= htmlspecialchars($consultation['type_consultation']??'—') ?></p></div>
      <?php foreach([['tension_arterielle','Tension','mmHg'],['rythme_cardiaque','Rythme','BPM'],['poids','Poids','kg'],['saturation_o2','SpO₂','%']] as [$k,$l,$u]): ?>
      <div><p class="text-xs font-bold uppercase text-on-surface-variant mb-1"><?=$l?></p><p class="font-medium text-sm"><?= htmlspecialchars($consultation[$k]??'—') ?> <span class="text-xs text-on-surface-variant"><?=$u?></span></p></div>
      <?php endforeach ?>
    </div>
    <?php if (!empty($consultation['diagnostic'])): ?><div class="border-t border-outline-variant/20 pt-4"><p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Diagnostic</p><p class="text-sm text-on-surface"><?= htmlspecialchars($consultation['diagnostic']) ?></p></div><?php endif ?>
    <?php if (!empty($consultation['compte_rendu'])): ?><div><p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Compte-rendu</p><p class="text-sm text-on-surface whitespace-pre-wrap"><?= htmlspecialchars($consultation['compte_rendu']) ?></p></div><?php endif ?>
  </div>
  <?php if (!empty($ant)): ?>
  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <h3 class="font-headline font-bold text-blue-900 text-sm mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">history_edu</span>Antécédents</h3>
    <div class="space-y-2"><?php foreach($ant as $a): ?><div class="bg-primary/5 rounded-lg p-3"><p class="text-xs font-bold text-primary"><?=htmlspecialchars($a['annee']??'').' · '.htmlspecialchars($a['titre']??'')?></p><?php if(!empty($a['description'])): ?><p class="text-xs text-on-surface-variant"><?=htmlspecialchars($a['description'])?></p><?php endif ?></div><?php endforeach ?></div>
  </div>
  <?php endif ?>
  <?php if (!empty($alg)): ?>
  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <h3 class="font-headline font-bold text-blue-900 text-sm mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-error text-lg">warning</span>Allergies</h3>
    <div class="flex flex-wrap gap-2"><?php $nc=['Élevé'=>'bg-error-container/40 text-error','Modéré'=>'bg-amber-100 text-amber-700','Faible'=>'bg-green-100 text-green-700']; foreach($alg as $a): ?><span class="px-2.5 py-1 rounded-full text-xs font-bold <?=$nc[$a['niveau']]??'bg-surface-container text-on-surface-variant'?>"><?=htmlspecialchars($a['nom']).' · '.htmlspecialchars($a['niveau'])?></span><?php endforeach ?></div>
  </div>
  <?php endif ?>
</div>
