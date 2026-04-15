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

    // ── Consultations List ────────────────────────────────────────

    public function consultationsList(): void {
        $perPage    = 10;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;

        $consultations = $this->adminModel->getAllConsultations($perPage, $offset);
        $totalCount    = $this->adminModel->countConsultations();
        $totalPages    = (int) ceil($totalCount / $perPage);

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/consultations/liste.php';
    }

    // ── Prescriptions List ────────────────────────────────────────

    public function prescriptionsList(): void {
        $perPage    = 10;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;

        $prescriptions = $this->adminModel->getAllPrescriptions($perPage, $offset);
        $totalCount    = $this->adminModel->countPrescriptions();
        $totalPages    = (int) ceil($totalCount / $perPage);

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/ordonnances/liste.php';
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

    // ── Consultation Details ──────────────────────────────────────

    public function viewConsultation(): void {
        $consultationId = (int)($_GET['id'] ?? 0);
        if ($consultationId === 0) {
            $this->redirect('?page=admin&action=consultations');
        }

        $consultation = $this->adminModel->getConsultationDetail($consultationId);
        if (!$consultation) {
            $this->redirect('?page=admin&action=consultations');
        }

        $antecedents = json_decode($consultation['antecedents'] ?? '[]', true) ?: [];
        $allergies = json_decode($consultation['allergies'] ?? '[]', true) ?: [];

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        require __DIR__ . '/../views/backoffice/admin/consultations/view.php';
    }

    public function deleteConsultation(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=admin&action=consultations');
        }

        $consultationId = (int)($_POST['id'] ?? 0);
        if ($consultationId > 0) {
            $this->adminModel->deleteConsultation($consultationId);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Consultation supprimée.'];
        }
        $this->redirect('?page=admin&action=consultations');
    }

    // ── Prescription Details & Status ─────────────────────────────

    public function viewPrescription(): void {
        $prescriptionId = (int)($_GET['id'] ?? 0);
        if ($prescriptionId === 0) {
            $this->redirect('?page=admin&action=prescriptions');
        }

        $prescription = $this->adminModel->getPrescriptionDetail($prescriptionId);
        if (!$prescription) {
            $this->redirect('?page=admin&action=prescriptions');
        }

        $medicaments = json_decode($prescription['medicaments'] ?? '[]', true) ?: [];

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        require __DIR__ . '/../views/backoffice/admin/ordonnances/view.php';
    }

    public function updatePrescriptionStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=admin&action=prescriptions');
        }

        $prescriptionId = (int)($_POST['id'] ?? 0);
        $status = htmlspecialchars(trim($_POST['status'] ?? 'active'));

        if ($prescriptionId > 0) {
            $this->adminModel->updatePrescriptionStatus($prescriptionId, $status);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Statut mis à jour.'];
        }
        $this->redirect('?page=admin&action=prescriptions');
    }

    public function deletePrescription(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=admin&action=prescriptions');
        }

        $prescriptionId = (int)($_POST['id'] ?? 0);
        if ($prescriptionId > 0) {
            $this->adminModel->deletePrescription($prescriptionId);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Ordonnance supprimée.'];
        }
        $this->redirect('?page=admin&action=prescriptions');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
