<?php
namespace Controllers;
require_once __DIR__ . '/../Models/DemandeOrdonnanceModel.php';
require_once __DIR__ . '/../config.php';

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
        $pdo = \config::getConnexion();
        foreach ($demandes as &$d) {
            $stmt = $pdo->prepare("SELECT prenom, nom, mail FROM utilisateurs WHERE id_PK = :id");
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
        $id     = (int)($_POST['id_demande'] ?? 0);
        $statut = $_POST['statut'] ?? '';
        if ($id > 0 && in_array($statut, ['traitee', 'refusee', 'en_attente'], true)) {
            $this->demandeModel->updateStatut($id, $statut);
            $labels = ['traitee' => 'Demande traitée.', 'refusee' => 'Demande refusée.', 'en_attente' => 'Remise en attente.'];
            $_SESSION['flash'] = ['type' => 'success', 'msg' => $labels[$statut]];
        }
        $this->redirect('/integration/dossier/demandes');
    }

    private function redirect(string $url): never { header("Location: {$url}"); exit; }
}
