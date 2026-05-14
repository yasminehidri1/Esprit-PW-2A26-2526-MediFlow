<?php
/**
 * Back Office Dashboard View
 * Stats, recent publications (paginated), comment moderation panel (paginated)
 */
?>

<!-- Bento Layout: Stats & Recent Activity -->
<div class="grid grid-cols-12 gap-8">

    <!-- Main Content Area (8 cols) -->
    <div class="col-span-8 space-y-6">

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total des Articles</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_articles'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">article</span>
                    <?= $postStats['published'] ?? 0 ?> publiés
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total des Vues</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_views'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">trending_up</span>
                    Global
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total des J'aime</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_likes'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">favorite</span>
                    Engagement
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'tertiary-fixed' ?> shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Commentaires en Attente</p>
                <h4 class="text-2xl font-extrabold text-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'primary' ?> stat-counter" data-target="<?= $commentStats['pending'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'on-surface-variant' ?> mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">gavel</span>
                    À réviser
                </p>
            </div>
        </div>

        <!-- Recent Publications -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-1 h-6 bg-tertiary rounded-full"></span>
                    Publications Récentes
                </h2>
                <a href="/integration/magazine/admin/articles" class="text-xs font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full hover:bg-primary-fixed-dim transition-colors">
                    VOIR TOUT
                </a>
            </div>

            <div class="space-y-1">
                <?php if (!empty($recentPosts)): ?>
                    <?php foreach ($recentPosts as $post): ?>
                    <div id="post-row-<?= $post['id'] ?>"
                         class="group flex items-center justify-between p-4 bg-surface-container-lowest rounded-lg hover:bg-blue-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <?php if (!empty($post['image_url'])): ?>
                            <img alt="Post Thumbnail" class="w-14 h-14 rounded-lg object-cover pointer-events-none" src="<?= htmlspecialchars($post['image_url']) ?>"/>
                            <?php else: ?>
                            <div class="w-14 h-14 rounded-lg bg-primary-fixed flex items-center justify-center flex-shrink-0 pointer-events-none">
                                <span class="material-symbols-outlined text-primary">article</span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <h3 class="font-bold text-on-surface"><?= htmlspecialchars($post['titre']) ?></h3>
                                <p class="text-xs text-on-surface-variant flex items-center gap-2">
                                    <span class="font-semibold text-tertiary"><?= htmlspecialchars($post['categorie']) ?></span>
                                    • <?= date('d M Y', strtotime($post['date_creation'])) ?>
                                    • Par <?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-on-surface-variant flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <?= number_format($post['views_count']) ?>
                            </span>
                            <!-- View Comments: navigate to Comments tab filtered by this post -->
                            <a href="/integration/magazine/admin/comments?post_id=<?= $post['id'] ?>"
                               class="p-2 text-on-surface-variant hover:text-tertiary hover:bg-tertiary-fixed/20 rounded-md transition-all"
                               title="View all comments for this post">
                                <span class="material-symbols-outlined">forum</span>
                            </a>
                            <a href="/integration/magazine/admin/article-form?id=<?= $post['id'] ?>" class="p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-md transition-all">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <button onclick="showDeleteModal('/integration/magazine/admin/delete?id=<?= $post['id'] ?>', 'article')" class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/20 rounded-md transition-all">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2">edit_note</span>
                        <p>Aucun article pour le moment. Créez votre première publication !</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Publications Pagination -->
            <?php if (($totalPostsPages ?? 1) > 1): ?>
            <div class="flex items-center justify-between pt-2 border-t border-surface-container">
                <span class="text-xs text-on-surface-variant">
                    Page <strong><?= $postsPage ?></strong> sur <strong><?= $totalPostsPages ?></strong>
                    <span class="opacity-60">(<?= $totalPostsCount ?> articles)</span>
                </span>
                <div class="flex items-center gap-1.5">
                    <?php if ($postsPage > 1): ?>
                    <a href="/integration/magazine/admin?posts_page=<?= $postsPage - 1 ?>&comments_page=<?= $commentsPage ?? 1 ?>"
                       class="px-3 py-1.5 text-xs font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="px-3 py-1.5 text-xs rounded-lg bg-surface-container text-on-surface-variant opacity-40">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPostsPages; $i++): ?>
                        <?php if ($i === $postsPage): ?>
                        <span class="w-7 h-7 flex items-center justify-center text-xs font-bold rounded-lg bg-primary text-on-primary"><?= $i ?></span>
                        <?php elseif ($i <= 3 || $i >= $totalPostsPages - 1 || abs($i - $postsPage) <= 1): ?>
                        <a href="/integration/magazine/admin?posts_page=<?= $i ?>&comments_page=<?= $commentsPage ?? 1 ?>"
                           class="w-7 h-7 flex items-center justify-center text-xs font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors"><?= $i ?></a>
                        <?php elseif ($i === 4 && $totalPostsPages > 6): ?>
                        <span class="text-on-surface-variant px-1">…</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($postsPage < $totalPostsPages): ?>
                    <a href="/integration/magazine/admin?posts_page=<?= $postsPage + 1 ?>&comments_page=<?= $commentsPage ?? 1 ?>"
                       class="px-3 py-1.5 text-xs font-bold rounded-lg bg-primary text-on-primary hover:opacity-90 transition-opacity flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="px-3 py-1.5 text-xs rounded-lg bg-surface-container text-on-surface-variant opacity-40">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /col-8 -->

    <!-- Right Sidebar: Comment Moderation + Categories -->
    <div class="col-span-4 space-y-8">

        <!-- Comment Moderation Panel -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-tertiary text-xl">forum</span>
                        Modération des Commentaires
                    </h2>
                    <p class="text-[11px] text-on-surface-variant mt-0.5">Messages récents</p>
                </div>
                <a href="/integration/magazine/admin/comments"
                   class="text-[10px] font-bold text-primary bg-primary-fixed px-2.5 py-1 rounded-full hover:bg-primary-fixed-dim transition-colors">
                    Voir Tout →
                </a>
            </div>

            <!-- Comments list -->
            <div class="space-y-3">
                <?php if (!empty($allComments)): ?>
                    <?php foreach ($allComments as $comment):
                        $statusColor = match($comment['statut']) {
                            'approuve'  => 'border-tertiary',
                            'rejete'    => 'border-error',
                            default     => 'border-amber-400',
                        };
                        $statusBadge = match($comment['statut']) {
                            'approuve'  => 'bg-tertiary-fixed text-on-tertiary-fixed',
                            'rejete'    => 'bg-error-container text-error',
                            default     => 'bg-amber-50 text-amber-700',
                        };
                        $statusLabel = match($comment['statut']) {
                            'approuve'  => 'Approuvé',
                            'rejete'    => 'Rejeté',
                            default     => 'En Attente',
                        };
                    ?>
                    <div class="bg-surface-container-lowest p-4 rounded-lg border-l-2 <?= $statusColor ?> shadow-sm">
                        <!-- Header row -->
                        <div class="flex justify-between items-start mb-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center text-[10px] font-bold text-on-secondary-container flex-shrink-0">
                                    <?= strtoupper(substr($comment['prenom'] ?? 'U', 0, 1) . substr($comment['nom'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="text-xs font-bold"><?= htmlspecialchars(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? '')) ?></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full <?= $statusBadge ?>"><?= $statusLabel ?></span>
                                <span class="text-[10px] text-on-surface-variant"><?= date('M d', strtotime($comment['date_creation'])) ?></span>
                            </div>
                        </div>

                        <!-- Post reference -->
                        <?php if (!empty($comment['post_titre'])): ?>
                        <p class="text-[10px] text-on-surface-variant mb-1.5">
                            sur <a href="/integration/magazine/admin/comments?post_id=<?= $comment['id_post'] ?>"
                                  class="text-primary font-medium hover:underline"><?= htmlspecialchars($comment['post_titre']) ?></a>
                        </p>
                        <?php endif; ?>

                        <!-- Comment body -->
                        <p class="text-xs text-on-surface-variant leading-relaxed mb-3 line-clamp-3"><?= htmlspecialchars($comment['contenu']) ?></p>

                        <!-- Delete -->
                        <div class="flex justify-end">
                            <button onclick="showDeleteModal('/integration/magazine/admin/comment/delete?id=<?= $comment['id'] ?>&redirect=/integration/magazine/admin?comments_page=<?= $commentsPage ?? 1 ?>', 'comment')"
                                    class="flex items-center gap-1 px-3 py-1 text-[10px] font-bold rounded-lg bg-error-container/30 text-error hover:bg-error-container/60 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 text-on-surface-variant">
                        <span class="material-symbols-outlined text-3xl text-tertiary mb-1">check_circle</span>
                        <p class="text-xs">Aucun commentaire pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comment Moderation Pagination -->
            <?php if (($totalCommentsPages ?? 1) > 1): ?>
            <div class="flex items-center justify-between pt-2 border-t border-surface-container">
                <span class="text-[10px] text-on-surface-variant">
                    Page <strong><?= $commentsPage ?></strong> / <strong><?= $totalCommentsPages ?></strong>
                </span>
                <div class="flex items-center gap-1">
                    <?php if ($commentsPage > 1): ?>
                    <a href="/integration/magazine/admin?comments_page=<?= $commentsPage - 1 ?>&posts_page=<?= $postsPage ?? 1 ?>"
                       class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors flex items-center">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="px-2.5 py-1.5 rounded-lg bg-surface-container text-on-surface-variant opacity-40">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <?php endif; ?>

                    <?php for ($ci = 1; $ci <= $totalCommentsPages; $ci++): ?>
                        <?php if ($ci === $commentsPage): ?>
                        <span class="w-6 h-6 flex items-center justify-center text-[10px] font-bold rounded-lg bg-primary text-on-primary"><?= $ci ?></span>
                        <?php elseif ($ci <= 2 || $ci >= $totalCommentsPages - 1 || abs($ci - $commentsPage) <= 1): ?>
                        <a href="/integration/magazine/admin?comments_page=<?= $ci ?>&posts_page=<?= $postsPage ?? 1 ?>"
                           class="w-6 h-6 flex items-center justify-center text-[10px] font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors"><?= $ci ?></a>
                        <?php elseif ($ci === 3 && $totalCommentsPages > 5): ?>
                        <span class="text-on-surface-variant text-[10px] px-0.5">…</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($commentsPage < $totalCommentsPages): ?>
                    <a href="/integration/magazine/admin?comments_page=<?= $commentsPage + 1 ?>&posts_page=<?= $postsPage ?? 1 ?>"
                       class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-primary text-on-primary hover:opacity-90 transition-opacity flex items-center">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                    <?php else: ?>
                    <button disabled class="px-2.5 py-1.5 rounded-lg bg-surface-container text-on-surface-variant opacity-40">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Category Breakdown -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <h2 class="text-lg font-bold">Catégories</h2>
            <div class="space-y-3">
                <?php if (!empty($postStats['categories'])): ?>
                    <?php foreach ($postStats['categories'] as $cat): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-on-surface-variant"><?= htmlspecialchars($cat['categorie']) ?></span>
                        <span class="text-xs font-bold text-primary bg-primary-fixed px-2 py-0.5 rounded-full"><?= $cat['count'] ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats mini -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Brouillons</p>
                <h4 class="text-2xl font-extrabold text-primary"><?= $postStats['drafts'] ?? 0 ?></h4>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Commentaires</p>
                <h4 class="text-2xl font-extrabold text-primary"><?= $commentStats['total'] ?? 0 ?></h4>
            </div>
        </div>

    </div><!-- /col-4 -->
</div>

<!-- System Status Bar -->
<div class="bg-surface-container-low rounded-2xl p-8 flex items-center justify-between">
    <div class="flex items-center gap-8">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-tertiary animate-pulse"></span>
            <span class="text-sm font-bold">Statut du Système : Opérationnel</span>
        </div>
        <div class="h-8 w-px bg-outline-variant/30"></div>
        <div class="text-sm">
            <span class="text-on-surface-variant">Base de données :</span>
            <span class="font-bold ml-1">mediflow</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-xs text-on-surface-variant italic">Module Magazine MediFlow v1.0</span>
        <a href="/integration/magazine" class="bg-white px-4 py-2 rounded-lg text-xs font-bold text-primary shadow-sm hover:bg-slate-50 transition-all">
            Voir Front Office →
        </a>
    </div>
</div>

<script>
// Animate stat counters on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.stat-counter').forEach(el => {
        const target = parseInt(el.dataset.target || '0', 10);
        const duration = 900;
        const step = target / (duration / 16);
        let current = 0;
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = Math.floor(current).toLocaleString();
            if (current >= target) clearInterval(timer);
        }, 16);
    });
});
</script>
