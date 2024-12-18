<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];

try {
    // Get faculty details including avatar
    $stmt = $pdo->prepare("
        SELECT 
            f.faculty_id,
            f.full_name,
            f.department,
            f.avatar_url
        FROM Faculty_Details f
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get all activities created by this faculty member
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            (SELECT COUNT(*) FROM activity_completions ac WHERE ac.activity_id = a.activity_id) as completion_count
        FROM activities a
        WHERE a.created_by = ?
        ORDER BY a.creation_date DESC
    ");
    $stmt->execute([$user_id]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load activities.";
}

include '../../includes/header.php';
include '../../includes/faculty_sidebar.php';
?>

<div class="main-content">
    <div class="activities-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Activities Management</h1>
                <p>Create and manage learning activities for your mentees</p>
            </div>
            <a href="create.php" class="btn-create">
                <i class="fas fa-plus"></i>
                Create New Activity
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

        <!-- Activities List -->
        <div class="activities-grid">
            <?php if (!empty($activities)): ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-card">
                        <div class="activity-header">
                            <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
                            <div class="activity-actions">
                                <a href="edit.php?id=<?php echo $activity['activity_id']; ?>" class="btn-edit" title="Edit Activity">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete(<?php echo $activity['activity_id']; ?>)" class="btn-delete" title="Delete Activity">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="activity-body">
                            <p class="description"><?php echo htmlspecialchars($activity['description']); ?></p>
                            <div class="activity-meta">
                                <span class="semester">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo htmlspecialchars($activity['semester']); ?>
                                </span>
                                <span class="completions">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo $activity['completion_count']; ?> Completions
                                </span>
                            </div>
                        </div>
                        
                        <div class="activity-footer">
                            <span class="date">Created: <?php echo date('M j, Y', strtotime($activity['creation_date'])); ?></span>
                            <a href="view.php?id=<?php echo $activity['activity_id']; ?>" class="btn-view">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tasks"></i>
                    <h2>No Activities Yet</h2>
                    <p>Start creating learning activities for your mentees</p>
                    <a href="create.php" class="btn-create">
                        <i class="fas fa-plus"></i> Create Your First Activity
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
:root {
    --darker-bg: #121212;
    --dark-bg: #1E1E1E;
    --card-bg: #252525;
    --border-color: #333333;
    --text-gray: #B3B3B3;
    --white: #FFFFFF;
    --accent-orange: #C97B14;
    --accent-orange-dark: #A66610;
    --error-red: #dc3545;
    --success-green: #28a745;
    --header-height: 60px;
}

.main-content {
    padding-top: calc(var(--header-height) + 2rem);
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    background: var(--darker-bg);
    padding: 2rem;
}

.activities-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.header-content h1 {
    color: var(--white);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.header-content p {
    color: var(--text-gray);
}

.btn-create {
    background: var(--accent-orange);
    color: var(--white);
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-create:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-weight: 500;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: var(--success-green);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: var(--error-red);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.activities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.activity-card {
    background: var(--dark-bg);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.activity-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent-orange);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.activity-header h3 {
    color: var(--white);
    font-size: 1.2rem;
    margin: 0;
}

.activity-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-edit, .btn-delete {
    background: none;
    border: none;
    color: var(--text-gray);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    color: var(--accent-orange);
    transform: translateY(-2px);
}

.btn-delete:hover {
    color: var(--error-red);
    transform: translateY(-2px);
}

.activity-body {
    flex: 1;
}

.description {
    color: var(--text-gray);
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.semester, .completions {
    color: var(--text-gray);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.date {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.btn-view {
    color: var(--accent-orange);
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-view:hover {
    color: var(--white);
    transform: translateX(5px);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: var(--dark-bg);
    border-radius: 15px;
    border: 1px solid var(--border-color);
}

.empty-state i {
    font-size: 3rem;
    color: var(--accent-orange);
    margin-bottom: 1rem;
}

.empty-state h2 {
    color: var(--white);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-gray);
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .activities-grid {
        grid-template-columns: 1fr;
    }

    .activity-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<script>
function confirmDelete(activityId) {
    if (confirm('Are you sure you want to delete this activity? This action cannot be undone.')) {
        window.location.href = `delete.php?id=${activityId}`;
    }
}
</script>

<?php include '../../includes/faculty_footer.php'; ?> 