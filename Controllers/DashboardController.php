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
        $role   = $_SESSION['user']['role'] ?? '';

        // Base data always needed
        $data = [
            'currentUser' => $this->dashboardModel->getUserById($userId),
            'pageTitle'   => 'Tableau de bord',
            'role'        => $role,
        ];

        // ── Role-specific stats ──────────────────────────────────────────
        if ($role === 'Admin') {
            $data['stats']          = $this->dashboardModel->getDashboardStats();
            $data['roles']          = $this->dashboardModel->getAllRoles();
            $data['recentActivity'] = $this->dashboardModel->getRecentActivity();
            $data['users']          = $this->dashboardModel->getAllUsers();
            $data['patients']       = $this->dashboardModel->getPatients();

        } elseif ($role === 'Patient') {
            require_once __DIR__ . '/../Models/Reservation.php';
            $resModel  = new \Reservation();
            $matricule = $data['currentUser']['matricule'] ?? null;

            // Precise filtering by matricule — no name collisions
            $myRes = $matricule ? $resModel->getByMatricule($matricule) : [];

            $data['myReservations'] = $myRes;
            $data['nbEnCours']      = count(array_filter($myRes, fn($r) => ($r['statut']??'') === 'en_cours'));
            $data['nbTermines']     = count(array_filter($myRes, fn($r) => ($r['statut']??'') === 'termine'));
            $data['nbEnRetard']     = count(array_filter($myRes, fn($r) => ($r['statut']??'') === 'en_retard'));
            $data['nbTotal']        = count($myRes);
            $data['latestRes']      = array_slice($myRes, 0, 3);

        } elseif ($role === 'Equipment') {
            // Equipment manager sees inventory stats
            require_once __DIR__ . '/../Models/Equipement.php';
            require_once __DIR__ . '/../Models/Reservation.php';
            $eqModel  = new \Equipement();
            $resModel = new \Reservation();
            $allEq    = $eqModel->getAll();
            $allRes   = $resModel->getAll();

            $data['totalEq']     = count($allEq);
            $data['disponibles'] = count(array_filter($allEq, fn($e) => ($e['statut']??'') === 'disponible'));
            $data['loues']       = count(array_filter($allEq, fn($e) => ($e['statut']??'') === 'loue'));
            $data['maintenance'] = count(array_filter($allEq, fn($e) => ($e['statut']??'') === 'maintenance'));
            $data['totalRes']    = count($allRes);
            $data['resEnCours']  = count(array_filter($allRes, fn($r) => ($r['statut']??'') === 'en_cours'));
            $data['latestEq']    = array_slice(array_reverse($allEq), 0, 4);

        } else {
            // Other roles — generic placeholder until their module is built
            $data['stats'] = [];
        }

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
     * Display user profile page
     * 
     * @return void
     */
    public function profile(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        $userId = $_SESSION['user']['id'];
        $userModel = new \Models\UserModel();
        
        $data = [
            'currentUser' => $userModel->getUserById($userId),
            'pageTitle' => 'My Profile',
            'errors' => [],
            'success' => false
        ];

        $this->renderView('Back/profile', $data);
    }

    /**
     * Handle profile update form submission
     * 
     * @return void
     */
    public function updateProfile(): void
    {
        $this->ensureSession();
        $this->requireAuth();

        // Debug: Log that we're in updateProfile
        error_log('=== updateProfile() CALLED ===');
        error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        error_log('REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
        error_log('POST data received: ' . print_r($_POST, true));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Not a POST request, redirecting');
            header('Location: /Mediflow/profile');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        error_log('Processing update for user ID: ' . $userId);
        
        $userModel = new \Models\UserModel();
        $errors = [];

        // Get and sanitize input - be explicit about checking $_POST
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
        $tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

        error_log('Extracted POST values:');
        error_log('  nom: ' . $nom);
        error_log('  prenom: ' . $prenom);
        error_log('  mail: ' . $mail);
        error_log('  tel: ' . $tel);
        error_log('  adresse: ' . $adresse);

        // Validation
        if (empty($nom)) {
            $errors[] = 'Last name is required.';
        }
        if (empty($prenom)) {
            $errors[] = 'First name is required.';
        }
        if (empty($mail)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ($userModel->emailExists($mail, $userId)) {
            $errors[] = 'This email is already in use by another user.';
        }

        // Password validation
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            } elseif ($password !== $password_confirm) {
                $errors[] = 'Passwords do not match.';
            }
        }

        error_log('Validation errors count: ' . count($errors));
        if (!empty($errors)) {
            error_log('Errors: ' . json_encode($errors));
        }

        // If no errors, update user
        if (empty($errors)) {
            error_log('No validation errors, proceeding with update');
            
            $updateData = [
                'nom' => $nom,
                'prenom' => $prenom,
                'mail' => $mail,
                'tel' => $tel,
                'adresse' => $adresse
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            error_log('Calling userModel->updateUser() with data: ' . json_encode($updateData));
            $updateResult = $userModel->updateUser($userId, $updateData);
            
            error_log('updateUser() returned: ' . ($updateResult ? 'TRUE' : 'FALSE'));

            if ($updateResult) {
                error_log('Update successful! Updating session...');
                
                // Update session
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['prenom'] = $prenom;
                $_SESSION['user']['mail'] = $mail;
                $_SESSION['user']['tel'] = $tel;
                $_SESSION['user']['adresse'] = $adresse;

                error_log('Session updated. Redirecting to /Mediflow/profile?success=1');
                header('Location: /Mediflow/profile?success=1');
                exit;
            } else {
                error_log('Update failed!');
                $errors[] = 'Failed to update profile. Please try again.';
            }
        }

        error_log('Rendering profile view with errors');
        
        // Re-display form with errors
        $data = [
            'currentUser' => $userModel->getUserById($userId),
            'pageTitle' => 'My Profile',
            'errors' => $errors,
            'success' => false
        ];

        $this->renderView('Back/profile', $data);
    }

    /**
     * Render a view file with data
     * 
     * @param string $view View path (relative to Views directory)
     * @param array $data Data to pass to view
     * @return void
     */
    private function renderView(string $view, array $data = []): void
    {
        extract($data);
        include __DIR__ . '/../Views/' . $view . '.php';
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
