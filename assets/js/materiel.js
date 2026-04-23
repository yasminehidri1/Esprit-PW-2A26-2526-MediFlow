/**
 * ============================================================
 *  materiel.js — MediFlow Rental
 * ============================================================
 *  Ce fichier gère TOUTE la logique JavaScript du module :
 *
 *  1. Contrôle de saisie (validation des formulaires)
 *  2. Calcul automatique du total en DT (EUR × taux BCT)
 *  3. Options de livraison (toggle visuel)
 *  4. Filtres du catalogue
 *  5. Appels AJAX vers les Controllers PHP (CRUD)
 *  6. Notifications toast
 *  7. Modale Backoffice (create / edit / delete)
 *
 *  Devise    : Dinar Tunisien (DT) — 3 décimales
 *  Taux BCT  : 1 EUR = 3.4052 DT (source : bct.gov.tn)
 *  Auteur    : MediFlow Team
 * ============================================================
 */

 'use strict'; // Mode strict : empêche les erreurs silencieuses JS

 /* ============================================================
    §1 — CONFIGURATION GLOBALE
    ============================================================ */
 
 /**
  * BASE_URL : racine du projet dans htdocs XAMPP.
  * Toutes les URLs absolues sont construites à partir d'ici.
  * ⚠️ À adapter si le dossier htdocs porte un autre nom.
  */
 const BASE_URL = '/projet web';
 
 /**
  * Taux de change officiel Banque Centrale de Tunisie
  * Source : https://www.bct.gov.tn — mis à jour manuellement
  */
 const EUR_TO_DT = 3.4052;
 
 /**
  * API : chemins vers les controllers PHP
  * Ces URLs sont appelées en AJAX (fetch) pour le CRUD
  */
 const API = {
   equipement:  `${BASE_URL}/controller/EquipementController.php`,
   reservation: `${BASE_URL}/controller/ReservationController.php`,
 };
 
 /* ============================================================
    §2 — UTILITAIRES GÉNÉRAUX
    ============================================================ */
 
 /**
  * formatDT(montant)
  * -----------------
  * Convertit un nombre en chaîne formatée en Dinar Tunisien.
  * Le dinar s'écrit avec 3 décimales (millimes).
  *
  * Exemple : formatDT(153.234) → "153,234 DT"
  *
  * @param {number} montant - Montant en DT
  * @returns {string}
  */
 function formatDT(montant) {
   return parseFloat(montant).toLocaleString('fr-TN', {
     minimumFractionDigits: 3,
     maximumFractionDigits: 3,
   }) + ' DT';
 }
 
 /**
  * daysBetween(start, end)
  * -----------------------
  * Calcule le nombre de jours entre deux dates ISO (YYYY-MM-DD).
  * Retourne 0 si les dates sont invalides ou dans le mauvais ordre.
  *
  * @param {string} start - Date de début
  * @param {string} end   - Date de fin
  * @returns {number}
  */
 function daysBetween(start, end) {
   if (!start || !end) return 0;
   const diff = new Date(end) - new Date(start);
   return Math.ceil(diff / 86400000); // 86400000 ms = 1 jour
 }
 
 /**
  * escHtml(str)
  * ------------
  * Échappe les caractères spéciaux HTML pour éviter les injections XSS.
  * Utilisé avant d'insérer du texte dynamique dans le DOM.
  *
  * @param {string} str
  * @returns {string}
  */
 function escHtml(str) {
   if (!str) return '';
   return String(str)
     .replace(/&/g, '&amp;')
     .replace(/</g, '&lt;')
     .replace(/>/g, '&gt;')
     .replace(/"/g, '&quot;')
     .replace(/'/g, '&#39;');
 }
 
 /* ============================================================
    §3 — SYSTÈME DE NOTIFICATIONS (Toast)
    ============================================================
    Un "toast" est un message temporaire qui apparaît en haut
    à droite de l'écran pendant 3,5 secondes puis disparaît.
    Il remplace les alert() natifs du navigateur.
    ============================================================ */
 
 /**
  * showToast(message, type)
  * ------------------------
  * Affiche une notification temporaire stylisée.
  *
  * @param {string} message - Texte à afficher
  * @param {'success'|'error'|'info'} type - Couleur du toast
  *
  * Fonctionnement :
  *   1. On cherche le container .toast-container dans le DOM
  *   2. On crée un élément <div> avec la classe toast + type
  *   3. Après 3,5s → on le fait disparaître en opacity 0
  *   4. Après 0,3s supplémentaires → on le supprime du DOM
  */
 function showToast(message, type = 'info') {
   // Créer le container s'il n'existe pas encore
   let container = document.querySelector('.toast-container');
   if (!container) {
     container = document.createElement('div');
     container.className = 'toast-container';
     document.body.appendChild(container);
   }
 
   // Icône selon le type
   const icons = {
     success: 'check_circle',
     error:   'error',
     info:    'info'
   };
 
   // Créer l'élément toast
   const toast = document.createElement('div');
   toast.className = `toast ${type}`;
   toast.innerHTML = `
     <span class="material-symbols-outlined">${icons[type] || 'info'}</span>
     <span>${message}</span>
   `;
 
   container.appendChild(toast);
 
   // Disparition automatique après 3,5 secondes
   setTimeout(() => {
     toast.style.opacity   = '0';
     toast.style.transform = 'translateX(20px)';
     toast.style.transition = 'all .3s ease';
     setTimeout(() => toast.remove(), 300);
   }, 3500);
 }
 
 /* ============================================================
    §4 — CONTRÔLE DE SAISIE (VALIDATION)
    ============================================================
    La validation côté client (JS) est la PREMIÈRE ligne de
    défense. Elle offre un retour immédiat à l'utilisateur
    SANS rechargement de page.
 
    ⚠️ Important : la validation JS ne remplace PAS la
    validation côté serveur (PHP). Les deux sont nécessaires :
    - JS  → confort utilisateur (UX)
    - PHP → sécurité réelle (ne peut pas être contournée)
    ============================================================ */
 
 /**
  * RÈGLES DE VALIDATION
  * --------------------
  * Centralisées ici pour faciliter la maintenance.
  * Chaque règle a un test (regex ou fonction) et un message.
  */
 const REGLES = {
 
   // Prénom : lettres, espaces, tirets, accents — min 2 caractères
   prenom: {
     test: (v) => /^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(v.trim()),
     msg:  'Le prénom doit contenir au moins 2 lettres (pas de chiffres).'
   },
 
   // Nom : même règle que prénom
   nom: {
     test: (v) => /^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(v.trim()),
     msg:  'Le nom doit contenir au moins 2 lettres (pas de chiffres).'
   },
 
   // Téléphone tunisien : commence par 2,3,4,5,7,9 — exactement 8 chiffres
   telephone: {
     test: (v) => {
       const clean = v.replace(/\s/g, ''); // supprimer les espaces
       return /^[2345789]\d{7}$/.test(clean);
     },
     msg: 'Numéro invalide. Format tunisien : 8 chiffres (ex: 20123456).'
   },
 
   // Date : doit être une date valide et non vide
   date: {
     test: (v) => v !== '' && !isNaN(new Date(v).getTime()),
     msg:  'Veuillez sélectionner une date valide.'
   },
 
   // Nom locataire (backoffice) : min 3 caractères
   locataire: {
     test: (v) => v.trim().length >= 3,
     msg:  'Le nom du locataire doit contenir au moins 3 caractères.'
   },
 
   // Ville (optionnelle mais si remplie : lettres et espaces)
   ville: {
     test: (v) => v.trim() === '' || /^[a-zA-ZÀ-ÿ\s'\-]{2,50}$/.test(v.trim()),
     msg:  'Ville invalide (lettres uniquement).'
   },
 };
 
 /**
  * afficherErreurChamp(inputId, message)
  * --------------------------------------
  * Affiche un message d'erreur rouge sous un champ de saisie
  * ET ajoute une bordure rouge au champ.
  *
  * Fonctionnement :
  *   1. On sélectionne l'élément input par son id
  *   2. On lui ajoute une bordure rouge (style inline)
  *   3. On crée un <small> avec le message d'erreur
  *   4. On l'insère juste après le champ dans le DOM
  *
  * @param {string} inputId - id HTML de l'input
  * @param {string} message - Message d'erreur à afficher
  */
 function afficherErreurChamp(inputId, message) {
   const input = document.getElementById(inputId);
   if (!input) return;
 
   // Bordure rouge sur le champ
   input.style.borderColor = '#dc2626';
   input.style.boxShadow   = '0 0 0 3px rgba(220,38,38,0.10)';
 
   // Supprimer l'ancien message s'il existe
   const ancien = input.parentElement.querySelector('.msg-erreur');
   if (ancien) ancien.remove();
 
   // Créer et insérer le message d'erreur
   const span = document.createElement('small');
   span.className   = 'msg-erreur';
   span.textContent = '⚠ ' + message;
   span.style.cssText = `
     color: #dc2626;
     font-size: 11px;
     font-weight: 600;
     display: block;
     margin-top: 4px;
   `;
 
   // Insérer après l'input dans le DOM
   input.insertAdjacentElement('afterend', span);
 }
 
 /**
  * effacerErreurChamp(inputId)
  * ---------------------------
  * Supprime le message d'erreur et remet le style normal
  * quand l'utilisateur commence à corriger le champ.
  *
  * @param {string} inputId - id HTML de l'input
  */
 function effacerErreurChamp(inputId) {
   const input = document.getElementById(inputId);
   if (!input) return;
 
   // Remettre le style normal
   input.style.borderColor = '';
   input.style.boxShadow   = '';
 
   // Supprimer le message d'erreur
   const msg = input.parentElement.querySelector('.msg-erreur');
   if (msg) msg.remove();
 }
 
 /**
  * effacerToutesLesErreurs()
  * -------------------------
  * Nettoie tous les messages d'erreur du formulaire en cours.
  * Appelé au début de chaque validation pour repartir de zéro.
  */
 function effacerToutesLesErreurs() {
   // Supprimer tous les messages d'erreur
   document.querySelectorAll('.msg-erreur').forEach(el => el.remove());
 
   // Remettre les bordures normales sur tous les inputs
   document.querySelectorAll('.form-input, .modal-input').forEach(input => {
     input.style.borderColor = '';
     input.style.boxShadow   = '';
   });
 }
 
 /**
  * validerFormulaireReservation()
  * --------------------------------
  * Valide le formulaire de réservation (reservation.php).
  *
  * Champs vérifiés :
  *   - Prénom    : lettres uniquement, min 2 caractères
  *   - Nom       : lettres uniquement, min 2 caractères
  *   - Téléphone : format tunisien 8 chiffres (optionnel mais si rempli → validé)
  *   - Date début : obligatoire, doit être >= aujourd'hui
  *   - Date fin   : obligatoire, doit être > date début
  *
  * @returns {boolean} true si tout est valide, false sinon
  */
 function validerFormulaireReservation() {
   effacerToutesLesErreurs(); // Nettoyer les anciens messages
 
   let valide = true; // On suppose que le formulaire est valide
 
   const prenom    = document.getElementById('firstname')?.value || '';
   const nom       = document.getElementById('lastname')?.value  || '';
   const telephone = document.getElementById('phone')?.value     || '';
   const dateDebut = document.getElementById('date-start')?.value || '';
   const dateFin   = document.getElementById('date-end')?.value   || '';
   const today     = new Date().toISOString().split('T')[0]; // Date du jour YYYY-MM-DD
 
   // --- Validation du PRÉNOM ---
   if (prenom.trim() === '') {
     afficherErreurChamp('firstname', 'Le prénom est obligatoire.');
     valide = false;
   } else if (!REGLES.prenom.test(prenom)) {
     afficherErreurChamp('firstname', REGLES.prenom.msg);
     valide = false;
   }
 
   // --- Validation du NOM ---
   if (nom.trim() === '') {
     afficherErreurChamp('lastname', 'Le nom est obligatoire.');
     valide = false;
   } else if (!REGLES.nom.test(nom)) {
     afficherErreurChamp('lastname', REGLES.nom.msg);
     valide = false;
   }
 
   // --- Validation du TÉLÉPHONE (optionnel) ---
   // Si l'utilisateur a rempli le champ → on vérifie le format
   // S'il est vide → on laisse passer (champ optionnel)
   if (telephone.trim() !== '' && !REGLES.telephone.test(telephone)) {
     afficherErreurChamp('phone', REGLES.telephone.msg);
     valide = false;
   }
 
   // --- Validation de la DATE DE DÉBUT ---
   if (dateDebut === '') {
     afficherErreurChamp('date-start', 'La date de début est obligatoire.');
     valide = false;
   } else if (dateDebut < today) {
     // La date ne peut pas être dans le passé
     afficherErreurChamp('date-start', 'La date de début ne peut pas être dans le passé.');
     valide = false;
   }
 
   // --- Validation de la DATE DE FIN ---
   if (dateFin === '') {
     afficherErreurChamp('date-end', 'La date de fin est obligatoire.');
     valide = false;
   } else if (dateDebut !== '' && dateFin <= dateDebut) {
     // La date de fin doit être STRICTEMENT après la date de début
     afficherErreurChamp('date-end', 'La date de fin doit être après la date de début.');
     valide = false;
   }
 
   // Si invalide → toast global + retour false
   if (!valide) {
     showToast('Veuillez corriger les erreurs dans le formulaire.', 'error');
   }
 
   return valide;
 }
 
 /**
  * validerFormulaireBackoffice()
  * ------------------------------
  * Valide la modale du backoffice (nouvelle réservation / modification).
  *
  * Champs vérifiés :
  *   - Équipement : doit être sélectionné
  *   - Nom locataire : min 3 caractères
  *   - Ville : optionnelle mais si remplie → lettres uniquement
  *   - Téléphone : optionnel, format tunisien si rempli
  *   - Date début : obligatoire
  *   - Date fin : optionnelle, mais si remplie → doit être > début
  *
  * @returns {boolean}
  */
 function validerFormulaireBackoffice() {
   effacerToutesLesErreurs();
 
   let valide = true;
 
   const equipId   = document.getElementById('edit-equipement')?.value || '';
   const nomLoc    = document.getElementById('edit-nom')?.value   || '';
   const ville     = document.getElementById('edit-ville')?.value || '';
   const tel       = document.getElementById('edit-tel')?.value   || '';
   const dateDebut = document.getElementById('edit-debut')?.value || '';
   const dateFin   = document.getElementById('edit-fin')?.value   || '';
 
   // --- Équipement sélectionné ---
   if (!equipId || equipId === '') {
     showToast('Veuillez sélectionner un équipement.', 'error');
     valide = false;
   }
 
   // --- Nom du locataire ---
   if (nomLoc.trim() === '') {
     afficherErreurChamp('edit-nom', 'Le nom du locataire est obligatoire.');
     valide = false;
   } else if (!REGLES.locataire.test(nomLoc)) {
     afficherErreurChamp('edit-nom', REGLES.locataire.msg);
     valide = false;
   }
 
   // --- Ville (optionnelle) ---
   if (ville.trim() !== '' && !REGLES.ville.test(ville)) {
     afficherErreurChamp('edit-ville', REGLES.ville.msg);
     valide = false;
   }
 
   // --- Téléphone (optionnel) ---
   if (tel.trim() !== '' && !REGLES.telephone.test(tel)) {
     afficherErreurChamp('edit-tel', REGLES.telephone.msg);
     valide = false;
   }
 
   // --- Date de début ---
   if (dateDebut === '') {
     afficherErreurChamp('edit-debut', 'La date de début est obligatoire.');
     valide = false;
   }
 
   // --- Date de fin (optionnelle) ---
   if (dateFin !== '' && dateDebut !== '' && dateFin <= dateDebut) {
     afficherErreurChamp('edit-fin', 'La date de fin doit être après la date de début.');
     valide = false;
   }
 
   if (!valide) {
     showToast('Veuillez corriger les erreurs.', 'error');
   }
 
   return valide;
 }
 
 /* ============================================================
    §5 — FETCH HELPERS (Appels AJAX vers PHP)
    ============================================================
    fetch() est une API JavaScript native pour faire des
    requêtes HTTP sans recharger la page.
 
    Chaque fonction correspond à une méthode HTTP :
    - GET    → Lire des données
    - POST   → Créer une nouvelle entrée
    - PUT    → Modifier une entrée existante
    - DELETE → Supprimer une entrée
    ============================================================ */
 
 /**
  * apiGet(entity, id?)
  * Envoie une requête GET au controller.
  * Sans id → récupère toutes les entrées.
  * Avec id → récupère une seule entrée.
  */
 async function apiGet(entity, id = null) {
   const url = id ? `${API[entity]}?id=${id}` : API[entity];
   const res = await fetch(url);
   if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);
   return res.json();
 }
 
 /**
  * apiPost(entity, data)
  * Envoie une requête POST avec les données en JSON.
  * Utilisé pour CRÉER une nouvelle réservation ou un équipement.
  */
 async function apiPost(entity, data) {
   const res = await fetch(API[entity], {
     method:  'POST',
     headers: { 'Content-Type': 'application/json' },
     body:    JSON.stringify(data),
   });
   return res.json();
 }
 
 /**
  * apiPut(entity, id, data)
  * Envoie une requête PUT pour MODIFIER une entrée existante.
  * L'id est passé dans l'URL (?id=X).
  */
 async function apiPut(entity, id, data) {
   const res = await fetch(`${API[entity]}?id=${id}`, {
     method:  'PUT',
     headers: { 'Content-Type': 'application/json' },
     body:    JSON.stringify(data),
   });
   return res.json();
 }
 
 /**
  * apiDelete(entity, id)
  * Envoie une requête DELETE pour SUPPRIMER une entrée.
  */
 async function apiDelete(entity, id) {
   const res = await fetch(`${API[entity]}?id=${id}`, { method: 'DELETE' });
   return res.json();
 }
 
 /* ============================================================
    §6 — MODULE CATALOGUE (catalogue.php)
    ============================================================ */
 
 /**
  * initFilterTabs()
  * ----------------
  * Initialise les boutons de filtrage (Tout / Mobilité / Respiratoire).
  *
  * Fonctionnement :
  *   1. Au clic sur un bouton → on retire .active de tous les boutons
  *   2. On ajoute .active sur le bouton cliqué
  *   3. On parcourt toutes les cartes .product-card
  *   4. On affiche ou masque selon le data-category de la carte
  */
 function initFilterTabs() {
   const tabs  = document.querySelectorAll('.filter-bar button');
   const cards = document.querySelectorAll('.product-card');
 
   tabs.forEach(tab => {
     tab.addEventListener('click', () => {
       // Désactiver tous les onglets
       tabs.forEach(t => t.classList.remove('active'));
       // Activer l'onglet cliqué
       tab.classList.add('active');
 
       const filter = tab.dataset.filter; // "all", "mobilite", "respiratoire"
 
       cards.forEach(card => {
         const cat = (card.dataset.category || '').toLowerCase();
         // Afficher si "all" OU si la catégorie correspond au filtre
         card.style.display =
           (filter === 'all' || cat.includes(filter.toLowerCase())) ? '' : 'none';
       });
     });
   });
 }
 
 /**
  * showEquipHistory(id, nom)
  * -------------------------
  * Affiche le nombre de locations passées pour un équipement.
  * Appelé au clic sur le bouton historique (horloge).
  */
 async function showEquipHistory(id, nom) {
   try {
     const reservations = await apiGet('reservation');
     const related = reservations.filter(r => String(r.equipement_id) === String(id));
     showToast(
       related.length === 0
         ? `Aucune location trouvée pour "${nom}"`
         : `${related.length} location(s) enregistrée(s) pour "${nom}"`,
       'info'
     );
   } catch (e) {
     showToast('Impossible de charger l\'historique.', 'error');
   }
 }
 
 /* ============================================================
    §7 — MODULE RÉSERVATION (reservation.php)
    ============================================================ */
 
 /**
  * updateTotal()
  * -------------
  * Recalcule et affiche le total à payer en DT
  * à chaque changement de date.
  *
  * Formule :
  *   total_DT = nb_jours × prix_EUR × taux_EUR_DT
  *
  * Les données viennent des attributs data-* du div #daily-rate
  * qui est rempli par PHP au chargement de la page.
  */
 function updateTotal() {
   const startEl = document.getElementById('date-start');
   const endEl   = document.getElementById('date-end');
   const totalEl = document.getElementById('total-amount');
   const durEl   = document.getElementById('duration-row');
   if (!startEl || !endEl || !totalEl) return;
 
   // Effacer l'erreur de date quand l'utilisateur modifie
   effacerErreurChamp('date-start');
   effacerErreurChamp('date-end');
 
   const days = daysBetween(startEl.value, endEl.value);
 
   if (!startEl.value || !endEl.value || days <= 0) {
     totalEl.textContent = '—';
     if (durEl) {
       durEl.querySelector('.lbl').textContent = 'Location';
       durEl.querySelector('.val').textContent = '—';
     }
     return;
   }
 
   // Lire le prix EUR et le taux depuis les attributs data- (mis par PHP)
   const rateEl  = document.getElementById('daily-rate');
   const prixEur = parseFloat(rateEl?.dataset?.rateEur || rateEl?.dataset?.rate || 0);
   const taux    = parseFloat(rateEl?.dataset?.taux || EUR_TO_DT);
   const prixDT  = prixEur * taux;    // Prix journalier en DT
   const totalDT = days * prixDT;     // Total en DT
 
   // Afficher le total formaté
   totalEl.textContent = formatDT(totalDT);
 
   if (durEl) {
     durEl.querySelector('.lbl').textContent = `Location (${days} jour${days > 1 ? 's' : ''})`;
     durEl.querySelector('.val').textContent  = formatDT(totalDT);
   }
 }
 
 /**
  * initDeliveryOptions()
  * ----------------------
  * Gère l'affichage visuel des options de livraison.
  * Quand on clique sur une option → elle devient "sélectionnée"
  * et l'autre devient "non sélectionnée".
  */
 function initDeliveryOptions() {
   document.querySelectorAll('.delivery-opt').forEach(opt => {
     opt.addEventListener('click', () => {
       // Désélectionner toutes les options
       document.querySelectorAll('.delivery-opt').forEach(o => {
         o.classList.remove('selected');
         o.classList.add('unselected');
         const t = o.querySelector('.opt-title');
         const i = o.querySelector('.opt-icon');
         if (t) { t.classList.remove('blue'); t.classList.add('gray'); }
         if (i) {
           i.textContent = 'radio_button_unchecked';
           i.classList.remove('filled');
           i.classList.add('unfilled');
         }
       });
 
       // Sélectionner l'option cliquée
       opt.classList.add('selected');
       opt.classList.remove('unselected');
       const t = opt.querySelector('.opt-title');
       const i = opt.querySelector('.opt-icon');
       if (t) { t.classList.add('blue'); t.classList.remove('gray'); }
       if (i) {
         i.textContent = 'check_circle';
         i.classList.add('filled');
         i.classList.remove('unfilled');
         i.style.fontVariationSettings = "'FILL' 1";
       }
     });
   });
 }
 
 /**
  * submitReservation()
  * --------------------
  * Soumet le formulaire de réservation après validation.
  *
  * Étapes :
  *   1. Appel de validerFormulaireReservation() → si invalide : STOP
  *   2. Désactiver le bouton (éviter double soumission)
  *   3. Envoyer les données en JSON via apiPost()
  *   4. Succès → toast + redirection vers catalogue
  *   5. Erreur → toast + réactiver le bouton
  */
 async function submitReservation() {
   // ÉTAPE 1 : Valider le formulaire AVANT d'envoyer
   if (!validerFormulaireReservation()) {
     return; // Arrêter si le formulaire est invalide
   }
 
   // Collecter les données du formulaire
   const equipId   = document.getElementById('equipement_id')?.value;
   const firstname = document.getElementById('firstname')?.value.trim();
   const lastname  = document.getElementById('lastname')?.value.trim();
   const phone     = document.getElementById('phone')?.value.trim();
   const dateStart = document.getElementById('date-start')?.value;
   const dateEnd   = document.getElementById('date-end')?.value;
 
   // ÉTAPE 2 : Désactiver le bouton (anti double-clic)
   const btn = document.getElementById('btn-confirm');
   if (btn) {
     btn.disabled     = true;
     btn.textContent  = 'Envoi en cours...';
   }
 
   // ÉTAPE 3 : Envoyer au controller PHP via AJAX
   try {
     const json = await apiPost('reservation', {
       equipement_id:   equipId,
       locataire_nom:   `${firstname} ${lastname}`,
       locataire_ville: '',
       date_debut:      dateStart,
       date_fin:        dateEnd,
       telephone:       phone,
       statut:          'en_cours',
     });
 
     // ÉTAPE 4 : Succès
     if (json.success) {
       showToast('✅ Réservation confirmée avec succès !', 'success');
       // Redirection vers le catalogue après 1,8 secondes
       setTimeout(() => {
         window.location.href = `${BASE_URL}/view/Frontoffice/catalogue.php`;
       }, 1800);
 
     } else {
       // ÉTAPE 5 : Erreur retournée par le serveur
       showToast('Erreur serveur : ' + (json.message || 'Inconnue'), 'error');
       if (btn) { btn.disabled = false; btn.textContent = 'Confirmer la réservation'; }
     }
 
   } catch (e) {
     // Erreur réseau (pas de connexion, serveur arrêté, etc.)
     showToast('Erreur réseau. Vérifiez que XAMPP est actif.', 'error');
     if (btn) { btn.disabled = false; btn.textContent = 'Confirmer la réservation'; }
   }
 }
 
 /* ============================================================
    §8 — MODULE BACKOFFICE (historique-location.php)
    ============================================================ */
 
 /**
  * openCreateModal()
  * -----------------
  * Ouvre la modale en mode CRÉATION (champs vides).
  */
 function openCreateModal() {
   document.getElementById('modal-title').textContent = 'Nouvelle Réservation';
   document.getElementById('edit-id').value           = ''; // ID vide = création
 
   // Vider tous les champs
   ['edit-nom', 'edit-ville', 'edit-tel', 'edit-debut', 'edit-fin'].forEach(id => {
     const el = document.getElementById(id);
     if (el) el.value = '';
   });
 
   // Statut par défaut
   const s = document.getElementById('edit-statut');
   if (s) s.value = 'en_cours';
 
   effacerToutesLesErreurs();
   document.getElementById('modal-overlay').classList.add('open');
 }
 
 /**
  * openEditModal(r)
  * ----------------
  * Ouvre la modale en mode MODIFICATION pré-remplie.
  * @param {Object} r - Objet réservation (depuis PHP json_encode)
  */
 function openEditModal(r) {
   document.getElementById('modal-title').textContent    = 'Modifier la Réservation';
   document.getElementById('edit-id').value              = r.id; // ID présent = modification
   document.getElementById('edit-equipement').value      = r.equipement_id;
   document.getElementById('edit-nom').value             = r.locataire_nom   || '';
   document.getElementById('edit-ville').value           = r.locataire_ville || '';
   document.getElementById('edit-tel').value             = r.telephone       || '';
   document.getElementById('edit-debut').value           = r.date_debut      || '';
   document.getElementById('edit-fin').value             = r.date_fin        || '';
   document.getElementById('edit-statut').value          = r.statut          || 'en_cours';
 
   effacerToutesLesErreurs();
   document.getElementById('modal-overlay').classList.add('open');
 }
 
 /**
  * closeModal()
  * ------------
  * Ferme la modale et efface les messages d'erreur.
  */
 function closeModal() {
   effacerToutesLesErreurs();
   document.getElementById('modal-overlay')?.classList.remove('open');
 }
 
 /**
  * saveReservation()
  * -----------------
  * Enregistre une réservation (create ou update selon si edit-id est rempli).
  *
  * Logique :
  *   - Si edit-id est vide  → POST (création)
  *   - Si edit-id a un ID   → PUT  (modification)
  */
 async function saveReservation() {
   // Valider le formulaire backoffice
   if (!validerFormulaireBackoffice()) return;
 
   const id = document.getElementById('edit-id').value;
 
   const data = {
     equipement_id:   document.getElementById('edit-equipement').value,
     locataire_nom:   document.getElementById('edit-nom').value.trim(),
     locataire_ville: document.getElementById('edit-ville').value.trim(),
     telephone:       document.getElementById('edit-tel').value.trim(),
     date_debut:      document.getElementById('edit-debut').value,
     date_fin:        document.getElementById('edit-fin').value || null,
     statut:          document.getElementById('edit-statut').value,
   };
 
   try {
     // id vide → créer | id présent → modifier
     const json = id
       ? await apiPut('reservation', id, data)   // PUT  → modifier
       : await apiPost('reservation', data);      // POST → créer
 
     if (json.success) {
       showToast(
         id ? '✅ Réservation modifiée avec succès !' : '✅ Réservation créée avec succès !',
         'success'
       );
       closeModal();
       // Recharger la page pour afficher les nouvelles données
       setTimeout(() => location.reload(), 1200);
     } else {
       showToast('Erreur serveur : ' + (json.message || 'Inconnue'), 'error');
     }
   } catch (e) {
     showToast('Erreur réseau. Vérifiez XAMPP.', 'error');
   }
 }
 
 /**
  * deleteReservation(id)
  * ----------------------
  * Supprime une réservation après confirmation de l'utilisateur.
  *
  * @param {number} id - ID de la réservation à supprimer
  */
 async function deleteReservation(id) {
   // Demander confirmation avant suppression (action irréversible)
   if (!confirm('⚠️ Supprimer cette réservation ?\n\nCette action est irréversible.')) return;
 
   try {
     const json = await apiDelete('reservation', id);
 
     if (json.success) {
       showToast('Réservation supprimée.', 'success');
       setTimeout(() => location.reload(), 1000);
     } else {
       showToast('Erreur lors de la suppression.', 'error');
     }
   } catch (e) {
     showToast('Erreur réseau.', 'error');
   }
 }
 
 /* ============================================================
    §9 — INIT : Point d'entrée principal
    ============================================================
    DOMContentLoaded se déclenche quand le HTML est entièrement
    chargé et parsé (mais avant les images).
    C'est le bon moment pour attacher les écouteurs d'événements.
    ============================================================ */
 document.addEventListener('DOMContentLoaded', () => {
 
   const path = window.location.pathname; // Ex: "/projet web/view/Frontoffice/catalogue.php"
 
   /* ─────────────────────────────
      Page : catalogue.php
   ───────────────────────────── */
   if (path.includes('catalogue')) {
     initFilterTabs();
 
     // FAB chat support
     document.querySelector('.fab')?.addEventListener('click', () =>
       showToast('Chat support — bientôt disponible.', 'info')
     );
   }
 
   /* ─────────────────────────────
      Page : reservation.php
   ───────────────────────────── */
   if (path.includes('reservation')) {
     // Recalcul du total à chaque changement de date
     document.getElementById('date-start')?.addEventListener('change', updateTotal);
     document.getElementById('date-end')?.addEventListener('change',   updateTotal);
 
     // Effacer l'erreur dès que l'utilisateur commence à taper
     ['firstname', 'lastname', 'phone'].forEach(id => {
       document.getElementById(id)?.addEventListener('input', () => effacerErreurChamp(id));
     });
 
     // Options de livraison
     initDeliveryOptions();
 
     // Bouton confirmer → valider puis soumettre
     document.getElementById('btn-confirm')?.addEventListener('click', submitReservation);
   }
 
   /* ─────────────────────────────
      Page : historique-location.php
   ───────────────────────────── */
   if (path.includes('historique')) {
 
     // Bouton Nouvelle Entrée
     document.getElementById('btn-new-entry')?.addEventListener('click', openCreateModal);
 
     // Boutons fermer la modale
     document.getElementById('btn-cancel')?.addEventListener('click', closeModal);
     document.getElementById('btn-cancel-footer')?.addEventListener('click', closeModal);
 
     // Clic en dehors de la modale → fermer
     document.getElementById('modal-overlay')?.addEventListener('click', e => {
       if (e.target === document.getElementById('modal-overlay')) closeModal();
     });
 
     // Bouton Enregistrer dans la modale
     document.getElementById('btn-save')?.addEventListener('click', saveReservation);
 
     // Effacer les erreurs en temps réel dans la modale
     ['edit-nom', 'edit-ville', 'edit-tel', 'edit-debut', 'edit-fin'].forEach(id => {
       document.getElementById(id)?.addEventListener('input', () => effacerErreurChamp(id));
     });
 
     // Recherche en temps réel dans le tableau
     document.getElementById('search-input')?.addEventListener('input', function () {
       const q = this.value.toLowerCase();
       document.querySelectorAll('#reservations-tbody tr').forEach(row => {
         row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
       });
     });
 
     // Filtre par catégorie
     document.getElementById('btn-filter')?.addEventListener('click', () => {
       const cat = (document.getElementById('filter-cat')?.value || '').toLowerCase();
       document.querySelectorAll('#reservations-tbody tr').forEach(row => {
         const rowCat = (row.dataset.cat || '').toLowerCase();
         row.style.display = (!cat || rowCat.includes(cat)) ? '' : 'none';
       });
       showToast('Filtre appliqué.', 'info');
     });
 
     // Export CSV du tableau
     document.getElementById('btn-export')?.addEventListener('click', () => {
       const rows   = [...document.querySelectorAll('#reservations-tbody tr:not([style*="none"])')];
       const header = 'Référence;Équipement;Catégorie;Locataire;Ville;Début;Fin;Statut\n';
       const csv    = rows.map(row => {
         const c = [...row.querySelectorAll('td')];
         return [
           c[0]?.textContent.trim(),
           c[1]?.querySelector('.eq-name')?.textContent.trim(),
           c[1]?.querySelector('.eq-cat')?.textContent.trim(),
           c[2]?.querySelector('.locataire-name')?.textContent.trim(),
           c[2]?.querySelector('.locataire-loc')?.textContent.trim(),
           c[3]?.querySelector('.date-start')?.textContent.trim(),
           c[3]?.querySelector('.date-end')?.textContent.trim(),
           c[4]?.textContent.trim(),
         ].join(';');
       }).join('\n');
 
       const blob = new Blob(['\uFEFF' + header + csv], { type: 'text/csv;charset=utf-8;' });
       const a    = Object.assign(document.createElement('a'), {
         href:     URL.createObjectURL(blob),
         download: `mediflow_rapport_${new Date().toISOString().slice(0,10)}.csv`
       });
       a.click();
       URL.revokeObjectURL(a.href);
       showToast('Rapport CSV exporté avec succès !', 'success');
     });
   }
 });