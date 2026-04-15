<?php
/**
 * Patient Dashboard View
 * Following the template: dashboard_patient.html
 */
?>
<!doctype html>
<html class="light" lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Mon Dossier Patient — MediFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-tint": "#005db7",
              "surface-variant": "#e0e3e5",
              primary: "#004d99",
              surface: "#f7f9fb",
              "on-background": "#191c1e",
              "secondary-fixed": "#d6e3ff",
              "on-error": "#ffffff",
              "on-primary": "#ffffff",
              "surface-container-lowest": "#ffffff",
              error: "#ba1a1a",
              "inverse-surface": "#2d3133",
              "surface-container-low": "#f2f4f6",
              "error-container": "#ffdad6",
              "tertiary-container": "#00736a",
              "outline-variant": "#c2c6d4",
              "surface-container": "#eceef0",
              "secondary-container": "#c0d5ff",
              "on-primary-fixed-variant": "#00468c",
              "on-tertiary-fixed-variant": "#005049",
              tertiary: "#005851",
              outline: "#727783",
              "on-surface": "#191c1e",
              "on-secondary-container": "#475c80",
              "secondary-fixed-dim": "#b2c7f1",
              "on-secondary": "#ffffff",
              "on-tertiary": "#ffffff",
              "on-error-container": "#93000a",
              "on-surface-variant": "#424752",
              "inverse-primary": "#a9c7ff",
              "on-tertiary-container": "#87f8ea",
              "on-secondary-fixed-variant": "#32476a",
              "primary-container": "#1565c0",
              "surface-bright": "#f7f9fb",
              secondary: "#4a5f83",
              "tertiary-fixed": "#84f5e8",
              "on-tertiary-fixed": "#00201d",
              "on-primary-container": "#dae5ff",
              "surface-dim": "#d8dadc",
              "on-secondary-fixed": "#021b3c",
              "on-primary-fixed": "#001b3d",
              "surface-container-high": "#e6e8ea",
              "inverse-on-surface": "#eff1f3",
              "primary-fixed-dim": "#a9c7ff",
              "primary-fixed": "#d6e3ff",
              "surface-container-highest": "#e0e3e5",
              "tertiary-fixed-dim": "#66d9cc",
              background: "#f7f9fb",
            },
            borderRadius: {
              DEFAULT: "0.25rem",
              lg: "0.5rem",
              xl: "0.75rem",
              full: "9999px",
            },
            fontFamily: {
              headline: ["Manrope"],
              body: ["Inter"],
              label: ["Inter"],
            },
          },
        },
      };
    </script>
    <style>
      .material-symbols-outlined {
        font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
        vertical-align: middle;
      }
      body { font-family: "Inter", sans-serif; }
      h1, h2, h3 { font-family: "Manrope", sans-serif; }
      .tonal-transition-no-border { border: none !important; }
      .no-border { border: none !important; }
    </style>
  </head>
  <body class="bg-surface text-on-surface selection:bg-secondary-container">
    <div class="flex min-h-screen">
      <!-- SIDEBAR -->
      <aside class="h-screen w-72 flex flex-col sticky left-0 top-0 bg-[#f2f4f6] dark:bg-slate-950 tonal-transition-no-border p-6 space-y-2">
        <div class="mb-8 px-2">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white shadow-lg shadow-primary/20">
              <span class="material-symbols-outlined">health_metrics</span>
            </div>
            <div>
              <h1 class="font-['Manrope'] text-xl font-bold text-slate-900 dark:text-white leading-none">MediFlow</h1>
              <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Patient Portal</p>
            </div>
          </div>
        </div>
        <nav class="flex-1 space-y-1">
          <a class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 text-[#1565C0] font-semibold rounded-lg shadow-sm shadow-blue-900/5 relative before:content-[''] before:absolute before:left-0 before:w-1 before:h-6 before:bg-[#005851] before:rounded-full translate-x-1 duration-200 group" href="#">
            <span class="material-symbols-outlined">description</span>
            <span class="font-['Inter'] text-sm font-medium">Mon Dossier</span>
          </a>
          
        </nav>
        <div class="pt-6 border-t border-slate-200 dark:border-slate-800">
          <a href="?page=logout" class="w-full py-3 px-4 bg-gradient-to-r from-error to-red-600 text-white rounded-xl font-semibold text-sm shadow-lg shadow-error/20 flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-lg">logout</span>
            Déconnexion
          </a>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="flex-1 flex flex-col bg-surface min-h-screen overflow-y-auto">
        <!-- TOP BAR -->
        <header class="w-full sticky top-0 z-50 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl no-border shadow-sm shadow-blue-900/5 shadow-[0_4px_20px_rgba(21,101,192,0.03)] flex justify-between items-center px-10 py-3">
          <div class="flex items-center gap-4">
            <span class="text-2xl font-black tracking-tighter text-[#1565C0] dark:text-blue-400 font-['Manrope']">Mon Dossier Patient</span>
            <div class="h-6 w-[1px] bg-outline-variant/30"></div>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
              <input class="pl-10 pr-4 py-1.5 bg-surface-container-low rounded-full text-xs border-none focus:ring-2 focus:ring-tertiary/30 w-64 transition-all group-hover:bg-surface-container" placeholder="Rechercher..." type="text" />
            </div>
          </div>
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
              <div class="text-right">
                <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></p>
                <p class="text-[10px] text-slate-500 font-medium">Patient MediFlow</p>
              </div>
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white font-bold text-sm">
                <?php echo strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1)); ?>
              </div>
            </div>
          </div>
        </header>

        <!-- CONTENT -->
        <div class="p-10 space-y-8">
          <!-- PATIENT HERO CARD -->
          <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-[0_20px_50px_rgba(0,77,153,0.03)] border-l-4 border-primary relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            <div class="flex flex-wrap items-start justify-between relative z-10">
              <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white text-4xl font-bold shadow-md">
                  <?php echo strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1)); ?>
                </div>
                <div>
                  <h2 class="text-3xl font-extrabold text-on-surface tracking-tight"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></h2>
                  <p class="text-on-surface-variant flex items-center gap-4 mt-1 font-medium">
                    <span>ID: #MF-<?php echo str_pad($patient['id_PK'], 5, '0', STR_PAD_LEFT); ?></span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span><?php echo htmlspecialchars($patient['mail']); ?></span>
                  </p>
                  <div class="flex gap-2 mt-4">
                    <span class="px-3 py-1 bg-secondary-container text-on-secondary-container text-xs font-bold rounded-full">Actif</span>
                  </div>
                </div>
              </div>
              <div class="flex gap-3">
                <button id="btnModifierProfil" class="px-6 py-2.5 bg-surface-container-high hover:bg-surface-container-highest transition-colors rounded-xl text-sm font-semibold text-slate-700 flex items-center gap-2 cursor-pointer">
                  <span class="material-symbols-outlined text-lg">edit</span>
                  Modifier Profil
                </button>
                <button id="btnExporterPDF" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold shadow-md shadow-primary/20 flex items-center gap-2 hover:shadow-lg transition-all cursor-pointer">
                  <span class="material-symbols-outlined text-lg">print</span>
                  Exporter PDF
                </button>
              </div>
            </div>

            <!-- VITALS CARDS -->
            <?php if ($vitals): ?>
            <div class="grid grid-cols-4 gap-6 mt-10">
              <div class="bg-surface-container-low p-5 rounded-xl border-t-2 border-tertiary-fixed">
                <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Tension Artérielle</p>
                <div class="flex items-baseline gap-2">
                  <span class="text-2xl font-black text-on-surface"><?php echo htmlspecialchars($vitals['tension_arterielle'] ?? 'N/A'); ?></span>
                  <span class="text-xs font-medium text-slate-500">mmHg</span>
                </div>
              </div>
              <div class="bg-surface-container-low p-5 rounded-xl border-t-2 border-tertiary-fixed">
                <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Fréquence Cardiaque</p>
                <div class="flex items-baseline gap-2">
                  <span class="text-2xl font-black text-on-surface"><?php echo htmlspecialchars($vitals['rythme_cardiaque'] ?? 'N/A'); ?></span>
                  <span class="text-xs font-medium text-slate-500">BPM</span>
                </div>
              </div>
              <div class="bg-surface-container-low p-5 rounded-xl border-t-2 border-tertiary-fixed">
                <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Poids</p>
                <div class="flex items-baseline gap-2">
                  <span class="text-2xl font-black text-on-surface"><?php echo htmlspecialchars($vitals['poids'] ?? 'N/A'); ?></span>
                  <span class="text-xs font-medium text-slate-500">kg</span>
                </div>
              </div>
              <div class="bg-surface-container-low p-5 rounded-xl border-t-2 border-tertiary-fixed">
                <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Saturation O²</p>
                <div class="flex items-baseline gap-2">
                  <span class="text-2xl font-black text-on-surface"><?php echo htmlspecialchars($vitals['saturation_o2'] ?? 'N/A'); ?></span>
                  <span class="text-xs font-medium text-slate-500">%</span>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- GRID -->
          <div class="grid grid-cols-12 gap-8 items-start">
            <!-- LEFT: HISTORY & DOCS -->
            <div class="col-span-8 space-y-8">
              <!-- TIMELINE -->
              <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                  <h3 class="text-xl font-bold flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Historique Clinique
                  </h3>
                </div>
                <div class="space-y-0">
                  <?php if (!empty($consultations)): ?>
                    <?php foreach ($consultations as $index => $consultation): ?>
                    <div class="relative pl-8 pb-10 group">
                      <div class="absolute left-0 top-1 w-0.5 h-full bg-slate-200 group-last:h-0"></div>
                      <div class="absolute left-[-5px] top-1 w-3 h-3 rounded-full <?php echo $index == 0 ? 'bg-primary' : 'bg-tertiary'; ?> border-4 border-white shadow-sm"></div>
                      <div class="bg-surface-container-low rounded-xl p-5 hover:bg-white transition-all hover:shadow-lg hover:shadow-blue-900/5 cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                          <span class="text-[10px] font-black uppercase tracking-widest <?php echo $index == 0 ? 'text-primary' : 'text-tertiary'; ?>">
                            <?php echo date('d M Y', strtotime($consultation['date_consultation'])); ?> · Consultation
                          </span>
                          <span class="px-2 py-0.5 bg-white text-[10px] font-bold rounded border border-slate-100 uppercase">Visite</span>
                        </div>
                        <h4 class="font-bold text-on-surface mb-1"><?php echo htmlspecialchars($consultation['type_consultation'] ?? 'Consultation'); ?></h4>
                        <p class="text-sm text-on-surface-variant leading-relaxed"><?php echo htmlspecialchars(substr($consultation['diagnostic'] ?? 'Pas de diagnostic enregistré', 0, 100)); ?></p>
                        <div class="mt-4 flex items-center gap-3">
                          <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                            <span class="material-symbols-outlined text-base">person</span>
                            <?php echo htmlspecialchars($consultation['medecin_prenom'] . ' ' . $consultation['medecin_nom']); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="text-center py-12 text-slate-500">
                      <p class="text-sm">Aucune consultation enregistrée.</p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- RIGHT: PRESCRIPTIONS & TEAM -->
            <div class="col-span-4 space-y-8">
              <!-- CURRENT TREATMENT (MEDICATIONS) -->
              <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm">
                <h3 class="text-xl font-bold flex items-center gap-3 mb-6">
                  <span class="material-symbols-outlined text-tertiary">pill</span>
                  Traitement Actuel
                </h3>
                <div class="space-y-4">
                  <?php
                    $activeMedications = [];
                    if (!empty($prescriptions)):
                      foreach ($prescriptions as $prescription):
                        if ($prescription['statut'] === 'active'):
                          $meds = json_decode($prescription['medicaments'] ?? '[]', true);
                          foreach ($meds as $med):
                            $activeMedications[] = $med;
                          endforeach;
                        endif;
                      endforeach;
                    endif;
                  ?>
                  <?php if (!empty($activeMedications)): ?>
                    <?php foreach (array_slice($activeMedications, 0, 3) as $medication): ?>
                    <div class="p-4 rounded-xl bg-tertiary/5 border-l-4 border-tertiary">
                      <div class="flex justify-between items-start">
                        <h4 class="font-bold text-on-surface"><?php echo htmlspecialchars($medication['nom'] ?? 'N/A'); ?> <?php echo htmlspecialchars($medication['dosage'] ?? ''); ?></h4>
                        <span class="px-2 py-0.5 bg-tertiary-fixed text-[10px] font-black rounded uppercase text-on-tertiary-fixed">
                          <?php echo htmlspecialchars($medication['frequence'] ? substr($medication['frequence'], 0, 10) : 'N/A'); ?>
                        </span>
                      </div>
                      <p class="text-xs text-on-surface-variant mt-1 italic">
                        <?php echo htmlspecialchars($medication['categorie'] ?? 'Médicament'); ?> · <?php echo htmlspecialchars($medication['frequence'] ?? 'N/A'); ?>
                      </p>
                      <div class="mt-3 flex items-center gap-2 text-[10px] font-bold text-tertiary uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-sm">event_repeat</span>
                        Durée: <?php echo htmlspecialchars($medication['duree'] ?? 'N/A'); ?>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="text-center py-6 text-slate-500">
                      <p class="text-sm">Aucun traitement actif.</p>
                    </div>
                  <?php endif; ?>
                </div>
                <button id="btnNouvelleOrdonnance" class="w-full mt-6 py-3 border border-tertiary/20 text-tertiary text-sm font-bold rounded-xl hover:bg-tertiary/5 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                  <span class="material-symbols-outlined text-lg">history_edu</span>
                  Nouvelle Ordonnance
                </button>
              </div>

              <!-- CARE TEAM -->
              <div class="bg-gradient-to-br from-primary to-primary-container rounded-2xl p-8 text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                <div class="absolute -bottom-8 -right-8 opacity-10">
                  <span class="material-symbols-outlined text-9xl">medical_services</span>
                </div>
                <h3 class="text-lg font-bold mb-6 relative z-10">Mon Équipe Médicale</h3>
                <div class="space-y-4 relative z-10">
                  <?php if (!empty($doctors)): ?>
                    <?php foreach (array_slice($doctors, 0, 3) as $doctor): ?>
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">
                        <?php echo strtoupper(substr($doctor['prenom'], 0, 1) . substr($doctor['nom'], 0, 1)); ?>
                      </div>
                      <div>
                        <p class="text-sm font-bold"><?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></p>
                        <p class="text-[10px] opacity-70 font-medium"><?php echo htmlspecialchars($doctor['role_libelle'] ?? 'Médecin'); ?></p>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="text-sm">Aucun médecin assigné pour le moment.</p>
                  <?php endif; ?>
                </div>
                <button id="btnContacterEquipe" class="w-full mt-8 py-2.5 bg-white text-primary text-xs font-bold rounded-xl hover:bg-opacity-90 transition-opacity cursor-pointer">
                  Contacter l'équipe
                </button>
              </div>

              <!-- ALERTS / OBSERVATIONS -->
              <div class="bg-error-container/30 border border-error/10 rounded-2xl p-6">
                <div class="flex items-center gap-2 text-error mb-3">
                  <span class="material-symbols-outlined">warning</span>
                  <span class="text-sm font-bold">Points d'attention</span>
                </div>
                <ul class="text-xs text-on-error-container space-y-2 font-medium">
                  <?php
                    $hasAlerts = false;
                    if (!empty($consultations) && isset($consultations[0])):
                      $latestConsult = $consultations[0];
                      $allergies = json_decode($latestConsult['allergies'] ?? '[]', true);
                      if (!empty($allergies)):
                        $hasAlerts = true;
                        foreach (array_slice($allergies, 0, 2) as $allergy):
                  ?>
                  <li class="flex items-start gap-2">
                    <span class="w-1 h-1 rounded-full bg-error mt-1.5 shrink-0"></span>
                    <span>Allergie: <?php echo htmlspecialchars($allergy['nom'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($allergy['niveau'] ?? 'N/A'); ?>)</span>
                  </li>
                  <?php
                        endforeach;
                      endif;
                    endif;
                    if (!$hasAlerts):
                  ?>
                  <li class="flex items-start gap-2">
                    <span class="w-1 h-1 rounded-full bg-error mt-1.5 shrink-0"></span>
                    <span>Aucune allergie ou antécédent particulier à signaler.</span>
                  </li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- MODAL MODIFIER PROFIL -->
    <div id="modalModifierProfil" class="hidden fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 py-6 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-8 space-y-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Modifier Profil</h3>
            <button onclick="closeModal('modalModifierProfil')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>
          <form id="formModifierProfil" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Prénom</label>
              <input type="text" id="modif_prenom" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Prénom" value="<?php echo htmlspecialchars($patient['prenom']); ?>" />
              <span class="text-xs text-red-500 hidden" id="err_prenom"></span>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Nom</label>
              <input type="text" id="modif_nom" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Nom" value="<?php echo htmlspecialchars($patient['nom']); ?>" />
              <span class="text-xs text-red-500 hidden" id="err_nom"></span>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
              <input type="email" id="modif_email" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Email" value="<?php echo htmlspecialchars($patient['mail']); ?>" />
              <span class="text-xs text-red-500 hidden" id="err_email"></span>
            </div>
            <div class="flex gap-3 pt-4">
              <button type="button" onclick="closeModal('modalModifierProfil')" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">Annuler</button>
              <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL NOUVELLE ORDONNANCE -->
    <div id="modalNouvelleOrdonnance" class="hidden fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 py-6 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-2xl p-8 space-y-6 max-h-96 overflow-y-auto">
          <div class="flex justify-between items-center mb-6 sticky top-0 bg-white dark:bg-slate-900 pb-4">
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Demander une Nouvelle Ordonnance</h3>
            <button onclick="closeModal('modalNouvelleOrdonnance')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>
          <form id="formNouvelleOrdonnance" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Sélectionner un Médecin</label>
              <select id="ord_medecin" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-tertiary focus:border-transparent transition-all" required>
                <option value="">-- Sélectionner un médecin --</option>
                <?php if (!empty($doctors)): ?>
                  <?php foreach ($doctors as $doctor): ?>
                  <option value="<?php echo htmlspecialchars($doctor['id_PK']); ?>"><?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <span class="text-xs text-red-500 hidden" id="err_ord_medecin"></span>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Description du Besoin</label>
              <textarea id="ord_description" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-tertiary focus:border-transparent transition-all" placeholder="Décrivez votre besoin d'ordonnance..." maxlength="500" rows="4"></textarea>
              <div class="flex justify-between items-center mt-1">
                <span class="text-xs text-red-500 hidden" id="err_ord_description"></span>
                <span class="text-xs text-slate-500"><span id="ord_desc_count">0</span>/500</span>
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button type="button" onclick="closeModal('modalNouvelleOrdonnance')" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">Annuler</button>
              <button type="submit" class="flex-1 px-4 py-2 bg-tertiary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Demander</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL CONTACTER EQUIPE -->
    <div id="modalContacterEquipe" class="hidden fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 py-6 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-8 space-y-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Contacter l'Équipe</h3>
            <button onclick="closeModal('modalContacterEquipe')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>
          <form id="formContacterEquipe" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Sujet</label>
              <input type="text" id="contact_sujet" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Sujet du message" maxlength="100" />
              <span class="text-xs text-red-500 hidden" id="err_contact_sujet"></span>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
              <textarea id="contact_message" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Votre message..." maxlength="1000" rows="4"></textarea>
              <div class="flex justify-between items-center mt-1">
                <span class="text-xs text-red-500 hidden" id="err_contact_message"></span>
                <span class="text-xs text-slate-500"><span id="contact_msg_count">0</span>/1000</span>
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button type="button" onclick="closeModal('modalContacterEquipe')" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">Annuler</button>
              <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Envoyer</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      // Validation functions
      const VALIDATORS = {
        prenom: (val) => {
          if (!val || val.trim().length === 0) return 'Le prénom est requis';
          if (val.length < 2) return 'Au minimum 2 caractères';
          if (val.length > 50) return 'Maximum 50 caractères';
          if (!/^[a-zàâäéèêëïîôöùûüœçA-ZÀÂÄÉÈÊËÏÎÔÖÙÛÜŒÇ\s'-]+$/.test(val)) return 'Caractères invalides';
          return '';
        },
        nom: (val) => {
          if (!val || val.trim().length === 0) return 'Le nom est requis';
          if (val.length < 2) return 'Au minimum 2 caractères';
          if (val.length > 50) return 'Maximum 50 caractères';
          if (!/^[a-zàâäéèêëïîôöùûüœçA-ZÀÂÄÉÈÊËÏÎÔÖÙÛÜŒÇ\s'-]+$/.test(val)) return 'Caractères invalides';
          return '';
        },
        email: (val) => {
          if (!val || val.trim().length === 0) return 'L\'email est requis';
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return 'Email invalide';
          return '';
        },
        medecin: (val) => {
          if (!val || val.trim().length === 0) return 'Sélectionnez un médecin';
          return '';
        },
        sujet: (val) => {
          if (!val || val.trim().length === 0) return 'Le sujet est requis';
          if (val.length < 3) return 'Au minimum 3 caractères';
          if (val.length > 100) return 'Maximum 100 caractères';
          return '';
        },
        message: (val) => {
          if (!val || val.trim().length === 0) return 'Le message est requis';
          if (val.length < 10) return 'Au minimum 10 caractères';
          if (val.length > 1000) return 'Maximum 1000 caractères';
          return '';
        }
      };

      // Real-time validation
      document.getElementById('modif_prenom')?.addEventListener('input', (e) => {
        const err = VALIDATORS.prenom(e.target.value);
        const errEl = document.getElementById('err_prenom');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('modif_nom')?.addEventListener('input', (e) => {
        const err = VALIDATORS.nom(e.target.value);
        const errEl = document.getElementById('err_nom');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('modif_email')?.addEventListener('input', (e) => {
        const err = VALIDATORS.email(e.target.value);
        const errEl = document.getElementById('err_email');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('ord_medecin')?.addEventListener('change', (e) => {
        const err = VALIDATORS.medecin(e.target.value);
        const errEl = document.getElementById('err_ord_medecin');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('ord_description')?.addEventListener('input', (e) => {
        document.getElementById('ord_desc_count').textContent = e.target.value.length;
        const err = e.target.value.length > 0 && e.target.value.length < 10 ? 'Au minimum 10 caractères' : '';
        const errEl = document.getElementById('err_ord_description');
        if (err) {
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('contact_sujet')?.addEventListener('input', (e) => {
        const err = VALIDATORS.sujet(e.target.value);
        const errEl = document.getElementById('err_contact_sujet');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      document.getElementById('contact_message')?.addEventListener('input', (e) => {
        document.getElementById('contact_msg_count').textContent = e.target.value.length;
        const err = VALIDATORS.message(e.target.value);
        const errEl = document.getElementById('err_contact_message');
        if (err) {
          e.target.classList.add('border-red-500', 'ring-red-500');
          errEl.textContent = err;
          errEl.classList.remove('hidden');
        } else {
          e.target.classList.remove('border-red-500', 'ring-red-500');
          e.target.classList.add('border-green-500', 'ring-green-500');
          errEl.classList.add('hidden');
        }
      });

      // Modal functions
      function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
      }

      function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
      }

      // Button listeners
      document.getElementById('btnModifierProfil')?.addEventListener('click', () => openModal('modalModifierProfil'));
      document.getElementById('btnNouvelleOrdonnance')?.addEventListener('click', () => openModal('modalNouvelleOrdonnance'));
      document.getElementById('btnContacterEquipe')?.addEventListener('click', () => openModal('modalContacterEquipe'));

      // Form submissions
      document.getElementById('formModifierProfil')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const prenom = document.getElementById('modif_prenom').value;
        const nom = document.getElementById('modif_nom').value;
        const email = document.getElementById('modif_email').value;

        const errPrenom = VALIDATORS.prenom(prenom);
        const errNom = VALIDATORS.nom(nom);
        const errEmail = VALIDATORS.email(email);

        if (errPrenom || errNom || errEmail) {
          alert('Veuillez corriger les erreurs');
          return;
        }

        try {
          const response = await fetch('index.php?page=patient&action=update-profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prenom, nom, email })
          });
          const result = await response.json();
          if (result.success) {
            alert(result.message);
            closeModal('modalModifierProfil');
            location.reload();
          } else {
            alert('Erreur: ' + (result.error || 'Une erreur est survenue'));
          }
        } catch (err) {
          alert('Erreur de connexion');
        }
      });

      document.getElementById('formNouvelleOrdonnance')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const medecin_id = document.getElementById('ord_medecin').value;
        const description = document.getElementById('ord_description').value;

        if (!medecin_id) {
          alert('Sélectionnez un médecin');
          return;
        }

        if (!description || description.length < 10) {
          alert('Veuillez décrire votre besoin (minimum 10 caractères)');
          return;
        }

        try {
          const response = await fetch('index.php?page=patient&action=request-prescription', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ medecin_id, description })
          });
          const result = await response.json();
          if (result.success) {
            alert(result.message);
            closeModal('modalNouvelleOrdonnance');
            document.getElementById('formNouvelleOrdonnance').reset();
          } else {
            alert('Erreur: ' + (result.error || 'Une erreur est survenue'));
          }
        } catch (err) {
          alert('Erreur de connexion');
        }
      });

      document.getElementById('formContacterEquipe')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const sujet = document.getElementById('contact_sujet').value;
        const message = document.getElementById('contact_message').value;

        const errSujet = VALIDATORS.sujet(sujet);
        const errMsg = VALIDATORS.message(message);

        if (errSujet || errMsg) {
          alert('Veuillez corriger les erreurs');
          return;
        }

        try {
          const response = await fetch('index.php?page=patient&action=contact-team', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sujet, message })
          });
          const result = await response.json();
          if (result.success) {
            alert(result.message);
            closeModal('modalContacterEquipe');
            document.getElementById('formContacterEquipe').reset();
          } else {
            alert('Erreur: ' + (result.error || 'Une erreur est survenue'));
          }
        } catch (err) {
          alert('Erreur de connexion');
        }
      });

      // Export PDF functionality
      document.getElementById('btnExporterPDF')?.addEventListener('click', async () => {
        try {
          const response = await fetch('index.php?page=patient&action=export-pdf');
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'dossier_patient_' + new Date().toISOString().slice(0, 10) + '.pdf';
          a.click();
          window.URL.revokeObjectURL(url);
        } catch (err) {
          alert('Erreur lors de l\'exportation');
        }
      });

      // Close modal on backdrop click
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('fixed')) {
          if (e.target.id === 'modalModifierProfil') closeModal('modalModifierProfil');
          if (e.target.id === 'modalNouvelleOrdonnance') closeModal('modalNouvelleOrdonnance');
          if (e.target.id === 'modalContacterEquipe') closeModal('modalContacterEquipe');
        }
      });
    </script>
  </body>
</html>
