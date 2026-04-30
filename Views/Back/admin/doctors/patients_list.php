<?php
/**
 * Doctor's Patients List View
 */
$activePage = 'admin';
require __DIR__ . '/../helpers/avatar.php';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Patients - MediFlow Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#004d99",
            "secondary": "#4a5f83",
            "tertiary": "#005851",
            "surface": "#f7f9fb",
            "on-surface": "#191c1e"
          },
          borderRadius: {
            "DEFAULT": "0.5rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          fontFamily: {
            "headline": ["Manrope"],
            "body": ["Inter"],
            "label": ["Inter"]
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<?php require __DIR__ . '/../layout/sidebar.php'; ?>
<?php require __DIR__ . '/../layout/topbar.php'; ?>

<!-- Main Content -->
<main class="ml-64 pt-24 pb-12 px-8 min-h-screen">
  <!-- Breadcrumb & Header -->
  <div class="mb-8">
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-3">
      <a href="index.php?page=admin" class="hover:text-blue-600 transition-colors">Dashboard</a>
      <span class="material-symbols-outlined text-sm">chevron_right</span>
      <a href="index.php?page=admin&action=doctors" class="hover:text-blue-600 transition-colors">Médecins</a>
      <span class="material-symbols-outlined text-sm">chevron_right</span>
      <span class="text-slate-700 font-semibold">Patients</span>
    </div>
    
    <div class="flex justify-between items-start mb-2">
      <div>
        <h1 class="text-3xl font-extrabold text-blue-900 font-headline">Patients de Dr. <?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></h1>
        <p class="text-slate-500 mt-2">Gestion et suivi des patients du médecin</p>
      </div>
      <a href="index.php?page=admin&action=doctors" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors font-medium">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Retour
      </a>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Patients -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
      <div class="flex justify-between items-start mb-3">
        <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
          <span class="material-symbols-outlined">people</span>
        </div>
      </div>
      <h3 class="text-sm text-slate-600 font-medium mb-1">Total Patients</h3>
      <p class="text-3xl font-bold text-slate-800"><?php echo count($patients); ?></p>
    </div>

    <!-- Total Consultations -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
      <div class="flex justify-between items-start mb-3">
        <div class="p-3 bg-green-50 rounded-lg text-green-600">
          <span class="material-symbols-outlined">assignment</span>
        </div>
      </div>
      <h3 class="text-sm text-slate-600 font-medium mb-1">Consultations</h3>
      <p class="text-3xl font-bold text-slate-800"><?php echo array_sum(array_map(fn($p) => $p['nb_consultations'], $patients)); ?></p>
    </div>

    <!-- Total Prescriptions -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
      <div class="flex justify-between items-start mb-3">
        <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
          <span class="material-symbols-outlined">description</span>
        </div>
      </div>
      <h3 class="text-sm text-slate-600 font-medium mb-1">Ordonnances</h3>
      <p class="text-3xl font-bold text-slate-800"><?php echo array_sum(array_map(fn($p) => $p['nb_ordonnances'], $patients)); ?></p>
    </div>
  </div>

  <!-- Patients Table -->
  <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
      <h2 class="text-lg font-bold text-slate-800">Liste des Patients</h2>
    </div>

    <?php if (!empty($patients)): ?>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead>
          <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100 bg-slate-50">
            <th class="px-6 py-4">Patient</th>
            <th class="px-6 py-4">Contact</th>
            <th class="px-6 py-4 text-center">Consultations</th>
            <th class="px-6 py-4 text-center">Ordonnances</th>
            <th class="px-6 py-4">Dernière Visite</th>
            <th class="px-6 py-4">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($patients as $patient): ?>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                  <?php echo $patient['prenom'][0] . $patient['nom'][0]; ?>
                </div>
                <div>
                  <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></p>
                  <p class="text-xs text-slate-500">ID: #PAT-<?php echo str_pad($patient['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm">
                <p class="text-slate-600 flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">mail</span>
                  <?php echo htmlspecialchars($patient['mail']); ?>
                </p>
                <p class="text-xs text-slate-500 flex items-center gap-2 mt-1">
                  <span class="material-symbols-outlined text-sm">call</span>
                  <?php echo htmlspecialchars($patient['tel'] ?? 'N/A'); ?>
                </p>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-sm hover:bg-blue-200 transition-colors">
                <?php echo (int)$patient['nb_consultations']; ?>
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600 font-bold text-sm hover:bg-green-200 transition-colors">
                <?php echo (int)$patient['nb_ordonnances']; ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center gap-2 text-sm text-slate-600">
                <span class="material-symbols-outlined text-sm">calendar_today</span>
                <?php 
                  if ($patient['last_consultation']) {
                    echo date('d M Y', strtotime($patient['last_consultation']));
                  } else {
                    echo '-';
                  }
                ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <button class="inline-flex items-center justify-center w-9 h-9 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Voir détails">
                <span class="material-symbols-outlined">open_in_new</span>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="px-6 py-12 text-center">
      <span class="material-symbols-outlined text-5xl text-slate-300 flex justify-center mb-3">person_off</span>
      <p class="text-slate-500 italic">Aucun patient trouvé pour ce médecin.</p>
    </div>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
