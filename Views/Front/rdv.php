<?php
/* ── Formulaire Réservation (Patient) ─── partial, rendered by layout.php ── */
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$erreurs = $_SESSION['rdv_erreurs'] ?? [];
unset($_SESSION['rdv_erreurs']);
?>

<div class="max-w-4xl mx-auto flex flex-col md:flex-row gap-8 items-start">

  <!-- Gauche : Infos médecin -->
  <div class="w-full md:w-1/3 space-y-6">
    <div class="bg-white rounded-3xl p-6 border border-outline-variant/20 shadow-sm sticky top-24">
      <div class="text-center mb-6">
        <div class="w-20 h-20 bg-primary-fixed rounded-2xl flex items-center justify-center mx-auto text-primary text-3xl font-black mb-4">
          <?= strtoupper(substr($medecin['prenom'] ?? 'M', 0, 1)) ?>
        </div>
        <h2 class="text-xl font-black text-on-surface font-headline">
          Dr. <?= htmlspecialchars(($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '')) ?>
        </h2>
        <p class="text-xs text-on-surface-variant font-medium mt-1">Médecine Générale</p>
      </div>

      <div class="space-y-4 pt-6 border-t border-outline-variant/10">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
            <span class="material-symbols-outlined text-sm">mail</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] uppercase font-bold text-on-surface-variant/60">Email</p>
            <p class="text-xs font-semibold text-on-surface truncate">
              <?= htmlspecialchars($medecin['mail'] ?? 'Non renseigné') ?>
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-tertiary/10 flex items-center justify-center text-tertiary flex-shrink-0">
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

  <!-- Droite : Formulaire -->
  <div class="flex-1 w-full space-y-6">
    <div class="bg-white rounded-3xl p-8 border border-outline-variant/20 shadow-sm">
      <div class="mb-8">
        <h1 class="text-2xl font-black text-on-surface font-headline">Prendre Rendez-vous</h1>
        <p class="text-sm text-on-surface-variant mt-1">Veuillez remplir vos informations personnelles.</p>
      </div>

      <!-- Messages d'erreur PHP (serveur) -->
      <?php if (!empty($erreurs)): ?>
        <div class="mb-6 p-4 bg-error-container/20 border border-error/20 rounded-2xl flex items-start gap-3">
          <span class="material-symbols-outlined text-error flex-shrink-0">error</span>
          <div class="text-xs font-medium text-on-error-container space-y-1">
            <?php foreach ($erreurs as $err): ?>
              <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Zone d'erreurs JS -->
      <div id="js-erreurs" class="hidden mb-6 p-4 bg-error-container/20 border border-error/20 rounded-2xl flex items-start gap-3">
        <span class="material-symbols-outlined text-error flex-shrink-0">error</span>
        <div id="js-erreurs-liste" class="text-xs font-medium text-on-error-container space-y-1"></div>
      </div>

      <form id="formRdv" method="POST" action="<?= $base ?>/rdv/traitement" class="space-y-6" novalidate>
        <input type="hidden" name="medecin_id" value="<?= $medecin['id'] ?? '' ?>">

        <!-- Nom / Prénom -->
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom"
                   value="<?= htmlspecialchars($patient['nom'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
            <p class="text-error text-[10px] hidden ml-1" id="err-nom">Nom invalide (lettres uniquement).</p>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom"
                   value="<?= htmlspecialchars($patient['prenom'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
            <p class="text-error text-[10px] hidden ml-1" id="err-prenom">Prénom invalide (lettres uniquement).</p>
          </div>
        </div>

        <!-- CIN / Genre -->
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="cin">CIN (8 chiffres)</label>
            <input type="text" id="cin" name="cin" maxlength="8" placeholder="Ex: 12345678"
                   value="<?= htmlspecialchars($patient['cin'] ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all font-mono"/>
            <p class="text-error text-[10px] hidden ml-1" id="err-cin">Le CIN doit contenir exactement 8 chiffres.</p>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="genre">Genre</label>
            <select id="genre" name="genre"
                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none cursor-pointer">
              <option value="">Sélectionnez…</option>
              <option value="homme">Homme</option>
              <option value="femme">Femme</option>
            </select>
            <p class="text-error text-[10px] hidden ml-1" id="err-genre">Veuillez sélectionner un genre.</p>
          </div>
        </div>

        <!-- Date / Heure -->
        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-outline-variant/10">
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="date_rdv">Date souhaitée</label>
            <input type="date" id="date_rdv" name="date_rdv" min="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($date_rdv ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
            <p class="text-error text-[10px] hidden ml-1" id="err-date">La date doit être aujourd'hui ou dans le futur.</p>
          </div>
          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant ml-1" for="heure_rdv">Heure</label>
            <input type="time" id="heure_rdv" name="heure_rdv"
                   value="<?= htmlspecialchars($heure_rdv ?? '') ?>"
                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/40 rounded-xl outline-none focus:ring-2 focus:ring-primary/20 text-sm transition-all"/>
            <p class="text-error text-[10px] hidden ml-1" id="err-heure">Veuillez sélectionner une heure.</p>
          </div>
        </div>

        <!-- Bouton -->
        <div class="pt-4">
          <button type="submit"
                  class="w-full py-4 bg-primary text-on-primary font-black rounded-2xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/30 font-headline tracking-wide uppercase text-sm">
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

<script>
(function () {
  const today = new Date().toISOString().split('T')[0];

  // ── Helpers ──────────────────────────────────────────────
  function get(id)      { return document.getElementById(id); }
  function val(id)      { return get(id).value.trim(); }
  function showErr(id)  { get('err-' + id).classList.remove('hidden'); get(id).classList.add('border-error'); }
  function hideErr(id)  { get('err-' + id).classList.add('hidden');    get(id).classList.remove('border-error'); }

  // Validation en temps réel au blur
  get('nom').addEventListener('blur',      () => validerChamp('nom'));
  get('prenom').addEventListener('blur',   () => validerChamp('prenom'));
  get('cin').addEventListener('blur',      () => validerChamp('cin'));
  get('cin').addEventListener('input',     () => { get('cin').value = get('cin').value.replace(/\D/g, '').slice(0, 8); });
  get('genre').addEventListener('change',  () => validerChamp('genre'));
  get('date_rdv').addEventListener('blur', () => validerChamp('date'));
  get('heure_rdv').addEventListener('blur',() => validerChamp('heure'));

  function validerChamp(champ) {
    switch(champ) {
      case 'nom':
        /^[a-zA-ZÀ-ÿ\s\-]+$/.test(val('nom')) && val('nom') !== ''
          ? hideErr('nom') : showErr('nom');
        break;
      case 'prenom':
        /^[a-zA-ZÀ-ÿ\s\-]+$/.test(val('prenom')) && val('prenom') !== ''
          ? hideErr('prenom') : showErr('prenom');
        break;
      case 'cin':
        /^[0-9]{8}$/.test(val('cin'))
          ? hideErr('cin') : showErr('cin');
        break;
      case 'genre':
        val('genre') !== ''
          ? hideErr('genre') : showErr('genre');
        break;
      case 'date':
        val('date_rdv') !== '' && val('date_rdv') >= today
          ? hideErr('date') : showErr('date');
        break;
      case 'heure':
        val('heure_rdv') !== ''
          ? hideErr('heure') : showErr('heure');
        break;
    }
  }

  // ── Validation à la soumission ────────────────────────────
  get('formRdv').addEventListener('submit', function (e) {
    const champs = ['nom', 'prenom', 'cin', 'genre', 'date', 'heure'];
    champs.forEach(c => validerChamp(c));

    const erreurs = [];
    if (!/^[a-zA-ZÀ-ÿ\s\-]+$/.test(val('nom'))    || val('nom') === '')    erreurs.push('Nom invalide (lettres uniquement).');
    if (!/^[a-zA-ZÀ-ÿ\s\-]+$/.test(val('prenom')) || val('prenom') === '') erreurs.push('Prénom invalide (lettres uniquement).');
    if (!/^[0-9]{8}$/.test(val('cin')))                                      erreurs.push('Le CIN doit contenir exactement 8 chiffres.');
    if (val('genre') === '')                                                  erreurs.push('Veuillez sélectionner un genre.');
    if (val('date_rdv') === '' || val('date_rdv') < today)                   erreurs.push('La date doit être aujourd\'hui ou dans le futur.');
    if (val('heure_rdv') === '')                                              erreurs.push('Veuillez sélectionner une heure.');

    if (erreurs.length > 0) {
      e.preventDefault();
      const zone  = get('js-erreurs');
      const liste = get('js-erreurs-liste');
      liste.innerHTML = erreurs.map(err => `<p>• ${err}</p>`).join('');
      zone.classList.remove('hidden');
      zone.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
})();
</script>