<?php // Views/Back/dossier_medical/admin_doctors_liste.php ?>
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-headline text-2xl font-extrabold text-blue-900">Gestion des Médecins</h1>
      <p class="text-sm text-on-surface-variant mt-0.5"><?= $totalCount ?? 0 ?> médecin(s) enregistré(s)</p>
    </div>
  </div>
  <?php if (!empty($flash)): ?>
  <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium <?= $flash['type']==='success'?'bg-tertiary-fixed/40':'bg-error-container/30 text-error' ?>">
    <span class="material-symbols-outlined"><?= $flash['type']==='success'?'check_circle':'error' ?></span><?= htmlspecialchars($flash['msg']) ?>
  </div>
  <?php endif ?>
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)]">
    <?php if (empty($doctors)): ?>
    <div class="py-16 text-center text-on-surface-variant"><span class="material-symbols-outlined text-5xl block mb-3 opacity-30">person_off</span><p class="font-semibold">Aucun médecin trouvé</p></div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-surface-container-low">
          <tr>
            <?php foreach([['Médecin','prenom'],['Email','mail'],['Patients','nb_patients'],['Rôle','role_libelle'],['Actions','']] as [$h,$s]): ?>
            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">
              <?php if($s): ?><a href="?sort=<?=$s?>&order=<?=($sortBy===$s&&$sortOrder==='ASC')?'DESC':'ASC'?>" class="hover:text-primary"><?= $h ?></a><?php else: ?><?= $h ?><?php endif ?>
            </th>
            <?php endforeach ?>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
          <?php foreach ($doctors as $d): ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold">
                  <?= strtoupper(substr($d['prenom'],0,1).substr($d['nom'],0,1)) ?>
                </div>
                <div>
                  <p class="font-semibold text-on-surface">Dr. <?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></p>
                  <p class="text-[11px] text-on-surface-variant"><?= htmlspecialchars($d['tel']??'') ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-xs text-on-surface-variant"><?= htmlspecialchars($d['mail']) ?></td>
            <td class="px-6 py-4">
              <a href="/integration/dossier/admin/doctors/patients?doctor_id=<?= $d['id_PK'] ?>"
                 class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                <?= (int)$d['nb_patients'] ?> pts
              </a>
            </td>
            <td class="px-6 py-4"><span class="text-xs px-2 py-0.5 bg-surface-container rounded-full"><?= htmlspecialchars($d['role_libelle']??'') ?></span></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="/integration/dossier/admin/doctors/edit?id=<?= $d['id_PK'] ?>"
                   class="flex items-center gap-1 px-2.5 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors">
                  <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </a>
                <form method="POST" action="/integration/dossier/admin/doctors/delete" class="inline"
                      onsubmit="return confirm('Supprimer ce médecin ?')">
                  <input type="hidden" name="id" value="<?= $d['id_PK'] ?>">
                  <button type="submit" class="flex items-center gap-1 px-2.5 py-1.5 bg-error-container/30 text-error rounded-lg text-xs font-semibold hover:bg-error-container/50 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
    <?php if (($totalPages??1) > 1): ?>
    <div class="px-6 py-4 border-t border-outline-variant/20 flex items-center justify-between">
      <p class="text-xs text-on-surface-variant">Page <?= $page ?> / <?= $totalPages ?></p>
      <div class="flex gap-1">
        <?php for($pg=1;$pg<=$totalPages;$pg++): ?>
        <a href="?p=<?=$pg?>&sort=<?=$sortBy?>&order=<?=$sortOrder?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold transition-colors <?=$pg===$page?'bg-primary text-white':'bg-surface-container-low hover:bg-primary/10 text-on-surface'?>"><?=$pg?></a>
        <?php endfor ?>
      </div>
    </div>
    <?php endif ?>
    <?php endif ?>
  </div>
</div>
