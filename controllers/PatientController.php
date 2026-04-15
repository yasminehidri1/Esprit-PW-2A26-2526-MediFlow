<?php
/**
 * PatientController — Patient dashboard and medical records.
 */

require_once __DIR__ . '/../models/PatientModel.php';

class PatientController {

    private PatientModel $patientModel;
    private int $patientId;

    public function __construct() {
        $this->patientModel = new PatientModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get patient ID from URL parameter or session
        $this->patientId = (int)($_GET['id'] ?? $_SESSION['user_id'] ?? 1);
    }

    public function dashboard(): void {
        $patient = $this->patientModel->getPatientById($this->patientId);

        if (!$patient) {
            http_response_code(404);
            echo "<!DOCTYPE html>
            <html lang='fr'>
            <head>
                <meta charset='utf-8'/>
                <title>Patient non trouvé</title>
                <script src='https://cdn.tailwindcss.com'></script>
            </head>
            <body class='min-h-screen bg-slate-50 flex items-center justify-center'>
                <div class='text-center'>
                    <h1 class='text-2xl font-bold text-red-600 mb-2'>⚠️ Erreur</h1>
                    <p class='text-slate-600 mb-2'>L'ID patient #" . $this->patientId . " n'existe pas dans la base de données.</p>
                    <p class='text-sm text-slate-500 mb-4'>Essaie avec un autre ID depuis la page de test.</p>
                    <a href='test_users.php' class='inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700'>
                        ← Retour à la liste
                    </a>
                </div>
            </body>
            </html>";
            exit;
        }

        try {
            $consultations = $this->patientModel->getPatientConsultations($this->patientId);
        } catch (Exception $e) {
            $consultations = [];
        }

        try {
            $prescriptions = $this->patientModel->getPatientPrescriptions($this->patientId);
        } catch (Exception $e) {
            $prescriptions = [];
        }

        try {
            $doctors = $this->patientModel->getPatientDoctors($this->patientId);
        } catch (Exception $e) {
            $doctors = [];
        }

        try {
            $vitals = $this->patientModel->getLatestVitals($this->patientId);
        } catch (Exception $e) {
            $vitals = null;
        }

        require __DIR__ . '/../views/frontoffice/patient/dashboard.php';
    }

    public function updateProfile(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        $errors = [];
        if (empty($data['prenom']) || strlen($data['prenom']) < 2 || strlen($data['prenom']) > 50) {
            $errors['prenom'] = 'Prénom invalide';
        }
        if (empty($data['nom']) || strlen($data['nom']) < 2 || strlen($data['nom']) > 50) {
            $errors['nom'] = 'Nom invalide';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        try {
            $this->patientModel->updateProfile(
                $this->patientId,
                $data['prenom'],
                $data['nom'],
                $data['email']
            );
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour']);
        }
    }

    public function requestPrescription(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        $errors = [];
        if (empty($data['medecin_id'])) {
            $errors['medecin'] = 'Sélectionnez un médecin';
        }
        if (empty($data['description']) || strlen($data['description']) < 10 || strlen($data['description']) > 500) {
            $errors['description'] = 'Description invalide (10-500 caractères)';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        try {
            $this->patientModel->addPrescriptionRequest(
                $this->patientId,
                (int)$data['medecin_id'],
                $data['description']
            );
            echo json_encode(['success' => true, 'message' => 'Demande d\'ordonnance envoyée au médecin']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la demande']);
        }
    }

    public function contactTeam(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        $errors = [];
        if (empty($data['sujet']) || strlen($data['sujet']) < 3 || strlen($data['sujet']) > 100) {
            $errors['sujet'] = 'Sujet invalide (3-100 caractères)';
        }
        if (empty($data['message']) || strlen($data['message']) < 10 || strlen($data['message']) > 1000) {
            $errors['message'] = 'Message invalide (10-1000 caractères)';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        try {
            $this->patientModel->addContactMessage(
                $this->patientId,
                $data['sujet'],
                $data['message']
            );
            echo json_encode(['success' => true, 'message' => 'Message envoyé à l\'équipe médicale']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'envoi du message']);
        }
    }

    public function exportPDF(): void {
        // Get patient data
        $patient = $this->patientModel->getPatientById($this->patientId);
        $consultations = $this->patientModel->getPatientConsultations($this->patientId);
        $prescriptions = $this->patientModel->getPatientPrescriptions($this->patientId);
        $doctors = $this->patientModel->getPatientDoctors($this->patientId);
        $vitals = $this->patientModel->getLatestVitals($this->patientId);

        // Generate PDF filename
        $filename = 'dossier_' . $patient['id_PK'] . '_' . date('Y-m-d_His') . '.pdf';

        // Generate simple HTML content for PDF
        $html = $this->generatePDFContent($patient, $consultations, $prescriptions, $doctors, $vitals);

        // For now, output as HTML (TODO: implement mPDF or dompdf)
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');
        echo $html;
    }

    private function generatePDFContent($patient, $consultations, $prescriptions, $doctors, $vitals): string {
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='utf-8'/>
            <title>Dossier Patient - " . htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) . "</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
                h1 { color: #004d99; border-bottom: 2px solid #005851; padding-bottom: 10px; }
                h2 { color: #005851; margin-top: 30px; margin-bottom: 15px; }
                .patient-info { background: #f0f4f8; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                .section { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background: #004d99; color: white; }
                tr:nth-child(even) { background: #f9f9f9; }
                .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <h1>📋 Dossier Médical Patient</h1>

            <div class='patient-info'>
                <h2>Informations Patient</h2>
                <p><strong>Nom:</strong> " . htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) . "</p>
                <p><strong>ID:</strong> #MF-" . str_pad($patient['id_PK'], 5, '0', STR_PAD_LEFT) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($patient['mail']) . "</p>
                <p><strong>Statut:</strong> Actif</p>
            </div>";

        if ($vitals) {
            $html .= "
            <div class='section'>
                <h2>Paramètres Vitaux (Dernière Consultation)</h2>
                <table>
                    <tr>
                        <td><strong>Tension Artérielle:</strong></td>
                        <td>" . htmlspecialchars($vitals['tension_arterielle'] ?? 'N/A') . " mmHg</td>
                    </tr>
                    <tr>
                        <td><strong>Fréquence Cardiaque:</strong></td>
                        <td>" . htmlspecialchars($vitals['rythme_cardiaque'] ?? 'N/A') . " BPM</td>
                    </tr>
                    <tr>
                        <td><strong>Poids:</strong></td>
                        <td>" . htmlspecialchars($vitals['poids'] ?? 'N/A') . " kg</td>
                    </tr>
                    <tr>
                        <td><strong>Saturation O²:</strong></td>
                        <td>" . htmlspecialchars($vitals['saturation_o2'] ?? 'N/A') . " %</td>
                    </tr>
                </table>
            </div>";
        }

        if (!empty($consultations)) {
            $html .= "<div class='section'><h2>Historique Clinique</h2><table>
                <tr><th>Date</th><th>Type</th><th>Médecin</th><th>Diagnostic</th></tr>";
            foreach (array_slice($consultations, 0, 10) as $c) {
                $html .= "<tr>
                    <td>" . date('d/m/Y', strtotime($c['date_consultation'])) . "</td>
                    <td>" . htmlspecialchars($c['type_consultation'] ?? 'N/A') . "</td>
                    <td>" . htmlspecialchars($c['medecin_prenom'] . ' ' . $c['medecin_nom']) . "</td>
                    <td>" . htmlspecialchars(substr($c['diagnostic'] ?? '', 0, 50)) . "</td>
                </tr>";
            }
            $html .= "</table></div>";
        }

        if (!empty($doctors)) {
            $html .= "<div class='section'><h2>Équipe Médicale</h2><table>
                <tr><th>Médecin</th><th>Rôle</th></tr>";
            foreach ($doctors as $d) {
                $html .= "<tr>
                    <td>" . htmlspecialchars($d['prenom'] . ' ' . $d['nom']) . "</td>
                    <td>" . htmlspecialchars($d['role_libelle'] ?? 'N/A') . "</td>
                </tr>";
            }
            $html .= "</table></div>";
        }

        $html .= "
            <div class='footer'>
                <p>Document généré le " . date('d/m/Y à H:i') . "</p>
                <p>Ce document est confidentiel et destiné à usage médical uniquement.</p>
            </div>
        </body>
        </html>";

        return $html;
    }
}
