/**
 * MediFlow - Main Application JavaScript
 * Handles animations, interactions, and page behaviors
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize page animations
    initializePageAnimations();
    
    // Setup navigation behaviors
    setupNavigation();
    
    // Setup scroll animations
    setupScrollAnimations();
    
    // Initialize dynamic content
    initializeHeroTypewriter();
});

/**
 * Initialize page load animations
 */
function initializePageAnimations() {
    setTimeout(function () {
        document.documentElement.classList.add('is-loaded');
    }, 200);
}

/**
 * Setup navbar scroll effects and mobile navigation
 */
function setupNavigation() {
    var navHeader = document.querySelector('.site-nav-header');
    
    // Handle navbar blur effect on scroll
    function onScrollNav() {
        if (window.scrollY > 50) {
            navHeader.classList.add('scrolled');
        } else {
            navHeader.classList.remove('scrolled');
        }
    }
    
    onScrollNav();
    window.addEventListener('scroll', onScrollNav);
    
    // Mobile navigation drawer controls
    var navToggle = document.querySelector('.nav-toggle');
    var navDrawer = document.getElementById('nav-drawer');
    var navClose = document.querySelector('.nav-close');
    
    function openDrawer() {
        navDrawer.setAttribute('aria-hidden', 'false');
        navToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDrawer() {
        navDrawer.setAttribute('aria-hidden', 'true');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
    
    if (navToggle) navToggle.addEventListener('click', openDrawer);
    if (navClose) navClose.addEventListener('click', closeDrawer);
    if (navDrawer) {
        navDrawer.addEventListener('click', function(e) {
            if (e.target === navDrawer) closeDrawer();
        });
    }
    
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });
}

/**
 * Setup scroll-triggered animations using Intersection Observer
 */
function setupScrollAnimations() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                
                // Trigger counter animations for counter elements inside this entry
                var counters = entry.target.querySelectorAll('.counter');
                counters.forEach(function(counter) {
                    animateCounter(counter);
                });
            }
        });
    }, { threshold: 0.15 });
    
    // Observe all animated elements
    document.querySelectorAll('[data-animate]').forEach(function(el) {
        observer.observe(el);
    });
}

/**
 * Animate counter numbers from 0 to target value
 * @param {HTMLElement} el - Counter element with data-target attribute
 */
function animateCounter(el) {
    if (el.dataset.animated) return;
    
    el.dataset.animated = '1';
    
    var target = parseInt(el.dataset.target || el.textContent || 0, 10);
    var duration = 2500;
    var start = null;
    var initial = parseInt(el.textContent, 10) || 0;
    
    function step(ts) {
        if (!start) start = ts;
        
        var progress = Math.min((ts - start) / duration, 1);
        el.textContent = Math.floor(initial + (target - initial) * progress);
        
        if (progress < 1) {
            requestAnimationFrame(step);
        } else {
            el.textContent = target + (el.textContent.endsWith('%') ? '%' : '');
        }
    }
    
    requestAnimationFrame(step);
}

/**
 * Initialize typewriter effect for hero headline
 */
function initializeHeroTypewriter() {
    var hero = document.querySelector('.hero-headline');
    
    if (!hero) return;
    
    var fullText = hero.dataset.text || hero.textContent;
    hero.textContent = '';
    
    var charIndex = 0;
    var charDelay = 50;
    var startDelay = 600;
    
    function typeNextCharacter() {
        if (charIndex <= fullText.length) {
            hero.textContent = fullText.slice(0, charIndex);
            charIndex++;
            setTimeout(typeNextCharacter, charDelay);
        }
    }
    
    setTimeout(typeNextCharacter, startDelay);
}
