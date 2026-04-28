<?php /* statistiques.php — partial */ ?>
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-black font-headline text-on-surface">Statistiques</h1>
    <p class="text-sm text-on-surface-variant mt-1">Analyse de votre activité</p>
  </div>

  <!-- KPI Cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $kpis = [
      ['Total RDV',       $s['total']         ?? 0,  'calendar_month', 'from-primary to-primary-container'],
      ["Aujourd'hui",     $s['nb_aujourdhui'] ?? 0,  'today',          'from-amber-400 to-amber-500'],
      ['Taux confirmat.', $taux_conf.'%',             'check_circle',   'from-tertiary to-tertiary-container'],
      ["Taux annulation", $taux_ann.'%',              'cancel',         'from-error to-red-700'],
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

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Monthly chart -->
    <div class="bg-white rounded-2xl p-6 border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)]">
      <h2 class="font-bold font-headline text-on-surface mb-4">Évolution mensuelle</h2>
      <canvas id="chartMois" height="200"></canvas>
    </div>
    <!-- Genre breakdown -->
    <div class="bg-white rounded-2xl p-6 border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)]">
      <h2 class="font-bold font-headline text-on-surface mb-4">Répartition genre</h2>
      <canvas id="chartGenre" height="200"></canvas>
      <div class="flex justify-center gap-6 mt-4 text-sm">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-primary inline-block"></span> Hommes (<?= $pct_homme ?>%)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-pink-400 inline-block"></span> Femmes (<?= $pct_femme ?>%)</span>
      </div>
    </div>
  </div>

  <!-- Statut breakdown -->
  <div class="bg-white rounded-2xl p-6 border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)]">
    <h2 class="font-bold font-headline text-on-surface mb-4">Répartition par statut</h2>
    <div class="flex gap-6 flex-wrap">
      <?php
      $total = max(1, ($s['total'] ?? 1));
      $statuts = [
        ['En attente', $s['nb_attente']   ?? 0, '#f59e0b'],
        ['Confirmés',  $s['nb_confirmes'] ?? 0, '#005851'],
        ['Annulés',    $s['nb_annules']   ?? 0, '#ba1a1a'],
      ];
      foreach ($statuts as [$label, $nb, $color]):
        $pct = round($nb/$total*100);
      ?>
      <div class="flex-1 min-w-[120px]">
        <div class="flex justify-between text-xs mb-1">
          <span class="text-on-surface-variant"><?= $label ?></span>
          <span class="font-bold text-on-surface"><?= $nb ?> (<?= $pct ?>%)</span>
        </div>
        <div class="h-2.5 bg-surface-container rounded-full overflow-hidden">
          <div class="h-full rounded-full transition-all" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
  const moisLabels = <?= $json_mois_labels ?>;
  const moisData   = <?= $json_mois_data ?>;
  new Chart(document.getElementById('chartMois'), {
    type:'bar',
    data:{ labels:moisLabels, datasets:[{ label:'RDV', data:moisData,
      backgroundColor:'rgba(0,77,153,0.18)', borderColor:'#004d99', borderWidth:2, borderRadius:6 }]},
    options:{ plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
  });
  new Chart(document.getElementById('chartGenre'), {
    type:'doughnut',
    data:{ labels:['Hommes','Femmes'], datasets:[{ data:[<?= $nb_homme ?>,<?= $nb_femme ?>],
      backgroundColor:['#004d99','#f472b6'], borderWidth:0 }]},
    options:{ plugins:{ legend:{ display:false } }, cutout:'65%' }
  });
})();
</script>