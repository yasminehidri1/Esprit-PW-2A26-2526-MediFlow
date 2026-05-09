<?php
/**
 * MediFlow Application Router
 *
 * Core application class that handles HTTP request routing.
 * Base path: /integration/
 *
 * @package MediFlow\Core
 * @version 2.0.0
 */

namespace Core;

class App
{
    /**
     * Run the application and route the request
     *
     * @return void
     */
    public function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Remove /integration prefix if present
        $path = preg_replace('#^/integration#', '', $path);
        $path = $path ?: '/';

        // â”€â”€ Auth â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/login(?:/|$)#', $path)) {
            (new \Controllers\AuthController())->login();
            return;
        }

        if (preg_match('#^/register(?:/|$)#', $path)) {
            (new \Controllers\AuthController())->register();
            return;
        }

        if (preg_match('#^/terms(?:/|$)#', $path)) {
            (new \Controllers\LandingController())->terms();
            return;
        }

        // â”€â”€ Logout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/logout(?:/|$)#', $path)) {
            session_start();
            session_destroy();
            header('Location: /integration/login');
            exit;
        }

        // â”€â”€ User Dashboard / Profile â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/dashboard/api/users(?:/|$)#', $path)) {
            (new \Controllers\DashboardController())->getUsers();
            return;
        }

        if (preg_match('#^/dashboard/api/stats(?:/|$)#', $path)) {
            (new \Controllers\DashboardController())->getStats();
            return;
        }

        if (preg_match('#^/profile/update(?:/|$)#', $path)) {
            (new \Controllers\DashboardController())->updateProfile();
            return;
        }

        if (preg_match('#^/profile(?:/|$)#', $path)) {
            (new \Controllers\DashboardController())->profile();
            return;
        }

        if (preg_match('#^/dashboard(?:/|$)#', $path)) {
            (new \Controllers\DashboardController())->index();
            return;
        }

        // â”€â”€ Admin user management â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/admin(?:/|$)#', $path)) {
            (new \Controllers\AdminController())->handle();
            return;
        }

        // â”€â”€ Equipment rental module â€” APIs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/equipment/api/reservations(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->reservationApi();
            return;
        }

        if (preg_match('#^/equipment/api/equipements(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->equipementApi();
            return;
        }

        if (preg_match('#^/equipment/api/analyze-image(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->analyzeImage();
            return;
        }

        // âœ… NOUVEAU â€” VÃ©rification disponibilitÃ© Ã©quipement en temps rÃ©el
        // AppelÃ© par reservation.php via fetch() dÃ¨s que le patient choisit ses dates
        // GET /integration/equipment/api/disponibilite?equipement_id=X&date_debut=Y&date_fin=Z
        if (preg_match('#^/equipment/api/disponibilite(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->checkDisponibilite();
            return;
        }

        // â”€â”€ Equipment rental module â€” Views â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/historique-location(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->historiqueLocation();
            return;
        }

        if (preg_match('#^/equipements(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->gestionEquipements();
            return;
        }

        if (preg_match('#^/mes-reservations(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->mesReservations();
            return;
        }

        if (preg_match('#^/reservation(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->reservation();
            return;
        }

        if (preg_match('#^/catalogue(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->catalogue();
            return;
        }

        // â”€â”€ Magazine module â€” Back Office â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/magazine/admin/comment/approve(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->approveComment();
            return;
        }

        if (preg_match('#^/magazine/admin/comment/reject(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->rejectComment();
            return;
        }

        if (preg_match('#^/magazine/admin/comment/delete(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->deleteComment();
            return;
        }

        if (preg_match('#^/magazine/admin/comments(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->viewPostComments();
            return;
        }

        if (preg_match('#^/magazine/admin/stats(?:/|$)#', $path)) {
            (new \Controllers\PostController())->statsPage();
            return;
        }

        if (preg_match('#^/magazine/admin/articles(?:/|$)#', $path)) {
            (new \Controllers\PostController())->listArticles();
            return;
        }

        if (preg_match('#^/magazine/admin/article-form(?:/|$)#', $path)) {
            (new \Controllers\PostController())->showForm();
            return;
        }

        if (preg_match('#^/magazine/admin/save(?:/|$)#', $path)) {
            (new \Controllers\PostController())->saveArticle();
            return;
        }

        if (preg_match('#^/magazine/admin/rephrase(?:/|$)#', $path)) {
            (new \Controllers\PostController())->rephrase();
            return;
        }

        if (preg_match('#^/magazine/admin/delete(?:/|$)#', $path)) {
            (new \Controllers\PostController())->deleteArticle();
            return;
        }

        if (preg_match('#^/magazine/admin(?:/|$)#', $path)) {
            (new \Controllers\PostController())->dashboard();
            return;
        }

        // â”€â”€ Magazine module â€” Front Office â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/magazine/comment/add-ajax(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->addCommentAjax();
            return;
        }

        if (preg_match('#^/magazine/comment/add(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->addComment();
            return;
        }

        if (preg_match('#^/magazine/comment/edit(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->editComment();
            return;
        }

        if (preg_match('#^/magazine/comment/like(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->likeComment();
            return;
        }

        if (preg_match('#^/magazine/comment/delete(?:/|$)#', $path)) {
            (new \Controllers\CommentController())->deleteOwnComment();
            return;
        }

        if (preg_match('#^/magazine/article(?:/|$)#', $path)) {
            (new \Controllers\PostController())->viewArticle();
            return;
        }

        if (preg_match('#^/magazine/category(?:/|$)#', $path)) {
            (new \Controllers\PostController())->category();
            return;
        }

        if (preg_match('#^/magazine/summarize(?:/|$)#', $path)) {
            (new \Controllers\PostController())->summarize();
            return;
        }

        if (preg_match('#^/magazine/bookmark(?:/|$)#', $path)) {
            (new \Controllers\PostController())->toggleBookmark();
            return;
        }

        if (preg_match('#^/magazine/bookmarks/data(?:/|$)#', $path)) {
            (new \Controllers\PostController())->bookmarksData();
            return;
        }

        if (preg_match('#^/magazine/bookmarks(?:/|$)#', $path)) {
            (new \Controllers\PostController())->myBookmarks();
            return;
        }

        if (preg_match('#^/magazine/like(?:/|$)#', $path)) {
            (new \Controllers\PostController())->likeArticle();
            return;
        }

        if (preg_match('#^/magazine/notifications/read(?:/|$)#', $path)) {
            (new \Controllers\NotificationController())->markRead();
            return;
        }

        if (preg_match('#^/magazine/notifications(?:/|$)#', $path)) {
            (new \Controllers\NotificationController())->index();
            return;
        }

        if (preg_match('#^/magazine/search(?:/|$)#', $path)) {
            (new \Controllers\PostController())->searchArticles();
            return;
        }

        if (preg_match('#^/magazine(?:/|$)#', $path)) {
            (new \Controllers\PostController())->home();
            return;
        }

        // â”€â”€ Rendez-vous module â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/rdv/admin(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->adminDashboard();
            return;
        }
        if (preg_match('#^/rdv/dashboard(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->doctorDashboard();
            return;
        }
        if (preg_match('#^/rdv/planning(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->doctorPlanning();
            return;
        }
        if (preg_match('#^/rdv/statistiques(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->doctorStats();
            return;
        }
        if (preg_match('#^/rdv/modifier(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->modifierRdvView();
            return;
        }
        if (preg_match('#^/rdv/annuaire(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->patientAnnuaire();
            return;
        }
        if (preg_match('#^/rdv/reserver(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->patientBookRdv();
            return;
        }
        if (preg_match('#^/rdv/medecin/planning(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->patientPlanning();
            return;
        }
        if (preg_match('#^/rdv/traitement(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->traitementRdv();
            return;
        }
        if (preg_match('#^/rdv/notification-lue(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->notificationLue();
            return;
        }
        if (preg_match('#^/rdv/notifications-toutes-lues(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->notificationsToutesLues();
            return;
        }
        if (preg_match('#^/rdv/notifications-count(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->notificationsCount();
            return;
        }
        if (preg_match('#^/rdv/reponse-modification(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->reponseModification();
            return;
        }
        if (preg_match('#^/rdv/confirmation(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->patientConfirmation();
            return;
        }
        if (preg_match('#^/rdv/mes-rdv(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->patientMesRdv();
            return;
        }
        if (preg_match('#^/rdv/ical(?:/|$)#', $path)) {
            (new \Controllers\RendezVousController())->exportIcal();
            return;
        }

        // â”€â”€ Stock MÃ©dicament module â€” Pharmacien â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // Commandes
        if (preg_match('#^/stock/orders/create(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->orderCreate();
            return;
        }
        if (preg_match('#^/stock/orders/cancel(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->orderCancel();
            return;
        }
        if (preg_match('#^/stock/orders/view(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->orderView();
            return;
        }
        if (preg_match('#^/stock/orders(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->orderList();
            return;
        }
        // Panier
        if (preg_match('#^/stock/cart(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->cart();
            return;
        }
        // Produits (lecture seule)
        if (preg_match('#^/stock/products/search(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->productSearch();
            return;
        }
        if (preg_match('#^/stock/products/filter(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->productFilter();
            return;
        }
        if (preg_match('#^/stock/products(?:/|$)#', $path)) {
            (new \Controllers\StockMedicamentController())->productList();
            return;
        }

        // â”€â”€ Fournisseur module â€” CRUD produits + confirmation commandes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (preg_match('#^/fournisseur/products/delete(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productDelete();
            return;
        }
        if (preg_match('#^/fournisseur/products/edit(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productEdit();
            return;
        }
        if (preg_match('#^/fournisseur/products/create(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productCreate();
            return;
        }
        if (preg_match('#^/fournisseur/products/search(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productSearch();
            return;
        }
        if (preg_match('#^/fournisseur/products/filter(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productFilter();
            return;
        }
        if (preg_match('#^/fournisseur/products(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->productList();
            return;
        }
        // Fournisseur â€” confirmation commandes
        if (preg_match('#^/fournisseur/orders/status(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->orderChangeStatus();
            return;
        }
        if (preg_match('#^/fournisseur/orders/view(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->orderView();
            return;
        }
        if (preg_match('#^/fournisseur/orders(?:/|$)#', $path)) {
            (new \Controllers\FournisseurController())->orderList();
            return;
        }

        // â”€â”€ Dossier MÃ©dical â€” Medecin routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        // Admin subdomain (more specific, must come before generic /dossier/admin)
        if (preg_match('#^/dossier/admin/doctors/patients/api(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->getDoctorPatientsAjax();
            return;
        }
        if (preg_match('#^/dossier/admin/doctors/patients(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->viewDoctorPatients();
            return;
        }
        if (preg_match('#^/dossier/admin/doctors/edit(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->editDoctor();
            return;
        }
        if (preg_match('#^/dossier/admin/doctors/delete(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->deleteDoctor();
            return;
        }
        if (preg_match('#^/dossier/admin/doctors(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->doctorsList();
            return;
        }
        if (preg_match('#^/dossier/admin/consultations/view(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->viewConsultation();
            return;
        }
        if (preg_match('#^/dossier/admin/consultations(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->listConsultations();
            return;
        }
        if (preg_match('#^/dossier/admin/ordonnances/view(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->viewOrdonnance();
            return;
        }
        if (preg_match('#^/dossier/admin/ordonnances(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->listOrdonnances();
            return;
        }
        if (preg_match('#^/dossier/admin(?:/|$)#', $path)) {
            (new \Controllers\DossierAdminController())->dashboard();
            return;
        }

        // Patient's own dossier
        if (preg_match('#^/dossier/patient/update-profile(?:/|$)#', $path)) {
            (new \Controllers\PatientDossierController())->updateProfile();
            return;
        }
        if (preg_match('#^/dossier/patient/request-prescription(?:/|$)#', $path)) {
            (new \Controllers\PatientDossierController())->requestPrescription();
            return;
        }
        if (preg_match('#^/dossier/patient(?:/|$)#', $path)) {
            (new \Controllers\PatientDossierController())->dashboard();
            return;
        }

        // Ordonnances (Medecin)
        if (preg_match('#^/dossier/ordonnance/add(?:/|$)#', $path)) {
            (new \Controllers\OrdonnanceController())->add();
            return;
        }
        if (preg_match('#^/dossier/ordonnance/edit(?:/|$)#', $path)) {
            (new \Controllers\OrdonnanceController())->edit();
            return;
        }
        if (preg_match('#^/dossier/ordonnance/delete(?:/|$)#', $path)) {
            (new \Controllers\OrdonnanceController())->delete();
            return;
        }
        if (preg_match('#^/dossier/ordonnance/view(?:/|$)#', $path)) {
            (new \Controllers\OrdonnanceController())->view();
            return;
        }
        if (preg_match('#^/dossier/ordonnances(?:/|$)#', $path)) {
            (new \Controllers\OrdonnanceController())->listAll();
            return;
        }

        // Demandes (Medecin)
        if (preg_match('#^/dossier/demandes/statut(?:/|$)#', $path)) {
            (new \Controllers\DemandeController())->updateStatut();
            return;
        }
        if (preg_match('#^/dossier/demandes(?:/|$)#', $path)) {
            (new \Controllers\DemandeController())->listDemandes();
            return;
        }

        // Consultation CRUD (Medecin)
        if (preg_match('#^/dossier/consultation/edit(?:/|$)#', $path)) {
            (new \Controllers\DossierController())->editConsultation();
            return;
        }
        if (preg_match('#^/dossier/consultation/delete(?:/|$)#', $path)) {
            (new \Controllers\DossierController())->deleteConsultation();
            return;
        }
        if (preg_match('#^/dossier/nouvelle-consultation(?:/|$)#', $path)) {
            (new \Controllers\DossierController())->nouvelleConsultation();
            return;
        }

        // Dossier view (patient detail)
        if (preg_match('#^/dossier/view(?:/|$)#', $path)) {
            (new \Controllers\DossierController())->viewDossier();
            return;
        }

        // Patient list (Medecin)
        if (preg_match('#^/dossier/patients(?:/|$)#', $path)) {
            (new \Controllers\DossierController())->listPatients();
            return;
        }

        // â”€â”€ Default: Landing page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        (new \Controllers\LandingController())->index();
    }
}
