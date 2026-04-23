<?php
/**
 * Session Helper Trait
 * 
 * Provides reusable session management methods for controllers
 * 
 * @package MediFlow\Core
 * @version 1.0.0
 */

namespace Core;

trait SessionHelper
{
    /**
     * Ensure session is started
     * 
     * @return void
     */
    protected function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
    }

    /**
     * Redirect to login if not authenticated
     * 
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: /Mediflow/login');
            exit;
        }
    }

    /**
     * Get database connection
     * 
     * @return \PDO
     */
    protected function getDatabase(): \PDO
    {
        require_once __DIR__ . '/../config.php';
        return \config::getConnexion();
    }

    /**
     * Get safe POST parameter
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getPost(string $key, $default = null)
    {
        return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }
}
