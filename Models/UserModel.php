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

    // Role to matricule prefix mapping
    private const ROLE_PREFIXES = [
        'Admin' => 'AD',
        'Medecin' => 'MD',
        'Patient' => 'PT',
        'Pharmacien' => 'PH',
        'Rendez-vous' => 'RDV',
        'Equipment' => 'EQ',
        'Magazine' => 'MG'
    ];

    private const DEFAULT_MATRICULE_START = 100;
    private const UNKNOWN_ROLE_PREFIX = 'UK';

    public function __construct()
    {
        $this->db = $this->getConnection();
    }

    /**
     * Get database connection (Singleton)
     * 
     * @return PDO
     */
    private function getConnection(): PDO
    {
        require_once __DIR__ . '/../config.php';
        return \config::getConnexion();
    }

    // ===== READ OPERATIONS =====

    /**
     * Get all users with their role information
     * 
     * @return array Array of users
     */
    public function getAllUsers(): array
    {
        $query = "
            SELECT 
                u.id_PK, u.matricule, u.nom, u.prenom, u.mail, u.tel, 
                u.adresse, u.id_role, r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            ORDER BY u.nom ASC, u.prenom ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID with role information
     * 
     * @param int $userId
     * @return array|null User data or null if not found
     */
    public function getUserById(int $userId): ?array
    {
        $query = "
            SELECT 
                u.id_PK, u.matricule, u.nom, u.prenom, u.mail, u.tel, 
                u.adresse, u.id_role, r.libelle as role_name
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
     * Get all available roles
     * 
     * @return array Array of roles with id and name
     */
    public function getAllRoles(): array
    {
        $query = "SELECT id_role, libelle FROM roles ORDER BY libelle ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search and filter users by term and/or role
     * 
     * @param string|null $search Search term for name, email, phone, matricule
     * @param int|null $roleId Filter by role ID
     * @return array Filtered users
     */
    public function searchAndFilterUsers(?string $search = null, ?int $roleId = null): array
    {
        $query = "
            SELECT 
                u.id_PK, u.matricule, u.nom, u.prenom, u.mail, u.tel, 
                u.adresse, u.id_role, r.libelle as role_name
            FROM utilisateurs u
            LEFT JOIN roles r ON u.id_role = r.id_role
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $query .= " AND (
                u.matricule LIKE :search OR
                u.prenom LIKE :search OR 
                u.nom LIKE :search OR 
                u.mail LIKE :search OR 
                u.tel LIKE :search
            )";
            $params['search'] = $searchTerm;
        }
        
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
     * Check if email already exists in database
     * 
     * @param string $email
     * @param int|null $excludeUserId Exclude user ID from check (for updates)
     * @return bool True if email exists, false otherwise
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

    // ===== CREATE OPERATION =====

    /**
     * Create a new user with auto-generated matricule
     * 
     * @param array $data User data array with keys: nom, prenom, mail, tel, adresse, id_role, password
     * @return int|null User ID if successful, null on failure
     */
    public function createUser(array $data): ?int
    {
        try {
            $matricule = $this->generateMatricule($data['id_role']);
            
            $query = "
                INSERT INTO utilisateurs 
                (matricule, nom, prenom, mail, tel, adresse, id_role, motdp)
                VALUES 
                (:matricule, :nom, :prenom, :mail, :tel, :adresse, :id_role, :motdp)
            ";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':matricule' => $matricule,
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

    // ===== UPDATE OPERATION =====

    /**
     * Update user data
     * 
     * @param int $userId
     * @param array $data Data to update (only updated fields needed)
     * @return bool True on success, false on failure
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
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "motdp = :motdp";
                $params[':motdp'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                return false;
            }
            
            $query = "UPDATE utilisateurs SET " . implode(', ', $updateFields) . " WHERE id_PK = :userId";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ===== DELETE OPERATION =====

    /**
     * Delete user by ID
     * 
     * @param int $userId
     * @return bool True on success, false on failure
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

    // ===== HELPER METHODS =====

    /**
     * Generate unique matricule based on role
     * Format: 2-letter prefix + 3-digit number (e.g., PT100, PT101, MD100)
     * 
     * @param int $roleId
     * @return string Generated matricule
     */
    public function generateMatricule(int $roleId): string
    {
        $roleName = $this->getRoleName($roleId);
        $prefix = self::ROLE_PREFIXES[$roleName] ?? self::UNKNOWN_ROLE_PREFIX;
        $nextNumber = self::DEFAULT_MATRICULE_START + $this->countUsersWithRole($roleId);
        
        return $prefix . $nextNumber;
    }

    /**
     * Get role name from role ID
     * 
     * @param int $roleId
     * @return string|null Role name or null if not found
     */
    private function getRoleName(int $roleId): ?string
    {
        $query = "SELECT libelle FROM roles WHERE id_role = :id_role LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_role' => $roleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['libelle'] ?? null;
    }

    /**
     * Count users assigned to a specific role
     * 
     * @param int $roleId
     * @return int Number of users with this role
     */
    private function countUsersWithRole(int $roleId): int
    {
        $query = "SELECT COUNT(*) as count FROM utilisateurs WHERE id_role = :id_role";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_role' => $roleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
}

