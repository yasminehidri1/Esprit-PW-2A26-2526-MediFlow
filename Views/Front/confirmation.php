<?php
/* ── Confirmation RDV (Patient) ─── partial, rendered by layout.php ── */
?>
<div class="max-w-xl mx-auto py-12">
  <div class="bg-white rounded-3xl p-10 border border-outline-variant/20 shadow-2xl shadow-primary/5 text-center">
    
    <div class="w-20 h-20 bg-tertiary-container rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce">
      <span class="material-symbols-outlined text-tertiary text-4xl" style="font-variation-settings:'FILL' 1">check_circle</span>
    </div>

    <h1 class="text-3xl font-black text-on-surface font-headline mb-4">Demande envoyée !</h1>
    <p class="text-on-surface-variant mb-10 px-4">Votre rendez-vous est en attente de confirmation. Vous recevrez une notification dès que le médecin aura validé votre créneau.</p>

    <!-- Recap Card -->
    <div class="bg-surface-container-low rounded-2xl p-6 text-left border border-outline-variant/10 mb-10 space-y-4">
      <div class="flex justify-between items-center border-b border-outline-variant/5 pb-3">
        <span class="text-xs font-bold text-on-surface-variant/60 uppercase">Référence</span>
        <span class="text-sm font-black text-on-surface font-mono">#<?= $rdv['id'] ?></span>
      </div>
      <div class="flex justify-between items-center border-b border-outline-variant/5 pb-3">
        <span class="text-xs font-bold text-on-surface-variant/60 uppercase">Médecin</span>
        <span class="text-sm font-bold text-on-surface">Dr. <?= htmlspecialchars(($rdv['medecin_prenom'] ?? '') . ' ' . ($rdv['medecin_nom'] ?? 'Médecin')) ?></span>
      </div>
      <div class="flex justify-between items-center border-b border-outline-variant/5 pb-3">
        <span class="text-xs font-bold text-on-surface-variant/60 uppercase">Date & Heure</span>
        <span class="text-sm font-bold text-on-surface"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?> à <?= substr($rdv['heure_rdv'], 0, 5) ?></span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-xs font-bold text-on-surface-variant/60 uppercase">Statut initial</span>
        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider">En attente</span>
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <a href="/integration/rdv/mes-rdv" class="w-full py-4 bg-primary text-on-primary font-black rounded-2xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 text-sm uppercase tracking-wide">
        Voir mes rendez-vous
      </a>
      <a href="/integration/rdv/annuaire" class="w-full py-4 bg-white border border-outline-variant text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container transition-all text-sm uppercase tracking-wide">
        Retour à l'annuaire
      </a>
    </div>

  </div>
</div>