/**
 * MediFlow Magazine — Back Office JavaScript
 * Admin interactions: delete modals, search, form validation, stat counters
 */

// ============================================================
// Delete Confirmation Modal
// ============================================================

function showDeleteModal(url, type = 'item') {
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('deleteConfirmBtn');
    const modalText = document.getElementById('deleteModalText');
    
    confirmBtn.href = url;
    modalText.textContent = `Are you sure you want to delete this ${type}? This action cannot be undone.`;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.querySelector('div > div').classList.add('animate-scaleIn');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on backdrop click
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// ============================================================
// Animated Stat Counters
// ============================================================

function animateCounters() {
    const counters = document.querySelectorAll('.stat-counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        const duration = 1500; // ms
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(eased * target);
            
            // Format large numbers
            if (current >= 1000) {
                counter.textContent = (current / 1000).toFixed(1) + 'k';
            } else {
                counter.textContent = current.toLocaleString();
            }
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                // Final value
                if (target >= 1000) {
                    counter.textContent = (target / 1000).toFixed(1) + 'k';
                } else {
                    counter.textContent = target.toLocaleString();
                }
            }
        }
        
        requestAnimationFrame(updateCounter);
    });
}

// ============================================================
// Article Form — Character Count & Read Time
// ============================================================

function initFormHelpers() {
    const textarea = document.querySelector('textarea[name="contenu"]');
    const charCount = document.getElementById('charCount');
    const readTime = document.getElementById('readTime');
    
    if (textarea && charCount && readTime) {
        function updateCounts() {
            const text = textarea.value;
            charCount.textContent = text.length.toLocaleString() + ' characters';
            
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            const minutes = Math.max(1, Math.ceil(words / 200));
            readTime.textContent = '~' + minutes + ' min read';
        }
        
        textarea.addEventListener('input', updateCounts);
        updateCounts(); // Initial count
    }
}

// ============================================================
// Auto-dismiss Flash Messages
// ============================================================

function initFlashDismiss() {
    const flashes = document.querySelectorAll('#flash-success, #flash-error');
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
// Initialize
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    animateCounters();
    initFormHelpers();
    initFlashDismiss();
});
