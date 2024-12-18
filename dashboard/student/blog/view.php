<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing', 'Freshman']);

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Get post details with author information
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
        WHERE bp.post_id = ?
    ");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['error'] = "Post not found.";
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load blog post.";
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="navigation-buttons">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Blog Posts
            </a>
            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                <div class="post-actions">
                    <a href="edit.php?id=<?php echo $post['post_id']; ?>" class="btn btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button onclick="deleteBlogPost(<?php echo $post['post_id']; ?>)" class="btn btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <article class="blog-post">
            <header class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span class="author">
                        <i class="fas fa-user"></i> 
                        <?php echo htmlspecialchars($post['author_name']); ?>
                    </span>
                    <span class="date">
                        <i class="fas fa-calendar"></i>
                        <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                    </span>
                    <?php if ($post['updated_at'] > $post['created_at']): ?>
                        <span class="updated">
                            <i class="fas fa-edit"></i>
                            Updated: <?php echo date('M d, Y', strtotime($post['updated_at'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </article>
    </div>
</div>

<style>
.content-wrapper {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.post-actions {
    display: flex;
    gap: 1rem;
}

.blog-post {
    background: var(--dark-gray);
    padding: 2.5rem;
    border-radius: 15px;
    margin-top: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.post-header {
    margin-bottom: 2.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 1.5rem;
}

.post-header h1 {
    color: var(--accent-orange);
    font-size: 2.5rem;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    color: var(--white);
    opacity: 0.8;
    font-size: 0.9rem;
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.post-content {
    color: var(--white);
    line-height: 1.8;
    font-size: 1.1rem;
    white-space: pre-wrap;
}

.post-actions {
    display: flex;
    gap: 1rem;
}

.post-meta i {
    color: var(--accent-orange);
    opacity: 0.8;
}

@media (max-width: 768px) {
    .blog-post {
        padding: 1.5rem;
    }

    .post-header h1 {
        font-size: 2rem;
    }

    .post-meta {
        flex-direction: column;
        gap: 0.8rem;
    }

    .post-content {
        font-size: 1rem;
        line-height: 1.6;
    }

    .post-actions {
        flex-direction: column;
        width: 100%;
    }

    .post-actions .btn {
        width: 100%;
        justify-content: center;
    }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.blog-post {
    animation: fadeIn 0.5s ease-out;
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