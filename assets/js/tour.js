/**
 * MediFlow Onboarding Tour (Intro.js)
 * 
 * French guided tour for new users
 * Shows on first login to dashboard
 */

const mediflowTour = {
    // Tour configuration
    intro: null,
    
    // Initialize tour
    init: function() {
        this.intro = introJs();
        this.intro.setOptions({
            steps: this.getTourSteps(),
            showProgress: true,
            showStepNumbers: true,
            disableInteraction: false,
            highlightClass: 'intro-highlight',
            scrollToElement: true,        // Let Intro.js handle scrolling natively
            scrollPadding: 80,
            overlayOpacity: 0.75,
            nextLabel: '→ Suivant',
            prevLabel: '← Précédent',
            skipLabel: '×',
            doneLabel: '✓ Terminé',
            tooltipPosition: 'auto',
            positionPrecedence: ['bottom', 'top', 'right', 'left'],
            exitOnEsc: true,
            exitOnOverlayClick: false,
            showBullets: true
        });

        // On step change — animate tooltip
        this.intro.onchange(function() {
            const tooltip = document.querySelector('.introjs-tooltip');
            if (tooltip) {
                tooltip.style.animation = 'none';
                // Force reflow so animation restarts
                void tooltip.offsetWidth;
                tooltip.style.animation = 'tooltip-slide-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }
        });

        // Scroll to element BEFORE Intro.js positions the tooltip
        this.intro.onbeforechange(function(targetEl) {
            if (targetEl && targetEl !== document.body && targetEl.tagName) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        this.intro.oncomplete(() => {
            this.markTourAsCompleted();
        });

        this.intro.onexit(() => {
            this.markTourAsCompleted();
        });
    },

    // Get tour steps (French)
    getTourSteps: function() {
        const currentPage = this.getCurrentPage();

        // Common intro
        const commonSteps = [
            {
                element: document.body,
                title: '✨ Bienvenue dans MediFlow',
                intro: 'Découvrez comment gérer facilement vos réservations d\'équipements médicaux. Suivez ce guide pour maîtriser la plateforme.',
                position: 'center'
            },
            {
                element: document.querySelector('aside'),
                title: '🧭 Navigation Principale',
                intro: 'Explorez toutes les sections depuis ce menu. Tableau de bord, catalogue, réservations, et bien plus encore—tout est accessible ici.',
                position: 'right'
            }
        ];

        // Page-specific steps — always resolve elements at tour start
        let pageSteps = [];

        if (currentPage === 'dashboard') {
            const statsEl   = document.querySelector('.dashboard-stats');
            const tableEl   = document.querySelector('.reservations-table');
            const actionsEl = document.querySelector('.quick-actions');

            if (statsEl) {
                pageSteps.push({
                    element: statsEl,
                    title: '📊 Aperçu de Vos Statistiques',
                    intro: 'Voyez en un coup d\'œil le nombre de réservations actives, votre historique complet, et les équipements disponibles.',
                    position: 'bottom'
                });
            }
            if (tableEl) {
                pageSteps.push({
                    element: tableEl,
                    title: '📋 Vos Réservations Récentes',
                    intro: 'Consultez vos dernières locations avec leur statut, les dates et les détails complets.',
                    position: 'top'
                });
            }
            if (actionsEl) {
                pageSteps.push({
                    element: actionsEl,
                    title: '⚡ Actions Rapides',
                    intro: 'Accélérez votre workflow : parcourez le catalogue ou consultez votre historique de location en un clic.',
                    position: 'top'
                });
            }
        } else if (currentPage === 'catalogue') {
            const gridEl   = document.querySelector('.equipment-grid');
            const filterEl = document.querySelector('.filter-section');
            if (gridEl)   pageSteps.push({ element: gridEl,   title: '🔍 Explorez Notre Catalogue', intro: 'Découvrez tous les équipements disponibles avec images, tarifs journaliers, et disponibilité en temps réel.', position: 'bottom' });
            if (filterEl) pageSteps.push({ element: filterEl, title: '🎯 Filtres Intelligents',      intro: 'Trouvez exactement ce que vous cherchez grâce à nos filtres. Catégorie, prix, disponibilité—affinez votre recherche facilement.', position: 'bottom' });
        } else if (currentPage === 'reservations') {
            const tableEl2   = document.querySelector('.reservations-table');
            const actionsEl2 = document.querySelector('.reservation-actions');
            if (tableEl2)   pageSteps.push({ element: tableEl2,   title: '📅 Historique Complet',   intro: 'Accédez à toutes vos réservations passées et présentes. Dates, tarifs, statuts—tout dans une vue claire et organisée.', position: 'bottom' });
            if (actionsEl2) pageSteps.push({ element: actionsEl2, title: '⚙️ Gérez Vos Locations',  intro: 'Consultez les détails, modifiez les dates ou annulez selon vos besoins. Vous avez le contrôle total.', position: 'top' });
        } else if (currentPage === 'profile') {
            const formEl = document.querySelector('.profile-form');
            const secEl  = document.querySelector('.security-section');
            if (formEl) pageSteps.push({ element: formEl, title: '👤 Votre Profil Personnel', intro: 'Mettez à jour vos informations : nom, email, téléphone, adresse. Gardez vos données à jour.', position: 'bottom' });
            if (secEl)  pageSteps.push({ element: secEl,  title: '🔐 Sécurité de Compte',     intro: 'Gérez votre mot de passe et paramètres de sécurité. Votre compte est entre de bonnes mains.', position: 'top' });
        }

        // Final step
        const finalSteps = [
            {
                element: document.body,
                title: '🎉 Vous êtes Prêt!',
                intro: 'La visite est complète. Explorez librement et n\'hésitez pas à relancer ce guide à tout moment depuis le bouton "Relancer la visite guidée".',
                position: 'center'
            }
        ];

        // Filter out any null/undefined elements that weren't found in the DOM
        const allSteps = [...commonSteps, ...pageSteps, ...finalSteps];
        return allSteps.filter(step => step.element !== null && step.element !== undefined);
    },

    // Detect current page
    getCurrentPage: function() {
        const path = window.location.pathname.replace(/\/+$/, ''); // strip trailing slash

        if (/\/catalogue/.test(path))          return 'catalogue';
        if (/\/mes-reservations/.test(path))   return 'reservations';
        if (/\/profile/.test(path))            return 'profile';
        if (/\/dashboard/.test(path))          return 'dashboard';

        // Root fallback — also treat as dashboard
        return 'dashboard';
    },

    // Start the tour
    start: function() {
        this.init();
        this.intro.start();
    },

    // Restart the tour (reset DB flag + restart)
    restart: function() {
        if (this.intro) {
            try { this.intro.exit(true); } catch(e) {}
        }
        // Small delay to let exit animation finish
        setTimeout(() => { this.start(); }, 300);
    },

    // Mark tour as completed
    markTourAsCompleted: function() {
        fetch('/integration/api/complete-onboarding', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            console.log('Tour marked as completed:', data);
            // Show restart button if it was hidden
            const restartBtn = document.querySelector('.restart-tour-btn');
            if (restartBtn) restartBtn.style.display = 'inline-flex';
        })
        .catch(err => console.error('Onboarding API error:', err));
    }
};

// Auto-start tour if needed
document.addEventListener('DOMContentLoaded', function() {
    const shouldStartTour = document.body.dataset.showTour === 'true';
    
    if (shouldStartTour) {
        // Delay slightly to let the page fully render
        setTimeout(() => {
            mediflowTour.start();
        }, 800);
    }
});
