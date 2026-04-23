<?php
/**
 * DemandeController — Historique des demandes d'ordonnance reçues par le médecin.
 */

require_once __DIR__ . '/../models/DemandeOrdonnanceModel.php';
require_once __DIR__ . '/../config/database.php';

class DemandeController {

    private DemandeOrdonnanceModel $demandeModel;
    private int   $medecinId;
    private array $medecinInfo;

    public function __construct() {
        $this->demandeModel = new DemandeOrdonnanceModel();

        session_start_if_not_started();
        $this->medecinId   = $_SESSION['user_id']   ?? 2;
        $this->medecinInfo = $_SESSION['user_info']  ?? [
            'prenom' => 'Jean',
            'nom'    => 'Dupont',
            'mail'   => 'medecin1@mediflow.com',
        ];
    }

    // ── Liste des demandes reçues ──────────────────────────────────

    public function listDemandes(): void {
        $demandes = $this->demandeModel->getDemandesByMedecin($this->medecinId);

        // Enrichir chaque demande avec les infos patient depuis la BDD
        $db = Database::getInstance();
        foreach ($demandes as &$d) {
            $stmt = $db->prepare("
                SELECT prenom, nom, mail
                FROM utilisateurs
                WHERE id_PK = :id
            ");
            $stmt->execute([':id' => (int)$d['id_patient']]);
            $patient = $stmt->fetch();
            $d['patient_prenom'] = $patient['prenom'] ?? '—';
            $d['patient_nom']    = $patient['nom']    ?? '—';
            $d['patient_mail']   = $patient['mail']   ?? '—';
        }
        unset($d);

        $medecin    = $this->medecinInfo;
        $medecinId  = $this->medecinId;
        $activePage = 'demandes';

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/backoffice/demandes/liste.php';
    }

    // ── Changer le statut d'une demande ───────────────────────────

    public function updateStatut(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=demandes');
        }

        $id     = (int)($_POST['id_demande'] ?? 0);
        $statut = $_POST['statut'] ?? '';

        if ($id > 0 && in_array($statut, ['traitee', 'refusee', 'en_attente'], true)) {
            $this->demandeModel->updateStatut($id, $statut);
            $labels = ['traitee' => 'Demande marquée comme traitée.', 'refusee' => 'Demande refusée.', 'en_attente' => 'Demande remise en attente.'];
            $_SESSION['flash'] = ['type' => 'success', 'msg' => $labels[$statut]];
        }

        $this->redirect('?page=demandes');
    }

    // ── Helper ────────────────────────────────────────────────────

    private function redirect(string $url): never {
        header("Location: {$url}");
        exit;
    }
}
