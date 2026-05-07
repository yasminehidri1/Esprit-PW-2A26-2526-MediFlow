<?php
namespace Controllers;
require_once __DIR__ . '/../Models/DemandeOrdonnanceModel.php';
require_once __DIR__ . '/../Models/NotificationModel.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Services/ClaudeService.php';
require_once __DIR__ . '/../Services/MailService.php';

class DemandeController {
    private \DemandeOrdonnanceModel $demandeModel;
    private int   $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->demandeModel = new \DemandeOrdonnanceModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) { header('Location: /integration/login'); exit; }
        $user = $_SESSION['user'];
        $this->medecinId   = (int)($user['id'] ?? 0);
        $this->medecinInfo = ['prenom' => $user['prenom'] ?? '', 'nom' => $user['nom'] ?? '', 'mail' => $user['mail'] ?? ''];
    }

    public function listDemandes(): void {
        $demandes = $this->demandeModel->getDemandesByMedecin($this->medecinId);
        $db = \Database::getInstance();
        foreach ($demandes as &$d) {
            $stmt = $db->prepare("SELECT prenom, nom, mail FROM utilisateurs WHERE id_PK = :id");
            $stmt->execute([':id' => (int)$d['id_patient']]);
            $patient = $stmt->fetch();
            $d['patient_prenom'] = $patient['prenom'] ?? '—';
            $d['patient_nom']    = $patient['nom']    ?? '—';
            $d['patient_mail']   = $patient['mail']   ?? '—';
        }
        unset($d);
        $medecin = $this->medecinInfo; $medecinId = $this->medecinId;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $currentView = 'dossier_medical/demandes_liste';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    public function updateStatut(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/integration/dossier/demandes');
        $id             = (int)($_POST['id_demande'] ?? 0);
        $statut         = $_POST['statut'] ?? '';
        $aiRefusMessage = !empty($_POST['ai_refus_message']) ? trim($_POST['ai_refus_message']) : null;

        if ($id > 0 && in_array($statut, ['traitee', 'refusee', 'en_attente'], true)) {
            $this->demandeModel->updateStatut($id, $statut, $aiRefusMessage);

            $labels = ['traitee' => 'Demande traitée.', 'refusee' => 'Demande refusée.', 'en_attente' => 'Remise en attente.'];
            $_SESSION['flash'] = ['type' => 'success', 'msg' => $labels[$statut]];

            // Email + notification uniquement pour traitée/refusée
            if (in_array($statut, ['traitee', 'refusee'], true)) {
                $this->notifyPatient($id, $statut, $aiRefusMessage ?? '');
            }
        }
        $this->redirect('/integration/dossier/demandes');
    }

    private function notifyPatient(int $demandeId, string $statut, string $aiMessage): void {
        $demande = $this->demandeModel->getDemandeById($demandeId);
        if (!$demande) return;

        // Récupérer les infos patient depuis la DB
        try {
            $db   = \Database::getInstance();
            $stmt = $db->prepare("SELECT prenom, nom, mail FROM utilisateurs WHERE id_PK = :id");
            $stmt->execute([':id' => (int)$demande['id_patient']]);
            $patient = $stmt->fetch();
        } catch (\Throwable) { return; }

        if (!$patient || empty($patient['mail'])) return;

        $patientName  = $patient['prenom'] . ' ' . $patient['nom'];
        $patientEmail = $patient['mail'];
        $description  = $demande['description'];
        $notifModel   = new \NotificationModel();
        $mailer       = new \Services\MailService();

        if ($statut === 'traitee') {
            $mailer->sendDemandeTraitee($patientEmail, $patientName, $description);

            // Notification confirmation → médecin
            $notifModel->add(
                $this->medecinId,
                'demande_traitee',
                'Demande traitée ✓',
                "Email de confirmation envoyé à {$patientName}.",
                $demandeId
            );
            // Notification → patient
            $notifModel->add(
                (int)$demande['id_patient'],
                'demande_traitee',
                'Demande d\'ordonnance acceptée ✓',
                'Votre médecin a accepté et traité votre demande. Vous pouvez récupérer votre ordonnance.',
                $demandeId
            );
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Demande traitée · Email envoyé à {$patientName}."];
        } else {
            $mailer->sendDemandeRefusee($patientEmail, $patientName, $description, $aiMessage);

            // Notification confirmation → médecin
            $notifModel->add(
                $this->medecinId,
                'demande_refusee',
                'Demande refusée',
                "Email de refus envoyé à {$patientName}.",
                $demandeId
            );
            // Notification → patient
            $notifModel->add(
                (int)$demande['id_patient'],
                'demande_refusee',
                'Demande d\'ordonnance non retenue',
                'Votre médecin n\'a pas pu donner suite à votre demande. Consultez votre email pour les détails.',
                $demandeId
            );
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Demande refusée · Email envoyé à {$patientName}."];
        }
    }

    /** POST JSON → génère un message de refus via l'IA */
    public function aiGenerateRefus(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false]); return; }

        $data        = json_decode(file_get_contents('php://input'), true);
        $raison      = trim($data['raison']      ?? '');
        $description = trim($data['description'] ?? '');
        $variation   = max(1, (int)($data['variation'] ?? 1));

        if (empty($raison)) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'Motif manquant']); return; }

        try {
            $claude  = new \Services\ClaudeService();
            $message = $claude->generateRefusMessage($raison, $description, $variation);
            echo json_encode(['success' => true, 'message' => $message]);
        } catch (\Throwable $e) {
            http_response_code(503);
            echo json_encode(['success' => false, 'error' => 'Service IA indisponible. Vérifiez la clé API.']);
        }
    }

    private function redirect(string $url): never { header("Location: {$url}"); exit; }
}
