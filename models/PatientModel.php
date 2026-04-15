<?php
/**
 * PatientModel — Patient data retrieval.
 */

require_once __DIR__ . '/../config/database.php';

class PatientModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getPatientById(int $patientId): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.libelle AS role_libelle
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            WHERE u.id_PK = :id
        ");
        $stmt->execute([':id' => $patientId]);
        return $stmt->fetch() ?: null;
    }

    public function getPatientConsultations(int $patientId): array {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom,
                   um.mail AS medecin_mail
            FROM consultation c
            LEFT JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE c.id_patient = :patient_id
            ORDER BY c.date_consultation DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getPatientPrescriptions(int $patientId): array {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   c.id_consultation,
                   up.prenom AS patient_prenom, up.nom AS patient_nom,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom
            FROM ordonnance o
            LEFT JOIN consultation c ON c.id_consultation = o.id_consultation
            LEFT JOIN utilisateurs up ON up.id_PK = c.id_patient
            LEFT JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE c.id_patient = :patient_id
            ORDER BY o.date_emission DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getPatientDoctors(int $patientId): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT um.*, r.libelle AS role_libelle,
                   COUNT(c.id_consultation) AS nb_consultations
            FROM consultation c
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            LEFT JOIN roles r ON r.id_role = um.id_role
            WHERE c.id_patient = :patient_id
            GROUP BY um.id_PK
            ORDER BY c.date_consultation DESC
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function getLatestVitals(int $patientId): ?array {
        $stmt = $this->db->prepare("
            SELECT tension_arterielle, rythme_cardiaque, poids, saturation_o2,
                   date_consultation
            FROM consultation
            WHERE id_patient = :patient_id
            ORDER BY date_consultation DESC
            LIMIT 1
        ");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetch() ?: null;
    }

    public function updateProfile(int $patientId, string $prenom, string $nom, string $mail): bool {
        $stmt = $this->db->prepare("
            UPDATE utilisateurs
            SET prenom = :prenom, nom = :nom, mail = :mail
            WHERE id_PK = :id
        ");
        return $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':mail' => $mail,
            ':id' => $patientId
        ]);
    }

    public function addPrescriptionRequest(int $patientId, int $medicId, string $description): bool {
        $stmt = $this->db->prepare("
            INSERT INTO prescription_requests (id_patient, id_medecin, description, date_request, statut)
            VALUES (:patient_id, :medecin_id, :description, NOW(), 'pending')
        ");
        return $stmt->execute([
            ':patient_id' => $patientId,
            ':medecin_id' => $medicId,
            ':description' => $description
        ]);
    }

    public function addContactMessage(int $patientId, string $sujet, string $message): bool {
        $stmt = $this->db->prepare("
            INSERT INTO contact_messages (id_patient, sujet, message, date_message, statut)
            VALUES (:patient_id, :sujet, :message, NOW(), 'unread')
        ");
        return $stmt->execute([
            ':patient_id' => $patientId,
            ':sujet' => $sujet,
            ':message' => $message
        ]);
    }
}
