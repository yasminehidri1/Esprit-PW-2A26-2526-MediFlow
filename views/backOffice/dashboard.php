<?php
/**
 * Back Office Dashboard View
 * Stats, recent publications (clickable to filter comments), comment moderation panel
 */

// Build a map: post_id => all comments for that post (for JS filtering)
$commentsByPost = [];
foreach ($allComments ?? [] as $c) {
    $commentsByPost[$c['id_post']][] = $c;
}
?>

<!-- Bento Layout: Stats & Recent Activity -->
<div class="grid grid-cols-12 gap-8">

    <!-- Main Content Area (8 cols) -->
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

        <!-- Recent Publications (clickable to filter comments) -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-1 h-6 bg-tertiary rounded-full"></span>
                    Recent Publications
                </h2>
                <div class="flex items-center gap-3">
                    <button id="clearPostFilter"
                            onclick="filterCommentsByPost(null)"
                            class="hidden text-xs font-bold text-on-surface-variant bg-surface-container px-3 py-1 rounded-full hover:bg-surface-container-high transition-colors">
                        ✕ Show all
                    </button>
                    <a href="backOffice.php?action=articles" class="text-xs font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full hover:bg-primary-fixed-dim transition-colors">
                        VIEW ALL
                    </a>
                </div>
            </div>

            <p class="text-xs text-on-surface-variant -mt-2">
                <span class="material-symbols-outlined text-xs align-middle">info</span>
                Click a publication to filter its comments in the sidebar →
            </p>

            <div class="space-y-1">
                <?php if (!empty($recentPosts)): ?>
                    <?php foreach ($recentPosts as $post): ?>
                    <div id="post-row-<?= $post['id'] ?>"
                         class="post-filter-row group flex items-center justify-between p-4 bg-surface-container-lowest rounded-lg hover:bg-blue-50 transition-colors cursor-pointer"
                         onclick="filterCommentsByPost(<?= $post['id'] ?>, <?= htmlspecialchars(json_encode($post['titre']), ENT_QUOTES) ?>)">
                        <div class="flex items-center gap-4">
                            <?php if (!empty($post['image_url'])): ?>
                            <img alt="Post Thumbnail" class="w-14 h-14 rounded-lg object-cover pointer-events-none" src="<?= htmlspecialchars($post['image_url']) ?>"/>
                            <?php else: ?>
                            <div class="w-14 h-14 rounded-lg bg-primary-fixed flex items-center justify-center flex-shrink-0 pointer-events-none">
                                <span class="material-symbols-outlined text-primary">article</span>
                            </div>
                            <?php endif; ?>
                            <div class="pointer-events-none">
                                <h3 class="font-bold text-on-surface"><?= htmlspecialchars($post['titre']) ?></h3>
                                <p class="text-xs text-on-surface-variant flex items-center gap-2">
                                    <span class="font-semibold text-tertiary"><?= htmlspecialchars($post['categorie']) ?></span>
                                    • <?= date('M d, Y', strtotime($post['date_creation'])) ?>
                                    • By <?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3" onclick="event.stopPropagation()">
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

    </div><!-- /col-8 -->

    <!-- Right Sidebar: Comment Moderation + Categories -->
    <div class="col-span-4 space-y-8">

        <!-- Comment Moderation Panel -->
        <div class="bg-surface-container-low rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-tertiary text-xl">forum</span>
                        Comment Moderation
                    </h2>
                    <p id="commentFilterLabel" class="text-[11px] text-on-surface-variant mt-0.5">All recent messages</p>
                </div>
                <span id="commentCount" class="text-xs font-bold text-primary bg-primary-fixed px-2.5 py-1 rounded-full">
                    <?= count($allComments ?? []) ?>
                </span>
            </div>

            <!-- Filters row: Status + User + Keywords -->
            <div class="space-y-2">

                <!-- Status pills: All / Pending only -->
                <div class="flex gap-1.5">
                    <button onclick="filterByStatus('all')"
                            class="status-tab px-3 py-1 text-[10px] font-bold rounded-full bg-primary text-on-primary transition-colors"
                            data-status="all">All</button>
                    <button onclick="filterByStatus('en_attente')"
                            class="status-tab px-3 py-1 text-[10px] font-bold rounded-full bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors"
                            data-status="en_attente">Pending</button>
                </div>

                <!-- User filter -->
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[16px] text-on-surface-variant pointer-events-none">person</span>
                    <select id="userFilter" onchange="applyFilters()"
                            class="w-full pl-8 pr-3 py-2 bg-surface-container-lowest border border-surface-container-high rounded-lg text-[11px] text-on-surface appearance-none focus:ring-2 focus:ring-primary/30 focus:outline-none">
                        <option value="">All users</option>
                        <?php
                        $uniqueUsers = [];
                        foreach ($allComments ?? [] as $c) {
                            $name = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''));
                            if ($name && !in_array($name, $uniqueUsers)) $uniqueUsers[] = $name;
                        }
                        sort($uniqueUsers);
                        foreach ($uniqueUsers as $uName):
                        ?>
                        <option value="<?= htmlspecialchars($uName) ?>"><?= htmlspecialchars($uName) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[14px] text-on-surface-variant pointer-events-none">expand_more</span>
                </div>

                <!-- Keyword search -->
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[16px] text-on-surface-variant pointer-events-none">search</span>
                    <input id="keywordFilter" type="text" oninput="applyFilters()"
                           placeholder="Search keywords…"
                           class="w-full pl-8 pr-3 py-2 bg-surface-container-lowest border border-surface-container-high rounded-lg text-[11px] text-on-surface placeholder:text-on-surface-variant focus:ring-2 focus:ring-primary/30 focus:outline-none"/>
                </div>

            </div>

            <!-- Comments list -->
            <div id="commentsList" class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
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
                            'approuve'  => 'Approved',
                            'rejete'    => 'Rejected',
                            default     => 'Pending',
                        };
                    ?>
                    <div class="comment-card bg-surface-container-lowest p-4 rounded-lg border-l-2 <?= $statusColor ?> shadow-sm"
                         data-post-id="<?= $comment['id_post'] ?>"
                         data-status="<?= $comment['statut'] ?>"
                         data-user="<?= htmlspecialchars(strtolower(trim(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? ''))), ENT_QUOTES) ?>"
                         data-text="<?= htmlspecialchars(strtolower($comment['contenu']), ENT_QUOTES) ?>"
                         data-post-title="<?= htmlspecialchars(strtolower($comment['post_titre'] ?? ''), ENT_QUOTES) ?>">

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
                        <p class="text-[10px] text-on-surface-variant mb-1.5">
                            on <em class="text-primary font-medium"><?= htmlspecialchars($comment['post_titre'] ?? 'Unknown') ?></em>
                        </p>

                        <!-- Comment body -->
                        <p class="text-xs text-on-surface-variant leading-relaxed mb-3 line-clamp-3"><?= htmlspecialchars($comment['contenu']) ?></p>

                        <!-- Delete only -->
                        <div class="flex justify-end">
                            <button onclick="showDeleteModal('backOffice.php?controller=comment&action=delete_comment&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=dashboard', 'comment')"
                                    class="flex items-center gap-1 px-3 py-1 text-[10px] font-bold rounded-lg bg-error-container/30 text-error hover:bg-error-container/60 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                                Delete
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 text-on-surface-variant">
                        <span class="material-symbols-outlined text-3xl text-tertiary mb-1">check_circle</span>
                        <p class="text-xs">No comments yet.</p>
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

        <!-- Quick Stats mini -->
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

    </div><!-- /col-4 -->
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

<script>
// ============================================================
// Comment filtering — post | status | user | keywords
// ============================================================

let activePostId = null;   // null = show all
let activeStatus = 'all';  // 'all' | 'en_attente'

// ---- Post click filter ----
function filterCommentsByPost(postId, postTitle) {
    activePostId = postId;

    // Reset keyword & user filters so the post selection is never blocked
    const kw = document.getElementById('keywordFilter');
    const uf = document.getElementById('userFilter');
    if (kw) kw.value = '';
    if (uf) uf.value = '';

    document.querySelectorAll('.post-filter-row').forEach(row => {
        row.classList.remove('bg-blue-50', 'border-l-4', 'border-primary', 'pl-3');
    });

    const clearBtn = document.getElementById('clearPostFilter');

    if (postId !== null) {
        document.getElementById('post-row-' + postId)?.classList.add('bg-blue-50', 'border-l-4', 'border-primary', 'pl-3');
        clearBtn?.classList.remove('hidden');
        document.getElementById('commentFilterLabel').textContent = 'Filtered: ' + (postTitle || 'Post #' + postId);
    } else {
        clearBtn?.classList.add('hidden');
        document.getElementById('commentFilterLabel').textContent = 'All recent messages';
    }

    applyFilters();
}

// ---- Status pill toggle ----
function filterByStatus(status) {
    activeStatus = status;

    document.querySelectorAll('.status-tab').forEach(btn => {
        const isActive = (btn.dataset.status === status);
        btn.classList.toggle('bg-primary', isActive);
        btn.classList.toggle('text-on-primary', isActive);
        btn.classList.toggle('bg-surface-container', !isActive);
        btn.classList.toggle('text-on-surface-variant', !isActive);
        btn.classList.toggle('hover:bg-surface-container-high', !isActive);
    });

    applyFilters();
}

// ---- Master filter — applies all four dimensions ----
function applyFilters() {
    const cards   = document.querySelectorAll('.comment-card');
    const keyword = (document.getElementById('keywordFilter')?.value || '').trim().toLowerCase();
    const user    = (document.getElementById('userFilter')?.value || '').trim().toLowerCase();
    let visible   = 0;

    cards.forEach(card => {
        const matchPost    = (activePostId === null) || (card.dataset.postId == activePostId);
        const matchStatus  = (activeStatus === 'all') || (card.dataset.status === activeStatus);
        const cardUser     = (card.dataset.user || '').toLowerCase();
        const cardText     = (card.dataset.text || '').toLowerCase();
        const cardPost     = (card.dataset.postTitle || '').toLowerCase();
        const matchUser    = !user    || cardUser.includes(user);
        const matchKeyword = !keyword || cardText.includes(keyword) || cardPost.includes(keyword) || cardUser.includes(keyword);

        const show = matchPost && matchStatus && matchUser && matchKeyword;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('commentCount').textContent = visible;

    let emptyEl = document.getElementById('commentsEmptyState');
    if (visible === 0) {
        if (!emptyEl) {
            emptyEl = document.createElement('div');
            emptyEl.id = 'commentsEmptyState';
            emptyEl.className = 'text-center py-6 text-on-surface-variant';
            emptyEl.innerHTML = '<span class="material-symbols-outlined text-2xl text-outline mb-1">search_off</span><p class="text-xs mt-1">No comments match this filter.</p>';
            document.getElementById('commentsList').appendChild(emptyEl);
        }
    } else {
        emptyEl?.remove();
    }
}
</script>
