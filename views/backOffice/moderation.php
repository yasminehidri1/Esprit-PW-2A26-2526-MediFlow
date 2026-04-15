<?php
/**
 * Back Office — Comment Moderation View
 * Full moderation queue with approve/reject/delete actions
 */
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold flex items-center gap-3">
                <span class="material-symbols-outlined text-tertiary text-3xl">gavel</span>
                Comment Moderation Queue
            </h2>
            <p class="text-on-surface-variant mt-1">Review, approve, or remove community comments.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-full">
                <?= $commentStats['pending'] ?? 0 ?> Pending
            </span>
            <span class="text-xs font-bold text-tertiary bg-tertiary-fixed px-3 py-1.5 rounded-full">
                <?= $commentStats['approved'] ?? 0 ?> Approved
            </span>
            <span class="text-xs font-bold text-error bg-error-container px-3 py-1.5 rounded-full">
                <?= $commentStats['rejected'] ?? 0 ?> Rejected
            </span>
        </div>
    </div>

    <?php if (!empty($pendingComments)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($pendingComments as $comment): ?>
        <div class="bg-surface-container-lowest p-6 rounded-xl border-l-4 border-tertiary shadow-sm hover:shadow-md transition-shadow">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-sm font-bold text-on-secondary-container">
                        <?= strtoupper(substr($comment['prenom'] ?? 'U', 0, 1) . substr($comment['nom'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? '')) ?></p>
                        <p class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($comment['mail'] ?? '') ?></p>
                    </div>
                </div>
                <span class="text-xs text-on-surface-variant bg-surface-container px-2 py-1 rounded">
                    <?= date('M d, Y H:i', strtotime($comment['date_creation'])) ?>
                </span>
            </div>

            <!-- Article Context -->
            <div class="mb-3 text-xs text-on-surface-variant bg-surface-container-low px-3 py-2 rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">article</span>
                On: <span class="font-bold text-primary"><?= htmlspecialchars($comment['post_titre'] ?? 'Unknown Article') ?></span>
            </div>

            <!-- Comment Content -->
            <p class="text-sm text-on-surface leading-relaxed mb-5"><?= htmlspecialchars($comment['contenu']) ?></p>

            <!-- Actions -->
            <div class="flex gap-3">
                <a href="backOffice.php?controller=comment&action=approve&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=moderation" 
                   class="flex-1 py-2.5 text-xs font-bold rounded-lg bg-tertiary text-on-tertiary text-center hover:opacity-90 transition-opacity flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">check</span>
                    Approve
                </a>
                <a href="backOffice.php?controller=comment&action=reject&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=moderation" 
                   class="flex-1 py-2.5 text-xs font-bold rounded-lg bg-surface-container text-on-surface text-center hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">block</span>
                    Reject
                </a>
                <button onclick="showDeleteModal('backOffice.php?controller=comment&action=delete_comment&id=<?= $comment['id'] ?>&redirect=backOffice.php?action=moderation', 'comment')" 
                        class="flex-1 py-2.5 text-xs font-bold rounded-lg bg-error text-on-error text-center hover:opacity-90 transition-opacity flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Delete
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-surface-container-lowest rounded-2xl p-16 text-center">
        <span class="material-symbols-outlined text-6xl text-tertiary mb-4">verified</span>
        <h3 class="text-xl font-bold text-on-surface mb-2">All Clear!</h3>
        <p class="text-on-surface-variant">There are no comments pending moderation. Great job keeping things tidy!</p>
        <a href="backOffice.php?action=dashboard" class="inline-block mt-6 px-6 py-2.5 bg-primary text-on-primary rounded-lg font-semibold hover:opacity-90 transition-opacity">
            Back to Dashboard
        </a>
    </div>
    <?php endif; ?>
</div>
