<?php
// Views/Back/dossier_medical/admin_dashboard.php — Admin Dossier Médical dashboard
$cStats = $consultationStats ?? []; $pStats = $prescriptionStats ?? []; $dStats = $doctorStats ?? [];
?>
<div class="space-y-6">
  <div>
    <h1 class="font-headline text-2xl font-extrabold text-blue-900">Tableau de Bord — Dossier Médical</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">Vue d'ensemble des consultations et ordonnances</p>
  </div>

  <!-- Stat cards -->
  <div class="grid grid-cols-4 gap-5">
    <?php $cards=[
      ['icon'=>'group','label'=>'Médecins','value'=>$dStats['total_doctors']??0,'color'=>'text-primary','bg'=>'bg-primary/10'],
      ['icon'=>'groups','label'=>'Patients','value'=>$totalPatients??0,'color'=>'text-tertiary','bg'=>'bg-tertiary/10'],
      ['icon'=>'event_note','label'=>'Consultations aujourd\'hui','value'=>$cStats['active_count']??0,'color'=>'text-amber-600','bg'=>'bg-amber-50'],
      ['icon'=>'receipt_long','label'=>'Ordonnances totales','value'=>$pStats['total_prescriptions']??0,'color'=>'text-violet-600','bg'=>'bg-violet-50'],
    ]; ?>
    <?php foreach($cards as $c): ?>
    <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)] flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl <?= $c['bg'] ?> flex items-center justify-center">
        <span class="material-symbols-outlined <?= $c['color'] ?> text-2xl"><?= $c['icon'] ?></span>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"><?= $c['label'] ?></p>
        <p class="text-3xl font-extrabold text-blue-900"><?= $c['value'] ?></p>
      </div>
    </div>
    <?php endforeach ?>
  </div>

  <div class="grid grid-cols-12 gap-6">
    <!-- Recent consultations -->
    <div class="col-span-8 bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
      <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
        <h3 class="font-headline font-bold text-blue-900 flex items-center gap-2 text-sm">
          <span class="material-symbols-outlined text-primary text-lg">history</span>Consultations Récentes
        </h3>
        <a href="/integration/dossier/admin/consultations" class="text-xs text-primary font-semibold hover:underline">Voir tout →</a>
      </div>
      <?php if (empty($recentConsultations)): ?>
      <p class="p-8 text-sm text-on-surface-variant text-center italic">Aucune consultation.</p>
      <?php else: ?>
      <div class="divide-y divide-outline-variant/10">
        <?php foreach ($recentConsultations as $c): ?>
        <div class="px-6 py-3.5 hover:bg-surface-container-low/40 transition-colors flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($c['patient_prenom'].' '.$c['patient_nom']) ?></p>
            <p class="text-xs text-on-surface-variant">Dr. <?= htmlspecialchars($c['medecin_prenom'].' '.$c['medecin_nom']) ?> · <?= htmlspecialchars($c['type_consultation'] ?? '') ?></p>
          </div>
          <div class="text-right">
            <p class="text-xs text-on-surface-variant"><?= date('d/m/Y', strtotime($c['date_consultation'])) ?></p>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $c['urgence']==='Urgent'?'bg-error-container/30 text-error':'bg-surface-container text-on-surface-variant' ?>"><?= $c['urgence'] ?></span>
          </div>
        </div>
        <?php endforeach ?>
      </div>
      <?php endif ?>
    </div>

    <!-- Right column -->
    <div class="col-span-4 space-y-5">
      <!-- Top doctors -->
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] p-5">
        <h3 class="font-headline font-bold text-blue-900 mb-3 text-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-lg">star</span>Top Médecins
        </h3>
        <?php foreach (array_slice($topDoctors??[],0,5) as $i => $d): ?>
        <div class="flex items-center gap-3 py-2 border-b border-outline-variant/10 last:border-0">
          <span class="text-xs font-bold text-on-surface-variant w-4">#<?= $i+1 ?></span>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-on-surface truncate">Dr. <?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></p>
          </div>
          <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full"><?= $d['nb_patients'] ?> pts</span>
        </div>
        <?php endforeach ?>
      </div>

      <!-- Quick links -->
      <div class="bg-gradient-to-br from-primary to-primary-container rounded-2xl p-5 text-white">
        <h3 class="font-headline font-bold mb-3 text-sm">Gestion Rapide</h3>
        <div class="space-y-2">
          <?php $links=[['Médecins','/integration/dossier/admin/doctors','group'],['Consultations','/integration/dossier/admin/consultations','event_note'],['Ordonnances','/integration/dossier/admin/ordonnances','receipt_long']]; ?>
          <?php foreach($links as [$lbl,$url,$icon]): ?>
          <a href="<?= $url ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-sm font-medium">
            <span class="material-symbols-outlined text-base"><?= $icon ?></span><?= $lbl ?>
          </a>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  </div>
</div>
