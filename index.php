<?php
/**
 * MediFlow Application Entry Point
 * 
 * This is the main entry point for the MediFlow hospital management system.
 * All requests are routed through this file.
 * 
 * @package MediFlow
 * @version 1.0.0
 * @author Out of the Box Team - Esprit 2A26
 */

declare(strict_types=1);

// Load Composer autoloader (for PHPMailer and other packages)
require_once __DIR__ . '/vendor/autoload.php';

// Load the custom application autoloader
require_once __DIR__ . '/Core/Autoloader.php';
\Core\Autoloader::register();

// Initialize and run the application
$app = new \Core\App();
$app->run();
