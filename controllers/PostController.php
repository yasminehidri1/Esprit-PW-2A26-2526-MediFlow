<?php
/**
 * Post Controller — MediFlow Magazine Module
 * Handles article-related actions for both back office and front office
 */

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController {
    private $postModel;
    private $commentModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
    }

    // =========================================================
    // BACK OFFICE ACTIONS
    // =========================================================

    /**
     * Dashboard — show stats and recent articles
     */
    public function dashboard() {
        $postStats = $this->postModel->getStats();
        $commentStats = $this->commentModel->getStats();
        $recentPosts = $this->postModel->getRecent(5);
        $pendingComments = $this->commentModel->getPending();

        include __DIR__ . '/../views/backOffice/layout.php';
    }

    /**
     * List all articles (admin view with all statuses)
     */
    public function listArticles() {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $filters = [];

        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        if (!empty($_GET['categorie'])) {
            $filters['categorie'] = $_GET['categorie'];
        }
        if (!empty($_GET['statut'])) {
            $filters['statut'] = $_GET['statut'];
        }

        $result = $this->postModel->getAll($filters, $page, 10);
        $categories = $this->postModel->getCategories();
        $currentView = 'articles';

        include __DIR__ . '/../views/backOffice/layout.php';
    }

    /**
     * Show the create/edit form
     */
    public function showForm() {
        $post = null;
        if (!empty($_GET['id'])) {
            $post = $this->postModel->getById($_GET['id']);
        }
        $currentView = 'article_form';

        include __DIR__ . '/../views/backOffice/layout.php';
    }

    /**
     * Process create/edit form submission
     */
    public function saveArticle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: backOffice.php?action=articles');
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
            header('Location: backOffice.php?action=form&id=' . ($_POST['id'] ?? ''));
            exit;
        }

        $data = [
            'titre'     => htmlspecialchars(trim($_POST['titre']), ENT_QUOTES, 'UTF-8'),
            'contenu'   => trim($_POST['contenu']),
            'categorie' => $_POST['categorie'] ?? 'General Health',
            'image_url' => !empty($_POST['image_url']) ? trim($_POST['image_url']) : null,
            'auteur_id' => $_POST['auteur_id'] ?? 1,
            'statut'    => $_POST['statut'] ?? 'brouillon'
        ];

        if (!empty($_POST['id'])) {
            // Update existing
            $this->postModel->update($_POST['id'], $data);
            $_SESSION['flash_success'] = 'Article updated successfully!';
        } else {
            // Create new
            $this->postModel->create($data);
            $_SESSION['flash_success'] = 'Article created successfully!';
        }

        header('Location: backOffice.php?action=articles');
        exit;
    }

    /**
     * Delete an article
     */
    public function deleteArticle() {
        if (!empty($_GET['id'])) {
            $this->postModel->delete($_GET['id']);
            $_SESSION['flash_success'] = 'Article deleted successfully!';
        }
        header('Location: backOffice.php?action=articles');
        exit;
    }

    // =========================================================
    // FRONT OFFICE ACTIONS
    // =========================================================

    /**
     * Front office home page — featured articles
     */
    public function home() {
        $featuredPost = $this->postModel->getMostPopular();
        $recentPosts = $this->postModel->getRecent(6);
        $categories = $this->postModel->getCategories();

        // Get comment count for featured post
        if ($featuredPost) {
            $featuredPost['comment_count'] = $this->commentModel->countByPost($featuredPost['id']);
        }

        $currentView = 'home';
        include __DIR__ . '/../views/frontOffice/layout.php';
    }

    /**
     * View a single article (front office)
     */
    public function viewArticle() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: frontOffice.php');
            exit;
        }

        $post = $this->postModel->getById($id);
        if (!$post || $post['statut'] !== 'publie') {
            header('Location: frontOffice.php');
            exit;
        }

        // Increment view count
        $this->postModel->incrementViews($id);
        $post['views_count']++;

        // Get comments
        $comments = $this->commentModel->getByPost($id);
        $commentCount = $this->commentModel->countByPost($id);

        // Get related posts (same category)
        $relatedResult = $this->postModel->getAll(['categorie' => $post['categorie'], 'statut' => 'publie'], 1, 4);
        $relatedPosts = array_filter($relatedResult['data'], function($p) use ($id) {
            return $p['id'] != $id;
        });
        $relatedPosts = array_slice($relatedPosts, 0, 3);

        $currentView = 'article';
        include __DIR__ . '/../views/frontOffice/layout.php';
    }

    /**
     * Browse articles by category
     */
    public function category() {
        $categorie = $_GET['cat'] ?? 'General Health';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $result = $this->postModel->getByCategory($categorie, $page, 9);
        $categories = $this->postModel->getCategories();

        $currentView = 'category';
        include __DIR__ . '/../views/frontOffice/layout.php';
    }

    /**
     * Like an article (AJAX endpoint)
     */
    public function likeArticle() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing article ID']);
            exit;
        }

        $newCount = $this->postModel->incrementLikes($id);
        echo json_encode(['success' => true, 'likes' => $newCount]);
        exit;
    }

    /**
     * Search articles (AJAX endpoint)
     */
    public function searchArticles() {
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
}
