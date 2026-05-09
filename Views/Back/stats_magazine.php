<?php
/**
 * Back Office — Magazine Statistics Dashboard
 */

// Archived count (total - published - drafts)
$archived = max(0, (int)$postStats['total_articles'] - (int)$postStats['published'] - (int)$postStats['drafts']);

// JSON blobs for Chart.js
$jsMonthLabels  = json_encode($chartMonthLabels);
$jsPostsData    = json_encode($chartPostsData);
$jsCommentsData = json_encode($chartCommentsData);
$jsViewsData    = json_encode($chartViewsData);
$jsLikesData    = json_encode($chartLikesData);

$jsCatLabels    = json_encode(array_column($categoryBreakdown, 'categorie'));
$jsCatPosts     = json_encode(array_map('intval', array_column($categoryBreakdown, 'post_count')));
$jsCatViews     = json_encode(array_map('intval', array_column($categoryBreakdown, 'total_views')));
$jsCatLikes     = json_encode(array_map('intval', array_column($categoryBreakdown, 'total_likes')));

$jsLikedLabels  = json_encode(array_map(fn($p) => mb_strimwidth($p['titre'], 0, 32, '…'), $mostLiked));
$jsLikedData    = json_encode(array_map('intval', array_column($mostLiked, 'likes_count')));
$jsViewedLabels = json_encode(array_map(fn($p) => mb_strimwidth($p['titre'], 0, 32, '…'), $mostViewed));
$jsViewedData   = json_encode(array_map('intval', array_column($mostViewed, 'views_count')));

$generatedAt    = date('d M Y, H:i');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<style>
/* KPI card counter animation */
@keyframes stCountUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.stat-card { animation: stCountUp .4s ease-out both; }
.chart-card { background:#fff; border-radius:1rem; border:1px solid #e6e8ea; padding:1.25rem; }
.chart-card h3 { font-size:.75rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#727783; margin-bottom:.75rem; display:flex; align-items:center; gap:.4rem; }
</style>

<div class="max-w-7xl mx-auto space-y-7">

  <!-- ── Page header ── -->
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-sky-500 flex items-center justify-center shadow-lg shadow-blue-200 flex-shrink-0">
        <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1">analytics</span>
      </div>
      <div>
        <h1 class="font-headline text-2xl font-extrabold text-blue-900 leading-none">Magazine Statistics</h1>
        <p class="text-xs text-slate-400 mt-1">Last refreshed: <?= $generatedAt ?></p>
      </div>
    </div>
    <a href="/integration/magazine/admin/stats"
       class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 text-sm font-semibold rounded-xl hover:bg-blue-100 transition-colors">
      <span class="material-symbols-outlined text-base">refresh</span> Refresh
    </a>
  </div>

  <!-- ── KPI Cards ── -->
  <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">

    <!-- Total Articles -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:0ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-blue-600 text-lg" style="font-variation-settings:'FILL' 1">article</span>
        </span>
        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
          +<?= $postStats['published'] ?> live
        </span>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= $postStats['total_articles'] ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Total Articles</p>
      <div class="flex gap-2 mt-2">
        <span class="text-[10px] text-slate-400"><?= $postStats['drafts'] ?> drafts</span>
        <span class="text-[10px] text-slate-300">·</span>
        <span class="text-[10px] text-slate-400"><?= $archived ?> archived</span>
      </div>
    </div>

    <!-- Total Views -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:60ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-sky-600 text-lg" style="font-variation-settings:'FILL' 1">visibility</span>
        </span>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= $postStats['total_views'] ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Total Views</p>
    </div>

    <!-- Total Likes -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:120ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-rose-500 text-lg" style="font-variation-settings:'FILL' 1">favorite</span>
        </span>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= $postStats['total_likes'] ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Total Likes</p>
    </div>

    <!-- Total Comments -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:180ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-teal-600 text-lg" style="font-variation-settings:'FILL' 1">forum</span>
        </span>
        <?php if ($commentStats['pending'] > 0): ?>
        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
          <?= $commentStats['pending'] ?> pending
        </span>
        <?php endif; ?>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= $commentStats['total'] ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Total Comments</p>
    </div>

    <!-- Bookmarks -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:240ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-indigo-500 text-lg" style="font-variation-settings:'FILL' 1">bookmarks</span>
        </span>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= $totalBookmarks ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Bookmarks</p>
    </div>

    <!-- Categories -->
    <div class="stat-card col-span-1 bg-white rounded-2xl border border-surface-container p-5 hover:shadow-md transition-shadow" style="animation-delay:300ms">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-violet-500 text-lg" style="font-variation-settings:'FILL' 1">label</span>
        </span>
      </div>
      <p class="text-2xl font-extrabold text-blue-900 font-headline" data-count="<?= count($categoryBreakdown) ?>">0</p>
      <p class="text-xs text-slate-400 mt-0.5 font-medium">Categories</p>
    </div>
  </div>

  <!-- ── Row 2: Activity timeline + Status donuts ── -->
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    <!-- Activity Timeline (2/3) -->
    <div class="chart-card xl:col-span-2">
      <h3><span class="material-symbols-outlined text-[15px] text-blue-500">show_chart</span> Activity Over 12 Months</h3>
      <canvas id="chartTimeline" height="110"></canvas>
    </div>

    <!-- Post Status Donut (1/3) -->
    <div class="chart-card xl:col-span-1 flex flex-col">
      <h3><span class="material-symbols-outlined text-[15px] text-blue-500">donut_large</span> Post Status</h3>
      <div class="flex-1 flex items-center justify-center">
        <canvas id="chartPostStatus" style="max-height:200px"></canvas>
      </div>
      <div class="mt-3 grid grid-cols-3 gap-2 text-center">
        <div>
          <p class="text-base font-extrabold text-emerald-600"><?= $postStats['published'] ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Published</p>
        </div>
        <div>
          <p class="text-base font-extrabold text-amber-500"><?= $postStats['drafts'] ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Drafts</p>
        </div>
        <div>
          <p class="text-base font-extrabold text-slate-400"><?= $archived ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Archived</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Row 3: Top 5 Liked + Top 5 Viewed ── -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div class="chart-card">
      <h3><span class="material-symbols-outlined text-[15px] text-rose-500" style="font-variation-settings:'FILL' 1">favorite</span> Top 5 Most Liked</h3>
      <canvas id="chartTopLiked" height="160"></canvas>
    </div>

    <div class="chart-card">
      <h3><span class="material-symbols-outlined text-[15px] text-sky-500" style="font-variation-settings:'FILL' 1">visibility</span> Top 5 Most Viewed</h3>
      <canvas id="chartTopViewed" height="160"></canvas>
    </div>
  </div>

  <!-- ── Row 4: Category Breakdown ── -->
  <div class="chart-card">
    <h3><span class="material-symbols-outlined text-[15px] text-violet-500" style="font-variation-settings:'FILL' 1">label</span> Category Breakdown (Published Articles)</h3>
    <canvas id="chartCategories" height="<?= max(60, count($categoryBreakdown) * 38) ?>"></canvas>
  </div>

  <!-- ── Row 5: Comment Status + Engagement totals ── -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- Comment Status -->
    <div class="chart-card flex flex-col">
      <h3><span class="material-symbols-outlined text-[15px] text-teal-500" style="font-variation-settings:'FILL' 1">forum</span> Comment Moderation Status</h3>
      <div class="flex-1 flex items-center justify-center">
        <canvas id="chartCommentStatus" style="max-height:200px"></canvas>
      </div>
      <div class="mt-3 grid grid-cols-3 gap-2 text-center">
        <div>
          <p class="text-base font-extrabold text-emerald-600"><?= $commentStats['approved'] ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Approved</p>
        </div>
        <div>
          <p class="text-base font-extrabold text-amber-500"><?= $commentStats['pending'] ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Pending</p>
        </div>
        <div>
          <p class="text-base font-extrabold text-red-500"><?= $commentStats['rejected'] ?></p>
          <p class="text-[10px] text-slate-400 font-medium">Rejected</p>
        </div>
      </div>
    </div>

    <!-- Engagement totals bar -->
    <div class="chart-card">
      <h3><span class="material-symbols-outlined text-[15px] text-blue-500">bolt</span> Engagement by Category</h3>
      <canvas id="chartCatEngagement" height="160"></canvas>
    </div>
  </div>

  <!-- ── Row 6: Top Posts Table ── -->
  <div class="bg-white rounded-2xl border border-surface-container overflow-hidden">
    <div class="px-6 py-4 border-b border-surface-container flex items-center justify-between">
      <h3 class="font-headline text-sm font-bold text-blue-900 flex items-center gap-2">
        <span class="material-symbols-outlined text-base text-blue-500" style="font-variation-settings:'FILL' 1">leaderboard</span>
        Top 10 Articles by Engagement Score
      </h3>
      <span class="text-[10px] text-slate-400">Score = views + likes×3 + comments×2</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-surface-container-low text-xs text-slate-500 uppercase tracking-wide">
            <th class="px-5 py-3 text-left font-semibold w-8">#</th>
            <th class="px-5 py-3 text-left font-semibold">Article</th>
            <th class="px-4 py-3 text-left font-semibold hidden md:table-cell">Category</th>
            <th class="px-4 py-3 text-right font-semibold">Views</th>
            <th class="px-4 py-3 text-right font-semibold">Likes</th>
            <th class="px-4 py-3 text-right font-semibold hidden sm:table-cell">Comments</th>
            <th class="px-4 py-3 text-right font-semibold hidden lg:table-cell">Bookmarks</th>
            <th class="px-4 py-3 text-right font-semibold">Score</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
          <?php foreach ($topPosts as $rank => $p):
            $score = (int)$p['views_count'] + (int)$p['likes_count'] * 3 + (int)$p['comment_count'] * 2;
            $rankColors = ['text-amber-500', 'text-slate-400', 'text-amber-700'];
          ?>
          <tr class="hover:bg-surface-container-low/50 transition-colors group">
            <td class="px-5 py-3.5">
              <span class="text-sm font-extrabold <?= $rankColors[$rank] ?? 'text-slate-300' ?>">
                <?= $rank < 3 ? ['🥇','🥈','🥉'][$rank] : ($rank + 1) ?>
              </span>
            </td>
            <td class="px-5 py-3.5">
              <a href="/integration/magazine/article?id=<?= $p['id'] ?>"
                 target="_blank"
                 class="text-blue-900 font-semibold hover:text-primary transition-colors line-clamp-1 group-hover:text-primary">
                <?= htmlspecialchars($p['titre']) ?>
              </a>
            </td>
            <td class="px-4 py-3.5 hidden md:table-cell">
              <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[11px] font-semibold rounded-full">
                <?= htmlspecialchars($p['categorie']) ?>
              </span>
            </td>
            <td class="px-4 py-3.5 text-right text-slate-600 font-medium"><?= number_format($p['views_count']) ?></td>
            <td class="px-4 py-3.5 text-right text-rose-500 font-medium"><?= number_format($p['likes_count']) ?></td>
            <td class="px-4 py-3.5 text-right text-teal-600 font-medium hidden sm:table-cell"><?= number_format($p['comment_count']) ?></td>
            <td class="px-4 py-3.5 text-right text-indigo-500 font-medium hidden lg:table-cell"><?= number_format($p['bookmark_count']) ?></td>
            <td class="px-4 py-3.5 text-right">
              <span class="px-2.5 py-1 bg-primary text-white text-[11px] font-bold rounded-full"><?= number_format($score) ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($topPosts)): ?>
          <tr>
            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">No published articles yet.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /max-w-7xl -->

<script>
(function () {
    'use strict';

    /* ── Palette ── */
    const C = {
        primary:  '#004d99',
        sky:      '#0284c7',
        lightSky: '#38bdf8',
        teal:     '#0d9488',
        rose:     '#e11d48',
        amber:    '#d97706',
        violet:   '#7c3aed',
        gray:     '#94a3b8',
        emerald:  '#059669',
    };

    /* ── Counter animation ── */
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10);
        if (!target) { el.textContent = '0'; return; }
        const duration = 900;
        const step     = Math.ceil(duration / target) || 1;
        let current    = 0;
        const inc      = Math.max(1, Math.ceil(target / 60));
        const timer    = setInterval(() => {
            current = Math.min(current + inc, target);
            el.textContent = current.toLocaleString();
            if (current >= target) clearInterval(timer);
        }, step);
    });

    /* ── Shared defaults ── */
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#727783';

    /* ── 1. Activity Timeline (posts + comments) ── */
    const monthLabels  = <?= $jsMonthLabels ?>;
    const postsData    = <?= $jsPostsData ?>;
    const commentsData = <?= $jsCommentsData ?>;
    const viewsData    = <?= $jsViewsData ?>;
    const likesData    = <?= $jsLikesData ?>;

    new Chart(document.getElementById('chartTimeline'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Articles Published',
                    data: postsData,
                    borderColor: C.primary,
                    backgroundColor: C.primary + '18',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: C.primary,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'yLeft',
                },
                {
                    label: 'Comments',
                    data: commentsData,
                    borderColor: C.teal,
                    backgroundColor: C.teal + '18',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: C.teal,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'yLeft',
                },
                {
                    label: 'Likes',
                    data: likesData,
                    borderColor: C.rose,
                    borderWidth: 1.5,
                    pointRadius: 2,
                    pointBackgroundColor: C.rose,
                    tension: 0.4,
                    borderDash: [4, 3],
                    fill: false,
                    yAxisID: 'yRight',
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, padding: 16, font: { size: 11 } } },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x:      { grid: { display: false }, ticks: { maxRotation: 45 } },
                yLeft:  { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1, precision: 0 } },
                yRight: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { precision: 0 } },
            },
        },
    });

    /* ── 2. Post Status Donut ── */
    new Chart(document.getElementById('chartPostStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Published', 'Drafts', 'Archived'],
            datasets: [{
                data: [<?= $postStats['published'] ?>, <?= $postStats['drafts'] ?>, <?= $archived ?>],
                backgroundColor: [C.emerald, C.amber, C.gray],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
        },
    });

    /* ── 3. Top 5 Most Liked (horizontal bar) ── */
    new Chart(document.getElementById('chartTopLiked'), {
        type: 'bar',
        data: {
            labels: <?= $jsLikedLabels ?>,
            datasets: [{
                label: 'Likes',
                data: <?= $jsLikedData ?>,
                backgroundColor: [
                    C.rose, C.rose + 'cc', C.rose + '99', C.rose + '66', C.rose + '44',
                ],
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                y: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });

    /* ── 4. Top 5 Most Viewed (horizontal bar) ── */
    new Chart(document.getElementById('chartTopViewed'), {
        type: 'bar',
        data: {
            labels: <?= $jsViewedLabels ?>,
            datasets: [{
                label: 'Views',
                data: <?= $jsViewedData ?>,
                backgroundColor: [
                    C.sky, C.sky + 'cc', C.sky + '99', C.sky + '66', C.sky + '44',
                ],
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                y: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });

    /* ── 5. Category Breakdown (horizontal grouped bar) ── */
    const catColors = [C.primary, C.sky, C.teal, C.violet, C.amber, C.rose, C.emerald, '#f59e0b', '#8b5cf6'];
    new Chart(document.getElementById('chartCategories'), {
        type: 'bar',
        data: {
            labels: <?= $jsCatLabels ?>,
            datasets: [
                {
                    label: 'Articles',
                    data: <?= $jsCatPosts ?>,
                    backgroundColor: C.primary + 'cc',
                    borderRadius: 5,
                    borderSkipped: false,
                },
                {
                    label: 'Likes',
                    data: <?= $jsCatLikes ?>,
                    backgroundColor: C.rose + 'bb',
                    borderRadius: 5,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, padding: 14 } },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                y: { grid: { display: false } },
            },
        },
    });

    /* ── 6. Comment Status Donut ── */
    new Chart(document.getElementById('chartCommentStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [<?= $commentStats['approved'] ?>, <?= $commentStats['pending'] ?>, <?= $commentStats['rejected'] ?>],
                backgroundColor: [C.emerald, C.amber, C.rose],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
        },
    });

    /* ── 7. Engagement by Category (views stacked bar) ── */
    new Chart(document.getElementById('chartCatEngagement'), {
        type: 'bar',
        data: {
            labels: <?= $jsCatLabels ?>,
            datasets: [
                {
                    label: 'Views',
                    data: <?= $jsCatViews ?>,
                    backgroundColor: C.sky + 'cc',
                    borderRadius: 5,
                    borderSkipped: false,
                    stack: 'engagement',
                },
                {
                    label: 'Likes',
                    data: <?= $jsCatLikes ?>,
                    backgroundColor: C.rose + 'bb',
                    borderRadius: 5,
                    borderSkipped: false,
                    stack: 'engagement',
                },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, padding: 14 } },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 },
            },
            scales: {
                x: { beginAtZero: true, stacked: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                y: { stacked: true, grid: { display: false } },
            },
        },
    });

})();
</script>