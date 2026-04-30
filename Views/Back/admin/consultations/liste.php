<?php
/**
 * Admin Consultations List View
 */
$activePage = 'admin';
require __DIR__ . '/../helpers/avatar.php';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Gestion des Consultations — MediFlow Admin</title>
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
    <h2 class="text-3xl font-extrabold text-blue-900 dark:text-blue-100 tracking-tight mb-2 font-headline">Gestion des Consultations</h2>
    <p class="text-slate-500 font-medium">Consultez l'historique de toutes les consultations médicales.</p>
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
      <h3 class="text-lg font-bold text-blue-900 font-headline">Liste des Consultations (<?php echo number_format($totalCount); ?> total)</h3>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-50 bg-slate-50/50">
            <th class="px-6 py-4">Patient</th>
            <th class="px-6 py-4">Médecin</th>
            <th class="px-6 py-4">Date</th>
            <th class="px-6 py-4">Type</th>
            <th class="px-6 py-4">Diagnostic</th>
            <th class="px-6 py-4">Statut</th>
            <th class="px-6 py-4">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php if (count($consultations) > 0): ?>
            <?php foreach ($consultations as $consultation): ?>
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php echo getAvatarHtml($consultation['id_consultation'], 'P', 'P', '32px'); ?>
                  <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($consultation['patient_prenom'] . ' ' . $consultation['patient_nom']); ?></p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php echo getAvatarHtml($consultation['id_consultation'] * 2, 'D', 'M', '32px'); ?>
                  <p class="text-sm text-slate-600">Dr. <?php echo htmlspecialchars($consultation['medecin_prenom'] . ' ' . $consultation['medecin_nom']); ?></p>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-slate-600"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($consultation['date_consultation']))); ?></p>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-medium text-slate-600"><?php echo htmlspecialchars($consultation['type_consultation'] ?? 'N/A'); ?></span>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-slate-600 max-w-xs truncate"><?php echo htmlspecialchars(substr($consultation['diagnostic'] ?? '', 0, 50)); ?>...</p>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold <?php echo $consultation['statut'] === 'Complétée' ? 'text-green-600' : 'text-yellow-600'; ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?php echo $consultation['statut'] === 'Complétée' ? 'bg-green-500' : 'bg-yellow-500'; ?>"></span>
                  <?php echo htmlspecialchars($consultation['statut']); ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <a href="?page=admin&action=view_consultation&id=<?php echo $consultation['id_consultation']; ?>" class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-100 rounded-lg" title="Voir">
                    <span class="material-symbols-outlined">visibility</span>
                  </a>
                  <button onclick="openDeleteConsultationModal(<?php echo $consultation['id_consultation']; ?>, '<?php echo htmlspecialchars($consultation['patient_prenom'] . ' ' . $consultation['patient_nom']); ?>')" class="text-slate-400 hover:text-error transition-colors p-2 hover:bg-red-50 rounded-lg" title="Supprimer">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                <p>Aucune consultation trouvée.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-50 flex justify-between items-center bg-slate-50/50">
      <div class="text-sm text-slate-500">
        Page <?php echo $page; ?> sur <?php echo $totalPages; ?> — <?php echo number_format($totalCount); ?> consultations
      </div>
      <div class="flex gap-2">
        <?php if ($page > 1): ?>
          <a href="?page=admin&action=consultations&p=<?php echo $page - 1; ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">← Précédent</a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?page=admin&action=consultations&p=<?php echo $page + 1; ?>" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors">Suivant →</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<!-- Delete Modal -->
<div id="deleteConsultationModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg p-6 max-w-sm">
    <h3 class="text-lg font-bold text-slate-800 mb-2">Confirmer la suppression</h3>
    <p class="text-slate-600 mb-6">Êtes-vous sûr de vouloir supprimer la consultation de <strong id="consultationPatient"></strong>?</p>
    <div class="flex gap-4">
      <button onclick="closeDeleteConsultationModal()" class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-bold hover:bg-slate-300">Annuler</button>
      <form id="deleteConsultationForm" method="POST" action="?page=admin&action=delete_consultation" class="flex-1">
        <input type="hidden" name="id" id="deleteConsultationId"/>
        <button type="submit" class="w-full px-4 py-2 bg-error text-white rounded-lg font-bold hover:bg-red-700">Supprimer</button>
      </form>
    </div>
  </div>
</div>

<script>
function openDeleteConsultationModal(id, patientName) {
  document.getElementById('consultationPatient').textContent = patientName;
  document.getElementById('deleteConsultationId').value = id;
  document.getElementById('deleteConsultationModal').classList.remove('hidden');
}

function closeDeleteConsultationModal() {
  document.getElementById('deleteConsultationModal').classList.add('hidden');
}
</script>

</body>
</html>
