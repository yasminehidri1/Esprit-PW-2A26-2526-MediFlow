<?php
require_once 'config.php';
try {
    $pdo = config::getConnexion();
    $pdo->exec("ALTER TABLE rendez_vous ADD COLUMN motif VARCHAR(100) DEFAULT 'Consultation générale' AFTER heure_rdv");
    echo "Column 'motif' added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
