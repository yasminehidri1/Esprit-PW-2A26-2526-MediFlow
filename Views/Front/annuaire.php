<?php
/* ── Annuaire Médecins (Patient) ─── partial, rendered by layout.php ── */
?>
<div class="space-y-8">

  <!-- Hero Section -->
  <div class="text-center space-y-4 py-6">
    <h1 class="text-3xl font-black text-on-surface font-headline">Trouvez votre Médecin</h1>
    <p class="text-on-surface-variant max-w-lg mx-auto">Prenez rendez-vous en quelques clics avec nos spécialistes qualifiés.</p>
    
    <!-- Search Bar -->
    <form method="GET" action="/integration/rdv/annuaire" class="max-w-xl mx-auto mt-8 flex gap-2">
      <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
               placeholder="Rechercher par nom ou spécialité…"
               class="w-full pl-12 pr-4 py-3 bg-white border border-outline-variant rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all text-sm"/>
      </div>
      <button type="submit" class="px-8 py-3 bg-primary text-on-primary font-bold rounded-2xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 text-sm">
        Rechercher
      </button>
    </form>
  </div>

  <!-- Results Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($medecins)): ?>
      <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-outline-variant/30">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4 block">person_search</span>
        <p class="text-on-surface-variant">Aucun médecin ne correspond à votre recherche.</p>
      </div>
    <?php endif; ?>

    <?php foreach ($medecins as $med): 
      $initial = strtoupper(substr($med['prenom'], 0, 1));
    ?>
    <div class="bg-white rounded-3xl p-6 border border-outline-variant/20 shadow-[0_4px_20px_rgba(0,77,153,0.05)] hover:shadow-xl transition-all group border-b-4 border-b-transparent hover:border-b-primary">
      <div class="flex items-start justify-between mb-6">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-fixed to-primary-fixed/50 flex items-center justify-center text-primary font-black text-xl shadow-inner">
          <?= $initial ?>
        </div>
        <div class="flex flex-col items-end">
          <span class="px-3 py-1 rounded-full bg-primary-fixed/30 text-primary text-[10px] font-black uppercase tracking-widest">Spécialiste</span>
          <p class="text-[10px] text-on-surface-variant mt-2">Disponibilité : <span class="text-green-600 font-bold">Immédiate</span></p>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <h3 class="text-lg font-black text-on-surface font-headline leading-tight">Dr. <?= htmlspecialchars($med['prenom'] . ' ' . $med['nom']) ?></h3>
          <p class="text-xs text-on-surface-variant font-medium mt-1">Médecine Générale</p>
        </div>

        <div class="grid grid-cols-2 gap-2 py-4 border-y border-outline-variant/10">
          <div class="text-center">
            <p class="text-[10px] uppercase font-bold text-on-surface-variant/60 tracking-wider">Patients</p>
            <p class="font-black text-on-surface"><?= $med['nb_rdv'] ?? 0 ?>+</p>
          </div>
          <div class="text-center border-l border-outline-variant/10">
            <p class="text-[10px] uppercase font-bold text-on-surface-variant/60 tracking-wider">Note</p>
            <p class="font-black text-on-surface flex items-center justify-center gap-1">
              4.9 <span class="material-symbols-outlined text-[10px] text-amber-500" style="font-variation-settings:'FILL' 1">star</span>
            </p>
          </div>
        </div>

        <div class="space-y-2">
          <a href="/integration/rdv/reserver?medecin_id=<?= $med['id'] ?>" 
             class="block w-full py-3 bg-surface-container-high text-on-surface text-center rounded-xl font-bold text-xs hover:bg-primary hover:text-on-primary transition-all">
            Prendre RDV
          </a>
          <a href="/integration/rdv/medecin/planning?medecin_id=<?= $med['id'] ?>" 
             class="block w-full py-3 bg-white border border-outline-variant/40 text-on-surface-variant text-center rounded-xl font-bold text-xs hover:bg-surface-container transition-all">
            Voir le planning
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>