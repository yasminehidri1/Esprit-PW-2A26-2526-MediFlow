<?php
require_once 'c:/xampp/htdocs/Mediflow/config.php';
$pdo  = config::getConnexion();
$cols = $pdo->query('SHOW COLUMNS FROM reservation LIKE "matricule"')->fetchAll();
if (count($cols) === 0) {
    $pdo->exec('ALTER TABLE reservation ADD COLUMN matricule VARCHAR(50) NULL AFTER locataire_nom');
    echo 'Column matricule added successfully.';
} else {
    echo 'Column matricule already exists.';
}
