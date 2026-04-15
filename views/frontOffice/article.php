<?php
/**
 * Front Office — Single Article View
 * Full article display with comments section
 */

// Re-use helpers if not already defined
if (!function_exists('estimateReadTime')) {
    function estimateReadTime($text) {
        $words = str_word_count(strip_tags($text));
        return max(1, ceil($words / 200)) . ' min read';
    }
}
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->diff($past);
        if ($diff->y > 0) return $diff->y . 'y ago';
        if ($diff->m > 0) return $diff->m . 'mo ago';
        if ($diff->d > 0) return $diff->d . 'd ago';
        if ($diff->h > 0) return $diff->h . 'h ago';
        if ($diff->i > 0) return $diff->i . 'm ago';
        return 'Just now';
    }
}
if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if ($num >= 1000) return round($num / 1000, 1) . 'k';
        return $num;
    }
}
?>

<article class="max-w-4xl">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-on-surface-variant mb-6">
        <a href="frontOffice.php" class="hover:text-primary transition-colors">Home</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <a href="frontOffice.php?action=category&cat=<?= urlencode($post['categorie']) ?>" class="hover:text-primary transition-colors"><?= htmlspecialchars($post['categorie']) ?></a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-on-surface font-medium truncate max-w-[200px]"><?= htmlspecialchars($post['titre']) ?></span>
    </div>

    <!-- Article Header -->
    <header class="mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed rounded-full text-xs font-bold uppercase tracking-tighter mb-4">
            <?= htmlspecialchars($post['categorie']) ?>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900 tracking-tight leading-tight mb-6">
            <?= htmlspecialchars($post['titre']) ?>
        </h1>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold border-2 border-primary-fixed">
                <?= strtoupper(substr($post['prenom'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <p class="font-bold text-on-surface">
                    <?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?>
                    <span class="text-slate-400 font-normal ml-2"><?= htmlspecialchars($post['role_name'] ?? '') ?></span>
                </p>
                <p class="text-sm text-slate-400 flex items-center gap-3">
                    <span><?= date('F d, Y', strtotime($post['date_publication'] ?? $post['date_creation'])) ?></span>
                    <span>·</span>
                    <span><?= estimateReadTime($post['contenu']) ?></span>
                    <span>·</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                        <?= number_format($post['views_count']) ?> views
                    </span>
                </p>
            </div>
        </div>
    </header>

    <!-- Cover Image -->
    <?php if (!empty($post['image_url'])): ?>
    <div class="mb-10 rounded-2xl overflow-hidden shadow-[0_20px_50px_rgba(0,77,153,0.05)]">
        <img alt="<?= htmlspecialchars($post['titre']) ?>" class="w-full h-auto max-h-[500px] object-cover" src="<?= htmlspecialchars($post['image_url']) ?>"/>
    </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="prose prose-lg max-w-none mb-12">
        <div class="text-on-surface leading-relaxed text-[17px] space-y-4">
            <?php
            // Convert plain text with line breaks to paragraphs
            $paragraphs = explode("\n\n", $post['contenu']);
            foreach ($paragraphs as $para):
                $para = trim($para);
                if (empty($para)) continue;
            ?>
            <p><?= nl2br(htmlspecialchars($para)) ?></p>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Interactions Bar -->
    <div class="flex items-center justify-between py-6 border-y border-slate-100 mb-12">
        <div class="flex items-center gap-6">
            <button class="flex items-center gap-2 group like-btn" data-post-id="<?= $post['id'] ?>">
                <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-red-500 transition-colors like-icon">favorite</span>
                <span class="text-sm font-bold text-slate-500 like-count"><?= formatNumber($post['likes_count']) ?></span>
            </button>
            <a href="#comments" class="flex items-center gap-2 text-slate-500 hover:text-blue-500 transition-colors">
                <span class="material-symbols-outlined text-2xl">forum</span>
                <span class="text-sm font-bold"><?= $commentCount ?> Comments</span>
            </a>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-on-surface-variant">Share:</span>
            <button onclick="navigator.clipboard.writeText(window.location.href); showToast('Link copied!')" class="p-2 hover:bg-surface-container-high rounded-full transition-colors" title="Copy link">
                <span class="material-symbols-outlined text-on-surface-variant text-xl">link</span>
            </button>
        </div>
    </div>

    <!-- Comments Section -->
    <section id="comments" class="space-y-8">
        <h2 class="text-2xl font-bold text-blue-900 flex items-center gap-3">
            <span class="material-symbols-outlined">forum</span>
            Community Discussion
            <span class="text-sm font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full"><?= $commentCount ?></span>
        </h2>

        <!-- Add Comment Form -->
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm">
            <form method="POST" action="frontOffice.php?action=add_comment" class="space-y-4">
                <input type="hidden" name="id_post" value="<?= $post['id'] ?>"/>
                <input type="hidden" name="id_utilisateur" value="4"/>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container font-bold text-sm flex-shrink-0">
                        U
                    </div>
                    <div class="flex-1">
                        <textarea name="contenu" rows="3" required maxlength="1000"
                                  placeholder="Share your thoughts on this article..."
                                  class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-on-surface focus:ring-2 focus:ring-tertiary/30 focus:border-tertiary transition-all resize-none text-sm"></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-on-surface-variant">Your comment will appear after moderation.</p>
                            <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-lg font-semibold text-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">send</span>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        <?php if (!empty($comments)): ?>
        <div class="space-y-4">
            <?php foreach ($comments as $comment): ?>
            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border-l-4 border-tertiary-fixed hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">
                            <?= strtoupper(substr($comment['prenom'] ?? 'U', 0, 1) . substr($comment['nom'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? '')) ?></p>
                            <p class="text-[10px] text-on-surface-variant"><?= timeAgo($comment['date_creation']) ?></p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($comment['contenu']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-2 text-outline">chat_bubble_outline</span>
            <p class="text-lg font-semibold mb-1">No comments yet</p>
            <p class="text-sm">Be the first to share your thoughts on this article!</p>
        </div>
        <?php endif; ?>
    </section>

    <!-- Related Articles -->
    <?php if (!empty($relatedPosts)): ?>
    <section class="mt-16 pt-8 border-t border-slate-100">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($relatedPosts as $rPost): ?>
            <a href="frontOffice.php?action=view&id=<?= $rPost['id'] ?>" class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all">
                <h3 class="font-bold text-blue-900 mb-2 hover:text-primary transition-colors"><?= htmlspecialchars($rPost['titre']) ?></h3>
                <p class="text-xs text-slate-500 line-clamp-2 mb-3"><?= htmlspecialchars(substr(strip_tags($rPost['contenu']), 0, 100)) ?>...</p>
                <div class="flex items-center justify-between text-xs text-on-surface-variant">
                    <span><?= htmlspecialchars(($rPost['prenom'] ?? '') . ' ' . ($rPost['nom'] ?? '')) ?></span>
                    <span><?= estimateReadTime($rPost['contenu']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</article>
