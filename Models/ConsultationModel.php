<?php
/**
 * ConsultationModel — CRUD operations on the `consultation` table.
 */

require_once __DIR__ . '/../config/database.php';

class ConsultationModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Read ──────────────────────────────────────────────────────

    /**
     * Get all patients (role = 7) with stats about their consultations.
     * Used by the patient list view.
     */
    public function getAllPatients(int $medecinId, int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT
                u.id_PK,
                u.nom,
                u.prenom,
                u.mail,
                u.tel,
                u.adresse,
                MAX(c.date_consultation) AS derniere_visite,
                MAX(c.type_consultation) AS dernier_type,
                MAX(c.diagnostic)       AS dernier_diagnostic,
                COUNT(c.id_consultation) AS nb_consultations,
                (SELECT id_consultation FROM consultation
                 WHERE id_patient = u.id_PK AND id_medecin = :mid2
                 ORDER BY date_consultation DESC LIMIT 1) AS derniere_consult_id
            FROM utilisateurs u
            JOIN roles r ON r.id_role = u.id_role
            JOIN consultation c
                ON c.id_patient = u.id_PK AND c.id_medecin = :mid
            WHERE r.libelle = 'Patient'
            GROUP BY u.id_PK
            ORDER BY derniere_visite DESC
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mid',  $medecinId, PDO::PARAM_INT);
        $stmt->bindValue(':mid2', $medecinId, PDO::PARAM_INT);
        $stmt->bindValue(':lim',  $limit,     PDO::PARAM_INT);
        $stmt->bindValue(':off',  $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Total count of patients for pagination. */
    public function countPatients(int $medecinId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT u.id_PK) 
            FROM utilisateurs u 
            JOIN roles r ON r.id_role = u.id_role 
            JOIN consultation c ON c.id_patient = u.id_PK 
            WHERE r.libelle = 'Patient' AND c.id_medecin = :mid
        ");
        $stmt->execute([':mid' => $medecinId]);
        return (int) $stmt->fetchColumn();
    }

    /** Stats for today's queue. */
    public function getTodayStats(int $medecinId): array {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total_today,
                SUM(CASE WHEN compte_rendu IS NOT NULL AND compte_rendu != '' THEN 1 ELSE 0 END) AS termines,
                SUM(CASE WHEN compte_rendu IS NULL OR compte_rendu = '' THEN 1 ELSE 0 END)  AS en_attente
            FROM consultation
            WHERE id_medecin = :mid
              AND DATE(date_consultation) = CURDATE()
        ");
        $stmt->execute([':mid' => $medecinId]);
        $row = $stmt->fetch();
        return $row ?: ['total_today' => 0, 'termines' => 0, 'en_attente' => 0];
    }

    /**
     * Get a single patient's info from utilisateurs.
     */
    public function getPatientById(int $patientId): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.libelle AS role_libelle
            FROM utilisateurs u
            JOIN roles r ON r.id_role = u.id_role
            WHERE u.id_PK = :id AND r.libelle = 'Patient'
        ");
        $stmt->execute([':id' => $patientId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all consultations for a patient, most recent first.
     */
    public function getConsultationsByPatient(int $patientId, int $medecinId): array {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   CONCAT(u.prenom, ' ', u.nom) AS nom_medecin
            FROM consultation c
            JOIN utilisateurs u ON u.id_PK = c.id_medecin
            WHERE c.id_patient = :pid
            ORDER BY c.date_consultation DESC
        ");
        $stmt->execute([':pid' => $patientId]);
        return $stmt->fetchAll();
    }

    /**
     * Get a single consultation by ID.
     */
    public function getConsultationById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   CONCAT(up.prenom, ' ', up.nom) AS nom_patient,
                   up.mail AS mail_patient,
                   up.tel  AS tel_patient,
                   up.adresse AS adresse_patient,
                   CONCAT(um.prenom, ' ', um.nom) AS nom_medecin
            FROM consultation c
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE c.id_consultation = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get the most recent consultation for a patient (for vitals + antecedents).
     */
    public function getLatestConsultation(int $patientId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM consultation
            WHERE id_patient = :pid
            ORDER BY date_consultation DESC
            LIMIT 1
        ");
        $stmt->execute([':pid' => $patientId]);
        return $stmt->fetch() ?: null;
    }

    // ── Write ─────────────────────────────────────────────────────

    /**
     * Create a new consultation record.
     */
    public function createConsultation(array $data): int {
        $sql = "
            INSERT INTO consultation
                (id_medecin, id_patient, date_consultation, type_consultation,
                 diagnostic, compte_rendu,
                 tension_arterielle, rythme_cardiaque, poids, saturation_o2,
                 antecedents, allergies)
            VALUES
                (:mid, :pid, :date, :type,
                 :diagnostic, :compterendu,
                 :tension, :rythme, :poids, :saturation,
                 :antecedents, :allergies)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':mid'         => $data['id_medecin'],
            ':pid'         => $data['id_patient'],
            ':date'        => $data['date_consultation'],
            ':type'        => $data['type_consultation'],
            ':diagnostic'  => $data['diagnostic'],
            ':compterendu' => $data['compte_rendu'],
            ':tension'     => $data['tension_arterielle'] ?? null,
            ':rythme'      => $data['rythme_cardiaque']   ?? null,
            ':poids'       => $data['poids']              ?? null,
            ':saturation'  => $data['saturation_o2']      ?? null,
            ':antecedents' => $data['antecedents']        ?? null,
            ':allergies'   => $data['allergies']          ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing consultation.
     */
    public function updateConsultation(int $id, array $data): bool {
        $sql = "
            UPDATE consultation SET
                date_consultation  = :date,
                type_consultation  = :type,
                diagnostic         = :diagnostic,
                compte_rendu       = :compterendu,
                tension_arterielle = :tension,
                rythme_cardiaque   = :rythme,
                poids              = :poids,
                saturation_o2      = :saturation,
                antecedents        = :antecedents,
                allergies          = :allergies
            WHERE id_consultation = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':date'        => $data['date_consultation'],
            ':type'        => $data['type_consultation'],
            ':diagnostic'  => $data['diagnostic'],
            ':compterendu' => $data['compte_rendu'],
            ':tension'     => $data['tension_arterielle'] ?? null,
            ':rythme'      => $data['rythme_cardiaque']   ?? null,
            ':poids'       => $data['poids']              ?? null,
            ':saturation'  => $data['saturation_o2']      ?? null,
            ':antecedents' => $data['antecedents']        ?? null,
            ':allergies'   => $data['allergies']          ?? null,
            ':id'          => $id,
        ]);
    }

    /**
     * Delete a consultation (its ordonnance is cascade-deleted by FK).
     */
    public function deleteConsultation(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM consultation WHERE id_consultation = :id");
        return $stmt->execute([':id' => $id]);
    }
}
