<?php
namespace Controllers;
require_once __DIR__ . '/../Models/PatientModel.php';
require_once __DIR__ . '/../Models/DemandeOrdonnanceModel.php';

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
        if (empty($data['prenom']) || strlen($data['prenom']) < 2) $errors['prenom'] = 'Prénom invalide';
        if (empty($data['nom'])    || strlen($data['nom'])    < 2) $errors['nom']    = 'Nom invalide';
        if (empty($data['email'])  || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide';
        if (!empty($errors)) { http_response_code(400); echo json_encode(['success' => false, 'errors' => $errors]); return; }
        try {
            $this->patientModel->updateProfile($this->patientId, $data['prenom'], $data['nom'], $data['email']);
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour']);
        } catch (\Exception $e) { http_response_code(500); echo json_encode(['success' => false, 'error' => 'Erreur serveur']); }
    }

    public function requestPrescription(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false]); return; }
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['medecin_id'])) { http_response_code(400); echo json_encode(['success' => false, 'errors' => ['medecin' => 'Sélectionnez un médecin']]); return; }
        if (empty($data['description']) || strlen($data['description']) < 10) { http_response_code(400); echo json_encode(['success' => false, 'errors' => ['description' => 'Description trop courte']]); return; }
        try {
            $this->demandeModel->createDemande($this->patientId, (int)$data['medecin_id'], $data['description']);
            echo json_encode(['success' => true, 'message' => 'Demande envoyée au médecin']);
        } catch (\Exception $e) { http_response_code(500); echo json_encode(['success' => false, 'error' => 'Erreur serveur']); }
    }
}
