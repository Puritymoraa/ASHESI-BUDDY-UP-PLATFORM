<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if (!isset($_GET['post_id'])) {
    $_SESSION['error'] = "❌ Invalid request: No blog post ID provided";
    header('Location: index.php');
    exit();
}

try {
    $post_id = $_GET['post_id'];
    
    // First check if blog post exists and get details
    $stmt = $pdo->prepare("
        SELECT title, DATE_FORMAT(created_at, '%M %d, %Y') as formatted_date 
        FROM blog_posts 
        WHERE post_id = ?
    ");
    $stmt->execute([$post_id]);
    $blog = $stmt->fetch();
    
    if (!$blog) {
        throw new Exception("Blog post not found in the system");
    }

    // Delete the blog post
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    
    $_SESSION['success'] = sprintf(
        "✅ Successfully deleted blog post: '%s' (created on %s)",
        htmlspecialchars($blog['title']),
        $blog['formatted_date']
    );
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "❌ Failed to delete blog post: " . $e->getMessage();
}

// Add back button functionality through session
$_SESSION['show_back'] = true;
header('Location: index.php');
exit();
?> 