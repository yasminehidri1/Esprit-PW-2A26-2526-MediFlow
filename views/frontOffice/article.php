<?php
/**
 * Front Office — Single Article View
 * Same design system as frontOffice.html template
 */
// alreadyLiked is passed by PostController::viewArticle()
$alreadyLiked = $alreadyLiked ?? false;
if (!function_exists('estimateReadTime')) {
    function estimateReadTime($text) {
        $words = str_word_count(strip_tags($text));
        return max(1, ceil($words / 200)) . ' min read';
    }
}
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $diff = (new DateTime())->diff(new DateTime($datetime));
        if ($diff->y > 0) return $diff->y . 'y ago';
        if ($diff->m > 0) return $diff->m . 'mo ago';
        if ($diff->d > 0) return $diff->d . 'd ago';
        if ($diff->h > 0) return $diff->h . 'h ago';
        if ($diff->i > 5) return $diff->i . 'm ago';  // Show "Xm ago" only after 5 minutes
        return 'Just now';  // Show "Just now" for first 5 minutes
    }
}
if (!function_exists('fmt')) {
    function fmt($n) { return $n >= 1000 ? round($n/1000,1).'k' : $n; }
}

$catBadge = [
    'General Health'   => 'text-blue-700',
    'Mental Wellness'  => 'text-violet-700',
    'Diet & Nutrition' => 'text-emerald-700',
    'Active Living'    => 'text-orange-700',
    'Research'         => 'text-tertiary',
    'Journals'         => 'text-rose-700',
];
$badge = $catBadge[$post['categorie']] ?? 'text-tertiary';
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

  <!-- Article Body (8 cols) -->
  <article class="lg:col-span-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
      <a href="frontOffice.php" class="hover:text-primary transition-colors">Home</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <a href="frontOffice.php?action=category&cat=<?= urlencode($post['categorie']) ?>"
         class="hover:text-primary transition-colors"><?= htmlspecialchars($post['categorie']) ?></a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <span class="truncate max-w-[220px] text-on-surface"><?= htmlspecialchars($post['titre']) ?></span>
    </nav>

    <!-- Category badge -->
    <span class="inline-block px-4 py-1.5 rounded-full bg-primary-container text-on-primary-container font-headline text-xs font-bold tracking-wider uppercase mb-6">
      <?= htmlspecialchars($post['categorie']) ?>
    </span>

    <!-- Title -->
    <h1 class="font-headline text-4xl md:text-5xl font-extrabold text-blue-900 tracking-tight leading-tight mb-8">
      <?= htmlspecialchars($post['titre']) ?>
    </h1>

    <!-- Author row -->
    <div class="flex items-center gap-4 mb-8 pb-8 border-b border-surface-container flex-wrap">
      <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-base flex-shrink-0 border-2 border-primary/10">
        <?= strtoupper(substr($post['prenom'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="flex-1">
        <p class="font-bold text-on-surface">
          <?= htmlspecialchars(($post['prenom'] ?? '') . ' ' . ($post['nom'] ?? '')) ?>
          <span class="text-slate-400 font-normal text-sm ml-2"><?= htmlspecialchars($post['role_name'] ?? '') ?></span>
        </p>
        <p class="text-sm text-slate-400">
          <?= date('F d, Y', strtotime($post['date_publication'] ?? $post['date_creation'])) ?>
          &bull; <?= estimateReadTime($post['contenu']) ?>
          &bull; <span class="inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">visibility</span><?= number_format($post['views_count']) ?> views
          </span>
        </p>
      </div>
      <!-- Share -->
      <button onclick="navigator.clipboard.writeText(window.location.href); showToast('Link copied!')"
              class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-surface-container transition-all" title="Copy link">
        <span class="material-symbols-outlined">share</span>
      </button>
    </div>

    <!-- Cover Image -->
    <?php if (!empty($post['image_url'])): ?>
    <div class="relative mb-10 rounded-xl overflow-hidden shadow-2xl shadow-blue-900/10 h-80 md:h-[420px]">
      <img src="<?= htmlspecialchars($post['image_url']) ?>"
           alt="<?= htmlspecialchars($post['titre']) ?>"
           class="w-full h-full object-cover"/>
    </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="prose-article text-on-surface text-[17px] leading-[1.9] space-y-5 mb-12">
      <?php
      $paragraphs = explode("\n\n", $post['contenu']);
      foreach ($paragraphs as $i => $para):
          $para = trim($para);
          if (empty($para)) continue;
      ?>
      <p class="<?= $i === 0 ? 'text-lg text-on-surface-variant font-medium' : 'text-on-surface' ?>">
        <?= nl2br(htmlspecialchars($para)) ?>
      </p>
      <?php endforeach; ?>
    </div>

    <!-- Engagement Bar -->
    <div class="flex items-center justify-between py-6 border-y border-surface-container mb-12">
      <div class="flex items-center gap-6 text-slate-400">
        <button class="like-btn group flex items-center gap-2 hover:text-red-500 transition-colors"
              data-post-id="<?= $post['id'] ?>"
              data-already-liked="<?= $alreadyLiked ? 'true' : 'false' ?>">
                <span class="material-symbols-outlined text-gray-400 group-hover:text-rose-500 transition-colors like-icon">favorite</span>
                <span class="text-sm font-bold text-gray-500 like-count"><?= fmt((int)$post['likes_count']) ?></span>
            </button>
        <a href="#comments" class="flex items-center gap-2 hover:text-blue-500 transition-colors">
          <span class="material-symbols-outlined text-[24px]">chat_bubble</span>
          <span class="font-bold text-sm"><?= $commentCount ?></span>
        </a>
      </div>
      <span class="text-xs text-slate-400"><?= estimateReadTime($post['contenu']) ?></span>
    </div>

    <!-- Comments Section -->
    <section id="comments" class="space-y-8">
      <h2 class="font-headline text-2xl font-bold text-blue-900 flex items-center gap-3">
        Community Discussion
        <span id="commentCount" class="text-sm font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full"><?= $commentCount ?></span>
      </h2>

      <!-- Add Comment -->
      <div class="bg-surface-container-low rounded-xl p-6">
        <form id="commentForm" method="POST" action="frontOffice.php?action=add_comment" class="space-y-4">
          <input type="hidden" name="id_post" value="<?= $post['id'] ?>"/>
          <input type="hidden" name="id_utilisateur" value="4"/>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm flex-shrink-0">U</div>
            <div class="flex-1">
              <textarea name="contenu" rows="3" required maxlength="1000"
                        placeholder="Share your thoughts on this article..."
                        class="w-full px-4 py-3 bg-white border border-surface-container-high rounded-lg text-on-surface focus:ring-2 focus:ring-tertiary/30 focus:border-tertiary transition-all resize-none text-sm"></textarea>
              <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-slate-400">Your comment will appear instantly.</p>
                <button type="submit"
                        class="px-5 py-2 bg-primary text-on-primary rounded-lg font-headline font-semibold text-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">send</span> Submit
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Comments list -->
      <div id="commentsList" class="space-y-4">
      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.03)] border-l-4 border-tertiary-fixed">
          <div class="flex justify-between items-start mb-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">
                <?= strtoupper(substr($comment['prenom'] ?? 'U', 0, 1) . substr($comment['nom'] ?? 'U', 0, 1)) ?>
              </div>
              <div>
                <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? '')) ?></p>
                <p class="text-[10px] text-slate-400"><?= timeAgo($comment['date_creation']) ?></p>
              </div>
            </div>
          </div>
          <p class="text-sm text-on-surface-variant leading-relaxed pl-12"><?= htmlspecialchars($comment['contenu']) ?></p>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-comments bg-surface-container-lowest rounded-xl py-16 text-center">
          <span class="material-symbols-outlined text-5xl text-outline mb-3">chat_bubble_outline</span>
          <p class="text-lg font-headline font-bold text-on-surface mb-1">No comments yet</p>
          <p class="text-sm text-on-surface-variant">Be the first to share your thoughts!</p>
        </div>
      <?php endif; ?>
      </div>
    </section>
  </article>

  <!-- Sidebar (4 cols) -->
  <aside class="lg:col-span-4 space-y-10">

    <!-- Related Articles -->
    <?php if (!empty($relatedPosts)): ?>
    <div class="bg-surface-container-low rounded-xl p-8">
      <h4 class="font-headline text-lg font-bold text-blue-900 mb-6">Related Articles</h4>
      <div class="space-y-6">
        <?php foreach ($relatedPosts as $rPost): ?>
        <div class="flex gap-4 group cursor-pointer">
          <?php if (!empty($rPost['image_url'])): ?>
          <img src="<?= htmlspecialchars($rPost['image_url']) ?>" alt=""
               class="w-16 h-16 rounded-lg object-cover flex-shrink-0 group-hover:opacity-80 transition-opacity"/>
          <?php else: ?>
          <div class="w-16 h-16 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-outline">article</span>
          </div>
          <?php endif; ?>
          <div>
            <a href="frontOffice.php?action=view&id=<?= $rPost['id'] ?>">
              <h5 class="text-sm font-bold text-blue-900 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                <?= htmlspecialchars($rPost['titre']) ?>
              </h5>
            </a>
            <p class="text-[11px] text-slate-400 mt-1"><?= estimateReadTime($rPost['contenu']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Newsletter -->
    <div class="bg-primary rounded-xl p-8 text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
      <h4 class="font-headline text-xl font-bold mb-3 relative z-10">Medical Excellence in your inbox.</h4>
      <p class="text-on-primary-container text-sm mb-6 relative z-10 opacity-90">Our weekly selection of the most impactful health research.</p>
      <div class="space-y-3 relative z-10">
        <input class="w-full bg-white/10 border-none rounded-lg py-3 px-4 text-sm placeholder:text-white/60 focus:ring-2 focus:ring-tertiary-fixed/30"
               placeholder="Your email address" type="email"/>
        <button class="w-full bg-tertiary-fixed text-on-tertiary-fixed font-headline font-bold py-3 rounded-lg hover:bg-tertiary-fixed-dim transition-all active:scale-[0.98]">
          Subscribe
        </button>
      </div>
    </div>

    <!-- Tags -->
    <div class="p-2">
      <h4 class="font-headline text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Key Topics</h4>
      <div class="flex flex-wrap gap-2">
        <?php
        $tags = ['General Health','Research','Mental Wellness','Diet & Nutrition','Active Living','Journals','Telemedicine','Neuroscience','Oncology','Pediatrics'];
        foreach ($tags as $tag):
          $href = in_array($tag, ['General Health','Research','Mental Wellness','Diet & Nutrition','Active Living','Journals'])
                  ? "frontOffice.php?action=category&cat=" . urlencode($tag) : "#";
        ?>
        <a href="<?= $href ?>"
           class="px-4 py-2 bg-surface-container-high rounded-lg text-xs font-bold text-slate-600 hover:bg-primary hover:text-white transition-all">
          <?= htmlspecialchars($tag) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>

</div>
