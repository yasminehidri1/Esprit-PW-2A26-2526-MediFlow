<?php
/**
 * Initialize missing users and roles tables for MediFlow Magazine
 * Adds the required utilisateurs and roles tables with sample data
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Create roles table
    $db->exec("CREATE TABLE IF NOT EXISTS `roles` (
        `id_role` int(11) NOT NULL AUTO_INCREMENT,
        `libelle` varchar(50) NOT NULL,
        PRIMARY KEY (`id_role`),
        UNIQUE KEY `unique_role_name` (`libelle`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Create utilisateurs table
    $db->exec("CREATE TABLE IF NOT EXISTS `utilisateurs` (
        `id_PK` int(11) NOT NULL AUTO_INCREMENT,
        `nom` varchar(100) NOT NULL,
        `prenom` varchar(100) NOT NULL,
        `mail` varchar(100) NOT NULL UNIQUE,
        `id_role` int(11) NOT NULL DEFAULT 1,
        `date_creation` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id_PK`),
        KEY `fk_utilisateurs_role` (`id_role`),
        CONSTRAINT `fk_utilisateurs_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE SET DEFAULT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Insert sample roles
    $db->exec("INSERT IGNORE INTO `roles` (`id_role`, `libelle`) VALUES
        (1, 'User'),
        (2, 'Author'),
        (3, 'Admin');");

    // Insert sample users (for comments)
    $db->exec("INSERT IGNORE INTO `utilisateurs` (`id_PK`, `nom`, `prenom`, `mail`, `id_role`) VALUES
        (1, 'Dupont', 'Marie', 'marie.dupont@mediflow.com', 2),
        (2, 'Martin', 'Jean', 'jean.martin@mediflow.com', 2),
        (3, 'Bernard', 'Paul', 'paul.bernard@mediflow.com', 3),
        (4, 'Reader', 'Test', 'test@mediflow.com', 1),
        (5, 'Johnson', 'Sarah', 'sarah.johnson@mediflow.com', 1),
        (6, 'Williams', 'Emma', 'emma.williams@mediflow.com', 1);");

    echo "✓ Tables created successfully\n";
    echo "✓ Sample roles inserted\n";
    echo "✓ Sample users inserted\n";
    echo "\nDatabase initialization complete!\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
