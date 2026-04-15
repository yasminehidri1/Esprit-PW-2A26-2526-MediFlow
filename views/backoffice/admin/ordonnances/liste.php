<?php
/**
 * Admin Prescriptions List View
 */
$activePage = 'admin';
require __DIR__ . '/../helpers/avatar.php';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Gestion des Ordonnances — MediFlow Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "tertiary-container": "#00736a",
            "on-primary-fixed-variant": "#00468c",
            "surface": "#f7f9fb",
            "surface-tint": "#005db7",
            "on-secondary-container": "#475c80",
            "surface-container-highest": "#e0e3e5",
            "on-tertiary-container": "#87f8ea",
            "background": "#f7f9fb",
            "error": "#ba1a1a",
            "on-primary-container": "#dae5ff",
            "secondary-container": "#c0d5ff",
            "on-tertiary-fixed": "#00201d",
            "on-tertiary": "#ffffff",
            "secondary-fixed-dim": "#b2c7f1",
            "secondary": "#4a5f83",
            "tertiary-fixed": "#84f5e8",
            "on-error-container": "#93000a",
            "inverse-on-surface": "#eff1f3",
            "secondary-fixed": "#d6e3ff",
            "surface-container-low": "#f2f4f6",
            "on-primary-fixed": "#001b3d",
            "inverse-surface": "#2d3133",
            "on-surface-variant": "#424752",
            "tertiary-fixed-dim": "#66d9cc",
            "surface-bright": "#f7f9fb",
            "outline": "#727783",
            "surface-container-lowest": "#ffffff",
            "tertiary": "#005851",
            "primary-container": "#1565c0",
            "on-error": "#ffffff",
            "inverse-primary": "#a9c7ff",
            "surface-container": "#eceef0",
            "on-primary": "#ffffff",
            "on-secondary-fixed-variant": "#32476a",
            "on-secondary-fixed": "#021b3c",
            "primary": "#004d99",
            "error-container": "#ffdad6",
            "outline-variant": "#c2c6d4",
            "on-surface": "#191c1e",
            "surface-dim": "#d8dadc",
            "primary-fixed-dim": "#a9c7ff",
            "primary-fixed": "#d6e3ff",
            "surface-container-high": "#e6e8ea",
            "on-secondary": "#ffffff",
            "surface-variant": "#e0e3e5",
            "on-tertiary-fixed-variant": "#005049",
            "on-background": "#191c1e"
          },
          "borderRadius": {
            "DEFAULT": "0.5rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "fontFamily": {
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
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-3xl font-extrabold text-blue-900 dark:text-blue-100 tracking-tight mb-2 font-headline">Gestion des Ordonnances</h2>
    <p class="text-slate-500 font-medium">Consultez le registre de toutes les ordonnances médicales.</p>
  </div>

  <!-- Flash Message -->
  <?php if ($flash): ?>
  <div class="mb-6 p-4 rounded-lg bg-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-50 text-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-800 border border-<?php echo $flash['type'] === 'success' ? 'green' : 'blue'; ?>-200 flex justify-between items-center" id="flash">
    <span><?php echo htmlspecialchars($flash['msg']); ?></span>
    <button onclick="document.getElementById('flash').style.display='none'" class="text-sm font-bold">×</button>
  </div>
  <?php endif; ?>

  <!-- Table Card -->
  <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_30px_rgba(0,0,0,0.02)] overflow-hidden">
    <div class="p-6 bg-slate-50/50 border-b border-slate-100">
      <h3 class="text-lg font-bold text-blue-900 font-headline">Registre des Ordonnances (<?php echo number_format($totalCount); ?> total)</h3>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-50 bg-slate-50/50">
            <th class="px-6 py-4">N° Ordonnance</th>
            <th class="px-6 py-4">Patient</th>
            <th class="px-6 py-4">Médecin</th>
            <th class="px-6 py-4">Date</th>
            <th class="px-6 py-4">Statut</th>
            <th class="px-6 py-4">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php if (count($prescriptions) > 0): ?>
            <?php foreach ($prescriptions as $prescription): ?>
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4">
                <p class="text-sm font-bold text-primary">#<?php echo htmlspecialchars($prescription['numero_ordonnance'] ?? 'N/A'); ?></p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php echo getAvatarHtml($prescription['id_ordonnance'], 'P', 'P', '32px'); ?>
                  <p class="text-sm text-on-surface"><?php echo htmlspecialchars($prescription['patient_prenom'] . ' ' . $prescription['patient_nom']); ?></p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php echo getAvatarHtml($prescription['id_ordonnance'] * 2, 'D', 'M', '32px'); ?>
                  <p class="text-sm text-slate-600">Dr. <?php echo htmlspecialchars($prescription['medecin_prenom'] . ' ' . $prescription['medecin_nom']); ?></p>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-slate-600"><?php echo htmlspecialchars(date('d/m/Y', strtotime($prescription['date_emission']))); ?></p>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold <?php
                  if ($prescription['statut'] === 'active') {
                    echo 'text-green-600';
                  } elseif ($prescription['statut'] === 'archivee') {
                    echo 'text-slate-600';
                  } else {
                    echo 'text-yellow-600';
                  }
                ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?php
                    if ($prescription['statut'] === 'active') {
                      echo 'bg-green-500';
                    } elseif ($prescription['statut'] === 'archivee') {
                      echo 'bg-slate-400';
                    } else {
                      echo 'bg-yellow-500';
                    }
                  ?>"></span>
                  <?php echo htmlspecialchars(ucfirst($prescription['statut'] ?? 'Inconnu')); ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <a href="?page=admin&action=view_prescription&id=<?php echo $prescription['id_ordonnance']; ?>" class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-100 rounded-lg" title="Voir">
                    <span class="material-symbols-outlined">visibility</span>
                  </a>
                  <button onclick="openDeletePrescriptionModal(<?php echo $prescription['id_ordonnance']; ?>, '<?php echo htmlspecialchars($prescription['numero_ordonnance']); ?>')" class="text-slate-400 hover:text-error transition-colors p-2 hover:bg-red-50 rounded-lg" title="Supprimer">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                <p>Aucune ordonnance trouvée.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-50 flex justify-between items-center bg-slate-50/50">
      <div class="text-sm text-slate-500">
        Page <?php echo $page; ?> sur <?php echo $totalPages; ?> — <?php echo number_format($totalCount); ?> ordonnances
      </div>
      <div class="flex gap-2">
        <?php if ($page > 1): ?>
          <a href="?page=admin&action=prescriptions&p=<?php echo $page - 1; ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">← Précédent</a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?page=admin&action=prescriptions&p=<?php echo $page + 1; ?>" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">Suivant →</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<!-- Delete Modal -->
<div id="deletePrescriptionModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg p-6 max-w-sm">
    <h3 class="text-lg font-bold text-slate-800 mb-2">Confirmer la suppression</h3>
    <p class="text-slate-600 mb-6">Êtes-vous sûr de vouloir supprimer l'ordonnance <strong id="prescriptionNumber"></strong>?</p>
    <div class="flex gap-4">
      <button onclick="closeDeletePrescriptionModal()" class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-bold hover:bg-slate-300">Annuler</button>
      <form id="deletePrescriptionForm" method="POST" action="?page=admin&action=delete_prescription" class="flex-1">
        <input type="hidden" name="id" id="deletePrescriptionId"/>
        <button type="submit" class="w-full px-4 py-2 bg-error text-white rounded-lg font-bold hover:bg-red-700">Supprimer</button>
      </form>
    </div>
  </div>
</div>

<script>
function openDeletePrescriptionModal(id, prescriptionNumber) {
  document.getElementById('prescriptionNumber').textContent = prescriptionNumber;
  document.getElementById('deletePrescriptionId').value = id;
  document.getElementById('deletePrescriptionModal').classList.remove('hidden');
}

function closeDeletePrescriptionModal() {
  document.getElementById('deletePrescriptionModal').classList.add('hidden');
}
</script>

</body>
</html>
