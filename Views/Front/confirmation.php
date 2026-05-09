<?php
/* ── Confirmation RDV (Patient) ─── partial, rendered by layout.php ── */
?>

<style>
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes scaleIn {
    from { opacity: 0; transform: scale(0.6); }
    to   { opacity: 1; transform: scale(1); }
  }
  @keyframes pulse-ring {
    0%   { transform: scale(1);   opacity: 0.4; }
    70%  { transform: scale(1.6); opacity: 0; }
    100% { transform: scale(1.6); opacity: 0; }
  }
  .anim-fade-up   { animation: fadeInUp 0.5s cubic-bezier(.22,1,.36,1) both; }
  .anim-scale-in  { animation: scaleIn  0.45s cubic-bezier(.34,1.56,.64,1) both; }
  .pulse-ring::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: rgba(0, 88, 81, 0.25);
    animation: pulse-ring 2s ease-out infinite;
  }
</style>

<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-surface">
  <div class="w-full max-w-lg">

    <!-- ── Carte principale ── -->
    <div class="bg-white rounded-3xl shadow-2xl shadow-primary/10 border border-outline-variant/15 overflow-hidden anim-fade-up">

      <!-- Bandeau header décoratif -->
      <div class="relative bg-gradient-to-br from-primary to-secondary h-3 w-full"></div>

      <div class="px-8 py-10">

        <!-- Icône succès avec pulse -->
        <div class="flex justify-center mb-8 anim-scale-in" style="animation-delay:.1s">
          <div class="relative w-24 h-24 pulse-ring">
            <div class="w-24 h-24 rounded-full bg-tertiary-container flex items-center justify-center shadow-lg shadow-primary/20">
              <span class="material-symbols-outlined text-5xl text-tertiary" style="font-variation-settings:'FILL' 1">check_circle</span>
            </div>
          </div>
        </div>

        <!-- Titre -->
        <div class="text-center mb-8 anim-fade-up" style="animation-delay:.18s">
          <h1 class="text-3xl font-black text-on-surface font-headline mb-2 tracking-tight">Demande envoyée !</h1>
          <p class="text-on-surface-variant text-sm leading-relaxed max-w-sm mx-auto">
            Votre rendez-vous est <strong class="text-on-surface">en attente de confirmation</strong>. Vous recevrez une notification dès que le médecin aura validé votre créneau.
          </p>
        </div>

        <!-- ── Recap Card ── -->
        <div class="bg-surface-container-low rounded-2xl border border-outline-variant/10 overflow-hidden mb-7 anim-fade-up" style="animation-delay:.26s">

          <!-- En-tête de la carte récap -->
          <div class="flex items-center gap-2 px-5 py-3 bg-surface-container border-b border-outline-variant/10">
            <span class="material-symbols-outlined text-primary text-base" style="font-variation-settings:'FILL' 1">receipt_long</span>
            <span class="text-xs font-black text-on-surface-variant uppercase tracking-widest">Récapitulatif</span>
          </div>

          <div class="divide-y divide-outline-variant/10">

            <!-- Référence -->
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">tag</span>
                Référence
              </div>
              <span class="font-mono font-black text-sm text-on-surface bg-surface-container px-3 py-1 rounded-lg border border-outline-variant/20">
                #<?= htmlspecialchars($rdv['id'] ?? '—') ?>
              </span>
            </div>

            <!-- Médecin -->
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">stethoscope</span>
                Médecin
              </div>
              <span class="text-sm font-bold text-on-surface">
                Dr.&nbsp;<?= htmlspecialchars(trim(($rdv['medecin_prenom'] ?? '') . ' ' . ($rdv['medecin_nom'] ?? 'Médecin'))) ?>
              </span>
            </div>

            <!-- Spécialité (si disponible) -->
            <?php if (!empty($rdv['medecin_specialite'])): ?>
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">local_hospital</span>
                Spécialité
              </div>
              <span class="text-sm font-semibold text-on-surface-variant">
                <?= htmlspecialchars($rdv['medecin_specialite']) ?>
              </span>
            </div>
            <?php endif; ?>

            <!-- Date & Heure -->
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">calendar_month</span>
                Date & Heure
              </div>
              <div class="text-right">
                <span class="text-sm font-black text-on-surface">
                  <?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?>
                </span>
                <span class="text-xs text-on-surface-variant ml-1.5">
                  à <?= substr($rdv['heure_rdv'], 0, 5) ?>
                </span>
              </div>
            </div>

            <!-- Patient -->
            <?php if (!empty($rdv['patient_prenom']) || !empty($rdv['patient_nom'])): ?>
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">person</span>
                Patient
              </div>
              <span class="text-sm font-semibold text-on-surface">
                <?= htmlspecialchars(trim(($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''))) ?>
              </span>
            </div>
            <?php endif; ?>

            <!-- Statut -->
            <div class="flex items-center justify-between px-5 py-3.5">
              <div class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/60 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm text-on-surface-variant/50">schedule</span>
                Statut
              </div>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                En attente
              </span>
            </div>

          </div>
        </div>

        <!-- ── Info rappel email ── -->
        <?php if (!empty($rdv['patient_email'])): ?>
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3.5 mb-7 anim-fade-up" style="animation-delay:.32s">
          <span class="material-symbols-outlined text-blue-500 text-lg mt-0.5" style="font-variation-settings:'FILL' 1">mark_email_read</span>
          <p class="text-xs text-blue-700 leading-relaxed">
            Un email de confirmation vous sera envoyé à
            <strong class="font-bold"><?= htmlspecialchars($rdv['patient_email']) ?></strong>
            dès que le médecin aura validé votre rendez-vous.
          </p>
        </div>
        <?php endif; ?>

        <!-- ── Boutons d'action ── -->
        <div class="flex flex-col gap-3 anim-fade-up" style="animation-delay:.38s">

          <!-- Ajouter à l'agenda -->
          <a href="/integration/rdv/ical?rdv_id=<?= (int)($rdv['id'] ?? 0) ?>"
             class="group w-full py-4 bg-surface-container border border-outline-variant text-on-surface font-bold rounded-2xl hover:bg-surface-container-high hover:border-primary/30 transition-all text-sm uppercase tracking-wide flex items-center justify-center gap-2.5">
            <span class="material-symbols-outlined text-primary text-base transition-transform group-hover:scale-110" style="font-variation-settings:'FILL' 1">calendar_add_on</span>
            Ajouter à mon agenda (.ics)
          </a>

          <!-- Voir mes RDV -->
          <a href="/integration/rdv/mes-rdv"
             class="w-full py-4 bg-primary text-on-primary font-black rounded-2xl hover:opacity-90 active:scale-[.98] transition-all shadow-lg shadow-primary/25 text-sm uppercase tracking-wide text-center">
            Voir mes rendez-vous
          </a>

          <!-- Retour annuaire -->
          <a href="/integration/rdv/annuaire"
             class="w-full py-4 bg-white border border-outline-variant text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container active:scale-[.98] transition-all text-sm uppercase tracking-wide text-center">
            Retour à l'annuaire
          </a>

        </div>

      </div><!-- /px-8 py-10 -->
    </div>
    <!-- /Carte principale -->

    <!-- Texte sous la carte -->
    <p class="text-center text-xs text-on-surface-variant/50 mt-5 anim-fade-up" style="animation-delay:.44s">
      Vous pouvez annuler votre rendez-vous depuis <a href="/integration/rdv/mes-rdv" class="underline hover:text-primary transition-colors">Mes rendez-vous</a>.
    </p>

  </div>
</div>