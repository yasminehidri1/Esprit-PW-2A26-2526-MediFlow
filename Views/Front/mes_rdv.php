<?php
/* ── Mes Rendez-vous (Patient) ─── partial, rendered by layout.php ── */
$mois_fr = ['','Janvier','Février','Mars','Avril','Mai','Juin',
            'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
?>
<div class="max-w-3xl mx-auto space-y-8">

  <!-- Page hero -->
  <div class="text-center space-y-4">
    <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-2xl flex items-center justify-center mx-auto shadow-lg">
      <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings:'FILL' 1">event_available</span>
    </div>
    <div>
      <h1 class="text-3xl font-black text-on-surface font-headline">Mes Rendez-vous</h1>
      <p class="text-on-surface-variant">Entrez votre numéro CIN pour retrouver tous vos rendez-vous.</p>
    </div>
  </div>

  <!-- Search Form / Auto-Detected Status -->
  <div class="bg-white rounded-3xl p-8 border border-outline-variant/20 shadow-sm">
    <?php if ($is_auto): ?>
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-tertiary-container rounded-full flex items-center justify-center text-on-tertiary-container">
          <span class="material-symbols-outlined">person_check</span>
        </div>
        <div>
          <h2 class="text-sm font-bold text-on-surface">Compte identifié</h2>
          <p class="text-xs text-on-surface-variant">Affichage automatique des rendez-vous pour le CIN <strong><?= htmlspecialchars($cin) ?></strong></p>
        </div>
      </div>
    <?php else: ?>
      <h2 class="text-sm font-bold text-on-surface mb-4">Rechercher mes rendez-vous</h2>
      <form method="POST" action="/integration/rdv/mes-rdv" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">id_card</span>
          <input type="text" name="cin" value="<?= htmlspecialchars($cin ?? '') ?>" maxlength="8" required
                 placeholder="Entrez votre CIN (8 chiffres)…"
                 class="w-full pl-12 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-2xl outline-none focus:ring-4 focus:ring-primary/10 transition-all text-sm"/>
        </div>
        <button type="submit" class="px-8 py-3 bg-primary text-on-primary font-bold rounded-2xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 text-sm">
          Rechercher
        </button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Search Results -->
  <div class="space-y-4">
    <?php if (empty($mes_rdv)): ?>
      <?php if ($cin): ?>
        <div class="bg-white rounded-2xl p-12 text-center border border-outline-variant/20 shadow-sm">
          <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4 block">event_busy</span>
          <h3 class="text-lg font-bold text-on-surface mb-2">Aucun rendez-vous trouvé</h3>
          <p class="text-on-surface-variant mb-6 text-sm">Aucun rendez-vous n'est associé au CIN <strong><?= htmlspecialchars($cin) ?></strong>.</p>
          <a href="/integration/rdv/annuaire" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
            Prendre un rendez-vous
          </a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="flex items-center justify-between mb-2">
        <h2 class="font-bold text-on-surface font-headline">Vos rendez-vous récents</h2>
        <span class="text-xs font-semibold text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
          <?= count($mes_rdv) ?> RDV trouvé<?= count($mes_rdv) > 1 ? 's' : '' ?>
        </span>
      </div>

      <div class="grid gap-4">
        <?php foreach ($mes_rdv as $rdv):
          $ts       = strtotime($rdv['date_rdv']);
          $jour_num = date('d', $ts);
          $mois     = $mois_fr[(int)date('m', $ts)];
          $annee    = date('Y', $ts);
          $heure    = date('H:i', strtotime($rdv['heure_rdv']));

          $sc = match($rdv['statut']) {
            'confirme' => 'bg-tertiary-fixed/40 text-on-tertiary-fixed',
            'annule'   => 'bg-error-container/40 text-on-error-container',
            default    => 'bg-amber-100 text-amber-800',
          };
          $sl = match($rdv['statut']) {
            'confirme' => 'Confirmé',
            'annule'   => 'Annulé',
            default    => 'En attente',
          };
        ?>
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.05)] flex items-center gap-5 hover:shadow-md transition-shadow">
          <!-- Date Box -->
          <div class="w-16 h-16 bg-primary-fixed/30 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
            <span class="text-2xl font-black text-primary leading-none"><?= $jour_num ?></span>
            <span class="text-[10px] font-bold text-primary uppercase mt-1"><?= substr($mois, 0, 3) ?></span>
          </div>

          <!-- Info -->
          <div class="flex-1 space-y-1.5">
            <div class="flex items-center justify-between">
              <p class="font-bold text-on-surface">Dr. <?= htmlspecialchars(($rdv['medecin_prenom'] ?? '') . ' ' . ($rdv['medecin_nom'] ?? 'Médecin')) ?></p>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $sc ?>"><?= $sl ?></span>
            </div>
            <div class="flex items-center gap-4 text-xs text-on-surface-variant">
              <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">schedule</span> <?= $heure ?>
              </span>
              <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">calendar_today</span> <?= $jour_num ?> <?= $mois ?> <?= $annee ?>
              </span>
            </div>
            <p class="text-[10px] text-on-surface-variant/60">Réf. RDV #<?= $rdv['id'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>