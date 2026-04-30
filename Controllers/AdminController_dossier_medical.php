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

        $featuredDoctors     = $this->adminModel->getFeaturedStaff(3);
        $recentConsultations = $this->adminModel->getRecentConsultations(4);
        $prescriptionData    = $this->adminModel->getPrescriptionStats();

        $totalPatients         = $this->adminModel->getTotalPatients();
        $topDoctors            = $this->adminModel->getTopDoctors(5);
        $consultationsByType   = $this->adminModel->getConsultationsByType();
        $prescriptionsByStatus = $this->adminModel->getPrescriptionsByStatus();
        $weeklyComparison      = $this->adminModel->getWeeklyComparison();
        $consultationCompletion = $this->adminModel->getConsultationCompletion();

        $admin      = $this->adminInfo;
        $adminId    = $this->adminId;
        $activePage = 'admin';

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/admin/dashboard.php';
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
                'nom'     => htmlspecialchars(trim($_POST['nom']     ?? '')),
                'prenom'  => htmlspecialchars(trim($_POST['prenom']  ?? '')),
                'mail'    => htmlspecialchars(trim($_POST['mail']    ?? '')),
                'tel'     => htmlspecialchars(trim($_POST['tel']     ?? '')),
                'adresse' => htmlspecialchars(trim($_POST['adresse'] ?? '')),
            ];

            $errors = [];
            if (strlen($data['nom']) < 2 || strlen($data['nom']) > 50)
                $errors['nom'] = 'Le nom doit contenir entre 2 et 50 caractères.';
            if (strlen($data['prenom']) < 2 || strlen($data['prenom']) > 50)
                $errors['prenom'] = 'Le prénom doit contenir entre 2 et 50 caractères.';
            if (empty($data['mail']) || !filter_var($data['mail'], FILTER_VALIDATE_EMAIL))
                $errors['mail'] = 'Adresse email invalide.';
            if (!empty($data['tel']) && !preg_match('/^[\d\s\-+().]{10,20}$/', $data['tel']))
                $errors['tel'] = 'Numéro de téléphone invalide (10 à 20 caractères).';
            if (!empty($data['adresse']) && strlen($data['adresse']) > 200)
                $errors['adresse'] = "L'adresse ne doit pas dépasser 200 caractères.";

            if (!empty($errors)) {
                $doctor     = array_merge(['id_PK' => $doctorId], $data);
                $validation_errors = $errors;
                $admin      = $this->adminInfo;
                $adminId    = $this->adminId;
                $activePage = 'admin';
                require __DIR__ . '/../views/backoffice/admin/doctors/edit.php';
                return;
            }

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

        require __DIR__ . '/../views/backoffice/admin/doctors/patients_list.php';
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
