<?php
/**
 * Back Office Dashboard View
 * Shows stats overview, recent articles, and pending comments
 */
?>

<!-- Bento Layout: Stats & Recent Activity -->
<div class="grid grid-cols-12 gap-8">

    <!-- Main Content Area -->
    <div class="col-span-8 space-y-6">
        <!-- Quick Stats Row -->
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total Articles</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_articles'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">article</span>
                    <?= $postStats['published'] ?? 0 ?> published
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total Views</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_views'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">trending_up</span>
                    All time
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Total Likes</p>
                <h4 class="text-2xl font-extrabold text-primary stat-counter" data-target="<?= $postStats['total_likes'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-tertiary mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">favorite</span>
                    Engagement
                </p>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'tertiary-fixed' ?> shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Pending Comments</p>
                <h4 class="text-2xl font-extrabold text-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'primary' ?> stat-counter" data-target="<?= $commentStats['pending'] ?? 0 ?>">0</h4>
                <p class="text-[10px] text-<?= ($commentStats['pending'] ?? 0) > 0 ? 'error' : 'on-surface-variant' ?> mt-2 flex items-center">
                    <span class="material-symbols-outlined text-xs mr-1">gavel</span>
                    Needs review
                </p>
            </div>
        </div>

        <!-- Recent Articles -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-1 h-6 bg-tertiary rounded-full"></span>
                    Recent Publications
                </h2>
                <a href="backOffice.php?action=articles" class="text-xs font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full hover:bg-primary-fixed-dim transition-colors">
                    VIEW ALL
                </a>
            </div>
            <div class="space-y-1">
                <?php if (!empty($recentPosts)): ?>
                    <?php foreach ($recentPosts as $post): ?>
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
                                <h3 class="font-bold text-on-surface"><?= htmlspecialchars($post['titre']) ?></h3>
                                <p class="text-xs text-on-surface-variant flex items-center gap-2">
                                    <span class="font-semibold text-tertiary"><?= htmlspecialchars($post['categorie']) ?></span>
                                    • <?= date('M d, Y', strtotime($post['date_creation'])) ?>
                                    • By <?= htmlspecialchars($post['prenom'] . ' ' . $post['nom']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-on-surface-variant flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <?= number_format($post['views_count']) ?>
                            </span>
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
                    <div class="text-center py-8 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2">edit_note</span>
                        <p>No articles yet. Create your first post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar: Pending Comments + Category Stats -->
    <div class="col-span-4 space-y-8">
        <!-- Comment Moderation Preview -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Comment Moderation</h2>
                <span class="material-symbols-outlined text-tertiary">gavel</span>
            </div>
            <div class="space-y-4">
                <?php if (!empty($pendingComments)): ?>
                    <?php foreach (array_slice($pendingComments, 0, 3) as $comment): ?>
                    <div class="bg-surface-container-lowest p-4 rounded-lg border-l-2 border-tertiary shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center text-[10px] font-bold text-on-secondary-container">
                                    <?= strtoupper(substr($comment['prenom'] ?? 'U', 0, 1) . substr($comment['nom'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="text-xs font-bold"><?= htmlspecialchars(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? '')) ?></span>
                            </div>
                            <span class="text-[10px] text-on-surface-variant"><?= date('M d', strtotime($comment['date_creation'])) ?></span>
                        </div>
                        <p class="text-xs text-on-surface-variant leading-relaxed mb-2">
                            on <em class="text-primary"><?= htmlspecialchars($comment['post_titre'] ?? 'Unknown') ?></em>
                        </p>
                        <p class="text-xs text-on-surface-variant leading-relaxed mb-3"><?= htmlspecialchars($comment['contenu']) ?></p>
                        <div class="flex gap-2">
                            <a href="backOffice.php?controller=comment&action=approve&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=dashboard" 
                               class="flex-1 py-1.5 text-[10px] font-bold rounded bg-tertiary-fixed text-on-tertiary-fixed text-center hover:opacity-80 transition-opacity">Approve</a>
                            <a href="backOffice.php?controller=comment&action=reject&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=dashboard" 
                               class="flex-1 py-1.5 text-[10px] font-bold rounded bg-surface-container text-on-surface text-center hover:bg-surface-container-high">Reject</a>
                            <button onclick="showDeleteModal('backOffice.php?controller=comment&action=delete_comment&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=dashboard', 'comment')" 
                                    class="flex-1 py-1.5 text-[10px] font-bold rounded bg-error-container/30 text-error hover:bg-error-container/50">Delete</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($pendingComments) > 3): ?>
                    <a href="backOffice.php?action=moderation" class="block text-center text-xs font-bold text-primary hover:underline">
                        View all <?= count($pendingComments) ?> pending comments →
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-on-surface-variant">
                        <span class="material-symbols-outlined text-2xl text-tertiary mb-1">check_circle</span>
                        <p class="text-xs">All caught up! No pending comments.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <h2 class="text-lg font-bold">Categories</h2>
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

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Drafts</p>
                <h4 class="text-2xl font-extrabold text-primary"><?= $postStats['drafts'] ?? 0 ?></h4>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-xl border-t-2 border-tertiary-fixed shadow-sm">
                <p class="text-xs font-bold text-on-surface-variant mb-1">Comments</p>
                <h4 class="text-2xl font-extrabold text-primary"><?= $commentStats['total'] ?? 0 ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- System Status Bar -->
<div class="bg-surface-container-low rounded-2xl p-8 flex items-center justify-between">
    <div class="flex items-center gap-8">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-tertiary animate-pulse"></span>
            <span class="text-sm font-bold">System Status: Operational</span>
        </div>
        <div class="h-8 w-px bg-outline-variant/30"></div>
        <div class="text-sm">
            <span class="text-on-surface-variant">Database:</span>
            <span class="font-bold ml-1">mediflow</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-xs text-on-surface-variant italic">MediFlow Magazine Module v1.0</span>
        <a href="frontOffice.php" class="bg-white px-4 py-2 rounded-lg text-xs font-bold text-primary shadow-sm hover:bg-slate-50 transition-all">
            View Front Office →
        </a>
    </div>
</div>
