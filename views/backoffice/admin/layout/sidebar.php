<?php
/**
 * Admin Sidebar Layout
 */
?>
<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-full w-64 z-50 bg-slate-50 dark:bg-slate-950 flex flex-col py-6 px-4 gap-2 font-manrope tracking-tight">
  <div class="flex items-center gap-3 px-2 mb-8">
    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white shadow-lg">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">medical_services</span>
    </div>
    <div>
      <h1 class="text-lg font-extrabold text-blue-800 dark:text-blue-200 leading-none">MediFlow</h1>
      <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Admin Console</p>
    </div>
  </div>

  <nav class="flex-1 space-y-1">
    <a class="flex items-center gap-3 px-4 py-3 <?php echo (($_GET['action'] ?? '') === 'dashboard' || ($_GET['action'] ?? '') === '') ? 'text-blue-700 dark:text-blue-300 font-bold bg-white dark:bg-slate-900 rounded-lg shadow-sm border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:translate-x-1'; ?> transition-transform duration-200" href="index.php?page=admin&action=dashboard">
      <span class="material-symbols-outlined">dashboard</span>
      Dashboard
    </a>

    <a class="flex items-center gap-3 px-4 py-3 <?php echo (($_GET['action'] ?? '') === 'doctors') ? 'text-blue-700 dark:text-blue-300 font-bold bg-white dark:bg-slate-900 rounded-lg shadow-sm border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:translate-x-1'; ?> transition-transform duration-200" href="index.php?page=admin&action=doctors">
      <span class="material-symbols-outlined">medical_services</span>
      Doctors
    </a>
  </nav>

  <div class="pt-6 mt-6 border-t border-slate-200 dark:border-slate-800 space-y-1">
    <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:translate-x-1 transition-transform duration-200" href="#">
      <span class="material-symbols-outlined">help_outline</span>
      Support
    </a>
    <a class="flex items-center gap-3 px-4 py-3 text-error dark:text-red-400 hover:translate-x-1 transition-transform duration-200" href="index.php?page=logout">
      <span class="material-symbols-outlined">logout</span>
      Logout
    </a>
  </div>
</aside>
