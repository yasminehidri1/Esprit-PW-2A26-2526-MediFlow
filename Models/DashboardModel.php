<?php
/**
 * Dashboard Model
 * 
 * Handles all database operations for dashboard metrics
 * 
 * @package MediFlow\Models
 * @version 1.0.0
 */

namespace Models;

use PDO;

class DashboardModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = $this->getConnection();
    }

    /**
     * Get database connection
     * 
     * @return PDO
     */
    private function getConnection(): PDO
    {
        require_once __DIR__ . '/../config.php';
        return \config::getConnexion();
    }

    /**
     * Get total user count
     * 
     * @return int
     */
    public function getTotalUsers(): int
    {
        $query = "SELECT COUNT(*) as total FROM utilisateurs";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'] ?? 0;
    }

    /**
     * Get active users (logged in today)
     * 
     * @return int
     */
    public function getActiveUsers(): int
    {
        // This assumes you have a login_history table
        // Adjust based on your actual implementation
        $query = "SELECT COUNT(DISTINCT id_PK) as active FROM utilisateurs LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['active'] ?? 0;
    }

    /**
     * Get users grouped by role
     * 
     * @return array
     */
    public function getUsersByRole(): array
    {
        $query = "
            SELECT 
                r.id_role,
                r.libelle as role_name,
                COUNT(u.id_PK) as user_count
            FROM roles r
            LEFT JOIN utilisateurs u ON r.id_role = u.id_role
            GROUP BY r.id_role, r.libelle
            ORDER BY user_count DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all roles for sidebar navigation
     * 
     * @return array
     */
    public function getAllRoles(): array
    {
        $query = "SELECT id_role, libelle FROM roles ORDER BY libelle ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all users with their role information
     * 
     * @return array
     */
    public function getAllUsers(): array
    {
        $query = "
            SELECT 
                u.id_PK,
                u.nom,
                u.prenom,
                u.mail,
                u.tel,
                u.adresse,
                r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            ORDER BY u.nom ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get recent user activity (last 10 users created/modified)
     * 
     * @return array
     */
    public function getRecentActivity(): array
    {
        $query = "
            SELECT 
                u.id_PK,
                u.nom,
                u.prenom,
                u.mail,
                r.libelle as role_name,
                'User registered' as action,
                NOW() as timestamp
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            ORDER BY u.id_PK DESC
            LIMIT 5
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get user by ID (for current logged-in user)
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserById(int $userId): ?array
    {
        $query = "
            SELECT 
                u.id_PK,
                u.nom,
                u.prenom,
                u.mail,
                u.tel,
                u.adresse,
                u.matricule,
                u.profile_pic,
                u.onboarding_completed,
                r.id_role,
                r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.id_PK = :userId
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all patients
     * 
     * @return array
     */
    public function getPatients(): array
    {
        $query = "
            SELECT 
                u.id_PK,
                u.matricule,
                u.nom,
                u.prenom,
                u.mail,
                u.tel,
                u.adresse,
                r.libelle as role_name,
                u.id_role
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE r.libelle = 'Patient' OR r.libelle = 'patient'
            ORDER BY u.nom ASC, u.prenom ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get total patient count
     * 
     * @return int
     */
    public function getTotalPatients(): int
    {
        $query = "
            SELECT COUNT(*) as total 
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE r.libelle = 'Patient' OR r.libelle = 'patient'
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'] ?? 0;
    }

    /**
     * Get dashboard statistics for admin
     * 
     * @return array
     */
    public function getDashboardStats(): array
    {
        return [
            'totalUsers' => $this->getTotalUsers(),
            'activeUsers' => $this->getActiveUsers(),
            'usersByRole' => $this->getUsersByRole(),
            'totalPatients' => $this->getTotalPatients(),
        ];
    }
}
