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
                
                // Check if response is actually JSON
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await res.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                }
                
                const data = await res.json();

                if (data.success) {
                    // Trust the server's response for the actual state
                    if (data.liked) {
                        _markLiked(this);
                        showToast('Thanks for your support! ❤️', 'success');
                    } else {
                        _markUnliked(this);
                        showToast('Like removed.', 'default');
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
            const commentId = this.dataset.commentId;
            const postId    = this.dataset.postId;
            const card      = document.getElementById(`comment-${commentId}`);
            
            showDeleteCommentModal(commentId, postId, card);
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
    // Map old types to new types
    const typeMap = {
        'success': 'success',
        'error': 'error',
        'default': 'info',
        'warning': 'warning'
    };
    
    const mappedType = typeMap[type] || 'info';
    
    // Remove existing notification if any
    const existing = document.getElementById('notificationCenter');
    if (existing) existing.remove();
    
    const bgColor = mappedType === 'success' ? 'bg-gradient-to-r from-emerald-500 to-teal-500' :
                    mappedType === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-500' :
                    mappedType === 'warning' ? 'bg-gradient-to-r from-amber-500 to-orange-500' :
                    'bg-gradient-to-r from-blue-500 to-indigo-500';
    
    const icon = mappedType === 'success' ? 'check_circle' :
                 mappedType === 'error' ? 'error' :
                 mappedType === 'warning' ? 'warning' :
                 'info';
    
    const notification = document.createElement('div');
    notification.id = 'notificationCenter';
    notification.className = `fixed bottom-6 right-6 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 z-50 animate-in fade-in slide-in-from-right-4 duration-300`;
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
    initSaveForLater();
    initSocialShareOverlay();
});

// Removed initAISummarize from here to article.php inline script

// ============================================================
// Save for Later — LocalStorage Based
// ============================================================
function initSaveForLater() {
    const saveBtn = document.getElementById('saveForLaterBtn');
    const drawer = document.getElementById('savedArticlesDrawer');
    const drawerToggle = document.getElementById('savedArticlesToggle');
    const closeBtn = document.getElementById('closeSavedDrawer');
    const overlay = document.getElementById('drawerOverlay');
    const listEl = document.getElementById('savedArticlesList');
    const badge = document.getElementById('savedCountBadge');

    if (!drawer || !drawerToggle) return;

    // Load initial state
    updateSavedUI();

    // Toggle drawer
    const openDrawer = () => {
        drawer.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        document.body.style.overflow = 'hidden';
        renderSavedList();
    };

    const closeDrawer = () => {
        drawer.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => overlay.classList.add('hidden'), 500);
        document.body.style.overflow = '';
    };

    drawerToggle.addEventListener('click', openDrawer);
    closeBtn?.addEventListener('click', closeDrawer);
    overlay?.addEventListener('click', closeDrawer);

    // Save/Unsave action
    if (saveBtn) {
        const postId = saveBtn.dataset.postId;
        let savedItems = JSON.parse(localStorage.getItem('mediflow_saved') || '[]');
        
        if (savedItems.some(i => i.id == postId)) {
            saveBtn.querySelector('.bookmark-icon').textContent = 'bookmark';
            saveBtn.classList.add('text-primary');
        }

        saveBtn.addEventListener('click', () => {
            savedItems = JSON.parse(localStorage.getItem('mediflow_saved') || '[]');
            const idx = savedItems.findIndex(i => i.id == postId);

            if (idx > -1) {
                savedItems.splice(idx, 1);
                saveBtn.querySelector('.bookmark-icon').textContent = 'bookmark_border';
                saveBtn.classList.remove('text-primary');
                showToast('Removed from reading list.');
            } else {
                savedItems.push({
                    id: postId,
                    title: saveBtn.dataset.postTitle,
                    image: saveBtn.dataset.postImage,
                    url: window.location.href,
                    date: new Date().toLocaleDateString()
                });
                saveBtn.querySelector('.bookmark-icon').textContent = 'bookmark';
                saveBtn.classList.add('text-primary');
                showToast('Saved for later! 📖', 'success');
            }
            localStorage.setItem('mediflow_saved', JSON.stringify(savedItems));
            updateSavedUI();
        });
    }

    function updateSavedUI() {
        const savedItems = JSON.parse(localStorage.getItem('mediflow_saved') || '[]');
        if (badge) {
            badge.textContent = savedItems.length;
            badge.classList.toggle('hidden', savedItems.length === 0);
        }
    }

    function renderSavedList() {
        const savedItems = JSON.parse(localStorage.getItem('mediflow_saved') || '[]');
        if (!listEl) return;

        if (savedItems.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-12 opacity-50">
                    <span class="material-symbols-outlined text-6xl mb-4">bookmark_border</span>
                    <p class="text-sm font-medium">Your reading list is empty.</p>
                </div>
            `;
            return;
        }

        listEl.innerHTML = savedItems.map(item => `
            <div class="flex gap-4 group relative bg-surface-container-low p-3 rounded-xl hover:bg-surface-container transition-colors">
                <img src="${esc(item.image)}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0 shadow-sm" alt=""/>
                <div class="flex-1 min-w-0">
                    <a href="${esc(item.url)}" class="block">
                        <h4 class="text-sm font-bold text-blue-900 leading-snug line-clamp-2 group-hover:text-primary transition-colors">${esc(item.title)}</h4>
                    </a>
                    <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">calendar_today</span>
                        Saved on ${esc(item.date)}
                    </p>
                </div>
                <button onclick="removeSavedItem('${item.id}')" class="absolute top-2 right-2 p-1 text-slate-300 hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        `).join('');
    }

    window.removeSavedItem = (id) => {
        let savedItems = JSON.parse(localStorage.getItem('mediflow_saved') || '[]');
        savedItems = savedItems.filter(i => i.id != id);
        localStorage.setItem('mediflow_saved', JSON.stringify(savedItems));
        
        // Update current page button if applicable
        if (saveBtn && saveBtn.dataset.postId == id) {
            saveBtn.querySelector('.bookmark-icon').textContent = 'bookmark_border';
            saveBtn.classList.remove('text-primary');
        }
        
        updateSavedUI();
        renderSavedList();
    };
}

// ============================================================
// Social Share Overlay (Highlight Selection)
// ============================================================
function initSocialShareOverlay() {
    const tooltip = document.getElementById('shareTooltip');
    const article = document.querySelector('.prose-article');
    if (!tooltip || !article) return;

    const showTooltip = () => {
        const selection = window.getSelection();
        const text = selection.toString().trim();

        if (text.length > 5 && selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            
            tooltip.style.left = `${rect.left + (rect.width / 2) - 50}px`;
            tooltip.style.top = `${rect.top + window.scrollY - 60}px`;
            tooltip.classList.remove('hidden');
        } else {
            tooltip.classList.add('hidden');
        }
    };

    document.addEventListener('mouseup', () => {
        setTimeout(showTooltip, 50);
    });

    document.getElementById('copySelection')?.addEventListener('click', () => {
        const text = window.getSelection().toString();
        navigator.clipboard.writeText(text);
        showToast('Text copied to clipboard!');
        window.getSelection().removeAllRanges();
        tooltip.classList.add('hidden');
    });

    document.getElementById('shareTwitter')?.addEventListener('click', () => {
        const text = window.getSelection().toString();
        const url = encodeURIComponent(window.location.href);
        const tweet = encodeURIComponent(`"${text.substring(0, 100)}..." \nRead more on MediFlow: `);
        window.open(`https://twitter.com/intent/tweet?text=${tweet}&url=${url}`, '_blank');
        tooltip.classList.add('hidden');
    });

    document.getElementById('shareLinkedIn')?.addEventListener('click', () => {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
        tooltip.classList.add('hidden');
    });
}

// ============================================================
// STYLED DELETE COMMENT MODAL
// ============================================================
function showDeleteCommentModal(commentId, postId, card) {
    const modal = document.createElement('div');
    modal.id = 'deleteCommentModal';
    modal.className = 'fixed inset-0 bg-black/40 flex items-center justify-center z-50 animate-in fade-in duration-200';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-in scale-in-95 duration-200">
            <!-- Header with icon -->
            <div class="bg-gradient-to-r from-red-500 to-rose-500 text-white p-6 rounded-t-2xl flex items-center gap-4">
                <span class="material-symbols-outlined text-4xl">delete</span>
                <div>
                    <h3 class="font-headline font-bold text-lg">Delete Comment?</h3>
                    <p class="text-sm text-white/90">This cannot be undone</p>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    Are you sure you want to delete this comment? Once deleted, it will be permanently removed and cannot be recovered.
                </p>
                
                <!-- Actions -->
                <div class="flex gap-3">
                    <button onclick="document.getElementById('deleteCommentModal').remove()" 
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button onclick="performDeleteComment('${commentId}', '${postId}', document.getElementById('comment-${commentId}'))" 
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

// Helper function to perform the actual delete
async function performDeleteComment(commentId, postId, card) {
    // Optimistic: hide card immediately
    if (card) card.style.opacity = '0.4';
    
    // Close modal
    const modal = document.getElementById('deleteCommentModal');
    if (modal) modal.remove();

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
}
