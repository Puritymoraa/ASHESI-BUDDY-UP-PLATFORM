<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Continuing']);

$user_id = $_SESSION['user_id'];
$user_details = [];
$mentee_count = 0;
$recent_activities = [];
$recent_blogs = [];

try {
    // Fetch user details
    $stmt = $pdo->prepare("
        SELECT c.full_name, c.avatar_url, c.student_id, c.major, c.nationality, c.hobby, c.fun_fact, u.email
        FROM users u
        JOIN continuing_student_details c ON u.user_id = c.user_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_details) {
        throw new Exception("User details not found.");
    }

    // Fetch mentee count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as mentee_count 
        FROM mentorship 
        WHERE continuing_id = ? AND status = 'active'
    ");
    $stmt->execute([$user_id]);
    $mentee_count = $stmt->fetchColumn();

    // Fetch recent activities
    $stmt = $pdo->prepare("
        SELECT a.activity_id, a.activity_name, a.description, ca.completion_date
        FROM activities a
        LEFT JOIN activity_completions ca ON a.activity_id = ca.activity_id AND ca.mentorship_id = ?
        ORDER BY a.creation_date DESC LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent blog posts
    $stmt = $pdo->prepare("
        SELECT b.post_id, b.title, b.content, b.created_at
        FROM blog_posts b
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $recent_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while loading your dashboard.";
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="dashboard-welcome">
        <h1>Welcome, <?php echo htmlspecialchars($user_details['full_name'] ?? 'Student'); ?>!</h1>
        <p class="date"><?php echo date('l, F j, Y'); ?></p>
    </div>

    <div class="dashboard-grid">
        <!-- Mentee Overview Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Mentee Overview</h2>
                <a href="mentees/" class="view-all">Manage Mentees</a>
            </div>
            <div class="card-content">
                <div class="mentee-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $mentee_count; ?>/3</span>
                        <span class="stat-label">Current Mentees</span>
                    </div>
                    <?php if ($mentee_count < 3): ?>
                        <a href="mentees/" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add Mentee
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Activities Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Recent Activities</h2>
                <a href="activities/" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (!empty($recent_activities)): ?>
                    <div class="activity-list">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-info">
                                    <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                </div>
                                <?php if ($activity['completion_date']): ?>
                                    <span class="status completed">Completed</span>
                                <?php else: ?>
                                    <a href="activities/index.php?id=<?php echo $activity['activity_id']; ?>" class="btn btn-secondary">Complete</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">No activities available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Blog Posts Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Your Blog Posts</h2>
                <a href="blog/" class="view-all">Manage Blog</a>
            </div>
            <div class="card-content">
                <?php if (!empty($recent_blogs)): ?>
                    <div class="blog-list">
                        <?php foreach ($recent_blogs as $blog): ?>
                            <div class="blog-item">
                                <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <p><?php echo substr(htmlspecialchars($blog['content']), 0, 100) . '...'; ?></p>
                                <div class="blog-meta">
                                    <span class="date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M j, Y', strtotime($blog['created_at'])); ?>
                                    </span>
                                    <a href="blog/edit.php?id=<?php echo $blog['post_id']; ?>" 
                                       class="btn btn-small">Edit</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">No blog posts yet</p>
                <?php endif; ?>
                <a href="blog/create.php" class="btn btn-primary create-blog">
                    <i class="fas fa-plus"></i> Create New Blog Post
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .main-content {
        margin-left: var(--sidebar-width);
        padding: 2rem;
        padding-top: 90px;
        min-height: 100vh;
    }

    .dashboard-welcome {
        margin-bottom: 2rem;
    }

    .dashboard-welcome h1 {
        color: var(--accent-orange);
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }

    .date {
        color: var(--white);
        opacity: 0.8;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .dashboard-card {
        background: var(--dark-gray);
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-header h2 {
        color: var(--accent-orange);
        font-size: 1.2rem;
    }

    .view-all {
        color: var(--white);
        text-decoration: none;
        font-size: 0.9rem;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .view-all:hover {
        opacity: 1;
    }

    .card-content {
        padding: 1.5rem;
    }

    .mentee-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2rem;
        color: var(--accent-orange);
        display: block;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .activity-list, .blog-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-item, .blog-item {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
    }

    .blog-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: var(--accent-orange);
        color: var(--white);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: var(--white);
    }

    .btn-small {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }

    .create-blog {
        margin-top: 1rem;
        width: 100%;
        justify-content: center;
    }
</style>

<?php include '../includes/footer.php'; ?>
