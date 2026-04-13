<?php
/**
 * User Model
 * 
 * Handles all database operations for user management (CRUD)
 * 
 * @package MediFlow\Models
 * @version 1.0.0
 */

namespace Models;

use PDO;

class UserModel
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
     * Get all users with their roles
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
                u.id_role,
                r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            ORDER BY u.nom ASC, u.prenom ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID
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
                u.id_role,
                r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE u.id_PK = :userId
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return int|null User ID if successful, null otherwise
     */
    public function createUser(array $data): ?int
    {
        try {
            $query = "
                INSERT INTO utilisateurs (nom, prenom, mail, tel, adresse, id_role, motdp)
                VALUES (:nom, :prenom, :mail, :tel, :adresse, :id_role, :motdp)
            ";
            
            $stmt = $this->db->prepare($query);
            
            $result = $stmt->execute([
                ':nom' => $data['nom'] ?? null,
                ':prenom' => $data['prenom'] ?? null,
                ':mail' => $data['mail'] ?? null,
                ':tel' => $data['tel'] ?? null,
                ':adresse' => $data['adresse'] ?? null,
                ':id_role' => $data['id_role'] ?? null,
                ':motdp' => password_hash($data['password'] ?? 'test123', PASSWORD_DEFAULT)
            ]);
            
            return $result ? (int)$this->db->lastInsertId() : null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Update user
     * 
     * @param int $userId
     * @param array $data User data to update
     * @return bool
     */
    public function updateUser(int $userId, array $data): bool
    {
        try {
            $allowedFields = ['nom', 'prenom', 'mail', 'tel', 'adresse', 'id_role'];
            $updateFields = [];
            $params = [':userId' => $userId];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return false;
            }
            
            // Handle password update separately if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "motdp = :motdp";
                $params[':motdp'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $query = "UPDATE utilisateurs SET " . implode(', ', $updateFields) . " WHERE id_PK = :userId";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Delete user
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        try {
            $query = "DELETE FROM utilisateurs WHERE id_PK = :userId";
            $stmt = $this->db->prepare($query);
            return $stmt->execute(['userId' => $userId]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get all roles
     * 
     * @return array
     */
    public function getAllRoles(): array
    {
        $query = "SELECT id_role, libelle FROM roles ORDER BY libelle ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search and filter users
     * 
     * @param string|null $search Search term (name, email, phone)
     * @param int|null $roleId Filter by role ID
     * @return array
     */
    public function searchAndFilterUsers(?string $search = null, ?int $roleId = null): array
    {
        $query = "
            SELECT 
                u.id_PK,
                u.nom,
                u.prenom,
                u.mail,
                u.tel,
                u.adresse,
                u.id_role,
                r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE 1=1
        ";
        
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $query .= " AND (
                u.prenom LIKE :search OR 
                u.nom LIKE :search OR 
                u.mail LIKE :search OR 
                u.tel LIKE :search
            )";
            $params['search'] = $searchTerm;
        }
        
        // Add role filter
        if ($roleId !== null && $roleId > 0) {
            $query .= " AND u.id_role = :roleId";
            $params['roleId'] = $roleId;
        }
        
        $query .= " ORDER BY u.nom ASC, u.prenom ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if email exists
     * 
     * @param string $email
     * @param int|null $excludeUserId Exclude user ID from check (for updates)
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM utilisateurs WHERE mail = :email";
        $params = ['email' => $email];
        
        if ($excludeUserId !== null) {
            $query .= " AND id_PK != :userId";
            $params['userId'] = $excludeUserId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
