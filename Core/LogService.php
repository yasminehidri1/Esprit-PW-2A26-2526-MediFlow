<?php
namespace Core;

class LogService
{
    /**
     * Create a system audit log.
     * 
     * @param int|null $userId ID of the user performing the action
     * @param string|null $role Role of the user at the time
     * @param string $actionType e.g., 'CREATE', 'UPDATE', 'DELETE', 'LOGIN'
     * @param string $module e.g., 'AUTH', 'DOSSIER', 'INVENTORY'
     * @param string $description Human readable description
     * @param array|null $payload Data snapshot (e.g., ['old' => [...], 'new' => [...]])
     */
    public static function logAction(
        ?int $userId,
        ?string $role,
        string $actionType,
        string $module,
        string $description,
        ?array $payload = null
    ): bool {
        require_once __DIR__ . '/../config.php';
        $db = \config::getConnexion();

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $payloadJson = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null;

        $sql = "INSERT INTO system_logs 
                (user_id, role, action_type, module, description, ip_address, user_agent, payload)
                VALUES 
                (:user_id, :role, :action_type, :module, :description, :ip_address, :user_agent, :payload)";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':user_id'     => $userId,
                ':role'        => $role,
                ':action_type' => strtoupper($actionType),
                ':module'      => strtoupper($module),
                ':description' => $description,
                ':ip_address'  => $ipAddress,
                ':user_agent'  => $userAgent,
                ':payload'     => $payloadJson
            ]);
        } catch (\PDOException $e) {
            error_log("LogService Error: " . $e->getMessage());
            return false;
        }
    }
}
