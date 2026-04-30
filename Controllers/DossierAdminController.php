<?php
namespace Controllers;
require_once __DIR__ . '/../Models/AdminModel.php';

class DossierAdminController {
    private \AdminModel $adminModel;
    private int   $adminId;
    private array $adminInfo;

    public function __construct() {
        $this->adminModel = new \AdminModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) { header('Location: /integration/login'); exit; }
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, ['Admin', 'Administrateur'], true)) {
            http_response_code(403); echo '<p style="color:red;padding:2rem">Accès réservé à l\'Admin.</p>'; exit;
        }
        $user = $_SESSION['user'];
        $this->adminId   = (int)($user['id'] ?? 0);
        $this->adminInfo = ['prenom' => $user['prenom'] ?? 'Admin', 'nom' => $user['nom'] ?? '', 'mail' => $user['mail'] ?? ''];
    }

    // ── Admin Dashboard ───────────────────────────────────────────
    public function dashboard(): void {
        $doctorStats            = $this->adminModel->getDoctorStats();
        $consultationStats      = $this->adminModel->getActiveConsultations();
        $prescriptionStats      = $this->adminModel->getTotalPrescriptions();
        $recentConsultations    = $this->adminModel->getRecentConsultations(6);
        $prescriptionData       = $this->adminModel->getPrescriptionStats();
        $totalPatients          = $this->adminModel->getTotalPatients();
        $topDoctors             = $this->adminModel->getTopDoctors(5);
        $consultationsByType    = $this->adminModel->getConsultationsByType();
        $prescriptionsByStatus  = $this->adminModel->getPrescriptionsByStatus();
        $weeklyComparison       = $this->adminModel->getWeeklyComparison();
        $consultationCompletion = $this->adminModel->getConsultationCompletion();
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/admin_dashboard';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Doctors List ──────────────────────────────────────────────
    public function doctorsList(): void {
        $perPage   = 10;
        $page      = max(1, (int)($_GET['p'] ?? 1));
        $offset    = ($page - 1) * $perPage;
        $sortBy    = $_GET['sort']  ?? 'prenom';
        $sortOrder = $_GET['order'] ?? 'ASC';
        $doctors    = $this->adminModel->getAllDoctors($perPage, $offset, $sortBy, $sortOrder);
        $totalCount = $this->adminModel->countDoctors();
        $totalPages = (int) ceil($totalCount / $perPage);
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/admin_doctors_liste';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Edit Doctor ───────────────────────────────────────────────
    public function editDoctor(): void {
        $doctorId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($doctorId === 0) $this->redirect('/integration/dossier/admin/doctors');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['nom' => htmlspecialchars(trim($_POST['nom'] ?? '')), 'prenom' => htmlspecialchars(trim($_POST['prenom'] ?? '')), 'mail' => htmlspecialchars(trim($_POST['mail'] ?? '')), 'tel' => htmlspecialchars(trim($_POST['tel'] ?? '')), 'adresse' => htmlspecialchars(trim($_POST['adresse'] ?? ''))];
            $errors = [];
            if (strlen($data['nom']) < 2)   $errors['nom']    = 'Nom invalide (min 2 car.)';
            if (strlen($data['prenom']) < 2) $errors['prenom'] = 'Prénom invalide (min 2 car.)';
            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) $errors['mail'] = 'Email invalide';
            if (!empty($errors)) {
                $doctor = array_merge(['id_PK' => $doctorId], $data);
                $validation_errors = $errors; $admin = $this->adminInfo; $adminId = $this->adminId;
                $currentView = 'dossier_medical/admin_doctors_edit';
                include __DIR__ . '/../Views/Back/layout.php'; return;
            }
            $this->adminModel->updateDoctor($doctorId, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Médecin mis à jour.'];
            $this->redirect('/integration/dossier/admin/doctors');
        } else {
            $doctor = $this->adminModel->getDoctorById($doctorId);
            if (!$doctor) $this->redirect('/integration/dossier/admin/doctors');
            $validation_errors = []; $admin = $this->adminInfo; $adminId = $this->adminId;
            $currentView = 'dossier_medical/admin_doctors_edit';
            include __DIR__ . '/../Views/Back/layout.php';
        }
    }

    // ── Delete Doctor ─────────────────────────────────────────────
    public function deleteDoctor(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/integration/dossier/admin/doctors');
        $doctorId = (int)($_POST['id'] ?? 0);
        if ($doctorId > 0) { $this->adminModel->deleteDoctor($doctorId); $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Médecin supprimé.']; }
        $this->redirect('/integration/dossier/admin/doctors');
    }

    // ── Doctor's Patients ─────────────────────────────────────────
    public function viewDoctorPatients(): void {
        $doctorId = (int)($_GET['doctor_id'] ?? 0);
        if ($doctorId <= 0) $this->redirect('/integration/dossier/admin/doctors');
        $doctor = $this->adminModel->getDoctorById($doctorId);
        if (!$doctor) $this->redirect('/integration/dossier/admin/doctors');
        $patients   = $this->adminModel->getPatientsByDoctor($doctorId);
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/admin_doctors_patients';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Consultations List (Admin) ────────────────────────────────
    public function listConsultations(): void {
        $perPage    = 15;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;
        $consultations = $this->adminModel->getAllConsultations($perPage, $offset);
        $totalCount    = $this->adminModel->countConsultations();
        $totalPages    = (int) ceil($totalCount / $perPage);
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $currentView = 'dossier_medical/admin_consultations_liste';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Consultation View (Admin) ─────────────────────────────────
    public function viewConsultation(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) $this->redirect('/integration/dossier/admin/consultations');
        $consultation = $this->adminModel->getConsultationDetail($id);
        if (!$consultation) $this->redirect('/integration/dossier/admin/consultations');
        $antecedents = json_decode($consultation['antecedents'] ?? '[]', true) ?: [];
        $allergies   = json_decode($consultation['allergies']   ?? '[]', true) ?: [];
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $currentView = 'dossier_medical/admin_consultation_view';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Ordonnances List (Admin) ──────────────────────────────────
    public function listOrdonnances(): void {
        $perPage    = 15;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;
        $ordonnances = $this->adminModel->getAllPrescriptions($perPage, $offset);
        $totalCount  = $this->adminModel->countPrescriptions();
        $totalPages  = (int) ceil($totalCount / $perPage);
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $currentView = 'dossier_medical/admin_ordonnances_liste';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Ordonnance View (Admin) ───────────────────────────────────
    public function viewOrdonnance(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) $this->redirect('/integration/dossier/admin/ordonnances');
        $ordonnance = $this->adminModel->getPrescriptionDetail($id);
        if (!$ordonnance) $this->redirect('/integration/dossier/admin/ordonnances');
        $medicaments = json_decode($ordonnance['medicaments'] ?? '[]', true) ?: [];
        $admin = $this->adminInfo; $adminId = $this->adminId;
        $currentView = 'dossier_medical/admin_ordonnance_view';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── AJAX: Doctor's Patients ───────────────────────────────────
    public function getDoctorPatientsAjax(): void {
        header('Content-Type: application/json; charset=utf-8');
        $doctorId = (int)($_GET['doctor_id'] ?? 0);
        if ($doctorId <= 0) { http_response_code(400); echo json_encode(['error' => 'doctor_id requis']); exit; }
        echo json_encode(['patients' => $this->adminModel->getPatientsByDoctor($doctorId)]);
        exit;
    }

    private function redirect(string $url): never { header("Location: {$url}"); exit; }
}
