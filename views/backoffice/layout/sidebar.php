<?php
/**
 * Shared Sidebar — included in all views.
 * Expects: $activePage (string), $medecinId (int), $medecin (array)
 */
$activePage = $activePage ?? '';

function sidebarLink(string $label, string $icon, string $page, string $activePage, array $params = []): string {
    $query  = http_build_query(array_merge(['page' => $page], $params));
    $href   = "index.php?{$query}";
    $isActive = ($activePage === $page);
    $base   = "flex items-center gap-3 px-4 py-3 font-inter text-sm font-medium transition-all";
    $cls    = $isActive
        ? "{$base} bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 rounded-lg shadow-sm border-l-4 border-teal-500"
        : "{$base} text-slate-600 dark:text-slate-400 hover:translate-x-1 hover:bg-white/50 rounded-lg";
    $fillStyle = $isActive ? "font-variation-settings:'FILL' 1;" : '';
    return "<a class=\"{$cls}\" href=\"{$href}\">
              <span class=\"material-symbols-outlined\" style=\"{$fillStyle}\">{$icon}</span>
              <span>{$label}</span>
            </a>";
}
?>
<aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 dark:bg-slate-950 flex flex-col py-6 px-4 z-40">

    <!-- Logo / Brand -->
    <div class="mb-10 px-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-lg">
                <span class="material-symbols-outlined">health_metrics</span>
            </div>
            <div>
                <h2 class="text-lg font-black text-blue-900 dark:text-blue-50 leading-none">Dr. Workspace</h2>
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mt-1">Espace Praticien</p>
            </div>
        </div>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 space-y-1">
        <?= sidebarLink('Mes Patients',  'folder_shared', 'patients',         $activePage) ?>
        <?= sidebarLink('Ordonnances',   'prescriptions', 'ordonnances_list', $activePage) ?>
        <?php
        // Badge demandes en attente
        $pendingCount = 0;
        if (isset($medecinId)) {
            require_once __DIR__ . '/../../../models/DemandeOrdonnanceModel.php';
            $pendingCount = (new DemandeOrdonnanceModel())->countPendingByMedecin((int)$medecinId);
        }
        $isActive = ($activePage === 'demandes');
        $base     = "flex items-center gap-3 px-4 py-3 font-inter text-sm font-medium transition-all";
        $cls      = $isActive
            ? "{$base} bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 rounded-lg shadow-sm border-l-4 border-teal-500"
            : "{$base} text-slate-600 dark:text-slate-400 hover:translate-x-1 hover:bg-white/50 rounded-lg";
        $fillStyle = $isActive ? "font-variation-settings:'FILL' 1;" : '';
        echo "<a class=\"{$cls}\" href=\"index.php?page=demandes\">
                <span class=\"material-symbols-outlined\" style=\"{$fillStyle}\">assignment</span>
                <span class=\"flex-1\">Demandes d'ordonnance</span>"
            . ($pendingCount > 0
                ? "<span class=\"ml-auto bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[18px] text-center\">{$pendingCount}</span>"
                : '')
            . "</a>";
        ?>
    </nav>

    <!-- Bottom Actions -->
    <div class="mt-auto border-t border-slate-200 pt-6 space-y-1">
        <a href="index.php?page=nouvelle_consultation"
           class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container text-white rounded-xl py-3 font-semibold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all mb-3">
            <span class="material-symbols-outlined text-sm">add_circle</span>
            Nouvelle Consultation
        </a>
        <a href="index.php?page=patient"
           class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-tertiary to-teal-600 text-white rounded-xl py-3 font-semibold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all mb-3">
            <span class="material-symbols-outlined text-sm">person</span>
            Portal Patient
        </a>
        <a href="#" class="flex items-center gap-3 text-slate-500 px-4 py-3 hover:translate-x-1 hover:bg-white/50 transition-all rounded-lg text-sm font-medium">
            <span class="material-symbols-outlined">help_outline</span>
            <span>Support</span>
        </a>
        <a href="index.php?page=logout"
           class="flex items-center gap-3 text-error px-4 py-3 hover:translate-x-1 hover:bg-red-50 transition-all rounded-lg text-sm font-medium">
            <span class="material-symbols-outlined">logout</span>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>
