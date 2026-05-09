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
     * Full analytics / statistics page for the magazine module
     * GET /integration/magazine/admin/stats
     */
    public function statsPage(): void
    {
        $this->requireMagazineAccess();

        $postStats      = $this->postModel->getStats();
        $commentStats   = $this->commentModel->getStats();
        $totalBookmarks = $this->postModel->getTotalBookmarks();

        $postsOverTime      = $this->postModel->getPostsOverTime(12);
        $engagementOverTime = $this->postModel->getEngagementOverTime(12);
        $commentsOverTime   = $this->commentModel->getCommentsOverTime(12);
        $categoryBreakdown  = $this->postModel->getCategoryBreakdown();
        $topPosts           = $this->postModel->getTopPostsWithStats(10);
        $mostLiked          = $this->postModel->getMostLiked(5);
        $mostViewed         = $this->postModel->getMostViewed(5);

        // Build aligned 12-month arrays (fill missing months with 0)
        $monthKeys   = [];
        $monthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $ts            = strtotime("-{$i} months");
            $monthKeys[]   = date('Y-m', $ts);
            $monthLabels[] = date('M Y', $ts);
        }

        $postsMap    = array_fill_keys($monthKeys, 0);
        $commentsMap = array_fill_keys($monthKeys, 0);
        $viewsMap    = array_fill_keys($monthKeys, 0);
        $likesMap    = array_fill_keys($monthKeys, 0);

        foreach ($postsOverTime      as $r) { $postsMap[$r['month']]    = (int)$r['count']; }
        foreach ($commentsOverTime   as $r) { $commentsMap[$r['month']] = (int)$r['count']; }
        foreach ($engagementOverTime as $r) {
            $viewsMap[$r['month']] = (int)$r['total_views'];
            $likesMap[$r['month']] = (int)$r['total_likes'];
        }

        $chartMonthLabels   = $monthLabels;
        $chartPostsData     = array_values($postsMap);
        $chartCommentsData  = array_values($commentsMap);
        $chartViewsData     = array_values($viewsMap);
        $chartLikesData     = array_values($likesMap);

        $currentView = 'stats_magazine';
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

        // Handle base64 image from the in-browser image editor (takes priority)
        if (!empty($_POST['edited_image_data'])) {
            $editResult = $this->handleBase64Upload($_POST['edited_image_data']);
            if ($editResult['success']) {
                $imageUrl = $editResult['path'];
            } else {
                $_SESSION['flash_error'] = $editResult['error'];
                $id = $_POST['id'] ?? '';
                header('Location: /integration/magazine/admin/article-form' . ($id ? '?id=' . $id : ''));
                exit;
            }
        // Handle regular file upload if provided and no edited image
        } elseif (!empty($_FILES['image_file']['name'])) {
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

        // Check if current user has liked / bookmarked this post
        $userId          = $_SESSION['user']['id'] ?? null;
        $alreadyLiked    = $userId ? $this->postModel->hasLiked((int)$id, (int)$userId) : false;
        $alreadyBookmarked = $userId ? $this->postModel->hasBookmarked((int)$id, (int)$userId) : false;

        // Get approved comments for this post
        $comments     = $this->commentModel->getByPost($id);
        $commentCount = $this->commentModel->countByPost($id);

        // IDs of comments the current user has already liked (for server-side initial state)
        $likedCommentIds = $userId
            ? $this->commentModel->getLikedCommentIds((int)$userId, (int)$id)
            : [];

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
     * Toggle bookmark/unbookmark — AJAX, returns { bookmarked: bool }
     * POST /integration/magazine/bookmark   { post_id: int }
     */
    public function toggleBookmark(): void
    {
        header('Content-Type: application/json');
        $this->requireAuth();

        $postId = (int)($_POST['post_id'] ?? 0);
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Missing post ID.']);
            exit;
        }

        $result = $this->postModel->toggleBookmark($postId, $userId);
        echo json_encode(['success' => true, 'bookmarked' => $result['bookmarked']]);
        exit;
    }

    /**
     * My Bookmarks page — shows all posts bookmarked by the current user
     * GET /integration/magazine/bookmarks
     */
    public function myBookmarks(): void
    {
        $this->requireAuth();
        $userId         = (int)($_SESSION['user']['id'] ?? 0);
        $bookmarkedPosts = $this->postModel->getBookmarkedPosts($userId);

        $GLOBALS['magazineSubView'] = 'bookmarks';
        $currentView = 'bookmarks';
        $this->renderMagazineView(get_defined_vars());
    }

    /**
     * Bookmarks data — AJAX endpoint for nav dropdown
     * GET /integration/magazine/bookmarks/data
     */
    public function bookmarksData(): void
    {
        header('Content-Type: application/json');
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if (!$userId) {
            echo json_encode(['bookmarks' => []]);
            exit;
        }

        $posts = $this->postModel->getBookmarkedPosts($userId);
        $items = array_map(fn($p) => [
            'id'            => (int)$p['id'],
            'titre'         => $p['titre'],
            'categorie'     => $p['categorie'],
            'image_url'     => $p['image_url'] ?? null,
            'bookmarked_at' => $p['bookmarked_at'],
        ], $posts);

        echo json_encode(['bookmarks' => $items]);
        exit;
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
     * AI Text Rephrasing — back office writing assistant
     * POST /integration/magazine/admin/rephrase   { text: string }
     * Returns { success: bool, rephrased: string }
     */
    public function rephrase(): void
    {
        header('Content-Type: application/json');
        $this->requireMagazineAccess();

        $input = json_decode(file_get_contents('php://input'), true);
        $text  = trim($input['text'] ?? '');

        if (empty($text)) {
            echo json_encode(['success' => false, 'error' => 'No text provided.']);
            exit;
        }

        $text    = mb_substr($text, 0, 1500);
        $prompt  = "Rephrase the following text to be more professional, clear, and engaging. "
                 . "Improve grammar and readability. Return ONLY the rephrased text, nothing else — "
                 . "no explanations, no labels, no quotes.\n\nText:\n" . $text;

        $payload = json_encode(['model' => 'tinyllama', 'prompt' => $prompt, 'stream' => false]);

        $ch = curl_init('http://localhost:11434/api/generate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 90,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $raw       = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $raw === false) {
            echo json_encode(['success' => false, 'error' => 'AI service unreachable.']);
            exit;
        }

        $ollamaData = json_decode($raw, true);
        $rephrased  = trim($ollamaData['response'] ?? '');
        $rephrased  = preg_replace('/^(Rephrased|Here is|Output|Result)[\s:\-]+/iu', '', $rephrased);

        echo json_encode(['success' => true, 'rephrased' => $rephrased ?: 'No output generated.']);
        exit;
    }

    /**
     * AI Post Summarization — calls local Ollama/TinyLlama and returns JSON
     * POST /integration/magazine/summarize   { post_id: int }
     * Returns { success: bool, summary: string, keyPoints: string[] }
     */
    public function summarize(): void
    {
        header('Content-Type: application/json');

        $postId = (int)($_POST['post_id'] ?? 0);
        if (!$postId) {
            echo json_encode(['success' => false, 'error' => 'Missing post ID.']);
            exit;
        }

        $post = $this->postModel->getById($postId);
        if (!$post || $post['statut'] !== 'publie') {
            echo json_encode(['success' => false, 'error' => 'Article not found.']);
            exit;
        }

        $content = strip_tags($post['contenu']);
        $content = preg_replace('/\s+/', ' ', trim($content));
        $content = mb_substr($content, 0, 2000);

        $prompt  = "You are a medical content assistant. Summarize the following health article in 2-3 clear sentences, then list exactly 3 key takeaways. "
                 . "Respond ONLY with valid JSON in this exact format (no extra text before or after): "
                 . "{\"summary\":\"...\",\"keyPoints\":[\"...\",\"...\",\"...\"]}\n\nArticle:\n" . $content;

        $payload = json_encode(['model' => 'tinyllama', 'prompt' => $prompt, 'stream' => false]);

        $ch = curl_init('http://localhost:11434/api/generate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 90,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $raw       = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $raw === false) {
            echo json_encode(['success' => false, 'error' => 'AI service unreachable. Make sure Ollama is running.']);
            exit;
        }

        $ollamaData = json_decode($raw, true);
        $text       = trim($ollamaData['response'] ?? '');

        // Extract the first valid JSON object from the response
        $start  = strpos($text, '{');
        $end    = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $parsed = json_decode(substr($text, $start, $end - $start + 1), true);
            if ($parsed && !empty($parsed['summary'])) {
                echo json_encode([
                    'success'   => true,
                    'summary'   => $parsed['summary'],
                    'keyPoints' => array_values(array_filter($parsed['keyPoints'] ?? [])),
                ]);
                exit;
            }
        }

        // Strip common TinyLlama prompt-echo artifacts and return as plain summary
        $text = preg_replace('/^(Article|Summary|Response|Here is|Note)[\s:\-]+/iu', '', $text);
        $text = trim($text);
        echo json_encode(['success' => true, 'summary' => $text ?: 'No summary generated.', 'keyPoints' => []]);
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
        if (!in_array($role, ['Admin', 'redacteur'])) {
            http_response_code(403);
            die('Accès refusé. Cette section est réservée aux éditeurs du magazine.');
        }
    }

    /**
     * Handle base64-encoded image data from the in-browser image editor
     */
    private function handleBase64Upload(string $dataUri): array
    {
        if (!preg_match('#^data:(image/(jpeg|png|webp));base64,(.+)$#i', $dataUri, $m)) {
            return ['success' => false, 'error' => 'Invalid image data.'];
        }

        $mimeType  = strtolower($m[1]);
        $extMap    = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $extension = $extMap[$mimeType] ?? 'jpg';

        $imageData = base64_decode($m[3]);
        if ($imageData === false || strlen($imageData) < 100) {
            return ['success' => false, 'error' => 'Could not decode image data.'];
        }
        if (strlen($imageData) > 8 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Edited image must be less than 8 MB.'];
        }

        $uploadDir = __DIR__ . '/../assets/uploads';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create upload directory.'];
        }

        $filename = uniqid('img_', true) . '.' . $extension;
        if (file_put_contents($uploadDir . '/' . $filename, $imageData) === false) {
            return ['success' => false, 'error' => 'Failed to save edited image.'];
        }

        return ['success' => true, 'path' => '/integration/assets/uploads/' . $filename];
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
