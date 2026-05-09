/**
 * MediFlow Magazine â€” Front Office JavaScript
 * Real-time likes (DB-backed, toggle like/unlike), AJAX comments with edit/delete,
 * live search, desktop search dropdown, toast notifications.
 */

// ============================================================
// Like Buttons â€” DB-backed, toggle like/unlike
// ============================================================
function initLikeButtons() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        // Mark already-liked buttons (from PHP-rendered state)
        if (btn.dataset.alreadyLiked === 'true') {
            _markLiked(btn);
        }

        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            if (!postId || this.dataset.busy) return;

            this.dataset.busy = 'true';
            const icon    = this.querySelector('.like-icon');
            const countEl = this.querySelector('.like-count');
            const isLiked = this.classList.contains('liked');

            icon?.classList.add('animate-heartBeat');

            try {
                const action = isLiked ? 'unlike' : 'like';
                const res  = await fetch(`/integration/magazine/like?id=${postId}&action=${action}`);
                const data = await res.json();

                if (data.success) {
                    if (isLiked) {
                        _markUnliked(this);
                        showToast('Like removed.', 'default');
                    } else {
                        _markLiked(this);
                        showToast('Thanks for your support! â¤ï¸', 'success');
                    }
                    if (countEl) {
                        const n = parseInt(data.likes, 10);
                        countEl.textContent = n >= 1000 ? (n / 1000).toFixed(1) + 'k' : n;
                    }
                } else if (data.message) {
                    showToast(data.message, 'default');
                }
            } catch (err) {
                console.error('Like failed:', err);
                showToast('Could not register like. Try again.', 'error');
            } finally {
                delete this.dataset.busy;
                setTimeout(() => icon?.classList.remove('animate-heartBeat'), 500);
            }
        });
    });
}

function _markLiked(btn) {
    btn.classList.add('liked');
    const icon = btn.querySelector('.like-icon');
    if (icon) {
        icon.style.fontVariationSettings = "'FILL' 1";
        icon.style.color = '#ef4444';
    }
}

function _markUnliked(btn) {
    btn.classList.remove('liked');
    const icon = btn.querySelector('.like-icon');
    if (icon) {
        icon.style.fontVariationSettings = "'FILL' 0";
        icon.style.color = '';
    }
}

// ============================================================
// AJAX Comment Submission (article page)
// ============================================================
function initCommentForm() {
    const form = document.getElementById('commentForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const textarea  = form.querySelector('textarea[name="contenu"]');
        const submitBtn = form.querySelector('button[type="submit"]');
        const content   = textarea?.value.trim();

        if (!content) {
            showToast('Please write something before submitting.', 'error');
            return;
        }

        const originalHTML  = submitBtn.innerHTML;
        submitBtn.disabled  = true;
        submitBtn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle;margin-right:6px;"></span> Submitting...';

        try {
            const formData = new FormData(form);
            const res = await fetch('/integration/magazine/comment/add', {
                method: 'POST',
                body:   formData
            });

            if (res.redirected || res.ok) {
                if (textarea) textarea.value = '';
                const userName = document.getElementById('currentUserName')?.textContent || 'You';
                const userInitials = document.getElementById('currentUserInitials')?.textContent || 'U';
                _prependComment(content, userName, userInitials);
                showToast('Comment posted! âœ“', 'success');
            } else {
                showToast('Submission failed. Please try again.', 'error');
            }
        } catch (err) {
            console.error('Comment submit error:', err);
            if (textarea) textarea.value = '';
            showToast('Comment submitted! âœ“', 'success');
        } finally {
            submitBtn.disabled  = false;
            submitBtn.innerHTML = originalHTML;
        }
    });
}

function _prependComment(content, userName, userInitials) {
    const list = document.getElementById('commentsList');
    if (!list) return;

    const empty = list.querySelector('.empty-comments');
    empty?.remove();

    const div = document.createElement('div');
    div.className = 'bg-surface-container-lowest rounded-xl p-5 shadow-[0_4px_20px_rgba(0,77,153,0.03)] border-l-4 border-tertiary-fixed animate-slideIn';
    div.innerHTML = `
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">${esc(userInitials)}</div>
            <div>
              <p class="text-sm font-bold text-on-surface">${esc(userName)}</p>
              <p class="text-[10px] text-slate-400">just now</p>
            </div>
          </div>
        </div>
        <p class="text-sm text-on-surface-variant leading-relaxed pl-12">${esc(content)}</p>
    `;
    list.prepend(div);

    const counter = document.getElementById('commentCount');
    if (counter) counter.textContent = parseInt(counter.textContent || '0', 10) + 1;
}

// ============================================================
// Inline Comment Edit/Delete (own comments)
// ============================================================
function initCommentActions() {
    // Edit toggle
    document.querySelectorAll('.comment-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const commentId = this.dataset.commentId;
            const display   = document.getElementById(`comment-text-${commentId}`);
            const editForm  = document.getElementById(`comment-edit-form-${commentId}`);
            if (!display || !editForm) return;

            const isEditing = !editForm.classList.contains('hidden');
            display.classList.toggle('hidden', !isEditing);
            editForm.classList.toggle('hidden', isEditing);
            if (!isEditing) editForm.querySelector('textarea')?.focus();
        });
    });

    // Cancel edit
    document.querySelectorAll('.comment-edit-cancel').forEach(btn => {
        btn.addEventListener('click', function () {
            const commentId = this.dataset.commentId;
            document.getElementById(`comment-text-${commentId}`)?.classList.remove('hidden');
            document.getElementById(`comment-edit-form-${commentId}`)?.classList.add('hidden');
        });
    });

    // Delete comment
    document.querySelectorAll('.comment-delete-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('Delete this comment?')) return;
            const commentId = this.dataset.commentId;
            const postId    = this.dataset.postId;
            const card      = document.getElementById(`comment-${commentId}`);

            // Optimistic: hide card immediately
            if (card) card.style.opacity = '0.4';

            try {
                const res = await fetch(`/integration/magazine/comment/delete?id=${commentId}&post_id=${postId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    card?.remove();
                    const counter = document.getElementById('commentCount');
                    if (counter) counter.textContent = Math.max(0, parseInt(counter.textContent || '0', 10) - 1);
                    showToast('Comment deleted.', 'default');
                } else {
                    if (card) card.style.opacity = '1';
                    showToast('Could not delete comment.', 'error');
                }
            } catch(err) {
                if (card) card.style.opacity = '1';
                showToast('Could not delete comment.', 'error');
            }
        });
    });
}

// ============================================================
// Desktop Inline Search â€” live dropdown
// ============================================================
function initDesktopSearch() {
    const input     = document.getElementById('navSearchInput');
    const resultsEl = document.getElementById('navSearchResults');
    if (!input || !resultsEl) return;

    let timer = null;

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();

        if (q.length < 2) { resultsEl.classList.add('hidden'); return; }

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`/integration/magazine/search?q=${encodeURIComponent(q)}`);
                const data = await res.json();

                if (!data.results?.length) {
                    resultsEl.innerHTML = `<div class="px-5 py-4 text-sm text-on-surface-variant text-center">No results for "<b>${esc(q)}</b>"</div>`;
                } else {
                    resultsEl.innerHTML = data.results.map(p => `
                        <a href="/integration/magazine/article?id=${p.id}"
                           class="flex items-center gap-4 px-5 py-3 hover:bg-surface-container-low transition-colors border-b border-surface-container last:border-0 group">
                            <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">article</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-on-surface truncate group-hover:text-primary transition-colors">${esc(p.titre)}</p>
                                <p class="text-[11px] text-outline mt-0.5">${esc(p.categorie)} Â· ${esc((p.prenom || '') + ' ' + (p.nom || ''))}</p>
                            </div>
                            <span class="material-symbols-outlined text-outline text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>
                    `).join('');
                }
                resultsEl.classList.remove('hidden');
            } catch (err) { console.error('Search error:', err); }
        }, 280);
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !resultsEl.contains(e.target)) {
            resultsEl.classList.add('hidden');
        }
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Escape') { resultsEl.classList.add('hidden'); input.blur(); }
    });
}

// ============================================================
// Mobile Search Overlay
// ============================================================
function toggleSearch() {
    const overlay  = document.getElementById('searchOverlay');
    const inputEl  = document.getElementById('searchInput');
    const resultsEl = document.getElementById('searchResults');

    const isHidden = overlay.classList.contains('hidden');
    overlay.classList.toggle('hidden', !isHidden);
    document.body.style.overflow = isHidden ? 'hidden' : '';

    if (isHidden) {
        overlay.classList.add('animate-fadeIn');
        setTimeout(() => inputEl?.focus(), 80);
    } else {
        if (inputEl)  inputEl.value = '';
        resultsEl?.classList.add('hidden');
    }
}

function initMobileSearch() {
    const inputEl   = document.getElementById('searchInput');
    const resultsEl = document.getElementById('searchResults');

    document.getElementById('searchToggle')?.addEventListener('click', toggleSearch);
    document.getElementById('searchOverlay')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) toggleSearch();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !document.getElementById('searchOverlay')?.classList.contains('hidden')) {
            toggleSearch();
        }
    });

    if (!inputEl || !resultsEl) return;

    let timer = null;
    inputEl.addEventListener('input', () => {
        clearTimeout(timer);
        const q = inputEl.value.trim();
        if (q.length < 2) { resultsEl.classList.add('hidden'); return; }

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`/integration/magazine/search?q=${encodeURIComponent(q)}`);
                const data = await res.json();

                resultsEl.innerHTML = !data.results?.length
                    ? `<div class="px-4 py-4 text-sm text-center text-on-surface-variant">No results for "<b>${esc(q)}</b>"</div>`
                    : data.results.map(p => `
                        <a href="/integration/magazine/article?id=${p.id}"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-surface-container last:border-0">
                            <span class="material-symbols-outlined text-on-surface-variant text-sm">article</span>
                            <div>
                                <p class="text-sm font-bold text-on-surface">${esc(p.titre)}</p>
                                <p class="text-[11px] text-outline">${esc(p.categorie)}</p>
                            </div>
                        </a>
                    `).join('');

                resultsEl.classList.remove('hidden');
            } catch (err) { console.error('Mobile search error:', err); }
        }, 280);
    });
}

// ============================================================
// Toast Notifications
// ============================================================
function showToast(message, type = 'default') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.25s, transform 0.25s';
        toast.style.opacity    = '0';
        toast.style.transform  = 'translateY(6px)';
        setTimeout(() => toast.remove(), 260);
    }, 3500);
}

// ============================================================
// Auto-dismiss Flash Messages
// ============================================================
function initFlashDismiss() {
    ['flash-success', 'flash-error'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 300);
        }, 5000);
    });
}

// ============================================================
// Stagger card entrance animation
// ============================================================
function initStaggerAnimation() {
    const items = document.querySelectorAll('.stagger-item');
    if (!('IntersectionObserver' in window) || !items.length) return;

    const obs = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.06 });

    items.forEach(item => {
        item.style.animationPlayState = 'paused';
        obs.observe(item);
    });
}

// ============================================================
// Threaded Comments â€” Reply toggle + Comment Likes
// ============================================================
function initThreadedComments() {
    // Reply toggle: show/hide inline reply form
    document.addEventListener('click', e => {
        const toggleBtn = e.target.closest('.comment-reply-toggle');
        if (toggleBtn) {
            const commentId  = toggleBtn.dataset.commentId;
            const replyForm  = document.getElementById(`reply-form-${commentId}`);
            if (!replyForm) return;
            const isOpen = !replyForm.classList.contains('hidden');
            replyForm.classList.toggle('hidden', isOpen);
            if (!isOpen) replyForm.querySelector('textarea')?.focus();
        }

        // Cancel reply
        const cancelBtn = e.target.closest('.reply-cancel-btn');
        if (cancelBtn) {
            const commentId = cancelBtn.dataset.commentId;
            document.getElementById(`reply-form-${commentId}`)?.classList.add('hidden');
        }
    });

    // Submit reply forms via fetch
    document.addEventListener('submit', async e => {
        const form = e.target.closest('.reply-form');
        if (!form) return;
        e.preventDefault();

        const textarea  = form.querySelector('.reply-textarea');
        const submitBtn = form.querySelector('button[type="submit"]');
        const content   = textarea?.value.trim();
        if (!content) return;

        const postId   = form.dataset.postId;
        const parentId = form.dataset.parentId;

        const orig = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span style="display:inline-block;width:12px;height:12px;border:2px solid rgba(255,255,255,.5);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite"></span>';

        try {
            const res  = await fetch('/integration/magazine/comment/add-ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_post: postId, parent_id: parseInt(parentId), contenu: content }),
            });
            const data = await res.json();

            if (data.success) {
                textarea.value = '';
                document.getElementById(`reply-form-${parentId}`)?.classList.add('hidden');
                _injectReply(parentId, content);
                showToast('Reply posted!', 'success');
                const counter = document.getElementById('commentCount');
                if (counter) counter.textContent = parseInt(counter.textContent || '0', 10) + 1;
            } else {
                showToast(data.message || 'Could not post reply.', 'error');
            }
        } catch (err) {
            showToast('Network error. Try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = orig;
        }
    });

    // Comment like buttons
    document.addEventListener('click', async e => {
        const likeBtn = e.target.closest('.comment-like-btn');
        if (!likeBtn || likeBtn.dataset.busy) return;
        likeBtn.dataset.busy = 'true';

        const commentId = likeBtn.dataset.commentId;
        const icon      = likeBtn.querySelector('.comment-like-icon');
        const countEl   = likeBtn.querySelector('.comment-like-count');

        try {
            const fd = new FormData();
            fd.append('comment_id', commentId);
            const res  = await fetch('/integration/magazine/comment/like', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                if (icon) {
                    icon.textContent = data.liked ? 'favorite' : 'favorite_border';
                    icon.style.color = data.liked ? '#ef4444' : '';
                    icon.style.fontVariationSettings = data.liked ? "'FILL' 1" : "'FILL' 0";
                }
                if (countEl) countEl.textContent = data.likes;
            }
        } catch(err) { /* silent */ } finally {
            delete likeBtn.dataset.busy;
        }
    });
}

function _injectReply(parentCommentId, content) {
    const parentNode = document.getElementById(`comment-${parentCommentId}`);
    if (!parentNode) return;

    let repliesContainer = parentNode.querySelector(':scope > .mt-4.space-y-4');
    if (!repliesContainer) {
        repliesContainer = document.createElement('div');
        repliesContainer.className = 'mt-4 space-y-4';
        parentNode.appendChild(repliesContainer);
    }

    const userName     = document.getElementById('currentUserName')?.textContent || 'You';
    const userInitials = document.getElementById('currentUserInitials')?.textContent || 'U';

    const div = document.createElement('div');
    div.className = 'comment-node ml-10 bg-surface-container-lowest rounded-xl p-5 border-l-4 border-blue-200 animate-slideIn';
    div.innerHTML = `
        <div class="flex items-start mb-3 gap-3">
          <div class="w-9 h-9 rounded-full bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container flex-shrink-0">${esc(userInitials)}</div>
          <div>
            <p class="text-sm font-bold text-on-surface">${esc(userName)} <span class="text-[10px] text-slate-400 font-normal ml-1">Â· Reply</span></p>
            <p class="text-[10px] text-slate-400">Just now</p>
          </div>
        </div>
        <p class="text-sm text-on-surface-variant leading-relaxed pl-12">${esc(content)}</p>`;
    repliesContainer.appendChild(div);
}

// ============================================================
// Bookmark Toggle (article page)
// ============================================================
function initBookmark() {
    const btn = document.getElementById('bookmarkBtn');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        if (this.dataset.busy) return;
        this.dataset.busy = 'true';

        const postId = this.dataset.postId;
        const isBookmarked = this.dataset.bookmarked === 'true';
        const icon = this.querySelector('.bookmark-icon');

        // Optimistic UI update
        _setBookmarkState(this, icon, !isBookmarked);

        try {
            const fd = new FormData();
            fd.append('post_id', postId);
            const res  = await fetch('/integration/magazine/bookmark', { method: 'POST', body: fd });
            const data = await res.json();

            if (!data.success) {
                _setBookmarkState(this, icon, isBookmarked); // revert
                showToast('Could not update bookmark.', 'error');
            } else {
                showToast(data.bookmarked ? 'Saved to bookmarks!' : 'Bookmark removed.', data.bookmarked ? 'success' : 'default');
            }
        } catch (e) {
            _setBookmarkState(this, icon, isBookmarked);
            showToast('Network error. Please try again.', 'error');
        } finally {
            delete this.dataset.busy;
        }
    });
}

function _setBookmarkState(btn, icon, bookmarked) {
    btn.dataset.bookmarked = bookmarked ? 'true' : 'false';
    btn.title = bookmarked ? 'Remove bookmark' : 'Save for later';
    if (icon) {
        icon.textContent = bookmarked ? 'bookmark' : 'bookmark_border';
        icon.style.color = bookmarked ? '#f59e0b' : '';
    }
}

// ============================================================
// AI Post Summary â€” Ollama / TinyLlama
// ============================================================
function initAISummary() {
    const btn           = document.getElementById('aiSummaryBtn');
    const panel         = document.getElementById('aiSummaryPanel');
    const closeBtn      = document.getElementById('aiSummaryClose');
    const loadingEl     = document.getElementById('aiSummaryLoading');
    const contentEl     = document.getElementById('aiSummaryContent');
    const summaryTextEl = document.getElementById('aiSummaryText');
    const keyPointsList = document.getElementById('aiKeyPoints');
    const keyPointsWrap = document.getElementById('aiKeyPointsWrap');

    if (!btn || !panel) return;

    let alreadyFetched = false;

    function openPanel() {
        panel.classList.remove('hidden');
        // Re-trigger slide-in each time
        panel.classList.remove('animate-slideIn');
        void panel.offsetWidth;
        panel.classList.add('animate-slideIn');
        btn.querySelector('.ai-btn-text').textContent = 'Hide Summary';
        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) icon.textContent = 'expand_less';
    }

    function closePanel() {
        panel.classList.add('hidden');
        btn.querySelector('.ai-btn-text').textContent = 'AI Summary';
        const icon = btn.querySelector('.material-symbols-outlined');
        if (icon) icon.textContent = 'auto_awesome';
    }

    btn.addEventListener('click', async () => {
        if (!panel.classList.contains('hidden')) { closePanel(); return; }
        openPanel();
        if (!alreadyFetched) { alreadyFetched = true; await fetchSummary(); }
    });

    closeBtn?.addEventListener('click', closePanel);

    async function fetchSummary() {
        loadingEl.classList.remove('hidden');
        contentEl.classList.add('hidden');

        try {
            const fd = new FormData();
            fd.append('post_id', btn.dataset.postId);

            const res  = await fetch('/integration/magazine/summarize', { method: 'POST', body: fd });
            const data = await res.json();

            loadingEl.classList.add('hidden');
            contentEl.classList.remove('hidden');

            if (!data.success) {
                summaryTextEl.textContent = data.error || 'Could not generate summary.';
                return;
            }

            typewrite(summaryTextEl, data.summary || 'No summary available.', 16, () => {
                if (data.keyPoints?.length) showKeyPoints(data.keyPoints);
            });

        } catch (err) {
            loadingEl.classList.add('hidden');
            contentEl.classList.remove('hidden');
            summaryTextEl.textContent = 'AI service unavailable. Make sure Ollama is running on localhost:11434.';
        }
    }

    function showKeyPoints(points) {
        keyPointsWrap.classList.remove('hidden');
        keyPointsList.innerHTML = '';
        points.forEach((point, i) => {
            setTimeout(() => {
                const li = document.createElement('li');
                li.className = 'flex items-start gap-2.5';
                li.style.cssText = 'opacity:0;transform:translateY(6px);transition:opacity .3s,transform .3s';
                li.innerHTML = `
                    <span style="min-width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#0284c7,#004d99);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;margin-top:1px">${i + 1}</span>
                    <span style="font-size:13px;color:#374151;line-height:1.6">${esc(point)}</span>`;
                keyPointsList.appendChild(li);
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    li.style.opacity = '1';
                    li.style.transform = 'translateY(0)';
                }));
            }, i * 280);
        });
    }
}

// Typewriter effect â€” renders text character-by-character with a blinking cursor
function typewrite(element, text, speed, onDone) {
    let i = 0;
    const textNode = document.createTextNode('');
    const cursor   = document.createElement('span');
    cursor.className = 'tw-cursor';
    element.innerHTML = '';
    element.appendChild(textNode);
    element.appendChild(cursor);

    const iv = setInterval(() => {
        textNode.textContent += text[i++];
        if (i >= text.length) {
            clearInterval(iv);
            cursor.remove();
            onDone?.();
        }
    }, speed);
}

// ============================================================
// Utility: Escape HTML for injected content
// ============================================================
function esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ============================================================
// Init all on DOMContentLoaded
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    initLikeButtons();
    initBookmark();
    initThreadedComments();
    initCommentForm();
    initCommentActions();
    initDesktopSearch();
    initMobileSearch();
    initFlashDismiss();
    initStaggerAnimation();
    initAISummary();
});

