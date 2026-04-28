<?php /* planning.php (Back) — partial */
$types_config = [
  'chirurgie'  => ['label'=>'Chirurgie',  'bg'=>'bg-red-50',    'border'=>'border-red-200',    'dot'=>'bg-red-500'],
  'reunion'    => ['label'=>'Réunion',    'bg'=>'bg-blue-50',   'border'=>'border-blue-200',   'dot'=>'bg-blue-500'],
  'pause'      => ['label'=>'Pause',      'bg'=>'bg-amber-50',  'border'=>'border-amber-200',  'dot'=>'bg-amber-500'],
  'formation'  => ['label'=>'Formation',  'bg'=>'bg-purple-50', 'border'=>'border-purple-200', 'dot'=>'bg-purple-500'],
  'urgence'    => ['label'=>'Urgence',    'bg'=>'bg-rose-50',   'border'=>'border-rose-200',   'dot'=>'bg-rose-500'],
  'rdv_conf'   => ['label'=>'RDV conf.',  'bg'=>'bg-green-50',  'border'=>'border-green-200',  'dot'=>'bg-green-500'],
  'rdv_att'    => ['label'=>'RDV att.',   'bg'=>'bg-gray-50',   'border'=>'border-gray-200',   'dot'=>'bg-gray-400'],
  'autre'      => ['label'=>'Autre',      'bg'=>'bg-surface-container', 'border'=>'border-outline-variant/30', 'dot'=>'bg-on-surface-variant'],
];
$noms_jours = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
?>
<div class="space-y-6">
  <!-- Header + navigation semaine -->
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="text-2xl font-black font-headline text-on-surface">Planning</h1>
      <p class="text-sm text-on-surface-variant mt-1">
        <?= $jours[0]->format('d/m') ?> → <?= end($jours)->format('d/m/Y') ?>
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= $url_prec ?>" class="p-2 rounded-xl border border-outline-variant/30 hover:bg-primary-fixed/30 transition-colors">
        <span class="material-symbols-outlined text-xl">chevron_left</span>
      </a>
      <span class="text-sm font-semibold text-on-surface px-3">Semaine <?= date('W', strtotime($debut_str)) ?></span>
      <a href="<?= $url_suiv ?>" class="p-2 rounded-xl border border-outline-variant/30 hover:bg-primary-fixed/30 transition-colors">
        <span class="material-symbols-outlined text-xl">chevron_right</span>
      </a>
    </div>
  </div>

  <!-- Weekly grid -->
  <div class="grid grid-cols-7 gap-3">
    <?php foreach ($jours as $i => $jour):
      $ds = $jour->format('Y-m-d');
      $isToday = $ds === date('Y-m-d');
      $events = $par_jour[$ds] ?? [];
    ?>
    <div class="bg-white rounded-2xl border <?= $isToday ? 'border-primary shadow-[0_0_0_2px_rgba(0,77,153,0.15)]' : 'border-outline-variant/20' ?> overflow-hidden">
      <!-- Day header -->
      <div class="px-3 py-2.5 <?= $isToday ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface-variant' ?> text-center">
        <p class="text-xs font-semibold uppercase tracking-wider"><?= $noms_jours[$i] ?></p>
        <p class="text-lg font-black font-headline"><?= $jour->format('d') ?></p>
      </div>
      <!-- Events -->
      <div class="p-2 space-y-1.5 min-h-[120px]">
        <?php if (empty($events)): ?>
        <p class="text-xs text-on-surface-variant/50 text-center py-4">Libre</p>
        <?php endif; ?>
        <?php foreach ($events as $ev):
          $typeKey = $ev['source']==='rdv'
            ? ($ev['type']==='confirme' ? 'rdv_conf' : ($ev['type']==='annule' ? 'autre' : 'rdv_att'))
            : ($ev['type'] ?? 'autre');
          $cfg = $types_config[$typeKey] ?? $types_config['autre'];
        ?>
        <div class="<?= $cfg['bg'] ?> border <?= $cfg['border'] ?> rounded-lg px-2 py-1.5 text-xs">
          <div class="flex items-center gap-1 mb-0.5">
            <span class="w-1.5 h-1.5 rounded-full <?= $cfg['dot'] ?> flex-shrink-0"></span>
            <span class="font-semibold text-on-surface truncate"><?= htmlspecialchars($ev['titre']) ?></span>
          </div>
          <p class="text-on-surface-variant pl-2.5"><?= substr($ev['debut'],0,5) ?><?= $ev['fin'] ? '–'.substr($ev['fin'],0,5) : '' ?></p>
          <?php if ($ev['source']==='planning'): ?>
          <a href="/integration/rdv/planning?del_event=<?= $ev['id'] ?>" onclick="return confirm('Supprimer ?')"
             class="text-error text-[10px] pl-2.5 hover:underline">supprimer</a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Add event form -->
  <div class="bg-white rounded-2xl border border-outline-variant/20 shadow-[0_2px_12px_rgba(0,77,153,0.07)] p-6">
    <h2 class="font-bold font-headline text-on-surface mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">add_circle</span> Ajouter un événement
    </h2>
    <form method="POST" action="/integration/rdv/planning" class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <div class="col-span-2 md:col-span-1">
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Titre *</label>
        <input type="text" name="titre" required placeholder="Titre de l'événement"
               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl outline-none focus:ring-2 focus:ring-primary/20"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Début *</label>
        <input type="datetime-local" name="date_debut" required
               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl outline-none focus:ring-2 focus:ring-primary/20"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Fin *</label>
        <input type="datetime-local" name="date_fin" required
               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl outline-none focus:ring-2 focus:ring-primary/20"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Type</label>
        <select name="type" class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl outline-none bg-white">
          <option value="reunion">Réunion</option>
          <option value="chirurgie">Chirurgie</option>
          <option value="pause">Pause</option>
          <option value="formation">Formation</option>
          <option value="urgence">Urgence</option>
          <option value="autre">Autre</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Note</label>
        <input type="text" name="note" placeholder="Note optionnelle"
               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-xl outline-none focus:ring-2 focus:ring-primary/20"/>
      </div>
      <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-primary text-on-primary text-sm font-bold rounded-xl hover:opacity-90 transition-opacity">
          Ajouter
        </button>
      </div>
    </form>
  </div>
</div>