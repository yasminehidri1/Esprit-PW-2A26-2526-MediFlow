<?php
/**
 * Landing Page Controller
 * 
 * Handles the main landing page display
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

class LandingController
{
    /**
     * Display the landing page
     * 
     * @return void
     */
    public function index(): void
    {
        include __DIR__ . '/../Views/layout/header.php';
        include __DIR__ . '/../Views/Front/landing.php';
        include __DIR__ . '/../Views/layout/footer.php';
    }

    /**
     * Display the terms and conditions page
     * 
     * @return void
     */
    public function terms(): void
    {
        include __DIR__ . '/../Views/Front/terms.php';
    }
}
