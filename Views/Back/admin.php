<?php
/* ── Admin RDV Dashboard ─── partial, rendered by layout.php ── */
$s = $stats ?? [];
?>
<div class="space-y-8">

  <!-- Page header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-black text-on-surface font-headline">Tableau de bord <span class="ml-2 text-sm font-semibold bg-primary text-on-primary px-3 py-1 rounded-full">ADMIN</span></h1>
      <p class="text-sm text-on-surface-variant mt-1">Vue globale des rendez-vous par médecin</p>
    </div>
    <form method="GET" action="/integration/rdv/admin" class="flex gap-2">
      <input name="recherche" value="<?= htmlspecialchars($recherche ?? '') ?>" placeholder="Rechercher un patient…"
             class="px-4 py-2 text-sm border border-outline-variant rounded-xl bg-surface-container-low focus:ring-2 focus:ring-primary/20 outline-none w-52"/>
      <select name="statut" class="px-3 py-2 text-sm border border-outline-variant rounded-xl bg-surface-container-low outline-none">
        <option value="">Tous statuts</option>
        <option value="en_attente"  <?= ($filtre_statut??'')==='en_attente'  ?'selected':'' ?>>En attente</option>
        <option value="confirme"    <?= ($filtre_statut??'')==='confirme'    ?'selected':'' ?>>Confirmé</option>
        <option value="annule"      <?= ($filtre_statut??'')==='annule'      ?'selected':'' ?>>Annulé</option>
      </select>
      <button class="px-4 py-2 bg-primary text-on-primary text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">Filtrer</button>
    </form>
  </div>

  <!-- Stats grid -->
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <?php
    $cards = [
      ['Total RDV',   $s['total']        ?? 0, 'calendar_month', 'bg-primary/10 text-primary'],
      ['En attente',  $s['nb_attente']   ?? 0, 'schedule',       'bg-amber-50 text-amber-600'],
      ['Confirmés',   $s['nb_confirmes'] ?? 0, 'check_circle',   'bg-tertiary-fixed/40 text-tertiary-container'],
      ['Annulés',     $s['nb_annules']   ?? 0, 'cancel',         'bg-error-container/40 text-error'],
      ['Médecins',    $s['nb_medecins']  ?? 0, 'stethoscope',    'bg-secondary-fixed/40 text-secondary'],
    ];
    foreach ($cards as [$label, $val, $icon, $cls]): ?>
    <div class="bg-white rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,77,153,0.07)] border border-outline-variant/20">
      <div class="w-10 h-10 rounded-xl <?= $cls ?> flex items-center justify-center mb-3">
        <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
      </div>
      <p class="text-2xl font-black text-on-surface font-headline"><?= $val ?></p>
      <p class="text-xs text-on-surface-variant font-medium mt-0.5"><?= $label ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Doctors + RDV list -->
  <?php foreach (($grouped ?? []) as $mid => $group):
    $info = $group['info'];
    $rdvs = $group['rdvs'];
    $nom_med = htmlspecialchars(($info['prenom'] ?? '') . ' ' . ($info['nom'] ?? 'Médecin #'.$mid));
  ?>
  <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,77,153,0.07)] border border-outline-variant/20 overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-on-primary font-bold text-sm">
          <?= strtoupper(substr($info['prenom'] ?? 'M', 0, 1)) ?>
        </div>
        <div>
          <p class="font-bold text-on-surface text-sm">Dr <?= $nom_med ?></p>
          <p class="text-xs text-on-surface-variant"><?= count($rdvs) ?> rendez-vous</p>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Patient</th>
            <th class="px-5 py-3 text-left">CIN</th>
            <th class="px-5 py-3 text-left">Date</th>
            <th class="px-5 py-3 text-left">Heure</th>
            <th class="px-5 py-3 text-left">Genre</th>
            <th class="px-5 py-3 text-left">Statut</th>
            <th class="px-5 py-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php foreach ($rdvs as $rdv):
            $sc = match($rdv['statut']) {
              'confirme'   => 'bg-tertiary-fixed/40 text-on-tertiary-fixed',
              'annule'     => 'bg-error-container/40 text-on-error-container',
              default      => 'bg-secondary-fixed/40 text-on-secondary-fixed',
            };
            $sl = match($rdv['statut']) {
              'confirme'   => 'Confirmé',
              'annule'     => 'Annulé',
              default      => 'En attente',
            };
          ?>
          <tr class="hover:bg-surface-container-lowest/60 transition-colors">
            <td class="px-5 py-3.5 font-medium text-on-surface">
              <?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?>
            </td>
            <td class="px-5 py-3.5 text-on-surface-variant font-mono text-xs"><?= htmlspecialchars($rdv['cin']) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant"><?= substr($rdv['heure_rdv'], 0, 5) ?></td>
            <td class="px-5 py-3.5 text-on-surface-variant capitalize"><?= htmlspecialchars($rdv['genre']) ?></td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $sc ?>"><?= $sl ?></span>
            </td>
            <td class="px-5 py-3.5">
              <form method="POST" action="/integration/rdv/admin" class="flex items-center gap-1">
                <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>"/>
                <select name="statut" class="text-xs border border-outline-variant/40 rounded-lg px-2 py-1 bg-transparent outline-none">
                  <option value="en_attente" <?= $rdv['statut']==='en_attente'?'selected':'' ?>>En attente</option>
                  <option value="confirme"   <?= $rdv['statut']==='confirme'?'selected':'' ?>>Confirmé</option>
                  <option value="annule"     <?= $rdv['statut']==='annule'?'selected':'' ?>>Annulé</option>
                </select>
                <button class="text-xs px-2 py-1 bg-primary text-on-primary rounded-lg hover:opacity-90 transition-opacity">OK</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (empty($grouped)): ?>
  <div class="bg-white rounded-2xl p-12 text-center border border-outline-variant/20">
    <span class="material-symbols-outlined text-5xl text-on-surface-variant/40 block mb-3">calendar_month</span>
    <p class="text-on-surface-variant">Aucun rendez-vous trouvé.</p>
  </div>
  <?php endif; ?>

</div>