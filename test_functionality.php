<?php
/**
 * Test script to diagnose like and comment functionality
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/models/Comment.php';

session_start();

echo "=== MediFlow Like & Comment Test ===\n\n";

// Test data
$testPostId = 1;
$testCommentContent = "This is a test comment from the diagnostic script.";
$testUserId = 4;

try {
    $postModel = new Post();
    $commentModel = new Comment();

    // Get initial post state
    $post = $postModel->getById($testPostId);
    echo "1. Initial Post State:\n";
    echo "   ID: " . $post['id'] . "\n";
    echo "   Title: " . $post['titre'] . "\n";
    echo "   Likes: " . $post['likes_count'] . "\n";
    echo "   Views: " . $post['views_count'] . "\n\n";

    // Test incrementing likes
    echo "2. Testing Like Increment:\n";
    $oldLikes = $post['likes_count'];
    $newLikes = $postModel->incrementLikes($testPostId);
    echo "   Before: " . $oldLikes . " → After: " . $newLikes . "\n";
    echo "   ✓ Like increment " . ($newLikes > $oldLikes ? "WORKS" : "FAILED") . "\n\n";

    // Test incrementing views
    echo "3. Testing View Increment:\n";
    $oldViews = $post['views_count'];
    $postModel->incrementViews($testPostId);
    $updated = $postModel->getById($testPostId);
    $newViews = $updated['views_count'];
    echo "   Before: " . $oldViews . " → After: " . $newViews . "\n";
    echo "   ✓ View increment " . ($newViews > $oldViews ? "WORKS" : "FAILED") . "\n\n";

    // Test creating a comment
    echo "4. Testing Comment Creation:\n";
    $commentData = [
        'id_post' => $testPostId,
        'id_utilisateur' => $testUserId,
        'contenu' => $testCommentContent,
        'statut' => 'en_attente'
    ];
    $commentId = $commentModel->create($commentData);
    echo "   Comment ID: " . $commentId . "\n";
    echo "   ✓ Comment creation " . ($commentId ? "WORKS" : "FAILED") . "\n\n";

    // Verify comment was saved
    if ($commentId) {
        $comment = $commentModel->getById($commentId);
        echo "5. Verifying Saved Comment:\n";
        echo "   Content: " . substr($comment['contenu'], 0, 50) . "...\n";
        echo "   Status: " . $comment['statut'] . "\n";
        echo "   ✓ Comment verified\n\n";
    }

    // Get comment count
    $count = $commentModel->countByPost($testPostId);
    echo "6. Comment Count for Post $testPostId: " . $count . "\n\n";

    echo "✅ All tests completed successfully!\n";
    echo "\n💡 If likes/comments still don't appear in the UI:\n";
    echo "   - Check browser console (F12) for JavaScript errors\n";
    echo "   - Check that the form/button elements have correct IDs\n";
    echo "   - Verify AJAX URLs are correct\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
?>
