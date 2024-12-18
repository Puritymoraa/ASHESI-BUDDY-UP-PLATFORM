<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing', 'Freshman', 'Faculty']);

try {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?: 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Updated query with correct table name
    $stmt = $pdo->prepare("
        SELECT 
            ac.completion_id,
            ac.activity_id,
            ac.mentorship_id,
            ac.experience,
            ac.completion_date,
            a.activity_name,
            c.full_name as mentor_name,
            f.full_name as mentee_name
        FROM activity_completions ac
        JOIN activities a ON ac.activity_id = a.activity_id
        JOIN mentorship m ON ac.mentorship_id = m.mentorship_id
        JOIN continuing_student_details c ON m.continuing_id = c.user_id
        JOIN freshman_details f ON m.freshman_id = f.user_id
        ORDER BY ac.completion_date DESC
        LIMIT ? OFFSET ?
    ");

    if (!$stmt->execute([$per_page, $offset])) {
        $error = $stmt->errorInfo();
        throw new Exception("Query failed: " . $error[2]);
    }
    
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count with correct table name
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM activity_completions");
    $total_activities = $count_stmt->fetchColumn();
    $total_pages = ceil($total_activities / $per_page);

} catch (Exception $e) {
    error_log("Error in feed.php: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching the activity feed. Please try again later.";
    $activities = [];
    $total_pages = 0;
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<!-- Add feed display HTML -->
<div class="main-content">
    <div class="content-wrapper">
        <div class="back-section">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Activities
            </a>
        </div>

        <div class="feed-header">
            <h2>Activity Feed</h2>
            <p class="subtitle">See what other mentors and mentees are up to</p>
        </div>

        <div class="feed-container">
            <?php if (count($activities) > 0): ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="feed-card">
                        <div class="feed-card-header">
                            <div class="user-info">
                                <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
                                <p class="participants">
                                    <span class="mentor"><?php echo htmlspecialchars($activity['mentor_name']); ?></span>
                                    <i class="fas fa-arrow-right"></i>
                                    <span class="mentee"><?php echo htmlspecialchars($activity['mentee_name']); ?></span>
                                </p>
                            </div>
                            <div class="date">
                                <?php echo date('M d, Y', strtotime($activity['completion_date'])); ?>
                            </div>
                        </div>
                        <div class="feed-card-body">
                            <p class="experience"><?php echo nl2br(htmlspecialchars($activity['experience'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-activities">No activities have been recorded yet.</p>
            <?php endif; ?>
        </div>

        <!-- Add pagination HTML after feed-container -->
        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>

                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="page-number <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>" class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .main-content {
        padding-top: 80px;
        margin-left: var(--sidebar-width);
        padding-right: 2rem;
        padding-bottom: 2rem;
        min-height: 100vh;
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .back-section {
        margin-bottom: 2rem;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        background: var(--dark-gray);
        color: var(--accent-orange);
        text-decoration: none;
        border: 1px solid var(--accent-orange);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: var(--accent-orange);
        color: var(--white);
        transform: translateY(-2px);
    }

    .feed-header {
        margin: 2rem 0;
    }

    .feed-header h2 {
        color: var(--accent-orange);
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .subtitle {
        color: var(--white);
        opacity: 0.8;
    }

    .feed-container {
        display: grid;
        gap: 1.5rem;
    }

    .feed-card {
        background: var(--dark-gray);
        border-radius: 15px;
        padding: 1.5rem;
    }

    .feed-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

</style>
