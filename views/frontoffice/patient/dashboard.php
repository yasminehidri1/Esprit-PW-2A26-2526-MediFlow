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
                <button id="btndemandeOrdonnance" class="w-full mt-6 py-3 border border-tertiary/20 text-tertiary text-sm font-bold rounded-xl hover:bg-tertiary/5 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                  <span class="material-symbols-outlined text-lg">history_edu</span>
                  demande Ordonnance
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

    <!-- MODAL DEMANDE ORDONNANCE -->
    <div id="modaldemandeOrdonnance" class="hidden fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 py-6 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

          <!-- Header gradient -->
          <div class="bg-gradient-to-r from-tertiary to-teal-500 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3 text-white">
              <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <span class="material-symbols-outlined">history_edu</span>
              </div>
              <div>
                <h3 class="text-xl font-bold leading-none">Demande d'Ordonnance</h3>
                <p class="text-teal-100 text-xs mt-0.5">Votre demande sera envoyée au médecin sélectionné</p>
              </div>
            </div>
            <button onclick="closeModal('modaldemandeOrdonnance')" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors">
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
          </div>

          <!-- Form body -->
          <div class="px-8 py-6">

            <!-- Success state (caché par défaut) -->
            <div id="ord_success" class="hidden flex-col items-center text-center py-6 gap-4">
              <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-emerald-600" style="font-variation-settings:'FILL' 1">check_circle</span>
              </div>
              <div>
                <p class="text-lg font-bold text-slate-800">Demande envoyée !</p>
                <p class="text-sm text-slate-500 mt-1">Votre médecin recevra votre demande et vous répondra prochainement.</p>
              </div>
              <button onclick="closeModal('modaldemandeOrdonnance')" class="mt-2 px-8 py-2.5 bg-tertiary text-white rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity">
                Fermer
              </button>
            </div>

            <!-- Form content -->
            <form id="formdemandeOrdonnance" class="space-y-5">

              <!-- Sélection médecin -->
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Médecin destinataire <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">stethoscope</span>
                  <select id="ord_medecin"
                          class="w-full pl-10 pr-10 py-3 border-2 border-slate-200 rounded-xl text-sm focus:outline-none transition-all appearance-none bg-white"
                          required>
                    <option value="">— Sélectionner un médecin —</option>
                    <?php $liste = !empty($allDoctors) ? $allDoctors : $doctors; ?>
                    <?php foreach ($liste as $doctor): ?>
                    <option value="<?php echo (int)$doctor['id_PK']; ?>">
                      Dr. <?php echo htmlspecialchars($doctor['prenom'] . ' ' . $doctor['nom']); ?>
                      <?php if (!empty($doctor['role_libelle'])): ?>
                        — <?php echo htmlspecialchars($doctor['role_libelle']); ?>
                      <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <span id="ord_medecin_icon" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none text-slate-400"></span>
                </div>
                <p id="err_ord_medecin" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">error</span>
                  <span id="err_ord_medecin_text"></span>
                </p>
              </div>

              <!-- Niveau d'urgence -->
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Niveau d'urgence</label>
                <div class="flex gap-2" id="urgence_chips">
                  <button type="button" data-value="normale"
                          class="urgence-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-emerald-200 text-emerald-700 bg-emerald-50 ring-2 ring-emerald-300">
                    <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">check_circle</span>
                    Normale
                  </button>
                  <button type="button" data-value="urgent"
                          class="urgence-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-slate-200 text-slate-500 bg-white hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50">
                    <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">schedule</span>
                    Urgent
                  </button>
                  <button type="button" data-value="tres_urgent"
                          class="urgence-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all border-slate-200 text-slate-500 bg-white hover:border-red-300 hover:text-red-600 hover:bg-red-50">
                    <span class="material-symbols-outlined text-sm block mx-auto mb-0.5">emergency</span>
                    Très urgent
                  </button>
                </div>
                <input type="hidden" id="ord_urgence" value="normale"/>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Description du besoin <span class="text-red-500">*</span>
                  <span class="text-xs font-normal text-slate-400 ml-1">(10 à 500 caractères)</span>
                </label>
                <div class="relative">
                  <textarea id="ord_description"
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:outline-none transition-all resize-none"
                            placeholder="Ex : Renouvellement de mon traitement Amlodipine 5mg, ordonnance expirée depuis le 15 avril..."
                            maxlength="500" rows="4"></textarea>
                  <span id="ord_desc_icon" class="material-symbols-outlined absolute right-3 top-3 text-lg text-slate-300"></span>
                </div>

                <!-- Barre de progression -->
                <div class="mt-2 space-y-1">
                  <div class="flex justify-between items-center">
                    <p id="err_ord_description" class="hidden text-xs text-red-500 flex items-center gap-1">
                      <span class="material-symbols-outlined text-sm">error</span>
                      <span id="err_ord_description_text"></span>
                    </p>
                    <p id="ok_ord_description" class="hidden text-xs text-emerald-600 flex items-center gap-1">
                      <span class="material-symbols-outlined text-sm">check_circle</span>
                      Description valide
                    </p>
                    <span class="text-xs font-semibold ml-auto" id="ord_desc_count_label">
                      <span id="ord_desc_count">0</span><span class="text-slate-400">/500</span>
                    </span>
                  </div>
                  <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div id="ord_desc_bar" class="h-full rounded-full transition-all duration-200 bg-slate-300" style="width:0%"></div>
                  </div>
                  <p class="text-[10px] text-slate-400">Minimum requis : <span id="ord_min_indicator" class="font-bold text-slate-500">0/10 caractères</span></p>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modaldemandeOrdonnance')"
                        class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-colors">
                  Annuler
                </button>
                <button type="submit" id="ord_submit_btn" disabled
                        class="flex-1 px-4 py-3 bg-tertiary text-white rounded-xl font-semibold text-sm transition-all opacity-40 cursor-not-allowed flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-sm" id="ord_submit_icon">send</span>
                  <span id="ord_submit_label">Envoyer la demande</span>
                </button>
              </div>
            </form>
          </div>
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

      // ── Demande Ordonnance : validation temps réel ──────────────

      function setFieldState(inputEl, iconEl, errWrapEl, errTextEl, okEl, isValid, errMsg) {
        inputEl.classList.remove('border-slate-200','border-red-400','border-emerald-400');
        if (isValid === null) {
          inputEl.classList.add('border-slate-200');
          if (iconEl) iconEl.textContent = '';
          if (errWrapEl) errWrapEl.classList.add('hidden');
          if (okEl) okEl.classList.add('hidden');
        } else if (isValid) {
          inputEl.classList.add('border-emerald-400');
          if (iconEl) { iconEl.textContent = 'check_circle'; iconEl.className = iconEl.className.replace(/text-(red|slate)-\w+/g,'') + ' text-emerald-500'; }
          if (errWrapEl) errWrapEl.classList.add('hidden');
          if (okEl) okEl.classList.remove('hidden');
        } else {
          inputEl.classList.add('border-red-400');
          if (iconEl) { iconEl.textContent = 'cancel'; iconEl.className = iconEl.className.replace(/text-(emerald|slate)-\w+/g,'') + ' text-red-400'; }
          if (errWrapEl) { errWrapEl.classList.remove('hidden'); if (errTextEl) errTextEl.textContent = errMsg; }
          if (okEl) okEl.classList.add('hidden');
        }
      }

      function validateOrdForm() {
        const medecinOk = !!document.getElementById('ord_medecin').value;
        const descLen   = document.getElementById('ord_description').value.trim().length;
        const descOk    = descLen >= 10 && descLen <= 500;
        const btn       = document.getElementById('ord_submit_btn');
        if (medecinOk && descOk) {
          btn.disabled = false;
          btn.classList.remove('opacity-40','cursor-not-allowed');
          btn.classList.add('hover:opacity-90','cursor-pointer');
        } else {
          btn.disabled = true;
          btn.classList.add('opacity-40','cursor-not-allowed');
          btn.classList.remove('hover:opacity-90','cursor-pointer');
        }
      }

      document.getElementById('ord_medecin')?.addEventListener('change', (e) => {
        const isValid = !!e.target.value;
        setFieldState(
          e.target,
          document.getElementById('ord_medecin_icon'),
          document.getElementById('err_ord_medecin'),
          document.getElementById('err_ord_medecin_text'),
          null,
          isValid ? true : (e.target.value === '' ? null : false),
          'Veuillez sélectionner un médecin'
        );
        validateOrdForm();
      });

      document.getElementById('ord_description')?.addEventListener('input', (e) => {
        const len  = e.target.value.length;
        const trim = e.target.value.trim().length;

        // Compteur
        document.getElementById('ord_desc_count').textContent = len;
        document.getElementById('ord_min_indicator').textContent = Math.min(trim, 10) + '/10 caractères';

        // Barre de progression
        const bar   = document.getElementById('ord_desc_bar');
        const pct   = Math.min(len / 500 * 100, 100);
        bar.style.width = pct + '%';
        bar.className = bar.className.replace(/bg-\w+-\d+/g, '');
        if (len === 0)       bar.classList.add('bg-slate-300');
        else if (pct < 40)   bar.classList.add('bg-emerald-400');
        else if (pct < 80)   bar.classList.add('bg-amber-400');
        else                 bar.classList.add('bg-red-400');

        // Couleur du compteur
        const label = document.getElementById('ord_desc_count_label');
        label.className = label.className.replace(/text-\w+-\d+/g,'');
        if (len > 450) label.classList.add('text-red-500');
        else if (len > 350) label.classList.add('text-amber-500');
        else label.classList.add('text-slate-600');

        // Validation
        const errWrap = document.getElementById('err_ord_description');
        const errText = document.getElementById('err_ord_description_text');
        const okEl    = document.getElementById('ok_ord_description');
        const icon    = document.getElementById('ord_desc_icon');

        if (len === 0) {
          setFieldState(e.target, icon, errWrap, errText, okEl, null, '');
        } else if (trim < 10) {
          setFieldState(e.target, icon, errWrap, errText, okEl, false, 'Minimum 10 caractères requis (' + trim + '/10)');
        } else {
          setFieldState(e.target, icon, errWrap, errText, okEl, true, '');
        }
        validateOrdForm();
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
      document.getElementById('btndemandeOrdonnance')?.addEventListener('click', () => openModal('modaldemandeOrdonnance'));
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

      // ── Urgence chips ─────────────────────────────────────────────
      const chipStyles = {
        normale:     { active: 'border-emerald-300 text-emerald-700 bg-emerald-50 ring-2 ring-emerald-200', inactive: 'border-slate-200 text-slate-500 bg-white hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50' },
        urgent:      { active: 'border-amber-300 text-amber-700 bg-amber-50 ring-2 ring-amber-200',         inactive: 'border-slate-200 text-slate-500 bg-white hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50' },
        tres_urgent: { active: 'border-red-300 text-red-700 bg-red-50 ring-2 ring-red-200',                 inactive: 'border-slate-200 text-slate-500 bg-white hover:border-red-300 hover:text-red-600 hover:bg-red-50' },
      };

      document.querySelectorAll('.urgence-chip').forEach(chip => {
        chip.addEventListener('click', () => {
          const val = chip.dataset.value;
          document.getElementById('ord_urgence').value = val;
          document.querySelectorAll('.urgence-chip').forEach(c => {
            const cv = c.dataset.value;
            c.className = c.className.replace(/border-\S+|text-\S+|bg-\S+|ring-\S+/g, '').trim();
            const style = cv === val ? chipStyles[cv].active : chipStyles[cv].inactive;
            c.className += ' urgence-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all ' + style;
          });
        });
      });

      // ── Soumission demande ordonnance ─────────────────────────────
      document.getElementById('formdemandeOrdonnance')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const medecin_id  = document.getElementById('ord_medecin').value;
        const description = document.getElementById('ord_description').value.trim();
        const urgence     = document.getElementById('ord_urgence').value;

        // Sécurité double
        if (!medecin_id || description.length < 10) return;

        // État chargement
        const btn   = document.getElementById('ord_submit_btn');
        const icon  = document.getElementById('ord_submit_icon');
        const label = document.getElementById('ord_submit_label');
        btn.disabled = true;
        icon.textContent  = 'hourglass_top';
        label.textContent = 'Envoi en cours…';

        try {
          const response = await fetch('index.php?page=patient&action=request-prescription', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ medecin_id, description: '[' + urgence.toUpperCase() + '] ' + description })
          });
          const result = await response.json();

          if (result.success) {
            // Afficher l'état succès dans le modal
            document.getElementById('formdemandeOrdonnance').classList.add('hidden');
            const successEl = document.getElementById('ord_success');
            successEl.classList.remove('hidden');
            successEl.classList.add('flex');
          } else {
            icon.textContent  = 'send';
            label.textContent = 'Envoyer la demande';
            btn.disabled = false;
            alert('Erreur : ' + (result.error || 'Une erreur est survenue'));
          }
        } catch (err) {
          icon.textContent  = 'send';
          label.textContent = 'Envoyer la demande';
          btn.disabled = false;
          alert('Erreur de connexion');
        }
      });

      // Réinitialiser le modal à la fermeture
      const origCloseModal = window.closeModal;
      window.closeModal = function(id) {
        if (id === 'modaldemandeOrdonnance') {
          document.getElementById('formdemandeOrdonnance').classList.remove('hidden');
          document.getElementById('ord_success').classList.add('hidden');
          document.getElementById('ord_success').classList.remove('flex');
          document.getElementById('formdemandeOrdonnance').reset();
          document.getElementById('ord_desc_bar').style.width = '0%';
          document.getElementById('ord_desc_count').textContent = '0';
          document.getElementById('ord_min_indicator').textContent = '0/10 caractères';
          document.getElementById('ord_submit_btn').disabled = true;
          document.getElementById('ord_submit_btn').classList.add('opacity-40','cursor-not-allowed');
          document.getElementById('ord_submit_icon').textContent = 'send';
          document.getElementById('ord_submit_label').textContent = 'Envoyer la demande';
          // Remettre le chip "normale" actif
          document.getElementById('ord_urgence').value = 'normale';
          document.querySelectorAll('.urgence-chip').forEach(c => {
            const cv = c.dataset.value;
            c.className = 'urgence-chip flex-1 py-2 px-3 rounded-xl border-2 text-xs font-bold transition-all '
              + (cv === 'normale' ? chipStyles.normale.active : chipStyles[cv].inactive);
          });
        }
        document.getElementById(id).classList.add('hidden');
      };

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
          if (e.target.id === 'modaldemandeOrdonnance') closeModal('modaldemandeOrdonnance');
          if (e.target.id === 'modalContacterEquipe') closeModal('modalContacterEquipe');
        }
      });
    </script>
  </body>
</html>
