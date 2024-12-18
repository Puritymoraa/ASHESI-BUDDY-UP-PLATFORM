<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

try {
    $post_id = $_POST['post_id'] ?? null;
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Validate inputs
    if (empty($title) || empty($content)) {
        throw new Exception("Title and content are required");
    }

    // Validate title length
    if (strlen($title) > 255) {
        throw new Exception("Title must be less than 255 characters");
    }

    // Format content for better display
    $content = nl2br(htmlspecialchars($content));

    if ($post_id) {
        // Update existing blog post
        $stmt = $pdo->prepare("
            UPDATE blog_posts 
            SET title = ?, 
                content = ?, 
                updated_at = CURRENT_TIMESTAMP
            WHERE post_id = ?
        ");
        $stmt->execute([$title, $content, $post_id]);
        $_SESSION['success'] = "✅ Blog post updated successfully!";
    } else {
        // Create new blog post
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (
                user_id, 
                title, 
                content,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        $_SESSION['success'] = "✅ New blog post created successfully!";
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "❌ Failed to save blog post: " . $e->getMessage();
}

header('Location: index.php');
exit();
?> 