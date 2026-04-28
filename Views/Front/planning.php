<?php
/* ── Planning Médecin (Vue Patient) ─── partial, rendered by layout.php ── */
$noms_jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];
$noms_mois  = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$mois_court = ['','Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
$aujourd_hui = date('Y-m-d');
?>
<div class="space-y-8">

  <!-- Doctor Hero -->
  <div class="bg-gradient-to-r from-primary to-primary-dark rounded-3xl p-8 text-on-primary flex flex-col md:flex-row items-center justify-between gap-6">
    <div class="flex items-center gap-6">
      <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center font-black text-2xl border border-white/30">
        <?= strtoupper(substr($medecin['prenom'] ?? 'M', 0, 1)) ?>
      </div>
      <div>
        <h1 class="text-2xl font-black font-headline">Dr. <?= htmlspecialchars(($medecin['prenom']??'') . ' ' . ($medecin['nom']??'')) ?></h1>
        <p class="text-sm opacity-80">Sélectionnez un créneau vert pour votre consultation.</p>
      </div>
    </div>
    <div class="flex gap-4 text-xs font-bold">
      <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-tertiary-fixed"></span> Disponible</div>
      <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-white/30"></span> Occupé</div>
      <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-amber-400"></span> Indisponible</div>
    </div>
  </div>

  <!-- Week Navigation -->
  <div class="flex items-center justify-between bg-white rounded-2xl p-4 border border-outline-variant/20 shadow-sm">
    <div class="space-y-0.5">
      <h2 class="font-bold text-on-surface">Semaine du <?= $jours[0]->format('d') ?> au <?= end($jours)->format('d') ?> <?= $noms_mois[(int)end($jours)->format('m')] ?></h2>
      <p class="text-[10px] text-on-surface-variant uppercase font-black tracking-widest">Consultations : 08h00 — 17h00</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?= $url_prec ?>" class="p-2 rounded-xl border border-outline-variant/30 hover:bg-surface-container transition-all">
        <span class="material-symbols-outlined">chevron_left</span>
      </a>
      <a href="/integration/rdv/medecin/planning?medecin_id=<?= $medecin['id'] ?>" class="px-4 py-2 bg-surface-container text-on-surface text-xs font-bold rounded-xl hover:bg-outline-variant/20 transition-all">
        Aujourd'hui
      </a>
      <a href="<?= $url_suiv ?>" class="p-2 rounded-xl border border-outline-variant/30 hover:bg-surface-container transition-all">
        <span class="material-symbols-outlined">chevron_right</span>
      </a>
    </div>
  </div>

  <!-- Calendar Grid -->
  <div class="bg-white rounded-3xl border border-outline-variant/20 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="grid grid-cols-[80px_repeat(5,1fr)] bg-surface-container-low border-b border-outline-variant/20">
      <div class="p-4 border-r border-outline-variant/10"></div>
      <?php foreach ($jours as $i => $jour): 
        $ds = $jour->format('Y-m-d');
        $isAuj = ($ds === $aujourd_hui);
      ?>
      <div class="p-4 text-center border-l border-outline-variant/10 <?= $isAuj ? 'bg-primary/5' : '' ?>">
        <p class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60"><?= $noms_jours[$i] ?></p>
        <p class="text-xl font-black font-headline <?= $isAuj ? 'text-primary' : 'text-on-surface' ?>"><?= $jour->format('d') ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Body -->
    <div class="divide-y divide-outline-variant/10">
      <?php 
      $creneaux = [];
      for($h=8;$h<17;$h++){ $creneaux[] = sprintf('%02d:00',$h); $creneaux[] = sprintf('%02d:30',$h); }
      
      foreach ($creneaux as $c): 
        $isHeurePleine = str_ends_with($c, ':00');
      ?>
      <div class="grid grid-cols-[80px_repeat(5,1fr)] <?= $isHeurePleine ? 'bg-surface-container-lowest/30' : '' ?>">
        <div class="p-2 text-right text-[10px] font-black text-on-surface-variant/40 pr-4 flex items-center justify-end">
          <?= $isHeurePleine ? $c : '·' ?>
        </div>
        <?php foreach ($jours as $jour): 
          $ds = $jour->format('Y-m-d');
          $estPasse = ($ds < $aujourd_hui) || ($ds === $aujourd_hui && strtotime("$ds $c:00") <= time());
          
          // Simulation simple d'état (à remplacer par la logique du controller/model)
          $etat = 'libre'; 
          if(isset($pris[$ds][$c])) $etat = 'pris';
          if(isset($bloque[$ds][$c])) $etat = 'bloque';
        ?>
        <div class="p-1 border-l border-outline-variant/10 min-h-[44px]">
          <?php if ($estPasse): ?>
            <div class="h-full rounded-lg bg-surface-container-lowest/50"></div>
          <?php elseif ($etat === 'libre'): ?>
            <a href="/integration/rdv/reserver?medecin_id=<?= $medecin['id'] ?>&date_rdv=<?= $ds ?>&heure_rdv=<?= $c ?>" 
               class="h-full w-full rounded-lg bg-tertiary-fixed text-on-tertiary-fixed flex items-center justify-center text-[11px] font-black hover:scale-[1.02] hover:shadow-md transition-all">
              <?= $c ?>
            </a>
          <?php elseif ($etat === 'pris'): ?>
            <div class="h-full w-full rounded-lg bg-surface-container text-on-surface-variant/40 flex items-center justify-center text-[10px] font-bold">
              Pris
            </div>
          <?php else: ?>
            <div class="h-full w-full rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-[9px] font-bold px-1 text-center leading-tight">
              Indisponible
            </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>