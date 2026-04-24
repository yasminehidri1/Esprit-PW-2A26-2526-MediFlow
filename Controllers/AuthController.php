<?php
/**
 * Authentication Controller
 * 
 * Handles user login and patient registration
 * 
 * @package MediFlow\Controllers
 * @version 1.0.0
 */

namespace Controllers;

use Core\SessionHelper;
use Models\UserModel;

class AuthController
{
    use SessionHelper;

    /**
     * Handle login page display and form submission
     * 
     * @return void
     */
    public function login(): void
    {
        $this->ensureSession();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->processLogin();
        }

        $this->renderView('Front/login', ['errors' => $errors]);
    }

    /**
     * Process login form submission
     * 
     * @return array List of errors (empty if successful)
     */
    private function processLogin(): array
    {
        $errors = [];
        $username = $this->getPost('username');
        $password = $this->getPost('password');
        $recaptchaToken = $this->getPost('g-recaptcha-response');

        // Verify reCAPTCHA
        $captchaErrors = $this->verifyRecaptcha($recaptchaToken);
        if (!empty($captchaErrors)) {
            return $captchaErrors;
        }

        // Validate required fields
        if (empty($username) || empty($password)) {
            return ['Veuillez remplir tous les champs.'];
        }

        try {
            $user = $this->authenticateUser($username, $password);
            
            if (!$user) {
                return ['Email/Prénom ou mot de passe incorrect.'];
            }

            // Set session and redirect
            $this->setSession($user, $username);
            header('Location: /integration/dashboard');
            exit;
        } catch (\PDOException $e) {
            return ['Erreur de connexion à la base de données.'];
        }
    }

    /**
     * Authenticate user credentials
     * 
     * @param string $username Email or first name
     * @param string $password Raw password
     * @return array|null User data if authenticated, null otherwise
     */
    private function authenticateUser(string $username, string $password): ?array
    {
        $db = $this->getDatabase();
        
        $query = "
            SELECT u.id_PK, u.mail, u.prenom, u.nom, u.motdp,
                   u.matricule, u.tel, u.adresse,
                   r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.mail = :username OR u.prenom = :username
            LIMIT 1
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['motdp'])) {
            return $user;
        }

        return null;
    }

    /**
     * Set user session data
     * 
     * @param array $user User data
     * @param string $username Login username
     * @return void
     */
    private function setSession(array $user, string $username): void
    {
        $_SESSION['user'] = [
            'id'        => $user['id_PK'],
            'username'  => $username,
            'mail'      => $user['mail'],
            'prenom'    => $user['prenom'],
            'nom'       => $user['nom'],
            'matricule' => $user['matricule'] ?? null,
            'tel'       => $user['tel']       ?? null,
            'adresse'   => $user['adresse']   ?? null,
            'role'      => $user['role_name'],
            'role_name' => $user['role_name'],
            // legacy aliases kept for compatibility
            'email'     => $user['mail'],
            'firstname' => $user['prenom'],
            'lastname'  => $user['nom'],
        ];
    }

    /**
     * Handle registration page display and form submission
     * 
     * @return void
     */
    public function register(): void
    {
        $this->ensureSession();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->processRegistration();
        }

        $this->renderView('Front/register', ['errors' => $errors]);
    }

    /**
     * Process patient registration
     * 
     * @return array List of errors (empty if successful)
     */
    private function processRegistration(): array
    {
        $errors = [];
        
        // Get form data
        $firstName = $this->getPost('firstName');
        $lastName = $this->getPost('lastName');
        $email = $this->getPost('email');
        $phone = $this->getPost('phone');
        $password = $this->getPost('password');
        $confirmPassword = $this->getPost('confirmPassword');
        $recaptchaToken = $this->getPost('g-recaptcha-response');

        // Verify reCAPTCHA
        $captchaErrors = $this->verifyRecaptcha($recaptchaToken);
        if (!empty($captchaErrors)) {
            return $captchaErrors;
        }

        // Validate input
        $validationErrors = $this->validateRegistration($firstName, $lastName, $email, $password, $confirmPassword);
        if (!empty($validationErrors)) {
            return $validationErrors;
        }

        try {
            $patientRoleId = $this->getPatientRoleId();
            $userModel = new UserModel();
            
            $userData = [
                'prenom' => $firstName,
                'nom' => $lastName,
                'mail' => $email,
                'tel' => $phone ?: null,
                'adresse' => null,
                'id_role' => $patientRoleId,
                'password' => $password
            ];

            $userId = $userModel->createUser($userData);

            if (!$userId) {
                return ['Erreur lors de la création du compte. Veuillez réessayer.'];
            }

            $_SESSION['success_message'] = 'Inscription réussie! Vous êtes enregistré en tant que patient. Vous pouvez maintenant vous connecter.';
            header('Location: /integration/login');
            exit;
        } catch (\PDOException $e) {
            return ['Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }

    /**
     * Validate registration form data
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return array List of validation errors
     */
    private function validateRegistration(string $firstName, string $lastName, string $email, string $password, string $confirmPassword): array
    {
        $errors = [];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            $errors[] = 'Veuillez remplir tous les champs requis.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse email invalide.';
        }

        return $errors;
    }

    /**
     * Get patient role ID from database
     * 
     * @return int Patient role ID
     */
    private function getPatientRoleId(): int
    {
        $db = $this->getDatabase();
        $query = "SELECT id_role FROM roles WHERE libelle = 'Patient' OR libelle = 'patient' LIMIT 1";
        $stmt = $db->query($query);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['id_role'] ?? 5;
    }

    /**
     * Verify Google reCAPTCHA v2 token
     * 
     * @param string $token The reCAPTCHA token from client
     * @return array List of errors (empty if valid)
     */
    private function verifyRecaptcha(string $token): array
    {
        $errors = [];

        // Check if token is provided
        if (empty($token)) {
            return ['Veuillez vérifier le reCAPTCHA.'];
        }

        try {
            // Get reCAPTCHA secret key from config
            require_once __DIR__ . '/../config.php';
            $secretKey = \config::getRecaptchaSecretKey();

            // Check if secret key is configured
            if (empty($secretKey) || $secretKey === 'YOUR_RECAPTCHA_SECRET_KEY') {
                error_log('Warning: reCAPTCHA secret key not configured');
                return [];
            }

            // Verify token with Google
            $verificationUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $postData = http_build_query([
                'secret' => $secretKey,
                'response' => $token
            ]);

            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postData,
                    'timeout' => 10
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents($verificationUrl, false, $context);

            if ($response === false) {
                return ['Erreur de vérification du reCAPTCHA. Veuillez réessayer.'];
            }

            $responseData = json_decode($response, true);

            // Check response
            if (!isset($responseData['success']) || !$responseData['success']) {
                error_log('reCAPTCHA verification failed: ' . json_encode($responseData));
                return ['Le reCAPTCHA n\'a pas pu être vérifié. Veuillez réessayer.'];
            }

            return [];
        } catch (\Exception $e) {
            error_log('reCAPTCHA verification error: ' . $e->getMessage());
            return ['Erreur lors de la vérification de sécurité. Veuillez réessayer plus tard.'];
        }
    }

    /**
     * Render view with data
     * 
     * @param string $view View path
     * @param array $data Data to pass to view
     * @return void
     */
    private function renderView(string $view, array $data = []): void
    {
        extract($data);
        $errors = $data['errors'] ?? [];
        
        include __DIR__ . '/../Views/layout/header.php';
        include __DIR__ . '/../Views/' . $view . '.php';
        include __DIR__ . '/../Views/layout/footer.php';
    }
}
