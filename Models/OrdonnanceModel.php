<?php
/**
 * OrdonnanceModel — CRUD operations on the `ordonnance` table.
 */

require_once __DIR__ . '/../config/database.php';

class OrdonnanceModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Read ──────────────────────────────────────────────────────

    /**
     * Get ordonnance by consultation ID (one-to-one in practice).
     */
    public function getByConsultation(int $consultationId): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   c.diagnostic,
                   c.date_consultation,
                   c.rythme_cardiaque,
                   c.id_patient,
                   c.id_medecin,
                   CONCAT(up.prenom, ' ', up.nom) AS nom_patient,
                   up.mail  AS mail_patient,
                   um.nom   AS nom_medecin_nom,
                   um.prenom AS prenom_medecin
            FROM ordonnance o
            JOIN consultation c  ON c.id_consultation  = o.id_consultation
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE o.id_consultation = :cid
            LIMIT 1
        ");
        $stmt->execute([':cid' => $consultationId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get ordonnance by its own PK.
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   c.diagnostic,
                   c.date_consultation,
                   c.rythme_cardiaque,
                   c.id_patient,
                   c.id_medecin,
                   CONCAT(up.prenom, ' ', up.nom) AS nom_patient,
                   up.mail   AS mail_patient,
                   um.nom    AS nom_medecin_nom,
                   um.prenom AS prenom_medecin,
                   TIMESTAMPDIFF(YEAR, up.created_at, NOW()) AS age_approx
            FROM ordonnance o
            JOIN consultation c  ON c.id_consultation  = o.id_consultation
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            JOIN utilisateurs um ON um.id_PK = c.id_medecin
            WHERE o.id_ordonnance = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all ordonnances for a patient across all their consultations.
     */
    public function getByPatient(int $patientId): array {
        $stmt = $this->db->prepare("
            SELECT o.*, c.date_consultation, c.diagnostic
            FROM ordonnance o
            JOIN consultation c ON c.id_consultation = o.id_consultation
            WHERE c.id_patient = :pid
            ORDER BY o.date_emission DESC
        ");
        $stmt->execute([':pid' => $patientId]);
        return $stmt->fetchAll();
    }

    /**
     * Get ALL ordonnances across all patients, grouped by patient.
     * Returns flat list enriched with patient + doctor info, ordered by patient then date.
     */
    public function getAllGroupedByPatient(int $medecinId): array {
        $stmt = $this->db->prepare("
            SELECT
                o.id_ordonnance,
                o.numero_ordonnance,
                o.date_emission,
                o.medicaments,
                o.statut,
                o.note_pharmacien,
                c.id_consultation,
                c.id_patient,
                c.diagnostic,
                c.date_consultation,
                c.type_consultation,
                CONCAT(up.prenom, ' ', up.nom) AS nom_patient,
                up.mail   AS mail_patient,
                up.prenom AS prenom_patient,
                up.nom    AS nom_patient_famille
            FROM ordonnance o
            JOIN consultation c  ON c.id_consultation = o.id_consultation
            JOIN utilisateurs up ON up.id_PK = c.id_patient
            WHERE c.id_medecin = :mid
            ORDER BY up.nom ASC, up.prenom ASC, o.date_emission DESC
        ");
        $stmt->execute([':mid' => $medecinId]);
        $rows = $stmt->fetchAll();

        // Group by patient id
        $grouped = [];
        foreach ($rows as $row) {
            $pid = $row['id_patient'];
            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'id_patient'    => $pid,
                    'nom_patient'   => $row['nom_patient'],
                    'prenom_patient'=> $row['prenom_patient'],
                    'nom_famille'   => $row['nom_patient_famille'],
                    'mail_patient'  => $row['mail_patient'],
                    'ordonnances'   => [],
                ];
            }
            $grouped[$pid]['ordonnances'][] = $row;
        }
        return array_values($grouped);
    }

    // ── Write ─────────────────────────────────────────────────────

    /**
     * Create a new ordonnance.
     */
    public function create(array $data): int {
        // Auto-generate numero_ordonnance if not provided
        if (empty($data['numero_ordonnance'])) {
            $data['numero_ordonnance'] = 'ORD-' . date('Y') . '-' . strtoupper(uniqid());
        }

        $sql = "
            INSERT INTO ordonnance
                (id_consultation, numero_ordonnance, date_emission, medicaments, note_pharmacien, statut)
            VALUES
                (:cid, :num, :date, :meds, :note, :statut)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cid'    => $data['id_consultation'],
            ':num'    => $data['numero_ordonnance'],
            ':date'   => $data['date_emission'] ?? date('Y-m-d'),
            ':meds'   => $data['medicaments'],       // JSON string
            ':note'   => $data['note_pharmacien'] ?? null,
            ':statut' => $data['statut'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing ordonnance.
     */
    public function update(int $id, array $data): bool {
        $sql = "
            UPDATE ordonnance SET
                date_emission    = :date,
                medicaments      = :meds,
                note_pharmacien  = :note,
                statut           = :statut
            WHERE id_ordonnance = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':date'   => $data['date_emission'],
            ':meds'   => $data['medicaments'],
            ':note'   => $data['note_pharmacien'] ?? null,
            ':statut' => $data['statut'] ?? 'active',
            ':id'     => $id,
        ]);
    }

    /**
     * Delete an ordonnance.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM ordonnance WHERE id_ordonnance = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check if a consultation already has an ordonnance.
     */
    public function existsForConsultation(int $consultationId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM ordonnance WHERE id_consultation = :cid");
        $stmt->execute([':cid' => $consultationId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
