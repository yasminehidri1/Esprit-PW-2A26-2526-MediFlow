<?php
// Views/Back/dossier_medical/patients_liste.php
// Partial: rendered inside layout.php — NO html/head/body tags
$search     = htmlspecialchars($_GET['q'] ?? '');
$totalToday = $stats['total_today'] ?? 0;
$termines   = $stats['termines']    ?? 0;
$enAttente  = $stats['en_attente']  ?? 0;
$totalCount = count($patients ?? []);
?>
<div class="space-y-8 max-w-6xl mx-auto">

  <!-- Header -->
  <div class="flex flex-wrap items-center justify-between gap-4 fade-in">
    <div>
      <h1 class="font-headline text-[32px] font-extrabold text-[#1a2b4b] tracking-tight">Ma Liste de Patients</h1>
      <p class="text-[15px] font-medium text-slate-500 mt-1">Vous avez <strong class="text-slate-700"><?= $totalCount ?> dossiers</strong> enregistrés.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <!-- Filter Button (as seen in screenshot) -->
        <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">
            <span class="material-symbols-outlined text-[20px]">filter_list</span>
            Filtrer
        </button>
    </div>
  </div>

  <!-- Top Dashboard Cards -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-in" style="animation-delay: 0.1s;">
    
    <!-- Live Queue Card (Spans 2 columns) -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
      <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-bold text-[#1a2b4b] font-headline">Aperçu de la file d'attente</h2>
          <span class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[11px] font-black tracking-widest uppercase border border-emerald-100">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              En direct
          </span>
      </div>
      
      <div class="grid grid-cols-3 gap-4">
          <!-- Aujourd'hui -->
          <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100">
                  <span class="material-symbols-outlined text-blue-500">schedule</span>
              </div>
              <div>
                  <p class="text-2xl font-black text-[#1a2b4b] leading-none"><?= sprintf('%02d', $totalToday) ?></p>
                  <p class="text-xs font-semibold text-slate-500 mt-1">Aujourd'hui</p>
              </div>
          </div>
          <!-- Terminés -->
          <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center border border-emerald-100">
                  <span class="material-symbols-outlined text-emerald-500">check_circle</span>
              </div>
              <div>
                  <p class="text-2xl font-black text-[#1a2b4b] leading-none"><?= sprintf('%02d', $termines) ?></p>
                  <p class="text-xs font-semibold text-slate-500 mt-1">Terminés</p>
              </div>
          </div>
          <!-- En attente -->
          <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center border border-amber-100">
                  <span class="material-symbols-outlined text-amber-500">pending_actions</span>
              </div>
              <div>
                  <p class="text-2xl font-black text-[#1a2b4b] leading-none"><?= sprintf('%02d', $enAttente) ?></p>
                  <p class="text-xs font-semibold text-slate-500 mt-1">En attente</p>
              </div>
          </div>
      </div>
    </div>

    <!-- Total Patients Blue Card -->
    <div class="bg-[#155baf] rounded-2xl p-6 shadow-md shadow-blue-900/20 text-white relative overflow-hidden flex flex-col justify-between">
      <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[120px] opacity-10">medical_services</span>
      
      <div>
          <p class="text-blue-100 font-bold mb-1">Total Patients</p>
          <p class="text-5xl font-black font-headline tracking-tight"><?= $totalCount ?></p>
      </div>
      
      <div class="flex items-center gap-2 mt-6 bg-white/10 w-fit px-3 py-1.5 rounded-lg backdrop-blur-sm">
          <span class="material-symbols-outlined text-sm">group</span>
          <p class="text-xs font-medium text-blue-50">Dossiers actifs dans le système</p>
      </div>
    </div>

  </div>

  <!-- Search & Table Container -->
  <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,77,153,0.05)] border border-slate-50 overflow-hidden fade-in" style="animation-delay: 0.2s;">
    
    <!-- Toolbar -->
    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
      <form method="GET" action="/integration/dossier/patients" class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-full px-5 py-2.5 w-full max-w-md transition-all focus-within:border-blue-400 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-50">
        <span class="material-symbols-outlined text-slate-400">search</span>
        <input type="text" name="q" value="<?= $search ?>" placeholder="Rechercher un patient (nom, email)..."
               class="bg-transparent text-sm font-medium w-full outline-none text-slate-700 placeholder:text-slate-400"/>
      </form>
    </div>

    <?php if (empty($patients)): ?>
    <div class="py-24 text-center">
      <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-[48px] text-slate-300">person_search</span>
      </div>
      <h3 class="font-bold text-xl text-slate-700 font-headline mb-2">Aucun patient trouvé</h3>
      <p class="text-slate-500 font-medium">Essayez un autre terme de recherche.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead>
          <tr>
            <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Patient</th>
            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Dernière visite</th>
            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400">Informations</th>
            <th class="px-8 py-5 text-[11px] font-black uppercase tracking-[0.15em] text-slate-400 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php 
          // Colors for initials and diagnostic badges to mimic the screenshot
          $avatarColors = ['bg-blue-50 text-blue-600', 'bg-amber-50 text-amber-600', 'bg-emerald-50 text-emerald-600', 'bg-rose-50 text-rose-600'];
          $badgeColors = [
              ['dot' => 'bg-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700'],
              ['dot' => 'bg-amber-500', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700'],
              ['dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
              ['dot' => 'bg-rose-500', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700'],
          ];
          
          foreach ($patients as $i => $p): 
              $initials = strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1)); 
              $colorIndex = $i % 4;
              $avaClass = $avatarColors[$colorIndex];
              $bdgClass = $badgeColors[$colorIndex];
          ?>
          <tr class="hover:bg-slate-50/50 transition-colors group">
            
            <!-- Patient Name & Email -->
            <td class="px-8 py-5">
              <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-full <?= $avaClass ?> flex items-center justify-center text-sm font-bold shrink-0">
                  <?= $initials ?>
                </div>
                <div>
                  <p class="font-bold text-[#1a2b4b] text-[15px] font-headline mb-0.5"><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></p>
                  <p class="text-[12px] font-medium text-slate-400">ID: #<?= str_pad($p['id_PK'],2,'0',STR_PAD_LEFT) ?> &bull; <?= htmlspecialchars($p['mail']) ?></p>
                </div>
              </div>
            </td>
            
            <!-- Dernière Visite -->
            <td class="px-6 py-5">
              <?php if ($p['derniere_visite']): ?>
                  <p class="font-bold text-slate-700 text-[13px]"><?= date('d M Y', strtotime($p['derniere_visite'])) ?></p>
                  <p class="text-[11px] font-medium text-slate-400 mt-0.5">Consultation</p>
              <?php else: ?>
                  <p class="font-bold text-slate-400 text-[13px] italic">-</p>
              <?php endif; ?>
            </td>
            
            <!-- Informations (Mocking diagnostic pills as requested in design) -->
            <td class="px-6 py-5">
                <span class="inline-flex items-center gap-2 <?= $bdgClass['bg'] ?> <?= $bdgClass['text'] ?> px-3 py-1.5 rounded-full text-xs font-bold">
                    <span class="w-1.5 h-1.5 rounded-full <?= $bdgClass['dot'] ?>"></span>
                    <?= (int)$p['nb_consultations'] ?> Consultation(s)
                </span>
            </td>
            
            <!-- Actions -->
            <td class="px-8 py-5 text-right">
              <div class="flex items-center justify-end gap-5">
                <a href="/integration/dossier/ordonnances?patient_id=<?= $p['id_PK'] ?>"
                   class="text-[13px] font-bold text-blue-600 hover:text-blue-800 transition-colors">
                  Voir l'ordonnance
                </a>
                
                <a href="/integration/dossier/view?patient_id=<?= $p['id_PK'] ?>"
                   class="flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-100 hover:border-slate-300 transition-all">
                  <span class="material-symbols-outlined text-[18px]">folder_shared</span>
                  Dossier Médical
                </a>
              </div>
            </td>
            
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Page <?= $page ?> sur <?= $totalPages ?></p>
      <div class="flex gap-2">
        <?php for ($pg = 1; $pg <= $totalPages; $pg++): ?>
        <a href="/integration/dossier/patients?p=<?= $pg ?><?= $search ? '&q='.urlencode($search) : '' ?>"
           class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold transition-all <?= $pg === $page ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
          <?= $pg ?>
        </a>
        <?php endfor ?>
      </div>
    </div>
    <?php endif ?>
    <?php endif ?>
    
  </div>
</div>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.fade-in { opacity: 0; animation: fadeIn 0.4s ease forwards; }
</style>
