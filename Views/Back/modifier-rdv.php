<?php /* modifier-rdv.php — partial */ ?>
<div class="max-w-xl mx-auto">
  <div class="mb-6">
    <a href="/integration/rdv/dashboard" class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors">
      <span class="material-symbols-outlined text-base">arrow_back</span> Retour au tableau de bord
    </a>
  </div>
  <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,77,153,0.08)] border border-outline-variant/20 p-8">
    <h1 class="text-xl font-black font-headline text-on-surface mb-6 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">edit_calendar</span>
      Modifier le rendez-vous
    </h1>
    <form method="POST" action="/integration/rdv/dashboard" class="space-y-5">
      <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>"/>

      <div class="bg-surface-container-low rounded-xl p-4 text-sm space-y-1">
        <p class="font-semibold text-on-surface"><?= htmlspecialchars($rdv['patient_prenom'].' '.$rdv['patient_nom']) ?></p>
        <p class="text-on-surface-variant">CIN : <?= htmlspecialchars($rdv['cin']) ?> &middot; <?= ucfirst($rdv['genre']) ?></p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-on-surface mb-1.5">Date du rendez-vous</label>
        <input type="date" name="date_rdv" value="<?= htmlspecialchars($rdv['date_rdv']) ?>" min="<?= date('Y-m-d') ?>" required
               class="w-full px-4 py-2.5 text-sm border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/20 outline-none bg-white"/>
      </div>
      <div>
        <label class="block text-sm font-semibold text-on-surface mb-1.5">Heure</label>
        <input type="time" name="heure_rdv" value="<?= substr($rdv['heure_rdv'],0,5) ?>" required
               class="w-full px-4 py-2.5 text-sm border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/20 outline-none bg-white"/>
      </div>
      <div>
        <label class="block text-sm font-semibold text-on-surface mb-1.5">Statut</label>
        <select name="statut" class="w-full px-4 py-2.5 text-sm border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/20 outline-none bg-white">
          <option value="en_attente" <?= $rdv['statut']==='en_attente'?'selected':'' ?>>En attente</option>
          <option value="confirme"   <?= $rdv['statut']==='confirme'?'selected':'' ?>>Confirmé</option>
          <option value="annule"     <?= $rdv['statut']==='annule'?'selected':'' ?>>Annulé</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 py-2.5 bg-primary text-on-primary text-sm font-bold rounded-xl hover:opacity-90 transition-opacity shadow-sm">
          Enregistrer les modifications
        </button>
        <a href="/integration/rdv/dashboard" class="flex-1 py-2.5 text-center text-sm font-semibold text-on-surface-variant bg-surface-container rounded-xl hover:bg-surface-container-high transition-colors">
          Annuler
        </a>
      </div>
    </form>
  </div>
</div>