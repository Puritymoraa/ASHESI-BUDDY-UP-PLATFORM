<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$faculty = null;
$recent_activities = [];
$mentee_count = 0;

try {
    // First get faculty_id from Faculty_Details
    $stmt = $pdo->prepare("
        SELECT 
            f.faculty_id,
            f.full_name,
            f.department,
            f.max_mentees,
            u.email
        FROM faculty_details f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faculty) {
        // Get recent activities
        $stmt = $pdo->prepare("
            SELECT * FROM activities 
            WHERE created_by = ? 
            ORDER BY creation_date DESC 
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get mentee count using faculty_id
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM faculty_mentees 
            WHERE faculty_id = ?
        ");
        $stmt->execute([$faculty['faculty_id']]);
        $mentee_count = $stmt->fetchColumn();
    } else {
        $_SESSION['error'] = "Faculty profile not found.";
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load dashboard data.";
}

include '../includes/header.php';
include '../includes/faculty_sidebar.php';
?>

<div class="main-content">
    <div class="dashboard-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($faculty['full_name'] ?? 'Professor'); ?> 👋</h1>
                <p>Here's what's happening with your mentees and activities</p>
            </div>
            <div class="date-time">
                <span class="date"><?php echo date('l, F j, Y'); ?></span>
                <span class="time" id="current-time"></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Current Mentees</h3>
                    <p class="stat-number"><?php echo $mentee_count; ?>/<?php echo htmlspecialchars($faculty['max_mentees'] ?? '5'); ?></p>
                    <span class="stat-label">Buddy Pairs</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3>Activities Created</h3>
                    <p class="stat-number"><?php echo count($recent_activities); ?></p>
                    <span class="stat-label">This Semester</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-info">
                    <h3>Department</h3>
                    <p class="stat-text"><?php echo htmlspecialchars($faculty['department'] ?? 'Not Set'); ?></p>
                </div>
            </div>
        </div>

        <div class="section-grid">
            <!-- Recent Activities Section -->
            <div class="dashboard-section activities-section">
                <div class="section-header">
                    <h2>Recent Activities</h2>
                    <a href="activities/create.php" class="btn-create">
                        <i class="fas fa-plus"></i> Create Activity
                    </a>
                </div>
                <div class="activities-list">
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-card">
                                <div class="activity-info">
                                    <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                </div>
                                <div class="activity-meta">
                                    <span class="semester"><?php echo htmlspecialchars($activity['semester']); ?></span>
                                    <span class="date"><?php echo date('M j, Y', strtotime($activity['creation_date'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <p>No activities created yet</p>
                            <a href="activities/create.php" class="btn-create">Create Your First Activity</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="dashboard-section quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="activities/create.php" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>New Activity</span>
                    </a>
                    <a href="mentees/select.php" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Mentees</span>
                    </a>
                    <a href="profile/index.php" class="action-card">
                        <i class="fas fa-user-edit"></i>
                        <span>Update Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --darker-bg: #1a1a1a;
    --dark-bg: #242424;
    --card-bg: #2a2a2a;
    --hover-bg: rgba(201, 123, 20, 0.1);
}

.main-content {
    margin-left: 40px;
    transition: margin-left 0.3s ease;
    min-height: 100vh;
    background: var(--darker-bg);
    padding: 2rem;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

.welcome-section {
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-text h1 {
    color: var(--white);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.welcome-text p {
    color: var(--text-gray);
}

.date-time {
    text-align: right;
    color: var(--text-gray);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--dark-bg);
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    background: var(--accent-orange);
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
}

.stat-info h3 {
    color: var(--text-gray);
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.stat-number {
    color: var(--white);
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.section-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.dashboard-section {
    background: var(--dark-bg);
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h2 {
    color: var(--white);
}

.btn-create {
    background: var(--accent-orange);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-create:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-2px);
}

.activity-card {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.activity-card:hover {
    transform: translateX(5px);
}

.activity-info h3 {
    color: var(--white);
    margin-bottom: 0.5rem;
}

.activity-info p {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.activity-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    color: var(--text-gray);
    font-size: 0.8rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.action-card {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    color: var(--white);
    text-decoration: none;
    transition: all 0.3s ease;
}

.action-card:hover {
    transform: translateY(-5px);
    background: var(--accent-orange);
}

.action-card i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    display: block;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--text-gray);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

@media (max-width: 1024px) {
    .section-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .welcome-section {
        flex-direction: column;
        text-align: center;
    }

    .date-time {
        text-align: center;
        margin-top: 1rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function updateTime() {
    const timeElement = document.getElementById('current-time');
    const now = new Date();
    timeElement.textContent = now.toLocaleTimeString();
}

// Update sidebar interaction
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebar.addEventListener('mouseenter', function() {
        mainContent.style.marginLeft = 'var(--sidebar-width)';
    });

    sidebar.addEventListener('mouseleave', function() {
        mainContent.style.marginLeft = '40px';
    });
});

setInterval(updateTime, 1000);
updateTime();
</script>

<?php include '../includes/faculty_footer.php'; ?> 