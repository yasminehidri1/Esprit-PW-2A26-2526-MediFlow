<?php
/**
 * AdminModel — CRUD and stat operations for admin dashboard.
 */

require_once __DIR__ . '/../config/database.php';

class AdminModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Statistics ────────────────────────────────────────────────

    /**
     * Get total count of doctors (users with role = 'Medecin').
     */
    public function getDoctorStats(): array {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT u.id_PK) AS total_doctors
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            WHERE r.libelle = 'Medecin'
        ");
        $row = $stmt->fetch();
        return [
            'total_doctors' => (int)($row['total_doctors'] ?? 0),
            'growth' => '+12%'  // Placeholder
        ];
    }

    /**
     * Get count of active consultations (today or recent).
     */
    public function getActiveConsultations(): array {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS active_count
            FROM consultation
            WHERE DATE(date_consultation) = CURDATE()
        ");
        $row = $stmt->fetch();
        return [
            'active_count' => (int)($row['active_count'] ?? 0),
            'status' => 'En direct'
        ];
    }

    /**
     * Get total count of prescriptions.
     */
    public function getTotalPrescriptions(): array {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total_prescriptions
            FROM ordonnance
        ");
        $row = $stmt->fetch();
        return [
            'total_prescriptions' => (int)($row['total_prescriptions'] ?? 0),
            'badge' => 'Record'
        ];
    }

    /**
     * Get revenue overview (placeholder).
     */
    public function getRevenueOverview(): array {
        return [
            'revenue' => '€84,200.00',
            'trend' => '+5.2%'
        ];
    }

    // ── Doctors ───────────────────────────────────────────────────

    /**
     * Get all doctors with pagination and sort.
     */
    public function getAllDoctors(int $limit = 10, int $offset = 0, string $sortBy = 'prenom', string $sortOrder = 'ASC'): array {
        $allowed   = ['prenom', 'nom', 'mail', 'nb_patients', 'role_libelle'];
        $sortBy    = in_array($sortBy, $allowed, true) ? $sortBy : 'prenom';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "
            SELECT u.id_PK, u.nom, u.prenom, u.mail, u.tel, u.adresse, u.id_role,
                   r.libelle AS role_libelle,
                   COALESCE(COUNT(DISTINCT c.id_patient), 0) AS nb_patients
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            LEFT JOIN consultation c ON c.id_medecin = u.id_PK
            WHERE r.libelle = 'Medecin'
            GROUP BY u.id_PK
            ORDER BY {$sortBy} {$sortOrder}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total doctors for pagination.
     */
    public function countDoctors(): int {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT u.id_PK) FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            WHERE r.libelle = 'Medecin'
        ");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get total count of patients.
     */
    public function getTotalPatients(): int {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT u.id_PK) FROM utilisateurs u
            JOIN roles r ON r.id_role = u.id_role
            WHERE r.libelle = 'Patient'
        ");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get consultation count per day for the last N days.
     */
    public function getConsultationsPerDay(int $days = 7): array {
        $sql = "
            SELECT DATE(date_consultation) AS day, COUNT(*) AS count
            FROM consultation
            WHERE date_consultation >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(date_consultation)
            ORDER BY day ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get top doctors by patient count.
     */
    public function getTopDoctors(int $limit = 5): array {
        $sql = "
            SELECT u.id_PK, u.nom, u.prenom,
                   COALESCE(COUNT(DISTINCT c.id_patient), 0) AS nb_patients
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            LEFT JOIN consultation c ON c.id_medecin = u.id_PK
            WHERE r.libelle = 'Medecin'
            GROUP BY u.id_PK
            ORDER BY nb_patients DESC
            LIMIT :lim
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get featured staff/personnel for dashboard (limited) - all staff, not just doctors.
     */
    public function getFeaturedStaff(int $limit = 3): array {
        $sql = "
            SELECT u.id_PK, u.nom, u.prenom, u.mail, u.id_role,
                   r.libelle AS role_name,
                   COALESCE(COUNT(DISTINCT c.id_patient), 0) AS nb_patients
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            LEFT JOIN consultation c ON c.id_medecin = u.id_PK
            WHERE u.id_role != 7
            GROUP BY u.id_PK
            ORDER BY nb_patients DESC, u.prenom, u.nom
            LIMIT :lim
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Breakdown of consultations by type (Urgence / Suivi / Contrôle / Standard).
     */
    public function getConsultationsByType(): array {
        $stmt = $this->db->query("
            SELECT
                CASE
                    WHEN LOWER(type_consultation) LIKE '%urgent%' THEN 'Urgence'
                    WHEN LOWER(type_consultation) LIKE '%suivi%'  THEN 'Suivi'
                    WHEN LOWER(type_consultation) LIKE '%annuel%'
                      OR LOWER(type_consultation) LIKE '%contrôle%'
                      OR LOWER(type_consultation) LIKE '%controle%' THEN 'Contrôle'
                    ELSE 'Standard'
                END AS type_label,
                COUNT(*) AS count
            FROM consultation
            GROUP BY type_label
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Breakdown of prescriptions by status (active / archivee / annulee).
     */
    public function getPrescriptionsByStatus(): array {
        $stmt = $this->db->query("
            SELECT statut, COUNT(*) AS count
            FROM ordonnance
            GROUP BY statut
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Compare consultations: this week vs last week.
     */
    public function getWeeklyComparison(): array {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN date_consultation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)  THEN 1 ELSE 0 END) AS cette_semaine,
                SUM(CASE WHEN date_consultation >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                          AND date_consultation <  DATE_SUB(CURDATE(), INTERVAL 7 DAY)  THEN 1 ELSE 0 END) AS semaine_passee
            FROM consultation
        ");
        $row = $stmt->fetch();
        $curr = (int)($row['cette_semaine']  ?? 0);
        $prev = (int)($row['semaine_passee'] ?? 0);
        $diff  = $curr - $prev;
        $trend = $prev > 0 ? round(($diff / $prev) * 100) : ($curr > 0 ? 100 : 0);
        return [
            'cette_semaine'  => $curr,
            'semaine_passee' => $prev,
            'diff'           => $diff,
            'trend_pct'      => $trend,
        ];
    }

    /**
     * Count consultations completed (with compte_rendu) vs pending.
     */
    public function getConsultationCompletion(): array {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN compte_rendu IS NOT NULL AND compte_rendu != '' THEN 1 ELSE 0 END) AS completees,
                SUM(CASE WHEN compte_rendu IS     NULL OR  compte_rendu  = ''  THEN 1 ELSE 0 END) AS en_attente,
                COUNT(*) AS total
            FROM consultation
        ");
        return $stmt->fetch() ?: ['completees' => 0, 'en_attente' => 0, 'total' => 0];
    }

    // ── Consultations ─────────────────────────────────────────────

    /**
     * Get all consultations with pagination.
     */
    public function getAllConsultations(int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT c.id_consultation,
                   up.prenom AS patient_prenom, up.nom AS patient_nom,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom,
                   c.date_consultation, c.type_consultation, c.diagnostic,
                   CASE
                     WHEN c.compte_rendu IS NOT NULL AND c.compte_rendu != '' THEN 'Complétée'
                     ELSE 'En attente'
                   END AS statut
            FROM consultation c
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            ORDER BY c.date_consultation DESC
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total consultations for pagination.
     */
    public function countConsultations(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM consultation");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get recent consultations for dashboard.
     */
    public function getRecentConsultations(int $limit = 4): array {
        $sql = "
            SELECT c.id_consultation,
                   up.prenom AS patient_prenom, up.nom AS patient_nom,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom,
                   c.date_consultation, c.type_consultation,
                   CASE
                     WHEN c.type_consultation LIKE '%urgent%' THEN 'Urgent'
                     WHEN c.type_consultation LIKE '%suivi%' THEN 'Suivi'
                     ELSE 'Standard'
                   END AS urgence
            FROM consultation c
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            ORDER BY c.date_consultation DESC
            LIMIT :lim
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Prescriptions ─────────────────────────────────────────────

    /**
     * Get all prescriptions with pagination.
     */
    public function getAllPrescriptions(int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT o.id_ordonnance, o.numero_ordonnance, o.date_emission, o.statut,
                   up.prenom AS patient_prenom, up.nom AS patient_nom,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom,
                   c.id_consultation
            FROM ordonnance o
            LEFT JOIN consultation c ON c.id_consultation = o.id_consultation
            LEFT JOIN utilisateurs up ON up.id_PK = c.id_patient
            LEFT JOIN utilisateurs um ON um.id_PK = c.id_medecin
            ORDER BY o.date_emission DESC
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total prescriptions for pagination.
     */
    public function countPrescriptions(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM ordonnance");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get prescription stats.
     */
    public function getPrescriptionStats(): array {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN DATE(date_emission) = CURDATE() THEN 1 ELSE 0 END) AS validated_today,
                SUM(CASE WHEN statut = 'active' THEN 1 ELSE 0 END) AS active_count,
                SUM(CASE WHEN statut NOT IN ('active', 'archivee') THEN 1 ELSE 0 END) AS pending_signature
            FROM ordonnance
        ");
        $row = $stmt->fetch();
        return [
            'validated_today' => (int)($row['validated_today'] ?? 0),
            'active_count' => (int)($row['active_count'] ?? 0),
            'pending_signature' => (int)($row['pending_signature'] ?? 0)
        ];
    }

    // ── Doctor CRUD ───────────────────────────────────────────────

    /**
     * Get a single doctor by ID.
     */
    public function getDoctorById(int $doctorId): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.libelle AS role_libelle
            FROM utilisateurs u
            LEFT JOIN roles r ON r.id_role = u.id_role
            WHERE u.id_PK = :id AND u.id_role != 7
        ");
        $stmt->execute([':id' => $doctorId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Update a doctor's information.
     */
    public function updateDoctor(int $doctorId, array $data): bool {
        $sql = "
            UPDATE utilisateurs SET
                nom = :nom,
                prenom = :prenom,
                mail = :mail,
                tel = :tel,
                adresse = :adresse
            WHERE id_PK = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':mail' => $data['mail'],
            ':tel' => $data['tel'] ?? null,
            ':adresse' => $data['adresse'] ?? null,
            ':id' => $doctorId,
        ]);
    }

    /**
     * Delete a doctor.
     */
    public function deleteDoctor(int $doctorId): bool {
        $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id_PK = :id AND id_role != 7");
        return $stmt->execute([':id' => $doctorId]);
    }

    // ── Consultation Detail ───────────────────────────────────────

    /**
     * Get consultation details.
     */
    public function getConsultationDetail(int $consultationId): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   up.prenom AS patient_prenom, up.nom AS patient_nom, up.mail AS patient_mail,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom, um.mail AS medecin_mail
            FROM consultation c
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE c.id_consultation = :id
        ");
        $stmt->execute([':id' => $consultationId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Delete a consultation and its related prescriptions.
     */
    public function deleteConsultation(int $consultationId): bool {
        // Delete associated prescriptions first (cascade)
        $stmt = $this->db->prepare("DELETE FROM ordonnance WHERE id_consultation = :id");
        $stmt->execute([':id' => $consultationId]);

        // Then delete the consultation
        $stmt = $this->db->prepare("DELETE FROM consultation WHERE id_consultation = :id");
        return $stmt->execute([':id' => $consultationId]);
    }

    // ── Prescription Detail & Management ──────────────────────────

    /**
     * Get prescription details.
     */
    public function getPrescriptionDetail(int $prescriptionId): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   up.prenom AS patient_prenom, up.nom AS patient_nom, up.mail AS patient_mail,
                   um.prenom AS medecin_prenom, um.nom AS medecin_nom, um.mail AS medecin_mail
            FROM ordonnance o
            LEFT JOIN consultation c ON c.id_consultation = o.id_consultation
            LEFT JOIN utilisateurs up ON up.id_PK = c.id_patient
            LEFT JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE o.id_ordonnance = :id
        ");
        $stmt->execute([':id' => $prescriptionId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Update prescription status.
     */
    public function updatePrescriptionStatus(int $prescriptionId, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE ordonnance SET statut = :status
            WHERE id_ordonnance = :id
        ");
        return $stmt->execute([':status' => $status, ':id' => $prescriptionId]);
    }

    /**
     * Delete a prescription.
     */
    public function deletePrescription(int $prescriptionId): bool {
        $stmt = $this->db->prepare("DELETE FROM ordonnance WHERE id_ordonnance = :id");
        return $stmt->execute([':id' => $prescriptionId]);
    }

    // ── Doctor's Patient Management ───────────────────────────────

    /**
     * Get all patients treated by a specific doctor with consultation and prescription counts.
     */
    public function getPatientsByDoctor(int $doctorId): array {
        $sql = "
            SELECT DISTINCT
                   u.id_PK, u.nom, u.prenom, u.mail, u.tel, u.adresse,
                   COALESCE(COUNT(DISTINCT c.id_consultation), 0) AS nb_consultations,
                   COALESCE(SUM(CASE WHEN o.id_ordonnance IS NOT NULL THEN 1 ELSE 0 END), 0) AS nb_ordonnances,
                   MAX(c.date_consultation) AS last_consultation
            FROM utilisateurs u
            JOIN roles r ON r.id_role = u.id_role
            INNER JOIN consultation c ON c.id_patient = u.id_PK
            LEFT JOIN ordonnance o ON o.id_consultation = c.id_consultation
            WHERE c.id_medecin = :doctor_id AND r.libelle = 'Patient'
            GROUP BY u.id_PK, u.nom, u.prenom, u.mail, u.tel, u.adresse
            ORDER BY u.prenom, u.nom
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':doctor_id' => $doctorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all consultations of a patient for a specific doctor.
     */
    public function getPatientConsultations(int $patientId, int $doctorId): array {
        $sql = "
            SELECT c.id_consultation,
                   c.date_consultation, c.type_consultation, c.diagnostic,
                   c.compte_rendu, c.tension_arterielle, c.rythme_cardiaque,
                   c.poids, c.saturation_o2, c.antecedents, c.allergies,
                   CASE
                     WHEN c.compte_rendu IS NOT NULL AND c.compte_rendu != '' THEN 'Complétée'
                     ELSE 'En attente'
                   END AS statut
            FROM consultation c
            WHERE c.id_patient = :patient_id AND c.id_medecin = :doctor_id
            ORDER BY c.date_consultation DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':patient_id' => $patientId, ':doctor_id' => $doctorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all prescriptions related to a patient's consultations with a specific doctor.
     */
    public function getPatientPrescriptions(int $patientId, int $doctorId): array {
        $sql = "
            SELECT o.id_ordonnance, o.numero_ordonnance, o.date_emission,
                   o.medicaments, o.note_pharmacien, o.statut,
                   c.id_consultation, c.type_consultation, c.date_consultation
            FROM ordonnance o
            INNER JOIN consultation c ON c.id_consultation = o.id_consultation
            WHERE c.id_patient = :patient_id AND c.id_medecin = :doctor_id
            ORDER BY o.date_emission DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':patient_id' => $patientId, ':doctor_id' => $doctorId]);
        return $stmt->fetchAll();
    }
}

