<?php
/**
 * Admin Dashboard View
 */
$activePage = 'admin';
require __DIR__ . '/helpers/avatar.php';
?>
<!DOCTYPE html>
<html class="light" lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Admin Dashboard — MediFlow</title>
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

<?php require __DIR__ . '/layout/sidebar.php'; ?>
<?php require __DIR__ . '/layout/topbar.php'; ?>

<!-- Main Content -->
<main class="ml-64 pt-24 pb-12 px-8 min-h-screen">
  <!-- Dashboard Header -->
  <div class="mb-10">
    <h2 class="text-3xl font-extrabold text-blue-900 dark:text-blue-100 tracking-tight mb-2 font-headline">Tableau de Bord Administrateur</h2>
    <p class="text-slate-500 font-medium">Bienvenue dans votre centre de commande clinique. Voici l'état de votre réseau aujourd'hui.</p>
  </div>

  <!-- Flash Message -->
  <?php if ($flash): ?>
  <div class="mb-6 p-4 rounded-lg bg-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-50 text-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-800 border border-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-200 flex justify-between items-center" id="flash">
    <span><?php echo htmlspecialchars($flash['msg']); ?></span>
    <button onclick="document.getElementById('flash').style.display='none'" class="text-sm font-bold">×</button>
  </div>
  <script>
    setTimeout(() => {
      const flash = document.getElementById('flash');
      if (flash) flash.style.display = 'none';
    }, 4000);
  </script>
  <?php endif; ?>

  <!-- KPI Grid -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
    <!-- Total Doctors -->
    <div class="bg-surface-container-lowest p-6 rounded-xl border-t-2 border-primary shadow-[0_4px_20px_rgba(0,0,0,0.03)] group hover:translate-y-[-4px] transition-all">
      <div class="flex justify-between items-start mb-4">
        <div class="p-2 bg-primary-fixed rounded-lg text-primary">
          <span class="material-symbols-outlined">group</span>
        </div>
        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full"><?php echo htmlspecialchars($doctorStats['growth']); ?></span>
      </div>
      <h3 class="text-sm font-semibold text-slate-500 mb-1">Total Doctors</h3>
      <p class="text-2xl font-bold text-on-surface"><?php echo number_format($doctorStats['total_doctors']); ?></p>
    </div>

    <!-- Active Consultations -->
    <div class="bg-surface-container-lowest p-6 rounded-xl border-t-2 border-tertiary shadow-[0_4px_20px_rgba(0,0,0,0.03)] group hover:translate-y-[-4px] transition-all">
      <div class="flex justify-between items-start mb-4">
        <div class="p-2 bg-tertiary-fixed rounded-lg text-tertiary">
          <span class="material-symbols-outlined">stethoscope</span>
        </div>
        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full"><?php echo htmlspecialchars($consultationStats['status']); ?></span>
      </div>
      <h3 class="text-sm font-semibold text-slate-500 mb-1">Active Consultations</h3>
      <p class="text-2xl font-bold text-on-surface"><?php echo number_format($consultationStats['active_count']); ?></p>
    </div>

    <!-- Total Prescriptions -->
    <div class="bg-surface-container-lowest p-6 rounded-xl border-t-2 border-secondary shadow-[0_4px_20px_rgba(0,0,0,0.03)] group hover:translate-y-[-4px] transition-all">
      <div class="flex justify-between items-start mb-4">
        <div class="p-2 bg-secondary-fixed rounded-lg text-secondary">
          <span class="material-symbols-outlined">medication</span>
        </div>
        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full"><?php echo htmlspecialchars($prescriptionStats['badge']); ?></span>
      </div>
      <h3 class="text-sm font-semibold text-slate-500 mb-1">Total Prescriptions</h3>
      <p class="text-2xl font-bold text-on-surface"><?php echo number_format($prescriptionStats['total_prescriptions']); ?></p>
    </div>

    <!-- Revenue Overview -->
    <div class="bg-primary p-6 rounded-xl shadow-[0_20px_40px_rgba(0,77,153,0.2)] text-white relative overflow-hidden group hover:translate-y-[-4px] transition-all">
      <div class="absolute -right-4 -bottom-4 opacity-10">
        <span class="material-symbols-outlined text-[120px]">payments</span>
      </div>
      <div class="flex justify-between items-start mb-4 relative z-10">
        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-md">
          <span class="material-symbols-outlined">trending_up</span>
        </div>
      </div>
      <h3 class="text-sm font-medium text-white/80 mb-1 relative z-10">Revenue Overview</h3>
      <p class="text-2xl font-bold relative z-10"><?php echo htmlspecialchars($revenueStats['revenue']); ?></p>
    </div>
  </div>

  <!-- Bento Layout Sections -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Section 1: Gestion des Médecins (Asymmetric Left) -->
    <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl shadow-[0_4px_30px_rgba(0,0,0,0.02)] overflow-hidden">
      <div class="p-6 flex justify-between items-center bg-slate-50/50 border-b border-slate-100">
        <h3 class="text-lg font-bold text-blue-900 font-headline">Gestion des Médecins</h3>
        <a href="index.php?page=admin&action=doctors" class="text-xs font-bold text-primary hover:underline transition-all">Voir tout</a>
      </div>
      <div class="p-0">
        <table class="w-full text-left">
          <thead>
            <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-50">
              <th class="px-6 py-4">Médecin</th>
              <th class="px-6 py-4">Spécialité</th>
              <th class="px-6 py-4">Statut</th>
              <th class="px-6 py-4">Consultations</th>
              <th class="px-6 py-4">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <?php foreach ($featuredDoctors as $doctor): ?>
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <?php echo getAvatarHtml($doctor['id_PK'], $doctor['prenom'], $doctor['nom']); ?>
                  <div>
                    <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></p>
                    <p class="text-xs text-slate-500">ID: #DR-<?php echo str_pad($doctor['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-medium text-slate-600"><?php echo htmlspecialchars($doctor['role_libelle'] ?? 'N/A'); ?></span>
              </td>
              <td class="px-6 py-4">
                <span class="flex items-center gap-1.5 text-xs font-bold text-green-600">
                  <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Actif
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm font-medium text-slate-600"><?php echo (int)$doctor['nb_consultations']; ?></span>
              </td>
              <td class="px-6 py-4">
                <a href="?page=admin&action=edit_doctor&id=<?php echo $doctor['id_PK']; ?>" class="text-slate-400 hover:text-primary transition-colors">
                  <span class="material-symbols-outlined">more_vert</span>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section 2: Flux des Consultations (Asymmetric Right) -->
    <div class="lg:col-span-5 flex flex-col gap-6">
      <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_30px_rgba(0,0,0,0.02)] border-l-4 border-tertiary">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-bold text-blue-900 font-headline">Flux des Consultations</h3>
          <div class="flex gap-1">
            <span class="w-2 h-2 rounded-full bg-tertiary-fixed"></span>
            <span class="w-2 h-2 rounded-full bg-slate-200"></span>
            <span class="w-2 h-2 rounded-full bg-slate-200"></span>
          </div>
        </div>
        <div class="space-y-4">
          <?php foreach ($recentConsultations as $consultation): ?>
          <a href="?page=admin&action=view_consultation&id=<?php echo $consultation['id_consultation']; ?>" class="p-4 rounded-lg bg-surface-container-low flex items-center gap-4 group hover:bg-tertiary-fixed/10 transition-colors cursor-pointer">
            <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-lg bg-white shadow-sm text-tertiary font-bold text-sm">
              <?php echo htmlspecialchars(date('H:i', strtotime($consultation['date_consultation']))); ?>
            </div>
            <div class="flex-1">
              <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($consultation['patient_prenom'] . ' ' . $consultation['patient_nom']); ?></p>
              <p class="text-[11px] text-slate-500">avec Dr. <?php echo htmlspecialchars($consultation['medecin_prenom'] . ' ' . $consultation['medecin_nom']); ?> • <span class="text-tertiary"><?php echo htmlspecialchars($consultation['urgence']); ?></span></p>
            </div>
            <span class="material-symbols-outlined text-slate-300 group-hover:text-tertiary transition-colors">arrow_forward_ios</span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Section 3: Registre des Ordonnances -->
      <div class="bg-blue-900 text-white p-6 rounded-xl shadow-xl overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-10">
          <span class="material-symbols-outlined text-[100px]">description</span>
        </div>
        <div class="relative z-10">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-secondary-fixed">verified</span>
            <h3 class="text-md font-bold font-headline">Registre des Ordonnances</h3>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-white/10">
              <span class="text-sm text-white/70">Dernière heure</span>
              <span class="text-sm font-bold"><?php echo (int)$prescriptionData['validated_today']; ?> validées</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-white/10">
              <span class="text-sm text-white/70">En attente signature</span>
              <span class="text-sm font-bold"><?php echo (int)$prescriptionData['pending_signature']; ?> dossiers</span>
            </div>
            <div class="mt-4">
              <a href="index.php?page=admin&action=prescriptions" class="block w-full py-2 bg-white text-blue-900 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors text-center">Générer le rapport hebdo</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

</body>
</html>
