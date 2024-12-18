<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing', 'Freshman']);

$user_id = $_SESSION['user_id'];

try {
    // Get all blog posts with author details
    $stmt = $pdo->prepare("
        SELECT 
            bp.*,
            CASE 
                WHEN cs.full_name IS NOT NULL THEN cs.full_name
                WHEN fs.full_name IS NOT NULL THEN fs.full_name
            END as author_name
        FROM blog_posts bp
        LEFT JOIN continuing_student_details cs ON bp.user_id = cs.user_id
        LEFT JOIN freshman_details fs ON bp.user_id = fs.user_id
        ORDER BY bp.created_at DESC
    ");
    $stmt->execute();
    $blog_posts = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching blog posts.";
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
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
.main-content {
    padding-top: 80px;
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    background: var(--background);
}

.content-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
    background: var(--dark-gray);
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
    color: var(--white);
    opacity: 0.8;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.blog-preview {
    color: var(--white);
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
    color: var(--white);
}

.btn-edit {
    background: #2196F3;
    color: var(--white);
}

.btn-delete {
    background: #dc3545;
    color: var(--white);
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
    Swal.fire({
        title: 'Are you sure?',
        text: "This blog post will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${postId}`;
        }
    });
}
</script>

<?php include '../../includes/footer.php'; ?> 