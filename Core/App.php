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

        // Admin user management (all CRUD in one controller)
        if (preg_match('#/admin(?:/|$)#', $path)) {
            $controller = new \Controllers\AdminController();
            $controller->handle();
            return;
        }

        // Default: Landing page
        $controller = new \Controllers\LandingController();
        $controller->index();
    }
}
