<?php
/**
 * Front Office — Category View
 * Same design system as frontOffice.html template
 */
if (!function_exists('estimateReadTime')) {
    function estimateReadTime($text) {
        $words = str_word_count(strip_tags($text));
        return max(1, ceil($words / 200)) . ' min read';
    }
}
if (!function_exists('fmt')) {
    function fmt($n) { return $n >= 1000 ? round($n/1000,1).'k' : $n; }
}
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $diff = (new DateTime())->diff(new DateTime($datetime));
        if ($diff->y>0) return $diff->y.'y ago';
        if ($diff->m>0) return $diff->m.'mo ago';
        if ($diff->d>0) return $diff->d.'d ago';
        if ($diff->h>0) return $diff->h.'h ago';
        if ($diff->i>0) return $diff->i.'m ago';
        return 'Just now';
    }
}

$catIcons = [
    'General Health'   => 'medical_services',
    'Mental Wellness'  => 'psychology',
    'Diet & Nutrition' => 'nutrition',
    'Active Living'    => 'fitness_center',
    'Research'         => 'biotech',
    'Journals'         => 'menu_book',
];
$catBadge = [
    'General Health'   => 'text-blue-700',
    'Mental Wellness'  => 'text-violet-700',
    'Diet & Nutrition' => 'text-emerald-700',
    'Active Living'    => 'text-orange-700',
    'Research'         => 'text-tertiary',
    'Journals'         => 'text-rose-700',
];
$icon  = $catIcons[$categorie]  ?? 'article';
$badge = $catBadge[$categorie]  ?? 'text-tertiary';
?>

<!-- Category Header -->
<div class="flex items-center gap-4 mb-4">
  <a href="/integration/magazine" class="p-2 hover:bg-surface-container-high rounded-lg transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div class="w-12 h-12 bg-primary-container rounded-xl flex items-center justify-center text-on-primary flex-shrink-0">
    <span class="material-symbols-outlined text-2xl"><?= $icon ?></span>
  </div>
  <div>
    <h1 class="font-headline text-3xl font-extrabold text-blue-900 tracking-tight"><?= htmlspecialchars($categorie) ?></h1>
    <p class="text-sm text-slate-500"><?= $result['total'] ?? 0 ?> article<?= ($result['total'] ?? 0) !== 1 ? 's' : '' ?> in this category</p>
  </div>
</div>

<!-- Category filter pills -->
<div class="flex flex-wrap gap-2 mb-10">
  <?php
  $allCats = ['General Health','Mental Wellness','Diet & Nutrition','Active Living','Research','Journals'];
  foreach ($allCats as $c):
  ?>
  <a href="/integration/magazine/category?cat=<?= urlencode($c) ?>"
     class="px-4 py-2 rounded-lg text-xs font-bold transition-all
            <?= $c === $categorie
               ? 'bg-primary text-on-primary shadow-sm'
               : 'bg-surface-container-high text-slate-600 hover:bg-primary hover:text-white' ?>">
    <?= htmlspecialchars($c) ?>
  </a>
  <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

  <!-- Articles Grid -->
  <div class="lg:col-span-8">

    <?php if (!empty($result['data'])): ?>

    <!-- Section heading -->
    <div class="flex items-center mb-8">
      <h2 class="font-headline text-2xl font-bold text-blue-900 tracking-tight whitespace-nowrap">
        <?= htmlspecialchars($categorie) ?>
      </h2>
      <div class="h-[2px] flex-grow ml-8 bg-surface-container-high rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <?php foreach ($result['data'] as $post): ?>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,77,153,0.03)] group flex flex-col stagger-item">

        <!-- Thumbnail -->
        <div class="relative h-56 overflow-hidden">
          <?php if (!empty($post['image_url'])): ?>
          <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="<?= htmlspecialchars($post['titre']) ?>"
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"/>
          <?php else: ?>
          <div class="w-full h-full bg-gradient-to-br from-surface-container to-surface-container-high flex items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-outline/30"><?= $icon ?></span>
          </div>
          <?php endif; ?>
          <span class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur-md rounded-md text-[10px] font-bold uppercase tracking-widest <?= $badge ?>">
            <?= htmlspecialchars($post['categorie']) ?>
          </span>
          <span class="absolute top-4 right-4 px-2 py-1 bg-black/30 backdrop-blur-md rounded text-[10px] font-semibold text-white">
            <?= estimateReadTime($post['contenu']) ?>
          </span>
        </div>

        <!-- Body -->
        <div class="p-6 flex-grow flex flex-col">
          <p class="text-[11px] text-slate-400 font-label mb-2 uppercase tracking-wider">
            <?= timeAgo($post['date_publication'] ?? $post['date_creation']) ?>
          </p>
          <h3 class="font-headline text-xl font-bold text-blue-900 mb-3 leading-snug line-clamp-2">
            <?= htmlspecialchars($post['titre']) ?>
          </h3>
          <p class="text-on-surface-variant text-sm line-clamp-2 mb-6 flex-grow">
            <?= htmlspecialchars(substr(strip_tags($post['contenu']), 0, 140)) ?>...
          </p>

          <!-- Footer -->
          <div class="flex items-center justify-between border-t border-surface-container pt-4">
            <div class="flex items-center gap-4 text-slate-400">
              <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[20px]">favorite</span>
                <span class="text-xs font-bold"><?= fmt((int)$post['likes_count']) ?></span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[20px]">visibility</span>
                <span class="text-xs font-bold"><?= fmt((int)$post['views_count']) ?></span>
              </div>
            </div>
            <a href="/integration/magazine/article?id=<?= $post['id'] ?>"
               class="text-primary font-headline text-sm font-bold flex items-center gap-1 group/btn">
              Read More
              <span class="material-symbols-outlined text-[18px] transition-transform group-hover/btn:translate-x-1">chevron_right</span>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (($result['totalPages'] ?? 0) > 1): ?>
    <div class="flex items-center justify-center gap-2 mt-12">
      <?php for ($i = 1; $i <= $result['totalPages']; $i++): ?>
      <a href="/integration/magazine/category?cat=<?= urlencode($categorie) ?>&page=<?= $i ?>"
         class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-bold transition-colors
                <?= $i == ($result['page'] ?? 1)
                   ? 'bg-primary text-on-primary'
                   : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' ?>">
        <?= $i ?>
      </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="text-center py-20">
      <span class="material-symbols-outlined text-6xl text-outline mb-4"><?= $icon ?></span>
      <h3 class="font-headline text-xl font-bold text-on-surface mb-2">No articles in this category yet</h3>
      <p class="text-sm text-on-surface-variant mb-6">Check back soon or explore other categories.</p>
      <a href="/integration/magazine" class="inline-block px-6 py-2.5 bg-primary text-on-primary rounded-lg font-headline font-bold hover:opacity-90 transition-opacity">
        Back to Home
      </a>
    </div>
    <?php endif; ?>

  </div><!-- /col-8 -->

  <!-- Sidebar (4 cols) -->
  <aside class="lg:col-span-4 space-y-10">

    <!-- Popular list -->
    <?php if (!empty($result['data'])): ?>
    <div class="bg-surface-container-low rounded-xl p-8">
      <h4 class="font-headline text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">trending_up</span>
        Most Viewed
      </h4>
      <div class="space-y-5">
        <?php foreach (array_slice($result['data'], 0, 4) as $pIdx => $pPost): ?>
        <div class="flex gap-4 group">
          <span class="text-3xl font-black text-blue-100 font-headline leading-none">
            <?= str_pad($pIdx + 1, 2, '0', STR_PAD_LEFT) ?>
          </span>
          <div>
            <a href="/integration/magazine/article?id=<?= $pPost['id'] ?>">
              <h5 class="text-sm font-bold text-blue-950 group-hover:text-primary transition-colors leading-snug">
                <?= htmlspecialchars($pPost['titre']) ?>
              </h5>
            </a>
            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="material-symbols-outlined text-xs">visibility</span>
              <?= fmt((int)$pPost['views_count']) ?> views
            </p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Newsletter -->
    <?php include __DIR__ . '/_newsletter_box.php'; ?>

    <!-- Other categories -->
    <div class="p-2">
      <h4 class="font-headline text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Other Categories</h4>
      <div class="flex flex-wrap gap-2">
        <?php
        $allCats2 = ['General Health','Mental Wellness','Diet & Nutrition','Active Living','Research','Journals'];
        foreach (array_filter($allCats2, fn($c) => $c !== $categorie) as $oc):
        ?>
        <a href="/integration/magazine/category?cat=<?= urlencode($oc) ?>"
           class="px-4 py-2 bg-surface-container-high rounded-lg text-xs font-bold text-slate-600 hover:bg-primary hover:text-white transition-all">
          <?= htmlspecialchars($oc) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

  </aside>

</div>
