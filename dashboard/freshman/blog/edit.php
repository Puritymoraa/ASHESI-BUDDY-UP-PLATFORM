<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Freshman']);

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['user_id'];

try {
    // Get post details and verify ownership
    $stmt = $pdo->prepare("
        SELECT * FROM blog_posts 
        WHERE post_id = ? AND user_id = ?
    ");
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['error'] = "Post not found or access denied.";
        header('Location: index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'];

        $stmt = $pdo->prepare("
            UPDATE blog_posts 
            SET title = ?, content = ?, updated_at = NOW()
            WHERE post_id = ? AND user_id = ?
        ");
        
        if ($stmt->execute([$title, $content, $post_id, $user_id])) {
            $_SESSION['success'] = "Blog post updated successfully!";
            header('Location: index.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to update blog post.";
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
include '../../includes/freshman_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="navigation-buttons">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Blog Posts
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="edit-post-form">
            <h2>Edit Blog Post</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" 
                           value="<?php echo htmlspecialchars($post['title']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="12" required><?php 
                        echo htmlspecialchars($post['content']); 
                    ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    margin-top: 60px;
}

.edit-post-form {
    background: var(--dark-gray);
    padding: 2rem;
    border-radius: 15px;
    margin-top: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.edit-post-form h2 {
    color: var(--accent-orange);
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    color: var(--white);
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--white);
}

.form-group textarea {
    resize: vertical;
    min-height: 200px;
    font-family: inherit;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-primary:active {
    transform: scale(0.95);
}

input:focus,
textarea:focus {
    outline: none;
    border-color: var(--accent-orange);
    box-shadow: 0 0 0 2px rgba(201, 123, 20, 0.2);
}

@media (max-width: 768px) {
    .edit-post-form {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }

    textarea {
        min-height: 150px;
    }
}
</style>

<?php include '../../includes/freshman_footer.php'; ?> 