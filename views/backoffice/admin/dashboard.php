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
              <th class="px-6 py-4">Patients</th>
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
                <span class="text-sm font-medium text-slate-600"><?php echo (int)$doctor['nb_patients']; ?></span>
              </td>
              <td class="px-6 py-4">
                <button onclick="viewDoctorPatients(<?php echo $doctor['id_PK']; ?>, '<?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?>')" class="text-slate-400 hover:text-blue-600 transition-colors" title="Voir les patients">
                  <span class="material-symbols-outlined">visibility</span>
                </button>
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

<!-- Modal 1: Doctor's Patients -->
<div id="doctorPatientsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
    <!-- Modal Header -->
    <div class="sticky top-0 bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold font-headline" id="doctorPatientsTitle">Patients du Médecin</h2>
        <p class="text-blue-100 text-sm mt-1">Liste complète des patients</p>
      </div>
      <button onclick="closeDoctorPatientsModal()" class="text-white hover:bg-blue-800 rounded-lg p-2 transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Modal Content -->
    <div class="p-6">
      <!-- Loading Spinner -->
      <div id="loadingSpinnerPatients" class="flex items-center justify-center py-8">
        <div class="animate-spin">
          <span class="material-symbols-outlined text-4xl text-blue-600">hourglass_empty</span>
        </div>
      </div>

      <!-- Patients Table -->
      <div id="doctorPatientsContent" class="hidden overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-200 bg-slate-50">
              <th class="px-4 py-3">Patient</th>
              <th class="px-4 py-3">Contact</th>
              <th class="px-4 py-3">Consultations</th>
              <th class="px-4 py-3">Dernière Visite</th>
              <th class="px-4 py-3 text-center">Détails</th>
            </tr>
          </thead>
          <tbody id="patientsTableBody" class="divide-y divide-slate-50">
            <!-- Will be populated by JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- No Results Message -->
      <div id="noResultsPatients" class="hidden text-center py-8">
        <p class="text-slate-500 italic">Aucun patient trouvé pour ce médecin.</p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
      <button onclick="closeDoctorPatientsModal()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
        Fermer
      </button>
    </div>
  </div>
</div>

<!-- Modal 2: Patient Details -->
<div id="patientDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
    <!-- Modal Header -->
    <div class="sticky top-0 bg-gradient-to-r from-green-900 to-green-700 text-white p-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold font-headline" id="patientDetailsTitle">Détails du Patient</h2>
        <p class="text-green-100 text-sm mt-1">Consultations et ordonnances</p>
      </div>
      <button onclick="closePatientDetailsModal()" class="text-white hover:bg-green-800 rounded-lg p-2 transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Modal Content -->
    <div class="p-6">
      <!-- Loading Spinner -->
      <div id="loadingSpinnerDetails" class="flex items-center justify-center py-8">
        <div class="animate-spin">
          <span class="material-symbols-outlined text-4xl text-green-600">hourglass_empty</span>
        </div>
      </div>

      <!-- Content Container -->
      <div id="detailsContent" class="hidden">
        <!-- Consultations Section -->
        <div class="mb-8">
          <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">assignment</span>
            Consultations Médicales
          </h3>
          <div id="consultationsContainer" class="space-y-4">
            <!-- Will be populated by JavaScript -->
          </div>
        </div>

        <!-- Prescriptions Section -->
        <div>
          <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">description</span>
            Ordonnances
          </h3>
          <div id="prescriptionsContainer" class="space-y-4">
            <!-- Will be populated by JavaScript -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
      <button onclick="closePatientDetailsModal()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors font-medium">
        Fermer
      </button>
    </div>
  </div>
</div>

<script>
// Variables globales
let currentDoctorId = null;

function viewDoctorPatients(doctorId, doctorName) {
  currentDoctorId = doctorId;
  const modal = document.getElementById('doctorPatientsModal');
  const title = document.getElementById('doctorPatientsTitle');
  const loadingSpinner = document.getElementById('loadingSpinnerPatients');
  const modalContent = document.getElementById('doctorPatientsContent');
  const noResultsMessage = document.getElementById('noResultsPatients');

  modal.classList.remove('hidden');
  loadingSpinner.classList.remove('hidden');
  modalContent.classList.add('hidden');
  noResultsMessage.classList.add('hidden');
  
  title.textContent = `Patients - Dr. ${doctorName}`;

  fetch(`index.php?page=admin&action=get_doctor_patients_ajax&doctor_id=${doctorId}`)
    .then(response => response.json())
    .then(data => {
      const patientsTableBody = document.getElementById('patientsTableBody');
      
      if (data.patients && data.patients.length > 0) {
        patientsTableBody.innerHTML = data.patients.map(patient => `
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center font-bold text-sm">
                  ${patient.prenom.charAt(0)}${patient.nom.charAt(0)}
                </div>
                <div>
                  <p class="font-bold text-slate-800">${patient.prenom} ${patient.nom}</p>
                  <p class="text-xs text-slate-500">ID: #PAT-${String(patient.id_PK).padStart(4, '0')}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm">
                <p class="text-slate-600">${patient.mail}</p>
                <p class="text-xs text-slate-500">${patient.tel || 'N/A'}</p>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm font-bold text-blue-600">${patient.nb_consultations}</span>
            </td>
            <td class="px-4 py-3">
              <span class="text-sm text-slate-600">
                ${patient.last_consultation ? new Date(patient.last_consultation).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Aucune'}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <button onclick="viewPatientDetails(${patient.id_PK}, '${patient.prenom} ${patient.nom}')" class="inline-flex items-center justify-center w-9 h-9 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">visibility</span>
              </button>
            </td>
          </tr>
        `).join('');
        
        modalContent.classList.remove('hidden');
      } else {
        noResultsMessage.classList.remove('hidden');
      }
      
      loadingSpinner.classList.add('hidden');
    })
    .catch(error => {
      console.error('Error:', error);
      noResultsMessage.classList.remove('hidden');
      loadingSpinner.classList.add('hidden');
    });
}

function closeDoctorPatientsModal() {
  document.getElementById('doctorPatientsModal').classList.add('hidden');
}

function viewPatientDetails(patientId, patientName) {
  if (!currentDoctorId) return;

  const modal = document.getElementById('patientDetailsModal');
  const title = document.getElementById('patientDetailsTitle');
  const loadingSpinner = document.getElementById('loadingSpinnerDetails');
  const content = document.getElementById('detailsContent');

  modal.classList.remove('hidden');
  loadingSpinner.classList.remove('hidden');
  content.classList.add('hidden');
  
  title.textContent = patientName;

  fetch(`index.php?page=admin&action=doctor_patient_details_ajax&patient_id=${patientId}&doctor_id=${currentDoctorId}`)
    .then(response => response.json())
    .then(data => {
      const consultationsContainer = document.getElementById('consultationsContainer');
      if (data.consultations && data.consultations.length > 0) {
        consultationsContainer.innerHTML = data.consultations.map(c => `
          <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-600">
            <div class="flex justify-between items-start mb-3">
              <div>
                <p class="font-bold text-slate-800">
                  <span class="material-symbols-outlined text-sm align-middle mr-1">calendar_today</span>
                  ${new Date(c.date_consultation).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </p>
                <p class="text-sm text-slate-600 mt-1"><strong>Type:</strong> ${c.type_consultation}</p>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-bold ${c.statut === 'Complétée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                ${c.statut}
              </span>
            </div>
            <div class="space-y-2 text-sm">
              ${c.diagnostic ? `<p><strong>Diagnostic:</strong> ${c.diagnostic}</p>` : ''}
              ${c.compte_rendu ? `<p><strong>Compte Rendu:</strong> ${c.compte_rendu}</p>` : ''}
              ${c.tension_arterielle ? `<p><strong>Tension Artérielle:</strong> ${c.tension_arterielle}</p>` : ''}
              ${c.rythme_cardiaque ? `<p><strong>Rythme Cardiaque:</strong> ${c.rythme_cardiaque} bpm</p>` : ''}
              ${c.poids ? `<p><strong>Poids:</strong> ${c.poids} kg</p>` : ''}
              ${c.saturation_o2 ? `<p><strong>Saturation O2:</strong> ${c.saturation_o2}%</p>` : ''}
            </div>
          </div>
        `).join('');
      } else {
        consultationsContainer.innerHTML = '<p class="text-slate-500 italic">Aucune consultation trouvée.</p>';
      }

      const prescriptionsContainer = document.getElementById('prescriptionsContainer');
      if (data.prescriptions && data.prescriptions.length > 0) {
        prescriptionsContainer.innerHTML = data.prescriptions.map(p => {
          let medicaments = [];
          try {
            medicaments = JSON.parse(p.medicaments || '[]');
          } catch (e) {
            medicaments = [];
          }
          
          return `
            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-600">
              <div class="flex justify-between items-start mb-3">
                <div>
                  <p class="font-bold text-slate-800">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">description</span>
                    N° ${p.numero_ordonnance || 'N/A'}
                  </p>
                  <p class="text-sm text-slate-600 mt-1">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">event</span>
                    ${new Date(p.date_emission).toLocaleDateString('fr-FR')}
                  </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold ${
                  p.statut === 'active' ? 'bg-green-100 text-green-800' : 
                  p.statut === 'archivee' ? 'bg-gray-100 text-gray-800' : 
                  'bg-red-100 text-red-800'
                }">
                  ${p.statut}
                </span>
              </div>
              <div class="space-y-2 text-sm">
                ${medicaments.length > 0 ? `
                  <div>
                    <strong>Médicaments:</strong>
                    <ul class="ml-4 mt-1 space-y-1">
                      ${medicaments.map(m => `<li class="text-slate-700">• ${m.nom || m.libelle || 'N/A'} - ${m.dosage || ''} ${m.quantite ? '(x' + m.quantite + ')' : ''}</li>`).join('')}
                    </ul>
                  </div>
                ` : ''}
                ${p.note_pharmacien ? `<p><strong>Note Pharmacien:</strong> ${p.note_pharmacien}</p>` : ''}
              </div>
            </div>
          `;
        }).join('');
      } else {
        prescriptionsContainer.innerHTML = '<p class="text-slate-500 italic">Aucune ordonnance trouvée.</p>';
      }

      loadingSpinner.classList.add('hidden');
      content.classList.remove('hidden');
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('consultationsContainer').innerHTML = '<p class="text-red-500">Erreur lors du chargement des données.</p>';
      loadingSpinner.classList.add('hidden');
      content.classList.remove('hidden');
    });
}

function closePatientDetailsModal() {
  document.getElementById('patientDetailsModal').classList.add('hidden');
}

// Close modals on escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeDoctorPatientsModal();
    closePatientDetailsModal();
  }
});

// Close modals when clicking outside
document.getElementById('doctorPatientsModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeDoctorPatientsModal();
  }
});

document.getElementById('patientDetailsModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closePatientDetailsModal();
  }
});
</script>

</body>
</html>
