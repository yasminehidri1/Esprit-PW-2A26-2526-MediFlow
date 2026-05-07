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
use Core\NotificationHelper;
use Core\MailHelper;
use Models\Notification;
use Models\UserModel;

class PostController
{
    use SessionHelper;

    private $postModel;
    private $commentModel;
    private $userModel;

    public function __construct()
    {
        $this->ensureSession();
        require_once __DIR__ . '/../Models/Post.php';
        require_once __DIR__ . '/../Models/Comment.php';
        $this->postModel    = new \Post();
        $this->commentModel = new \Comment();
        $this->userModel    = new UserModel();
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

        // Charts (default: last 30 days)
        $chartDays          = 30;
        $publishedPerDay    = $this->postModel->getPublishedCountsByDay($chartDays);
        $commentsPerDay     = $this->commentModel->getCountsByDay($chartDays);
        $topViewedPublished = $this->postModel->getTopPublishedBy('views', 5);
        $topLikedPublished  = $this->postModel->getTopPublishedBy('likes', 5);

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

        // Handle edited image data URL from image editor
        if (!empty($_POST['originalImageData']) && strpos($_POST['originalImageData'], 'data:image') === 0) {
            $uploadResult = $this->handleDataUrlImage($_POST['originalImageData']);
            if ($uploadResult['success']) {
                $imageUrl = $uploadResult['path'];
            } else {
                $_SESSION['flash_error'] = $uploadResult['error'];
                $id = $_POST['id'] ?? '';
                header('Location: /integration/magazine/admin/article-form' . ($id ? '?id=' . $id : ''));
                exit;
            }
        }
        // Handle file upload if provided
        elseif (!empty($_FILES['image_file']['name'])) {
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
        // If editing existing article and no new image provided, preserve existing image
        elseif (!empty($_POST['id'])) {
            $existingPost = $this->postModel->getById($_POST['id']);
            if ($existingPost && !empty($existingPost['image_url'])) {
                $imageUrl = $existingPost['image_url'];
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

        $publishedNow = false;
        $postId = null;

        if (!empty($_POST['id'])) {
            $postId = (int)$_POST['id'];
            $existing = $this->postModel->getById($postId);
            $prevStatus = $existing['statut'] ?? '';

            $this->postModel->update($postId, $data);
            $_SESSION['flash_success'] = 'Article updated successfully!';

            if ($prevStatus !== 'publie' && ($data['statut'] ?? '') === 'publie') {
                $publishedNow = true;
            }
        } else {
            $postId = (int)$this->postModel->create($data);
            $_SESSION['flash_success'] = 'Article created successfully!';

            if (($data['statut'] ?? '') === 'publie') {
                $publishedNow = true;
            }
        }

        // Newsletter blast when a post is newly published
        if ($publishedNow && $postId) {
            try {
                // Get all active subscribers
                require_once __DIR__ . '/../Models/EmailSubscriber.php';
                $emailSubscriber = new \Models\EmailSubscriber();
                $subscribers = $emailSubscriber->getActiveSubscribers();

                if (!empty($subscribers)) {
                    $post = $this->postModel->getById($postId);
                    $title = $post['titre'] ?? 'New article';
                    $excerpt = substr($post['contenu'] ?? '', 0, 200) . '...';
                    $postUrl = (($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/integration/magazine/article?id=' . $postId);

                    // Use NotificationHelper to send emails to all subscribers
                    NotificationHelper::sendNewPostNotificationToSubscribers($title, $excerpt, $postId);

                    $_SESSION['flash_success'] .= " 📧 Newsletter sent to " . count($subscribers) . " subscriber(s).";
                }
            } catch (Exception $e) {
                error_log("Newsletter send error: " . $e->getMessage());
                // Don't fail the post save if newsletter fails
            }
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

        $postId = $_GET['id'] ?? 0;
        if (!empty($postId)) {
            $this->postModel->delete($postId);
            $_SESSION['flash_success'] = 'Article deleted successfully!';
        }
        
        // If referrer is from front office, redirect back to magazine
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, '/magazine/article') !== false) {
            header('Location: /integration/magazine');
        } else {
            header('Location: /integration/magazine/admin/articles');
        }
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
        
        try {
            $id     = (int)($_GET['id'] ?? 0);
            $userId = (int)($_SESSION['user']['id'] ?? 0);
            $action = $_GET['action'] ?? 'like';

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Missing article ID']);
                exit;
            }

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'You must be logged in to like articles.']);
                exit;
            }

            $result = $this->postModel->toggleLike($id, $userId);
            
            // Trigger notification only on like action (not unlike)
            if ($action === 'like' && $result['liked']) {
                try {
                    $user = $this->userModel->getUserById($userId);
                    $post = $this->postModel->getById($id);
                    
                    $userName = $user['nom'] ?? 'Someone';
                    $postTitle = $post['titre'] ?? 'Article';
                    $postAuthorId = (int)($post['auteur_id'] ?? 0);

                    // Notify all admins about the like (in-app only)
                    if ($postAuthorId && $postAuthorId !== $userId) {
                        NotificationHelper::notifyPostLiked($userName, $postTitle);
                    }
                } catch (Exception $notifErr) {
                    // Log but don't fail the like
                    error_log("Notification error: " . $notifErr->getMessage());
                }
            }
            
            echo json_encode([
                'success' => true,
                'liked'   => $result['liked'],
                'likes'   => $result['likes'],
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            error_log("Like error: " . $e->getMessage());
            exit;
        }
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

    /**
     * Handle image data URL from canvas editor and save as file
     */
    private function handleDataUrlImage(string $dataUrl): array
    {
        // Log for debugging
        error_log("DEBUG: handleDataUrlImage called with data URL length: " . strlen($dataUrl));
        
        // Validate data URL format - be more flexible with the regex
        if (!preg_match('/^data:image\/([a-z]+);base64,(.+)$/i', $dataUrl, $matches)) {
            error_log("DEBUG: Data URL regex failed to match. Data: " . substr($dataUrl, 0, 100));
            return ['success' => false, 'error' => 'Invalid image data URL format.'];
        }

        $mimeType = 'image/' . strtolower($matches[1]);
        $imageData = base64_decode($matches[2], true);

        if ($imageData === false) {
            error_log("DEBUG: Base64 decode failed");
            return ['success' => false, 'error' => 'Failed to decode image data.'];
        }

        if (strlen($imageData) > 5 * 1024 * 1024) {
            error_log("DEBUG: Image too large: " . strlen($imageData) . " bytes");
            return ['success' => false, 'error' => 'Image size must be less than 5MB.'];
        }

        $uploadDir = __DIR__ . '/../assets/uploads';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("DEBUG: Failed to create upload directory: " . $uploadDir);
                return ['success' => false, 'error' => 'Failed to create upload directory.'];
            }
        }

        // Map MIME types to file extensions
        $extMap    = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $extension = $extMap[$mimeType] ?? 'jpg';
        $filename  = uniqid('img_', true) . '.' . $extension;
        $filepath  = $uploadDir . '/' . $filename;

        if (file_put_contents($filepath, $imageData) === false) {
            error_log("DEBUG: Failed to write file to: " . $filepath);
            return ['success' => false, 'error' => 'Failed to save the image.'];
        }

        error_log("DEBUG: Image saved successfully: " . $filepath);
        return ['success' => true, 'path' => '/integration/assets/uploads/' . $filename];
    }

    /**
     * AI Summarization (AJAX endpoint) - Using Google Gemini
     */
    public function summarizeArticle(): void {
        // Clear any output buffers to prevent HTML being prepended
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $postId = $_GET['id'] ?? null;
            if (!$postId) {
                echo json_encode(['success' => false, 'error' => 'Missing Post ID']);
                exit;
            }

            $post = $this->postModel->getById($postId);
            if (!$post) {
                echo json_encode(['success' => false, 'error' => 'Post not found']);
                exit;
            }

            // Clean content for AI
            $content = strip_tags($post['contenu']);
            $content = substr($content, 0, 4000); // Token limit safety

            $ch = curl_init("http://localhost:11434/api/chat");
            
            $prompt = "You are a professional medical journalist. Summarize the following health article into 3-4 concise, high-impact bullet points. Use a professional and encouraging tone.\n\nTitle: " . $post['titre'] . "\n\nContent: " . $content;
            
            $data = [
                "model" => "llama3:latest",
                "stream" => false,
                "messages" => [
                    ["role" => "system", "content" => "You are a professional medical journalist. Summarize health articles into 3-4 concise, high-impact bullet points."],
                    ["role" => "user", "content" => $prompt]
                ]
            ];
            
            // Log the request being sent
            error_log("Ollama Summarize Request: " . json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Debug logging - ALWAYS output what we get
            error_log("=== OLLAMA SUMMARIZE DEBUG ===");
            error_log("HTTP Code: " . $httpCode);
            error_log("cURL Error: " . ($curlError ?: "NONE"));
            error_log("Response (first 1000 chars): " . substr($response, 0, 1000));
            error_log("Response Length: " . strlen($response));
            error_log("===========================");
            
            // If we got HTML back, it's likely a connection error
            if (strpos($response, '<') === 0) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Ollama Connection Failed',
                    'details' => 'Received HTML. Check http://localhost:11434/api/tags in browser',
                    'httpCode' => $httpCode,
                    'rawResponse' => substr($response, 0, 200)
                ]);
                exit;
            }

            // Check for cURL errors first
            if ($curlError) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Connection Error: ' . $curlError
                ]);
                exit;
            }

            // Check HTTP status code
            if ($httpCode !== 200) {
                $errorDetails = is_string($response) ? $response : json_encode($response);
                echo json_encode([
                    'success' => false, 
                    'error' => 'API Error (HTTP ' . $httpCode . ')',
                    'details' => substr($errorDetails, 0, 500)
                ]);
                exit;
            }

            // Log raw response for debugging
            error_log("Ollama Response: " . substr($response, 0, 500));
            
            $result = json_decode($response, true);
            
            if (!$result) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Invalid JSON Response from Ollama',
                    'details' => 'Raw: ' . substr($response, 0, 300)
                ]);
                exit;
            }
            
            // Handle Ollama response format
            if (isset($result['message']['content'])) {
                $summary = $result['message']['content'];
            } elseif (isset($result['choices'][0]['message']['content'])) {
                $summary = $result['choices'][0]['message']['content'];
            } else {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Unexpected API Response Format',
                    'details' => json_encode($result)
                ]);
                exit;
            }

            echo json_encode(['success' => true, 'summary' => $summary]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Server Error',
                'details' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Rephrase article content using AI
     */
    /**
     * Rephrase article content using Google Gemini
     */
    public function rephraseContent(): void {
        // IMMEDIATE DEBUG: Log that this function was called
        error_log("=== REPHRASE CONTENT FUNCTION CALLED ===");
        
        // Clear any output buffers to prevent HTML being prepended
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $content = $input['content'] ?? '';
            $tone = $input['tone'] ?? 'professional';

            if (!$content) {
                echo json_encode(['success' => false, 'error' => 'No content provided']);
                exit;
            }

            // Limit content length for API
            $content = substr($content, 0, 3000);

            $tonePrompts = [
                'professional' => 'Rephrase in a professional and formal tone.',
                'friendly' => 'Rephrase in a friendly and conversational tone.',
                'academic' => 'Rephrase in an academic and detailed tone.',
                'simple' => 'Rephrase in a simple and clear tone, easy to understand.'
            ];

            $prompt = $tonePrompts[$tone] ?? $tonePrompts['professional'];

            $ch = curl_init("http://localhost:11434/api/chat");
            
            $fullPrompt = "You are a professional content editor. Your task is to rephrase medical/health content to improve clarity, flow, and readability while maintaining accuracy.\n\n" . $prompt . "\n\nContent to rephrase:\n\n" . $content;
            
            $data = [
                "model" => "llama3:latest",
                "stream" => false,
                "messages" => [
                    ["role" => "system", "content" => "You are a professional content editor. Rephrase medical/health content to improve clarity, flow, and readability."],
                    ["role" => "user", "content" => $fullPrompt]
                ]
            ];
            
            // Log the request being sent
            error_log("Ollama Rephrase Request: " . json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curlError) {
                echo json_encode(['success' => false, 'error' => 'Connection Error: ' . $curlError]);
                exit;
            }

            if ($httpCode !== 200) {
                echo json_encode(['success' => false, 'error' => 'AI Service Error (HTTP ' . $httpCode . ')', 'details' => substr($response, 0, 300)]);
                exit;
            }

            // Debug logging - ALWAYS output what we get
            error_log("=== OLLAMA REPHRASE DEBUG ===");
            error_log("HTTP Code: " . $httpCode);
            error_log("Response (first 1000 chars): " . substr($response, 0, 1000));
            error_log("Response Length: " . strlen($response));
            error_log("===========================");
            
            // If we got HTML back, it's likely a connection error
            if (strpos($response, '<') === 0) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Ollama Connection Failed',
                    'details' => 'Received HTML. Check http://localhost:11434/api/tags in browser',
                    'httpCode' => $httpCode,
                    'rawResponse' => substr($response, 0, 200)
                ]);
                exit;
            }
            
            $result = json_decode($response, true);
            
            if (!$result) {
                echo json_encode(['success' => false, 'error' => 'Invalid JSON Response from Ollama', 'details' => 'Raw: ' . substr($response, 0, 300)]);
                exit;
            }
            
            // Handle Ollama response format
            if (isset($result['message']['content'])) {
                $rephrased = $result['message']['content'];
            } elseif (isset($result['choices'][0]['message']['content'])) {
                $rephrased = $result['choices'][0]['message']['content'];
            } else {
                echo json_encode(['success' => false, 'error' => 'Unexpected API Response Format', 'details' => json_encode($result)]);
                exit;
            }

            echo json_encode(['success' => true, 'rephrased' => $rephrased]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Server Error',
                'details' => $e->getMessage()
            ]);
            exit;
        }
    }
}
