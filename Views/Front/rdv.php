<?php
/* ── Formulaire Réservation (Patient) ─── partial, rendered by layout.php ── */
?>
<div class="max-w-4xl mx-auto flex flex-col md:flex-row gap-8 items-start">

  <!-- Left Side: Doctor Info & Steps -->
  <div class="w-full md:w-1/3 space-y-6">
    <div class="bg-white rounded-3xl p-6 border border-outline-variant/20 shadow-sm sticky top-24">
      <div class="text-center mb-6">
        <div class="w-20 h-20 bg-primary-fixed rounded-2xl flex items-center justify-center mx-auto text-primary text-3xl font-black mb-4">
          <?= strtoupper(substr($medecin['prenom'], 0, 1)) ?>
        </div>
        <h2 class="text-xl font-black text-on-surface font-headline">Dr. <?= htmlspecialchars($medecin['prenom'].' '.$medecin['nom']) ?></h2>
        <p class="text-xs text-on-surface-variant font-medium mt-1">Médecine Générale</p>
      </div>

      <div class="space-y-4 pt-6 border-t border-outline-variant/10">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-sm">mail</span>
          </div>
          <div class="flex-1">
            <p class="text-[10px] uppercase font-bold text-on-surface-variant/60">Email</p>
            <p class="text-xs font-semibold text-on-surface"><?= htmlspecialchars($medecin['mail']) ?></p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-tertiary/10 flex items-center justify-center text-tertiary">
            <span class="material-symbols-outlined text-sm">verified</span>
          </div>
          <div class="flex-1">
            <p class="text-[10px] uppercase font-bold text-on-surface-variant/60">Vérifié</p>
            <p class="text-xs font-semibold text-on-surface">Membre certifié MediFlow</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Booking Form -->
  <div class="flex-1 w-full space-y-6">
    <div class="bg-white rounded-3xl p-8 border border-outline-variant/20 shadow-sm">
      <div class="mb-8">
        <h1 class="text-2xl font-black text-on-surface font-headline">Prendre Rendez-vous</h1>
        <p class="text-sm text-on-surface-variant mt-1">Veuillez remplir vos informations personnelles.</p>
      </div>

      <?php if (!empty($erreurs)): ?>
        <div class="mb-6 p-4 bg-error-container/20 border border-error/20 rounded-2xl flex items-start gap-3">
          <span class="material-symbols-outlined text-error">error</span>
          <div class="text-xs font-medium text-on-error-container">
            <?php foreach ($erreurs as $err) echo "<p>$err</p>"; ?>
          </div>
        </div>
      <?php endif; ?>

      <form method="POST" action="/integration/rdv/traitement" class="space-y-6">
        <input type="hidden" name="medecin_id" value="<?= $medecin['id'] ?>">

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">Nom</label>
            <input type="text" name="nom" required placeholder="Votre nom" value="<?= htmlspecialchars($patient['nom'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">Prénom</label>
            <input type="text" name="prenom" required placeholder="Votre prénom" value="<?= htmlspecialchars($patient['prenom'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">CIN (8 chiffres)</label>
            <input type="text" name="cin" required maxlength="8" pattern="[0-9]{8}" placeholder="Ex: 12345678" value="<?= htmlspecialchars($patient['cin'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all font-mono"/>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">Genre</label>
            <select name="genre" required
                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none cursor-pointer">
              <option value="">Sélectionnez…</option>
              <option value="homme">Homme</option>
              <option value="femme">Femme</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-outline-variant/10">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">Date souhaitée</label>
            <input type="date" name="date_rdv" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($date_rdv ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1">Heure</label>
            <input type="time" name="heure_rdv" required value="<?= htmlspecialchars($heure_rdv ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
          </div>
        </div>

        <div class="pt-4">
          <button type="submit" class="w-full py-4 bg-primary text-on-primary font-black rounded-2xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/30 font-headline tracking-wide uppercase text-sm">
            Confirmer la demande de RDV
          </button>
          <p class="text-[10px] text-center text-on-surface-variant mt-4 px-8">
            En confirmant, vous acceptez que vos informations soient transmises au cabinet du praticien.
          </p>
        </div>
      </form>
    </div>
  </div>

</div>