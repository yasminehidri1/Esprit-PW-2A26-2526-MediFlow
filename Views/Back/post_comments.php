<?php
/**
 * Back Office — Comments Tab
 * Shows all comments (paginated) or filtered by a specific post.
 */

$postTitle   = $post['titre'] ?? null;
$postId      = $post['id'] ?? (empty($_GET['post_id']) ? null : (int)$_GET['post_id']);
$currentPage = $currentPage ?? 1;
$totalPages  = $totalPages  ?? 1;
$isFiltered  = ($postId !== null && $postTitle !== null);

// Build pagination base URL
$paginationBase = '/integration/magazine/admin/comments'
    . ($postId ? '?post_id=' . $postId : '');

// Helpers reused across card statuses
$statusMeta = [
    'approuve'  => ['color' => 'border-tertiary',   'bg'    => 'bg-tertiary-fixed/10',     'badge' => 'bg-tertiary-fixed text-on-tertiary-fixed',      'label' => 'Approuvé'],
    'rejete'    => ['color' => 'border-error',       'bg'    => 'bg-error-container/20',    'badge' => 'bg-error-container text-error',                 'label' => 'Rejeté'],
    'en_attente'=> ['color' => 'border-amber-400',   'bg'    => 'bg-amber-50/30',           'badge' => 'bg-amber-50 text-amber-700',                    'label' => 'En Attente'],
];
?>

<div class="space-y-6">

    <!-- ── Page header ─────────────────────────────────────────────── -->
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-2xl font-bold flex items-center gap-3">
                    <span class="w-1.5 h-8 bg-tertiary rounded-full"></span>
                    <?= $isFiltered ? 'Commentaires de l\'Article' : 'Tous les Commentaires' ?>
                </h2>
            </div>
            <?php if ($isFiltered): ?>
            <p class="text-sm text-on-surface-variant ml-5">
                Affichage des commentaires pour :
                <span class="font-semibold text-primary"><?= htmlspecialchars($postTitle) ?></span>
            </p>
            <?php else: ?>
            <p class="text-sm text-on-surface-variant ml-5">Tous les commentaires de toutes les publications</p>
            <?php endif; ?>
        </div>

        <!-- Stats pills -->
        <div class="flex items-center gap-2 flex-wrap">
            <span class="flex items-center gap-1.5 text-xs font-bold text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-full">
                <span class="material-symbols-outlined text-sm">comment</span>
                <?= $totalComments ?? 0 ?> Total
            </span>
            <?php
            $approved = count(array_filter($postComments ?? [], fn($c) => $c['statut'] === 'approuve'));
            $pending  = count(array_filter($postComments ?? [], fn($c) => $c['statut'] === 'en_attente'));
            ?>
            <?php if ($approved > 0): ?>
            <span class="text-xs font-bold text-tertiary bg-tertiary-fixed px-3 py-1.5 rounded-full">
                <?= $approved ?> Approuvés
            </span>
            <?php endif; ?>
            <?php if ($pending > 0): ?>
            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full">
                <?= $pending ?> En Attente
            </span>
            <?php endif; ?>

            <?php if ($isFiltered): ?>
            <a href="/integration/magazine/admin/comments"
               class="flex items-center gap-1.5 text-xs font-bold text-primary bg-primary-fixed px-3 py-1.5 rounded-full hover:bg-primary-fixed-dim transition-colors">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span>
                Tous les Commentaires
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Filter active notice banner ────────────────────────────── -->
    <?php if ($isFiltered): ?>
    <div class="flex items-center gap-3 bg-primary-fixed/40 border border-primary/20 rounded-xl px-5 py-3">
        <span class="material-symbols-outlined text-primary text-xl">filter_alt</span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-primary">Filtré par publication</p>
            <p class="text-xs text-on-surface-variant truncate"><?= htmlspecialchars($postTitle) ?></p>
        </div>
        <a href="/integration/magazine/admin/comments"
           class="text-xs font-bold text-primary hover:underline whitespace-nowrap">
            ✕ Effacer le filtre
        </a>
    </div>
    <?php endif; ?>

    <!-- ── Comments grid ───────────────────────────────────────────── -->
    <?php if (!empty($postComments)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <?php foreach ($postComments as $comment):
            $sm = $statusMeta[$comment['statut']] ?? $statusMeta['en_attente'];
            $initials = strtoupper(
                substr($comment['prenom'] ?? 'U', 0, 1) .
                substr($comment['nom']   ?? 'U', 0, 1)
            );
            $fullName = trim(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? ''));
            $postRef  = htmlspecialchars($comment['post_titre'] ?? '');
            $deleteRedirect = '/integration/magazine/admin/comments'
                . ($postId ? '?post_id=' . $postId : '')
                . '&page=' . $currentPage;
        ?>
        <div class="group bg-surface-container-lowest rounded-xl border-l-4 <?= $sm['color'] ?> <?= $sm['bg'] ?> p-5 shadow-sm hover:shadow-md transition-all duration-200">

            <!-- Card header -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-sm font-bold text-on-secondary-container shrink-0">
                        <?= $initials ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($fullName) ?></p>
                        <p class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($comment['mail'] ?? '') ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full <?= $sm['badge'] ?>">
                        <?= $sm['label'] ?>
                    </span>
                </div>
            </div>

            <!-- Post reference (only in all-comments mode) -->
            <?php if (!$isFiltered && !empty($comment['post_titre'])): ?>
            <div class="flex items-center gap-1.5 bg-surface-container px-3 py-1.5 rounded-lg mb-3">
                <span class="material-symbols-outlined text-[14px] text-primary">article</span>
                <p class="text-[11px] text-primary font-medium truncate"><?= $postRef ?></p>
            </div>
            <?php endif; ?>

            <!-- Comment body -->
            <p class="text-sm text-on-surface leading-relaxed mb-4"><?= htmlspecialchars($comment['contenu']) ?></p>

            <!-- Footer: date + actions -->
            <div class="flex items-center justify-between mt-auto pt-3 border-t border-surface-container">
                <span class="text-[10px] text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                    <?= date('M d, Y · H:i', strtotime($comment['date_creation'])) ?>
                </span>
                <button onclick="showDeleteModal('/integration/magazine/admin/comment/delete?id=<?= $comment['id'] ?>&redirect=<?= urlencode($deleteRedirect) ?>', 'comment')"
                        class="flex items-center gap-1 px-3 py-1.5 text-[11px] font-bold rounded-lg bg-error-container/30 text-error hover:bg-error-container/70 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Pagination ─────────────────────────────────────────────── -->
    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between bg-surface-container-low rounded-xl p-4 border border-surface-container">
        <div class="text-sm text-on-surface-variant">
            Page <span class="font-bold text-on-surface"><?= $currentPage ?></span>
            sur <span class="font-bold text-on-surface"><?= $totalPages ?></span>
            <span class="text-xs ml-1 opacity-70">(<?= $totalComments ?> au total)</span>
        </div>
        <div class="flex items-center gap-2">
            <!-- Previous -->
            <?php if ($currentPage > 1): ?>
            <a href="<?= $paginationBase ?>&page=<?= $currentPage - 1 ?>"
               class="px-4 py-2 text-xs font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
                Précédent
            </a>
            <?php else: ?>
            <button disabled class="px-4 py-2 text-xs font-bold rounded-lg bg-surface-container text-on-surface-variant opacity-40 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
                Précédent
            </button>
            <?php endif; ?>

            <!-- Page numbers -->
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $totalPages; $i++):
                    $show = ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 1);
                    $ellipsisBefore = ($i === 2 && $currentPage > 4);
                    $ellipsisAfter  = ($i === $totalPages - 1 && $currentPage < $totalPages - 3);
                ?>
                    <?php if ($ellipsisBefore || $ellipsisAfter): ?>
                        <span class="px-2 text-on-surface-variant self-center">…</span>
                    <?php endif; ?>
                    <?php if ($show): ?>
                        <?php if ($i === $currentPage): ?>
                        <span class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded-lg bg-primary text-on-primary">
                            <?= $i ?>
                        </span>
                        <?php else: ?>
                        <a href="<?= $paginationBase ?>&page=<?= $i ?>"
                           class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors">
                            <?= $i ?>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <!-- Next -->
            <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $paginationBase ?>&page=<?= $currentPage + 1 ?>"
               class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-on-primary hover:opacity-90 transition-opacity flex items-center gap-1">
                Suivant
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
            <?php else: ?>
            <button disabled class="px-4 py-2 text-xs font-bold rounded-lg bg-surface-container text-on-surface-variant opacity-40 flex items-center gap-1">
                Suivant
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php else: /* No comments */ ?>
    <div class="bg-surface-container-lowest rounded-2xl p-20 text-center">
        <span class="material-symbols-outlined text-6xl text-tertiary mb-4 block">chat_bubble_outline</span>
        <h3 class="text-xl font-bold text-on-surface mb-2">
            <?= $isFiltered ? 'Aucun commentaire pour cet article' : 'Aucun commentaire pour le moment' ?>
        </h3>
        <p class="text-on-surface-variant mb-6">
            <?= $isFiltered
                ? 'Cette publication n\'a pas encore reçu de commentaires.'
                : 'Aucun commentaire n\'a encore été soumis.' ?>
        </p>
        <?php if ($isFiltered): ?>
        <a href="/integration/magazine/admin/comments"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary rounded-lg font-semibold hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">forum</span>
            Voir Tous les Commentaires
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
