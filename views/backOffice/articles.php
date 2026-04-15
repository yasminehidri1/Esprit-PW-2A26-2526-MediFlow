<?php
/**
 * Back Office — Articles List View
 * Paginated article list with edit/delete actions, search, and filters
 */
?>

<div class="grid grid-cols-12 gap-8">
    <!-- Post List Section -->
    <div class="col-span-8 space-y-6">
        <div class="bg-surface-container-low rounded-xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-1 h-6 bg-tertiary rounded-full"></span>
                    Active Publications
                </h2>
                <span class="text-xs font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full"><?= $result['total'] ?? 0 ?> TOTAL</span>
            </div>

            <!-- Filters Row -->
            <div class="flex items-center gap-3 flex-wrap">
                <a href="backOffice.php?action=articles" 
                   class="text-xs font-bold px-3 py-1.5 rounded-full transition-colors <?= empty($_GET['categorie']) && empty($_GET['statut']) ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
                    All
                </a>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <a href="backOffice.php?action=articles&categorie=<?= urlencode($cat) ?>" 
                       class="text-xs font-bold px-3 py-1.5 rounded-full transition-colors <?= ($_GET['categorie'] ?? '') === $cat ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="h-4 w-px bg-outline-variant/30 mx-1"></div>
                <a href="backOffice.php?action=articles&statut=publie" 
                   class="text-xs font-bold px-3 py-1.5 rounded-full transition-colors <?= ($_GET['statut'] ?? '') === 'publie' ? 'bg-tertiary text-on-tertiary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
                    Published
                </a>
                <a href="backOffice.php?action=articles&statut=brouillon" 
                   class="text-xs font-bold px-3 py-1.5 rounded-full transition-colors <?= ($_GET['statut'] ?? '') === 'brouillon' ? 'bg-secondary text-on-secondary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
                    Drafts
                </a>
            </div>

            <!-- Articles List -->
            <div class="space-y-1">
                <?php if (!empty($result['data'])): ?>
                    <?php foreach ($result['data'] as $post): ?>
                    <div class="group flex items-center justify-between p-4 bg-surface-container-lowest rounded-lg hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <?php if (!empty($post['image_url'])): ?>
                            <img alt="Post Thumbnail" class="w-14 h-14 rounded-lg object-cover" src="<?= htmlspecialchars($post['image_url']) ?>"/>
                            <?php else: ?>
                            <div class="w-14 h-14 rounded-lg bg-primary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">article</span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-on-surface"><?= htmlspecialchars($post['titre']) ?></h3>
                                    <?php 
                                    $statusColors = [
                                        'publie'    => 'bg-tertiary-fixed text-on-tertiary-fixed',
                                        'brouillon' => 'bg-secondary-container text-on-secondary-container',
                                        'archive'   => 'bg-surface-container-high text-on-surface-variant',
                                    ];
                                    $statusLabels = [
                                        'publie'    => 'Published',
                                        'brouillon' => 'Draft',
                                        'archive'   => 'Archived',
                                    ];
                                    ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $statusColors[$post['statut']] ?? '' ?>">
                                        <?= $statusLabels[$post['statut']] ?? $post['statut'] ?>
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant flex items-center gap-2 mt-1">
                                    <span class="font-semibold text-tertiary"><?= htmlspecialchars($post['categorie']) ?></span>
                                    • <?= date('M d, Y', strtotime($post['date_creation'])) ?>
                                    • By <?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="hidden md:flex items-center gap-4 text-xs text-on-surface-variant mr-2">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <?= number_format($post['views_count']) ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">favorite</span>
                                    <?= number_format($post['likes_count']) ?>
                                </span>
                            </div>
                            <a href="backOffice.php?action=form&id=<?= $post['id'] ?>" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-md transition-all">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <button onclick="showDeleteModal('backOffice.php?action=delete&id=<?= $post['id'] ?>', 'article')" class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-md transition-all">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12 text-on-surface-variant">
                        <span class="material-symbols-outlined text-5xl mb-3 text-outline">search_off</span>
                        <p class="text-lg font-semibold mb-1">No articles found</p>
                        <p class="text-sm">Try adjusting your filters or <a href="backOffice.php?action=form" class="text-primary font-bold hover:underline">create a new post</a>.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (($result['totalPages'] ?? 0) > 1): ?>
            <div class="flex items-center justify-center gap-2 pt-4">
                <?php for ($i = 1; $i <= $result['totalPages']; $i++): ?>
                    <?php
                    $queryParams = $_GET;
                    $queryParams['page'] = $i;
                    $url = 'backOffice.php?' . http_build_query($queryParams);
                    ?>
                    <a href="<?= htmlspecialchars($url) ?>" 
                       class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-colors <?= $i == ($result['page'] ?? 1) 
                           ? 'bg-primary text-on-primary' 
                           : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar: Quick Actions & Info -->
    <div class="col-span-4 space-y-6">
        <a href="backOffice.php?action=form" class="block bg-gradient-to-br from-primary to-primary-container rounded-2xl p-6 text-on-primary hover:scale-[1.01] transition-transform shadow-lg">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-3xl">edit_square</span>
                <h3 class="text-lg font-bold">New Article</h3>
            </div>
            <p class="text-sm text-on-primary-container/80">Create a new health article, research paper, or clinical update for the magazine.</p>
        </a>

        <a href="backOffice.php?action=moderation" class="block bg-surface-container-low rounded-2xl p-6 hover:bg-surface-container transition-colors">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold">Moderation Queue</h3>
                <span class="material-symbols-outlined text-tertiary">gavel</span>
            </div>
            <p class="text-sm text-on-surface-variant">Review and manage community comments across all articles.</p>
        </a>

        <div class="bg-tertiary-container text-on-tertiary-container p-6 rounded-2xl text-center">
            <span class="material-symbols-outlined text-4xl mb-3">auto_stories</span>
            <h3 class="text-lg font-bold mb-2">Content Tips</h3>
            <p class="text-xs opacity-80 leading-relaxed">Write engaging health articles with clear headings, evidence-based claims, and actionable takeaways for patients.</p>
        </div>
    </div>
</div>
