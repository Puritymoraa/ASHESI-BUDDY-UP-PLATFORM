<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

header('Content-Type: application/json');

if (!isset($_GET['post_id'])) {
    echo json_encode([
        'error' => 'Blog post ID not provided',
        'status' => 'error',
        'message' => 'Please provide a valid blog post ID'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            post_id,
            title,
            REPLACE(content, '<br />', '\n') as content,
            DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at,
            DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at
        FROM blog_posts 
        WHERE post_id = ?
    ");
    $stmt->execute([$_GET['post_id']]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($blog) {
        // Format content for display
        $blog['content'] = strip_tags($blog['content']); // Remove HTML tags
        echo json_encode([
            'status' => 'success',
            'data' => $blog
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'error' => 'Blog post not found',
            'message' => 'The requested blog post could not be found'
        ]);
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'error' => 'Database error occurred',
        'message' => 'An error occurred while fetching the blog post'
    ]);
}
?> 