<?php
/**
 * Front Office — Single Article View
 */
?>
<style>
@keyframes twBlink { 0%,100%{opacity:1} 50%{opacity:0} }
.tw-cursor { display:inline-block;width:2px;height:.9em;background:#004d99;margin-left:2px;vertical-align:text-bottom;animation:twBlink .75s step-end infinite; }
@keyframes aiDot { 0%,80%,100%{transform:translateY(0);opacity:.4} 40%{transform:translateY(-6px);opacity:1} }
.ai-dot { width:8px;height:8px;border-radius:50%;animation:aiDot 1.2s ease-in-out infinite; }
@keyframes aiFadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.ai-fadeup { animation:aiFadeUp .35s ease-out both; }
</style>
<?php
$alreadyLiked      = $alreadyLiked      ?? false;
$alreadyBookmarked = $alreadyBookmarked ?? false;
$likedCommentIds   = $likedCommentIds   ?? [];
$currentUserId = $_SESSION['user']['id'] ?? null;
$currentUserName = trim(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? ''));
$currentUserInitials = strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1) . substr($_SESSION['user']['nom'] ?? 'U', 0, 1));
$isLoggedIn = !empty($currentUserId);

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

<!-- Hidden spans for JS to read current user info -->
<span id="currentUserName" class="hidden"><?= htmlspecialchars($currentUserName) ?></span>
<span id="currentUserInitials" class="hidden"><?= htmlspecialchars($currentUserInitials) ?></span>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

  <!-- Article Body (8 cols) -->
  <article class="lg:col-span-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
      <a href="/integration/magazine" class="hover:text-primary transition-colors">Home</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <a href="/integration/magazine/category?cat=<?= urlencode($post['categorie']) ?>"
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
        <?php if ($isLoggedIn): ?>
        <button class="like-btn group flex items-center gap-2 hover:text-red-500 transition-colors"
                data-post-id="<?= $post['id'] ?>"
                data-already-liked="<?= $alreadyLiked ? 'true' : 'false' ?>">
            <span class="material-symbols-outlined text-gray-400 group-hover:text-rose-500 transition-colors like-icon">favorite</span>
            <span class="text-sm font-bold text-gray-500 like-count"><?= fmt((int)$post['likes_count']) ?></span>
        </button>
        <button id="bookmarkBtn"
                class="bookmark-btn group flex items-center gap-1.5 transition-colors"
                data-post-id="<?= $post['id'] ?>"
                data-bookmarked="<?= $alreadyBookmarked ? 'true' : 'false' ?>"
                title="<?= $alreadyBookmarked ? 'Remove bookmark' : 'Save for later' ?>">
          <span class="material-symbols-outlined text-[22px] transition-all bookmark-icon
                       <?= $alreadyBookmarked ? 'text-amber-500' : 'text-gray-400 group-hover:text-amber-400' ?>">
            <?= $alreadyBookmarked ? 'bookmark' : 'bookmark_border' ?>
          </span>
        </button>
        <?php else: ?>
        <span class="flex items-center gap-2 text-slate-400 cursor-default" title="Log in to like this article">
            <span class="material-symbols-outlined text-gray-300">favorite</span>
            <span class="text-sm font-bold"><?= fmt((int)$post['likes_count']) ?></span>
        </span>
        <span class="text-slate-300" title="Log in to bookmark">
          <span class="material-symbols-outlined text-[22px]">bookmark_border</span>
        </span>
        <?php endif; ?>
        <a href="#comments" class="flex items-center gap-2 hover:text-blue-500 transition-colors">
          <span class="material-symbols-outlined text-[24px]">chat_bubble</span>
          <span class="font-bold text-sm"><?= $commentCount ?></span>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs text-slate-400 hidden sm:inline"><?= estimateReadTime($post['contenu']) ?></span>
        <button id="aiSummaryBtn"
                data-post-id="<?= $post['id'] ?>"
                class="group flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl
                       bg-gradient-to-r from-blue-50 to-sky-50
                       border border-blue-200/70
                       hover:from-blue-100 hover:to-sky-100 hover:border-blue-300
                       text-blue-700 transition-all duration-200
                       active:scale-95 shadow-sm hover:shadow-md hover:shadow-blue-100/60">
          <span class="material-symbols-outlined text-[18px] text-blue-600 transition-transform duration-300 group-hover:rotate-12">auto_awesome</span>
          <span class="ai-btn-text text-xs font-bold tracking-wide">AI Summary</span>
        </button>
      </div>
    </div>

    <!-- AI Summary Panel -->
    <div id="aiSummaryPanel" class="hidden mb-10 rounded-2xl overflow-hidden border border-blue-100/80 shadow-xl shadow-blue-900/5 animate-slideIn">
      <!-- Gradient accent strip -->
      <div class="h-1 bg-gradient-to-r from-blue-600 via-sky-500 to-teal-400"></div>

      <!-- Panel header -->
      <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-slate-50 to-blue-50/60 border-b border-blue-100/60">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-md shadow-blue-200">
            <span class="material-symbols-outlined text-white text-[18px]">psychology</span>
          </div>
          <div>
            <p class="text-sm font-bold text-slate-800 leading-none">AI Summary</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Powered by TinyLlama · Ollama</p>
          </div>
        </div>
        <button id="aiSummaryClose"
                class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-white rounded-lg transition-all">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>

      <!-- Loading state -->
      <div id="aiSummaryLoading" class="flex items-center gap-4 px-6 py-8 bg-white">
        <div class="flex gap-1.5">
          <div class="ai-dot bg-blue-500"  style="animation-delay:0ms"></div>
          <div class="ai-dot bg-sky-400"   style="animation-delay:180ms"></div>
          <div class="ai-dot bg-teal-400"  style="animation-delay:360ms"></div>
        </div>
        <span class="text-sm text-slate-500 font-medium">Analyzing article with AI…</span>
      </div>

      <!-- Generated content (hidden until ready) -->
      <div id="aiSummaryContent" class="hidden bg-white px-6 py-6 space-y-5">

        <!-- Summary -->
        <div class="ai-fadeup">
          <div class="flex items-center gap-2 mb-2.5">
            <span class="material-symbols-outlined text-[16px] text-blue-600">summarize</span>
            <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-widest">Summary</p>
          </div>
          <p id="aiSummaryText" class="text-sm text-slate-700 leading-relaxed pl-6"></p>
        </div>

        <!-- Key Points -->
        <div id="aiKeyPointsWrap" class="hidden ai-fadeup">
          <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-[16px] text-sky-600">format_list_bulleted</span>
            <p class="text-[11px] font-extrabold text-sky-600 uppercase tracking-widest">Key Takeaways</p>
          </div>
          <ul id="aiKeyPoints" class="space-y-2.5 pl-6"></ul>
        </div>

        <!-- Disclaimer -->
        <p class="text-[11px] text-slate-400 border-t border-slate-100 pt-3 pl-6">
          AI-generated summary — always consult a qualified healthcare professional for medical advice.
        </p>
      </div>
    </div>

    <!-- Comments Section -->
    <section id="comments" class="space-y-8">
      <h2 class="font-headline text-2xl font-bold text-blue-900 flex items-center gap-3">
        Community Discussion
        <span id="commentCount" class="text-sm font-bold text-primary bg-primary-fixed px-3 py-1 rounded-full"><?= $commentCount ?></span>
      </h2>

      <!-- Add Comment (only if logged in) -->
      <?php if ($isLoggedIn): ?>
      <div class="bg-surface-container-low rounded-xl p-6">
        <form id="commentForm" method="POST" action="/integration/magazine/comment/add" class="space-y-4">
          <input type="hidden" name="id_post" value="<?= $post['id'] ?>"/>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm flex-shrink-0">
              <?= htmlspecialchars($currentUserInitials) ?>
            </div>
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
      <?php else: ?>
      <div class="bg-surface-container-low rounded-xl p-6 text-center">
        <p class="text-sm text-on-surface-variant">
          <a href="/integration/login" class="text-primary font-bold hover:underline">Log in</a> to join the discussion.
        </p>
      </div>
      <?php endif; ?>

      <!-- Flash messages -->
      <?php if (!empty($_SESSION['flash_success'])): ?>
      <div id="flash-success" class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-5 py-3 text-sm font-medium">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
      </div>
      <?php unset($_SESSION['flash_success']); endif; ?>
      <?php if (!empty($_SESSION['flash_error'])): ?>
      <div id="flash-error" class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-5 py-3 text-sm font-medium">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
      </div>
      <?php unset($_SESSION['flash_error']); endif; ?>

      <!-- Comments list -->
      <div id="commentsList" class="space-y-5">
      <?php if (!empty($comments)):
        // Recursive comment renderer (max 3 visual levels)
        function renderComment($comment, $postId, $currentUserId, $isLoggedIn, $depth = 0, $likedCommentIds = []) {
            $isOwnComment = $currentUserId && ((int)($comment['id_utilisateur'] ?? 0) === (int)$currentUserId);
            $initials = strtoupper(
                substr($comment['prenom'] ?? 'U', 0, 1) .
                substr($comment['nom']    ?? 'U', 0, 1)
            );
            $borderColors = ['border-tertiary-fixed','border-indigo-200','border-violet-200'];
            $borderClass  = $borderColors[min($depth, 2)];
            $indentClass  = $depth > 0 ? 'ml-10' : '';
        ?>
        <div id="comment-<?= $comment['id'] ?>"
             class="comment-node <?= $indentClass ?> bg-surface-container-lowest rounded-xl p-5
                    shadow-[0_2px_12px_rgba(0,77,153,0.04)] border-l-4 <?= $borderClass ?>
                    animate-slideIn">

          <!-- Comment header -->
          <div class="flex justify-between items-start mb-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container flex-shrink-0">
                <?= $initials ?>
              </div>
              <div>
                <p class="text-sm font-bold text-on-surface">
                  <?= htmlspecialchars(trim(($comment['prenom'] ?? '') . ' ' . ($comment['nom'] ?? ''))) ?>
                  <?php if ($depth > 0): ?><span class="text-[10px] text-slate-400 font-normal ml-1">· Reply</span><?php endif; ?>
                </p>
                <p class="text-[10px] text-slate-400"><?= timeAgo($comment['date_creation']) ?></p>
              </div>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-1">
              <!-- Like comment -->
              <?php if ($isLoggedIn):
                $alreadyLikedComment = in_array((int)$comment['id'], $likedCommentIds);
                $likeIcon  = $alreadyLikedComment ? 'favorite'       : 'favorite_border';
                $likeStyle = $alreadyLikedComment ? "color:#ef4444;font-variation-settings:'FILL' 1" : '';
                $countColor = $alreadyLikedComment ? 'color:#ef4444' : '';
              ?>
              <button class="comment-like-btn group flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-rose-50 transition-all"
                      data-comment-id="<?= $comment['id'] ?>">
                <span class="material-symbols-outlined text-[16px] transition-colors comment-like-icon"
                      style="<?= $likeStyle ?>"><?= $likeIcon ?></span>
                <span class="text-[11px] font-bold comment-like-count"
                      style="<?= $countColor ?>"><?= (int)$comment['likes_count'] ?></span>
              </button>
              <?php else: ?>
              <span class="flex items-center gap-1 px-2 py-1 text-slate-300">
                <span class="material-symbols-outlined text-[16px]">favorite_border</span>
                <span class="text-[11px]"><?= (int)$comment['likes_count'] ?></span>
              </span>
              <?php endif; ?>

              <!-- Reply (only on root and 1st-level for clean UI) -->
              <?php if ($isLoggedIn && $depth < 2): ?>
              <button class="comment-reply-toggle p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" title="Reply">
                <span class="material-symbols-outlined text-[16px]">reply</span>
              </button>
              <?php endif; ?>

              <?php if ($isOwnComment): ?>
              <button class="comment-edit-btn p-1.5 text-slate-400 hover:text-primary hover:bg-surface-container rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" title="Edit">
                <span class="material-symbols-outlined text-[16px]">edit</span>
              </button>
              <button class="comment-delete-btn p-1.5 text-slate-400 hover:text-error hover:bg-error-container/20 rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $postId ?>" title="Delete">
                <span class="material-symbols-outlined text-[16px]">delete</span>
              </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Comment text -->
          <p id="comment-text-<?= $comment['id'] ?>" class="text-sm text-on-surface-variant leading-relaxed pl-12">
            <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
          </p>

          <!-- Edit form -->
          <?php if ($isOwnComment): ?>
          <form id="comment-edit-form-<?= $comment['id'] ?>" class="hidden pl-12 mt-3 space-y-2"
                method="POST" action="/integration/magazine/comment/edit">
            <input type="hidden" name="id" value="<?= $comment['id'] ?>"/>
            <input type="hidden" name="id_post" value="<?= $postId ?>"/>
            <textarea name="contenu" rows="2" maxlength="1000"
                      class="w-full px-3 py-2 bg-white border border-surface-container-high rounded-lg text-sm resize-none focus:ring-2 focus:ring-primary/30"><?= htmlspecialchars($comment['contenu']) ?></textarea>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:opacity-90">Save</button>
              <button type="button" class="comment-edit-cancel px-4 py-1.5 bg-surface-container text-on-surface-variant text-xs font-bold rounded-lg hover:bg-surface-container-high"
                      data-comment-id="<?= $comment['id'] ?>">Cancel</button>
            </div>
          </form>
          <?php endif; ?>

          <!-- Inline reply form (hidden, toggled by JS) -->
          <?php if ($isLoggedIn && $depth < 2): ?>
          <div id="reply-form-<?= $comment['id'] ?>" class="hidden pl-12 mt-4">
            <form class="reply-form flex items-start gap-3"
                  data-post-id="<?= $postId ?>" data-parent-id="<?= $comment['id'] ?>">
              <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-xs flex-shrink-0 mt-0.5">
                <?= htmlspecialchars($currentUserInitials ?? 'U') ?>
              </div>
              <div class="flex-1">
                <textarea rows="2" maxlength="500" placeholder="Write a reply…"
                          class="reply-textarea w-full px-3 py-2 bg-white border border-surface-container-high rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all"></textarea>
                <div class="flex gap-2 mt-2">
                  <button type="submit" class="px-4 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">send</span> Reply
                  </button>
                  <button type="button" class="reply-cancel-btn px-3 py-1.5 bg-slate-100 text-slate-500 text-xs font-medium rounded-lg hover:bg-slate-200 transition-colors"
                          data-comment-id="<?= $comment['id'] ?>">Cancel</button>
                </div>
              </div>
            </form>
          </div>
          <?php endif; ?>

          <!-- Nested replies -->
          <?php if (!empty($comment['replies'])): ?>
          <div class="mt-4 space-y-4">
            <?php foreach ($comment['replies'] as $reply):
                renderComment($reply, $postId, $currentUserId, $isLoggedIn, $depth + 1, $likedCommentIds);
            endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php } // end renderComment ?>

        <?php foreach ($comments as $comment):
            renderComment($comment, $post['id'], $currentUserId, $isLoggedIn, 0, $likedCommentIds);
        endforeach; ?>

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
            <a href="/integration/magazine/article?id=<?= $rPost['id'] ?>">
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
    <?php include __DIR__ . '/_newsletter_widget.php'; ?>

    <!-- Tags -->
    <div class="p-2">
      <h4 class="font-headline text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Key Topics</h4>
      <div class="flex flex-wrap gap-2">
        <?php
        $tags = ['General Health','Research','Mental Wellness','Diet & Nutrition','Active Living','Journals','Telemedicine','Neuroscience','Oncology','Pediatrics'];
        foreach ($tags as $tag):
          $href = in_array($tag, ['General Health','Research','Mental Wellness','Diet & Nutrition','Active Living','Journals'])
                  ? "/integration/magazine/category?cat=" . urlencode($tag) : "#";
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
