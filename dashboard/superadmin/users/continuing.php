<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

try {
    // Get all continuing students with their details
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.email, u.registration_date, 
               c.full_name, c.student_id, c.major,
               (SELECT COUNT(*) FROM Mentorship m WHERE m.continuing_id = u.user_id AND m.status = 'Active') as active_mentees
        FROM users u
        JOIN continuing_student_details c ON u.user_id = c.user_id
        WHERE u.role = 'Continuing'
        ORDER BY c.full_name ASC
    ");
    $stmt->execute();
    $continuing = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load continuing student data: " . $e->getMessage();
    $continuing = []; // Initialize as empty array if query fails
}

$current_page = 'continuing';
include '../../includes/header.php';
include '../../includes/superadmin_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <h1>Manage Continuing Students</h1>
            <p class="subtitle">View and manage continuing student accounts</p>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                        <th>Major</th>
                        <th>Active Mentees</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($continuing as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['major']); ?></td>
                            <td><?php echo htmlspecialchars($student['active_mentees']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($student['registration_date'])); ?></td>
                            <td class="actions">
                                <button onclick="openEditModal(<?php echo $student['user_id']; ?>)" 
                                        class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $student['user_id']; ?>)" 
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

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Continuing Student Details</h2>
        <form id="editForm" action="update_user.php" method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="edit_full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="edit_student_id" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="major">Major</label>
                <input type="text" id="edit_major" name="major" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Changes</button>
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

.page-header h1 {
    color: var(--accent-orange);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: var(--text-secondary);
}

.table-container {
    background: var(--dark-bg);
    border-radius: 10px;
    padding: 1rem;
    overflow-x: auto;
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
    color: var(--accent-orange);
    background: var(--medium-bg);
}

.btn-icon.delete:hover {
    color: #dc3545;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: var(--dark-bg);
    margin: 10% auto;
    padding: 2rem;
    width: 90%;
    max-width: 500px;
    border-radius: 10px;
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

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid var(--border-color);
    background: var(--medium-bg);
    color: var(--text-primary);
    border-radius: 4px;
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
    background: #28a745;
    color: white;
}

.alert-error {
    background: #dc3545;
    color: white;
}
</style>

<script>
function openEditModal(userId) {
    fetch(`get_user_details.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_user_id').value = data.user_id;
            document.getElementById('edit_full_name').value = data.full_name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_student_id').value = data.student_id;
            document.getElementById('edit_major').value = data.major;
            document.getElementById('editModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user? This will also remove all their mentorship relationships.')) {
        window.location.href = `delete_user.php?user_id=${userId}&role=Continuing`;
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../../includes/superadmin_footer.php'; ?> 