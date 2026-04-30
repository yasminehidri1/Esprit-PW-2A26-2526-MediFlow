<?php
/**
 * config/database.php — Database bridge for MediFlow models.
 *
 * All models do: require_once __DIR__ . '/../config/database.php';
 * and then call: Database::getInstance()
 *
 * This file loads the root config.php (which holds the real PDO singleton
 * under class `config`) and exposes a `Database` class whose static
 * getInstance() method delegates to config::getConnexion().
 *
 * This means ALL models work without any changes to their code.
 */

// Load the real PDO config from project root (one level up from config/)
require_once __DIR__ . '/../config.php';

if (!class_exists('Database')) {
    class Database {
        /**
         * Returns the shared PDO connection via the existing config singleton.
         */
        public static function getInstance(): PDO {
            return config::getConnexion();
        }
    }
}
