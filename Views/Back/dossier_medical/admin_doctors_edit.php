<?php // admin_doctors_edit.php
$ve = $validation_errors ?? [];
$filled = fn($k) => htmlspecialchars($doctor[$k] ?? '');
?>
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="/integration/dossier/admin/doctors" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
      <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Modifier le Médecin</h1>
      <p class="text-sm text-on-surface-variant">Dr. <?= $filled('prenom') ?> <?= $filled('nom') ?></p>
    </div>
  </div>
  <?php if (!empty($ve)): ?>
  <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 flex gap-3">
    <span class="material-symbols-outlined text-error shrink-0">error</span>
    <ul class="text-sm text-error space-y-0.5"><?php foreach($ve as $msg): ?><li>• <?= htmlspecialchars($msg) ?></li><?php endforeach ?></ul>
  </div>
  <?php endif ?>
  <div class="bg-white rounded-2xl p-7 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <form method="POST" action="/integration/dossier/admin/doctors/edit?id=<?= $doctor['id_PK'] ?>" class="space-y-4">
      <input type="hidden" name="id" value="<?= $doctor['id_PK'] ?>">
      <?php foreach([['prenom','Prénom *'],['nom','Nom *'],['mail','Email *'],['tel','Téléphone'],['adresse','Adresse']] as [$f,$l]): ?>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1.5"><?= $l ?></label>
        <?php if($f==='adresse'): ?>
        <textarea name="<?=$f?>" rows="2" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 resize-none <?= isset($ve[$f])?'border-error':'' ?>"><?= $filled($f) ?></textarea>
        <?php else: ?>
        <input type="<?=$f==='mail'?'email':'text'?>" name="<?=$f?>" value="<?= $filled($f) ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 <?= isset($ve[$f])?'border-error':'' ?>"/>
        <?php endif ?>
        <?php if(isset($ve[$f])): ?><p class="text-xs text-error mt-1"><?= $ve[$f] ?></p><?php endif ?>
      </div>
      <?php endforeach ?>
      <div class="flex justify-end gap-3 pt-2">
        <a href="/integration/dossier/admin/doctors" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-on-surface-variant bg-surface-container hover:bg-surface-container-high transition-colors">Annuler</a>
        <button type="submit" class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-container shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2">
          <span class="material-symbols-outlined text-sm">save</span>Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>
