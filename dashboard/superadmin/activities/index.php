<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

try {
    // Get all activities with creator details
    $stmt = $pdo->prepare("
        SELECT a.*, 
               CASE 
                   WHEN u.role = 'Continuing' THEN cs.full_name
                   WHEN u.role = 'Faculty' THEN f.full_name
                   WHEN u.role = 'SuperAdmin' THEN sa.full_name
               END as creator_name,
               u.role as creator_role
        FROM activities a
        JOIN users u ON a.created_by = u.user_id
        LEFT JOIN continuing_student_details cs ON u.user_id = cs.user_id AND u.role = 'Continuing'
        LEFT JOIN faculty_details f ON u.user_id = f.user_id AND u.role = 'Faculty'
        LEFT JOIN superadmin_details sa ON u.user_id = sa.user_id AND u.role = 'SuperAdmin'
        ORDER BY a.creation_date DESC
    ");
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load activities: " . $e->getMessage();
    $activities = []; // Initialize as empty array if query fails
}

$current_page = 'activities';
include '../../includes/header.php';
include '../../includes/superadmin_sidebar.php';
include '../../includes/back_button.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="header-content">
                <h1>Manage Activities</h1>
                <button class="btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add New Activity
                </button>
            </div>
            <p class="subtitle">View and manage all activities in the system</p>
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
                        <th>Activity Name</th>
                        <th>Semester</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Creation Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                            <td><?php echo htmlspecialchars($activity['semester']); ?></td>
                            <td class="description-cell">
                                <?php echo htmlspecialchars($activity['description']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($activity['creator_name']); ?>
                                <small>(<?php echo $activity['creator_role']; ?>)</small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($activity['creation_date'])); ?></td>
                            <td class="actions">
                                <button onclick="openEditModal(<?php echo $activity['activity_id']; ?>)" 
                                        class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $activity['activity_id']; ?>)" 
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
<div id="activityModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Add New Activity</h2>
        <form id="activityForm" action="save_activity.php" method="POST">
            <input type="hidden" name="activity_id" id="edit_activity_id">
            <div class="form-group">
                <label for="activity_name">Activity Name</label>
                <input type="text" id="edit_activity_name" name="activity_name" required>
            </div>
            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="text" id="edit_semester" name="semester" required 
                       placeholder="e.g., 2023/2024">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="edit_description" name="description" required rows="4"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Activity</button>
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
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

.description-cell {
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

.modal-content {
    background: var(--dark-bg);
    margin: 5% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid var(--border-color);
    background: var(--medium-bg);
    color: var(--text-primary);
    border-radius: 4px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-primary,
.btn-secondary {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--accent-orange);
    color: white;
}

.btn-secondary {
    background: var(--medium-bg);
    color: var(--text-primary);
}

.btn-primary:hover {
    background: var(--accent-orange-dark);
}

.btn-secondary:hover {
    background: var(--light-bg);
}

/* Alert Styles */
.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .data-table th,
    .data-table td {
        padding: 0.75rem;
    }

    .description-cell {
        max-width: 200px;
    }

    .modal-content {
        width: 95%;
        margin: 2% auto;
        padding: 1.5rem;
    }
}
</style>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Activity';
    document.getElementById('activityForm').reset();
    document.getElementById('edit_activity_id').value = '';
    document.getElementById('activityModal').style.display = 'block';
}

function openEditModal(activityId) {
    document.getElementById('modalTitle').textContent = 'Edit Activity';
    fetch(`get_activity.php?activity_id=${activityId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_activity_id').value = data.activity_id;
            document.getElementById('edit_activity_name').value = data.activity_name;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_semester').value = data.semester;
            document.getElementById('activityModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load activity details');
        });
}

function closeModal() {
    document.getElementById('activityModal').style.display = 'none';
}

function confirmDelete(activityId) {
    if (confirm('Are you sure you want to delete this activity?')) {
        window.location.href = `delete_activity.php?activity_id=${activityId}`;
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('activityModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../../includes/superadmin_footer.php'; ?> 