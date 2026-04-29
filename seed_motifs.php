<?php
require_once 'config.php';
try {
    $pdo = config::getConnexion();
    $motifs = ['Consultation générale', 'Suivi post-opératoire', 'Urgence', 'Vaccination', 'Examen de routine'];
    $stmt = $pdo->query("SELECT id FROM rendez_vous");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $update = $pdo->prepare("UPDATE rendez_vous SET motif = :m WHERE id = :id");
    foreach ($ids as $id) {
        $update->execute([
            ':m' => $motifs[array_rand($motifs)],
            ':id' => $id
        ]);
    }
    echo "Motifs updated for " . count($ids) . " records.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
