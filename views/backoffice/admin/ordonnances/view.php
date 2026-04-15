<?php
/**
 * Admin Prescription Details View
 */
$activePage = 'admin';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Détails Ordonnance — MediFlow Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "primary": "#004d99",
            "tertiary": "#005851",
            "secondary": "#4a5f83",
            "error": "#ba1a1a",
            "surface": "#f7f9fb",
            "surface-container-lowest": "#ffffff",
            "on-surface": "#191c1e"
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Manrope', sans-serif; }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<?php require __DIR__ . '/../layout/sidebar.php'; ?>
<?php require __DIR__ . '/../layout/topbar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 pt-24 pb-12 px-4 lg:px-8 min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
  <div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <a href="?page=admin&action=prescriptions" class="inline-flex items-center gap-2 text-primary font-bold hover:text-blue-700 mb-4 transition">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
        <span class="hidden sm:inline">Retour aux Ordonnances</span>
        <span class="sm:hidden">Retour</span>
      </a>
      <div>
        <h1 class="text-2xl lg:text-4xl font-bold text-blue-900 mb-1">Détails Ordonnance</h1>
        <p class="text-sm lg:text-base text-slate-600">Ordonnance #<?php echo htmlspecialchars($prescription['numero_ordonnance']); ?></p>
      </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <!-- Patient Info Card -->
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600">person</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Patient</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nom</p>
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($prescription['patient_prenom'] . ' ' . $prescription['patient_nom']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email</p>
            <p class="text-sm font-semibold text-slate-900 break-all"><?php echo htmlspecialchars($prescription['patient_mail']); ?></p>
          </div>
        </div>
      </div>

      <!-- Doctor Info Card -->
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-green-600">medical_services</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Médecin</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nom</p>
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($prescription['medecin_prenom'] . ' ' . $prescription['medecin_nom']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email</p>
            <p class="text-sm font-semibold text-slate-900 break-all"><?php echo htmlspecialchars($prescription['medecin_mail']); ?></p>
          </div>
        </div>
      </div>

      <!-- Prescription Details Card -->
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-purple-600">description</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Détails Ordonnance</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Date d'émission</p>
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars(date('d/m/Y', strtotime($prescription['date_emission']))); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Statut</p>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full <?php
              if ($prescription['statut'] === 'active') {
                echo 'bg-green-100 text-green-700';
              } elseif ($prescription['statut'] === 'archivee') {
                echo 'bg-slate-200 text-slate-700';
              } else {
                echo 'bg-yellow-100 text-yellow-700';
              }
            ?>">
              <span class="w-2 h-2 rounded-full <?php
                if ($prescription['statut'] === 'active') {
                  echo 'bg-green-500';
                } elseif ($prescription['statut'] === 'archivee') {
                  echo 'bg-slate-400';
                } else {
                  echo 'bg-yellow-500';
                }
              ?>"></span>
              <span class="text-sm font-bold"><?php echo htmlspecialchars(ucfirst($prescription['statut'])); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Medications Card -->
      <?php if (!empty($medicaments)): ?>
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600">medication</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Médicaments (<?php echo count($medicaments); ?>)</h2>
        </div>
        <div class="space-y-4">
          <?php foreach ($medicaments as $med): ?>
          <div class="p-4 lg:p-6 bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl border border-blue-200 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($med['nom'] ?? ''); ?></h3>
              <span class="px-3 py-1 bg-blue-200 text-blue-900 text-xs font-bold rounded-full">Médicament</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-xs text-slate-600 font-bold mb-1">DOSAGE</p>
                <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($med['dosage'] ?? 'N/A'); ?></p>
              </div>
              <div>
                <p class="text-xs text-slate-600 font-bold mb-1">FRÉQUENCE</p>
                <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($med['frequence'] ?? 'N/A'); ?></p>
              </div>
              <div>
                <p class="text-xs text-slate-600 font-bold mb-1">DURÉE</p>
                <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($med['duree'] ?? 'N/A'); ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Notes -->
      <?php if ($prescription['note_pharmacien']): ?>
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600">notes</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Notes du Pharmacien</h2>
        </div>
        <div class="p-4 lg:p-6 bg-amber-50 rounded-xl border border-amber-200">
          <p class="text-slate-700 leading-relaxed"><?php echo htmlspecialchars($prescription['note_pharmacien']); ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Actions -->
      <div class="p-6 lg:p-8 bg-slate-50 flex flex-col sm:flex-row gap-3 flex-wrap items-center">
        <a href="?page=admin&action=prescriptions" class="flex items-center justify-center gap-2 px-6 py-3 bg-slate-300 hover:bg-slate-400 text-slate-800 rounded-lg font-bold transition-all transform hover:scale-105">
          <span class="material-symbols-outlined">arrow_back</span>
          <span>Retour</span>
        </a>

        <!-- Status Update -->
        <form method="POST" action="?page=admin&action=update_prescription_status" class="flex gap-2 w-full sm:w-auto">
          <input type="hidden" name="id" value="<?php echo $prescription['id_ordonnance']; ?>"/>
          <select name="status" class="px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary font-semibold text-sm">
            <option value="active" <?php echo $prescription['statut'] === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="archivee" <?php echo $prescription['statut'] === 'archivee' ? 'selected' : ''; ?>>Archivée</option>
            <option value="annulee" <?php echo $prescription['statut'] === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
          </select>
          <button type="submit" class="flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-blue-700 text-white rounded-lg font-bold transition-all transform hover:scale-105 whitespace-nowrap">
            <span class="material-symbols-outlined">save</span>
            <span>Mettre à jour</span>
          </button>
        </form>

        <!-- Delete Button -->
        <form method="POST" action="?page=admin&action=delete_prescription" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance?');" class="ml-auto w-full sm:w-auto">
          <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-error hover:bg-red-700 text-white rounded-lg font-bold transition-all transform hover:scale-105">
            <span class="material-symbols-outlined">delete</span>
            <span>Supprimer</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</main>

</body>
</html>
