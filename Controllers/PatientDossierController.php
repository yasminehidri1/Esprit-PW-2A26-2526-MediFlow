<?php
namespace Controllers;
require_once __DIR__ . '/../Models/PatientModel.php';
require_once __DIR__ . '/../Models/DemandeOrdonnanceModel.php';
require_once __DIR__ . '/../Models/NotificationModel.php';
require_once __DIR__ . '/../Services/ClaudeService.php';

class PatientDossierController {
    private \PatientModel $patientModel;
    private \DemandeOrdonnanceModel $demandeModel;
    private int $patientId;

    public function __construct() {
        $this->patientModel = new \PatientModel();
        $this->demandeModel  = new \DemandeOrdonnanceModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) { header('Location: /integration/login'); exit; }
        // Patient's own ID comes from session; medecins/admin can view via ?id=
        $role = $_SESSION['user']['role'] ?? '';
        if (in_array($role, ['Admin', 'Administrateur', 'Medecin'], true)) {
            $this->patientId = (int)($_GET['id'] ?? $_SESSION['user']['id'] ?? 0);
        } else {
            $this->patientId = (int)($_SESSION['user']['id'] ?? 0);
        }
    }

    public function dashboard(): void {
        $patient = $this->patientModel->getPatientById($this->patientId);
        if (!$patient) { http_response_code(404); echo '<p style="padding:2rem;color:red">Patient introuvable (ID #' . $this->patientId . ').</p>'; exit; }

        try { $consultations = $this->patientModel->getPatientConsultations($this->patientId); } catch (\Exception $e) { $consultations = []; }
        try { $prescriptions = $this->patientModel->getPatientPrescriptions($this->patientId); } catch (\Exception $e) { $prescriptions = []; }
        try { $doctors = $this->patientModel->getPatientDoctors($this->patientId); } catch (\Exception $e) { $doctors = []; }
        try { $allDoctors = $this->patientModel->getAllDoctors(); } catch (\Exception $e) { $allDoctors = []; }
        try { $vitals = $this->patientModel->getLatestVitals($this->patientId); } catch (\Exception $e) { $vitals = null; }

        $currentView = 'dossier_medical/patient_dossier';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function updateProfile(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false]); return; }
        $data = json_decode(file_get_contents('php://input'), true);
        $errors = [];
        if (empty($data['prenom']) || strlen($data['prenom']) < 2) $errors['prenom'] = 'Prénom invalide (min. 2 caractères)';
        if (empty($data['nom'])    || strlen($data['nom'])    < 2) $errors['nom']    = 'Nom invalide (min. 2 caractères)';
        if (empty($data['email'])  || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Adresse email invalide';
        if (!empty($data['tel']) && !preg_match('/^[\d\s\+\-\(\)]{6,20}$/', $data['tel'])) $errors['tel'] = 'Numéro de téléphone invalide';
        if (!empty($errors)) { http_response_code(400); echo json_encode(['success' => false, 'errors' => $errors]); return; }
        $tel     = trim($data['tel']     ?? '');
        $adresse = trim($data['adresse'] ?? '');
        try {
            $this->patientModel->updateProfile($this->patientId, $data['prenom'], $data['nom'], $data['email'], $tel, $adresse);
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour', 'prenom' => $data['prenom'], 'nom' => $data['nom']]);
        } catch (\Exception $e) { http_response_code(500); echo json_encode(['success' => false, 'error' => 'Erreur serveur']); }
    }

    public function chatbot(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false]); return; }
        $data     = json_decode(file_get_contents('php://input'), true);
        $symptoms = trim($data['symptoms'] ?? '');
        if (strlen($symptoms) < 5)    { http_response_code(400); echo json_encode(['success' => false, 'error' => 'Décrivez vos symptômes (min. 5 caractères).']); return; }
        if (strlen($symptoms) > 1000) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'Description trop longue (max. 1000 caractères).']); return; }

        // Détecter les messages non-médicaux (salutations, questions générales)
        $greetings = ['bonjour','bonsoir','salut','hello','hi','merci','ok','oui','non','ca va','ça va','comment','qui es','quoi'];
        $lower = mb_strtolower($symptoms, 'UTF-8');
        foreach ($greetings as $g) {
            if (trim($lower) === $g || trim($lower) === $g.'!') {
                echo json_encode([
                    'success'  => true,
                    'urgence'  => 'none',
                    'conseil'  => '',
                    'signes_alerte' => [],
                    'message'  => 'Bonjour ! Je suis votre assistant médical de triage. Décrivez-moi vos symptômes (ex: douleur, fièvre, durée) et je vous conseillerai sur la démarche à suivre.',
                ]);
                return;
            }
        }
        try {
            $claude  = new \Services\ClaudeService();
            $result  = $claude->analyzeSymptoms($symptoms);
            $debug   = $result['_debug'] ?? null;
            unset($result['_debug']);
            $payload = array_merge(['success' => true], $result);
            if ($debug) $payload['_debug'] = $debug; // visible en dev pour diagnostic
            echo json_encode($payload);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Service temporairement indisponible.', '_debug' => $e->getMessage()]);
        }
    }

    public function requestPrescription(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false]); return; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['medecin_id'])) { http_response_code(400); echo json_encode(['success' => false, 'errors' => ['medecin' => 'Sélectionnez un médecin']]); return; }
        if (empty($data['description']) || strlen($data['description']) < 10) { http_response_code(400); echo json_encode(['success' => false, 'errors' => ['description' => 'Description trop courte']]); return; }
        $validUrgences = ['normale', 'urgent', 'tres_urgent'];
        $urgence = in_array($data['urgence'] ?? '', $validUrgences, true) ? $data['urgence'] : 'normale';
        try {
            $medecinId  = (int)$data['medecin_id'];
            $newId      = $this->demandeModel->createDemande($this->patientId, $medecinId, $data['description'], $urgence);

            // Analyse IA (non bloquante)
            try {
                $claude = new \Services\ClaudeService();
                $ai     = $claude->analyzeUrgency($data['description'], $urgence);
                $this->demandeModel->updateAiAnalysis($newId, $ai['urgence'], $ai['justification']);
            } catch (\Throwable) {}

            // Notification pour le médecin destinataire
            try {
                $patientName = trim(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? 'Un patient'));
                $notifModel  = new \NotificationModel();
                $notifModel->add(
                    $medecinId,
                    'new_demande',
                    'Nouvelle demande d\'ordonnance',
                    "{$patientName} vous a envoyé une demande d'ordonnance.",
                    $newId
                );
            } catch (\Throwable) {}

            echo json_encode(['success' => true, 'message' => 'Demande envoyée au médecin']);
        } catch (\Exception $e) { http_response_code(500); echo json_encode(['success' => false, 'error' => 'Erreur serveur']); }
    }
}
