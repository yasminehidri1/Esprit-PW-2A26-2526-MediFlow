<?php
/**
 * OrdonnanceController — Handles Prescription CRUD.
 */

require_once __DIR__ . '/../models/OrdonnanceModel.php';
require_once __DIR__ . '/../models/ConsultationModel.php';

class OrdonnanceController {

    private OrdonnanceModel   $ordonnanceModel;
    private ConsultationModel $consultationModel;
    private int   $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->ordonnanceModel   = new OrdonnanceModel();
        $this->consultationModel = new ConsultationModel();

        session_start_if_not_started();
        $this->medecinId   = $_SESSION['user_id']   ?? 2;
        $this->medecinInfo = $_SESSION['user_info']  ?? [
            'prenom' => 'Jean',
            'nom'    => 'Dupont',
            'mail'   => 'medecin1@mediflow.com',
        ];
    }

    // ── View Ordonnance ───────────────────────────────────────────

    public function listAll(): void {
        $grouped   = $this->ordonnanceModel->getAllGroupedByPatient($this->medecinId);
        $totalOrdo = array_sum(array_map(fn($g) => count($g['ordonnances']), $grouped));
        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/ordonnance/liste.php';
    }

    // ── View Single Ordonnance ────────────────────────────────────

    public function view(): void {
        // Accept either ordonnance id or consultation id
        $ordonnanceId  = (int)($_GET['id']          ?? 0);
        $consultationId = (int)($_GET['consult_id'] ?? 0);

        if ($ordonnanceId > 0) {
            $ordonnance = $this->ordonnanceModel->getById($ordonnanceId);
        } elseif ($consultationId > 0) {
            $ordonnance = $this->ordonnanceModel->getByConsultation($consultationId);
        } else {
            $this->redirect('?page=patients');
        }

        if (!$ordonnance) {
            // No prescription yet — redirect to create form
            $this->redirect("?page=ordonnance&action=add&consult_id={$consultationId}");
        }

        // Decode medication JSON
        $medicaments  = json_decode($ordonnance['medicaments'] ?? '[]', true) ?: [];
        $consultation = $this->consultationModel->getConsultationById((int)$ordonnance['id_consultation']);
        $patient      = $this->consultationModel->getPatientById((int)$ordonnance['id_patient']);

        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/ordonnance/view.php';
    }

    // ── Add Ordonnance Form (GET) / Save (POST) ───────────────────

    public function add(): void {
        $consultationId = (int)($_GET['consult_id'] ?? $_POST['id_consultation'] ?? 0);
        if ($consultationId === 0) $this->redirect('?page=patients');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicaments = $this->buildMedicamentsJson();

            $data = [
                'id_consultation'   => $consultationId,
                'numero_ordonnance' => '', // auto-generated in model
                'date_emission'     => $_POST['date_emission'] ?? date('Y-m-d'),
                'medicaments'       => $medicaments,
                'note_pharmacien'   => htmlspecialchars(trim($_POST['note_pharmacien'] ?? '')),
                'statut'            => 'active',
            ];

            // Validate data
            $errors = $this->validateOrdonnance($data);
            if (!empty($errors)) {
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                $this->redirect("?page=ordonnance&action=add&consult_id={$consultationId}");
            }

            $ordonnanceId = $this->ordonnanceModel->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Ordonnance créée avec succès.'];
            $this->redirect("?page=ordonnance&id={$ordonnanceId}");
        }

        // GET — show blank form
        $consultation = $this->consultationModel->getConsultationById($consultationId);
        if (!$consultation) $this->redirect('?page=patients');

        $patient   = $this->consultationModel->getPatientById((int)$consultation['id_patient']);
        $mode      = 'add';
        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;
        $ordonnance = null;
        $medicaments = [];
        $validation_errors = $_SESSION['validation_errors'] ?? [];
        unset($_SESSION['validation_errors']);

        require __DIR__ . '/../views/backoffice/ordonnance/form.php';
    }

    // ── Edit Ordonnance Form (GET) / Save (POST) ──────────────────

    public function edit(): void {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id === 0) $this->redirect('?page=patients');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicaments = $this->buildMedicamentsJson();
            $data = [
                'date_emission'   => $_POST['date_emission'] ?? date('Y-m-d'),
                'medicaments'     => $medicaments,
                'note_pharmacien' => htmlspecialchars(trim($_POST['note_pharmacien'] ?? '')),
                'statut'          => $_POST['statut'] ?? 'active',
            ];

            // Validate data
            $errors = $this->validateOrdonnance($data);
            if (!empty($errors)) {
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                $this->redirect("?page=ordonnance&action=edit&id={$id}");
            }

            $this->ordonnanceModel->update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Ordonnance mise à jour.'];
            $this->redirect("?page=ordonnance&id={$id}");
        }

        // GET — show edit form pre-filled
        $ordonnance  = $this->ordonnanceModel->getById($id);
        if (!$ordonnance) $this->redirect('?page=patients');

        $consultation = $this->consultationModel->getConsultationById((int)$ordonnance['id_consultation']);
        $patient      = $this->consultationModel->getPatientById((int)$ordonnance['id_patient']);
        $medicaments  = json_decode($ordonnance['medicaments'] ?? '[]', true) ?: [];
        $mode         = 'edit';
        $medecin      = $this->medecinInfo;
        $medecinId    = $this->medecinId;
        $validation_errors = $_SESSION['validation_errors'] ?? [];
        unset($_SESSION['validation_errors']);

        require __DIR__ . '/../views/backoffice/ordonnance/form.php';
    }

    // ── Delete Ordonnance ─────────────────────────────────────────

    public function delete(): void {
        $id        = (int)($_POST['id']         ?? 0);
        $patientId = (int)($_POST['patient_id'] ?? 0);

        if ($id > 0) {
            $this->ordonnanceModel->delete($id);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Ordonnance supprimée.'];
        }
        $this->redirect("?page=dossier&patient_id={$patientId}");
    }

    // ── Helpers ───────────────────────────────────────────────────

    /** Validate ordonnance data. Returns array of errors (empty = valid). */
    private function validateOrdonnance(array $data): array {
        $errors = [];

        if (empty($data['id_consultation']) || !is_numeric($data['id_consultation'])) {
            $errors['id_consultation'] = 'Consultation invalide';
        }

        if (empty($data['date_emission'])) {
            $errors['date_emission'] = 'La date est requise';
        } else {
            $d = \DateTime::createFromFormat('Y-m-d', $data['date_emission']);
            if (!$d || $d > new \DateTime('today')) {
                $errors['date_emission'] = 'Date invalide ou future';
            }
        }

        // Must have at least one medication
        if (empty($data['medicaments'])) {
            $errors['medicaments'] = 'Au moins un médicament est requis';
        } else {
            $meds = json_decode($data['medicaments'], true);
            if (!is_array($meds) || count($meds) === 0) {
                $errors['medicaments'] = 'Au moins un médicament est requis';
            } else {
                foreach ($meds as $i => $med) {
                    if (empty($med['nom'])) {
                        $errors['medicaments'] = 'Le nom du médicament ne peut pas être vide';
                        break;
                    }
                    if (strlen($med['nom']) > 100) {
                        $errors['medicaments'] = 'Le nom du médicament est trop long';
                        break;
                    }
                }
            }
        }

        if (!empty($data['note_pharmacien']) && strlen($data['note_pharmacien']) > 500) {
            $errors['note_pharmacien'] = 'La note du pharmacien est trop longue';
        }

        // Validate statut if present
        if (!empty($data['statut'])) {
            if (!in_array($data['statut'], ['active','archivee','annulee'])) {
                $errors['statut'] = 'Statut invalide';
            }
        }

        return $errors;
    }

    /** Build medication JSON array from repeated POST fields. */
    private function buildMedicamentsJson(): string {
        $noms           = $_POST['med_nom']          ?? [];
        $dosages        = $_POST['med_dosage']        ?? [];
        $frequences     = $_POST['med_frequence']     ?? [];
        $durees         = $_POST['med_duree']         ?? [];
        $instructions   = $_POST['med_instructions']  ?? [];
        $categories     = $_POST['med_categorie']     ?? [];

        $result = [];
        foreach ($noms as $i => $nom) {
            if (!empty(trim($nom))) {
                $result[] = [
                    'nom'          => htmlspecialchars($nom),
                    'dosage'       => htmlspecialchars($dosages[$i]      ?? ''),
                    'frequence'    => htmlspecialchars($frequences[$i]   ?? ''),
                    'duree'        => htmlspecialchars($durees[$i]       ?? ''),
                    'instructions' => htmlspecialchars($instructions[$i] ?? ''),
                    'categorie'    => htmlspecialchars($categories[$i]   ?? ''),
                ];
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    private function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
