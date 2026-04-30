<?php
// Views/Back/dossier_medical/dossier_view.php — Patient Dossier (Doctor view)
// Partial rendered inside layout.php
$pName = htmlspecialchars($patient['prenom'].' '.$patient['nom']);
$pid   = $patient['id_PK'];
?>

<?php if (!empty($flash)): ?>
<div class="mb-4 flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium
  <?= $flash['type']==='success' ? 'bg-tertiary-fixed/40 text-on-surface' : 'bg-error-container/30 text-error' ?>">
  <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success' ? 'check_circle' : 'error' ?></span>
  <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif ?>

<div class="space-y-6">

  <!-- Back + Header -->
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div class="flex items-center gap-4">
      <a href="/integration/dossier/patients" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
      </a>
      <div>
        <h1 class="font-headline text-2xl font-extrabold text-blue-900 tracking-tight"><?= $pName ?></h1>
        <p class="text-sm text-on-surface-variant">#MF-<?= str_pad($pid,5,'0',STR_PAD_LEFT) ?> · <?= htmlspecialchars($patient['mail']) ?></p>
      </div>
    </div>
    <a href="/integration/dossier/nouvelle-consultation?patient_id=<?= $pid ?>"
       class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm shadow-md shadow-primary/20 hover:shadow-lg transition-all">
      <span class="material-symbols-outlined text-lg">add</span>
      Nouvelle Consultation
    </a>
  </div>

  <div class="grid grid-cols-12 gap-6">

    <!-- LEFT: Vitals + Antecedents + Allergies -->
    <div class="col-span-4 space-y-5">

      <!-- Vitals -->
      <?php if ($vitals): ?>
      <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <h3 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm">
          <span class="material-symbols-outlined text-primary text-lg">monitor_heart</span>Constantes vitales
        </h3>
        <div class="grid grid-cols-2 gap-3">
          <?php $vitData = [['Tension','tension_arterielle','mmHg','bloodtype'],['Rythme','rythme_cardiaque','BPM','favorite'],['Poids','poids','kg','scale'],['SpO₂','saturation_o2','%','air']]; ?>
          <?php foreach ($vitData as [$lbl,$key,$unit,$icon]): ?>
          <div class="bg-surface-container-low rounded-xl p-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1"><?= $lbl ?></p>
            <p class="font-extrabold text-on-surface"><?= htmlspecialchars($vitals[$key] ?? '—') ?> <span class="text-xs font-normal text-on-surface-variant"><?= $unit ?></span></p>
          </div>
          <?php endforeach ?>
        </div>
        <p class="text-[10px] text-on-surface-variant mt-3">Dernière MAJ : <?= date('d/m/Y', strtotime($vitals['date_consultation'])) ?></p>
      </div>
      <?php endif ?>

      <!-- Antécédents -->
      <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <h3 class="font-headline font-bold text-blue-900 mb-3 flex items-center gap-2 text-sm">
          <span class="material-symbols-outlined text-primary text-lg">history_edu</span>Antécédents
        </h3>
        <?php if (empty($antecedents)): ?>
        <p class="text-sm text-on-surface-variant italic">Aucun antécédent enregistré.</p>
        <?php else: ?>
        <div class="space-y-2">
          <?php foreach ($antecedents as $ant): ?>
          <div class="bg-primary/5 rounded-lg p-3">
            <p class="text-xs font-bold text-primary"><?= htmlspecialchars($ant['annee'] ?? '') ?> · <?= htmlspecialchars($ant['titre'] ?? '') ?></p>
            <?php if (!empty($ant['description'])): ?><p class="text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($ant['description']) ?></p><?php endif ?>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>

      <!-- Allergies -->
      <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <h3 class="font-headline font-bold text-blue-900 mb-3 flex items-center gap-2 text-sm">
          <span class="material-symbols-outlined text-error text-lg">warning</span>Allergies
        </h3>
        <?php if (empty($allergies)): ?>
        <p class="text-sm text-on-surface-variant italic">Aucune allergie connue.</p>
        <?php else: ?>
        <div class="flex flex-wrap gap-2">
          <?php $nColors=['Élevé'=>'bg-error-container/40 text-error','Modéré'=>'bg-amber-100 text-amber-700','Faible'=>'bg-green-100 text-green-700']; ?>
          <?php foreach ($allergies as $alg): ?>
          <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $nColors[$alg['niveau']] ?? 'bg-surface-container text-on-surface-variant' ?>">
            <?= htmlspecialchars($alg['nom']) ?> · <?= htmlspecialchars($alg['niveau']) ?>
          </span>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>
    </div>

    <!-- RIGHT: Consultations timeline -->
    <div class="col-span-8">
      <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
          <h3 class="font-headline font-bold text-blue-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">history</span>Historique Consultations
          </h3>
          <span class="text-xs text-on-surface-variant"><?= count($consultations) ?> consultation(s)</span>
        </div>

        <?php if (empty($consultations)): ?>
        <div class="py-16 text-center text-on-surface-variant">
          <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">event_note</span>
          <p class="font-semibold">Aucune consultation enregistrée</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-outline-variant/10">
          <?php foreach ($consultations as $c): ?>
          <?php
            $hasOrdo = !empty($c['derniere_consult_id']);
            $typeColors = ['Consultation urgente'=>'bg-error-container/30 text-error','Suivi Traitement'=>'bg-amber-100 text-amber-700','Bilan Annuel'=>'bg-blue-50 text-primary','Contrôle annuel'=>'bg-blue-50 text-primary'];
            $tc = $typeColors[$c['type_consultation']] ?? 'bg-surface-container text-on-surface-variant';
          ?>
          <div class="px-6 py-4 hover:bg-surface-container-low/40 transition-colors">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-xs font-bold text-on-surface-variant"><?= date('d/m/Y H:i', strtotime($c['date_consultation'])) ?></span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $tc ?>"><?= htmlspecialchars($c['type_consultation'] ?? 'N/A') ?></span>
                </div>
                <?php if (!empty($c['diagnostic'])): ?>
                <p class="text-sm font-semibold text-on-surface truncate"><?= htmlspecialchars($c['diagnostic']) ?></p>
                <?php endif ?>
                <?php if (!empty($c['compte_rendu'])): ?>
                <p class="text-xs text-on-surface-variant mt-0.5 line-clamp-2"><?= htmlspecialchars($c['compte_rendu']) ?></p>
                <?php endif ?>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <a href="/integration/dossier/ordonnance/view?consult_id=<?= $c['id_consultation'] ?>"
                   class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-tertiary/10 text-tertiary hover:bg-tertiary/20 transition-colors">
                  <span class="material-symbols-outlined text-sm">receipt_long</span>Ordo.
                </a>
                <a href="/integration/dossier/consultation/edit?id=<?= $c['id_consultation'] ?>"
                   class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                  <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <form method="POST" action="/integration/dossier/consultation/delete" class="inline"
                      onsubmit="return confirm('Supprimer cette consultation ?')">
                  <input type="hidden" name="id" value="<?= $c['id_consultation'] ?>">
                  <input type="hidden" name="id_patient" value="<?= $pid ?>">
                  <button type="submit" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-error-container/30 text-error hover:bg-error-container/50 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
