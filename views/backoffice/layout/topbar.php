<?php
/**
 * Shared Top Header Bar — included in all views.
 * Expects: $pageTitle (string), $breadcrumb (array), $medecin (array)
 */
$pageTitle  = $pageTitle  ?? 'MediFlow';
$breadcrumb = $breadcrumb ?? [];
$medecin    = $medecin    ?? ['prenom' => 'Dr.', 'nom' => 'Vance'];
$doctorName = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');
?>
<header class="fixed top-0 left-64 right-0 z-30 bg-white/70 backdrop-blur-xl h-16 flex justify-between items-center px-8 shadow-[0_20px_50px_rgba(0,77,153,0.05)] border-b border-slate-100/80">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm font-medium text-on-surface-variant">
        <a href="index.php?page=patients" class="text-slate-400 hover:text-primary transition-colors">
            Espace Praticien
        </a>
        <?php foreach ($breadcrumb as $i => $crumb): ?>
            <span class="material-symbols-outlined text-xs text-slate-300">chevron_right</span>
            <?php if ($i < count($breadcrumb) - 1): ?>
                <a href="<?= htmlspecialchars($crumb['url'] ?? '#') ?>"
                   class="text-slate-500 hover:text-primary transition-colors">
                    <?= htmlspecialchars($crumb['label']) ?>
                </a>
            <?php else: ?>
                <span class="text-primary font-semibold"><?= htmlspecialchars($crumb['label']) ?></span>
            <?php endif ?>
        <?php endforeach ?>
    </div>

    <!-- Right side: Search + Notifications + User -->
    <div class="flex items-center gap-5">

        <!-- Search -->
        <div class="relative hidden md:block">
            <input id="header-search"
                   type="text"
                   placeholder="Rechercher un patient..."
                   class="w-60 bg-surface-container-low border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                   onkeyup="headerSearch(this.value)" />
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
        </div>

        <div class="flex items-center gap-2 border-l border-slate-100 pl-5">
            <!-- Notifications -->
            <button class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors" title="Notifications">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
            </button>

            <!-- Settings -->
            <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors" title="Paramètres">
                <span class="material-symbols-outlined">settings</span>
            </button>

            <!-- Doctor Avatar -->
            <div class="flex items-center gap-3 ml-1 pl-4 border-l border-slate-100">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-blue-900 leading-none"><?= htmlspecialchars($doctorName) ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Médecin Généraliste</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white font-bold text-sm ring-2 ring-primary/20">
                    <?= strtoupper(substr($medecin['prenom'] ?? 'D', 0, 1) . substr($medecin['nom'] ?? 'V', 0, 1)) ?>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Global header search — redirects to patient list with query
function headerSearch(val) {
    if (val.length >= 2) {
        clearTimeout(window._searchTimer);
        window._searchTimer = setTimeout(() => {
            window.location.href = 'index.php?page=patients&q=' + encodeURIComponent(val);
        }, 600);
    }
}
</script>
