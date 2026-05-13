<?php

class NotificationModel {

    private string $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../data/notifications.json';
        $this->initFile();
    }

    private function initFile(): void {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    private function readAll(): array {
        return json_decode(file_get_contents($this->filePath), true) ?: [];
    }

    private function writeAll(array $data): void {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    /** Crée une notification pour un médecin. */
    public function add(int $medecinId, string $type, string $title, string $message, int $demandeId = 0): void {
        $all   = $this->readAll();
        $maxId = empty($all) ? 0 : max(array_column($all, 'id'));

        $all[] = [
            'id'         => $maxId + 1,
            'medecin_id' => $medecinId,
            'type'       => $type,      // new_demande | demande_traitee | demande_refusee
            'title'      => $title,
            'message'    => $message,
            'demande_id' => $demandeId,
            'read'       => false,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->writeAll($all);
    }

    /** Retourne les notifications d'un médecin (non lues en premier). */
    public function getByMedecin(int $medecinId, int $limit = 20): array {
        $all      = $this->readAll();
        $filtered = array_filter($all, fn($n) => (int)$n['medecin_id'] === $medecinId);
        usort($filtered, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return array_values(array_slice($filtered, 0, $limit));
    }

    /** Compte les notifications non lues d'un médecin. */
    public function countUnread(int $medecinId): int {
        $all = $this->readAll();
        return count(array_filter($all, fn($n) => (int)$n['medecin_id'] === $medecinId && !$n['read']));
    }

    /** Marque toutes les notifications d'un médecin comme lues. */
    public function markAllRead(int $medecinId): void {
        $all = $this->readAll();
        foreach ($all as &$n) {
            if ((int)$n['medecin_id'] === $medecinId) $n['read'] = true;
        }
        unset($n);
        $this->writeAll($all);
    }
}
