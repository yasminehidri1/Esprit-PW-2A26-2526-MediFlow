<?php
/**
 * Admin CRUD Controller
 * 
 * Handles all admin user management operations (Create, Read, Update, Delete)
 * All operations in a single file
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Models\UserModel;

class AdminController
{
    private UserModel $userModel;
    private string $action;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->checkAdminAccess();
        $this->action = $_GET['action'] ?? 'list';
    }

    private function checkAdminAccess(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            http_response_code(403);
            die('Accès refusé. Seuls les administrateurs peuvent accéder à cette page.');
        }
    }

    public function handle(): void
    {
        match($this->action) {
            'list' => $this->list(),
            'create' => $this->create(),
            'store' => $this->store(),
            'edit' => $this->edit(),
            'update' => $this->update(),
            'delete' => $this->delete(),
            default => $this->list()
        };
    }

    // ==================== LIST ====================
    private function list(): void
    {
        try {
            // Get search and filter parameters
            $search = trim($_GET['search'] ?? '');
            $roleFilter = isset($_GET['role']) ? (int)$_GET['role'] : null;
            
            // Get filtered users
            $users = $this->userModel->searchAndFilterUsers(
                $search ?: null,
                ($roleFilter && $roleFilter > 0) ? $roleFilter : null
            );
            
            $roles = $this->userModel->getAllRoles();
            
            $data = [
                'users' => $users,
                'roles' => $roles,
                'search' => $search,
                'roleFilter' => $roleFilter,
                'message' => $_SESSION['message'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];
            
            unset($_SESSION['message'], $_SESSION['error']);
            
            include __DIR__ . '/../Views/Back/admin_users_list.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors du chargement des utilisateurs.';
            $this->list();
        }
    }

    // ==================== CREATE FORM ====================
    private function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $roles = $this->userModel->getAllRoles();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        include __DIR__ . '/../Views/Back/admin_users_create.php';
    }

    // ==================== STORE ====================
    private function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Mediflow/admin');
            exit;
        }

        $errors = [];

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $tel = trim($_POST['tel'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $id_role = trim($_POST['id_role'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password_confirm = trim($_POST['password_confirm'] ?? '');

        if (empty($nom)) $errors[] = 'Le nom est requis.';
        if (empty($prenom)) $errors[] = 'Le prénom est requis.';
        if (empty($mail)) $errors[] = 'L\'email est requis.';
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if ($this->userModel->emailExists($mail)) $errors[] = 'Cet email existe déjà.';
        if (empty($id_role)) $errors[] = 'Le rôle est requis.';
        if (empty($password)) $errors[] = 'Le mot de passe est requis.';
        if (strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        if ($password !== $password_confirm) $errors[] = 'Les mots de passe ne correspondent pas.';

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /Mediflow/admin?action=create');
            exit;
        }

        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'mail' => $mail,
            'tel' => $tel ?: null,
            'adresse' => $adresse ?: null,
            'id_role' => $id_role,
            'password' => $password
        ];

        if ($this->userModel->createUser($userData)) {
            $_SESSION['message'] = 'Utilisateur créé avec succès.';
            header('Location: /Mediflow/admin');
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de l\'utilisateur.';
            header('Location: /Mediflow/admin?action=create');
        }
        exit;
    }

    // ==================== EDIT FORM ====================
    private function edit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update();
            return;
        }

        $userId = (int)($_GET['id'] ?? 0);
        
        if ($userId <= 0) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /Mediflow/admin');
            exit;
        }

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /Mediflow/admin');
            exit;
        }

        $roles = $this->userModel->getAllRoles();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        include __DIR__ . '/../Views/Back/admin_users_edit.php';
    }

    // ==================== UPDATE ====================
    private function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Mediflow/admin');
            exit;
        }

        $userId = (int)($_POST['id'] ?? 0);
        $errors = [];

        if ($userId <= 0) {
            $_SESSION['error'] = 'Utilisateur invalide.';
            header('Location: /Mediflow/admin');
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $tel = trim($_POST['tel'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $id_role = trim($_POST['id_role'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password_confirm = trim($_POST['password_confirm'] ?? '');

        if (empty($nom)) $errors[] = 'Le nom est requis.';
        if (empty($prenom)) $errors[] = 'Le prénom est requis.';
        if (empty($mail)) $errors[] = 'L\'email est requis.';
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if ($this->userModel->emailExists($mail, $userId)) $errors[] = 'Cet email existe déjà.';
        if (empty($id_role)) $errors[] = 'Le rôle est requis.';

        if (!empty($password)) {
            if (strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            if ($password !== $password_confirm) $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header("Location: /Mediflow/admin?action=edit&id=$userId");
            exit;
        }

        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'mail' => $mail,
            'tel' => $tel ?: null,
            'adresse' => $adresse ?: null,
            'id_role' => $id_role
        ];

        if (!empty($password)) {
            $userData['password'] = $password;
        }

        if ($this->userModel->updateUser($userId, $userData)) {
            $_SESSION['message'] = 'Utilisateur mis à jour avec succès.';
            header('Location: /Mediflow/admin');
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour de l\'utilisateur.';
            header("Location: /Mediflow/admin?action=edit&id=$userId");
        }
        exit;
    }

    // ==================== DELETE ====================
    private function delete(): void
    {
        $userId = (int)($_GET['id'] ?? 0);

        if ($userId <= 0) {
            $_SESSION['error'] = 'Utilisateur invalide.';
            header('Location: /Mediflow/admin');
            exit;
        }

        if ($this->userModel->deleteUser($userId)) {
            $_SESSION['message'] = 'Utilisateur supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de l\'utilisateur.';
        }

        header('Location: /Mediflow/admin');
        exit;
    }
}
