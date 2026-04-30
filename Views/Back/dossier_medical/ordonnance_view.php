<?php
// Views/Back/dossier_medical/ordonnance_view.php — View a single prescription
$pName   = htmlspecialchars(($patient['prenom'] ?? '').' '.($patient['nom'] ?? ''));
$dName   = htmlspecialchars(($ordonnance['prenom_medecin'] ?? '').' '.($ordonnance['nom_medecin_nom'] ?? ''));
$statColors=['active'=>'bg-tertiary-fixed/40 text-tertiary','archivee'=>'bg-surface-container text-on-surface-variant','annulee'=>'bg-error-container/30 text-error'];
$st = $ordonnance['statut'] ?? 'active';
?>
<div class="max-w-3xl mx-auto space-y-6">

  <?php if (!empty($flash)): ?>
  <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium <?= $flash['type']==='success'?'bg-tertiary-fixed/40':'bg-error-container/30 text-error' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
  <?php endif ?>

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="/integration/dossier/ordonnances" class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
      </a>
      <div>
        <h1 class="font-headline text-2xl font-extrabold text-blue-900">Ordonnance <?= htmlspecialchars($ordonnance['numero_ordonnance'] ?? '') ?></h1>
        <p class="text-sm text-on-surface-variant">Patient : <strong><?= $pName ?></strong></p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 rounded-full text-xs font-bold <?= $statColors[$st] ?? '' ?>"><?= ucfirst($st) ?></span>
      <a href="/integration/dossier/ordonnance/edit?id=<?= $ordonnance['id_ordonnance'] ?>"
         class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-semibold hover:bg-primary/20 transition-colors">
        <span class="material-symbols-outlined text-sm">edit</span>Modifier
      </a>
      <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
        <span class="material-symbols-outlined text-sm">print</span>Imprimer
      </button>
    </div>
  </div>

  <!-- Info card -->
  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <div class="grid grid-cols-2 gap-6">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Patient</p>
        <p class="font-semibold text-on-surface"><?= $pName ?></p>
        <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($ordonnance['mail_patient'] ?? '') ?></p>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Médecin prescripteur</p>
        <p class="font-semibold text-on-surface">Dr. <?= $dName ?></p>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Date d'émission</p>
        <p class="font-semibold text-on-surface"><?= date('d/m/Y', strtotime($ordonnance['date_emission'])) ?></p>
      </div>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Consultation liée</p>
        <p class="font-semibold text-on-surface"><?= date('d/m/Y', strtotime($consultation['date_consultation'] ?? 'now')) ?></p>
        <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($consultation['type_consultation'] ?? '') ?></p>
      </div>
    </div>
    <?php if (!empty($ordonnance['diagnostic'])): ?>
    <div class="mt-4 pt-4 border-t border-outline-variant/20">
      <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Diagnostic</p>
      <p class="text-sm text-on-surface"><?= htmlspecialchars($ordonnance['diagnostic']) ?></p>
    </div>
    <?php endif ?>
  </div>

  <!-- Medications -->
  <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <h3 class="font-headline font-bold text-blue-900 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary">medication</span>Médicaments prescrits
    </h3>
    <?php if (empty($medicaments)): ?>
    <p class="text-on-surface-variant italic text-sm">Aucun médicament enregistré.</p>
    <?php else: ?>
    <div class="space-y-3">
      <?php foreach ($medicaments as $i => $med): ?>
      <div class="bg-surface-container-low rounded-xl p-4 border-l-4 border-primary/40">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center"><?= $i+1 ?></span>
            <p class="font-bold text-on-surface"><?= htmlspecialchars($med['nom'] ?? '') ?> <span class="font-normal text-on-surface-variant"><?= htmlspecialchars($med['dosage'] ?? '') ?></span></p>
          </div>
          <?php if (!empty($med['categorie'])): ?><span class="px-2 py-0.5 bg-blue-50 text-primary rounded-full text-[10px] font-bold"><?= htmlspecialchars($med['categorie']) ?></span><?php endif ?>
        </div>
        <div class="grid grid-cols-3 gap-3 text-xs text-on-surface-variant">
          <?php if (!empty($med['frequence'])): ?><div><span class="font-bold">Fréquence:</span> <?= htmlspecialchars($med['frequence']) ?></div><?php endif ?>
          <?php if (!empty($med['duree'])): ?><div><span class="font-bold">Durée:</span> <?= htmlspecialchars($med['duree']) ?></div><?php endif ?>
          <?php if (!empty($med['instructions'])): ?><div class="col-span-3"><span class="font-bold">Instructions:</span> <?= htmlspecialchars($med['instructions']) ?></div><?php endif ?>
        </div>
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>

  <!-- Note pharmacien -->
  <?php if (!empty($ordonnance['note_pharmacien'])): ?>
  <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-5">
    <h3 class="font-headline font-bold text-amber-700 mb-2 flex items-center gap-2 text-sm">
      <span class="material-symbols-outlined text-lg">local_pharmacy</span>Note au Pharmacien
    </h3>
    <p class="text-sm text-amber-800"><?= htmlspecialchars($ordonnance['note_pharmacien']) ?></p>
  </div>
  <?php endif ?>
</div>

<style>@media print { .ml-64, header, aside, nav { display: none !important; } main { margin: 0 !important; } }</style>
