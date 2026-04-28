<?php
require_once __DIR__ . '/config.php';
$pdo = config::getConnexion();
try {
    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN cin char(8) DEFAULT NULL AFTER motdp");
    echo "Column 'cin' added to 'utilisateurs'.\n";
    $pdo->exec("UPDATE utilisateurs SET cin = '12345678' WHERE mail = 'john.doe@gmail.com'");
    echo "Patient 'john.doe' updated with CIN '12345678'.\n";
} catch (Exception $e) {
    echo "INFO: " . $e->getMessage() . "\n";
}
