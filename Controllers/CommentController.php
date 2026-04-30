<?php
/**
 * Comment Controller — MediFlow Magazine Module
 * Handles comment moderation (back office) and commenting (front office).
 *
 * @package MediFlow\Controllers
 * @version 2.0.0
 */

namespace Controllers;

use Core\SessionHelper;

class CommentController
{
    use SessionHelper;

    private $commentModel;

    public function __construct()
    {
        $this->ensureSession();
        require_once __DIR__ . '/../Models/Comment.php';
        require_once __DIR__ . '/../Models/Post.php';
        $this->commentModel = new \Comment();
    }

    // =========================================================
    // BACK OFFICE ACTIONS (Moderation)
    // =========================================================

    /**
     * Show moderation queue (pending comments)
     */
    public function moderationQueue(): void
    {
        $this->requireMagazineAccess();

        $pendingComments = $this->commentModel->getPending();
        $commentStats    = $this->commentModel->getStats();
        $currentView     = 'moderation';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    /**
     * Approve a comment
     */
    public function approveComment(): void
    {
        $this->requireMagazineAccess();

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->approve($id);
            $_SESSION['flash_success'] = 'Comment approved successfully!';
        }

        $redirect = $_GET['redirect'] ?? '/integration/magazine/admin/comments';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Reject a comment
     */
    public function rejectComment(): void
    {
        $this->requireMagazineAccess();

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->reject($id);
            $_SESSION['flash_success'] = 'Comment rejected.';
        }

        $redirect = $_GET['redirect'] ?? '/integration/magazine/admin/comments';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Delete a comment (back office admin action)
     */
    public function deleteComment(): void
    {
        $this->requireMagazineAccess();

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->commentModel->delete($id);
            $_SESSION['flash_success'] = 'Comment deleted successfully!';
        }

        $redirect = $_GET['redirect'] ?? '/integration/magazine/admin/comments';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * View comments tab — all comments or filtered by post
     */
    public function viewPostComments(): void
    {
        $this->requireMagazineAccess();

        $postId  = !empty($_GET['post_id']) ? (int)$_GET['post_id'] : null;
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 8;

        $postModel = new \Post();

        if ($postId) {
            // Filtered mode: specific post
            $post          = $postModel->getById($postId);
            $totalComments = $this->commentModel->countAllByPost($postId);
            $totalPages    = max(1, (int)ceil($totalComments / $perPage));
            $currentPage   = min($page, $totalPages);
            $postComments  = $this->commentModel->getAllByPostWithPagination($postId, $currentPage, $perPage);
        } else {
            // All-comments mode: no filter
            $post          = null;
            $totalComments = $this->commentModel->countAll();
            $totalPages    = max(1, (int)ceil($totalComments / $perPage));
            $currentPage   = min($page, $totalPages);
            $postComments  = $this->commentModel->getRecentAllPaginated($currentPage, $perPage);
        }

        $commentStats = $this->commentModel->getStats();
        $currentView  = 'post_comments';

        include __DIR__ . '/../Views/Back/layout.php';
    }

    // =========================================================
    // FRONT OFFICE ACTIONS
    // =========================================================

    /**
     * Add a new comment (form POST from front office)
     */
    public function addComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/magazine');
            exit;
        }

        $postId  = $_POST['id_post'] ?? null;
        $contenu = trim($_POST['contenu'] ?? '');

        // Use the authenticated user's ID; fall back to guest placeholder
        $userId  = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash_error'] = 'You must be logged in to comment.';
            header('Location: /integration/magazine/article?id=' . $postId);
            exit;
        }

        if (empty($contenu)) {
            $_SESSION['flash_error'] = 'Comment cannot be empty.';
            header('Location: /integration/magazine/article?id=' . $postId);
            exit;
        }

        if (strlen($contenu) > 1000) {
            $_SESSION['flash_error'] = 'Comment is too long (max 1000 characters).';
            header('Location: /integration/magazine/article?id=' . $postId);
            exit;
        }

        $this->commentModel->create([
            'id_post'        => $postId,
            'id_utilisateur' => $userId,
            'contenu'        => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            'statut'         => 'approuve',
        ]);

        $_SESSION['flash_success'] = 'Your comment has been posted!';
        header('Location: /integration/magazine/article?id=' . $postId);
        exit;
    }

    /**
     * Add comment via AJAX
     */
    public function addCommentAjax(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'You must be logged in to comment.']);
            exit;
        }

        $input   = json_decode(file_get_contents('php://input'), true);
        $postId  = $input['id_post'] ?? null;
        $contenu = trim($input['contenu'] ?? '');

        if (empty($contenu)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            exit;
        }

        $commentId = $this->commentModel->create([
            'id_post'        => $postId,
            'id_utilisateur' => $userId,
            'contenu'        => htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            'statut'         => 'approuve',
        ]);

        echo json_encode([
            'success'    => true,
            'message'    => 'Comment posted!',
            'comment_id' => $commentId,
        ]);
        exit;
    }

    /**
     * Edit own comment (front office)
     */
    public function editComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /integration/magazine');
            exit;
        }

        $id      = (int)($_POST['id'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');
        $postId  = (int)($_POST['id_post'] ?? 0);
        $userId  = (int)($_SESSION['user']['id'] ?? 0);

        if ($id && !empty($contenu) && $userId) {
            // Verify ownership
            $comment = $this->commentModel->getById($id);
            if ($comment && (int)$comment['id_utilisateur'] === $userId) {
                $this->commentModel->update($id, htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'));
                $_SESSION['flash_success'] = 'Comment updated successfully!';
            } else {
                $_SESSION['flash_error'] = 'You can only edit your own comments.';
            }
        }

        header('Location: /integration/magazine/article?id=' . $postId);
        exit;
    }

    /**
     * Delete own comment (front office)
     */
    public function deleteOwnComment(): void
    {
        $id     = (int)($_GET['id'] ?? 0);
        $postId = (int)($_GET['post_id'] ?? 0);
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if ($id && $userId) {
            $comment = $this->commentModel->getById($id);
            if ($comment && (int)$comment['id_utilisateur'] === $userId) {
                $this->commentModel->delete($id);
                $_SESSION['flash_success'] = 'Comment deleted.';
            } else {
                $_SESSION['flash_error'] = 'You can only delete your own comments.';
            }
        }

        // AJAX request — return JSON instead of redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: /integration/magazine/article?id=' . $postId);
        exit;
    }

    // =========================================================
    // HELPERS
    // =========================================================

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
}
