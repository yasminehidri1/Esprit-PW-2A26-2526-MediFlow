<?php
/**
 * DemandeOrdonnanceModel — Gestion des demandes d'ordonnance via fichier JSON.
 * Aucune modification de la base de données requise.
 */

class DemandeOrdonnanceModel {

    private string $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../data/demandes.json';
        $this->initFile();
    }

    // ── Initialisation ─────────────────────────────────────────────

    private function initFile(): void {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    // ── Lecture ────────────────────────────────────────────────────

    private function readAll(): array {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?: [];
    }

    private function writeAll(array $data): void {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    /** Retourne toutes les demandes destinées à un médecin.
     *  Les demandes en_attente sont triées par urgence IA décroissante,
     *  puis par date. Les traitées/refusées suivent ensuite. */
    public function getDemandesByMedecin(int $medecinId): array {
        $all      = $this->readAll();
        $filtered = array_filter($all, fn($d) => (int)$d['id_medecin'] === $medecinId);

        $urgencePriority = ['tres_urgent' => 0, 'urgent' => 1, 'normale' => 2, null => 3, '' => 3];

        usort($filtered, function ($a, $b) use ($urgencePriority) {
            $aWaiting = $a['statut'] === 'en_attente';
            $bWaiting = $b['statut'] === 'en_attente';

            // En attente avant traitée/refusée
            if ($aWaiting !== $bWaiting) return $bWaiting <=> $aWaiting;

            if ($aWaiting) {
                // Tri par urgence IA (ou urgence patient si pas encore analysée)
                $aUrgence = $a['ai_urgence'] ?? $a['urgence'] ?? null;
                $bUrgence = $b['ai_urgence'] ?? $b['urgence'] ?? null;
                $pA = $urgencePriority[$aUrgence] ?? 3;
                $pB = $urgencePriority[$bUrgence] ?? 3;
                if ($pA !== $pB) return $pA <=> $pB;
            }

            // À urgence égale : plus récente d'abord
            return strcmp($b['created_at'], $a['created_at']);
        });

        return array_values($filtered);
    }

    /** Nombre de demandes en attente pour un médecin (pour le badge sidebar). */
    public function countPendingByMedecin(int $medecinId): int {
        $demandes = $this->getDemandesByMedecin($medecinId);
        return count(array_filter($demandes, fn($d) => $d['statut'] === 'en_attente'));
    }

    /** Retourne une demande par son ID. */
    public function getDemandeById(int $id): ?array {
        foreach ($this->readAll() as $d) {
            if ((int)$d['id_demande'] === $id) return $d;
        }
        return null;
    }

    // ── Écriture ───────────────────────────────────────────────────

    /** Crée une nouvelle demande et retourne son ID. */
    public function createDemande(int $patientId, int $medecinId, string $description, string $urgence = 'normale'): int {
        $all   = $this->readAll();
        $maxId = empty($all) ? 0 : max(array_column($all, 'id_demande'));
        $newId = $maxId + 1;

        $all[] = [
            'id_demande'       => $newId,
            'id_patient'       => $patientId,
            'id_medecin'       => $medecinId,
            'description'      => $description,
            'urgence'          => $urgence,
            'ai_urgence'       => null,
            'ai_justification' => null,
            'ai_refus_message' => null,
            'statut'           => 'en_attente',
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->writeAll($all);
        return $newId;
    }

    /** Stocke l'analyse IA (urgence + justification) sur une demande. */
    public function updateAiAnalysis(int $id, string $aiUrgence, string $aiJustification): bool {
        $all   = $this->readAll();
        $found = false;
        foreach ($all as &$d) {
            if ((int)$d['id_demande'] === $id) {
                $d['ai_urgence']       = $aiUrgence;
                $d['ai_justification'] = $aiJustification;
                $found = true;
                break;
            }
        }
        unset($d);
        if ($found) $this->writeAll($all);
        return $found;
    }

    /** Met à jour le statut d'une demande ('en_attente', 'traitee', 'refusee'). */
    public function updateStatut(int $id, string $statut, ?string $aiRefusMessage = null): bool {
        $valid = ['en_attente', 'traitee', 'refusee'];
        if (!in_array($statut, $valid, true)) return false;

        $all   = $this->readAll();
        $found = false;
        foreach ($all as &$d) {
            if ((int)$d['id_demande'] === $id) {
                $d['statut'] = $statut;
                if ($aiRefusMessage !== null) $d['ai_refus_message'] = $aiRefusMessage;
                $found = true;
                break;
            }
        }
        unset($d);

        if ($found) {
            $this->writeAll($all);
        }
        return $found;
    }
}
