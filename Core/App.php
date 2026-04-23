<?php
/**
 * MediFlow Application Router
 * 
 * Core application class that handles HTTP request routing
 * 
 * @package MediFlow
 * @version 1.0.0
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
        
        // Remove /Mediflow prefix if present
        $path = preg_replace('#^/Mediflow#', '', $path);
        $path = $path ?: '/';

        // Route to appropriate controller
        if (preg_match('#/login(?:/|$)#', $path)) {
            $controller = new \Controllers\AuthController();
            $controller->login();
            return;
        }

        if (preg_match('#/register(?:/|$)#', $path)) {
            $controller = new \Controllers\AuthController();
            $controller->register();
            return;
        }

        if (preg_match('#/terms(?:/|$)#', $path)) {
            $controller = new \Controllers\LandingController();
            $controller->terms();
            return;
        }

        if (preg_match('#/dashboard(?:/|$)#', $path)) {
            $controller = new \Controllers\DashboardController();
            $controller->index();
            return;
        }

        if (preg_match('#/dashboard/api/users(?:/|$)#', $path)) {
            $controller = new \Controllers\DashboardController();
            $controller->getUsers();
            return;
        }

        if (preg_match('#/dashboard/api/stats(?:/|$)#', $path)) {
            $controller = new \Controllers\DashboardController();
            $controller->getStats();
            return;
        }

        if (preg_match('#/profile/update(?:/|$)#', $path)) {
            $controller = new \Controllers\DashboardController();
            $controller->updateProfile();
            return;
        }

        if (preg_match('#/profile(?:/|$)#', $path)) {
            $controller = new \Controllers\DashboardController();
            $controller->profile();
            return;
        }

        // Admin user management (all CRUD in one controller)
        if (preg_match('#/admin(?:/|$)#', $path)) {
            $controller = new \Controllers\AdminController();
            $controller->handle();
            return;
        }

        // ── Equipment rental module (Patient-facing) ──
        if (preg_match('#/equipment/api/reservations(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->reservationApi();
            return;
        }

        if (preg_match('#/equipment/api/equipements(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->equipementApi();
            return;
        }

        // ── Equipment manager backoffice ──
        if (preg_match('#/historique-location(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->historiqueLocation();
            return;
        }

        if (preg_match('#/equipements(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->gestionEquipements();
            return;
        }

        if (preg_match('#/mes-reservations(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->mesReservations();
            return;
        }

        if (preg_match('#/reservation(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->reservation();
            return;
        }

        if (preg_match('#/catalogue(?:/|$)#', $path)) {
            $controller = new \Controllers\PatientEquipmentController();
            $controller->catalogue();
            return;
        }

        // Default: Landing page
        $controller = new \Controllers\LandingController();
        $controller->index();
    }
}
