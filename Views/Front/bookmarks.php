<?php
/**
 * Front Office — My Bookmarks page
 */
$currentUserId = $_SESSION['user']['id'] ?? null;
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
        if ($diff->i > 5) return $diff->i . 'm ago';
        return 'Just now';
    }
}
$count = count($bookmarkedPosts);
?>
<style>
@keyframes bmFadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.bm-card { animation: bmFadeUp .35s ease-out both; }
</style>

<div class="max-w-5xl mx-auto">

  <!-- Page header -->
  <div class="flex items-center gap-5 mb-10">
    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-lg shadow-blue-200 flex-shrink-0">
      <span class="material-symbols-outlined text-white text-2xl">bookmarks</span>
    </div>
    <div>
      <h1 class="font-headline text-3xl font-extrabold text-blue-900 leading-none">My Bookmarks</h1>
      <p class="text-sm text-slate-400 mt-1">Articles you saved for later reading</p>
    </div>
    <div class="ml-auto flex items-center gap-3">
      <span class="px-4 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 text-sm font-bold rounded-full" id="bmCounter">
        <?= $count ?> saved
      </span>
      <?php if ($count > 0): ?>
      <a href="/integration/magazine"
         class="hidden sm:flex items-center gap-1.5 px-4 py-1.5 bg-primary text-white text-sm font-bold rounded-full hover:opacity-90 transition-opacity">
        <span class="material-symbols-outlined text-sm">explore</span> Browse more
      </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($bookmarkedPosts)): ?>
  <!-- Empty state -->
  <div class="flex flex-col items-center justify-center py-28 rounded-2xl bg-surface-container-low border border-surface-container">
    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-sky-100 flex items-center justify-center mb-6 shadow-inner">
      <span class="material-symbols-outlined text-5xl text-blue-300">bookmark_border</span>
    </div>
    <h3 class="font-headline text-xl font-bold text-blue-900 mb-2">Nothing saved yet</h3>
    <p class="text-sm text-slate-400 mb-8 text-center max-w-xs leading-relaxed">
      When you save articles to read later, they'll all appear right here.
    </p>
    <a href="/integration/magazine"
       class="flex items-center gap-2 px-7 py-3 bg-primary text-white font-bold rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-blue-200">
      <span class="material-symbols-outlined text-sm">explore</span> Browse Articles
    </a>
  </div>

  <?php else: ?>
  <!-- Bookmarks grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($bookmarkedPosts as $i => $p): ?>
    <article class="bm-card group bg-white rounded-2xl border border-surface-container overflow-hidden
                    hover:shadow-xl hover:shadow-blue-900/8 hover:-translate-y-1 transition-all duration-300"
             style="animation-delay: <?= $i * 60 ?>ms">

      <!-- Image / placeholder -->
      <?php if (!empty($p['image_url'])): ?>
      <div class="h-40 overflow-hidden relative">
        <img src="<?= htmlspecialchars($p['image_url']) ?>"
             alt="<?= htmlspecialchars($p['titre']) ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
        <!-- Category pill over image -->
        <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur-sm text-blue-700 text-[10px] font-bold rounded-full shadow-sm">
          <?= htmlspecialchars($p['categorie']) ?>
        </span>
      </div>
      <?php else: ?>
      <div class="h-40 bg-gradient-to-br from-blue-50 to-sky-100 flex items-center justify-center relative">
        <span class="material-symbols-outlined text-5xl text-blue-200">article</span>
        <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 text-blue-700 text-[10px] font-bold rounded-full shadow-sm">
          <?= htmlspecialchars($p['categorie']) ?>
        </span>
      </div>
      <?php endif; ?>

      <!-- Card body -->
      <div class="p-5">
        <!-- Title -->
        <h3 class="font-headline text-sm font-bold text-blue-900 leading-snug line-clamp-2 mb-4 group-hover:text-primary transition-colors">
          <a href="/integration/magazine/article?id=<?= $p['id'] ?>">
            <?= htmlspecialchars($p['titre']) ?>
          </a>
        </h3>

        <!-- Meta row -->
        <div class="flex items-center justify-between pt-3 border-t border-surface-container">
          <div class="flex items-center gap-3 text-[11px] text-slate-400">
            <span class="flex items-center gap-1">
              <span class="material-symbols-outlined text-[13px]">schedule</span>
              <?= estimateReadTime($p['contenu']) ?>
            </span>
            <span class="flex items-center gap-1">
              <span class="material-symbols-outlined text-[13px] text-blue-400">bookmark</span>
              <?= timeAgo($p['bookmarked_at']) ?>
            </span>
          </div>
          <button class="remove-bookmark-btn flex items-center gap-1 px-2 py-1 text-slate-300 hover:text-red-400 hover:bg-red-50 rounded-lg transition-all text-[11px] font-medium"
                  data-post-id="<?= $p['id'] ?>" title="Remove bookmark">
            <span class="material-symbols-outlined text-[14px]">bookmark_remove</span>
            <span class="hidden sm:inline">Remove</span>
          </button>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Back link -->
  <div class="mt-10 flex items-center justify-between">
    <a href="/integration/magazine" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-primary transition-colors">
      <span class="material-symbols-outlined text-sm">arrow_back</span> Back to Magazine
    </a>
  </div>
</div>

<script>
document.querySelectorAll('.remove-bookmark-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const postId = this.dataset.postId;
        const card   = this.closest('article');

        card.style.transition = 'opacity .25s, transform .25s';
        card.style.opacity    = '0.4';
        card.style.pointerEvents = 'none';

        const fd = new FormData();
        fd.append('post_id', postId);

        try {
            const res  = await fetch('/integration/magazine/bookmark', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success && !data.bookmarked) {
                card.style.opacity   = '0';
                card.style.transform = 'scale(.95) translateY(-4px)';
                setTimeout(() => {
                    card.remove();
                    const remaining = document.querySelectorAll('article').length;
                    const counter   = document.getElementById('bmCounter');
                    if (counter) counter.textContent = remaining + ' saved';
                    if (remaining === 0) location.reload();
                }, 280);
            } else {
                card.style.opacity      = '1';
                card.style.pointerEvents = '';
            }
        } catch(e) {
            card.style.opacity       = '1';
            card.style.pointerEvents = '';
        }
    });
});
</script>