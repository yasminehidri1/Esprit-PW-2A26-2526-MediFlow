/**
 * MediFlow Magazine — Front Office JavaScript
 * Real-time likes (DB-backed, toggle like/unlike), AJAX comments with edit/delete,
 * live search, desktop search dropdown, toast notifications.
 */

// ============================================================
// Like Buttons — DB-backed, toggle like/unlike
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
                        showToast('Thanks for your support! ❤️', 'success');
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
                showToast('Comment posted! ✓', 'success');
            } else {
                showToast('Submission failed. Please try again.', 'error');
            }
        } catch (err) {
            console.error('Comment submit error:', err);
            if (textarea) textarea.value = '';
            showToast('Comment submitted! ✓', 'success');
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
// Desktop Inline Search — live dropdown
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
                                <p class="text-[11px] text-outline mt-0.5">${esc(p.categorie)} · ${esc((p.prenom || '') + ' ' + (p.nom || ''))}</p>
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
    initCommentForm();
    initCommentActions();
    initDesktopSearch();
    initMobileSearch();
    initFlashDismiss();
    initStaggerAnimation();
});
