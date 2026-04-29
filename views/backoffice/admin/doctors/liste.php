<?php
/**
 * Admin Doctors List View
 */
$activePage = 'admin';
require __DIR__ . '/../helpers/avatar.php';

$sortBy    = $_GET['sort']  ?? 'prenom';
$sortOrder = $_GET['order'] ?? 'ASC';

function sortUrl(string $col, string $currentSort, string $currentOrder): string {
    $order = ($currentSort === $col && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    return "?page=admin&action=doctors&sort={$col}&order={$order}";
}
function sortIcon(string $col, string $currentSort, string $currentOrder): string {
    if ($currentSort !== $col) {
        return '<span class="material-symbols-outlined text-sm text-slate-300 ml-1">unfold_more</span>';
    }
    return $currentOrder === 'ASC'
        ? '<span class="material-symbols-outlined text-sm text-primary ml-1">arrow_upward</span>'
        : '<span class="material-symbols-outlined text-sm text-primary ml-1">arrow_downward</span>';
}
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Gestion des Médecins — MediFlow Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "tertiary-container": "#00736a", "on-primary-fixed-variant": "#00468c",
            "surface": "#f7f9fb", "surface-tint": "#005db7",
            "on-secondary-container": "#475c80", "surface-container-highest": "#e0e3e5",
            "on-tertiary-container": "#87f8ea", "background": "#f7f9fb",
            "error": "#ba1a1a", "on-primary-container": "#dae5ff",
            "secondary-container": "#c0d5ff", "on-tertiary-fixed": "#00201d",
            "on-tertiary": "#ffffff", "secondary-fixed-dim": "#b2c7f1",
            "secondary": "#4a5f83", "tertiary-fixed": "#84f5e8",
            "on-error-container": "#93000a", "inverse-on-surface": "#eff1f3",
            "secondary-fixed": "#d6e3ff", "surface-container-low": "#f2f4f6",
            "on-primary-fixed": "#001b3d", "inverse-surface": "#2d3133",
            "on-surface-variant": "#424752", "tertiary-fixed-dim": "#66d9cc",
            "surface-bright": "#f7f9fb", "outline": "#727783",
            "surface-container-lowest": "#ffffff", "tertiary": "#005851",
            "primary-container": "#1565c0", "on-error": "#ffffff",
            "inverse-primary": "#a9c7ff", "surface-container": "#eceef0",
            "on-primary": "#ffffff", "on-secondary-fixed-variant": "#32476a",
            "on-secondary-fixed": "#021b3c", "primary": "#004d99",
            "error-container": "#ffdad6", "outline-variant": "#c2c6d4",
            "on-surface": "#191c1e", "surface-dim": "#d8dadc",
            "primary-fixed-dim": "#a9c7ff", "primary-fixed": "#d6e3ff",
            "surface-container-high": "#e6e8ea", "on-secondary": "#ffffff",
            "surface-variant": "#e0e3e5", "on-tertiary-fixed-variant": "#005049",
            "on-background": "#191c1e"
          },
          "borderRadius": { "DEFAULT": "0.5rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
          "fontFamily": { "headline": ["Manrope"], "body": ["Inter"], "label": ["Inter"] }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
    .row-hidden { display: none; }
    .sort-link { display: flex; align-items: center; cursor: pointer; }
    .sort-link:hover { color: #004d99; }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<?php require __DIR__ . '/../layout/sidebar.php'; ?>
<?php require __DIR__ . '/../layout/topbar.php'; ?>

<main class="ml-64 pt-24 pb-12 px-8 min-h-screen">

  <!-- Header -->
  <div class="mb-7">
    <h2 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1 font-headline">Gestion des Médecins</h2>
    <p class="text-slate-500 text-sm font-medium"><?php echo number_format($totalCount); ?> médecin<?php echo $totalCount > 1 ? 's' : ''; ?> enregistré<?php echo $totalCount > 1 ? 's' : ''; ?> dans le réseau</p>
  </div>

  <!-- Flash Message -->
  <?php if ($flash): ?>
  <div class="mb-6 p-4 rounded-lg bg-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-50 text-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-800 border border-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-200 flex justify-between items-center" id="flash">
    <span><?php echo htmlspecialchars($flash['msg']); ?></span>
    <button onclick="document.getElementById('flash').style.display='none'" class="text-sm font-bold">×</button>
  </div>
  <?php endif; ?>

  <!-- Barre de recherche -->
  <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-6">
    <div class="flex gap-3">
      <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-3.5 top-3 text-slate-400 text-lg">search</span>
        <input
          type="text"
          id="searchInput"
          placeholder="Rechercher par nom, email, téléphone..."
          class="w-full pl-11 pr-10 py-2.5 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
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
  <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
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
              <a href="<?php echo sortUrl('prenom', $sortBy, $sortOrder); ?>" class="sort-link">
                Médecin<?php echo sortIcon('prenom', $sortBy, $sortOrder); ?>
              </a>
            </th>
            <th class="px-6 py-3.5">
              <a href="<?php echo sortUrl('mail', $sortBy, $sortOrder); ?>" class="sort-link">
                Email<?php echo sortIcon('mail', $sortBy, $sortOrder); ?>
              </a>
            </th>
            <th class="px-6 py-3.5">Téléphone</th>
            <th class="px-6 py-3.5">Rôle</th>
            <th class="px-6 py-3.5">
              <a href="<?php echo sortUrl('nb_patients', $sortBy, $sortOrder); ?>" class="sort-link">
                Patients<?php echo sortIcon('nb_patients', $sortBy, $sortOrder); ?>
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
            <tr class="hover:bg-blue-50/20 transition-colors doctor-row"
                data-name="<?php echo htmlspecialchars(strtolower($doctor['prenom'] . ' ' . $doctor['nom'])); ?>"
                data-mail="<?php echo htmlspecialchars(strtolower($doctor['mail'])); ?>"
                data-tel="<?php echo htmlspecialchars(strtolower($doctor['tel'] ?? '')); ?>">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <?php echo getAvatarHtml($doctor['id_PK'], $doctor['prenom'], $doctor['nom']); ?>
                  <div>
                    <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></p>
                    <p class="text-xs text-slate-400">#DR-<?php echo str_pad($doctor['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <a href="mailto:<?php echo htmlspecialchars($doctor['mail']); ?>" class="text-sm text-slate-600 hover:text-primary transition-colors">
                  <?php echo htmlspecialchars($doctor['mail']); ?>
                </a>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm text-slate-600"><?php echo htmlspecialchars($doctor['tel'] ?? '—'); ?></span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                  <span class="material-symbols-outlined text-xs">medical_services</span>
                  <?php echo htmlspecialchars($doctor['role_libelle'] ?? 'N/A'); ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <span class="text-sm font-bold text-primary w-5"><?php echo (int)$doctor['nb_patients']; ?></span>
                  <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden max-w-[72px]">
                    <div class="h-full bg-primary rounded-full" style="width: <?php echo $pct; ?>%"></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <button
                  onclick="viewDoctorPatients(<?php echo $doctor['id_PK']; ?>, '<?php echo htmlspecialchars(addslashes($doctor['prenom'] . ' ' . $doctor['nom'])); ?>')"
                  class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-primary bg-blue-50 hover:bg-primary hover:text-white transition-all"
                  title="Voir les patients">
                  <span class="material-symbols-outlined text-sm">visibility</span>Patients
                </button>
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
    <div id="noSearchResults" class="hidden py-12 text-center">
      <span class="material-symbols-outlined text-4xl text-slate-200 flex justify-center mb-2">search_off</span>
      <p class="text-slate-500">Aucun résultat pour cette recherche.</p>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-wrap justify-between items-center gap-3">
      <div class="text-sm text-slate-500">
        <strong><?php echo number_format($totalCount); ?></strong> médecin<?php echo $totalCount > 1 ? 's' : ''; ?> au total
      </div>
      <div class="flex gap-2 flex-wrap">
        <?php if ($page > 1): ?>
          <a href="?page=admin&action=doctors&p=<?php echo $page - 1; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
             class="inline-flex items-center gap-1 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
            <span class="material-symbols-outlined text-sm">chevron_left</span>Précédent
          </a>
        <?php endif; ?>
        <?php
        $startPg = max(1, $page - 2);
        $endPg   = min($totalPages, $page + 2);
        for ($pg = $startPg; $pg <= $endPg; $pg++):
          $isActive = ($pg === $page);
        ?>
          <a href="?page=admin&action=doctors&p=<?php echo $pg; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
             class="px-3.5 py-2 rounded-lg text-sm font-bold transition-colors <?php echo $isActive ? 'bg-primary text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
            <?php echo $pg; ?>
          </a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <a href="?page=admin&action=doctors&p=<?php echo $page + 1; ?>&sort=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($sortOrder); ?>"
             class="inline-flex items-center gap-1 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">
            Suivant<span class="material-symbols-outlined text-sm">chevron_right</span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<!-- Modal 1: Doctor's Patients -->
<div id="doctorPatientsModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
    <div class="sticky top-0 bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 flex justify-between items-center shadow-lg">
      <div>
        <h2 class="text-xl font-bold font-headline" id="doctorPatientsTitle">Patients du Médecin</h2>
        <p class="text-blue-100 text-xs mt-1">Consultations et ordonnances par patient</p>
      </div>
      <button onclick="closeDoctorPatientsModal()" class="text-white hover:bg-blue-800 rounded-lg p-2 transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="p-6 bg-gradient-to-b from-slate-50 to-white">
      <div id="loadingSpinnerPatients" class="flex items-center justify-center py-12">
        <div class="flex flex-col items-center gap-4">
          <div class="animate-spin"><span class="material-symbols-outlined text-5xl text-blue-600">hourglass_empty</span></div>
          <p class="text-slate-500 font-medium">Chargement des patients...</p>
        </div>
      </div>
      <div id="doctorPatientsContent" class="hidden overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-200 bg-slate-100">
              <th class="px-4 py-3">Patient</th>
              <th class="px-4 py-3">Contact</th>
              <th class="px-4 py-3 text-center">Consultations</th>
              <th class="px-4 py-3 text-center">Ordonnances</th>
              <th class="px-4 py-3">Dernière Visite</th>
            </tr>
          </thead>
          <tbody id="patientsTableBody" class="divide-y divide-slate-100"></tbody>
        </table>
      </div>
      <div id="noResultsPatients" class="hidden text-center py-12">
        <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 flex justify-center">person_off</span>
        <p class="text-slate-500 italic">Aucun patient trouvé pour ce médecin.</p>
      </div>
    </div>
    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end">
      <button onclick="closeDoctorPatientsModal()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm">Fermer</button>
    </div>
  </div>
</div>

<!-- Modal 2: Patient Details -->
<div id="patientDetailsModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
    <div class="sticky top-0 bg-gradient-to-r from-green-900 to-green-700 text-white p-6 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-bold font-headline" id="patientDetailsTitle">Détails du Patient</h2>
        <p class="text-green-100 text-xs mt-1">Consultations et ordonnances</p>
      </div>
      <button onclick="closePatientDetailsModal()" class="text-white hover:bg-green-800 rounded-lg p-2 transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="p-6">
      <div id="loadingSpinnerDetails" class="flex items-center justify-center py-8">
        <div class="animate-spin"><span class="material-symbols-outlined text-4xl text-green-600">hourglass_empty</span></div>
      </div>
      <div id="detailsContent" class="hidden">
        <div class="mb-8">
          <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">assignment</span>Consultations Médicales
          </h3>
          <div id="consultationsContainer" class="space-y-4"></div>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">description</span>Ordonnances
          </h3>
          <div id="prescriptionsContainer" class="space-y-4"></div>
        </div>
      </div>
    </div>
    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end">
      <button onclick="closePatientDetailsModal()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium text-sm">Fermer</button>
    </div>
  </div>
</div>

<script>
// ── Recherche en temps réel ───────────────────────────────────────────────
const searchInput    = document.getElementById('searchInput');
const clearBtn       = document.getElementById('clearSearch');
const visibleCounter = document.getElementById('visibleCount');
const noResults      = document.getElementById('noSearchResults');

function filterTable() {
  const term = searchInput.value.toLowerCase().trim();
  const rows = document.querySelectorAll('.doctor-row');
  let visible = 0;

  rows.forEach(row => {
    const match = !term ||
      row.dataset.name.includes(term) ||
      row.dataset.mail.includes(term) ||
      row.dataset.tel.includes(term);
    row.classList.toggle('row-hidden', !match);
    if (match) visible++;
  });

  visibleCounter.textContent = visible;
  clearBtn.classList.toggle('hidden', term === '');
  noResults.classList.toggle('hidden', visible > 0 || rows.length === 0);
}

searchInput.addEventListener('input', filterTable);
clearBtn.addEventListener('click', () => { searchInput.value = ''; filterTable(); searchInput.focus(); });

// ── Modal Patients ────────────────────────────────────────────────────────
function viewDoctorPatients(doctorId, doctorName) {
  const modal   = document.getElementById('doctorPatientsModal');
  const spinner = document.getElementById('loadingSpinnerPatients');
  const content = document.getElementById('doctorPatientsContent');
  const empty   = document.getElementById('noResultsPatients');

  modal.classList.remove('hidden');
  spinner.classList.remove('hidden');
  content.classList.add('hidden');
  empty.classList.add('hidden');
  document.getElementById('doctorPatientsTitle').textContent = `Patients — Dr. ${doctorName}`;

  fetch(`index.php?page=admin&action=get_doctor_patients_ajax&doctor_id=${doctorId}`)
    .then(r => r.json())
    .then(data => {
      const tbody = document.getElementById('patientsTableBody');
      if (data.patients && data.patients.length > 0) {
        tbody.innerHTML = data.patients.map(p => `
          <tr class="hover:bg-blue-50/50 transition-colors">
            <td class="px-4 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                  ${p.prenom.charAt(0)}${p.nom.charAt(0)}
                </div>
                <div>
                  <p class="font-semibold text-slate-800 text-sm">${p.prenom} ${p.nom}</p>
                  <p class="text-xs text-slate-400">#PAT-${String(p.id_PK).padStart(4,'0')}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3.5 text-xs text-slate-600"><p>${p.mail}</p><p class="text-slate-400">${p.tel || 'N/A'}</p></td>
            <td class="px-4 py-3.5 text-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">${p.nb_consultations}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 font-bold text-sm">${p.nb_ordonnances}</span>
            </td>
            <td class="px-4 py-3.5 text-xs text-slate-600">
              ${p.last_consultation ? new Date(p.last_consultation).toLocaleDateString('fr-FR', {year:'numeric',month:'short',day:'numeric'}) : '—'}
            </td>
          </tr>`).join('');
        content.classList.remove('hidden');
      } else {
        empty.classList.remove('hidden');
      }
      spinner.classList.add('hidden');
    })
    .catch(() => { empty.classList.remove('hidden'); spinner.classList.add('hidden'); });
}

function closeDoctorPatientsModal() {
  document.getElementById('doctorPatientsModal').classList.add('hidden');
}
function closePatientDetailsModal() {
  document.getElementById('patientDetailsModal').classList.add('hidden');
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeDoctorPatientsModal(); closePatientDetailsModal(); }
});
document.getElementById('doctorPatientsModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeDoctorPatientsModal(); });
document.getElementById('patientDetailsModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closePatientDetailsModal(); });
</script>

</body>
</html>
