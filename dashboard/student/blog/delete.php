<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing', 'Freshman']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: index.php');
    exit();
}

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['user_id'];

try {
    // Verify ownership and delete
    $stmt = $pdo->prepare("
        DELETE FROM blog_posts 
        WHERE post_id = ? AND user_id = ?
    ");
    
    if ($stmt->execute([$post_id, $user_id])) {
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Blog post deleted successfully!";
        } else {
            $_SESSION['error'] = "Post not found or access denied.";
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to delete blog post.";
}

header('Location: index.php');
exit();
