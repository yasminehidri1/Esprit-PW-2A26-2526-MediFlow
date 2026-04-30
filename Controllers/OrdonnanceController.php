<?php
namespace Controllers;
require_once __DIR__ . '/../Models/OrdonnanceModel.php';
require_once __DIR__ . '/../Models/ConsultationModel.php';

class OrdonnanceController {
    private \OrdonnanceModel   $ordonnanceModel;
    private \ConsultationModel $consultationModel;
    private int   $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->ordonnanceModel   = new \OrdonnanceModel();
        $this->consultationModel = new \ConsultationModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) { header('Location: /integration/login'); exit; }
        $user = $_SESSION['user'] ?? [];
        $this->medecinId   = (int)($user['id'] ?? 0);
        $this->medecinInfo = ['prenom' => $user['prenom'] ?? '', 'nom' => $user['nom'] ?? '', 'mail' => $user['mail'] ?? ''];
    }

    public function listAll(): void {
        $grouped   = $this->ordonnanceModel->getAllGroupedByPatient($this->medecinId);
        $totalOrdo = array_sum(array_map(fn($g) => count($g['ordonnances']), $grouped));
        $medecin   = $this->medecinInfo;
        $medecinId = $this->medecinId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/ordonnances_liste';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function view(): void {
        $ordonnanceId   = (int)($_GET['id']         ?? 0);
        $consultationId = (int)($_GET['consult_id'] ?? 0);
        if ($ordonnanceId > 0) {
            $ordonnance = $this->ordonnanceModel->getById($ordonnanceId);
        } elseif ($consultationId > 0) {
            $ordonnance = $this->ordonnanceModel->getByConsultation($consultationId);
        } else { $this->redirect('/integration/dossier/patients'); }
        if (!$ordonnance) $this->redirect("/integration/dossier/ordonnance/add?consult_id={$consultationId}");
        $medicaments  = json_decode($ordonnance['medicaments'] ?? '[]', true) ?: [];
        $consultation = $this->consultationModel->getConsultationById((int)$ordonnance['id_consultation']);
        $patient      = $this->consultationModel->getPatientById((int)$ordonnance['id_patient']);
        $medecin = $this->medecinInfo; $medecinId = $this->medecinId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/ordonnance_view';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function add(): void {
        $consultationId = (int)($_GET['consult_id'] ?? $_POST['id_consultation'] ?? 0);
        if ($consultationId === 0) $this->redirect('/integration/dossier/patients');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicaments = $this->buildMedicamentsJson();
            $data = [
                'id_consultation' => $consultationId, 'numero_ordonnance' => '',
                'date_emission'   => $_POST['date_emission'] ?? date('Y-m-d'),
                'medicaments'     => $medicaments,
                'note_pharmacien' => htmlspecialchars(trim($_POST['note_pharmacien'] ?? '')),
                'statut'          => 'active',
            ];
            $errors = $this->validateOrdonnance($data);
            if (!empty($errors)) { $_SESSION['validation_errors'] = $errors; $this->redirect("/integration/dossier/ordonnance/add?consult_id={$consultationId}"); }
            $ordonnanceId = $this->ordonnanceModel->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Ordonnance créée avec succès.'];
            $this->redirect("/integration/dossier/ordonnance/view?id={$ordonnanceId}");
        }
        $consultation = $this->consultationModel->getConsultationById($consultationId);
        if (!$consultation) $this->redirect('/integration/dossier/patients');
        $patient  = $this->consultationModel->getPatientById((int)$consultation['id_patient']);
        $mode     = 'add'; $medecin = $this->medecinInfo; $medecinId = $this->medecinId;
        $ordonnance = null; $medicaments = [];
        $validation_errors = $_SESSION['validation_errors'] ?? []; unset($_SESSION['validation_errors']);
        $currentView = 'dossier_medical/ordonnance_form';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id === 0) $this->redirect('/integration/dossier/patients');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicaments = $this->buildMedicamentsJson();
            $data = ['date_emission' => $_POST['date_emission'] ?? date('Y-m-d'), 'medicaments' => $medicaments,
                     'note_pharmacien' => htmlspecialchars(trim($_POST['note_pharmacien'] ?? '')),
                     'statut' => $_POST['statut'] ?? 'active'];
            $errors = $this->validateOrdonnance($data, false);
            if (!empty($errors)) { $_SESSION['validation_errors'] = $errors; $this->redirect("/integration/dossier/ordonnance/edit?id={$id}"); }
            $this->ordonnanceModel->update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Ordonnance mise à jour.'];
            $this->redirect("/integration/dossier/ordonnance/view?id={$id}");
        }
        $ordonnance  = $this->ordonnanceModel->getById($id);
        if (!$ordonnance) $this->redirect('/integration/dossier/patients');
        $consultation = $this->consultationModel->getConsultationById((int)$ordonnance['id_consultation']);
        $patient      = $this->consultationModel->getPatientById((int)$ordonnance['id_patient']);
        $medicaments  = json_decode($ordonnance['medicaments'] ?? '[]', true) ?: [];
        $mode = 'edit'; $medecin = $this->medecinInfo; $medecinId = $this->medecinId;
        $validation_errors = $_SESSION['validation_errors'] ?? []; unset($_SESSION['validation_errors']);
        $currentView = 'dossier_medical/ordonnance_form';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function delete(): void {
        $id = (int)($_POST['id'] ?? 0); $patientId = (int)($_POST['patient_id'] ?? 0);
        if ($id > 0) { $this->ordonnanceModel->delete($id); $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Ordonnance supprimée.']; }
        $this->redirect("/integration/dossier/view?patient_id={$patientId}");
    }

    private function validateOrdonnance(array $data, bool $req = true): array {
        $errors = [];
        if ($req && empty($data['id_consultation'])) $errors['id_consultation'] = 'Consultation invalide';
        if (empty($data['date_emission'])) { $errors['date_emission'] = 'La date est requise'; }
        else { $d = \DateTime::createFromFormat('Y-m-d', $data['date_emission']); if (!$d || $d > new \DateTime('today')) $errors['date_emission'] = 'Date invalide'; }
        if (empty($data['medicaments'])) { $errors['medicaments'] = 'Au moins un médicament requis'; }
        else { $meds = json_decode($data['medicaments'], true); if (!is_array($meds) || !count($meds)) $errors['medicaments'] = 'Au moins un médicament requis'; }
        return $errors;
    }

    private function buildMedicamentsJson(): string {
        $noms = $_POST['med_nom'] ?? []; $dosages = $_POST['med_dosage'] ?? [];
        $freq = $_POST['med_frequence'] ?? []; $durees = $_POST['med_duree'] ?? [];
        $inst = $_POST['med_instructions'] ?? []; $cats = $_POST['med_categorie'] ?? [];
        $result = [];
        foreach ($noms as $i => $nom) {
            if (!empty(trim($nom))) $result[] = ['nom' => htmlspecialchars($nom), 'dosage' => htmlspecialchars($dosages[$i] ?? ''), 'frequence' => htmlspecialchars($freq[$i] ?? ''), 'duree' => htmlspecialchars($durees[$i] ?? ''), 'instructions' => htmlspecialchars($inst[$i] ?? ''), 'categorie' => htmlspecialchars($cats[$i] ?? '')];
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    private function redirect(string $url): never { header("Location: {$url}"); exit; }
}
