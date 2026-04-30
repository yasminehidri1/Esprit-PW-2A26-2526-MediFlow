<?php
/**
 * Admin Controller
 * 
 * Handles administrative user management (CRUD operations)
 * Restricted to Admin role only
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Core\SessionHelper;
use Models\UserModel;

class AdminController
{
    use SessionHelper;

    private UserModel $userModel;
    private string $action;

    public function __construct()
    {
        $this->ensureSession();
        $this->requireAdminAccess();
        $this->userModel = new UserModel();
        $this->action = $_GET['action'] ?? 'list';
    }

    /**
     * Check if user has admin role
     * 
     * @return void
     */
    private function requireAdminAccess(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            http_response_code(403);
            die('Accès refusé. Seuls les administrateurs peuvent accéder à cette page.');
        }
    }

    /**
     * Route action to appropriate method
     * 
     * @return void
     */
    public function handle(): void
    {
        match($this->action) {
            'list' => $this->list(),
            'create' => $this->create(),
            'store' => $this->store(),
            'edit' => $this->edit(),
            'update' => $this->update(),
            'delete' => $this->delete(),
            'toggle_status' => $this->toggleStatus(),
            default => $this->list()
        };
    }

    /**
     * Display list of users with search and filtering
     * 
     * @return void
     */
    private function list(): void
    {
        try {
            $search = trim($_GET['search'] ?? '');
            $roleFilter = isset($_GET['role']) && $_GET['role'] !== '' ? (int)$_GET['role'] : null;
            
            // Pagination settings
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = 5; // Users per page
            $offset = ($page - 1) * $limit;
            
            $totalUsers = $this->userModel->countSearchAndFilterUsers(
                $search ?: null,
                $roleFilter
            );
            
            $totalPages = ceil($totalUsers / $limit);
            // Adjust page if it exceeds total pages
            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
                $offset = ($page - 1) * $limit;
            }
            
            $users = $this->userModel->searchAndFilterUsers(
                $search ?: null,
                $roleFilter,
                $limit,
                $offset
            );
            
            $roles = $this->userModel->getAllRoles();
            
            $data = [
                'users' => $users,
                'roles' => $roles,
                'search' => $search,
                'roleFilter' => $roleFilter,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'totalUsers' => $totalUsers,
                    'totalPages' => $totalPages
                ],
                'message' => $_SESSION['message'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];
            
            unset($_SESSION['message'], $_SESSION['error']);
            
            $currentView = 'admin_users_list';
            include __DIR__ . '/../Views/Back/layout.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors du chargement des utilisateurs.';
            $this->list();
        }
    }

    /**
     * Display create user form
     * 
     * @return void
     */
    private function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $roles = $this->userModel->getAllRoles();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        $currentView = 'admin_users_create';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * Store new user in database
     * 
     * @return void
     */
    private function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/admin');
            exit;
        }

        // Get and sanitize form data
        $formData = [
            'nom' => $this->getPost('nom'),
            'prenom' => $this->getPost('prenom'),
            'mail' => $this->getPost('mail'),
            'tel' => $this->getPost('tel'),
            'adresse' => $this->getPost('adresse'),
            'id_role' => (int)$this->getPost('id_role'),
            'password' => $this->getPost('password'),
            'password_confirm' => $this->getPost('password_confirm')
        ];

        // Validate form data
        $errors = $this->validateUserInput($formData);

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /integration/admin?action=create');
            exit;
        }

        // Prepare user data for creation
        $userData = [
            'nom' => $formData['nom'],
            'prenom' => $formData['prenom'],
            'mail' => $formData['mail'],
            'tel' => $formData['tel'] ?: null,
            'adresse' => $formData['adresse'] ?: null,
            'id_role' => $formData['id_role'],
            'password' => $formData['password']
        ];

        if ($this->userModel->createUser($userData)) {
            $_SESSION['message'] = 'Utilisateur créé avec succès.';
            header('Location: /integration/admin');
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de l\'utilisateur.';
            header('Location: /integration/admin?action=create');
        }
        exit;
    }

    /**
     * Display edit user form
     * 
     * @return void
     */
    private function edit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update();
            return;
        }

        $userId = (int)($_GET['id'] ?? 0);
        
        if ($userId <= 0 || !$user = $this->userModel->getUserById($userId)) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /integration/admin');
            exit;
        }

        $roles = $this->userModel->getAllRoles();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        $currentView = 'admin_users_edit';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * Update existing user
     * 
     * @return void
     */
    private function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/admin');
            exit;
        }

        $userId = (int)($_POST['id'] ?? 0);
        
        if ($userId <= 0) {
            $_SESSION['error'] = 'Utilisateur invalide.';
            header('Location: /integration/admin');
            exit;
        }

        // Get and sanitize form data
        $formData = [
            'id' => $userId,
            'nom' => $this->getPost('nom'),
            'prenom' => $this->getPost('prenom'),
            'mail' => $this->getPost('mail'),
            'tel' => $this->getPost('tel'),
            'adresse' => $this->getPost('adresse'),
            'id_role' => (int)$this->getPost('id_role'),
            'password' => $this->getPost('password'),
            'password_confirm' => $this->getPost('password_confirm')
        ];

        // Validate form data
        $errors = $this->validateUserInput($formData, $userId);

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header("Location: /integration/admin?action=edit&id=$userId");
            exit;
        }

        // Prepare user data for update
        $userData = [
            'nom' => $formData['nom'],
            'prenom' => $formData['prenom'],
            'mail' => $formData['mail'],
            'tel' => $formData['tel'] ?: null,
            'adresse' => $formData['adresse'] ?: null,
            'id_role' => $formData['id_role']
        ];

        if (!empty($formData['password'])) {
            $userData['password'] = $formData['password'];
        }

        if ($this->userModel->updateUser($userId, $userData)) {
            $_SESSION['message'] = 'Utilisateur mis à jour avec succès.';
            header('Location: /integration/admin');
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour de l\'utilisateur.';
            header("Location: /integration/admin?action=edit&id=$userId");
        }
        exit;
    }

    /**
     * Delete user
     * 
     * @return void
     */
    private function delete(): void
    {
        $userId = (int)($_GET['id'] ?? 0);

        if ($userId <= 0) {
            $_SESSION['error'] = 'Utilisateur invalide.';
            header('Location: /integration/admin');
            exit;
        }

        if ($this->userModel->deleteUser($userId)) {
            $_SESSION['message'] = 'Utilisateur supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de l\'utilisateur.';
        }

        header('Location: /integration/admin');
        exit;
    }

    /**
     * Toggle user status (suspend/unsuspend)
     * 
     * @return void
     */
    private function toggleStatus(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id === 0) {
            $_SESSION['error'] = 'ID utilisateur invalide.';
            header('Location: /integration/admin');
            exit;
        }

        // Prevent suspending oneself
        if ($id === (int)$_SESSION['user']['id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas modifier votre propre statut.';
            header('Location: /integration/admin');
            exit;
        }

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /integration/admin');
            exit;
        }

        $newStatus = ($user['status'] === 'suspended') ? 'active' : 'suspended';

        if ($this->userModel->updateUserStatus($id, $newStatus)) {
            $_SESSION['message'] = $newStatus === 'active' ? 'Utilisateur réactivé avec succès.' : 'Utilisateur suspendu avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification du statut de l\'utilisateur.';
        }
        
        header('Location: /integration/admin');
        exit;
    }

    /**
     * Validate user input data
     * 
     * @param array $data Form data
     * @param int|null $userId For update operations (email uniqueness check)
     * @return array List of validation errors
     */
    private function validateUserInput(array $data, ?int $userId = null): array
    {
        $errors = [];

        // Required fields
        if (empty($data['nom'])) $errors[] = 'Le nom est requis.';
        if (empty($data['prenom'])) $errors[] = 'Le prénom est requis.';
        if (empty($data['mail'])) $errors[] = 'L\'email est requis.';
        if (empty($data['id_role'])) $errors[] = 'Le rôle est requis.';

        // Email validation
        if (!empty($data['mail'])) {
            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email invalide.';
            } elseif ($this->userModel->emailExists($data['mail'], $userId)) {
                $errors[] = 'Cet email existe déjà.';
            }
        }

        // Password validation for new users
        if ($userId === null) {
            if (empty($data['password'])) {
                $errors[] = 'Le mot de passe est requis.';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($data['password'] !== $data['password_confirm']) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
        } elseif (!empty($data['password'])) {
            // Password validation for updates (optional)
            if (strlen($data['password']) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($data['password'] !== $data['password_confirm']) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
        }

        return $errors;
    }
}
