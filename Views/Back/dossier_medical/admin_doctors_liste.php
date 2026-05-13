<?php // Views/Back/dossier_medical/admin_doctors_liste.php ?>

<!-- Header -->
<div class="mb-7 flex flex-wrap justify-between items-center gap-4">
  <div>
    <h2 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1 font-headline">Gestion des Médecins</h2>
    <p class="text-slate-500 text-sm font-medium"><?php echo number_format($totalCount); ?> médecin<?php echo $totalCount > 1 ? 's' : ''; ?> enregistré<?php echo $totalCount > 1 ? 's' : ''; ?> dans le réseau</p>
  </div>
</div>

<!-- Flash Message -->
<?php if (!empty($flash)): ?>
<div class="mb-6 p-4 rounded-lg bg-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-50 text-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-800 border border-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-200 flex justify-between items-center" id="flash">
  <span class="flex items-center gap-2"><span class="material-symbols-outlined"><?php echo $flash['type'] === 'success' ? 'check_circle' : 'info'; ?></span><?php echo htmlspecialchars($flash['msg']); ?></span>
  <button onclick="document.getElementById('flash').style.display='none'" class="text-sm font-bold">×</button>
</div>
<?php endif; ?>

<!-- Barre de recherche -->
<div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 p-5 mb-6">
  <div class="flex gap-3">
    <div class="flex-1 relative">
      <span class="material-symbols-outlined absolute left-3.5 top-3 text-slate-400 text-lg">search</span>
      <input
        type="text"
        id="searchInput"
        placeholder="Rechercher par nom, email, téléphone..."
        class="w-full pl-11 pr-10 py-2.5 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-blue-700 focus:ring-1 focus:ring-blue-700"
      />
      <button id="clearSearch" class="absolute right-3 top-3 text-slate-300 hover:text-slate-500 hidden transition-colors">
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 whitespace-nowrap">
      <span class="material-symbols-outlined text-sm text-slate-400">manage_search</span>
      <span id="visibleCount"><?php echo count($doctors); ?></span>
      <span class="text-slate-400">/ <?php echo number_format($totalCount); ?></span>
    </div>
  </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-100 overflow-hidden">
  <div class="px-6 py-4 bg-slate-50/60 border-b border-slate-100 flex justify-between items-center">
    <h3 class="text-base font-bold text-blue-900 font-headline">Liste des Médecins</h3>
    <span class="text-xs text-slate-400 font-medium">
      Page <strong class="text-slate-600"><?php echo $page; ?></strong> / <?php echo max(1, $totalPages); ?>
    </span>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-[10px] uppercase tracking-widest text-slate-400 font-bold border-b border-slate-100 bg-slate-50/50">
          <th class="px-6 py-3.5">
            <a href="?sort=prenom&order=<?php echo ($sortBy === 'prenom' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>" class="flex items-center hover:text-blue-700">
              Médecin
              <?php if ($sortBy === 'prenom'): ?><span class="material-symbols-outlined text-sm text-blue-700 ml-1"><?php echo $sortOrder === 'ASC' ? 'arrow_upward' : 'arrow_downward'; ?></span><?php else: ?><span class="material-symbols-outlined text-sm text-slate-300 ml-1">unfold_more</span><?php endif; ?>
            </a>
          </th>
          <th class="px-6 py-3.5">
            <a href="?sort=mail&order=<?php echo ($sortBy === 'mail' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>" class="flex items-center hover:text-blue-700">
              Email
              <?php if ($sortBy === 'mail'): ?><span class="material-symbols-outlined text-sm text-blue-700 ml-1"><?php echo $sortOrder === 'ASC' ? 'arrow_upward' : 'arrow_downward'; ?></span><?php else: ?><span class="material-symbols-outlined text-sm text-slate-300 ml-1">unfold_more</span><?php endif; ?>
            </a>
          </th>
          <th class="px-6 py-3.5">Téléphone</th>
          <th class="px-6 py-3.5">Rôle</th>
          <th class="px-6 py-3.5">
            <a href="?sort=nb_patients&order=<?php echo ($sortBy === 'nb_patients' && $sortOrder === 'ASC') ? 'DESC' : 'ASC'; ?>" class="flex items-center hover:text-blue-700">
              Patients
              <?php if ($sortBy === 'nb_patients'): ?><span class="material-symbols-outlined text-sm text-blue-700 ml-1"><?php echo $sortOrder === 'ASC' ? 'arrow_upward' : 'arrow_downward'; ?></span><?php else: ?><span class="material-symbols-outlined text-sm text-slate-300 ml-1">unfold_more</span><?php endif; ?>
            </a>
          </th>
          <th class="px-6 py-3.5 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php if (count($doctors) > 0): ?>
          <?php
          $maxPatients = max(array_column($doctors, 'nb_patients')) ?: 1;
          foreach ($doctors as $doctor):
            $pct = $maxPatients > 0 ? round(((int)$doctor['nb_patients'] / $maxPatients) * 100) : 0;
          ?>
          <tr class="hover:bg-blue-50/20 transition-colors doctor-row group"
              data-name="<?php echo htmlspecialchars(strtolower($doctor['prenom'] . ' ' . $doctor['nom'])); ?>"
              data-mail="<?php echo htmlspecialchars(strtolower($doctor['mail'])); ?>"
              data-tel="<?php echo htmlspecialchars(strtolower($doctor['tel'] ?? '')); ?>">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-extrabold flex-shrink-0">
                  <?php echo strtoupper(substr($doctor['prenom'], 0, 1) . substr($doctor['nom'], 0, 1)); ?>
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-800">Dr. <?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></p>
                  <p class="text-[11px] text-slate-400 font-medium">#DR-<?php echo str_pad($doctor['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <a href="mailto:<?php echo htmlspecialchars($doctor['mail']); ?>" class="text-sm text-slate-600 hover:text-blue-700 transition-colors">
                <?php echo htmlspecialchars($doctor['mail']); ?>
              </a>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-slate-600 font-medium"><?php echo htmlspecialchars($doctor['tel'] ?? '—'); ?></span>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                <span class="material-symbols-outlined text-[14px]">medical_services</span>
                <?php echo htmlspecialchars($doctor['role_libelle'] ?? 'N/A'); ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-blue-700 w-5"><?php echo (int)$doctor['nb_patients']; ?></span>
                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden max-w-[72px]">
                  <div class="h-full bg-blue-600 rounded-full" style="width: <?php echo $pct; ?>%"></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <button
                  onclick="viewDoctorPatients(<?php echo $doctor['id_PK']; ?>, '<?php echo htmlspecialchars(addslashes($doctor['prenom'] . ' ' . $doctor['nom'])); ?>')"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                  title="Voir les patients">
                  <span class="material-symbols-outlined text-sm">visibility</span>Patients
                </button>
                <!-- Edit & Delete buttons (shown on hover to match current UX, or always visible) -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <a href="/integration/dossier/admin/doctors/edit?id=<?php echo $doctor['id_PK']; ?>"
                     class="flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors" title="Modifier">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </a>
                  <form method="POST" action="/integration/dossier/admin/doctors/delete" class="inline m-0 p-0" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ? Cette action est irréversible.')">
                    <input type="hidden" name="id" value="<?php echo $doctor['id_PK']; ?>">
                    <button type="submit" class="flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Supprimer">
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </form>
                </div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="px-6 py-16 text-center">
              <span class="material-symbols-outlined text-5xl text-slate-200 flex justify-center mb-3">person_search</span>
              <p class="text-slate-500 font-medium">Aucun médecin trouvé.</p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pas de résultats de recherche -->
  <div id="noSearchResults" class="hidden py-12 text-center border-t border-slate-50">
    <span class="material-symbols-outlined text-4xl text-slate-200 flex justify-center mb-2">search_off</span>
    <p class="text-slate-500">Aucun résultat pour cette recherche locale.</p>
  </div>

  <!-- Pagination -->
  <?php if (($totalPages ?? 1) > 1 || count($doctors) > 0): ?>
  <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-wrap justify-between items-center gap-3">
    <div class="text-sm text-slate-500">
      <strong><?php echo number_format($totalCount); ?></strong> médecin<?php echo $totalCount > 1 ? 's' : ''; ?> au total
    </div>
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="flex gap-2 flex-wrap">
      <?php if ($page > 1): ?>
        <a href="?p=<?php echo $page - 1; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
          <span class="material-symbols-outlined text-[18px]">chevron_left</span>
        </a>
      <?php endif; ?>
      <?php
      $startPg = max(1, $page - 2);
      $endPg   = min($totalPages, $page + 2);
      for ($pg = $startPg; $pg <= $endPg; $pg++):
        $isActive = ($pg === $page);
      ?>
        <a href="?p=<?php echo $pg; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
           class="px-3.5 py-1.5 rounded-lg text-sm font-bold transition-colors <?php echo $isActive ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
          <?php echo $pg; ?>
        </a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="?p=<?php echo $page + 1; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
          <span class="material-symbols-outlined text-[18px]">chevron_right</span>
        </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Modal 1: Doctor's Patients (Reused from Dashboard but kept local here) -->
<div id="doctorPatientsModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
  <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden">
    <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 flex justify-between items-center shadow-md z-10 flex-shrink-0">
      <div>
        <h2 class="text-xl font-extrabold font-headline" id="doctorPatientsTitle">Patients du Médecin</h2>
        <p class="text-blue-100 text-xs mt-1 font-medium">Consultations et ordonnances par patient</p>
      </div>
      <button onclick="closeDoctorPatientsModal()" class="text-white hover:bg-blue-800/50 rounded-lg p-2 transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="p-6 bg-slate-50/50 flex-1 overflow-y-auto">
      <div id="loadingSpinnerPatients" class="flex items-center justify-center py-16">
        <div class="flex flex-col items-center gap-4">
          <div class="animate-spin"><span class="material-symbols-outlined text-4xl text-blue-600">hourglass_empty</span></div>
          <p class="text-slate-500 font-medium text-sm">Chargement des données...</p>
        </div>
      </div>
      <div id="doctorPatientsContent" class="hidden overflow-hidden bg-white border border-slate-100 shadow-sm rounded-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="text-[10px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100 bg-slate-50/80">
                <th class="px-5 py-3">Patient</th>
                <th class="px-5 py-3">Contact</th>
                <th class="px-5 py-3 text-center">Consultations</th>
                <th class="px-5 py-3 text-center">Ordonnances</th>
                <th class="px-5 py-3">Dernière Visite</th>
              </tr>
            </thead>
            <tbody id="patientsTableBody" class="divide-y divide-slate-50"></tbody>
          </table>
        </div>
      </div>
      <div id="noResultsPatients" class="hidden text-center py-16 bg-white border border-slate-100 rounded-xl shadow-sm">
        <span class="material-symbols-outlined text-5xl text-slate-200 mb-3 flex justify-center">person_off</span>
        <p class="text-slate-500 font-medium">Ce médecin n'a actuellement aucun patient attribué.</p>
      </div>
    </div>
    <div class="bg-white px-6 py-4 border-t border-slate-100 flex justify-end flex-shrink-0">
      <button onclick="closeDoctorPatientsModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors font-bold text-sm">Fermer</button>
    </div>
  </div>
</div>

<script>
// ── Recherche en temps réel ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const searchInput    = document.getElementById('searchInput');
  const clearBtn       = document.getElementById('clearSearch');
  const visibleCounter = document.getElementById('visibleCount');
  const noResults      = document.getElementById('noSearchResults');

  function filterTable() {
    if (!searchInput) return;
    const term = searchInput.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.doctor-row');
    let visible = 0;

    rows.forEach(row => {
      const match = !term ||
        (row.dataset.name && row.dataset.name.includes(term)) ||
        (row.dataset.mail && row.dataset.mail.includes(term)) ||
        (row.dataset.tel && row.dataset.tel.includes(term));
      row.classList.toggle('hidden', !match);
      if (match) visible++;
    });

    if (visibleCounter) visibleCounter.textContent = visible;
    if (clearBtn) clearBtn.classList.toggle('hidden', term === '');
    if (noResults) noResults.classList.toggle('hidden', visible > 0 || rows.length === 0);
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterTable);
  }
  if (clearBtn) {
    clearBtn.addEventListener('click', () => { searchInput.value = ''; filterTable(); searchInput.focus(); });
  }
});

// ── Modal Patients ────────────────────────────────────────────────────────
function viewDoctorPatients(doctorId, doctorName) {
  const modal   = document.getElementById('doctorPatientsModal');
  const spinner = document.getElementById('loadingSpinnerPatients');
  const content = document.getElementById('doctorPatientsContent');
  const empty   = document.getElementById('noResultsPatients');

  if(!modal) return;

  modal.classList.remove('hidden');
  spinner.classList.remove('hidden');
  content.classList.add('hidden');
  empty.classList.add('hidden');
  document.getElementById('doctorPatientsTitle').textContent = `Patients — Dr. ${doctorName}`;

  // Use the correct API endpoint for the new architecture
  fetch(`/integration/dossier/admin/doctors/patients/api?doctor_id=${doctorId}`)
    .then(r => r.json())
    .then(data => {
      const tbody = document.getElementById('patientsTableBody');
      if (data.patients && data.patients.length > 0) {
        tbody.innerHTML = data.patients.map(p => `
          <tr class="hover:bg-blue-50/30 transition-colors">
            <td class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                  ${p.prenom.charAt(0).toUpperCase()}${p.nom.charAt(0).toUpperCase()}
                </div>
                <div>
                  <p class="font-bold text-slate-800 text-sm">${p.prenom} ${p.nom}</p>
                  <p class="text-[11px] text-slate-400 font-medium">#PAT-${String(p.id_PK).padStart(4,'0')}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 text-[13px] text-slate-600 font-medium">
              <p class="truncate max-w-[150px]" title="${p.mail}">${p.mail}</p>
              <p class="text-slate-400 mt-0.5">${p.tel || 'N/A'}</p>
            </td>
            <td class="px-5 py-4 text-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 border border-blue-100 text-blue-700 font-extrabold text-sm shadow-sm">${p.nb_consultations}</span>
            </td>
            <td class="px-5 py-4 text-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 font-extrabold text-sm shadow-sm">${p.nb_ordonnances}</span>
            </td>
            <td class="px-5 py-4 text-xs text-slate-600 font-medium">
              ${p.last_consultation ? new Date(p.last_consultation).toLocaleDateString('fr-FR', {year:'numeric',month:'short',day:'numeric'}) : '—'}
            </td>
          </tr>`).join('');
        content.classList.remove('hidden');
      } else {
        empty.classList.remove('hidden');
      }
      spinner.classList.add('hidden');
    })
    .catch((err) => { 
        console.error("Erreur lors de la récupération des patients:", err);
        empty.classList.remove('hidden'); 
        spinner.classList.add('hidden'); 
    });
}

function closeDoctorPatientsModal() {
  const modal = document.getElementById('doctorPatientsModal');
  if(modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeDoctorPatientsModal();
});
document.getElementById('doctorPatientsModal')?.addEventListener('click', e => { 
  if (e.target === e.currentTarget) closeDoctorPatientsModal(); 
});
</script>
