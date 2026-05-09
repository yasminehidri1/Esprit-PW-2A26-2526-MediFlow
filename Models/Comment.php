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
     * Get approved root comments (parent_id IS NULL) for a post, with nested replies pre-attached.
     * Returns a flat list of root comments; each has a 'replies' key with nested arrays (max 3 levels).
     */
    public function getByPost($postId) {
        // Fetch all approved comments for the post in one query
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                WHERE c.id_post = :id_post AND c.statut = 'approuve'
                ORDER BY c.date_creation ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_post' => $postId]);
        $all = $stmt->fetchAll();

        // Build id-indexed map and attach replies
        $map  = [];
        $roots = [];
        foreach ($all as &$row) { $row['replies'] = []; $map[$row['id']] = &$row; }
        unset($row);

        foreach ($all as &$row) {
            if ($row['parent_id'] && isset($map[$row['parent_id']])) {
                $map[$row['parent_id']]['replies'][] = &$row;
            } else {
                $roots[] = &$row;
            }
        }
        unset($row);

        // Return root comments in reverse-chronological order
        return array_reverse($roots);
    }

    /**
     * Get direct replies for a comment (flat list)
     */
    public function getReplies(int $parentId): array {
        $sql = "SELECT c.*, u.nom, u.prenom, u.mail
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id_PK
                WHERE c.parent_id = :parent_id AND c.statut = 'approuve'
                ORDER BY c.date_creation ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':parent_id' => $parentId]);
        return $stmt->fetchAll();
    }

    /**
     * Toggle like/unlike on a comment. Returns ['liked'=>bool, 'likes'=>int]
     */
    public function toggleLike(int $commentId, int $userId): array {
        $check = $this->db->prepare("SELECT id FROM comment_likes WHERE comment_id = :c AND user_id = :u");
        $check->execute([':c' => $commentId, ':u' => $userId]);

        if ($check->fetch()) {
            $this->db->prepare("DELETE FROM comment_likes WHERE comment_id = :c AND user_id = :u")
                     ->execute([':c' => $commentId, ':u' => $userId]);
            $this->db->prepare("UPDATE {$this->table} SET likes_count = GREATEST(0, likes_count-1) WHERE id = :id")
                     ->execute([':id' => $commentId]);
            $liked = false;
        } else {
            try {
                $this->db->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (:c, :u)")
                         ->execute([':c' => $commentId, ':u' => $userId]);
                $this->db->prepare("UPDATE {$this->table} SET likes_count = likes_count+1 WHERE id = :id")
                         ->execute([':id' => $commentId]);
            } catch (\PDOException $e) { /* duplicate */ }
            $liked = true;
        }

        $row   = $this->db->prepare("SELECT likes_count FROM {$this->table} WHERE id = :id");
        $row->execute([':id' => $commentId]);
        $likes = (int)($row->fetch()['likes_count'] ?? 0);
        return ['liked' => $liked, 'likes' => $likes];
    }

    /**
     * Check if a user has liked a comment
     */
    public function hasLikedComment(int $commentId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT id FROM comment_likes WHERE comment_id = :c AND user_id = :u");
        $stmt->execute([':c' => $commentId, ':u' => $userId]);
        return (bool)$stmt->fetch();
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
     * Create a new comment (supports parent_id for replies)
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (id_post, id_utilisateur, contenu, statut, parent_id)
                VALUES (:id_post, :id_utilisateur, :contenu, :statut, :parent_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_post'        => $data['id_post'],
            ':id_utilisateur' => $data['id_utilisateur'],
            ':contenu'        => $data['contenu'],
            ':statut'         => $data['statut'] ?? 'en_attente',
            ':parent_id'      => $data['parent_id'] ?? null,
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
     * Return the comment IDs (for a given post) that a user has already liked.
     * Used to render the initial liked state server-side on page load.
     */
    public function getLikedCommentIds(int $userId, int $postId): array {
        $sql = "SELECT cl.comment_id
                FROM comment_likes cl
                JOIN comments c ON cl.comment_id = c.id
                WHERE cl.user_id = :user_id AND c.id_post = :post_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':post_id' => $postId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function getCommentsOverTime(int $months = 12): array {
        $sql = "SELECT DATE_FORMAT(date_creation, '%Y-%m') as month, COUNT(*) as count
                FROM {$this->table}
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                GROUP BY month ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
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
