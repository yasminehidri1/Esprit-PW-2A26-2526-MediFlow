<?php
// Views/Back/dossier_medical/demandes_liste.php — Prescription requests received by doctor
$statColors=['en_attente'=>'bg-amber-100 text-amber-700','traitee'=>'bg-tertiary-fixed/40 text-tertiary','refusee'=>'bg-error-container/30 text-error'];
$statIcons =['en_attente'=>'pending','traitee'=>'check_circle','refusee'=>'cancel'];
?>
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Demandes d'Ordonnance</h1>
      <p class="text-sm text-on-surface-variant mt-0.5"><?= count($demandes) ?> demande(s) reçue(s)</p>
    </div>
  </div>

  <?php if (!empty($flash)): ?>
  <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium <?= $flash['type']==='success'?'bg-tertiary-fixed/40':'bg-error-container/30 text-error' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
  <?php endif ?>

  <?php if (empty($demandes)): ?>
  <div class="bg-white rounded-2xl p-16 text-center shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <span class="material-symbols-outlined text-5xl block mb-3 opacity-30">inbox</span>
    <p class="font-semibold text-on-surface-variant">Aucune demande reçue</p>
  </div>
  <?php else: ?>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <div class="divide-y divide-outline-variant/10">
      <?php foreach ($demandes as $d): ?>
      <?php $st = $d['statut'] ?? 'en_attente'; ?>
      <div class="px-6 py-5 hover:bg-surface-container-low/40 transition-colors">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3 flex-1 min-w-0">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold shrink-0">
              <?= strtoupper(substr($d['patient_prenom'],0,1).substr($d['patient_nom'],0,1)) ?>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <p class="font-semibold text-on-surface"><?= htmlspecialchars($d['patient_prenom'].' '.$d['patient_nom']) ?></p>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1 <?= $statColors[$st] ?? '' ?>">
                  <span class="material-symbols-outlined text-xs"><?= $statIcons[$st] ?? 'help' ?></span>
                  <?= ['en_attente'=>'En attente','traitee'=>'Traitée','refusee'=>'Refusée'][$st] ?? $st ?>
                </span>
              </div>
              <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($d['patient_mail']) ?></p>
              <p class="text-sm text-on-surface mt-2"><?= htmlspecialchars($d['description']) ?></p>
              <p class="text-[10px] text-on-surface-variant mt-1"><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></p>
            </div>
          </div>
          <?php if ($st === 'en_attente'): ?>
          <div class="flex items-center gap-2 shrink-0">
            <form method="POST" action="/integration/dossier/demandes/statut">
              <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
              <input type="hidden" name="statut" value="traitee">
              <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-tertiary/10 text-tertiary rounded-lg text-xs font-semibold hover:bg-tertiary/20 transition-colors">
                <span class="material-symbols-outlined text-sm">check</span>Traiter
              </button>
            </form>
            <form method="POST" action="/integration/dossier/demandes/statut">
              <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
              <input type="hidden" name="statut" value="refusee">
              <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-error-container/30 text-error rounded-lg text-xs font-semibold hover:bg-error-container/50 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>Refuser
              </button>
            </form>
            <a href="/integration/dossier/patients" class="flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors">
              <span class="material-symbols-outlined text-sm">history_edu</span>Créer ordo.
            </a>
          </div>
          <?php elseif ($st === 'traitee' || $st === 'refusee'): ?>
          <form method="POST" action="/integration/dossier/demandes/statut">
            <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
            <input type="hidden" name="statut" value="en_attente">
            <button type="submit" class="px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high transition-colors">Remettre en attente</button>
          </form>
          <?php endif ?>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
  <?php endif ?>
</div>
