<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing', 'Freshman']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = $_POST['content']; // Will be sanitized when displaying

    try {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (user_id, title, content, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        
        if ($stmt->execute([$_SESSION['user_id'], $title, $content])) {
            $_SESSION['success'] = "Blog post created successfully!";
            header('Location: index.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to create blog post.";
    }
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
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="create-post-form">
            <h2>Create New Blog Post</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="Enter your blog post title">
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="12" required
                              placeholder="Write your blog post content here..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Publish Post
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
}

.create-post-form {
    background: var(--dark-gray);
    padding: 2rem;
    border-radius: 15px;
    margin-top: 1rem;
}

.create-post-form h2 {
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
}

.form-actions {
    margin-top: 2rem;
    display: flex;
    justify-content: flex-end;
}

textarea {
    resize: vertical;
    min-height: 200px;
    font-family: inherit;
}

input::placeholder,
textarea::placeholder {
    color: rgba(255, 255, 255, 0.5);
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
    .create-post-form {
        padding: 1.5rem;
    }

    .form-actions {
        margin-top: 1.5rem;
    }

    textarea {
        min-height: 150px;
    }
}
</style>

<?php include '../../includes/footer.php'; ?> 