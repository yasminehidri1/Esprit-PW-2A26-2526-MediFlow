<?php
/**
 * Back Office Dashboard Controller
 * 
 * Handles dashboard display and data for authenticated users
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Models\DashboardModel;

class DashboardController
{
    private DashboardModel $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Display admin dashboard
     * 
     * @return void
     */
    public function index(): void
    {
        // Check if user is authenticated
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            header('Location: /Mediflow/login');
            exit;
        }

        // Get current user data
        $userId = $_SESSION['user']['id'];
        $currentUser = $this->dashboardModel->getUserById($userId);

        // Get dashboard statistics
        $stats = $this->dashboardModel->getDashboardStats();
        $roles = $this->dashboardModel->getAllRoles();
        $recentActivity = $this->dashboardModel->getRecentActivity();
        $users = $this->dashboardModel->getAllUsers();

        // Prepare data for view
        $data = [
            'currentUser' => $currentUser,
            'stats' => $stats,
            'roles' => $roles,
            'recentActivity' => $recentActivity,
            'users' => $users,
            'pageTitle' => 'Admin Dashboard',
        ];

        // Render dashboard
        include __DIR__ . '/../Views/Back/dashboard.php';
    }

    /**
     * Get users API endpoint (for AJAX requests)
     * 
     * @return void
     */
    public function getUsers(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $users = $this->dashboardModel->getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    /**
     * Get dashboard stats API endpoint
     * 
     * @return void
     */
    public function getStats(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $stats = $this->dashboardModel->getDashboardStats();
        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}
