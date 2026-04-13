<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication operations including login and registration
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

class AuthController
{
    /**
     * Display and handle login form
     * 
     * @return void
     */
    public function login(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string)($_POST['username'] ?? ''));
            $password = trim((string)($_POST['password'] ?? ''));

            // Validate input
            if (empty($username) || empty($password)) {
                $errors[] = 'Veuillez remplir tous les champs.';
            } else {
                // Authenticate against database
                try {
                    require_once __DIR__ . '/../config.php';
                    $pdo = \config::getConnexion();
                    
                    // Search by email or username
                    $query = "SELECT u.id_PK, u.mail, u.prenom, u.nom, u.motdp, r.libelle as role_name 
                             FROM utilisateurs u 
                             LEFT JOIN roles r ON u.id_role = r.id_role 
                             WHERE u.mail = :username OR u.prenom = :username";
                    
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['username' => $username]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    // Verify password using bcrypt
                    if ($user && password_verify($password, $user['motdp'])) {
                        // Set comprehensive session data
                        $_SESSION['user'] = [
                            'id' => $user['id_PK'],
                            'username' => $username,
                            'email' => $user['mail'],
                            'firstname' => $user['prenom'],
                            'lastname' => $user['nom'],
                            'role' => $user['role_name']
                        ];
                        
                        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
                        header('Location: ' . $base . '/dashboard');
                        exit;
                    } else {
                        $errors[] = 'Email/Prénom ou mot de passe incorrect.';
                    }
                } catch (\PDOException $e) {
                    $errors[] = 'Erreur de connexion à la base de données.';
                }
            }
        }

        // Render login page
        include __DIR__ . '/../Views/layout/header.php';
        include __DIR__ . '/../Views/Front/login.php';
        include __DIR__ . '/../Views/layout/footer.php';
    }

    /**
     * Display and handle registration form
     * 
     * @return void
     */
    public function register(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $errors = [];

        // Handle registration form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = trim((string)($_POST['firstName'] ?? ''));
            $lastName = trim((string)($_POST['lastName'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $phone = trim((string)($_POST['phone'] ?? ''));
            $password = trim((string)($_POST['password'] ?? ''));
            $confirmPassword = trim((string)($_POST['confirmPassword'] ?? ''));

            // Validate registration data
            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                $errors[] = 'Veuillez remplir tous les champs requis.';
            } elseif ($password !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            } else {
                // Only allow patient registration
                try {
                    require_once __DIR__ . '/../config.php';
                    $pdo = \config::getConnexion();
                    
                    // Get patient role ID (assuming patient role ID = 5, adjust based on your roles)
                    $roleQuery = "SELECT id_role FROM roles WHERE libelle = 'Patient' OR libelle = 'patient'";
                    $roleStmt = $pdo->query($roleQuery);
                    $roleResult = $roleStmt->fetch(\PDO::FETCH_ASSOC);
                    $patientRoleId = $roleResult['id_role'] ?? 5; // Default to 5 if not found
                    
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new patient user
                    $insertQuery = "INSERT INTO utilisateurs (prenom, nom, mail, tel, motdp, id_role) 
                                   VALUES (:prenom, :nom, :mail, :tel, :motdp, :id_role)";
                    $stmt = $pdo->prepare($insertQuery);
                    $stmt->execute([
                        ':prenom' => $firstName,
                        ':nom' => $lastName,
                        ':mail' => $email,
                        ':tel' => $phone,
                        ':motdp' => $hashedPassword,
                        ':id_role' => $patientRoleId
                    ]);
                    
                    $_SESSION['success_message'] = 'Inscription réussie! Vous êtes enregistré en tant que patient. Vous pouvez maintenant vous connecter.';
                    
                    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
                    header('Location: ' . ($base ?: '/') . 'login');
                    exit;
                } catch (\PDOException $e) {
                    $errors[] = 'Erreur lors de l\'inscription: ' . $e->getMessage();
                }
            }
        }

        // Render registration page
        include __DIR__ . '/../Views/layout/header.php';
        include __DIR__ . '/../Views/Front/register.php';
        include __DIR__ . '/../Views/layout/footer.php';
    }
}
