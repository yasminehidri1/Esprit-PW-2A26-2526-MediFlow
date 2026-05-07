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
        'Admin'        => 'AD',
        'Medecin'      => 'MD',
        'Patient'      => 'PT',
        'pharmacien'   => 'PH',
        'receptionist' => 'RC',
        'Technicien'   => 'TC',
        'redacteur'    => 'RD',
        'Fournisseur'  => 'FR',
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
                u.adresse, u.id_role, u.status, u.profile_pic, r.libelle as role_name
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
     * Search and filter users by term and/or role with pagination
     * 
     * @param string|null $search Search term for name, email, phone, matricule
     * @param int|null $roleId Filter by role ID
     * @param int|null $limit Maximum number of results to return
     * @param int|null $offset Offset for pagination
     * @return array Filtered users
     */
    public function searchAndFilterUsers(?string $search = null, ?int $roleId = null, ?int $limit = null, ?int $offset = null): array
    {
        $query = "
            SELECT 
                u.id_PK, u.matricule, u.nom, u.prenom, u.mail, u.tel, 
                u.adresse, u.id_role, u.status, r.libelle as role_name
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
        
        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $query .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count users matching search and filter criteria for pagination
     * 
     * @param string|null $search Search term
     * @param int|null $roleId Filter by role ID
     * @return int Total number of matching users
     */
    public function countSearchAndFilterUsers(?string $search = null, ?int $roleId = null): int
    {
        $query = "
            SELECT COUNT(*) as total
            FROM utilisateurs u
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
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)($result['total'] ?? 0);
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
            error_log("createUser Error: " . $e->getMessage());
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
            error_log('UserModel::updateUser() called with userId: ' . $userId . ', data: ' . json_encode($data));
            
            $allowedFields = ['nom', 'prenom', 'mail', 'tel', 'adresse', 'id_role'];
            $updateFields = [];
            $params = [':userId' => $userId];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "`$field` = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "`motdp` = :motdp";
                $params[':motdp'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                error_log('No update fields provided');
                return false;
            }
            
            $query = "UPDATE `utilisateurs` SET " . implode(', ', $updateFields) . " WHERE `id_PK` = :userId";
            error_log('Generated SQL query: ' . $query);
            error_log('Query parameters: ' . json_encode($params));
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($params);
            
            error_log('Execute result: ' . ($result ? 'true' : 'false'));
            error_log('Rows affected: ' . $stmt->rowCount());
            
            if (!$result) {
                error_log('PDO Error Info: ' . json_encode($stmt->errorInfo()));
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log('PDOException in updateUser: ' . $e->getMessage());
            error_log('Error Code: ' . $e->getCode());
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

    /**
     * Update user status (active/suspended)
     * 
     * @param int $userId
     * @param string $status
     * @return bool True on success, false on failure
     */
    public function updateUserStatus(int $userId, string $status): bool
    {
        try {
            $query = "UPDATE utilisateurs SET status = :status WHERE id_PK = :userId";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                'status' => $status,
                'userId' => $userId
            ]);
        } catch (\PDOException $e) {
            error_log('Error updating status: ' . $e->getMessage());
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
        
        $maxNumber = $this->getMaxMatriculeNumber($roleId, $prefix);
        $nextNumber = $maxNumber + 1;
        
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
     * Get the highest matricule number currently in use for a role
     * 
     * @param int $roleId
     * @param string $prefix
     * @return int Highest number used
     */
    private function getMaxMatriculeNumber(int $roleId, string $prefix): int
    {
        $query = "SELECT matricule FROM utilisateurs WHERE id_role = :id_role AND matricule LIKE :prefix ORDER BY LENGTH(matricule) DESC, matricule DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_role' => $roleId, 
            ':prefix' => $prefix . '%'
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['matricule'])) {
            $numStr = substr($result['matricule'], strlen($prefix));
            if (is_numeric($numStr)) {
                return (int)$numStr;
            }
        }
        
        return self::DEFAULT_MATRICULE_START - 1;
    }
}

