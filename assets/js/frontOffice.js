/**
 * MediFlow Magazine — Front Office JavaScript
 * Patient interactions: likes, search, comments, animations
 */

// ============================================================
// AJAX Like Button
// ============================================================

function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.like-btn');
    
    likeButtons.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const icon = this.querySelector('.like-icon');
            const countEl = this.querySelector('.like-count');
            
            // Optimistic UI update
            this.classList.add('liked');
            icon.style.fontVariationSettings = "'FILL' 1";
            icon.classList.add('animate-heartBeat');
            
            try {
                const response = await fetch(`frontOffice.php?action=like&id=${postId}`);
                const data = await response.json();
                
                if (data.success) {
                    // Format the new count
                    let count = data.likes;
                    countEl.textContent = count >= 1000 ? (count / 1000).toFixed(1) + 'k' : count;
                    showToast('Thanks for the love! ❤️', 'success');
                }
            } catch (error) {
                console.error('Like failed:', error);
                // Revert on error
                this.classList.remove('liked');
                icon.style.fontVariationSettings = "'FILL' 0";
            }
            
            // Remove animation class after it plays
            setTimeout(() => icon.classList.remove('animate-heartBeat'), 500);
        });
    });
}

// ============================================================
// Search Overlay
// ============================================================

function toggleSearch() {
    const overlay = document.getElementById('searchOverlay');
    const input = document.getElementById('searchInput');
    
    if (overlay.classList.contains('hidden')) {
        overlay.classList.remove('hidden');
        overlay.classList.add('animate-fadeIn');
        document.body.style.overflow = 'hidden';
        setTimeout(() => input?.focus(), 100);
    } else {
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        if (input) input.value = '';
        document.getElementById('searchResults')?.classList.add('hidden');
    }
}

let searchTimeout = null;

function initSearch() {
    const input = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('searchResults');
    const toggle = document.getElementById('searchToggle');
    
    toggle?.addEventListener('click', toggleSearch);
    
    if (!input || !resultsContainer) return;
    
    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`frontOffice.php?action=search&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.results && data.results.length > 0) {
                    resultsContainer.innerHTML = data.results.map(post => `
                        <a href="frontOffice.php?action=view&id=${post.id}" 
                           class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl">article</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-blue-900 truncate">${escapeHtml(post.titre)}</h4>
                                <p class="text-xs text-slate-400">${escapeHtml(post.categorie)} · ${escapeHtml((post.prenom || '') + ' ' + (post.nom || ''))}</p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward</span>
                        </a>
                    `).join('');
                    resultsContainer.classList.remove('hidden');
                } else {
                    resultsContainer.innerHTML = `
                        <div class="text-center py-8 text-slate-400">
                            <span class="material-symbols-outlined text-3xl mb-2">search_off</span>
                            <p class="text-sm">No articles found for "${escapeHtml(query)}"</p>
                        </div>
                    `;
                    resultsContainer.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Search failed:', error);
            }
        }, 300);
    });
    
    // Close on Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggleSearch();
        }
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
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// Auto-dismiss Flash Messages
// ============================================================

function initFlashDismiss() {
    const flashes = document.querySelectorAll('.toast-notification, [id^="flash-"]');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.3s, transform 0.3s';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-12px)';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    });
}

// ============================================================
// Utility: Escape HTML
// ============================================================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================================
// Stagger Animation on Scroll
// ============================================================

function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slideUp');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('article, section').forEach(el => {
        observer.observe(el);
    });
}

// ============================================================
// Magazine Sidebar Toggle
// ============================================================

let magazineOpen = false;

function toggleMagazineMenu() {
    const menu = document.getElementById('magazineCategories');
    const chevron = document.getElementById('magazineChevron');
    if (!menu || !chevron) return;

    magazineOpen = !magazineOpen;

    if (magazineOpen) {
        // Expand: measure scroll height and animate to it
        menu.style.maxHeight = menu.scrollHeight + 'px';
        menu.style.opacity = '1';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        menu.style.maxHeight = '0';
        menu.style.opacity = '0';
        chevron.style.transform = 'rotate(0deg)';
    }
}

function initMagazineMenu() {
    const menu = document.getElementById('magazineCategories');
    if (!menu) return;

    // Auto-expand if we are on a category page (URL contains action=category)
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'category') {
        magazineOpen = true;
        menu.style.transition = 'none'; // No animation on load
        menu.style.maxHeight = menu.scrollHeight + 'px';
        menu.style.opacity = '1';
        const chevron = document.getElementById('magazineChevron');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        // Restore transition after paint
        requestAnimationFrame(() => {
            menu.style.transition = '';
        });
    }
}

// ============================================================
// Initialize
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    initLikeButtons();
    initSearch();
    initFlashDismiss();
    initScrollAnimations();
    initMagazineMenu();
});
