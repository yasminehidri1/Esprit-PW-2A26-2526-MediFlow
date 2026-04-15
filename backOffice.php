<?php
/**
 * MediFlow Magazine — Back Office Entry Point (Router)
 * Dispatches requests to the appropriate controller action
 */

session_start();

require_once __DIR__ . '/controllers/PostController.php';
require_once __DIR__ . '/controllers/CommentController.php';

$controller = $_GET['controller'] ?? 'post';
$action = $_GET['action'] ?? 'dashboard';

// ---- Comment Controller Routes ----
if ($controller === 'comment') {
    $commentController = new CommentController();

    switch ($action) {
        case 'approve':
            $commentController->approveComment();
            break;
        case 'reject':
            $commentController->rejectComment();
            break;
        case 'delete_comment':
            $commentController->deleteComment();
            break;
        default:
            $commentController->moderationQueue();
            break;
    }
    exit;
}

// ---- Post Controller Routes (default) ----
$postController = new PostController();

switch ($action) {
    case 'dashboard':
        $postController->dashboard();
        break;
    case 'articles':
        $postController->listArticles();
        break;
    case 'form':
        $postController->showForm();
        break;
    case 'save':
        $postController->saveArticle();
        break;
    case 'delete':
        $postController->deleteArticle();
        break;
    default:
        $postController->dashboard();
        break;
}
