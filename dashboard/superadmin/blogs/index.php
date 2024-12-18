<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

try {
    // Get all blog posts with creator details
    $stmt = $pdo->prepare("
        SELECT b.*, 
               CASE 
                   WHEN u.role = 'Continuing' THEN cs.full_name
                   WHEN u.role = 'Faculty' THEN f.full_name
                   WHEN u.role = 'SuperAdmin' THEN sa.full_name
               END as creator_name,
               u.role as creator_role
        FROM blog_posts b
        JOIN users u ON b.user_id = u.user_id
        LEFT JOIN continuing_student_details cs ON u.user_id = cs.user_id AND u.role = 'Continuing'
        LEFT JOIN faculty_details f ON u.user_id = f.user_id AND u.role = 'Faculty'
        LEFT JOIN superadmin_details sa ON u.user_id = sa.user_id AND u.role = 'SuperAdmin'
        ORDER BY b.created_at DESC
    ");
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load blog posts: " . $e->getMessage();
    $blogs = []; // Initialize as empty array if query fails
}

$current_page = 'blogs';
include '../../includes/header.php';
include '../../includes/superadmin_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="header-content">
                <h1>Manage Blog Posts</h1>
                <button class="btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add New Post
                </button>
            </div>
            <p class="subtitle">View and manage all blog posts in the system</p>
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

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $blog): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                            <td class="content-preview">
                                <?php echo htmlspecialchars(substr($blog['content'], 0, 100)) . '...'; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($blog['creator_name']); ?>
                                <small>(<?php echo $blog['creator_role']; ?>)</small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($blog['updated_at'])); ?></td>
                            <td class="actions">
                                <button onclick="openEditModal(<?php echo $blog['post_id']; ?>)" 
                                        class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $blog['post_id']; ?>)" 
                                        class="btn-icon delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="blogModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Blog Post</h2>
            <span class="close">&times;</span>
        </div>
        <form id="blogForm" action="save_blog.php" method="POST">
            <input type="hidden" name="post_id" id="edit_post_id">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="edit_title" name="title" required 
                       class="form-control" placeholder="Enter blog title">
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="edit_content" name="content" required 
                          class="form-control" rows="12" 
                          placeholder="Write your blog content here..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Post
                </button>
                <button type="button" class="btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Copy all styles from activities/index.php */
.content-wrapper {
    padding: 2rem;
}

.page-header {
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.page-header h1 {
    color: var(--accent-orange);
    font-size: 2rem;
    margin: 0;
}

.subtitle {
    color: var(--text-secondary);
}

/* Table styles */
.table-container {
    background: var(--dark-bg);
    border-radius: 10px;
    padding: 1rem;
    overflow-x: auto;
    margin-top: 1rem;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
}

.content-preview {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    background: none;
    border: none;
    color: var(--text-primary);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.btn-icon:hover {
    background: var(--medium-bg);
}

.btn-icon.delete:hover {
    color: var(--error-color);
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

/* Enhanced Modal Styles */
.modal-content {
    background: var(--dark-bg);
    margin: 3% auto;
    padding: 0;
    border-radius: 12px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-header {
    background: var(--medium-bg);
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
}

.modal-header h2 {
    margin: 0;
    color: var(--accent-orange);
    font-size: 1.5rem;
}

.close {
    color: var(--text-secondary);
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: var(--error-color);
}

#blogForm {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--medium-bg);
    color: var(--text-primary);
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent-orange);
    box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
}

textarea.form-control {
    resize: vertical;
    min-height: 200px;
    font-family: inherit;
    line-height: 1.5;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.btn-primary, .btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--accent-orange);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--medium-bg);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--light-bg);
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }

    .modal-header {
        padding: 1rem;
    }

    #blogForm {
        padding: 1rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

/* Rest of the styles from activities/index.php */
</style>

<script>
// Update JavaScript to match activities pattern
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Blog Post';
    document.getElementById('blogForm').reset();
    document.getElementById('edit_post_id').value = '';
    document.getElementById('blogModal').style.display = 'block';
}

function openEditModal(postId) {
    document.getElementById('modalTitle').textContent = 'Edit Blog Post';
    fetch(`get_blog.php?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_post_id').value = data.post_id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_content').value = data.content;
            document.getElementById('blogModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load blog post details');
        });
}

function closeModal() {
    document.getElementById('blogModal').style.display = 'none';
}

function confirmDelete(postId) {
    if (confirm('Are you sure you want to delete this blog post?')) {
        window.location.href = `delete_blog.php?post_id=${postId}`;
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('blogModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../../includes/superadmin_footer.php'; ?> 