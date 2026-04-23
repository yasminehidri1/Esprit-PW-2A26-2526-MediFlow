<?php
/**
 * AdminController — Admin dashboard and management views.
 */

require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {

    private AdminModel $adminModel;
    private int $adminId;
    private array $adminInfo;

    public function __construct() {
        $this->adminModel = new AdminModel();

        session_start_if_not_started();
        $this->adminId   = $_SESSION['user_id']   ?? 1;  // Default to admin user
        $this->adminInfo = $_SESSION['user_info'] ?? [
            'prenom' => 'Admin',
            'nom'    => 'Principal',
            'mail'   => 'admin@mediflow.com',
        ];
    }

    // ── Dashboard ─────────────────────────────────────────────────

    public function dashboard(): void {
        $doctorStats       = $this->adminModel->getDoctorStats();
        $consultationStats = $this->adminModel->getActiveConsultations();
        $prescriptionStats = $this->adminModel->getTotalPrescriptions();
        $revenueStats      = $this->adminModel->getRevenueOverview();

        $featuredDoctors   = $this->adminModel->getFeaturedStaff(3);
        $recentConsultations = $this->adminModel->getRecentConsultations(4);
        $prescriptionData  = $this->adminModel->getPrescriptionStats();

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        // Flash message from POST redirect
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/dashboard.php';
    }

    // ── Doctors List ──────────────────────────────────────────────

    public function doctorsList(): void {
        $perPage    = 10;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;

        $doctors    = $this->adminModel->getAllDoctors($perPage, $offset);
        $totalCount = $this->adminModel->countDoctors();
        $totalPages = (int) ceil($totalCount / $perPage);

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/doctors/liste.php';
    }

    // ── Doctor Details & Edit ─────────────────────────────────────

    public function editDoctor(): void {
        $doctorId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($doctorId === 0) {
            $this->redirect('?page=admin&action=doctors');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => htmlspecialchars(trim($_POST['nom'] ?? '')),
                'prenom' => htmlspecialchars(trim($_POST['prenom'] ?? '')),
                'mail' => htmlspecialchars(trim($_POST['mail'] ?? '')),
                'tel' => htmlspecialchars(trim($_POST['tel'] ?? '')),
                'adresse' => htmlspecialchars(trim($_POST['adresse'] ?? '')),
            ];

            $this->adminModel->updateDoctor($doctorId, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Médecin mis à jour avec succès.'];
            $this->redirect('?page=admin&action=doctors');
        } else {
            $doctor = $this->adminModel->getDoctorById($doctorId);
            if (!$doctor) {
                $this->redirect('?page=admin&action=doctors');
            }

            $admin      = $this->adminInfo;
            $adminId    = $this->adminId;
            $activePage = 'admin';

            require __DIR__ . '/../views/backoffice/admin/doctors/edit.php';
        }
    }

    public function deleteDoctor(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=admin&action=doctors');
        }

        $doctorId = (int)($_POST['id'] ?? 0);
        if ($doctorId > 0) {
            $this->adminModel->deleteDoctor($doctorId);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Médecin supprimé avec succès.'];
        }
        $this->redirect('?page=admin&action=doctors');
    }

    // ── Doctor's Patients Management ──────────────────────────────

    public function viewDoctorPatients(): void {
        $doctorId = (int)($_GET['doctor_id'] ?? 0);

        if ($doctorId <= 0) {
            $this->redirect('?page=admin&action=doctors');
        }

        // Get doctor info
        $doctor = $this->adminModel->getDoctorById($doctorId);
        if (!$doctor) {
            $this->redirect('?page=admin&action=doctors');
        }

        // Get all patients of this doctor
        $patients = $this->adminModel->getPatientsByDoctor($doctorId);

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';
        $flash      = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/doctors/patients.php';
    }

    // ── Patient Details (AJAX) ────────────────────────────────────

    public function getDoctorPatientDetailsAjax(): void {
        header('Content-Type: application/json; charset=utf-8');

        $patientId = (int)($_GET['patient_id'] ?? 0);
        $doctorId = (int)($_GET['doctor_id'] ?? 0);

        if ($patientId <= 0 || $doctorId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Patient ID and Doctor ID are required']);
            exit;
        }

        // Get patient consultations and prescriptions
        $consultations = $this->adminModel->getPatientConsultations($patientId, $doctorId);
        $prescriptions = $this->adminModel->getPatientPrescriptions($patientId, $doctorId);

        echo json_encode([
            'consultations' => $consultations,
            'prescriptions' => $prescriptions,
        ]);
        exit;
    }

    // ── Doctor's Patients AJAX ────────────────────────────────────

    public function getDoctorPatientsAjax(): void {
        header('Content-Type: application/json; charset=utf-8');

        $doctorId = (int)($_GET['doctor_id'] ?? 0);

        if ($doctorId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Doctor ID is required']);
            exit;
        }

        $patients = $this->adminModel->getPatientsByDoctor($doctorId);

        echo json_encode([
            'patients' => $patients,
        ]);
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
