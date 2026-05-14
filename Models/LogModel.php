<?php
namespace Models;

use PDO;

class LogModel
{
    private PDO $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config.php';
        $this->db = \config::getConnexion();
    }

    public function getLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $query = "
            SELECT l.*, u.nom, u.prenom, u.mail 
            FROM system_logs l
            LEFT JOIN utilisateurs u ON l.user_id = u.id_PK
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['action_type'])) {
            $query .= " AND l.action_type = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }

        if (!empty($filters['module'])) {
            $query .= " AND l.module = :module";
            $params[':module'] = $filters['module'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(l.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(l.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $query .= " ORDER BY l.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
