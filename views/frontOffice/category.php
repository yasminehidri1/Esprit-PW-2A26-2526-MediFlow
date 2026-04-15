<?php
/**
 * Front Office — Category View
 * Articles filtered by category
 */
if (!function_exists('estimateReadTime')) {
    function estimateReadTime($text) {
        $words = str_word_count(strip_tags($text));
        return max(1, ceil($words / 200)) . ' min read';
    }
}
if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if ($num >= 1000) return round($num / 1000, 1) . 'k';
        return $num;
    }
}

$categoryIcons = [
    'General Health'   => 'medical_services',
    'Mental Wellness'  => 'psychology',
    'Diet & Nutrition' => 'nutrition',
    'Active Living'    => 'fitness_center',
    'Research'         => 'biotech',
    'Journals'         => 'menu_book',
];
$icon = $categoryIcons[$categorie] ?? 'article';
?>

<!-- Category Header -->
<header class="mb-10">
    <div class="flex items-center gap-4 mb-4">
        <a href="frontOffice.php" class="p-2 hover:bg-surface-container-high rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="w-14 h-14 bg-primary-container rounded-2xl flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined text-3xl"><?= $icon ?></span>
        </div>
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight"><?= htmlspecialchars($categorie) ?></h1>
            <p class="text-sm text-slate-500"><?= $result['total'] ?? 0 ?> article<?= ($result['total'] ?? 0) !== 1 ? 's' : '' ?> in this category</p>
        </div>
    </div>
</header>

<!-- Articles Grid -->
<?php if (!empty($result['data'])): ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($result['data'] as $post): ?>
    <article class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-[0_10px_30px_rgba(0,77,153,0.03)] hover:shadow-[0_15px_40px_rgba(0,77,153,0.06)] hover:scale-[1.01] transition-all">
        <?php if (!empty($post['image_url'])): ?>
        <img alt="<?= htmlspecialchars($post['titre']) ?>" class="w-full h-48 object-cover" src="<?= htmlspecialchars($post['image_url']) ?>"/>
        <?php else: ?>
        <div class="w-full h-48 bg-gradient-to-br from-primary/5 to-tertiary/5 flex items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-primary/15"><?= $icon ?></span>
        </div>
        <?php endif; ?>
        <div class="p-5">
            <a href="frontOffice.php?action=view&id=<?= $post['id'] ?>">
                <h3 class="font-bold text-blue-900 mb-2 leading-snug hover:text-primary transition-colors"><?= htmlspecialchars($post['titre']) ?></h3>
            </a>
            <p class="text-sm text-slate-500 line-clamp-2 mb-4"><?= htmlspecialchars(substr(strip_tags($post['contenu']), 0, 120)) ?>...</p>
            <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center text-[10px] font-bold text-on-secondary-container">
                        <?= strtoupper(substr($post['prenom'] ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="text-xs text-on-surface-variant"><?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?></span>
                </div>
                <div class="flex items-center gap-3 text-xs text-on-surface-variant">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">favorite</span>
                        <?= formatNumber($post['likes_count']) ?>
                    </span>
                    <span><?= estimateReadTime($post['contenu']) ?></span>
                </div>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if (($result['totalPages'] ?? 0) > 1): ?>
<div class="flex items-center justify-center gap-2 mt-10">
    <?php for ($i = 1; $i <= $result['totalPages']; $i++): ?>
    <a href="frontOffice.php?action=category&cat=<?= urlencode($categorie) ?>&page=<?= $i ?>" 
       class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-colors <?= $i == ($result['page'] ?? 1)
           ? 'bg-primary text-on-primary shadow-sm' 
           : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="text-center py-20 text-on-surface-variant">
    <span class="material-symbols-outlined text-6xl mb-4 text-outline"><?= $icon ?></span>
    <h3 class="text-xl font-bold text-on-surface mb-2">No articles in this category yet</h3>
    <p class="text-sm mb-6">Check back soon or explore other categories.</p>
    <a href="frontOffice.php" class="inline-block px-6 py-2.5 bg-primary text-on-primary rounded-lg font-semibold hover:opacity-90 transition-opacity">
        Back to Home
    </a>
</div>
<?php endif; ?>
