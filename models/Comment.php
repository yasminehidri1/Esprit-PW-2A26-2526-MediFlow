<?php
/**
 * Comment Model — MediFlow Magazine Module
 * Handles CRUD operations for comments (comments table)
 */

require_once __DIR__ . '/../config/database.php';

class Comment {
    private $db;
    private $table = 'comments';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get approved comments for a specific post
     */
    public function getByPost($postId) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                WHERE c.id_post = :id_post AND c.statut = 'approuve'
                ORDER BY c.date_creation DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_post' => $postId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all comments for a post (any status — admin use)
     */
    public function getAllByPost($postId) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                WHERE c.id_post = :id_post
                ORDER BY c.date_creation DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_post' => $postId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all pending comments (for moderation queue)
     */
    public function getPending() {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail, p.titre as post_titre
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                LEFT JOIN posts p ON c.id_post = p.id
                WHERE c.statut = 'en_attente'
                ORDER BY c.date_creation ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get a single comment by ID
     */
    public function getById($id) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail, p.titre as post_titre
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                LEFT JOIN posts p ON c.id_post = p.id
                WHERE c.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new comment
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (id_post, id_utilisateur, contenu, statut)
                VALUES (:id_post, :id_utilisateur, :contenu, :statut)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_post'        => $data['id_post'],
            ':id_utilisateur' => $data['id_utilisateur'],
            ':contenu'        => $data['contenu'],
            ':statut'         => $data['statut'] ?? 'en_attente'
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update a comment's content
     */
    public function update($id, $contenu) {
        $sql = "UPDATE {$this->table} SET contenu = :contenu WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':contenu' => $contenu, ':id' => $id]);
    }

    /**
     * Approve a comment
     */
    public function approve($id) {
        $sql = "UPDATE {$this->table} SET statut = 'approuve' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Reject a comment
     */
    public function reject($id) {
        $sql = "UPDATE {$this->table} SET statut = 'rejete' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Delete a comment
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get comments by a specific user
     */
    public function getByUser($userId) {
        $sql = "SELECT c.*, p.titre as post_titre
                FROM {$this->table} c
                LEFT JOIN posts p ON c.id_post = p.id
                WHERE c.id_utilisateur = :id_utilisateur
                ORDER BY c.date_creation DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_utilisateur' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get comment statistics for dashboard
     */
    public function getStats() {
        $stats = [];

        // Total comments
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $stats['total'] = $stmt->fetch()['total'];

        // Pending comments
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'en_attente'");
        $stats['pending'] = $stmt->fetch()['total'];

        // Approved comments
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'approuve'");
        $stats['approved'] = $stmt->fetch()['total'];

        // Rejected / deleted
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'rejete'");
        $stats['rejected'] = $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Count comments for a specific post
     */
    public function countByPost($postId) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE id_post = :id_post AND statut = 'approuve'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_post' => $postId]);
        return $stmt->fetch()['total'];
    }
}
