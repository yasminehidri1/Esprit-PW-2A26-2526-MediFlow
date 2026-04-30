<?php
/**
 * Admin Consultation Details View
 */
$activePage = 'admin';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Détails Consultation — MediFlow Admin</title>
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
      <a href="?page=admin&action=consultations" class="inline-flex items-center gap-2 text-primary font-bold hover:text-blue-700 mb-4 transition">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
        <span class="hidden sm:inline">Retour aux Consultations</span>
        <span class="sm:hidden">Retour</span>
      </a>
      <div>
        <h1 class="text-2xl lg:text-4xl font-bold text-blue-900 mb-1">Détails Consultation</h1>
        <p class="text-sm lg:text-base text-slate-600">Consultation #<?php echo $consultation['id_consultation']; ?></p>
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
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($consultation['patient_prenom'] . ' ' . $consultation['patient_nom']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email</p>
            <p class="text-sm font-semibold text-slate-900 break-all"><?php echo htmlspecialchars($consultation['patient_mail']); ?></p>
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
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($consultation['medecin_prenom'] . ' ' . $consultation['medecin_nom']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email</p>
            <p class="text-sm font-semibold text-slate-900 break-all"><?php echo htmlspecialchars($consultation['medecin_mail']); ?></p>
          </div>
        </div>
      </div>

      <!-- Consultation Details Card -->
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-purple-600">description</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Détails Médicaux</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Date</p>
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($consultation['date_consultation']))); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Type</p>
            <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($consultation['type_consultation']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl sm:col-span-2">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Diagnostic</p>
            <p class="text-base font-semibold text-slate-900"><?php echo htmlspecialchars($consultation['diagnostic']); ?></p>
          </div>
          <div class="bg-slate-50 p-4 rounded-xl sm:col-span-2">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Compte Rendu</p>
            <p class="text-base text-slate-700"><?php echo htmlspecialchars($consultation['compte_rendu'] ?? 'N/A'); ?></p>
          </div>
        </div>
      </div>

      <!-- Vitals Card -->
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-red-600">favorite</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Signes Vitaux</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-gradient-to-br from-red-50 to-red-100/50 p-4 rounded-xl border border-red-200">
            <p class="text-xs text-red-600 font-bold uppercase tracking-wide mb-2">Tension Artérielle</p>
            <p class="text-2xl font-bold text-red-900"><?php echo htmlspecialchars($consultation['tension_arterielle'] ?? 'N/A'); ?></p>
          </div>
          <div class="bg-gradient-to-br from-orange-50 to-orange-100/50 p-4 rounded-xl border border-orange-200">
            <p class="text-xs text-orange-600 font-bold uppercase tracking-wide mb-2">Rythme Cardiaque</p>
            <p class="text-2xl font-bold text-orange-900"><?php echo htmlspecialchars($consultation['rythme_cardiaque'] ?? 'N/A'); ?> <span class="text-sm">bpm</span></p>
          </div>
          <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 rounded-xl border border-blue-200">
            <p class="text-xs text-blue-600 font-bold uppercase tracking-wide mb-2">Poids</p>
            <p class="text-2xl font-bold text-blue-900"><?php echo htmlspecialchars($consultation['poids'] ?? 'N/A'); ?> <span class="text-sm">kg</span></p>
          </div>
          <div class="bg-gradient-to-br from-green-50 to-green-100/50 p-4 rounded-xl border border-green-200">
            <p class="text-xs text-green-600 font-bold uppercase tracking-wide mb-2">Saturation O²</p>
            <p class="text-2xl font-bold text-green-900"><?php echo htmlspecialchars($consultation['saturation_o2'] ?? 'N/A'); ?><span class="text-sm">%</span></p>
          </div>
        </div>
      </div>

      <!-- Antecedents & Allergies -->
      <?php if (!empty($antecedents) || !empty($allergies)): ?>
      <div class="border-b border-slate-100 p-6 lg:p-8 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-yellow-600">history</span>
          </div>
          <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Antécédents & Allergies</h2>
        </div>

        <?php if (!empty($antecedents)): ?>
        <div class="mb-6">
          <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center text-xs font-bold text-yellow-700">!</span>
            Antécédents
          </h3>
          <div class="space-y-3">
            <?php foreach ($antecedents as $ant): ?>
            <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200">
              <p class="font-bold text-slate-800"><?php echo htmlspecialchars($ant['titre'] ?? ''); ?> <span class="text-sm text-slate-500">(<?php echo htmlspecialchars($ant['annee'] ?? ''); ?>)</span></p>
              <p class="text-sm text-slate-600 mt-2"><?php echo htmlspecialchars($ant['description'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($allergies)): ?>
        <div>
          <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center text-xs font-bold">⚠</span>
            Allergies
          </h3>
          <div class="space-y-3">
            <?php foreach ($allergies as $allergy): ?>
            <div class="p-4 bg-red-50 rounded-xl border-2 border-red-300">
              <p class="font-bold text-red-900"><?php echo htmlspecialchars($allergy['nom'] ?? ''); ?></p>
              <p class="text-sm text-red-700 mt-1">Niveau de sévérité: <span class="font-semibold"><?php echo htmlspecialchars($allergy['niveau'] ?? ''); ?></span></p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Actions -->
      <div class="p-6 lg:p-8 bg-slate-50 flex flex-col sm:flex-row gap-3 flex-wrap">
        <a href="?page=admin&action=consultations" class="flex items-center justify-center gap-2 px-6 py-3 bg-slate-300 hover:bg-slate-400 text-slate-800 rounded-lg font-bold transition-all transform hover:scale-105">
          <span class="material-symbols-outlined">arrow_back</span>
          <span>Retour</span>
        </a>

        <!-- Delete Button -->
        <form method="POST" action="?page=admin&action=delete_consultation" style="display:inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette consultation?');" class="ml-auto w-full sm:w-auto">
          <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-error hover:bg-red-700 text-white rounded-lg font-bold transition-all transform hover:scale-105">
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
