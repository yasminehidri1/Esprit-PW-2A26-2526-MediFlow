<?php
/**
 * Admin Dashboard View
 */
$activePage = 'admin';
require __DIR__ . '/helpers/avatar.php';

// Pre-compute totals for CSS-based stat bars
$totalConsultTypes = array_sum(array_column($consultationsByType, 'count')) ?: 1;
$totalPrescStatus  = array_sum(array_column($prescriptionsByStatus, 'count')) ?: 1;
$totalDoctorPats   = max(array_column($topDoctors, 'nb_patients')) ?: 1;
$completionPct     = $consultationCompletion['total'] > 0
    ? round(($consultationCompletion['completees'] / $consultationCompletion['total']) * 100)
    : 0;

$typeColors = [
    'Urgence'  => ['bar' => 'bg-red-500',    'badge' => 'bg-red-50 text-red-700 border-red-100',    'icon' => 'emergency'],
    'Suivi'    => ['bar' => 'bg-teal-500',   'badge' => 'bg-teal-50 text-teal-700 border-teal-100', 'icon' => 'repeat'],
    'Contrôle' => ['bar' => 'bg-blue-500',   'badge' => 'bg-blue-50 text-blue-700 border-blue-100', 'icon' => 'event_available'],
    'Standard' => ['bar' => 'bg-slate-400',  'badge' => 'bg-slate-50 text-slate-600 border-slate-200','icon' => 'stethoscope'],
];
$statusColors = [
    'active'   => ['bar' => 'bg-emerald-500', 'label' => 'Active',    'badge' => 'bg-emerald-50 text-emerald-700'],
    'archivee' => ['bar' => 'bg-slate-400',   'label' => 'Archivée',  'badge' => 'bg-slate-50 text-slate-600'],
    'annulee'  => ['bar' => 'bg-red-400',     'label' => 'Annulée',   'badge' => 'bg-red-50 text-red-600'],
];
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
    .kpi-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.07); }
    .stat-bar { transition: width 0.6s ease; }
    .ring-track {
      background: conic-gradient(var(--fill-color) var(--pct), #f1f5f9 0);
      border-radius: 50%;
    }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<?php require __DIR__ . '/layout/sidebar.php'; ?>
<?php require __DIR__ . '/layout/topbar.php'; ?>

<main class="ml-64 pt-24 pb-12 px-8 min-h-screen">

  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-3xl font-extrabold text-blue-900 tracking-tight mb-1 font-headline">Tableau de Bord</h2>
    <p class="text-slate-500 text-sm font-medium">Vue d'ensemble du réseau MediFlow • <?php echo date('l d F Y'); ?></p>
  </div>

  <!-- Flash -->
  <?php if ($flash): ?>
  <div class="mb-6 p-4 rounded-lg bg-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-50 text-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-800 border border-<?php echo $flash['type'] === 'success' ? 'green' : ($flash['type'] === 'info' ? 'blue' : 'yellow'); ?>-200 flex justify-between items-center" id="flash">
    <span><?php echo htmlspecialchars($flash['msg']); ?></span>
    <button onclick="document.getElementById('flash').style.display='none'" class="text-sm font-bold">×</button>
  </div>
  <script>setTimeout(() => { const f = document.getElementById('flash'); if (f) f.style.display = 'none'; }, 4000);</script>
  <?php endif; ?>

  <!-- ── KPI Row (5 cartes) ─────────────────────────────────────────── -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-8">

    <div class="kpi-card bg-white p-5 rounded-xl border-t-4 border-primary shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <div class="p-2 bg-blue-50 rounded-lg"><span class="material-symbols-outlined text-primary text-xl">medical_services</span></div>
        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($doctorStats['growth']); ?></span>
      </div>
      <p class="text-2xl font-extrabold text-on-surface"><?php echo number_format($doctorStats['total_doctors']); ?></p>
      <p class="text-xs text-slate-500 mt-1">Médecins actifs</p>
    </div>

    <div class="kpi-card bg-white p-5 rounded-xl border-t-4 border-violet-500 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <div class="p-2 bg-violet-50 rounded-lg"><span class="material-symbols-outlined text-violet-600 text-xl">group</span></div>
        <span class="text-[11px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">Réseau</span>
      </div>
      <p class="text-2xl font-extrabold text-on-surface"><?php echo number_format($totalPatients); ?></p>
      <p class="text-xs text-slate-500 mt-1">Patients enregistrés</p>
    </div>

    <div class="kpi-card bg-white p-5 rounded-xl border-t-4 border-teal-500 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <div class="p-2 bg-teal-50 rounded-lg"><span class="material-symbols-outlined text-teal-600 text-xl">stethoscope</span></div>
        <span class="text-[11px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($consultationStats['status']); ?></span>
      </div>
      <p class="text-2xl font-extrabold text-on-surface"><?php echo number_format($consultationStats['active_count']); ?></p>
      <p class="text-xs text-slate-500 mt-1">Consultations aujourd'hui</p>
    </div>

    <div class="kpi-card bg-white p-5 rounded-xl border-t-4 border-secondary shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <div class="p-2 bg-slate-100 rounded-lg"><span class="material-symbols-outlined text-secondary text-xl">medication</span></div>
        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($prescriptionStats['badge']); ?></span>
      </div>
      <p class="text-2xl font-extrabold text-on-surface"><?php echo number_format($prescriptionStats['total_prescriptions']); ?></p>
      <p class="text-xs text-slate-500 mt-1">Ordonnances totales</p>
    </div>

    <div class="kpi-card bg-primary p-5 rounded-xl shadow-lg text-white relative overflow-hidden">
      <div class="absolute -right-3 -bottom-3 opacity-10"><span class="material-symbols-outlined text-[80px]">payments</span></div>
      <div class="flex items-center justify-between mb-3 relative z-10">
        <div class="p-2 bg-white/20 rounded-lg"><span class="material-symbols-outlined text-white text-xl">trending_up</span></div>
        <span class="text-[11px] font-bold text-blue-100 bg-white/20 px-2 py-0.5 rounded-full">+5.2%</span>
      </div>
      <p class="text-2xl font-extrabold relative z-10"><?php echo htmlspecialchars($revenueStats['revenue']); ?></p>
      <p class="text-xs text-white/70 mt-1 relative z-10">Revenus estimés</p>
    </div>
  </div>

  <!-- ── Bento principal ─────────────────────────────────────────────── -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

    <!-- Médecins les + actifs -->
    <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="px-6 py-4 flex justify-between items-center border-b border-slate-100 bg-slate-50/60">
        <h3 class="text-base font-bold text-blue-900 font-headline">Médecins les + actifs</h3>
        <a href="index.php?page=admin&action=doctors" class="text-xs font-bold text-primary hover:underline">Voir tout →</a>
      </div>
      <table class="w-full text-left text-sm">
        <thead>
          <tr class="text-[10px] uppercase tracking-widest text-slate-400 font-bold border-b border-slate-50">
            <th class="px-6 py-3">Médecin</th>
            <th class="px-6 py-3 text-center">Patients</th>
            <th class="px-6 py-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php foreach ($featuredDoctors as $doctor): ?>
          <tr class="hover:bg-blue-50/20 transition-colors">
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-3">
                <?php echo getAvatarHtml($doctor['id_PK'], $doctor['prenom'], $doctor['nom']); ?>
                <div>
                  <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></p>
                  <p class="text-xs text-slate-400">#DR-<?php echo str_pad($doctor['id_PK'], 4, '0', STR_PAD_LEFT); ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-3.5 text-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm"><?php echo (int)$doctor['nb_patients']; ?></span>
            </td>
            <td class="px-6 py-3.5 text-center">
              <button onclick="viewDoctorPatients(<?php echo $doctor['id_PK']; ?>, '<?php echo htmlspecialchars(addslashes($doctor['prenom'] . ' ' . $doctor['nom'])); ?>')"
                class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-blue-800 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                <span class="material-symbols-outlined text-sm">visibility</span>Voir
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Flux des Consultations + Ordonnances -->
    <div class="lg:col-span-5 flex flex-col gap-5">
      <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-tertiary overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
          <h3 class="text-base font-bold text-blue-900 font-headline">Flux des Consultations</h3>
        </div>
        <div class="p-4 space-y-2">
          <?php foreach ($recentConsultations as $c): ?>
          <a href="?page=admin&action=view_consultation&id=<?php echo $c['id_consultation']; ?>"
             class="p-3 rounded-lg bg-slate-50 flex items-center gap-3 group hover:bg-teal-50/60 transition-colors">
            <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center rounded-lg bg-white shadow-sm text-tertiary font-bold text-xs border border-slate-100">
              <?php echo date('H:i', strtotime($c['date_consultation'])); ?>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-on-surface truncate"><?php echo htmlspecialchars($c['patient_prenom'] . ' ' . $c['patient_nom']); ?></p>
              <p class="text-[11px] text-slate-500">Dr. <?php echo htmlspecialchars($c['medecin_prenom'] . ' ' . $c['medecin_nom']); ?> •
                <span class="<?php echo $c['urgence'] === 'Urgent' ? 'text-red-500' : 'text-tertiary'; ?> font-medium"><?php echo htmlspecialchars($c['urgence']); ?></span>
              </p>
            </div>
            <span class="material-symbols-outlined text-slate-300 text-sm group-hover:text-tertiary transition-colors">arrow_forward_ios</span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="bg-blue-900 text-white p-5 rounded-xl shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10"><span class="material-symbols-outlined text-[80px]">description</span></div>
        <div class="relative z-10">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-secondary-fixed text-lg">verified</span>
            <h3 class="text-sm font-bold font-headline">Registre des Ordonnances</h3>
          </div>
          <div class="space-y-2.5">
            <div class="flex justify-between items-center py-2 border-b border-white/10">
              <span class="text-xs text-white/70">Émises aujourd'hui</span>
              <span class="text-sm font-bold"><?php echo (int)$prescriptionData['validated_today']; ?> ordonnances</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-white/10">
              <span class="text-xs text-white/70">En attente signature</span>
              <span class="text-sm font-bold"><?php echo (int)$prescriptionData['pending_signature']; ?> dossiers</span>
            </div>
            <div class="flex justify-between items-center py-2">
              <span class="text-xs text-white/70">Actives</span>
              <span class="text-sm font-bold text-emerald-300"><?php echo (int)$prescriptionData['active_count']; ?></span>
            </div>
          </div>
          <a href="index.php?page=admin&action=prescriptions" class="mt-4 block w-full py-2 bg-white text-blue-900 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors text-center">Rapport hebdomadaire →</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Statistiques Analytiques (CSS-only) ────────────────────────── -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- 1. Activité hebdomadaire + taux de complétion -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
      <div class="flex items-center gap-2 mb-5">
        <span class="material-symbols-outlined text-primary">calendar_month</span>
        <h3 class="text-base font-bold text-blue-900 font-headline">Cette semaine</h3>
      </div>

      <!-- Comparison: cette semaine vs semaine passée -->
      <div class="mb-6">
        <div class="flex items-end gap-3 mb-2">
          <span class="text-4xl font-extrabold text-on-surface"><?php echo $weeklyComparison['cette_semaine']; ?></span>
          <div class="mb-1">
            <?php
            $pct = $weeklyComparison['trend_pct'];
            $up  = $pct >= 0;
            ?>
            <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-0.5 rounded-full <?php echo $up ? 'text-emerald-700 bg-emerald-50' : 'text-red-600 bg-red-50'; ?>">
              <span class="material-symbols-outlined text-sm"><?php echo $up ? 'arrow_upward' : 'arrow_downward'; ?></span>
              <?php echo abs($pct); ?>%
            </span>
            <p class="text-xs text-slate-400 mt-0.5">vs semaine passée (<?php echo $weeklyComparison['semaine_passee']; ?>)</p>
          </div>
        </div>
        <p class="text-xs text-slate-500">Consultations effectuées</p>
      </div>

      <!-- Taux de complétion -->
      <div>
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs font-semibold text-slate-600">Dossiers complétés</span>
          <span class="text-xs font-bold text-primary"><?php echo $completionPct; ?>%</span>
        </div>
        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden mb-1">
          <div class="h-full bg-primary rounded-full stat-bar" style="width: <?php echo $completionPct; ?>%"></div>
        </div>
        <div class="flex justify-between text-[11px] text-slate-400">
          <span><?php echo (int)$consultationCompletion['completees']; ?> complétées</span>
          <span><?php echo (int)$consultationCompletion['en_attente']; ?> en attente</span>
        </div>
      </div>

      <!-- Anneau de progression -->
      <div class="flex items-center justify-center mt-6">
        <div class="relative w-24 h-24">
          <div class="ring-track w-24 h-24" style="--fill-color: #004d99; --pct: <?php echo $completionPct; ?>deg;
               background: conic-gradient(#004d99 <?php echo $completionPct * 3.6; ?>deg, #f1f5f9 0deg);">
          </div>
          <div class="absolute inset-2 bg-white rounded-full flex flex-col items-center justify-center">
            <span class="text-lg font-extrabold text-primary"><?php echo $completionPct; ?>%</span>
            <span class="text-[9px] text-slate-400 font-medium leading-tight text-center">Taux<br>complétion</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Types de consultations -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
      <div class="flex items-center gap-2 mb-5">
        <span class="material-symbols-outlined text-teal-600">bar_chart_4_bars</span>
        <h3 class="text-base font-bold text-blue-900 font-headline">Types de consultations</h3>
      </div>
      <p class="text-xs text-slate-400 mb-5">Total : <strong class="text-on-surface"><?php echo number_format($totalConsultTypes); ?></strong> consultations</p>

      <div class="space-y-4">
        <?php foreach ($consultationsByType as $row):
          $type = $row['type_label'];
          $cnt  = (int)$row['count'];
          $pct  = round(($cnt / $totalConsultTypes) * 100);
          $col  = $typeColors[$type] ?? $typeColors['Standard'];
        ?>
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
              <span class="material-symbols-outlined text-sm <?php echo str_replace('bg-', 'text-', explode(' ', $col['bar'])[0]); ?>"><?php echo $col['icon']; ?></span>
              <?php echo htmlspecialchars($type); ?>
            </span>
            <span class="text-xs font-bold text-slate-600"><?php echo $cnt; ?> <span class="text-slate-400 font-normal">(<?php echo $pct; ?>%)</span></span>
          </div>
          <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full <?php echo $col['bar']; ?> rounded-full stat-bar" style="width: <?php echo $pct; ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($consultationsByType)): ?>
          <p class="text-sm text-slate-400 text-center py-4">Aucune donnée disponible.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- 3. Ordonnances par statut + Top médecins -->
    <div class="flex flex-col gap-5">

      <!-- Ordonnances par statut -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center gap-2 mb-4">
          <span class="material-symbols-outlined text-secondary">description</span>
          <h3 class="text-sm font-bold text-blue-900 font-headline">Statuts des ordonnances</h3>
        </div>
        <div class="space-y-3">
          <?php foreach ($prescriptionsByStatus as $row):
            $status = $row['statut'];
            $cnt    = (int)$row['count'];
            $pct    = round(($cnt / $totalPrescStatus) * 100);
            $col    = $statusColors[$status] ?? ['bar' => 'bg-slate-400', 'label' => ucfirst($status), 'badge' => 'bg-slate-50 text-slate-600'];
          ?>
          <div>
            <div class="flex justify-between items-center mb-1">
              <span class="text-xs font-semibold text-slate-600"><?php echo htmlspecialchars($col['label']); ?></span>
              <span class="text-xs font-bold <?php echo $col['badge']; ?> px-2 py-0.5 rounded-full"><?php echo $cnt; ?></span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full <?php echo $col['bar']; ?> rounded-full stat-bar" style="width: <?php echo $pct; ?>%"></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($prescriptionsByStatus)): ?>
            <p class="text-xs text-slate-400 text-center py-2">Aucune donnée.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Top Médecins par patients -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex-1">
        <div class="flex items-center gap-2 mb-4">
          <span class="material-symbols-outlined text-primary">leaderboard</span>
          <h3 class="text-sm font-bold text-blue-900 font-headline">Top 5 médecins</h3>
        </div>
        <div class="space-y-3">
          <?php foreach ($topDoctors as $i => $doc):
            $nb  = (int)$doc['nb_patients'];
            $pct = $totalDoctorPats > 0 ? round(($nb / $totalDoctorPats) * 100) : 0;
            $rankColors = ['text-yellow-500', 'text-slate-400', 'text-amber-600', 'text-slate-500', 'text-slate-400'];
          ?>
          <div class="flex items-center gap-3">
            <span class="text-sm font-extrabold <?php echo $rankColors[$i] ?? 'text-slate-400'; ?> w-4"><?php echo $i + 1; ?></span>
            <div class="flex-1 min-w-0">
              <div class="flex justify-between items-center mb-0.5">
                <span class="text-xs font-semibold text-slate-700 truncate">Dr. <?php echo htmlspecialchars($doc['prenom'] . ' ' . $doc['nom']); ?></span>
                <span class="text-xs font-bold text-primary ml-2 flex-shrink-0"><?php echo $nb; ?></span>
              </div>
              <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full stat-bar" style="width: <?php echo $pct; ?>%"></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($topDoctors)): ?>
            <p class="text-xs text-slate-400 text-center py-2">Aucun médecin.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- Modal: Doctor's Patients -->
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

<script>
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

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDoctorPatientsModal(); });
document.getElementById('doctorPatientsModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeDoctorPatientsModal();
});
</script>

</body>
</html>
