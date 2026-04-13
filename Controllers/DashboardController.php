<?php
/**
 * Dashboard Controller
 * 
 * Handles admin dashboard display and data management
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Core\SessionHelper;
use Models\DashboardModel;

class DashboardController
{
    use SessionHelper;

    private DashboardModel $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Display admin dashboard with statistics and user data
     * 
     * @return void
     */
    public function index(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $userId = $_SESSION['user']['id'];
        
        // Gather dashboard data
        $data = [
            'currentUser' => $this->dashboardModel->getUserById($userId),
            'stats' => $this->dashboardModel->getDashboardStats(),
            'roles' => $this->dashboardModel->getAllRoles(),
            'recentActivity' => $this->dashboardModel->getRecentActivity(),
            'users' => $this->dashboardModel->getAllUsers(),
            'patients' => $this->dashboardModel->getPatients(),
            'pageTitle' => 'Admin Dashboard',
        ];

        include __DIR__ . '/../Views/Back/dashboard.php';
    }

    /**
     * API endpoint: Get all users (JSON response)
     * 
     * @return void
     */
    public function getUsers(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $users = $this->dashboardModel->getAllUsers();
        $this->jsonResponse($users);
    }

    /**
     * API endpoint: Get dashboard statistics (JSON response)
     * 
     * @return void
     */
    public function getStats(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $stats = $this->dashboardModel->getDashboardStats();
        $this->jsonResponse($stats);
    }

    /**
     * Send JSON response
     * 
     * @param mixed $data
     * @return void
     */
    private function jsonResponse($data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
