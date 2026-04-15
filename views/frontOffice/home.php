<?php
/**
 * Front Office — Home Page View
 * Featured article, recent feed, and community discussion
 */

// Helper function for read time
function estimateReadTime($text) {
    $words = str_word_count(strip_tags($text));
    $minutes = max(1, ceil($words / 200));
    return $minutes . ' min read';
}

// Helper function for time ago
function timeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min ago';
    return 'Just now';
}

// Helper function for formatting likes
function formatNumber($num) {
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'k';
    }
    return $num;
}
?>

<!-- Hero Header -->
<header class="mb-12">
    <div class="inline-flex items-center gap-2 px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed rounded-full text-xs font-bold uppercase tracking-tighter mb-4">
        <span class="w-1.5 h-1.5 bg-tertiary rounded-full animate-pulse"></span>
        Trending Today
    </div>
    <h1 class="text-4xl md:text-5xl font-extrabold text-blue-900 tracking-tight leading-tight mb-4">
        Curated Healthcare <br/><span class="text-tertiary">Knowledge Base</span>
    </h1>
    <p class="text-lg text-slate-500 max-w-2xl leading-relaxed">
        Verified medical insights and news directly from our clinical staff to help you navigate your wellness journey.
    </p>
</header>

<!-- Articles Feed (Asymmetric Layout) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Featured Article Card -->
    <?php if ($featuredPost): ?>
    <article class="lg:col-span-8 bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,77,153,0.05)] border-t-2 border-tertiary-fixed hover:shadow-[0_25px_60px_rgba(0,77,153,0.08)] transition-shadow">
        <?php if (!empty($featuredPost['image_url'])): ?>
        <img alt="<?= htmlspecialchars($featuredPost['titre']) ?>" class="w-full h-80 object-cover" src="<?= htmlspecialchars($featuredPost['image_url']) ?>"/>
        <?php else: ?>
        <div class="w-full h-80 bg-gradient-to-br from-primary/10 to-tertiary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-8xl text-primary/20">article</span>
        </div>
        <?php endif; ?>
        <div class="p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm border-2 border-primary-fixed">
                    <?= strtoupper(substr($featuredPost['prenom'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-on-surface">
                        <?= htmlspecialchars(($featuredPost['prenom'] ?? '') . ' ' . ($featuredPost['nom'] ?? '')) ?>
                        <span class="text-slate-400 font-normal ml-2"><?= htmlspecialchars($featuredPost['role_name'] ?? '') ?></span>
                    </p>
                    <p class="text-xs text-slate-400">
                        <?= date('M d, Y', strtotime($featuredPost['date_publication'] ?? $featuredPost['date_creation'])) ?>
                        · <?= estimateReadTime($featuredPost['contenu']) ?>
                    </p>
                </div>
            </div>
            <h2 class="text-3xl font-extrabold text-blue-900 mb-4 leading-tight"><?= htmlspecialchars($featuredPost['titre']) ?></h2>
            <p class="text-slate-600 mb-8 leading-relaxed">
                <?= htmlspecialchars(substr(strip_tags($featuredPost['contenu']), 0, 250)) ?>...
            </p>
            <!-- Interactions Bar -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                <div class="flex items-center gap-6">
                    <button class="flex items-center gap-2 group like-btn" data-post-id="<?= $featuredPost['id'] ?>">
                        <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-red-500 transition-colors like-icon">favorite</span>
                        <span class="text-sm font-bold text-slate-500 like-count"><?= formatNumber($featuredPost['likes_count']) ?></span>
                    </button>
                    <a href="frontOffice.php?action=view&id=<?= $featuredPost['id'] ?>#comments" class="flex items-center gap-2 group">
                        <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-blue-500 transition-colors">forum</span>
                        <span class="text-sm font-bold text-slate-500"><?= $featuredPost['comment_count'] ?? 0 ?> Comments</span>
                    </a>
                </div>
                <a href="frontOffice.php?action=view&id=<?= $featuredPost['id'] ?>" class="flex items-center gap-2 text-primary font-bold hover:gap-3 transition-all">
                    <span>Read Full Article</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </article>
    <?php endif; ?>

    <!-- Sidebar Content (Secondary Feed) -->
    <div class="lg:col-span-4 space-y-8">
        <!-- Recent Article Cards -->
        <?php 
        $secondaryPosts = array_slice($recentPosts, 1, 3);
        $categoryColors = [
            'General Health' => ['text-tertiary', 'bg-tertiary-fixed/30'],
            'Mental Wellness' => ['text-primary', 'bg-primary-fixed/30'],
            'Diet & Nutrition' => ['text-tertiary', 'bg-tertiary-fixed/30'],
            'Active Living' => ['text-secondary', 'bg-secondary-fixed/30'],
            'Research' => ['text-secondary', 'bg-secondary-fixed/30'],
            'Journals' => ['text-primary', 'bg-primary-fixed/30'],
        ];
        foreach ($secondaryPosts as $index => $sPost): 
            $colors = $categoryColors[$sPost['categorie']] ?? ['text-tertiary', 'bg-tertiary-fixed/30'];
        ?>
        <article class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_10px_30px_rgba(0,77,153,0.03)] <?= $index === 0 ? 'border-t-2 border-secondary-container' : '' ?> hover:shadow-[0_15px_40px_rgba(0,77,153,0.06)] transition-shadow">
            <a href="frontOffice.php?action=view&id=<?= $sPost['id'] ?>">
                <h3 class="text-xl font-bold text-blue-900 mb-3 hover:text-primary transition-colors"><?= htmlspecialchars($sPost['titre']) ?></h3>
            </a>
            <p class="text-sm text-slate-500 mb-4 line-clamp-2"><?= htmlspecialchars(substr(strip_tags($sPost['contenu']), 0, 120)) ?>...</p>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button class="like-btn flex items-center gap-1" data-post-id="<?= $sPost['id'] ?>">
                        <span class="material-symbols-outlined text-slate-400 text-lg like-icon">favorite</span>
                        <span class="text-xs font-bold text-slate-400 like-count"><?= formatNumber($sPost['likes_count']) ?></span>
                    </button>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded <?= $colors[0] ?> <?= $colors[1] ?>"><?= htmlspecialchars($sPost['categorie']) ?></span>
            </div>
        </article>
        <?php endforeach; ?>

        <!-- Community Discussion Mini-View -->
        <div class="bg-surface-container-high rounded-2xl p-6">
            <h4 class="font-headline font-bold text-sm text-blue-900 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">comment</span>
                Recent Community Discussion
            </h4>
            <div class="space-y-3 text-center py-4">
                <span class="material-symbols-outlined text-2xl text-outline">forum</span>
                <p class="text-xs text-on-surface-variant">Read articles and join the discussion!</p>
            </div>
        </div>
    </div>
</div>

<!-- Bento Grid of More Articles -->
<?php
$morePosts = array_slice($recentPosts, 4);
if (!empty($morePosts)):
?>
<section class="mt-16">
    <h2 class="text-2xl font-bold text-blue-900 mb-8">Clinical Insights</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($morePosts as $mIndex => $mPost): ?>
        <div class="<?= $mIndex === 0 ? 'md:col-span-2' : '' ?> bg-surface-container-lowest p-6 rounded-2xl flex flex-col justify-between hover:scale-[1.01] transition-transform shadow-[0_10px_30px_rgba(0,77,153,0.03)] <?= $mIndex === 0 ? 'border-b-4 border-primary' : '' ?>">
            <div>
                <span class="text-[10px] font-bold text-primary-container tracking-widest uppercase"><?= htmlspecialchars($mPost['categorie']) ?></span>
                <a href="frontOffice.php?action=view&id=<?= $mPost['id'] ?>">
                    <h3 class="text-xl font-bold text-blue-900 mt-2 mb-4 leading-tight hover:text-primary transition-colors"><?= htmlspecialchars($mPost['titre']) ?></h3>
                </a>
                <p class="text-sm text-slate-500"><?= htmlspecialchars(substr(strip_tags($mPost['contenu']), 0, 150)) ?>...</p>
            </div>
            <div class="mt-8 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">
                        <?= strtoupper(substr($mPost['prenom'] ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="text-xs text-on-surface-variant"><?= htmlspecialchars(($mPost['prenom'] ?? '') . ' ' . ($mPost['nom'] ?? '')) ?></span>
                </div>
                <span class="text-xs text-slate-400"><?= estimateReadTime($mPost['contenu']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>
<?php endif; ?>
