<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "=== Database Connection: SUCCESS ===\n\n";

    // Check tables
    $tables = ['posts', 'comments', 'utilisateurs', 'roles'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        $result = $stmt->fetch();
        echo "$table: " . ($result ? "EXISTS" : "MISSING") . "\n";
    }

    echo "\n=== Data Count ===\n";
    foreach (['posts', 'comments', 'utilisateurs'] as $table) {
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM $table");
        $result = $stmt->fetch();
        echo "$table: " . ($result['cnt'] ?? 0) . " rows\n";
    }

    echo "\n=== User ID 4 (for comments) ===\n";
    $stmt = $db->prepare("SELECT id_PK, nom, prenom FROM utilisateurs WHERE id_PK = 4");
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user) {
        echo "Found: " . $user['prenom'] . " " . $user['nom'] . "\n";
    } else {
        echo "User ID 4 NOT FOUND - Comments will fail on constraint!\n";
    }

} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
