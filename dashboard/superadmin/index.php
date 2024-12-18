<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['SuperAdmin']);

// Get statistics
try {
    // Count users by role
    $stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $stmt->execute();
    $user_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count active mentorships
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM mentorship WHERE status = 'Active'");
    $stmt->execute();
    $mentorship_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Count activities
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities");
    $stmt->execute();
    $activities_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Count blog posts
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM blog_posts");
    $stmt->execute();
    $blog_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load dashboard data.";
}

$current_page = 'dashboard'; // For sidebar active state
include '../includes/header.php';
include '../includes/superadmin_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="dashboard-header">
            <h1>SuperAdmin Dashboard</h1>
            <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
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

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <?php foreach ($user_stats as $stat): ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <?php
                        $icon = match($stat['role']) {
                            'Freshman' => 'fa-user-graduate',
                            'Continuing' => 'fa-users',
                            'Faculty' => 'fa-chalkboard-teacher',
                            default => 'fa-user'
                        };
                        ?>
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stat['count']; ?></h3>
                        <p><?php echo $stat['role']; ?> Users</p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $mentorship_count; ?></h3>
                    <p>Active Mentorships</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $activities_count; ?></h3>
                    <p>Activities</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-blog"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $blog_count; ?></h3>
                    <p>Blog Posts</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="users/freshmen.php" class="action-card">
                    <i class="fas fa-user-graduate"></i>
                    <span>Manage Freshmen</span>
                </a>
                <a href="users/continuing.php" class="action-card">
                    <i class="fas fa-users"></i>
                    <span>Manage Continuing</span>
                </a>
                <a href="users/faculty.php" class="action-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Manage Faculty</span>
                </a>
                <a href="activities/index.php" class="action-card">
                    <i class="fas fa-tasks"></i>
                    <span>Manage Activities</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    padding: 2rem;
    margin-left: var(--sidebar-width);
}

.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    color: var(--accent-orange);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.welcome-text {
    color: var(--text-secondary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--dark-bg);
    border-radius: 10px;
    padding: 1.5rem;
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
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
}

.stat-info h3 {
    font-size: 1.8rem;
    color: var(--white);
    margin-bottom: 0.3rem;
}

.stat-info p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.quick-actions {
    margin-top: 3rem;
}

.quick-actions h2 {
    color: var(--white);
    margin-bottom: 1.5rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.action-card {
    background: var(--dark-bg);
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    text-decoration: none;
    color: var(--white);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.action-card:hover {
    background: var(--medium-bg);
    transform: translateY(-5px);
}

.action-card i {
    font-size: 2rem;
    color: var(--accent-orange);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid #28a745;
    color: #28a745;
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid #dc3545;
    color: #dc3545;
}
</style>

<?php include '../includes/superadmin_footer.php'; ?>