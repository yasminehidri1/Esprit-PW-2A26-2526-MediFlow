<?php
/**
 * Comment Model — MediFlow Magazine Module
 * Handles CRUD operations for comments (comments table)
 */

require_once __DIR__ . '/../config.php';

class Comment {
    private $db;
    private $table = 'comments';

    public function __construct() {
        $this->db = \config::getConnexion();
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
     * Get all comments for a post with pagination
     */
    public function getAllByPostWithPagination($postId, int $page = 1, int $perPage = 6) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                WHERE c.id_post = :id_post
                ORDER BY c.date_creation DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_post', $postId);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all comments for a specific post
     */
    public function countAllByPost($postId) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE id_post = :id_post";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_post' => $postId]);
        return $stmt->fetch()['total'];
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
     * Get the N most recent comments (any status) — used by the dashboard Comment Moderation panel
     */
    public function getRecentAll(int $limit = 20) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail, p.titre as post_titre, p.id as id_post_ref
                FROM {$this->table} c
                LEFT JOIN utilisateurs u  ON c.id_utilisateur = u.id_PK
                LEFT JOIN posts p         ON c.id_post = p.id
                ORDER BY c.date_creation DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all comments (any status) — for pagination
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()['total'];
    }

    /**
     * Get paginated recent comments (any status) — for dashboard comment moderation with pagination
     */
    public function getRecentAllPaginated(int $page = 1, int $perPage = 6) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT c.*, u.nom, u.prenom, u.mail, p.titre as post_titre, p.id as id_post_ref
                FROM {$this->table} c
                LEFT JOIN utilisateurs u  ON c.id_utilisateur = u.id_PK
                LEFT JOIN posts p         ON c.id_post = p.id
                ORDER BY c.date_creation DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
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
