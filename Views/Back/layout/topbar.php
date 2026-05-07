<?php
/**
 * Shared Top Header Bar — included in all views.
 * Expects: $pageTitle (string), $breadcrumb (array), $medecin (array), $medecinId (int)
 */
$pageTitle  = $pageTitle  ?? 'MediFlow';
$breadcrumb = $breadcrumb ?? [];
$medecin    = $medecin    ?? ['prenom' => 'Dr.', 'nom' => 'Vance'];
$doctorName = 'Dr. ' . ($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '');

// ID de l'utilisateur connecté (médecin OU patient)
$currentUserId = (int)($_SESSION['user']['id'] ?? $medecinId ?? 0);

// Charger les notifications
$notifUnread = 0;
$notifList   = [];
if ($currentUserId > 0) {
    require_once __DIR__ . '/../../../Models/NotificationModel.php';
    $notifModel  = new NotificationModel();
    $notifUnread = $notifModel->countUnread($currentUserId);
    $notifList   = $notifModel->getByMedecin($currentUserId, 8);
}

$notifIcons = [
    'new_demande'     => ['icon' => 'assignment',  'color' => 'text-blue-500',   'bg' => 'bg-blue-50'],
    'demande_traitee' => ['icon' => 'check_circle','color' => 'text-emerald-500','bg' => 'bg-emerald-50'],
    'demande_refusee' => ['icon' => 'cancel',      'color' => 'text-red-500',    'bg' => 'bg-red-50'],
    'default'         => ['icon' => 'notifications','color' => 'text-slate-500', 'bg' => 'bg-slate-50'],
];
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

    <!-- Right side -->
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

            <!-- ── Cloche notifications ── -->
            <div class="relative" id="notif-wrapper">
                <button id="notif-btn"
                        onclick="toggleNotifDropdown()"
                        class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors"
                        title="Notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    <?php if ($notifUnread > 0): ?>
                    <span id="notif-badge"
                          class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 border-2 border-white">
                        <?= $notifUnread > 9 ? '9+' : $notifUnread ?>
                    </span>
                    <?php else: ?>
                    <span id="notif-badge" class="hidden absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 border-2 border-white"></span>
                    <?php endif ?>
                </button>

                <!-- Dropdown notifications -->
                <div id="notif-dropdown"
                     class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">

                    <!-- Header dropdown -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                        <span class="font-bold text-slate-800 text-sm">Notifications</span>
                        <?php if ($notifUnread > 0): ?>
                        <button onclick="markAllRead(<?= $currentUserId ?>)"
                                class="text-xs text-primary hover:underline font-semibold">
                            Tout marquer comme lu
                        </button>
                        <?php endif ?>
                    </div>

                    <!-- Liste -->
                    <div class="max-h-72 overflow-y-auto divide-y divide-slate-50" id="notif-list">
                        <?php if (empty($notifList)): ?>
                        <div class="px-4 py-8 text-center text-slate-400">
                            <span class="material-symbols-outlined text-3xl block mb-2 text-slate-300">notifications_none</span>
                            <p class="text-xs font-medium">Aucune notification</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($notifList as $n):
                            $cfg    = $notifIcons[$n['type']] ?? $notifIcons['default'];
                            $diff   = time() - strtotime($n['created_at']);
                            $timeAgo = $diff < 60 ? "À l'instant" : ($diff < 3600 ? floor($diff/60).' min' : ($diff < 86400 ? floor($diff/3600).'h' : date('d/m/Y', strtotime($n['created_at']))));
                            $unread = !$n['read'];
                        ?>
                        <div class="flex items-start gap-3 px-4 py-3 <?= $unread ? 'bg-blue-50/40' : 'bg-white' ?> hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-full <?= $cfg['bg'] ?> flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-sm <?= $cfg['color'] ?>"><?= $cfg['icon'] ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($n['title']) ?></p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed"><?= htmlspecialchars($n['message']) ?></p>
                                <p class="text-[10px] text-slate-400 mt-1"><?= $timeAgo ?></p>
                            </div>
                            <?php if ($unread): ?>
                            <div class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-2"></div>
                            <?php endif ?>
                        </div>
                        <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/50">
                        <a href="/integration/dossier/demandes"
                           class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                            Voir toutes les demandes
                        </a>
                    </div>
                </div>
            </div>
            <!-- ── Fin cloche ── -->

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
function headerSearch(val) {
    if (val.length >= 2) {
        clearTimeout(window._searchTimer);
        window._searchTimer = setTimeout(() => {
            window.location.href = 'index.php?page=patients&q=' + encodeURIComponent(val);
        }, 600);
    }
}

// ── Dropdown notifications ──────────────────────────────────────────
function toggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    dd.classList.toggle('hidden');
}

// Fermer en cliquant ailleurs
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('notif-dropdown')?.classList.add('hidden');
    }
});

// ── Marquer tout comme lu ───────────────────────────────────────────
async function markAllRead(medecinId) {
    await fetch('/integration/notifications/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ medecin_id: medecinId }),
    });
    // Masquer le badge
    const badge = document.getElementById('notif-badge');
    if (badge) badge.classList.add('hidden');
    // Retirer les points bleus
    document.querySelectorAll('#notif-list .bg-blue-50\\/40').forEach(el => {
        el.classList.remove('bg-blue-50/40');
    });
    document.querySelectorAll('#notif-list .w-2.h-2.bg-blue-500').forEach(el => el.remove());
    // Masquer le bouton "Tout marquer"
    event.target.style.display = 'none';
}
</script>


