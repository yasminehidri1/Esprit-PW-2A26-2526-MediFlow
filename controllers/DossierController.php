<?php
/**
 * DossierController — Handles Patient List and Dossier Médical views.
 */

require_once __DIR__ . '/../models/ConsultationModel.php';
require_once __DIR__ . '/../models/OrdonnanceModel.php';

class DossierController {

    private ConsultationModel $consultationModel;
    private OrdonnanceModel   $ordonnanceModel;

    // Logged-in doctor's ID (in a real app, from session)
    private int $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->consultationModel = new ConsultationModel();
        $this->ordonnanceModel   = new OrdonnanceModel();

        // SESSION: get logged-in doctor
        session_start_if_not_started();
        $this->medecinId   = $_SESSION['user_id']   ?? 2; // Default to demo doctor
        $this->medecinInfo = $_SESSION['user_info']  ?? [
            'prenom' => 'Jean',
            'nom'    => 'Dupont',
            'mail'   => 'medecin1@mediflow.com',
        ];
    }

    // ── Nouvelle Consultation (sidebar button) ───────────────────

    public function nouvelleConsultation(): void {
        // After POST (form submitted from this page), delegate to addConsultation logic
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addConsultation();
            return;
        }

        // GET: show patient selector + blank consultation form
        $allPatients = $this->consultationModel->getAllPatients($this->medecinId, 100, 0);
        $medecin     = $this->medecinInfo;
        $medecinId   = $this->medecinId;
        $antecedents = [];
        $allergies   = [];

        require __DIR__ . '/../views/backoffice/dossier/nouvelle_consultation.php';
    }

    // ── Patient List ──────────────────────────────────────────────

    public function listPatients(): void {
        $perPage    = 10;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;
        $search     = trim($_GET['q'] ?? '');

        $patients   = $this->consultationModel->getAllPatients($this->medecinId, $perPage, $offset);
        $totalCount = $this->consultationModel->countPatients();
        $totalPages = (int) ceil($totalCount / $perPage);
        $stats      = $this->consultationModel->getTodayStats($this->medecinId);

        // Client-side search is handled by JS in the view,
        // but we also accept server-side ?q= for non-JS users.
        if ($search !== '') {
            $patients = array_filter($patients, function ($p) use ($search) {
                $full = strtolower($p['prenom'] . ' ' . $p['nom'] . ' ' . $p['mail']);
                return str_contains($full, strtolower($search));
            });
        }

        $medecin    = $this->medecinInfo;
        $medecinId  = $this->medecinId;

        require __DIR__ . '/../views/backoffice/patients/liste.php';
    }

    // ── Dossier Médical ───────────────────────────────────────────

    public function viewDossier(): void {
        $patientId = (int)($_GET['patient_id'] ?? 0);
        if ($patientId === 0) {
            $this->redirect('?page=patients');
        }

        $patient       = $this->consultationModel->getPatientById($patientId);
        if (!$patient) {
            $this->redirect('?page=patients');
        }

        $consultations = $this->consultationModel->getConsultationsByPatient($patientId, $this->medecinId);
        $latestConsult = $this->consultationModel->getLatestConsultation($patientId);

        $antecedents = [];
        $allergies   = [];
        $vitals      = null;

        if ($latestConsult) {
            $antecedents = json_decode($latestConsult['antecedents'] ?? '[]', true) ?: [];
            $allergies   = json_decode($latestConsult['allergies']   ?? '[]', true) ?: [];
            $vitals      = $latestConsult;
        }

        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;

        // Flash message from POST redirect
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/dossier/view.php';
    }

    // ── Add Consultation ──────────────────────────────────────────

    public function addConsultation(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=patients');
        }

        $patientId = (int)($_POST['id_patient'] ?? 0);

        // Encode antecedents & allergies as JSON
        $antecedents = $this->buildJsonFromPost('ant_annee', 'ant_titre', 'ant_desc');
        $allergies   = $this->buildAllergiesFromPost();

        $data = [
            'id_medecin'         => $this->medecinId,
            'id_patient'         => $patientId,
            'date_consultation'  => $_POST['date_consultation'] ?? date('Y-m-d H:i:s'),
            'type_consultation'  => htmlspecialchars(trim($_POST['type_consultation'] ?? '')),
            'diagnostic'         => htmlspecialchars(trim($_POST['diagnostic'] ?? '')),
            'compte_rendu'       => htmlspecialchars(trim($_POST['compte_rendu'] ?? '')),
            'tension_arterielle' => htmlspecialchars(trim($_POST['tension_arterielle'] ?? '')),
            'rythme_cardiaque'   => (int)($_POST['rythme_cardiaque'] ?? 0) ?: null,
            'poids'              => (float)($_POST['poids'] ?? 0) ?: null,
            'saturation_o2'      => (int)($_POST['saturation_o2'] ?? 0) ?: null,
            'antecedents'        => $antecedents,
            'allergies'          => $allergies,
        ];

        // Validate data
        $errors = $this->validateConsultation($data);
        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $this->redirect("?page=dossier&action=add&patient_id={$patientId}");
        }

        $this->consultationModel->createConsultation($data);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Consultation ajoutée avec succès.'];
        $this->redirect("?page=dossier&patient_id={$patientId}");
    }

    // ── Edit Consultation ─────────────────────────────────────────

    public function editConsultation(): void {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id === 0) $this->redirect('?page=patients');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientId   = (int)($_POST['id_patient'] ?? 0);
            $antecedents = $this->buildJsonFromPost('ant_annee', 'ant_titre', 'ant_desc');
            $allergies   = $this->buildAllergiesFromPost();

            $data = [
                'date_consultation'  => $_POST['date_consultation'] ?? date('Y-m-d H:i:s'),
                'type_consultation'  => htmlspecialchars(trim($_POST['type_consultation'] ?? '')),
                'diagnostic'         => htmlspecialchars(trim($_POST['diagnostic'] ?? '')),
                'compte_rendu'       => htmlspecialchars(trim($_POST['compte_rendu'] ?? '')),
                'tension_arterielle' => htmlspecialchars(trim($_POST['tension_arterielle'] ?? '')),
                'rythme_cardiaque'   => (int)($_POST['rythme_cardiaque'] ?? 0) ?: null,
                'poids'              => (float)($_POST['poids'] ?? 0) ?: null,
                'saturation_o2'      => (int)($_POST['saturation_o2'] ?? 0) ?: null,
                'antecedents'        => $antecedents,
                'allergies'          => $allergies,
            ];

            // Validate data
            $errors = $this->validateConsultation($data);
            if (!empty($errors)) {
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                $this->redirect("?page=dossier&action=edit&id={$id}");
            }

            $this->consultationModel->updateConsultation($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Consultation mise à jour.'];
            $this->redirect("?page=dossier&patient_id={$patientId}");
        } else {
            // Show edit form pre-filled
            $consultation = $this->consultationModel->getConsultationById($id);
            if (!$consultation) $this->redirect('?page=patients');

            $patient      = $this->consultationModel->getPatientById((int)$consultation['id_patient']);
            $antecedents  = json_decode($consultation['antecedents'] ?? '[]', true) ?: [];
            $allergies    = json_decode($consultation['allergies']   ?? '[]', true) ?: [];

            $medecin   = $this->medecinInfo;
            $medecinId = $this->medecinId;
            $mode      = 'edit';
            $validation_errors = $_SESSION['validation_errors'] ?? [];
            unset($_SESSION['validation_errors']);
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);

            require __DIR__ . '/../views/backoffice/dossier/form_consultation.php';
        }
    }

    // ── Delete Consultation ───────────────────────────────────────

    public function deleteConsultation(): void {
        $id        = (int)($_POST['id'] ?? 0);
        $patientId = (int)($_POST['id_patient'] ?? 0);

        if ($id > 0) {
            $this->consultationModel->deleteConsultation($id);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Consultation supprimée.'];
        }
        $this->redirect("?page=dossier&patient_id={$patientId}");
    }

    // ── Helpers ───────────────────────────────────────────────────

    /** Validate consultation data. Returns array of errors (empty = valid). */
    private function validateConsultation(array $data): array {
        $errors = [];

        // Required fields
        if (empty($data['id_patient']) || !is_numeric($data['id_patient'])) {
            $errors['id_patient'] = 'Patient invalide';
        }

        if (empty($data['date_consultation'])) {
            $errors['date_consultation'] = 'La date est requise';
        } else {
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date_consultation']);
            if (!$dt || $dt > new \DateTime()) {
                $errors['date_consultation'] = 'Date/heure invalide ou future';
            }
        }

        // Type
        $validTypes = ['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement',
                      'Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'];
        if (!in_array($data['type_consultation'] ?? '', $validTypes)) {
            $errors['type_consultation'] = 'Type de consultation invalide';
        }

        // Diagnostic (optional but validate if present)
        if (!empty($data['diagnostic']) && strlen($data['diagnostic']) > 150) {
            $errors['diagnostic'] = 'Le diagnostic ne peut pas dépasser 150 caractères';
        }

        // Compte rendu (optional)
        if (!empty($data['compte_rendu']) && strlen($data['compte_rendu']) > 5000) {
            $errors['compte_rendu'] = 'Le compte-rendu est trop long';
        }

        // Vitals validation
        if (!empty($data['tension_arterielle'])) {
            if (!preg_match('/^\d{1,3}\/\d{1,3}$/', $data['tension_arterielle'])) {
                $errors['tension_arterielle'] = 'Format invalide (ex: 120/80)';
            } else {
                [$sys, $dia] = explode('/', $data['tension_arterielle']);
                $sys = (int)$sys;
                $dia = (int)$dia;
                if (!($sys >= 60 && $sys <= 250 && $dia >= 30 && $dia <= 150 && $sys > $dia)) {
                    $errors['tension_arterielle'] = 'Tension invalide (ex: 120/80)';
                }
            }
        }

        if (!empty($data['rythme_cardiaque'])) {
            $rate = (int)$data['rythme_cardiaque'];
            if ($rate < 30 || $rate > 300) {
                $errors['rythme_cardiaque'] = 'Le rythme cardiaque doit être entre 30-300 BPM';
            }
        }

        if (!empty($data['poids'])) {
            $weight = (float)$data['poids'];
            if ($weight <= 2 || $weight >= 500) {
                $errors['poids'] = 'Le poids doit être entre 2-500 kg';
            }
        }

        if (!empty($data['saturation_o2'])) {
            $sat = (int)$data['saturation_o2'];
            if ($sat < 0 || $sat > 100) {
                $errors['saturation_o2'] = 'La saturation O² doit être entre 0-100%';
            }
        }

        return $errors;
    }

    /** Build JSON for antecedents from repeated POST fields. */
    private function buildJsonFromPost(string $anneeKey, string $titreKey, string $descKey): string {
        $annees = $_POST[$anneeKey] ?? [];
        $titres = $_POST[$titreKey] ?? [];
        $descs  = $_POST[$descKey]  ?? [];
        $result = [];
        $count  = count($annees);
        for ($i = 0; $i < $count; $i++) {
            if (!empty(trim($titres[$i] ?? ''))) {
                $result[] = [
                    'annee'       => htmlspecialchars($annees[$i] ?? ''),
                    'titre'       => htmlspecialchars($titres[$i]),
                    'description' => htmlspecialchars($descs[$i] ?? ''),
                ];
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /** Build JSON for allergies from POST. */
    private function buildAllergiesFromPost(): string {
        $noms    = $_POST['allergie_nom']    ?? [];
        $niveaux = $_POST['allergie_niveau'] ?? [];
        $result  = [];
        foreach ($noms as $i => $nom) {
            if (!empty(trim($nom))) {
                $result[] = [
                    'nom'    => htmlspecialchars($nom),
                    'niveau' => htmlspecialchars($niveaux[$i] ?? 'Modéré'),
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
