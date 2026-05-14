<?php
/**
 * Admin Logs View (Premium Aesthetic)
 */
?>
<style>
    /* ── Custom Animations & Effects ── */
    @keyframes fadeInSlideUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-enter {
        animation: fadeInSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    
    <?php for($i = 1; $i <= 15; $i++): ?>
    .stagger-row:nth-child(<?= $i ?>) {
        animation-delay: <?= $i * 0.04 ?>s;
    }
    <?php endfor; ?>

    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.8) 100%);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 4px 24px -4px rgba(0, 77, 153, 0.08), 0 1px 4px -1px rgba(0, 77, 153, 0.04);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    
    .filter-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.8);
    }

    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(241, 245, 249, 0.4) 0%, rgba(248, 250, 252, 0.8) 100%);
        box-shadow: inset 2px 0 0 #004d99;
    }
    
    /* Custom Scrollbar for payload details */
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,77,153,0.15); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,77,153,0.25); }
    
    details > summary { list-style: none; }
    details > summary::-webkit-details-marker { display: none; }
    details[open] summary .expand-icon { transform: rotate(180deg); }
</style>

<div class="mb-8 animate-enter" style="animation-delay: 0s;">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black font-headline tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400 mb-1">
                Audit Trail
            </h2>
            <p class="text-on-surface-variant text-sm font-medium">Surveillance complète des activités et événements système.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <div class="bg-primary-fixed/30 text-primary px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Live Logging Active
            </div>
            <button onclick="window.location.reload()" class="bg-white border border-outline-variant/40 hover:bg-surface-container hover:border-outline-variant/60 text-on-surface px-4 py-2 rounded-xl transition-all flex items-center gap-2 font-semibold text-sm shadow-sm active:scale-95">
                <span class="material-symbols-outlined text-lg">refresh</span>
            </button>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="glass-card rounded-2xl p-5 mb-6 animate-enter" style="animation-delay: 0.1s;">
    <form method="GET" action="/integration/admin" class="flex flex-wrap items-end gap-4">
        <input type="hidden" name="action" value="logs">
        
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Type d'action</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">bolt</span>
                <select name="action_type" class="filter-glass w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer appearance-none">
                    <option value="">Toutes les actions</option>
                    <option value="LOGIN" <?= ($_GET['action_type'] ?? '') === 'LOGIN' ? 'selected' : '' ?>>Login</option>
                    <option value="CREATE" <?= ($_GET['action_type'] ?? '') === 'CREATE' ? 'selected' : '' ?>>Create</option>
                    <option value="UPDATE" <?= ($_GET['action_type'] ?? '') === 'UPDATE' ? 'selected' : '' ?>>Update</option>
                    <option value="DELETE" <?= ($_GET['action_type'] ?? '') === 'DELETE' ? 'selected' : '' ?>>Delete</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">expand_more</span>
            </div>
        </div>

        <div class="flex-1 min-w-[140px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Module</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">view_module</span>
                <select name="module" class="filter-glass w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer appearance-none">
                    <option value="">Tous les modules</option>
                    <option value="AUTH" <?= ($_GET['module'] ?? '') === 'AUTH' ? 'selected' : '' ?>>Auth</option>
                    <option value="DOSSIER" <?= ($_GET['module'] ?? '') === 'DOSSIER' ? 'selected' : '' ?>>Dossier Médical</option>
                    <option value="USERS" <?= ($_GET['module'] ?? '') === 'USERS' ? 'selected' : '' ?>>Utilisateurs</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">expand_more</span>
            </div>
        </div>

        <div class="flex-[1.5] min-w-[280px]">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Période</label>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">calendar_today</span>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" class="filter-glass w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer">
                </div>
                <span class="text-slate-300 font-bold px-1">→</span>
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">calendar_today</span>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" class="filter-glass w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer">
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-[0_4px_12px_rgba(0,77,153,0.3)] hover:shadow-[0_6px_16px_rgba(0,77,153,0.4)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filtrer
            </button>
            <?php if(!empty($_GET['action_type']) || !empty($_GET['module']) || !empty($_GET['date_from']) || !empty($_GET['date_to'])): ?>
            <a href="/integration/admin?action=logs" class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="glass-card rounded-2xl overflow-hidden animate-enter" style="animation-delay: 0.2s;">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200/60">
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Horodatage</th>
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Acteur</th>
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Module</th>
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Action</th>
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest w-1/3">Description & Détails</th>
                    <th class="px-5 py-4 text-[11px] font-extrabold text-slate-500 uppercase tracking-widest whitespace-nowrap">Réseau</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100/80">
                <?php if (!empty($logs)): ?>
                    <?php foreach($logs as $log): ?>
                    <tr class="animate-enter stagger-row table-row-hover group transition-all duration-200 bg-white/40">
                        <!-- Timestamp -->
                        <td class="px-5 py-4 align-top">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-bold text-slate-700 whitespace-nowrap"><?= date('d M Y', strtotime($log['created_at'])) ?></span>
                                <span class="text-xs text-slate-400 font-mono whitespace-nowrap bg-slate-100/50 w-fit px-1.5 rounded"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                            </div>
                        </td>
                        
                        <!-- User -->
                        <td class="px-5 py-4 align-top">
                            <?php if ($log['user_id']): ?>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 border border-blue-200/50 flex items-center justify-center text-primary text-xs font-bold shadow-inner">
                                        <?= strtoupper(substr($log['prenom'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($log['prenom'] . ' ' . $log['nom']) ?></span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?= htmlspecialchars($log['role'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-2 text-slate-400">
                                    <span class="material-symbols-outlined text-lg opacity-50">smart_toy</span>
                                    <span class="italic text-xs font-medium">Système</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Module -->
                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md font-bold text-[10px] uppercase tracking-widest border border-slate-200/60 shadow-sm">
                                <span class="material-symbols-outlined text-[14px] normal-case">
                                    <?= $log['module'] === 'AUTH' ? 'key' : ($log['module'] === 'DOSSIER' ? 'medical_information' : 'category') ?>
                                </span>
                                <?= htmlspecialchars($log['module']) ?>
                            </span>
                        </td>
                        
                        <!-- Action -->
                        <td class="px-5 py-4 align-top">
                            <?php 
                                $actionColors = [
                                    'CREATE' => 'bg-emerald-50 text-emerald-600 border-emerald-200/60 shadow-[0_2px_10px_rgba(16,185,129,0.1)]',
                                    'UPDATE' => 'bg-blue-50 text-blue-600 border-blue-200/60 shadow-[0_2px_10px_rgba(59,130,246,0.1)]',
                                    'DELETE' => 'bg-rose-50 text-rose-600 border-rose-200/60 shadow-[0_2px_10px_rgba(244,63,94,0.1)]',
                                    'LOGIN'  => 'bg-purple-50 text-purple-600 border-purple-200/60 shadow-[0_2px_10px_rgba(168,85,247,0.1)]',
                                    'LOGOUT' => 'bg-slate-100 text-slate-600 border-slate-200/60 shadow-sm'
                                ];
                                $badgeStyle = $actionColors[$log['action_type']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            ?>
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border <?= $badgeStyle ?>">
                                <?= htmlspecialchars($log['action_type']) ?>
                            </span>
                        </td>
                        
                        <!-- Description & Payload -->
                        <td class="px-5 py-4 align-top">
                            <div class="text-slate-700 font-medium leading-relaxed mb-1.5"><?= htmlspecialchars($log['description']) ?></div>
                            <?php if (!empty($log['payload'])): ?>
                                <details class="group/details mt-2">
                                    <summary class="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-primary/5 hover:bg-primary/10 px-2 py-1 rounded cursor-pointer transition-colors select-none">
                                        <span class="material-symbols-outlined text-[14px]">data_object</span>
                                        Voir le payload JSON
                                        <span class="material-symbols-outlined text-[14px] expand-icon transition-transform duration-200">expand_more</span>
                                    </summary>
                                    <div class="mt-2 p-3 bg-[#0d1117] rounded-xl border border-slate-700/50 shadow-inner overflow-hidden">
                                        <div class="custom-scrollbar overflow-x-auto pb-1">
                                            <pre class="text-[11px] font-mono leading-relaxed text-slate-300"><code><?php
                                                $decoded = json_decode($log['payload'], true);
                                                if(json_last_error() === JSON_ERROR_NONE) {
                                                    // Simple syntax highlighting for JSON keys
                                                    $jsonString = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    $jsonString = htmlspecialchars($jsonString);
                                                    $jsonString = preg_replace('/"([^"]+)":/', '<span class="text-blue-300">"$1"</span>:', $jsonString);
                                                    echo $jsonString;
                                                } else {
                                                    echo htmlspecialchars($log['payload']);
                                                }
                                            ?></code></pre>
                                        </div>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Network/Device -->
                        <td class="px-5 py-4 align-top">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-mono text-slate-500 bg-slate-100/80 px-2 py-1 rounded w-fit border border-slate-200/50">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">lan</span>
                                    <?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?>
                                </div>
                                <?php if(!empty($log['user_agent'])): ?>
                                <div class="flex items-start gap-1.5 group-hover:opacity-100 opacity-60 transition-opacity">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400 mt-0.5">devices</span>
                                    <div class="text-[10px] font-medium text-slate-500 leading-tight max-w-[150px] line-clamp-2" title="<?= htmlspecialchars($log['user_agent']) ?>">
                                        <?= htmlspecialchars($log['user_agent']) ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                <span class="material-symbols-outlined text-3xl text-slate-400">history_off</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-1">Aucun log trouvé</h3>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">Il n'y a pas d'activité enregistrée correspondant à vos critères de filtrage actuels.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer / Pagination placeholder -->
    <?php if (!empty($logs)): ?>
    <div class="px-5 py-4 border-t border-slate-200/60 bg-slate-50/50 flex items-center justify-between">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Affichage des <?= count($logs) ?> derniers événements</p>
        <div class="flex items-center gap-1">
            <button disabled class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 bg-white opacity-50 cursor-not-allowed"><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>
            <button disabled class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 bg-white opacity-50 cursor-not-allowed"><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>
        </div>
    </div>
    <?php endif; ?>
</div>
