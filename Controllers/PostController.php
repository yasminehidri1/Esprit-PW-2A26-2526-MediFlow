<?php
/**
 * Post Controller — MediFlow Magazine Module
 * Handles article-related actions for both back office and front office.
 *
 * @package MediFlow\Controllers
 * @version 2.0.0
 */

namespace Controllers;

use Core\SessionHelper;

class PostController
{
    use SessionHelper;

    private $postModel;
    private $commentModel;

    public function __construct()
    {
        $this->ensureSession();
        require_once __DIR__ . '/../Models/Post.php';
        require_once __DIR__ . '/../Models/Comment.php';
        $this->postModel    = new \Post();
        $this->commentModel = new \Comment();
    }

    // =========================================================
    // BACK OFFICE ACTIONS
    // =========================================================

    /**
     * Dashboard — show stats and recent articles (with pagination)
     */
    public function dashboard(): void
    {
        $this->requireMagazineAccess();

        $postStats    = $this->postModel->getStats();
        $commentStats = $this->commentModel->getStats();

        // --- Recent Publications pagination ---
        $postsPerPage    = 5;
        $postsPage       = max(1, (int)($_GET['posts_page'] ?? 1));
        $totalPostsCount = $this->postModel->countRecent();
        $totalPostsPages = max(1, (int)ceil($totalPostsCount / $postsPerPage));
        $postsPage       = min($postsPage, $totalPostsPages);
        $recentPosts     = $this->postModel->getRecentPaginated($postsPage, $postsPerPage);

        // --- Comment Moderation pagination ---
        $commentsPerPage    = 6;
        $commentsPage       = max(1, (int)($_GET['comments_page'] ?? 1));
        $totalCommentsCount = $this->commentModel->countAll();
        $totalCommentsPages = max(1, (int)ceil($totalCommentsCount / $commentsPerPage));
        $commentsPage       = min($commentsPage, $totalCommentsPages);
        $allComments        = $this->commentModel->getRecentAllPaginated($commentsPage, $commentsPerPage);

        $currentView = 'dashboard_magazine';
        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * List all articles (admin view with all statuses)
     */
    public function listArticles(): void
    {
        $this->requireMagazineAccess();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $filters = [];

        if (!empty($_GET['search']))   $filters['search']   = $_GET['search'];
        if (!empty($_GET['categorie'])) $filters['categorie'] = $_GET['categorie'];
        if (!empty($_GET['statut']))   $filters['statut']   = $_GET['statut'];

        $result      = $this->postModel->getAll($filters, $page, 10);
        $categories  = $this->postModel->getCategories();
        $currentView = 'articles';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * Show the create/edit form
     */
    public function showForm(): void
    {
        $this->requireMagazineAccess();

        $post = null;
        if (!empty($_GET['id'])) {
            $post = $this->postModel->getById($_GET['id']);
        }
        $currentView = 'article_form';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * Process create/edit form submission
     */
    public function saveArticle(): void
    {
        $this->requireMagazineAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/magazine/admin/articles');
            exit;
        }

        // Validate required fields
        $errors = [];
        if (empty(trim($_POST['titre'] ?? ''))) {
            $errors[] = 'Title is required.';
        }
        if (empty(trim($_POST['contenu'] ?? ''))) {
            $errors[] = 'Content is required.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            $id = $_POST['id'] ?? '';
            header('Location: /integration/magazine/admin/article-form' . ($id ? '?id=' . $id : ''));
            exit;
        }

        $imageUrl = !empty($_POST['image_url']) ? trim($_POST['image_url']) : null;

        // Handle file upload if provided
        if (!empty($_FILES['image_file']['name'])) {
            $uploadResult = $this->handleImageUpload($_FILES['image_file']);
            if ($uploadResult['success']) {
                $imageUrl = $uploadResult['path'];
            } else {
                $_SESSION['flash_error'] = $uploadResult['error'];
                $id = $_POST['id'] ?? '';
                header('Location: /integration/magazine/admin/article-form' . ($id ? '?id=' . $id : ''));
                exit;
            }
        }

        // Use authenticated user's ID as author
        $auteurId = $_SESSION['user']['id'] ?? 1;

        $data = [
            'titre'     => htmlspecialchars(trim($_POST['titre']), ENT_QUOTES, 'UTF-8'),
            'contenu'   => trim($_POST['contenu']),
            'categorie' => $_POST['categorie'] ?? 'General Health',
            'image_url' => $imageUrl,
            'auteur_id' => $auteurId,
            'statut'    => $_POST['statut'] ?? 'brouillon',
        ];

        if (!empty($_POST['id'])) {
            $this->postModel->update($_POST['id'], $data);
            $_SESSION['flash_success'] = 'Article updated successfully!';
        } else {
            $this->postModel->create($data);
            $_SESSION['flash_success'] = 'Article created successfully!';
        }

        header('Location: /integration/magazine/admin/articles');
        exit;
    }

    /**
     * Delete an article
     */
    public function deleteArticle(): void
    {
        $this->requireMagazineAccess();

        if (!empty($_GET['id'])) {
            $this->postModel->delete($_GET['id']);
            $_SESSION['flash_success'] = 'Article deleted successfully!';
        }
        header('Location: /integration/magazine/admin/articles');
        exit;
    }

    // =========================================================
    // FRONT OFFICE ACTIONS
    // =========================================================

    /**
     * Front office home page — featured articles
     */
    public function home(): void
    {
        $featuredPost    = $this->postModel->getMostPopular();
        $recentPosts     = $this->postModel->getRecent(6);
        $categories      = $this->postModel->getCategories();
        $popularPosts    = $this->postModel->getMostLiked(5);
        $mostViewedPosts = $this->postModel->getMostViewed(5);

        $userId = $_SESSION['user']['id'] ?? null;

        if ($featuredPost) {
            $featuredPost['comment_count'] = $this->commentModel->countByPost($featuredPost['id']);
            $featuredPost['user_has_liked'] = $userId ? $this->postModel->hasLiked($featuredPost['id'], $userId) : false;
        }

        foreach ($recentPosts as &$post) {
            $post['comment_count'] = $this->commentModel->countByPost($post['id']);
            $post['user_has_liked'] = $userId ? $this->postModel->hasLiked($post['id'], $userId) : false;
        }

        $GLOBALS['magazineSubView'] = 'home';
        $currentView = 'home';
        $this->renderMagazineView(get_defined_vars());
    }

    /**
     * View a single article (front office)
     */
    public function viewArticle(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /integration/magazine');
            exit;
        }

        $post = $this->postModel->getById($id);
        if (!$post || $post['statut'] !== 'publie') {
            header('Location: /integration/magazine');
            exit;
        }

        // Increment view count
        $this->postModel->incrementViews($id);
        $post['views_count']++;

        // Check if current user has liked this post
        $userId      = $_SESSION['user']['id'] ?? null;
        $alreadyLiked = $userId ? $this->postModel->hasLiked((int)$id, (int)$userId) : false;

        // Get approved comments for this post
        $comments     = $this->commentModel->getByPost($id);
        $commentCount = $this->commentModel->countByPost($id);

        // Related posts (same category)
        $relatedResult = $this->postModel->getAll(
            ['categorie' => $post['categorie'], 'statut' => 'publie'],
            1,
            4
        );
        $relatedPosts = array_filter($relatedResult['data'], fn($p) => $p['id'] != $id);
        $relatedPosts = array_slice($relatedPosts, 0, 3);

        foreach ($relatedPosts as &$rp) {
            $rp['comment_count'] = $this->commentModel->countByPost($rp['id']);
        }

        $GLOBALS['magazineSubView'] = 'article';
        $currentView = 'article';
        $this->renderMagazineView(get_defined_vars());
    }

    /**
     * Browse articles by category
     */
    public function category(): void
    {
        $categorie   = $_GET['cat'] ?? 'General Health';
        $page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $result      = $this->postModel->getByCategory($categorie, $page, 9);
        $categories  = $this->postModel->getCategories();
        $GLOBALS['magazineSubView'] = 'category';
        $currentView = 'category';

        $this->renderMagazineView(get_defined_vars());
    }

    /**
     * Like / Unlike an article (AJAX endpoint — DB-backed, toggle)
     */
    public function likeArticle(): void
    {
        header('Content-Type: application/json');
        $id     = (int)($_GET['id'] ?? 0);
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing article ID']);
            exit;
        }

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'You must be logged in to like articles.']);
            exit;
        }

        $result = $this->postModel->toggleLike($id, $userId);
        echo json_encode([
            'success' => true,
            'liked'   => $result['liked'],
            'likes'   => $result['likes'],
        ]);
        exit;
    }

    /**
     * Search articles (AJAX endpoint)
     */
    public function searchArticles(): void
    {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }

        $results = $this->postModel->searchByTitle($query);
        echo json_encode(['results' => $results]);
        exit;
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Render a magazine front-office view.
     * — If the visitor is a logged-in Patient, wrap it in Back/layout.php so
     *   the unified sidebar stays visible (magazine_patient.php is the content partial).
     * — Otherwise use the standalone Front/layout.php (full editorial page).
     */
    private function renderMagazineView(array $data = []): void
    {
        extract($data); // Make all passed variables available to the included views

        $role = $_SESSION['user']['role'] ?? '';
        if ($role === 'Patient') {
            // The calling method already set $GLOBALS['magazineSubView'] ('home'|'article'|'category').
            // Back/layout.php will include magazine_patient.php which reads that global.
            $currentView = '../Front/magazine_patient';
            include __DIR__ . '/../Views/Back/layout.php';
        } else {
            // Public / Admin / Magazine: use standalone editorial layout
            include __DIR__ . '/../Views/Front/layout.php';
        }
    }

    /**
     * Require that the user has Magazine or Admin role for back-office actions
     */
    private function requireMagazineAccess(): void
    {
        $this->requireAuth();
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, ['Admin', 'Magazine'])) {
            http_response_code(403);
            die('Accès refusé. Cette section est réservée aux éditeurs du magazine.');
        }
    }

    /**
     * Handle image file uploads with validation
     */
    private function handleImageUpload(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload failed. Please try again.'];
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo        = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType     = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return ['success' => false, 'error' => 'Only JPG, PNG, and WebP images are allowed.'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Image size must be less than 5MB.'];
        }

        $uploadDir = __DIR__ . '/../assets/uploads';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create upload directory.'];
        }

        // Use a safe extension from the MIME type, not from user-supplied filename
        $extMap    = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $extension = $extMap[$mimeType] ?? 'jpg';
        $filename  = uniqid('img_', true) . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
            return ['success' => false, 'error' => 'Failed to save the uploaded image.'];
        }

        // Store as a root-relative URL so it renders correctly from any page
        return ['success' => true, 'path' => '/integration/assets/uploads/' . $filename];
    }
}
