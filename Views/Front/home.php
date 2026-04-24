<?php
/**
 * Front Office — Home Page
 * Faithful PHP rebuild of frontOffice.html template design
 */

// ---------- helpers ----------
function estimateReadTime(string $text): string {
    $words = str_word_count(strip_tags($text));
    return max(1, ceil($words / 200)) . ' min read';
}
function timeAgo(string $datetime): string {
    $diff = (new DateTime())->diff(new DateTime($datetime));
    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'mo ago';
    if ($diff->d > 0) return $diff->d . 'd ago';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'm ago';
    return 'Just now';
}
function fmt(int $n): string {
    return $n >= 1000 ? round($n / 1000, 1) . 'k' : (string)$n;
}

$catBadge = [
    'General Health'  => 'text-blue-700',
    'Mental Wellness' => 'text-violet-700',
    'Diet & Nutrition'=> 'text-emerald-700',
    'Active Living'   => 'text-orange-700',
    'Research'        => 'text-tertiary',
    'Journals'        => 'text-rose-700',
];
?>

<!-- ============================================================ -->
<!-- HERO — Full-Width Featured Article                             -->
<!-- ============================================================ -->
<?php if ($featuredPost): ?>
<section class="mb-16">
  <div class="relative w-full h-[540px] rounded-xl overflow-hidden shadow-2xl group">

    <?php if (!empty($featuredPost['image_url'])): ?>
    <img src="<?= htmlspecialchars($featuredPost['image_url']) ?>"
         alt="<?= htmlspecialchars($featuredPost['titre']) ?>"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"/>
    <?php else: ?>
    <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
    <?php endif; ?>

    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-on-primary-fixed/90 via-on-primary-fixed/40 to-transparent"></div>

    <!-- Content -->
    <div class="absolute bottom-0 left-0 p-12 w-full lg:w-2/3">
      <span class="inline-block px-4 py-1.5 rounded-full bg-primary-container text-on-primary-container font-headline text-xs font-bold tracking-wider uppercase mb-6">
        <?= htmlspecialchars($featuredPost['categorie']) ?>
      </span>

      <h1 class="text-white font-headline text-5xl font-extrabold tracking-tight mb-6 leading-tight">
        <?= htmlspecialchars($featuredPost['titre']) ?>
      </h1>

      <div class="flex items-center gap-4 text-white/90 font-label flex-wrap">
        <!-- Author avatar initials -->
        <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center font-bold text-sm flex-shrink-0">
          <?= strtoupper(substr($featuredPost['prenom'] ?? 'A', 0, 1)) ?>
        </div>
        <div>
          <p class="font-bold"><?= htmlspecialchars(($featuredPost['prenom'] ?? '') . ' ' . ($featuredPost['nom'] ?? '')) ?></p>
          <p class="text-xs opacity-75"><?= htmlspecialchars($featuredPost['role_name'] ?? 'Author') ?> &bull; <?= estimateReadTime($featuredPost['contenu']) ?></p>
        </div>
        <a href="/integration/magazine/article?id=<?= $featuredPost['id'] ?>"
           class="ml-auto bg-white text-primary px-8 py-3 rounded-lg font-headline font-bold text-sm hover:bg-surface-container-low transition-all active:scale-95">
          Read Article
        </a>
      </div>
    </div>

    <!-- Stats chips top-right -->
    <div class="absolute top-6 right-6 flex gap-2">
      <span class="flex items-center gap-1.5 px-3 py-1.5 bg-black/30 backdrop-blur-md rounded-full text-white text-xs font-semibold">
        <span class="material-symbols-outlined text-sm">visibility</span>
        <?= fmt((int)$featuredPost['views_count']) ?>
      </span>
      <span class="flex items-center gap-1.5 px-3 py-1.5 bg-black/30 backdrop-blur-md rounded-full text-white text-xs font-semibold">
        <span class="material-symbols-outlined text-sm">favorite</span>
        <?= fmt((int)$featuredPost['likes_count']) ?>
      </span>
    </div>

  </div>
</section>
<?php endif; ?>


<!-- ============================================================ -->
<!-- CONTENT GRID + SIDEBAR                                        -->
<!-- ============================================================ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

  <!-- ---- Main Feed (8 cols) ---- -->
  <div class="lg:col-span-8">

    <!-- Section heading -->
    <div class="flex items-center justify-between mb-8">
      <h2 class="font-headline text-2xl font-bold text-blue-900 tracking-tight">Latest Publications</h2>
      <div class="h-[2px] flex-grow mx-8 bg-surface-container-high rounded-full"></div>
    </div>

    <!-- 2-col article card grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <?php
      // Show up to 4 recent posts (skip featured if same)
      $cardPosts = array_filter($recentPosts, fn($p) => $p['id'] !== ($featuredPost['id'] ?? null));
      $cardPosts = array_slice(array_values($cardPosts), 0, 4);
      foreach ($cardPosts as $post):
        $badge = $catBadge[$post['categorie']] ?? 'text-tertiary';
      ?>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,77,153,0.03)] group flex flex-col stagger-item">

        <!-- Thumbnail -->
        <div class="relative h-56 overflow-hidden">
          <?php if (!empty($post['image_url'])): ?>
          <img src="<?= htmlspecialchars($post['image_url']) ?>"
               alt="<?= htmlspecialchars($post['titre']) ?>"
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"/>
          <?php else: ?>
          <div class="w-full h-full bg-gradient-to-br from-surface-container to-surface-container-high flex items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-outline/30">article</span>
          </div>
          <?php endif; ?>
          <span class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur-md rounded-md text-[10px] font-bold uppercase tracking-widest <?= $badge ?>">
            <?= htmlspecialchars($post['categorie']) ?>
          </span>
        </div>

        <!-- Body -->
        <div class="p-6 flex-grow flex flex-col">
          <p class="text-[11px] text-slate-400 font-label mb-2 uppercase tracking-wider">
            <?= timeAgo($post['date_publication'] ?? $post['date_creation']) ?>
            &bull; <?= estimateReadTime($post['contenu']) ?>
          </p>
          <h3 class="font-headline text-xl font-bold text-blue-900 mb-3 leading-snug line-clamp-2">
            <?= htmlspecialchars($post['titre']) ?>
          </h3>
          <p class="text-on-surface-variant text-sm line-clamp-2 mb-6 flex-grow">
            <?= htmlspecialchars(substr(strip_tags($post['contenu']), 0, 140)) ?>...
          </p>

          <!-- Footer row -->
          <div class="flex items-center justify-between border-t border-surface-container pt-4">
            <div class="flex items-center gap-4 text-slate-400">
              <button class="like-btn flex items-center gap-1.5 hover:text-red-500 cursor-pointer transition-colors"
                      data-post-id="<?= $post['id'] ?>">
                <span class="material-symbols-outlined text-[20px] like-icon">favorite</span>
                <span class="text-xs font-bold like-count"><?= fmt((int)$post['likes_count']) ?></span>
              </button>
              <a href="/integration/magazine/article?id=<?= $post['id'] ?>#comments"
                 class="flex items-center gap-1.5 hover:text-blue-500 cursor-pointer transition-colors">
                <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
                <span class="text-xs font-bold">
                  <?= isset($post['comment_count']) ? $post['comment_count'] : '' ?>
                </span>
              </a>
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

      <!-- CTA card if fewer than 4 posts -->
      <?php if (count($cardPosts) < 4): ?>
      <div class="bg-gradient-to-br from-primary to-primary-container rounded-xl p-8 flex flex-col justify-center text-on-primary">
        <span class="material-symbols-outlined text-4xl mb-4 opacity-80">edit_square</span>
        <h3 class="font-headline text-xl font-bold mb-2">More Coming Soon</h3>
        <p class="text-sm opacity-80 mb-6">Our editorial team is publishing new content regularly.</p>
        <a href="/integration/magazine/category?cat=General Health"
           class="bg-white text-primary px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-surface-container-low transition-all self-start active:scale-95">
          Browse Topics →
        </a>
      </div>
      <?php endif; ?>
    </div><!-- /grid -->

    <!-- ---- Second Row: Wide "Spotlight" Card ---- -->
    <?php
    $spotlights = array_slice(array_values(array_filter($recentPosts,
        fn($p) => $p['id'] !== ($featuredPost['id'] ?? null) && !in_array($p['id'], array_column($cardPosts, 'id'))
    )), 0, 2);
    ?>
    <?php if (!empty($spotlights)): ?>
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
      <?php foreach ($spotlights as $sp):
        $badge = $catBadge[$sp['categorie']] ?? 'text-tertiary';
      ?>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,77,153,0.03)] group flex gap-5 p-5 items-start">
        <!-- Thumbnail -->
        <div class="relative w-28 h-28 flex-shrink-0 rounded-lg overflow-hidden">
          <?php if (!empty($sp['image_url'])): ?>
          <img src="<?= htmlspecialchars($sp['image_url']) ?>" alt="<?= htmlspecialchars($sp['titre']) ?>"
               class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
          <?php else: ?>
          <div class="w-full h-full bg-surface-container-high flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl text-outline/30">article</span>
          </div>
          <?php endif; ?>
        </div>
        <!-- Text -->
        <div>
          <span class="text-[10px] font-bold uppercase tracking-widest <?= $badge ?>"><?= htmlspecialchars($sp['categorie']) ?></span>
          <h3 class="font-headline font-bold text-blue-900 text-sm mt-1 mb-2 leading-snug line-clamp-3">
            <?= htmlspecialchars($sp['titre']) ?>
          </h3>
          <div class="flex items-center gap-3">
            <a href="/integration/magazine/article?id=<?= $sp['id'] ?>"
               class="text-primary text-xs font-bold flex items-center gap-0.5 group/btn">
              Read More
              <span class="material-symbols-outlined text-sm transition-transform group-hover/btn:translate-x-0.5">chevron_right</span>
            </a>
            <span class="text-[11px] text-slate-400"><?= estimateReadTime($sp['contenu']) ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div><!-- /col-8 -->


  <!-- ---- Sidebar (4 cols) ---- -->
  <aside class="lg:col-span-4 space-y-12">

    <!-- Popular This Week (by likes) -->
    <div class="bg-surface-container-low rounded-xl p-8">
      <h4 class="font-headline text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined text-rose-500">favorite</span>
        Most Liked
      </h4>
      <div class="space-y-6">
        <?php if (!empty($popularPosts)): ?>
          <?php foreach ($popularPosts as $pIdx => $pPost): ?>
          <div class="flex gap-4 group cursor-pointer">
            <span class="text-3xl font-black text-blue-100 font-headline leading-none">
              <?= str_pad($pIdx + 1, 2, '0', STR_PAD_LEFT) ?>
            </span>
            <div class="flex-1 min-w-0">
              <a href="/integration/magazine/article?id=<?= $pPost['id'] ?>">
                <h5 class="text-sm font-bold text-blue-950 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                  <?= htmlspecialchars($pPost['titre']) ?>
                </h5>
              </a>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-[11px] text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($pPost['categorie']) ?></span>
                <span class="flex items-center gap-0.5 text-[11px] text-rose-400 font-bold">
                  <span class="material-symbols-outlined text-[13px]">favorite</span>
                  <?= fmt((int)$pPost['likes_count']) ?>
                </span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-sm text-on-surface-variant text-center py-4">No articles yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Most Viewed -->
    <div class="bg-surface-container-low rounded-xl p-8">
      <h4 class="font-headline text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">trending_up</span>
        Most Viewed
      </h4>
      <div class="space-y-6">
        <?php if (!empty($mostViewedPosts)): ?>
          <?php foreach ($mostViewedPosts as $vIdx => $vPost): ?>
          <div class="flex gap-4 group cursor-pointer">
            <span class="text-3xl font-black text-blue-100 font-headline leading-none">
              <?= str_pad($vIdx + 1, 2, '0', STR_PAD_LEFT) ?>
            </span>
            <div class="flex-1 min-w-0">
              <a href="/integration/magazine/article?id=<?= $vPost['id'] ?>">
                <h5 class="text-sm font-bold text-blue-950 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                  <?= htmlspecialchars($vPost['titre']) ?>
                </h5>
              </a>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-[11px] text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($vPost['categorie']) ?></span>
                <span class="flex items-center gap-0.5 text-[11px] text-blue-400 font-bold">
                  <span class="material-symbols-outlined text-[13px]">visibility</span>
                  <?= fmt((int)$vPost['views_count']) ?>
                </span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-sm text-on-surface-variant text-center py-4">No articles yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Newsletter Signup -->
    <div class="bg-primary rounded-xl p-8 text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
      <div class="absolute -left-6 -bottom-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
      <h4 class="font-headline text-xl font-bold mb-3 relative z-10">
        Medical Excellence in your inbox.
      </h4>
      <p class="text-on-primary-container text-sm mb-6 relative z-10 opacity-90">
        Receive our weekly selection of the most impactful healthcare research and insights.
      </p>
      <div class="space-y-3 relative z-10">
        <input class="w-full bg-white/10 border-none rounded-lg py-3 px-4 text-sm placeholder:text-white/60 focus:ring-2 focus:ring-tertiary-fixed/30 text-white"
               placeholder="Your email address" type="email"/>
        <button class="w-full bg-tertiary-fixed text-on-tertiary-fixed font-headline font-bold py-3 rounded-lg hover:bg-tertiary-fixed-dim transition-all active:scale-[0.98]">
          Subscribe
        </button>
      </div>
      <p class="text-[10px] mt-4 text-center text-white/50">Your privacy is guaranteed.</p>
    </div>

    <!-- Category Tags Cloud -->
    <div class="p-2">
      <h4 class="font-headline text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Key Topics</h4>
      <div class="flex flex-wrap gap-2">
        <?php
        $tags = ['General Health', 'Research', 'Mental Wellness', 'Diet & Nutrition', 'Active Living', 'Journals', 'Telemedicine', 'Neuroscience'];
        foreach ($tags as $tag):
          $isCat = in_array($tag, ['General Health','Research','Mental Wellness','Diet & Nutrition','Active Living','Journals']);
          $href  = $isCat ? "/integration/magazine/category?cat=" . urlencode($tag) : "#";
        ?>
        <a class="px-4 py-2 bg-surface-container-high rounded-lg text-xs font-bold text-slate-600 hover:bg-primary hover:text-white transition-all"
           href="<?= $href ?>"><?= htmlspecialchars($tag) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Categories by count from DB stats -->
    <?php if (!empty($categories)): ?>
    <div class="bg-surface-container-low rounded-xl p-6">
      <h4 class="font-headline text-sm font-bold text-slate-400 uppercase tracking-widest mb-5">Browse Categories</h4>
      <div class="space-y-3">
        <?php foreach ($categories as $catName):
          $badge = $catBadge[$catName] ?? 'text-tertiary';
        ?>
        <a href="/integration/magazine/category?cat=<?= urlencode($catName) ?>"
           class="flex items-center justify-between group py-1">
          <span class="text-sm font-semibold text-on-surface-variant group-hover:text-primary transition-colors">
            <?= htmlspecialchars($catName) ?>
          </span>
          <span class="material-symbols-outlined text-sm text-outline group-hover:translate-x-1 transition-transform">chevron_right</span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </aside><!-- /sidebar -->

</div><!-- /grid -->
