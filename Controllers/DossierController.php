<?php
/**
 * DossierController — Handles Patient List and Dossier Médical views.
 * Namespace: Controllers (matches MediFlow MVC architecture)
 */

namespace Controllers;

require_once __DIR__ . '/../Models/ConsultationModel.php';
require_once __DIR__ . '/../Models/OrdonnanceModel.php';

class DossierController {

    private \ConsultationModel $consultationModel;
    private \OrdonnanceModel   $ordonnanceModel;
    private int   $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->consultationModel = new \ConsultationModel();
        $this->ordonnanceModel   = new \OrdonnanceModel();

        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requireAuth(['Medecin', 'Admin', 'Administrateur']);

        $user = $_SESSION['user'] ?? [];
        $this->medecinId   = (int)($user['id']     ?? 0);
        $this->medecinInfo = [
            'prenom' => $user['prenom'] ?? 'Médecin',
            'nom'    => $user['nom']    ?? '',
            'mail'   => $user['mail']   ?? '',
        ];
    }

    private function requireAuth(array $roles): void {
        if (empty($_SESSION['user'])) {
            header('Location: /integration/login');
            exit;
        }
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo '<p style="color:red;padding:2rem">Accès refusé. Rôle requis : ' . implode(' / ', $roles) . '</p>';
            exit;
        }
    }

    // ── Liste des patients ────────────────────────────────────────

    public function listPatients(): void {
        $perPage    = 12;
        $page       = max(1, (int)($_GET['p'] ?? 1));
        $offset     = ($page - 1) * $perPage;
        $search     = trim($_GET['q'] ?? '');

        $patients   = $this->consultationModel->getAllPatients($this->medecinId, $perPage, $offset);
        $totalCount = $this->consultationModel->countPatients($this->medecinId);
        $totalPages = (int) ceil($totalCount / $perPage);
        $stats      = $this->consultationModel->getTodayStats($this->medecinId);

        if ($search !== '') {
            $patients = array_filter($patients, function ($p) use ($search) {
                $full = strtolower($p['prenom'] . ' ' . $p['nom'] . ' ' . $p['mail']);
                return str_contains($full, strtolower($search));
            });
        }

        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;
        $currentView = 'dossier_medical/patients_liste';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Nouvelle Consultation ────────────────────────────────────

    public function nouvelleConsultation(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addConsultation();
            return;
        }

        $allPatients = $this->consultationModel->getAllPatients($this->medecinId, 200, 0);
        $medecin     = $this->medecinInfo;
        $medecinId   = $this->medecinId;
        $antecedents = [];
        $allergies   = [];
        $currentView = 'dossier_medical/consultation_form';
        $mode        = 'new';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Dossier Médical (patient detail) ─────────────────────────

    public function viewDossier(): void {
        $patientId = (int)($_GET['patient_id'] ?? 0);
        if ($patientId === 0) $this->redirect('/integration/dossier/patients');

        $patient = $this->consultationModel->getPatientById($patientId);
        if (!$patient) $this->redirect('/integration/dossier/patients');

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

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $currentView = 'dossier_medical/dossier_view';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    // ── Add Consultation (POST) ───────────────────────────────────

    public function addConsultation(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/integration/dossier/patients');
        }

        $patientId   = (int)($_POST['id_patient'] ?? 0);
        $antecedents = $this->buildJsonFromPost('ant_annee', 'ant_titre', 'ant_desc');
        $allergies   = $this->buildAllergiesFromPost();

        $data = [
            'id_medecin'         => $this->medecinId,
            'id_patient'         => $patientId,
            'date_consultation'  => $this->formatDateTime($_POST['date_consultation'] ?? ''),
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

        $errors = $this->validateConsultation($data);
        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['form_data']         = $_POST;
            $this->redirect("/integration/dossier/nouvelle-consultation");
        }

        $this->consultationModel->createConsultation($data);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Consultation ajoutée avec succès.'];
        $this->redirect("/integration/dossier/view?patient_id={$patientId}");
    }

    // ── Edit Consultation ─────────────────────────────────────────

    public function editConsultation(): void {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id === 0) $this->redirect('/integration/dossier/patients');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientId   = (int)($_POST['id_patient'] ?? 0);
            $antecedents = $this->buildJsonFromPost('ant_annee', 'ant_titre', 'ant_desc');
            $allergies   = $this->buildAllergiesFromPost();

            $data = [
                'date_consultation'  => $this->formatDateTime($_POST['date_consultation'] ?? ''),
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

            $errors = $this->validateConsultation($data, false);
            if (!empty($errors)) {
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['form_data']         = $_POST;
                $this->redirect("/integration/dossier/consultation/edit?id={$id}");
            }

            $this->consultationModel->updateConsultation($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Consultation mise à jour.'];
            $this->redirect("/integration/dossier/view?patient_id={$patientId}");

        } else {
            $consultation = $this->consultationModel->getConsultationById($id);
            if (!$consultation) $this->redirect('/integration/dossier/patients');

            $patient     = $this->consultationModel->getPatientById((int)$consultation['id_patient']);
            $antecedents = json_decode($consultation['antecedents'] ?? '[]', true) ?: [];
            $allergies   = json_decode($consultation['allergies']   ?? '[]', true) ?: [];

            $medecin   = $this->medecinInfo;
            $medecinId = $this->medecinId;
            $mode      = 'edit';
            $validation_errors = $_SESSION['validation_errors'] ?? [];
            unset($_SESSION['validation_errors']);
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);

            $currentView = 'dossier_medical/consultation_form';
            include __DIR__ . '/../Views/Back/layout.php';
        }
    }

    // ── Delete Consultation (POST) ────────────────────────────────

    public function deleteConsultation(): void {
        $id        = (int)($_POST['id'] ?? 0);
        $patientId = (int)($_POST['id_patient'] ?? 0);

        if ($id > 0) {
            $this->consultationModel->deleteConsultation($id);
            $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Consultation supprimée.'];
        }
        $this->redirect("/integration/dossier/view?patient_id={$patientId}");
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function validateConsultation(array $data, bool $requirePatient = true): array {
        $errors = [];

        if ($requirePatient && (empty($data['id_patient']) || !is_numeric($data['id_patient']))) {
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

        $validTypes = ['Contrôle annuel','Bilan Annuel','Suivi Spécialisé','Suivi Traitement',
                       'Téléconsultation','Consultation urgente','Contrôle Post-Op','Symptômes Grippaux'];
        if (!in_array($data['type_consultation'] ?? '', $validTypes)) {
            $errors['type_consultation'] = 'Type de consultation invalide';
        }

        if (!empty($data['diagnostic']) && strlen($data['diagnostic']) > 150) {
            $errors['diagnostic'] = 'Le diagnostic ne peut pas dépasser 150 caractères';
        }

        if (!empty($data['tension_arterielle'])) {
            if (!preg_match('/^\d{1,3}\/\d{1,3}$/', $data['tension_arterielle'])) {
                $errors['tension_arterielle'] = 'Format invalide (ex: 120/80)';
            }
        }

        if (!empty($data['rythme_cardiaque'])) {
            $rate = (int)$data['rythme_cardiaque'];
            if ($rate < 30 || $rate > 300) $errors['rythme_cardiaque'] = 'Rythme : 30-300 BPM';
        }

        if (!empty($data['poids'])) {
            $w = (float)$data['poids'];
            if ($w <= 2 || $w >= 500) $errors['poids'] = 'Poids : 2-500 kg';
        }

        if (!empty($data['saturation_o2'])) {
            $s = (int)$data['saturation_o2'];
            if ($s < 0 || $s > 100) $errors['saturation_o2'] = 'Saturation : 0-100%';
        }

        return $errors;
    }

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

    private function formatDateTime(string $dateTime): string {
        if (empty($dateTime)) return date('Y-m-d H:i:s');
        $formatted = str_replace('T', ' ', $dateTime);
        if (strlen($formatted) === 16) $formatted .= ':00';
        return $formatted;
    }

    private function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
