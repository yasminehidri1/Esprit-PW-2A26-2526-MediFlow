<?php
// Suppress all display errors
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '0');

// Buffer output
ob_start();

// Header
header('Content-Type: application/json');

$log = __DIR__ . '/upload.log';

try {
    file_put_contents($log, "\n=== UPLOAD " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
    
    // Start session
    file_put_contents($log, "1. Starting session\n", FILE_APPEND);
    session_start();
    file_put_contents($log, "2. Session started, ID: " . session_id() . "\n", FILE_APPEND);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Not POST');
    }
    file_put_contents($log, "3. Method POST OK\n", FILE_APPEND);
    
    if (empty($_SESSION['user']['id'])) {
        throw new Exception('No user');
    }
    file_put_contents($log, "4. User ID: " . $_SESSION['user']['id'] . "\n", FILE_APPEND);
    
    $userId = (int)$_SESSION['user']['id'];
    
    if (empty($_FILES['profile_pic'])) {
        throw new Exception('No file');
    }
    file_put_contents($log, "5. File received: " . $_FILES['profile_pic']['name'] . "\n", FILE_APPEND);
    
    $file = $_FILES['profile_pic'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }
    file_put_contents($log, "6. File upload OK\n", FILE_APPEND);
    
    // Validate file
    if ($file['size'] <= 0 || $file['size'] > 5242880) {
        throw new Exception('Bad size');
    }
    file_put_contents($log, "7. File size OK: " . $file['size'] . " bytes\n", FILE_APPEND);
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        throw new Exception('Bad ext');
    }
    file_put_contents($log, "8. File ext OK: " . $ext . "\n", FILE_APPEND);
    
    if (!@getimagesize($file['tmp_name'])) {
        throw new Exception('Not image');
    }
    file_put_contents($log, "9. Image validation OK\n", FILE_APPEND);
    
    // Create dir
    $dir = __DIR__ . '/../assets/uploads/profiles';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    file_put_contents($log, "10. Directory ready: " . $dir . "\n", FILE_APPEND);
    
    // Save file
    $fname = 'profile_' . $userId . '_' . time() . '.' . $ext;
    $path = $dir . '/' . $fname;
    
    if (!@move_uploaded_file($file['tmp_name'], $path)) {
        throw new Exception('Move failed');
    }
    file_put_contents($log, "11. File moved to: " . $path . "\n", FILE_APPEND);
    
    // Database
    file_put_contents($log, "12. Loading config\n", FILE_APPEND);
    if (!file_exists(__DIR__ . '/../config.php')) {
        throw new Exception('Config file not found at ' . __DIR__ . '/../config.php');
    }
    try {
        require_once __DIR__ . '/../config.php';
        file_put_contents($log, "13. Getting DB connection\n", FILE_APPEND);
        $db = \config::getConnexion();
        file_put_contents($log, "14. DB connected\n", FILE_APPEND);
    } catch (Throwable $dbErr) {
        file_put_contents($log, "DB ERROR: " . $dbErr->getMessage() . "\n", FILE_APPEND);
        throw $dbErr;
    }
    
    // Delete old
    file_put_contents($log, "15. Checking for old profile pic\n", FILE_APPEND);
    $s = $db->prepare("SELECT profile_pic FROM utilisateurs WHERE id_PK = ?");
    $s->execute([$userId]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if ($r && $r['profile_pic']) {
        @unlink(__DIR__ . '/../' . ltrim($r['profile_pic'], '/'));
        file_put_contents($log, "16. Old pic deleted\n", FILE_APPEND);
    } else {
        file_put_contents($log, "16. No old pic found\n", FILE_APPEND);
    }
    
    // Update
    file_put_contents($log, "17. Updating database\n", FILE_APPEND);
    $p = '/integration/assets/uploads/profiles/' . $fname;
    $s = $db->prepare("UPDATE utilisateurs SET profile_pic = ? WHERE id_PK = ?");
    $s->execute([$p, $userId]);
    file_put_contents($log, "18. Database updated successfully\n", FILE_APPEND);
    
    $_SESSION['user']['profile_pic'] = $p;
    
    ob_end_clean();
    echo json_encode(['success' => true, 'filename' => $p]);
    
    
} catch (Exception $e) {
    file_put_contents($log, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

exit;