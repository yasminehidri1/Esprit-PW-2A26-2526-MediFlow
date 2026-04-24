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

        // ── Auth ────────────────────────────────────────────────────────────
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

        // ── Logout ──────────────────────────────────────────────────────────
        if (preg_match('#^/logout(?:/|$)#', $path)) {
            session_start();
            session_destroy();
            header('Location: /integration/login');
            exit;
        }

        // ── User Dashboard / Profile ────────────────────────────────────────
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

        // ── Admin user management ───────────────────────────────────────────
        if (preg_match('#^/admin(?:/|$)#', $path)) {
            (new \Controllers\AdminController())->handle();
            return;
        }

        // ── Equipment rental module — APIs ──────────────────────────────────
        if (preg_match('#^/equipment/api/reservations(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->reservationApi();
            return;
        }

        if (preg_match('#^/equipment/api/equipements(?:/|$)#', $path)) {
            (new \Controllers\PatientEquipmentController())->equipementApi();
            return;
        }

        // ── Equipment rental module — Views ─────────────────────────────────
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

        // ── Magazine module — Back Office ────────────────────────────────────
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

        if (preg_match('#^/magazine/admin/delete(?:/|$)#', $path)) {
            (new \Controllers\PostController())->deleteArticle();
            return;
        }

        if (preg_match('#^/magazine/admin(?:/|$)#', $path)) {
            (new \Controllers\PostController())->dashboard();
            return;
        }

        // ── Magazine module — Front Office ───────────────────────────────────
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

        if (preg_match('#^/magazine/like(?:/|$)#', $path)) {
            (new \Controllers\PostController())->likeArticle();
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

        // ── Default: Landing page ────────────────────────────────────────────
        (new \Controllers\LandingController())->index();
    }
}
