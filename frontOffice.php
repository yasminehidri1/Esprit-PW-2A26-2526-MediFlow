<?php
/**
 * MediFlow Magazine — Front Office Entry Point (Router)
 * Dispatches requests to the appropriate controller action
 */

session_start();

require_once __DIR__ . '/controllers/PostController.php';
require_once __DIR__ . '/controllers/CommentController.php';

$action = $_GET['action'] ?? 'home';

// ---- Comment Actions ----
$commentActions = ['add_comment', 'add_comment_ajax', 'edit_comment', 'delete_own_comment'];
if (in_array($action, $commentActions)) {
    $commentController = new CommentController();

    switch ($action) {
        case 'add_comment':
            $commentController->addComment();
            break;
        case 'add_comment_ajax':
            $commentController->addCommentAjax();
            break;
        case 'edit_comment':
            $commentController->editComment();
            break;
        case 'delete_own_comment':
            $commentController->deleteOwnComment();
            break;
    }
    exit;
}

// ---- Post Actions ----
$postController = new PostController();

switch ($action) {
    case 'home':
        $postController->home();
        break;
    case 'view':
        $postController->viewArticle();
        break;
    case 'category':
        $postController->category();
        break;
    case 'like':
        $postController->likeArticle();
        break;
    case 'search':
        $postController->searchArticles();
        break;
    default:
        $postController->home();
        break;
}
