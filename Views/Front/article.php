<?php
/**
 * Front Office — Single Article View
 */
$alreadyLiked = $alreadyLiked ?? false;
$currentUserId = $_SESSION['user']['id'] ?? null;
$currentUserName = trim(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? ''));
$currentUserInitials = strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1) . substr($_SESSION['user']['nom'] ?? 'U', 0, 1));
$isLoggedIn = !empty($currentUserId);
$userRole = $_SESSION['user']['role'] ?? '';
$isAdmin = strtolower($userRole) === 'admin';

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
      <!-- Actions -->
      <div class="flex items-center gap-2">
        <button id="saveForLaterBtn" data-post-id="<?= $post['id'] ?>" data-post-title="<?= htmlspecialchars($post['titre']) ?>" data-post-image="<?= htmlspecialchars($post['image_url'] ?? '') ?>"
                class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-surface-container transition-all" title="Save for later">
          <span class="material-symbols-outlined bookmark-icon">bookmark_border</span>
        </button>
        
        <!-- Share Menu -->
        <div class="relative group">
          <button class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-surface-container transition-all" title="Share article">
            <span class="material-symbols-outlined">share</span>
          </button>
          <div class="absolute right-0 mt-2 w-56 bg-white border border-surface-container rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
            <div class="p-4 space-y-2">
              <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Share on</p>
              
              <!-- Copy Link -->
              <button onclick="navigator.clipboard.writeText(window.location.href); showNotification('success', 'Link copied to clipboard!')"
                      class="w-full flex items-center gap-3 px-3 py-2 text-sm text-on-surface hover:bg-surface-container rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">content_copy</span>
                <span>Copy Link</span>
              </button>
              
              <!-- Facebook -->
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI']) ?>" target="_blank" rel="noopener"
                 class="w-full flex items-center gap-3 px-3 py-2 text-sm text-[#1877f2] hover:bg-surface-container rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Facebook</span>
              </a>
              
              <!-- Twitter -->
              <a href="https://twitter.com/intent/tweet?url=<?= urlencode(($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($post['titre']) ?>" target="_blank" rel="noopener"
                 class="w-full flex items-center gap-3 px-3 py-2 text-sm text-[#000000] hover:bg-surface-container rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 002.856-3.51 10 10 0 01-2.806.856 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                <span>Twitter/X</span>
              </a>
              
              <!-- LinkedIn -->
              <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI']) ?>" target="_blank" rel="noopener"
                 class="w-full flex items-center gap-3 px-3 py-2 text-sm text-[#0a66c2] hover:bg-surface-container rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.475-2.236-1.986-2.236-1.081 0-1.722.722-2.004 1.418-.103.249-.129.597-.129.946v5.441h-3.554s.05-8.814 0-9.752h3.554v1.375c.43-.664 1.199-1.61 2.922-1.61 2.134 0 3.732 1.39 3.732 4.377v5.61zM5.337 8.855c-1.144 0-1.915-.762-1.915-1.715 0-.953.77-1.715 1.926-1.715 1.155 0 1.916.762 1.916 1.715 0 .953-.771 1.715-1.927 1.715zm1.582 11.597H3.714V9.505h3.205v10.947zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"/></svg>
                <span>LinkedIn</span>
              </a>
              
              <!-- WhatsApp -->
              <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['titre'] . ' - ' . (($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI'])) ?>" target="_blank" rel="noopener"
                 class="w-full flex items-center gap-3 px-3 py-2 text-sm text-[#25d366] hover:bg-surface-container rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.6915026,2.4744748 C15.6684016,0.4953846 12.7909947,-0.1139597 9.97368649,0.0152448903 C4.74438975,0.245686705 0.436075697,4.52428005 0.206284687,9.75357992 C0.0861826966,13.0151496 1.15792661,16.1685148 3.23073512,18.1899299 L2.45651486,22.0348371 C2.25490699,23.0762508 3.13008928,23.9720311 4.17160257,23.7704232 L8.01671231,22.9962029 C9.73358339,23.8231998 11.6743595,24.2831279 13.6399903,24.2831279 C18.8692904,24.2831279 23.1775285,19.9749899 23.4073194,14.7456898 C23.5274215,11.9283815 22.714604,9.05097138 20.691503,7.03187234 L17.6915026,2.4744748 Z M13.6399903,21.7019025 C12.1339945,21.7019025 10.6725456,21.3076949 9.41266819,20.5892521 L8.81150773,20.2380505 L5.92360586,20.9155239 L6.60107925,18.0276421 L6.24947761,17.4264816 C5.44066916,16.1256014 5.04039603,14.5890051 5.17076933,13.0151496 C5.35728356,9.80160857 7.96126944,7.19762268 11.1748381,7.07752057 C14.3884068,6.9574185 17.0924033,9.56140442 17.2089175,12.7752731 C17.3290196,16.1256014 14.7250337,21.7019025 13.6399903,21.7019025 Z M18.5825355,14.0273449 C18.3809276,13.8257371 17.9865199,13.6241292 17.3744079,13.4225214 C16.7622960,13.220913 15.0454252,12.5034402 14.6510175,12.3018323 C14.2566099,12.1002245 13.9954704,12.1002245 13.7938625,12.5034402 C13.5922546,12.9066558 12.9801426,13.8257371 12.8100325,14.0273449 C12.6399225,14.2289527 12.4698124,14.2289527 12.2682045,14.0273449 C11.0283272,13.6241292 9.54564852,12.6112849 8.54853118,11.3704098 C8.14512346,10.8146442 8.54564852,10.3626048 8.94905624,9.95918702 C9.15066411,9.75757915 9.15066411,9.55597128 8.94905624,9.35436341 C8.74744837,9.15275554 7.83635996,7.43589051 7.63475209,7.03187234 C7.4331442,6.62785416 7.23153632,6.68882267 7.02992845,6.68882267 C6.86981839,6.68882267 6.66821052,6.68882267 6.46660265,6.68882267 C6.2650948,6.68882267 5.96333673,6.88042954 5.5689292,7.29444772 C4.97710863,7.89629631 3.49443486,9.40896008 3.49443486,11.1258309 C3.49443486,12.8427018 5.7710098,14.4866152 6.17442752,15.0424808 C6.57784524,15.5983463 9.92296869,20.9155239 15.0454252,23.3427597 C15.6575372,23.6631039 16.1436995,23.8647117 16.5381071,24.0047147 C17.1504222,24.1946211 17.7627373,24.1560308 18.2488997,23.960124 C18.8608116,23.7585162 20.5778802,22.5177411 20.9723879,21.163882 C21.1739957,20.5518702 21.1739957,20.0657078 20.9723879,19.8641 C20.7707801,19.6624921 20.0532872,19.3421459 18.5825355,14.0273449 Z"/></svg>
                <span>WhatsApp</span>
              </a>
              
              <!-- Email -->
              <a href="mailto:?subject=<?= urlencode($post['titre']) ?>&body=<?= urlencode('Check this article: ' . (($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI'])) ?>"
                 class="w-full flex items-center gap-3 px-3 py-2 text-sm text-orange-600 hover:bg-surface-container rounded-lg transition-colors">
                <span class="material-symbols-outlined">mail</span>
                <span>Email</span>
              </a>
            </div>
          </div>
        </div>
        
        <?php if ($isAdmin): ?>
        <!-- Admin Actions -->
        <div class="ml-auto flex items-center gap-2 border-l border-surface-container pl-2">
          <a href="/integration/magazine/admin/article-form?id=<?= $post['id'] ?>"
             class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-surface-container transition-all" title="Edit article">
            <span class="material-symbols-outlined">edit</span>
          </a>
          <button onclick="showDeleteModal(<?= $post['id'] ?>)"
                  class="text-slate-400 hover:text-error p-2 rounded-lg hover:bg-red-50 transition-all" title="Delete article">
            <span class="material-symbols-outlined">delete</span>
          </button>
        </div>
        <?php endif; ?>
      </div>
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
        <?php else: ?>
        <span class="flex items-center gap-2 text-slate-400 cursor-default" title="Log in to like this article">
            <span class="material-symbols-outlined text-gray-300">favorite</span>
            <span class="text-sm font-bold"><?= fmt((int)$post['likes_count']) ?></span>
        </span>
        <?php endif; ?>
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
      <div id="commentsList" class="space-y-4">
      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment):
          $isOwnComment = $currentUserId && ((int)$comment['id_utilisateur'] === (int)$currentUserId);
        ?>
        <div id="comment-<?= $comment['id'] ?>" class="bg-surface-container-lowest rounded-xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.03)] border-l-4 border-tertiary-fixed">
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
            <?php if ($isOwnComment): ?>
            <div class="flex items-center gap-1">
              <button class="comment-edit-btn p-1.5 text-slate-400 hover:text-primary hover:bg-surface-container rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" title="Edit comment">
                <span class="material-symbols-outlined text-sm">edit</span>
              </button>
              <button class="comment-delete-btn p-1.5 text-slate-400 hover:text-error hover:bg-error-container/20 rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" title="Delete comment">
                <span class="material-symbols-outlined text-sm">delete</span>
              </button>
            </div>
            <?php elseif ($isAdmin): ?>
            <!-- Admin can delete any comment -->
            <div class="flex items-center gap-1">
              <button class="comment-delete-btn p-1.5 text-slate-400 hover:text-error hover:bg-error-container/20 rounded-lg transition-all"
                      data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" title="Delete comment (Admin)">
                <span class="material-symbols-outlined text-sm">delete</span>
              </button>
            </div>
            <?php endif; ?>
          </div>

          <!-- Comment text (display mode) -->
          <p id="comment-text-<?= $comment['id'] ?>" class="text-sm text-on-surface-variant leading-relaxed pl-12">
            <?= htmlspecialchars($comment['contenu']) ?>
          </p>

          <!-- Edit form (hidden by default) -->
          <?php if ($isOwnComment): ?>
          <form id="comment-edit-form-<?= $comment['id'] ?>" class="hidden pl-12 mt-3 space-y-2"
                method="POST" action="/integration/magazine/comment/edit">
            <input type="hidden" name="id" value="<?= $comment['id'] ?>"/>
            <input type="hidden" name="id_post" value="<?= $post['id'] ?>"/>
            <textarea name="contenu" rows="2" maxlength="1000"
                      class="w-full px-3 py-2 bg-white border border-surface-container-high rounded-lg text-sm resize-none focus:ring-2 focus:ring-primary/30"><?= htmlspecialchars($comment['contenu']) ?></textarea>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:opacity-90">
                Save
              </button>
              <button type="button" class="comment-edit-cancel px-4 py-1.5 bg-surface-container text-on-surface-variant text-xs font-bold rounded-lg hover:bg-surface-container-high"
                      data-comment-id="<?= $comment['id'] ?>">
                Cancel
              </button>
            </div>
          </form>
          <?php endif; ?>
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
    <?php include __DIR__ . '/_newsletter_box.php'; ?>

    <!-- AI Summary Trigger (Sidebar) -->
    <div class="flex flex-col items-center py-8 px-6 bg-gradient-to-b from-blue-50/50 to-indigo-50/50 rounded-xl border border-blue-100/50 text-center relative overflow-hidden group">
        <!-- Decorative bg -->
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
        
        <!-- AI Summary Result Container (Hidden by default) -->
        <div id="summaryResultContainer" class="hidden w-full mb-6 relative z-10">
            <div class="bg-gradient-to-br from-blue-50/80 via-indigo-50/60 to-blue-50/40 backdrop-blur-xl border border-blue-200/40 shadow-xl shadow-blue-900/5 rounded-2xl p-6 space-y-4 transform transition-all duration-500">
                <!-- Header -->
                <div class="flex items-center gap-3 pb-3 border-b border-blue-200/30">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/20">
                        <span class="material-symbols-outlined text-white text-lg">auto_awesome</span>
                    </div>
                    <h5 class="font-headline font-bold text-blue-900 text-sm">AI Summary</h5>
                </div>
                
                <!-- Content -->
                <div id="summaryContent" class="space-y-3">
                    <!-- Thinking message or summary will appear here -->
                </div>
            </div>
        </div>

        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-blue-600 mb-4 shadow-sm relative z-10 border border-blue-50">
            <span class="material-symbols-outlined">auto_awesome</span>
        </div>
        <h4 class="font-headline text-lg font-bold text-blue-900 mb-2 relative z-10">Too long to read?</h4>
        <p class="text-slate-500 text-xs mb-6 relative z-10">Get a concise AI-generated summary of this article in seconds.</p>
        <button id="summarizeBtn" onclick="handleAISummarize(this)" data-post-id="<?= $post['id'] ?>"
                class="w-full flex justify-center items-center gap-2 py-3 bg-blue-900 text-white rounded-xl font-headline font-bold text-sm shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all group relative z-10">
            <span class="material-symbols-outlined text-[18px] group-hover:rotate-12 transition-transform">bolt</span>
            Generate Summary
        </button>
    </div>

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

<!-- AI Summary Inline Logic -->
<script>
const thinkingMessages = [
    'Analyzing article content...',
    'Processing medical insights...',
    'Synthesizing key findings...',
    'Generating summary...',
    'Polishing insights...'
];

window.handleAISummarize = async function(btn) {
    if (btn.dataset.loading) return;

    const summaryContainer = document.getElementById('summaryResultContainer');
    const summaryContent = document.getElementById('summaryContent');
    const postId = btn.getAttribute('data-post-id');
    const originalHTML = btn.innerHTML;
    
    btn.dataset.loading = 'true';
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Generating...';
    btn.classList.add('opacity-80');
    btn.disabled = true;

    // Show container with thinking message
    summaryContainer.classList.remove('hidden');
    
    let messageIndex = 0;
    const messageInterval = setInterval(() => {
        summaryContent.innerHTML = `
            <div class="flex items-center gap-3 py-3">
                <div class="flex gap-1">
                    <div class="w-2 h-2 rounded-full bg-blue-600 animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-2 h-2 rounded-full bg-indigo-600 animate-bounce" style="animation-delay: 0.1s;"></div>
                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0.2s;"></div>
                </div>
                <p class="text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">${thinkingMessages[messageIndex]}</p>
            </div>
        `;
        messageIndex = (messageIndex + 1) % thinkingMessages.length;
    }, 1000);

    try {
        const res = await fetch(`/integration/magazine/summarize?id=${postId}`);
        const text = await res.text();
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseErr) {
            clearInterval(messageInterval);
            summaryContent.innerHTML = `
                <div class="flex items-start gap-3 py-2">
                    <span class="material-symbols-outlined text-red-500 text-lg flex-shrink-0">error_outline</span>
                    <div>
                        <p class="text-sm font-bold text-red-700">Invalid Response from Server</p>
                        <p class="text-xs text-red-600 mt-1.5 opacity-80">Response: ${text.substring(0, 100)}</p>
                    </div>
                </div>
            `;
            return;
        }

        clearInterval(messageInterval);

        if (data.success) {
            summaryContent.innerHTML = '';
            
            const lines = data.summary.split('\n').filter(l => l.trim() !== '');
            let lineIdx = 0;

            function typeLine() {
                if (lineIdx < lines.length) {
                    const line = lines[lineIdx].trim();
                    if (line === '') {
                        lineIdx++;
                        setTimeout(typeLine, 50);
                        return;
                    }

                    let wrapper;

                    if (line.startsWith('-') || line.startsWith('*')) {
                        wrapper = document.createElement('div');
                        wrapper.className = 'flex gap-3 items-start group/item';
                        wrapper.innerHTML = `
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center mt-0.5 group-hover/item:scale-110 transition-transform">
                                <span class="text-blue-600 font-bold text-sm">✓</span>
                            </div>
                            <p class="text-sm font-medium text-blue-900 leading-relaxed flex-1">${line.replace(/^[-*]\s*/, '')}</p>
                        `;
                        summaryContent.appendChild(wrapper);
                    } else {
                        wrapper = document.createElement('p');
                        wrapper.className = 'text-sm text-blue-800 leading-relaxed font-medium';
                        wrapper.textContent = line;
                        summaryContent.appendChild(wrapper);
                    }

                    wrapper.style.opacity = '0';
                    wrapper.style.transform = 'translateY(12px)';
                    wrapper.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                    
                    setTimeout(() => {
                        wrapper.style.opacity = '1';
                        wrapper.style.transform = 'translateY(0)';
                        lineIdx++;
                        setTimeout(typeLine, 250);
                    }, 50);
                } else {
                    // Add footer with dismiss button
                    const footer = document.createElement('div');
                    footer.className = 'flex justify-between items-center mt-5 pt-4 border-t border-blue-200/30';
                    footer.innerHTML = `
                        <p class="text-xs text-blue-600/70 italic">Generated by AI for educational purposes</p>
                        <button class="px-4 py-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-100/50 rounded-lg transition-all">✕ Dismiss</button>
                    `;
                    footer.querySelector('button').onclick = () => {
                        summaryContainer.classList.add('hidden');
                        summaryContent.innerHTML = '';
                    };
                    summaryContent.appendChild(footer);
                }
            }
            typeLine();
        } else {
            summaryContent.innerHTML = `
                <div class="flex items-start gap-3 py-2">
                    <span class="material-symbols-outlined text-red-500 text-lg flex-shrink-0">error_outline</span>
                    <div>
                        <p class="text-sm font-bold text-red-700">${data.error}</p>
                        ${data.details ? `<p class="text-xs text-red-600 mt-1.5 opacity-80">${data.details}</p>` : ''}
                    </div>
                </div>
            `;
            console.error('AI error response:', data);
        }
    } catch (err) {
        clearInterval(messageInterval);
        summaryContent.innerHTML = `
            <div class="flex items-start gap-3 py-2">
                <span class="material-symbols-outlined text-red-500 text-lg flex-shrink-0">cloud_off</span>
                <div>
                    <p class="text-sm font-bold text-red-700">Network Error</p>
                    <p class="text-xs text-red-600 mt-1.5 opacity-80">${err.message}</p>
                </div>
            </div>
        `;
        console.error('AI fetch error:', err);
    } finally {
        delete btn.dataset.loading;
        btn.innerHTML = originalHTML;
        btn.classList.remove('opacity-80');
        btn.disabled = false;
    }
};

// ============================================================
// NOTIFICATION & MODAL SYSTEM
// ============================================================

function showNotification(type, message) {
    // Remove existing notification if any
    const existing = document.getElementById('notificationCenter');
    if (existing) existing.remove();
    
    const bgColor = type === 'success' ? 'bg-gradient-to-r from-emerald-500 to-teal-500' :
                    type === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-500' :
                    type === 'warning' ? 'bg-gradient-to-r from-amber-500 to-orange-500' :
                    'bg-gradient-to-r from-blue-500 to-indigo-500';
    
    const icon = type === 'success' ? 'check_circle' :
                 type === 'error' ? 'error' :
                 type === 'warning' ? 'warning' :
                 'info';
    
    const notification = document.createElement('div');
    notification.id = 'notificationCenter';
    notification.className = `fixed top-6 right-6 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 z-50 animate-in fade-in slide-in-from-right-4 duration-300`;
    notification.innerHTML = `
        <span class="material-symbols-outlined">${icon}</span>
        <span class="font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.remove('animate-in', 'fade-in', 'slide-in-from-right-4');
            notification.classList.add('animate-out', 'fade-out', 'slide-out-to-right-4');
            setTimeout(() => notification.remove(), 300);
        }
    }, 4000);
}

function showDeleteModal(postId) {
    const modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.className = 'fixed inset-0 bg-black/40 flex items-center justify-center z-50 animate-in fade-in duration-200';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-in scale-in-95 duration-200">
            <!-- Header with icon -->
            <div class="bg-gradient-to-r from-red-500 to-rose-500 text-white p-6 rounded-t-2xl flex items-center gap-4">
                <span class="material-symbols-outlined text-4xl">warning</span>
                <div>
                    <h3 class="font-headline font-bold text-lg">Delete Article?</h3>
                    <p class="text-sm text-white/90">This action cannot be undone</p>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                    Are you sure you want to delete this article? It will be permanently removed from the magazine and cannot be recovered.
                </p>
                
                <!-- Actions -->
                <div class="flex gap-3">
                    <button onclick="document.getElementById('deleteModal').remove()" 
                            class="flex-1 px-4 py-2.5 bg-surface-container text-on-surface-variant font-semibold rounded-xl hover:bg-surface-container-high transition-colors">
                        Cancel
                    </button>
                    <button onclick="window.location.href='/integration/magazine/admin/delete?id=${postId}'" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-rose-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-red-500/30 transition-all">
                        <span class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">delete</span>
                            Delete
                        </span>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };
}
</script>
