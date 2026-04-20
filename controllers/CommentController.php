<?php
/**
 * Comment Controller — MediFlow Magazine Module
 * Handles comment moderation (back office) and commenting (front office)
 */

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Post.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
    }

    // =========================================================
    // BACK OFFICE ACTIONS (Moderation)
    // =========================================================

    /**
     * Show moderation queue
     */
    public function moderationQueue() {
        $pendingComments = $this->commentModel->getPending();
        $commentStats = $this->commentModel->getStats();
        $currentView = 'moderation';

        include __DIR__ . '/../views/backOffice/layout.php';
    }

    /**
     * Approve a comment
     */
    public function approveComment() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->approve($id);
            $_SESSION['flash_success'] = 'Comment approved successfully!';
        }

        $redirect = $_GET['redirect'] ?? 'backOffice.php?action=moderation';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Reject a comment
     */
    public function rejectComment() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->reject($id);
            $_SESSION['flash_success'] = 'Comment rejected.';
        }

        $redirect = $_GET['redirect'] ?? 'backOffice.php?action=moderation';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Delete a comment (admin)
     */
    public function deleteComment() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->delete($id);
            $_SESSION['flash_success'] = 'Comment deleted successfully!';
        }

        $redirect = $_GET['redirect'] ?? 'backOffice.php?action=moderation';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * View comments tab — all comments or filtered by post
     */
    public function viewPostComments() {
        $postId  = !empty($_GET['post_id']) ? (int)$_GET['post_id'] : null;
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 8;

        $postModel = new Post();

        if ($postId) {
            // ---- Filtered mode: specific post ----
            $post          = $postModel->getById($postId);
            $totalComments = $this->commentModel->countAllByPost($postId);
            $totalPages    = max(1, (int)ceil($totalComments / $perPage));
            $currentPage   = min($page, $totalPages);
            $postComments  = $this->commentModel->getAllByPostWithPagination($postId, $currentPage, $perPage);
        } else {
            // ---- All-comments mode: no filter ----
            $post          = null;
            $totalComments = $this->commentModel->countAll();
            $totalPages    = max(1, (int)ceil($totalComments / $perPage));
            $currentPage   = min($page, $totalPages);
            $postComments  = $this->commentModel->getRecentAllPaginated($currentPage, $perPage);
        }

        $commentStats = $this->commentModel->getStats();
        $currentView  = 'post_comments';

        include __DIR__ . '/../views/backOffice/layout.php';
    }

    // =========================================================
    // FRONT OFFICE ACTIONS
    // =========================================================

    /**
     * Add a new comment (from front office)
     */
    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: frontOffice.php');
            exit;
        }

        $postId = $_POST['id_post'] ?? null;
        $contenu = trim($_POST['contenu'] ?? '');
        $userId = $_POST['id_utilisateur'] ?? 4; // Default to a user for now (no auth module)

        if (empty($contenu)) {
            $_SESSION['flash_error'] = 'Comment cannot be empty.';
            header('Location: frontOffice.php?action=view&id=' . $postId);
            exit;
        }

        if (strlen($contenu) > 1000) {
            $_SESSION['flash_error'] = 'Comment is too long (max 1000 characters).';
            header('Location: frontOffice.php?action=view&id=' . $postId);
            exit;
        }

        $this->commentModel->create([
            'id_post'        => $postId,
            'id_utilisateur' => $userId,
            'contenu'        => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            'statut'         => 'approuve'
        ]);

        $_SESSION['flash_success'] = 'Your comment has been posted!';
        header('Location: frontOffice.php?action=view&id=' . $postId);
        exit;
    }

    /**
     * Add comment via AJAX
     */
    public function addCommentAjax() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $postId = $input['id_post'] ?? null;
        $contenu = trim($input['contenu'] ?? '');
        $userId = $input['id_utilisateur'] ?? 4;

        if (empty($contenu)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            exit;
        }

        $commentId = $this->commentModel->create([
            'id_post'        => $postId,
            'id_utilisateur' => $userId,
            'contenu'        => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            'statut'         => 'approuve'
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Comment posted!',
            'comment_id' => $commentId
        ]);
        exit;
    }

    /**
     * Edit own comment (front office)
     */
    public function editComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: frontOffice.php');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $contenu = trim($_POST['contenu'] ?? '');
        $postId = $_POST['id_post'] ?? null;

        if ($id && !empty($contenu)) {
            $this->commentModel->update($id, htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'));
            $_SESSION['flash_success'] = 'Comment updated successfully!';
        }

        header('Location: frontOffice.php?action=view&id=' . $postId);
        exit;
    }

    /**
     * Delete own comment (front office)
     */
    public function deleteOwnComment() {
        $id = $_GET['id'] ?? null;
        $postId = $_GET['post_id'] ?? null;

        if ($id) {
            $this->commentModel->delete($id);
            $_SESSION['flash_success'] = 'Comment deleted.';
        }

        header('Location: frontOffice.php?action=view&id=' . $postId);
        exit;
    }
}
