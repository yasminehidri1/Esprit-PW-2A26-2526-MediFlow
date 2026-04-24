<?php
/**
 * Post Model — MediFlow Magazine Module
 * Handles CRUD operations for magazine articles (posts table)
 */

require_once __DIR__ . '/../config.php';

class Post {
    private $db;
    private $table = 'posts';

    public function __construct() {
        $this->db = \config::getConnexion();
    }

    /**
     * Get all posts with optional filters and pagination
     */
    public function getAll($filters = [], $page = 1, $perPage = 10) {
        $where = [];
        $params = [];

        if (!empty($filters['categorie'])) {
            $where[] = "p.categorie = :categorie";
            $params[':categorie'] = $filters['categorie'];
        }

        if (!empty($filters['statut'])) {
            $where[] = "p.statut = :statut";
            $params[':statut'] = $filters['statut'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.titre LIKE :search OR p.contenu LIKE :search2)";
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['auteur_id'])) {
            $where[] = "p.auteur_id = :auteur_id";
            $params[':auteur_id'] = $filters['auteur_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Fetch posts with author info
        $sql = "SELECT p.*, u.nom, u.prenom, u.mail, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                {$whereClause}
                ORDER BY p.date_creation DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get a single post by ID with author details
     */
    public function getById($id) {
        $sql = "SELECT p.*, u.nom, u.prenom, u.mail, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new post
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (titre, contenu, categorie, image_url, auteur_id, statut, date_publication)
                VALUES (:titre, :contenu, :categorie, :image_url, :auteur_id, :statut, :date_publication)";

        $stmt = $this->db->prepare($sql);
        $datePublication = ($data['statut'] === 'publie') ? date('Y-m-d H:i:s') : null;

        $stmt->execute([
            ':titre'            => $data['titre'],
            ':contenu'          => $data['contenu'],
            ':categorie'        => $data['categorie'] ?? 'General Health',
            ':image_url'        => $data['image_url'] ?? null,
            ':auteur_id'        => $data['auteur_id'],
            ':statut'           => $data['statut'] ?? 'brouillon',
            ':date_publication' => $datePublication
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update an existing post
     */
    public function update($id, $data) {
        // If status changed to 'publie' and no publication date exists, set it
        $existing = $this->getById($id);
        $datePublication = $existing['date_publication'];
        if (($data['statut'] ?? '') === 'publie' && empty($datePublication)) {
            $datePublication = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE {$this->table} 
                SET titre = :titre, contenu = :contenu, categorie = :categorie, 
                    image_url = :image_url, statut = :statut, date_publication = :date_publication
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre'            => $data['titre'],
            ':contenu'          => $data['contenu'],
            ':categorie'        => $data['categorie'] ?? 'General Health',
            ':image_url'        => $data['image_url'] ?? null,
            ':statut'           => $data['statut'] ?? 'brouillon',
            ':date_publication' => $datePublication,
            ':id'               => $id
        ]);
    }

    /**
     * Delete a post
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle like/unlike for a user — inserts into post_likes and syncs likes_count
     * Returns ['liked'=>bool, 'likes'=>int]
     */
    public function toggleLike(int $postId, int $userId): array {
        // Check if already liked
        $check = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id");
        $check->execute([':post_id' => $postId, ':user_id' => $userId]);
        $existing = $check->fetch();

        if ($existing) {
            // Unlike
            $this->db->prepare("DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id")
                     ->execute([':post_id' => $postId, ':user_id' => $userId]);
            $this->db->prepare("UPDATE {$this->table} SET likes_count = GREATEST(0, likes_count - 1) WHERE id = :id")
                     ->execute([':id' => $postId]);
            $liked = false;
        } else {
            // Like
            try {
                $this->db->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)")
                         ->execute([':post_id' => $postId, ':user_id' => $userId]);
                $this->db->prepare("UPDATE {$this->table} SET likes_count = likes_count + 1 WHERE id = :id")
                         ->execute([':id' => $postId]);
            } catch (\PDOException $e) {
                // Duplicate key — already liked, just return current state
            }
            $liked = true;
        }

        // Get fresh count
        $count = $this->db->prepare("SELECT likes_count FROM {$this->table} WHERE id = :id");
        $count->execute([':id' => $postId]);
        $likes = (int)($count->fetch()['likes_count'] ?? 0);

        return ['liked' => $liked, 'likes' => $likes];
    }

    /**
     * Check if a user has already liked a post
     */
    public function hasLiked(int $postId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute([':post_id' => $postId, ':user_id' => $userId]);
        return (bool)$stmt->fetch();
    }

    /**
     * Increment views count
     */
    public function incrementViews($id) {
        $sql = "UPDATE {$this->table} SET views_count = views_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats() {
        $stats = [];

        // Total articles
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $stats['total_articles'] = $stmt->fetch()['total'];

        // Published articles
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'publie'");
        $stats['published'] = $stmt->fetch()['total'];

        // Draft articles
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'brouillon'");
        $stats['drafts'] = $stmt->fetch()['total'];

        // Total views
        $stmt = $this->db->query("SELECT COALESCE(SUM(views_count), 0) as total FROM {$this->table}");
        $stats['total_views'] = $stmt->fetch()['total'];

        // Total likes
        $stmt = $this->db->query("SELECT COALESCE(SUM(likes_count), 0) as total FROM {$this->table}");
        $stats['total_likes'] = $stmt->fetch()['total'];

        // Category breakdown
        $stmt = $this->db->query("SELECT categorie, COUNT(*) as count FROM {$this->table} GROUP BY categorie ORDER BY count DESC");
        $stats['categories'] = $stmt->fetchAll();

        return $stats;
    }

    /**
     * Get recent published posts
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT p.*, u.nom, u.prenom, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.statut = 'publie'
                ORDER BY p.date_publication DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all published posts — for dashboard pagination
     */
    public function countRecent() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'publie'");
        return $stmt->fetch()['total'];
    }

    /**
     * Get paginated recent published posts — for dashboard with pagination
     */
    public function getRecentPaginated(int $page = 1, int $perPage = 5) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, u.nom, u.prenom, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.statut = 'publie'
                ORDER BY p.date_publication DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get the most popular post (by views)
     */
    public function getMostPopular() {
        $sql = "SELECT p.*, u.nom, u.prenom, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.statut = 'publie'
                ORDER BY p.views_count DESC
                LIMIT 1";

        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    /**
     * Search posts by title
     */
    public function searchByTitle($query) {
        $sql = "SELECT p.*, u.nom, u.prenom
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                WHERE p.titre LIKE :query AND p.statut = 'publie'
                ORDER BY p.date_publication DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Get all unique categories
     */
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT categorie FROM {$this->table} ORDER BY categorie ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get posts ordered by likes (most popular)
     */
    public function getMostLiked(int $limit = 5): array {
        $sql = "SELECT p.*, u.nom, u.prenom, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.statut = 'publie'
                ORDER BY p.likes_count DESC, p.date_publication DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get posts ordered by views (most viewed)
     */
    public function getMostViewed(int $limit = 5): array {
        $sql = "SELECT p.*, u.nom, u.prenom, r.libelle as role_name
                FROM {$this->table} p
                LEFT JOIN utilisateurs u ON p.auteur_id = u.id_PK
                LEFT JOIN roles r ON u.id_role = r.id_role
                WHERE p.statut = 'publie'
                ORDER BY p.views_count DESC, p.date_publication DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get posts by category (published only)
     */
    public function getByCategory($categorie, $page = 1, $perPage = 10) {
        return $this->getAll(['categorie' => $categorie, 'statut' => 'publie'], $page, $perPage);
    }
}
