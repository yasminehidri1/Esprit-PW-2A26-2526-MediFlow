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
            $result = $this->authenticateUser($username, $password);

            if ($result === 'suspended') {
                return ['Votre compte a été suspendu. Veuillez contacter l\'administrateur.'];
            }

            if (!$result) {
                return ['Email/Prénom ou mot de passe incorrect.'];
            }

            // Set session and redirect
            $this->setSession($result, $username);
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
    private function authenticateUser(string $username, string $password): array|string|null
    {
        $db = $this->getDatabase();
        
        $query = "
            SELECT u.id_PK, u.mail, u.prenom, u.nom, u.motdp,
                   u.matricule, u.tel, u.adresse, u.profile_pic,
                   u.status, r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.mail = :username OR u.prenom = :username
            LIMIT 1
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // User doesn't exist in database
        if (!$user) {
            return null;
        }

        // User exists but password doesn't match
        if (!password_verify($password, $user['motdp'])) {
            return null;
        }

        // ✅ Check suspension AFTER password is verified (to avoid timing attacks)
        if (($user['status'] ?? 'active') === 'suspended') {
            return 'suspended';
        }

        // User authenticated successfully
        return $user;
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
            'id'                   => $user['id_PK'],
            'username'             => $username,
            'mail'                 => $user['mail'],
            'prenom'               => $user['prenom'],
            'nom'                  => $user['nom'],
            'matricule'            => $user['matricule']            ?? null,
            'tel'                  => $user['tel']                  ?? null,
            'adresse'              => $user['adresse']              ?? null,
            'profile_pic'          => $user['profile_pic']          ?? null,
            'role'                 => $user['role_name'],
            'role_name'            => $user['role_name'],
            'onboarding_completed' => $user['onboarding_completed'] ?? false,
            // legacy aliases kept for compatibility
            'email'                => $user['mail'],
            'firstname'            => $user['prenom'],
            'lastname'             => $user['nom'],
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

            $newUser = $userModel->getUserById($userId);
            $matricule = $newUser['matricule'] ?? 'N/A';

            \Core\NotificationService::push(
                \Core\NotificationService::TYPE_NEW_USER,
                'Nouveau patient inscrit',
                "Un nouveau patient ({$firstName} {$lastName} - Matricule: {$matricule}) s'est inscrit manuellement.",
                $userId
            );

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

        // Check if email already exists
        if (!empty($email) && $this->emailExists($email)) {
            $errors[] = 'Cet email est déjà associé à un compte.';
        }

        return $errors;
    }

    /**
     * Check if email already exists in database
     * 
     * @param string $email
     * @return bool True if email exists, false otherwise
     */
    private function emailExists(string $email): bool
    {
        try {
            $db = $this->getDatabase();
            $query = "SELECT id_PK FROM utilisateurs WHERE mail = :email LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute(['email' => $email]);
            return (bool) $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error checking email existence: ' . $e->getMessage());
            return false;
        }
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
     * Handle Google OAuth callback
     * 
     * @return void
     */
    public function googleCallback(): void
    {
        $this->ensureSession();
        
        // Handle error responses from Google
        if (isset($_GET['error'])) {
            header('Location: /integration/login?error=google_auth_failed');
            exit;
        }

        // Check for authorization code
        if (!isset($_GET['code'])) {
            header('Location: /integration/login?error=missing_auth_code');
            exit;
        }

        $code = $_GET['code'];
        
        try {
            // Exchange authorization code for access token
            $accessToken = $this->exchangeCodeForToken($code);
            
            if (!$accessToken) {
                header('Location: /integration/login?error=token_exchange_failed');
                exit;
            }

            // Get user info from Google
            $googleUser = $this->getGoogleUserInfo($accessToken);
            
            if (!$googleUser) {
                header('Location: /integration/login?error=failed_to_fetch_user_info');
                exit;
            }

            // Find or create user
            $user = $this->findOrCreateGoogleUser($googleUser);

            if (!$user) {
                header('Location: /integration/login?error=user_creation_failed');
                exit;
            }

            // Block suspended accounts from logging in via Google
            if (($user['status'] ?? 'active') === 'suspended') {
                header('Location: /integration/login?error=account_suspended');
                exit;
            }

            // Set session and redirect to dashboard
            $this->setSession($user, $googleUser['email']);
            header('Location: /integration/dashboard');
            exit;
            
        } catch (\Exception $e) {
            error_log('Google OAuth Error: ' . $e->getMessage());
            header('Location: /integration/login?error=google_auth_error');
            exit;
        }
    }

    /**
     * Exchange Google authorization code for access token
     * 
     * @param string $code Authorization code from Google
     * @return string|null Access token or null on failure
     */
    private function exchangeCodeForToken(string $code): ?string
    {
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        
        $data = [
            'code' => $code,
            'client_id' => \config::getGoogleClientId(),
            'client_secret' => \config::getGoogleClientSecret(),
            'redirect_uri' => \config::getGoogleRedirectUri(),
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['access_token'] ?? null;
    }

    /**
     * Get user information from Google using access token
     * 
     * @param string $accessToken Google access token
     * @return array|null User info or null on failure
     */
    private function getGoogleUserInfo(string $accessToken): ?array
    {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Find existing user by Google email or create new one
     * 
     * @param array $googleUser Google user info
     * @return array|null User data or null on failure
     */
    private function findOrCreateGoogleUser(array $googleUser): ?array
    {
        $db = $this->getDatabase();
        $email      = $googleUser['email']       ?? null;
        $firstName  = $googleUser['given_name']  ?? ($googleUser['name'] ?? 'User');
        $lastName   = $googleUser['family_name'] ?? '';
        $profilePic = $googleUser['picture']     ?? null;

        if (!$email) {
            error_log('Google OAuth: no email returned from Google');
            return null;
        }

        // Base SELECT used both for lookup and after insert
        $selectQuery = "
            SELECT u.id_PK, u.mail, u.prenom, u.nom, u.motdp,
                   u.matricule, u.tel, u.adresse, u.profile_pic,
                   u.status, u.onboarding_completed,
                   r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.mail = :email
            LIMIT 1
        ";

        // Check if user already exists
        $stmt = $db->prepare($selectQuery);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            // Existing user — optionally update their profile pic if they don't have one
            if (empty($user['profile_pic']) && $profilePic) {
                try {
                    $db->prepare("UPDATE utilisateurs SET profile_pic = :pic WHERE mail = :email")
                       ->execute(['pic' => $profilePic, 'email' => $email]);
                    $user['profile_pic'] = $profilePic;
                } catch (\PDOException $e) {
                    error_log('Google OAuth: could not update profile_pic: ' . $e->getMessage());
                }
            }
            return $user;
        }

        // ── New user — auto-register as Patient ──────────────────────────────
        try {
            $patientRoleId = $this->getPatientRoleId();
            $userModel     = new UserModel();
            $matricule     = $userModel->generateMatricule($patientRoleId);
            $randomPassword = bin2hex(random_bytes(16));

            $insertQuery = "
                INSERT INTO utilisateurs
                    (matricule, mail, prenom, nom, id_role, motdp, profile_pic, created_at, updated_at)
                VALUES
                    (:matricule, :email, :firstName, :lastName, :id_role, :password, :profile_pic, NOW(), NOW())
            ";

            $stmt = $db->prepare($insertQuery);
            $stmt->execute([
                'matricule'   => $matricule,
                'email'       => $email,
                'firstName'   => $firstName,
                'lastName'    => $lastName,
                'id_role'     => $patientRoleId,
                'password'    => password_hash($randomPassword, PASSWORD_BCRYPT),
                'profile_pic' => $profilePic,
            ]);

            // Set a welcome flash message for first-time Google sign-up
            $_SESSION['flash_success'] = 'Bienvenue sur MediFlow, ' . htmlspecialchars($firstName) . ' ! Votre compte a été créé automatiquement via Google.';

            // Return the newly created user
            $stmt = $db->prepare($selectQuery);
            $stmt->execute(['email' => $email]);
            $newUser = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($newUser) {
                $matricule = $newUser['matricule'] ?? 'N/A';
                \Core\NotificationService::push(
                    \Core\NotificationService::TYPE_GOOGLE_SIGNUP,
                    'Inscription via Google',
                    "Le patient {$firstName} {$lastName} (Matricule: {$matricule}) s'est inscrit avec son compte Google.",
                    $newUser['id_PK']
                );
            }

            return $newUser;

        } catch (\PDOException $e) {
            error_log('Google OAuth: error creating new user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle forgot password page display and form submission
     * 
     * @return void
     */
    public function forgotPassword(): void
    {
        $this->ensureSession();
        $errors = [];
        $success = false;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->getPost('email');
            
            if (empty($email)) {
                $errors[] = 'Veuillez entrer votre adresse email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            } else {
                $success = $this->processPasswordReset($email);
                if (!$success) {
                    $errors[] = 'Cette adresse email n\'existe pas dans notre système.';
                }
            }
        }

        $this->renderView('Front/forgot_password', [
            'errors' => $errors,
            'success' => $success,
            'email' => $email
        ]);
    }

    /**
     * Process forgot password request and send reset email
     * 
     * @param string $email User email
     * @return bool True if email sent successfully
     */
    private function processPasswordReset(string $email): bool
    {
        try {
            $db = $this->getDatabase();
            
            // Check if user exists (case-insensitive email check)
            $query = "SELECT id_PK, mail, prenom FROM utilisateurs WHERE LOWER(mail) = LOWER(:email) LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            // Generate reset token (64 characters)
            $resetToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token in database
            $insertQuery = "
                INSERT INTO password_resets (email, token, expires_at, created_at)
                VALUES (:email, :token, :expires_at, NOW())
                ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at
            ";
            
            $stmt = $db->prepare($insertQuery);
            $stmt->execute([
                'email' => $email,
                'token' => $resetToken,
                'expires_at' => $expiresAt
            ]);

            // Send reset email
            return $this->sendPasswordResetEmail($email, $user['prenom'], $resetToken);

        } catch (\PDOException $e) {
            error_log('Password reset error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email using simple file logging (development-friendly)
     * 
     * @param string $email User email
     * @param string $firstName User first name
     * @param string $token Reset token
     * @return bool True if email "sent" (logged)
     */
    private function sendPasswordResetEmail(string $email, string $firstName, string $token): bool
    {
        try {
            $resetLink = "http://localhost/integration/reset-password?token=" . urlencode($token);
            
            // Create email content
            $emailContent = [
                'to' => $email,
                'from' => 'noreply@mediflow.local',
                'subject' => 'Réinitialisation de votre mot de passe - MediFlow',
                'firstName' => $firstName,
                'resetLink' => $resetLink,
                'sentAt' => date('Y-m-d H:i:s'),
                'body' => $this->getPasswordResetEmailHTML($firstName, $resetLink)
            ];
            
            // Send via SMTP
            return $this->sendViaSMTP($email, $emailContent['subject'], $emailContent['body']);
            
        } catch (\Exception $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email via SMTP (Gmail)
     * Uses PHPMailer library
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $htmlContent HTML email body
     * @return bool Success status
     */
    private function sendViaSMTP(string $to, string $subject, string $htmlContent): bool
    {
        try {
            // Check if PHPMailer is installed
            if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                error_log('PHPMailer not installed. Run: composer require phpmailer/phpmailer');
                return false;
            }
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = \config::getSmtpHost();
            $mail->Port = \config::getSmtpPort();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            
            // Credentials
            $mail->Username = \config::getSmtpEmail();
            $mail->Password = \config::getSmtpPassword();
            
            // Check if credentials are configured
            if (strpos($mail->Username, 'YOUR_') === 0 || strpos($mail->Password, 'YOUR_') === 0) {
                error_log('SMTP credentials not configured in config.php');
                return false;
            }
            
            // Set from and to
            $mail->setFrom(\config::getSmtpEmail(), 'MediFlow');
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            
            // Send
            $mail->send();
            error_log("Email sent successfully via SMTP to: " . $to);
            return true;
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('PHPMailer error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('SMTP error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log email to file for development/testing
     * 
     * @param array $emailData Email data
     * @return void
     */
    private function logEmailToFile(array $emailData): void
    {
        $logsDir = __DIR__ . '/../logs/emails';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        
        // Create email file
        $filename = $logsDir . '/email_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                .email-container { background: white; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .email-header { background: #004d99; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .email-meta { background: #f9f9f9; padding: 10px; border-left: 4px solid #004d99; margin-bottom: 20px; font-size: 12px; }
                .email-body { line-height: 1.6; }
                .reset-link { display: inline-block; background: #004d99; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 10px 0; }
                .copy-link { background: #f0f0f0; padding: 10px; margin: 10px 0; word-break: break-all; font-size: 12px; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h2>Email Log Entry - MediFlow</h2>
                </div>
                
                <div class='email-meta'>
                    <p><strong>To:</strong> " . htmlspecialchars($emailData['to']) . "</p>
                    <p><strong>From:</strong> " . htmlspecialchars($emailData['from']) . "</p>
                    <p><strong>Subject:</strong> " . htmlspecialchars($emailData['subject']) . "</p>
                    <p><strong>Sent:</strong> " . htmlspecialchars($emailData['sentAt']) . "</p>
                </div>
                
                <div class='email-body'>
                    {$emailData['body']}
                </div>
            </div>
        </body>
        </html>
        ";
        
        file_put_contents($filename, $htmlContent);
    }

    /**
     * Get password reset email HTML content
     * 
     * @param string $firstName User first name
     * @param string $resetLink Reset link
     * @return string HTML email content
     */
    private function getPasswordResetEmailHTML(string $firstName, string $resetLink): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 500px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #004d99, #1565c0); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #e0e0e0; }
                .footer { background: #e0e0e0; padding: 15px; border-radius: 0 0 8px 8px; font-size: 12px; color: #666; text-align: center; }
                .btn { display: inline-block; background: #004d99; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .warning { color: #dc2626; font-size: 12px; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Réinitialisation de mot de passe</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($firstName) . "</strong>,</p>
                    
                    <p>Vous avez demandé la réinitialisation de votre mot de passe MediFlow.</p>
                    
                    <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
                    
                    <center>
                        <a href='" . htmlspecialchars($resetLink) . "' class='btn'>Réinitialiser mon mot de passe</a>
                    </center>
                    
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <p style='word-break: break-all; background: #fff; padding: 10px; border: 1px solid #ddd; font-size: 12px;'>" . htmlspecialchars($resetLink) . "</p>
                    
                    <div class='warning'>
                        <strong>⚠️ Sécurité :</strong><br>
                        - Ce lien expire dans 1 heure<br>
                        - Si vous n'avez pas demandé cette réinitialisation, ignorez cet email<br>
                        - Ne partagez jamais ce lien avec quelqu'un d'autre
                    </div>
                </div>
                <div class='footer'>
                    <p>© 2026 MediFlow. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Handle password reset page display and form submission
     * 
     * @return void
     */
    public function resetPassword(): void
    {
        $this->ensureSession();
        $errors = [];
        $success = false;
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $errors[] = 'Token de réinitialisation manquant.';
        } else {
            // Verify token exists and is not expired
            $isValidToken = $this->validateResetToken($token);
            if (!$isValidToken) {
                $errors[] = 'Ce lien de réinitialisation est invalide ou a expiré. Veuillez demander un nouveau lien.';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirmPassword');

            if (empty($password) || empty($confirmPassword)) {
                $errors[] = 'Veuillez entrer un mot de passe.';
            } elseif ($password !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            } else {
                $success = $this->updatePasswordWithToken($token, $password);
                if ($success) {
                    // Clear session and redirect to login
                    session_destroy();
                    $_SESSION = [];
                    header('Location: /integration/login?success=password_reset');
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la réinitialisation du mot de passe.';
                }
            }
        }

        $this->renderView('Front/reset_password', [
            'errors' => $errors,
            'success' => $success,
            'token' => $token
        ]);
    }

    /**
     * Validate password reset token
     * 
     * @param string $token Reset token
     * @return bool True if token is valid and not expired
     */
    private function validateResetToken(string $token): bool
    {
        try {
            $db = $this->getDatabase();
            
            $query = "
                SELECT email FROM password_resets 
                WHERE token = :token 
                AND expires_at > NOW() 
                LIMIT 1
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return (bool) $result;

        } catch (\PDOException $e) {
            error_log('Token validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password using reset token
     * 
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool True if password updated successfully
     */
    private function updatePasswordWithToken(string $token, string $newPassword): bool
    {
        try {
            $db = $this->getDatabase();

            // Get email from token
            $query = "SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            $email = $result['email'];
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update user password
            $updateQuery = "UPDATE utilisateurs SET motdp = :password WHERE mail = :email";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([
                'password' => $hashedPassword,
                'email' => $email
            ]);

            // Delete used token
            $deleteQuery = "DELETE FROM password_resets WHERE token = :token";
            $stmt = $db->prepare($deleteQuery);
            $stmt->execute(['token' => $token]);

            // Get matricule
            $userQuery = "SELECT matricule, id_PK FROM utilisateurs WHERE mail = :email";
            $stmt = $db->prepare($userQuery);
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
            $matricule = $userData['matricule'] ?? 'N/A';
            $userId = $userData['id_PK'] ?? null;

            \Core\NotificationService::push(
                \Core\NotificationService::TYPE_PASSWORD_CHANGE,
                'Mot de passe réinitialisé',
                "L'utilisateur (Matricule: {$matricule}) a réinitialisé son mot de passe.",
                $userId
            );

            return true;

        } catch (\PDOException $e) {
            error_log('Password update error: ' . $e->getMessage());
            return false;
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

        // Login and register are full standalone pages — no header/footer wrapper needed
        $standaloneViews = ['Front/login', 'Front/register', 'Front/forgot_password', 'Front/reset_password'];
        if (in_array($view, $standaloneViews)) {
            include __DIR__ . '/../Views/' . $view . '.php';
        } else {
            include __DIR__ . '/../Views/layout/header.php';
            include __DIR__ . '/../Views/' . $view . '.php';
            include __DIR__ . '/../Views/layout/footer.php';
        }
    }
}
