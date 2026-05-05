<?php // Views/Back/dossier_medical/admin_doctors_patients.php ?>

<!-- Breadcrumb & Header -->
<div class="mb-8">
  <div class="flex items-center gap-2 text-sm text-slate-500 mb-3">
    <a href="/integration/dossier/admin" class="hover:text-blue-700 transition-colors font-medium">Dashboard</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <a href="/integration/dossier/admin/doctors" class="hover:text-blue-700 transition-colors font-medium">Médecins</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-slate-800 font-bold">Patients</span>
  </div>
  
  <div class="flex flex-wrap gap-4 justify-between items-start mb-2">
    <div>
      <h1 class="text-3xl font-extrabold text-blue-900 font-headline tracking-tight">Patients de Dr. <?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></h1>
      <p class="text-slate-500 mt-2 font-medium">Gestion et suivi des patients du médecin</p>
    </div>
    <a href="/integration/dossier/admin/doctors" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors font-bold shadow-sm">
      <span class="material-symbols-outlined text-lg">arrow_back</span>
      Retour
    </a>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <!-- Total Patients -->
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 p-6 flex flex-col hover:-translate-y-1 transition-transform duration-300">
    <div class="flex justify-between items-start mb-3">
      <div class="p-3 bg-blue-50 rounded-xl text-blue-700 shadow-sm border border-blue-100">
        <span class="material-symbols-outlined text-2xl">people</span>
      </div>
    </div>
    <h3 class="text-sm text-slate-500 font-bold mb-1">Total Patients</h3>
    <p class="text-3xl font-extrabold text-slate-800"><?php echo count($patients); ?></p>
  </div>

  <!-- Total Consultations -->
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 p-6 flex flex-col hover:-translate-y-1 transition-transform duration-300">
    <div class="flex justify-between items-start mb-3">
      <div class="p-3 bg-emerald-50 rounded-xl text-emerald-700 shadow-sm border border-emerald-100">
        <span class="material-symbols-outlined text-2xl">assignment</span>
      </div>
    </div>
    <h3 class="text-sm text-slate-500 font-bold mb-1">Consultations</h3>
    <p class="text-3xl font-extrabold text-slate-800"><?php echo array_sum(array_map(fn($p) => $p['nb_consultations'], $patients)); ?></p>
  </div>

  <!-- Total Prescriptions -->
  <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 p-6 flex flex-col hover:-translate-y-1 transition-transform duration-300">
    <div class="flex justify-between items-start mb-3">
      <div class="p-3 bg-purple-50 rounded-xl text-purple-700 shadow-sm border border-purple-100">
        <span class="material-symbols-outlined text-2xl">description</span>
      </div>
    </div>
    <h3 class="text-sm text-slate-500 font-bold mb-1">Ordonnances</h3>
    <p class="text-3xl font-extrabold text-slate-800"><?php echo array_sum(array_map(fn($p) => $p['nb_ordonnances'], $patients)); ?></p>
  </div>
</div>

<!-- Patients Table -->
<div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 overflow-hidden">
  <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/60">
    <h2 class="text-lg font-bold text-slate-800 font-headline">Liste des Patients</h2>
  </div>

  <?php if (!empty($patients)): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead>
        <tr class="text-[11px] uppercase tracking-widest text-slate-400 font-bold border-b border-slate-100 bg-slate-50/50">
          <th class="px-6 py-4">Patient</th>
          <th class="px-6 py-4">Contact</th>
          <th class="px-6 py-4 text-center">Consultations</th>
          <th class="px-6 py-4 text-center">Ordonnances</th>
          <th class="px-6 py-4">Dernière Visite</th>
          <th class="px-6 py-4 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php foreach ($patients as $patient): ?>
        <tr class="hover:bg-slate-50/80 transition-colors group">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                <?php echo strtoupper($patient['prenom'][0] . $patient['nom'][0]); ?>
              </div>
              <div>
                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></p>
                <p class="text-xs text-slate-400 font-medium mt-0.5">ID: #PAT-<?php echo str_pad($patient['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="text-[13px] font-medium">
              <p class="text-slate-600 flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-slate-400">mail</span>
                <?php echo htmlspecialchars($patient['mail']); ?>
              </p>
              <p class="text-xs text-slate-500 flex items-center gap-2 mt-1">
                <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                <?php echo htmlspecialchars($patient['tel'] ?? 'N/A'); ?>
              </p>
            </div>
          </td>
          <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 text-blue-700 font-extrabold text-sm hover:bg-blue-100 transition-colors border border-blue-100 shadow-sm">
              <?php echo (int)$patient['nb_consultations']; ?>
            </span>
          </td>
          <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-sm hover:bg-emerald-100 transition-colors border border-emerald-100 shadow-sm">
              <?php echo (int)$patient['nb_ordonnances']; ?>
            </span>
          </td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center gap-2 text-sm text-slate-600 font-medium bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
              <span class="material-symbols-outlined text-[16px] text-slate-400">calendar_today</span>
              <?php 
                if ($patient['last_consultation']) {
                  echo date('d M Y', strtotime($patient['last_consultation']));
                } else {
                  echo 'Aucune visite';
                }
              ?>
            </span>
          </td>
          <td class="px-6 py-4 text-center">
            <a href="/integration/dossier/admin/consultations?patient_id=<?php echo $patient['id_PK']; ?>" class="inline-flex items-center justify-center w-10 h-10 text-slate-400 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100 group-hover:border-blue-100" title="Voir le dossier">
              <span class="material-symbols-outlined text-[20px]">folder_open</span>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="px-6 py-16 text-center">
    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
      <span class="material-symbols-outlined text-4xl text-slate-300">person_off</span>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-1">Aucun patient</h3>
    <p class="text-slate-500 font-medium">Ce médecin n'a actuellement aucun patient attribué.</p>
  </div>
  <?php endif; ?>
</div>
