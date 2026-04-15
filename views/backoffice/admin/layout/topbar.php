<?php
/**
 * Admin Topbar Layout
 */
?>
<!-- Top Bar -->
<header class="fixed top-0 w-full z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl flex justify-between items-center px-8 h-16 ml-64 shadow-[0_20px_50px_rgba(0,77,153,0.05)]">
  <div class="flex items-center w-96"></div>

  <div class="flex items-center gap-6 pr-64">
    <button class="relative p-2 text-slate-500 hover:bg-slate-100/50 rounded-full transition-colors">
      <span class="material-symbols-outlined">notifications</span>
      <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
    </button>

    <button class="p-2 text-slate-500 hover:bg-slate-100/50 rounded-full transition-colors">
      <span class="material-symbols-outlined">settings</span>
    </button>

    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
      <div class="text-right">
        <p class="text-sm font-bold text-blue-900 dark:text-blue-100 leading-tight"><?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?></p>
        <p class="text-[11px] text-slate-500 font-medium">Superviseur MediFlow</p>
      </div>
      <img alt="Administrator Profile" class="w-10 h-10 rounded-full object-cover shadow-sm ring-2 ring-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBesnyR_Do3VOwv6nrAKo04HpYIKaa73NbIQYTFiyafo8r_TL77eEyLxQAyIo_05e8E90IzH5dvqGro51xXEXMFxVbxYGADymQ2sBWHIOTW5CTVeSGJ07TDiIUQ5PkC3quceHrodUbw8c2AzEiZ9mwdfxlUg9IzACVPsIkWncwjJLC2v1E0hAp7LYhyfquP07U5cw0WfiUuKr8iLUdADzn7jqBoBrqWQnSS_SYvlV9DDnFBRLqXYf4YCuxLEwMd7UcLQu0YtvZ3_wM"/>
    </div>
  </div>
</header>
