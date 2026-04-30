<?php
// Views/Back/dossier_medical/ordonnances_liste.php — Doctor's prescriptions list
$statColors=['active'=>'bg-tertiary-fixed/40 text-tertiary','archivee'=>'bg-surface-container text-on-surface-variant','annulee'=>'bg-error-container/30 text-error'];
?>
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Mes Ordonnances</h1>
      <p class="text-sm text-on-surface-variant mt-0.5"><?= $totalOrdo ?? 0 ?> ordonnance(s) au total</p>
    </div>
  </div>

  <?php if (empty($grouped)): ?>
  <div class="bg-white rounded-2xl p-16 text-center shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">receipt_long</span>
    <p class="font-semibold text-on-surface-variant">Aucune ordonnance enregistrée</p>
  </div>
  <?php else: ?>
  <?php foreach ($grouped as $group): ?>
  <?php $pName = htmlspecialchars($group['prenom_patient'].' '.$group['nom_famille']); ?>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
      <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold">
        <?= strtoupper(substr($group['prenom_patient'],0,1).substr($group['nom_famille'],0,1)) ?>
      </div>
      <div>
        <p class="font-semibold text-on-surface"><?= $pName ?></p>
        <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($group['mail_patient']) ?> · <?= count($group['ordonnances']) ?> ordonnance(s)</p>
      </div>
    </div>
    <div class="divide-y divide-outline-variant/10">
      <?php foreach ($group['ordonnances'] as $o):
        $meds = json_decode($o['medicaments'] ?? '[]', true) ?: [];
        $st = $o['statut'] ?? 'active';
      ?>
      <div class="px-6 py-4 flex items-center justify-between hover:bg-surface-container-low/40 transition-colors">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-bold text-on-surface-variant"><?= htmlspecialchars($o['numero_ordonnance']) ?></span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $statColors[$st] ?? '' ?>"><?= ucfirst($st) ?></span>
          </div>
          <p class="text-sm font-medium text-on-surface"><?= count($meds) ?> médicament(s) · Émis le <?= date('d/m/Y', strtotime($o['date_emission'])) ?></p>
          <?php if (!empty($o['diagnostic'])): ?><p class="text-xs text-on-surface-variant"><?= htmlspecialchars(substr($o['diagnostic'],0,80)) ?></p><?php endif ?>
        </div>
        <div class="flex items-center gap-2">
          <a href="/integration/dossier/ordonnance/view?id=<?= $o['id_ordonnance'] ?>"
             class="flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors">
            <span class="material-symbols-outlined text-sm">visibility</span>Voir
          </a>
          <a href="/integration/dossier/ordonnance/edit?id=<?= $o['id_ordonnance'] ?>"
             class="flex items-center gap-1 px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined text-sm">edit</span>
          </a>
          <form method="POST" action="/integration/dossier/ordonnance/delete" class="inline" onsubmit="return confirm('Supprimer cette ordonnance ?')">
            <input type="hidden" name="id" value="<?= $o['id_ordonnance'] ?>">
            <input type="hidden" name="patient_id" value="<?= $group['id_patient'] ?>">
            <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-error-container/30 text-error rounded-lg text-xs font-semibold hover:bg-error-container/50 transition-colors">
              <span class="material-symbols-outlined text-sm">delete</span>
            </button>
          </form>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
  <?php endforeach ?>
  <?php endif ?>
</div>
