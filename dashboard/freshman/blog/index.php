<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Freshman']);

$user_id = $_SESSION['user_id'];

try {
    // Get all blog posts with author details
    $stmt = $pdo->prepare("
        SELECT 
            bp.*,
            f.full_name as author_name
        FROM blog_posts bp
        JOIN freshman_details f ON bp.user_id = f.user_id
        ORDER BY bp.created_at DESC
    ");
    $stmt->execute();
    $blog_posts = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching blog posts.";
}

include '../../includes/header.php';
include '../../includes/freshman_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="navigation-buttons">
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Blog
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="blog-posts">
            <?php if (count($blog_posts) > 0): ?>
                <?php foreach ($blog_posts as $post): ?>
                    <div class="blog-card">
                        <div class="blog-header">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="blog-meta">
                                <span class="author">
                                    <i class="fas fa-user"></i> 
                                    <?php echo htmlspecialchars($post['author_name']); ?>
                                </span>
                                <span class="date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="blog-preview">
                            <?php 
                                $preview = substr(strip_tags($post['content']), 0, 200);
                                echo htmlspecialchars($preview) . '...';
                            ?>
                        </div>
                        <div class="blog-actions">
                            <a href="view.php?id=<?php echo $post['post_id']; ?>" class="btn btn-view">
                                Read More
                            </a>
                            <?php if ($post['user_id'] == $user_id): ?>
                                <a href="edit.php?id=<?php echo $post['post_id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="deleteBlogPost(<?php echo $post['post_id']; ?>)" class="btn btn-delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <p>No blog posts yet. Be the first to create one!</p>
                    <a href="create.php" class="btn btn-primary">Create Blog</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
    margin-top: 60px;
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-primary {
    background: var(--accent-orange);
    color: var(--white);
}

.btn-secondary {
    background: var(--dark-gray);
    color: var(--accent-orange);
    border: 1px solid var(--accent-orange);
}

.blog-posts {
    display: grid;
    gap: 2rem;
}

.blog-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-5px);
}

.blog-header h3 {
    color: var(--accent-orange);
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.blog-meta {
    display: flex;
    gap: 1rem;
    color: var(--text-secondary);
    opacity: 0.8;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.blog-preview {
    color: var(--text-primary);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.blog-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-view {
    background: var(--accent-orange);
    color: var(--text-primary);
}

.btn-edit {
    background: var(--info-blue);
    color: var(--text-primary);
}

.btn-delete {
    background: var(--error-red);
    color: var(--text-primary);
}

.no-posts {
    text-align: center;
    color: var(--white);
    padding: 3rem;
    background: var(--dark-gray);
    border-radius: 15px;
}

@media (max-width: 768px) {
    .navigation-buttons {
        flex-direction: column;
    }
    
    .blog-card {
        padding: 1.5rem;
    }
}
</style>

<script>
function deleteBlogPost(postId) {
    if (confirm('Are you sure you want to delete this blog post?')) {
        window.location.href = `delete.php?id=${postId}`;
    }
}
</script>

<?php include '../../includes/freshman_footer.php'; ?> 