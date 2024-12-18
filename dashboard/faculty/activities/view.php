<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$activity_id = $_GET['id'] ?? null;
$error = null;
$activity = null;
$completions = [];

// Get faculty details for header
try {
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

    // Get activity details
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            (SELECT COUNT(*) FROM activity_completions ac WHERE ac.activity_id = a.activity_id) as completion_count
        FROM activities a
        WHERE a.activity_id = ? AND a.created_by = ?
    ");
    $stmt->execute([$activity_id, $user_id]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activity) {
        $_SESSION['error'] = "Activity not found or access denied.";
        header('Location: index.php');
        exit;
    }

    // Get completion details
    $stmt = $pdo->prepare("
        SELECT 
            ac.*,
            s.full_name as student_name,
            s.student_id
        FROM activity_completions ac
        JOIN freshman_details s ON ac.mentorship_id = s.freshman_id
        WHERE ac.activity_id = ?
        ORDER BY ac.completion_date DESC
    ");
    $stmt->execute([$activity_id]);
    $completions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load activity details.";
    header('Location: index.php');
    exit;
}

include '../../includes/header.php';
include '../../includes/faculty_sidebar.php';
?>

<div class="main-content">
    <div class="view-activity-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Activity Details</h1>
                <p>View detailed information about this activity</p>
            </div>
            <div class="header-actions">
                <a href="edit.php?id=<?php echo $activity_id; ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit Activity
                </a>
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Activities
                </a>
            </div>
        </div>

        <!-- Activity Details Card -->
        <div class="activity-detail-card">
            <div class="activity-header">
                <h2><?php echo htmlspecialchars($activity['activity_name']); ?></h2>
                <span class="semester-badge"><?php echo htmlspecialchars($activity['semester']); ?></span>
            </div>
            
            <div class="activity-stats">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span class="stat-value"><?php echo $activity['completion_count']; ?></span>
                    <span class="stat-label">Completions</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-calendar"></i>
                    <span class="stat-value"><?php echo date('M j, Y', strtotime($activity['creation_date'])); ?></span>
                    <span class="stat-label">Created</span>
                </div>
            </div>

            <div class="activity-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($activity['description'])); ?></p>
            </div>

            <!-- Completions Section -->
            <div class="completions-section">
                <h3>Activity Completions</h3>
                <?php if (!empty($completions)): ?>
                    <div class="completions-list">
                        <?php foreach ($completions as $completion): ?>
                            <div class="completion-item">
                                <div class="student-info">
                                    <span class="student-name">
                                        <?php echo htmlspecialchars($completion['student_name']); ?>
                                    </span>
                                    <span class="student-id">
                                        ID: <?php echo htmlspecialchars($completion['student_id']); ?>
                                    </span>
                                </div>
                                <div class="completion-date">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo date('M j, Y', strtotime($completion['completion_date'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-clipboard-check"></i>
                        <p>No completions recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
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

.view-activity-container {
    max-width: 1000px;
    margin: 0 auto;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.activity-detail-card {
    background: var(--dark-bg);
    border-radius: 15px;
    padding: 2rem;
    margin-top: 2rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.activity-header h2 {
    color: var(--white);
    font-size: 1.8rem;
    margin: 0;
}

.semester-badge {
    background: var(--accent-orange);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.activity-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--card-bg);
    border-radius: 10px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-item i {
    font-size: 1.5rem;
    color: var(--accent-orange);
    margin-bottom: 0.5rem;
}

.stat-value {
    color: var(--white);
    font-size: 1.2rem;
    font-weight: 500;
}

.stat-label {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.activity-description {
    margin-bottom: 2rem;
}

.activity-description h3 {
    color: var(--white);
    margin-bottom: 1rem;
}

.activity-description p {
    color: var(--text-gray);
    line-height: 1.6;
}

.completions-section h3 {
    color: var(--white);
    margin-bottom: 1rem;
}

.completions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.completion-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--card-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.student-info {
    display: flex;
    flex-direction: column;
}

.student-name {
    color: var(--white);
    font-weight: 500;
}

.student-id {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.completion-date {
    color: var(--text-gray);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.completion-date i {
    color: var(--success-green);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--text-gray);
}

.empty-state i {
    font-size: 2rem;
    color: var(--accent-orange);
    margin-bottom: 1rem;
}

.btn-edit, .btn-back {
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-edit {
    background: var(--accent-orange);
    color: var(--white);
}

.btn-edit:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-2px);
}

.btn-back {
    background: var(--card-bg);
    color: var(--text-gray);
    border: 1px solid var(--border-color);
}

.btn-back:hover {
    color: var(--white);
    border-color: var(--accent-orange);
}

@media (max-width: 768px) {
    .activity-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .btn-edit, .btn-back {
        width: 100%;
        justify-content: center;
    }

    .completion-item {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<?php include '../../includes/faculty_footer.php'; ?> 