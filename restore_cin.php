<?php
require_once 'config.php';
try {
    $pdo = config::getConnexion();
    // Add cin column to utilisateurs
    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN cin CHAR(8) AFTER id_role");
    echo "Column 'cin' added to 'utilisateurs' successfully.\n";

    // Seed CIN for our test patient John Doe (let's assume John Doe is id_PK 17 or similar)
    // We'll update all patients with a dummy CIN for testing
    $pdo->exec("UPDATE utilisateurs SET cin = '12345678' WHERE id_role = 9"); // 9 = Patient
    echo "Test CINs seeded for patients.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
