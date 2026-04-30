<?php
/**
 * Admin Users List Embedded View
 */
?>
<style>
    .message-box { animation: slideInRight 0.5s ease-out; }
    .table-container { animation: fadeIn 0.8s ease-out backwards; }
    tbody tr { animation: fadeIn 0.4s ease-out backwards; }
    tbody tr:nth-child(1) { animation-delay: 0.05s; }
    tbody tr:nth-child(2) { animation-delay: 0.1s; }
    tbody tr:nth-child(3) { animation-delay: 0.15s; }
    tbody tr:nth-child(4) { animation-delay: 0.2s; }
    tbody tr:nth-child(5) { animation-delay: 0.25s; }
    tbody tr:nth-child(n+6) { animation-delay: 0.3s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
    .action-btn { position: relative; overflow: hidden; }
    .action-btn::after {
        content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0; border-radius: 50%;
        background: rgba(255,255,255,0.5); transform: translate(-50%, -50%); transition: width 0.6s, height 0.6s;
    }
    .action-btn:hover::after { width: 300px; height: 300px; }
</style>
<div class="mb-8">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-3xl font-black bg-gradient-to-r from-primary via-primary-container to-primary-container bg-clip-text text-transparent">Gestion des Utilisateurs</h2>
        <a href="/integration/admin?action=create" class="bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add_circle</span> Ajouter Utilisateur
        </a>
    </div>
    <p class="text-on-surface-variant text-sm font-medium">Administrez tous les utilisateurs du système • Total: <span class="font-bold text-primary"><?php echo count($data['users'] ?? []); ?></span> utilisateurs</p>
</div>

            <!-- Messages -->
            <?php if (!empty($data['message'])): ?>
                <div class="message-box bg-gradient-to-r from-emerald-50 via-green-50 to-emerald-50 border-l-4 border-emerald-500 border-r border-t border-b border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg backdrop-blur-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-3xl flex-shrink-0">task_alt</span>
                    <div class="flex-1">
                        <p class="font-bold text-lg">Opération réussie</p>
                        <p class="text-sm text-emerald-700 font-medium"><?php echo htmlspecialchars($data['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['error'])): ?>
                <div class="message-box bg-gradient-to-r from-red-50 via-rose-50 to-red-50 border-l-4 border-red-500 border-r border-t border-b border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6 flex items-center gap-4 shadow-lg backdrop-blur-sm">
                    <span class="material-symbols-outlined text-red-600 text-3xl flex-shrink-0">error_outline</span>
                    <div class="flex-1">
                        <p class="font-bold text-lg">Erreur</p>
                        <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($data['error']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search and Filter Controls -->
            <div class="mb-6 bg-white rounded-xl shadow-lg p-6 border border-outline/30" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(242,244,246,0.95) 100%);">
                <form method="GET" action="/integration/admin" class="flex flex-wrap gap-4 items-end">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[250px]">
                        <label class="block text-sm font-semibold text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-primary">search</span>
                            Rechercher
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Nom, email, téléphone..." 
                            value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-2 border-outline rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 text-sm font-medium"
                            style="background: white; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.05);"
                        />
                    </div>

                    <!-- Role Filter Dropdown -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-semibold text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-primary">filter_alt</span>
                            Filtrer par rôle
                        </label>
                        <select 
                            name="role" 
                            class="w-full px-4 py-3 border-2 border-outline rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 text-sm font-medium appearance-none cursor-pointer"
                            style="background: white url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22><path fill=%22%23004d99%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22/></svg>') no-repeat right 12px center; background-size: 20px; padding-right: 40px; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.05);"
                        >
                            <option value="">-- Tous les rôles --</option>
                            <?php foreach ($data['roles'] as $role): ?>
                                <option value="<?php echo $role['id_role']; ?>" <?php echo isset($data['roleFilter']) && $data['roleFilter'] == $role['id_role'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['libelle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button 
                            type="submit"
                            class="bg-gradient-to-r from-primary to-primary-container text-on-primary px-6 py-3 rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2 action-btn shadow-lg"
                            style="border: none; cursor: pointer;"
                        >
                            <span class="material-symbols-outlined text-base">search</span>
                            Chercher
                        </button>
                        <?php if (!empty($data['search']) || !empty($data['roleFilter'])): ?>
                            <a 
                                href="/integration/admin"
                                class="bg-gradient-to-r from-surface-container to-surface-container-high text-on-surface px-6 py-3 rounded-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 font-semibold text-sm flex items-center gap-2 action-btn shadow-lg"
                                style="border: 1px solid var(--outline); text-decoration: none; display: inline-flex;"
                            >
                                <span class="material-symbols-outlined text-base">clear</span>
                                Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search Results Info -->
            <?php if (!empty($data['search']) || !empty($data['roleFilter'])): ?>
                <div class="mb-4 px-4 py-3 bg-primary/10 border-l-4 border-primary rounded-lg flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-lg">info</span>
                    <p class="text-sm text-on-surface font-medium">
                        Résultats trouvés: <strong><?php echo count($data['users']); ?></strong>
                        <?php if (!empty($data['search'])): ?>
                            pour "<strong><?php echo htmlspecialchars($data['search']); ?></strong>"
                        <?php endif; ?>
                        <?php if (!empty($data['roleFilter'])): ?>
                            dans le rôle "<strong><?php 
                                $selectedRole = array_values(array_filter($data['roles'], fn($r) => $r['id_role'] == $data['roleFilter']));
                                echo $selectedRole[0]['libelle'] ?? 'Inconnu';
                            ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            <div class="table-container" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(242,244,246,0.95) 100%); border-radius: 18px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08); overflow: hidden; border: 1px solid rgba(0, 77, 153, 0.08); backdrop-filter: blur(10px);">
                <table style="width: 100%;">
                    <tr style="background: linear-gradient(90deg, rgba(0, 77, 153, 0.12), rgba(21, 101, 192, 0.08), rgba(0, 118, 81, 0.06)); border-bottom: 2px solid rgba(0, 77, 153, 0.15);">
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Nom Complet</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Téléphone</th>
                        <th style="padding: 20px 24px; text-align: left; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Rôle</th>
                        <th style="padding: 20px 24px; text-align: center; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                        <th style="padding: 20px 24px; text-align: center; font-size: 14px; font-weight: 700; color: #191c1e; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                    <?php if (!empty($data['users'])): ?>
                        <?php foreach ($data['users'] as $user): ?>
                            <tr style="border-bottom: 1px solid rgba(0, 77, 153, 0.06); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 77, 153, 0.06)'; this.style.transform='scale(1.001)'; this.style.boxShadow='inset 0 2px 8px rgba(0, 77, 153, 0.05)'" onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                <td style="padding: 18px 24px; font-size: 14px; color: #191c1e; font-weight: 700; vertical-align: middle;"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; color: #424752; vertical-align: middle; font-weight: 500;"><?php echo htmlspecialchars($user['mail']); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; color: #191c1e; vertical-align: middle; font-weight: 500;"><?php echo htmlspecialchars($user['tel'] ?? '-'); ?></td>
                                <td style="padding: 18px 24px; font-size: 14px; vertical-align: middle;"><span style="background: linear-gradient(135deg, #d6e3ff 0%, #c0d5ff 100%); color: #004d99; font-weight: 800; padding: 10px 16px; border-radius: 20px; font-size: 12px; display: inline-block; white-space: nowrap; border: 1.5px solid rgba(0, 77, 153, 0.25); box-shadow: 0 4px 12px rgba(0, 77, 153, 0.12); text-transform: uppercase; letter-spacing: 0.4px;"><?php echo htmlspecialchars($user['role_name'] ?? 'N/A'); ?></span></td>
                                <td style="padding: 18px 24px; font-size: 14px; vertical-align: middle; text-align: center;">
                                    <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                        <span style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; font-weight: 800; padding: 8px 14px; border-radius: 20px; font-size: 11px; display: inline-block; white-space: nowrap; border: 1.5px solid rgba(6, 95, 70, 0.2); box-shadow: 0 4px 12px rgba(6, 95, 70, 0.1); text-transform: uppercase; letter-spacing: 0.5px;">
                                            <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: text-bottom; margin-right: 4px;">check_circle</span> Actif
                                        </span>
                                    <?php else: ?>
                                        <span style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; font-weight: 800; padding: 8px 14px; border-radius: 20px; font-size: 11px; display: inline-block; white-space: nowrap; border: 1.5px solid rgba(153, 27, 27, 0.2); box-shadow: 0 4px 12px rgba(153, 27, 27, 0.1); text-transform: uppercase; letter-spacing: 0.5px;">
                                            <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: text-bottom; margin-right: 4px;">block</span> Suspendu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px 24px; font-size: 14px; vertical-align: middle; text-align: center;">
                                    <a href="/integration/admin?action=edit&id=<?php echo $user['id_PK']; ?>" style="color: #004d99; background: linear-gradient(135deg, rgba(0, 77, 153, 0.08), rgba(21, 101, 192, 0.06)); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border: 1px solid rgba(0, 77, 153, 0.1); font-size: 20px; margin-right: 10px; box-shadow: 0 2px 8px rgba(0, 77, 153, 0.06);" title="Modifier" onmouseover="this.style.backgroundColor='rgba(0, 77, 153, 0.15)'; this.style.transform='scale(1.15) rotate(-5deg)'; this.style.boxShadow='0 8px 20px rgba(0, 77, 153, 0.2)'" onmouseout="this.style.backgroundColor='rgba(0, 77, 153, 0.08)'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0, 77, 153, 0.06)'"><span class="material-symbols-outlined">edit</span></a>
                                    
                                    <?php if ($user['id_PK'] != ($_SESSION['user']['id'] ?? 0)): ?>
                                        <a href="/integration/admin?action=toggle_status&id=<?php echo $user['id_PK']; ?>" style="color: <?php echo (($user['status'] ?? 'active') === 'active') ? '#b45309' : '#047857'; ?>; background: linear-gradient(135deg, <?php echo (($user['status'] ?? 'active') === 'active') ? 'rgba(180, 83, 9, 0.08), rgba(217, 119, 6, 0.06)' : 'rgba(4, 120, 87, 0.08), rgba(5, 150, 105, 0.06)'; ?>); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border: 1px solid <?php echo (($user['status'] ?? 'active') === 'active') ? 'rgba(180, 83, 9, 0.1)' : 'rgba(4, 120, 87, 0.1)'; ?>; font-size: 20px; margin-right: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);" title="<?php echo (($user['status'] ?? 'active') === 'active') ? 'Suspendre' : 'Réactiver'; ?>" onmouseover="this.style.transform='scale(1.15) rotate(5deg)'" onmouseout="this.style.transform='scale(1)'" onclick="return confirm('Êtes-vous sûr de vouloir <?php echo (($user['status'] ?? 'active') === 'active') ? 'suspendre' : 'réactiver'; ?> cet utilisateur?');"><span class="material-symbols-outlined"><?php echo (($user['status'] ?? 'active') === 'active') ? 'block' : 'check_circle'; ?></span></a>
                                    <?php endif; ?>
                                    
                                    <a href="/integration/admin?action=delete&id=<?php echo $user['id_PK']; ?>" style="color: #ba1a1a; background: linear-gradient(135deg, rgba(186, 26, 26, 0.08), rgba(212, 47, 47, 0.06)); width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; text-decoration: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border: 1px solid rgba(186, 26, 26, 0.1); font-size: 20px; box-shadow: 0 2px 8px rgba(186, 26, 26, 0.06);" title="Supprimer" onmouseover="this.style.backgroundColor='rgba(186, 26, 26, 0.15)'; this.style.transform='scale(1.15) rotate(5deg)'; this.style.boxShadow='0 8px 20px rgba(186, 26, 26, 0.2)'" onmouseout="this.style.backgroundColor='rgba(186, 26, 26, 0.08)'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(186, 26, 26, 0.06)'" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');"><span class="material-symbols-outlined">delete</span></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="padding: 60px 20px; text-align: center; color: #424752;"><span class="material-symbols-outlined" style="display: block; font-size: 56px; margin-bottom: 16px; opacity: 0.3; color: #004d99;">group_off</span><p style="font-weight: 700; font-size: 20px; margin-top: 8px; color: #191c1e;">Aucun utilisateur trouvé</p><p style="font-size: 14px; margin-top: 12px; color: #424752; font-weight: 500;">Cliquez sur <strong>"Ajouter Utilisateur"</strong> pour en créer un nouveau</p></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Pagination -->
            <?php
            if (isset($data['pagination'])) {
                $baseUrl = '/integration/admin?';
                if (!empty($data['search'])) $baseUrl .= 'search=' . urlencode($data['search']) . '&';
                if (!empty($data['roleFilter'])) $baseUrl .= 'role=' . urlencode($data['roleFilter']) . '&';

                $currentPage = $data['pagination']['page'] ?? 1;
                $totalPages = $data['pagination']['totalPages'] ?? 1;
                
                if ($totalPages > 1):
            ?>
            <div class="mt-8 flex justify-center">
                <div class="flex items-center gap-2">
                    <!-- Previous Button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= $baseUrl . 'page=' . ($currentPage - 1) ?>" class="px-4 py-2 bg-white text-on-surface hover:bg-primary/10 hover:text-primary border border-outline/30 rounded-lg text-sm font-bold transition-all shadow-sm">
                            &laquo;
                        </a>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php 
                            // Show first, last, and pages around current
                            if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): 
                        ?>
                            <a href="<?= $baseUrl . 'page=' . $i ?>" 
                               class="min-w-[40px] h-10 flex items-center justify-center rounded-lg font-bold text-sm transition-all duration-300 <?= $i === $currentPage ? 'bg-gradient-to-r from-primary to-primary-container text-white shadow-md transform scale-105 border-none' : 'bg-white text-on-surface hover:bg-primary/10 hover:text-primary border border-outline/30 shadow-sm' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <span class="px-2 text-on-surface-variant font-bold">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= $baseUrl . 'page=' . ($currentPage + 1) ?>" class="px-4 py-2 bg-white text-on-surface hover:bg-primary/10 hover:text-primary border border-outline/30 rounded-lg text-sm font-bold transition-all shadow-sm">
                            &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                endif;
            } 
            ?>

