<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Freshman']);

$user_id = $_SESSION['user_id'];
$current_page = 'dashboard';

try {
    // Debug: Print user_id
    // echo "User ID: " . $user_id . "<br>";
    
    // Get freshman details with continuing buddy information
    $stmt = $pdo->prepare("
        SELECT 
            f.*,
            u.email,
            c.full_name as buddy_name,
            c.major as buddy_major,
            c.avatar_url as buddy_avatar,
            c.user_id as continuing_buddy_id
        FROM freshman_details f
        JOIN users u ON f.user_id = u.user_id
        LEFT JOIN continuing_student_details c ON c.user_id = (
            SELECT continuing_id 
            FROM mentorship 
            WHERE freshman_id = f.user_id 
            LIMIT 1
        )
        WHERE f.user_id = ?
    ");
    
    if (!$stmt->execute([$user_id])) {
        // Debug database errors
        $error = $stmt->errorInfo();
        error_log("Database Error: " . print_r($error, true));
        throw new PDOException("Database query failed: " . $error[2]);
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug: Print buddy information
    error_log("Buddy Info: " . print_r([
        'buddy_name' => $user['buddy_name'] ?? 'Not set',
        'buddy_major' => $user['buddy_major'] ?? 'Not set',
        'continuing_buddy_id' => $user['continuing_buddy_id'] ?? 'Not set'
    ], true));

    if (!$user) {
        // Debug: Check if user exists in Users table
        $checkUser = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $checkUser->execute([$user_id]);
        $userExists = $checkUser->fetch();
        
        if (!$userExists) {
            throw new Exception("User not found in Users table");
        } else {
            // Check if user exists in Freshman_Details
            $checkFreshman = $pdo->prepare("SELECT * FROM freshman_details WHERE user_id = ?");
            $checkFreshman->execute([$user_id]);
            $freshmanExists = $checkFreshman->fetch();
            
            if (!$freshmanExists) {
                throw new Exception("User not found in Freshman_Details table");
            }
        }
        
        throw new Exception("User details not found");
    }

    // Get blog post count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as post_count 
        FROM blog_posts 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    $blog_stats = $stmt->fetch();

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

include '../includes/header.php';
include '../includes/freshman_sidebar.php';
?>

<style>
    .main-content {
        padding-top: 80px;
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        background: var(--background);
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .welcome-section {
        text-align: center;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .floating-emoji {
        position: absolute;
        font-size: 2rem;
        animation: float 3s ease-in-out infinite;
        animation-delay: var(--delay);
    }

    .floating-emoji:nth-child(1) { top: 20%; left: 20%; }
    .floating-emoji:nth-child(2) { top: 30%; right: 20%; }
    .floating-emoji:nth-child(3) { bottom: 20%; left: 40%; }

    .welcome-section h1 {
        font-size: 2.5rem;
        color: var(--white);
        margin-bottom: 0.5rem;
    }

    .welcome-section p {
        font-size: 1.2rem;
        color: var(--accent-orange);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .dashboard-card {
        background: var(--dark-gray);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-header i {
        color: var(--accent-orange);
        font-size: 1.5rem;
    }

    .card-header h3 {
        color: var(--white);
        margin: 0;
        font-size: 1.3rem;
    }

    .stats-content {
        display: grid;
        gap: 1rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .stat-item i {
        color: var(--accent-orange);
        font-size: 1.5rem;
    }

    .stat-info h4 {
        color: var(--white);
        margin: 0;
        font-size: 1rem;
    }

    .stat-info p {
        color: var(--accent-orange);
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        color: var(--white);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-3px);
        background: var(--accent-orange);
    }

    .action-btn i {
        font-size: 1.5rem;
    }

    .activity-content {
        display: grid;
        gap: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .activity-item i {
        color: var(--accent-orange);
    }

    .activity-info p {
        color: var(--white);
        margin: 0;
    }

    .activity-info span {
        color: var(--accent-orange);
        font-size: 0.9rem;
    }

    .no-activity {
        color: var(--white);
        text-align: center;
        opacity: 0.7;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(10deg);
        }
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }

        .content-wrapper {
            padding: 1rem;
        }

        .welcome-section {
            padding: 1.5rem;
        }

        .welcome-section h1 {
            font-size: 2rem;
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .buddy-card {
        background: var(--dark-gray);
    }

    .buddy-content {
        padding: 1rem;
    }

    .buddy-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .buddy-avatar {
        flex-shrink: 0;
    }

    .buddy-avatar img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent-orange);
    }

    .buddy-details {
        flex-grow: 1;
    }

    .buddy-details h4 {
        color: var(--white);
        margin: 0 0 0.5rem 0;
        font-size: 1.2rem;
    }

    .buddy-details p {
        color: var(--accent-orange);
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .message-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--accent-orange);
        color: var(--white);
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .message-btn:hover {
        background: var(--white);
        color: var(--accent-orange);
    }

    .no-buddy {
        text-align: center;
        padding: 2rem;
        color: var(--white);
    }

    .no-buddy i {
        font-size: 3rem;
        color: var(--accent-orange);
        margin-bottom: 1rem;
    }

    .no-buddy .sub-text {
        color: var(--accent-orange);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
</style>

<div class="main-content">
    <div class="content-wrapper">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($user)): ?>
            <div class="welcome-section">
                <div class="floating-emoji" style="--delay: 0s">👋</div>
                <div class="floating-emoji" style="--delay: 2s">📚</div>
                <div class="floating-emoji" style="--delay: 4s">✨</div>
                <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>!</h1>
                <p>Your Freshman Dashboard <span class="emoji">🎓</span></p>
            </div>

            <div class="dashboard-grid">
                <!-- Quick Stats -->
                <div class="dashboard-card stats-card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        <h3>Quick Stats</h3>
                    </div>
                    <div class="stats-content">
                        <div class="stat-item">
                            <i class="fas fa-book-open"></i>
                            <div class="stat-info">
                                <h4>Blog Posts</h4>
                                <p><?php echo $blog_stats['post_count']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card actions-card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="profile/index.php" class="action-btn">
                            <i class="fas fa-user"></i>
                            View Profile
                        </a>
                        <a href="blog/index.php" class="action-btn">
                            <i class="fas fa-blog"></i>
                            My Blog Posts
                        </a>
                        <a href="blog/create.php" class="action-btn">
                            <i class="fas fa-plus"></i>
                            New Blog Post
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-card activity-card">
                    <div class="card-header">
                        <i class="fas fa-history"></i>
                        <h3>Recent Blog Posts</h3>
                    </div>
                    <div class="activity-content">
                        <?php
                        try {
                            $stmt = $pdo->prepare("
                                SELECT title, created_at 
                                FROM blog_posts 
                                WHERE user_id = ? 
                                ORDER BY created_at DESC 
                                LIMIT 5
                            ");
                            $stmt->execute([$user_id]);
                            $recent_posts = $stmt->fetchAll();

                            if (count($recent_posts) > 0) {
                                foreach ($recent_posts as $post) {
                                    echo '<div class="activity-item">';
                                    echo '<i class="fas fa-pen"></i>';
                                    echo '<div class="activity-info">';
                                    echo '<p>' . htmlspecialchars($post['title']) . '</p>';
                                    echo '<span>' . date('M d, Y', strtotime($post['created_at'])) . '</span>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="no-activity">No recent blog posts</p>';
                            }
                        } catch (PDOException $e) {
                            echo '<p class="error-message">Failed to load recent posts</p>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Add this card after the Quick Stats card in your dashboard-grid -->
                <div class="dashboard-card buddy-card">
                    <div class="card-header">
                        <i class="fas fa-users"></i>
                        <h3>My Buddy</h3>
                    </div>
                    <div class="buddy-content">
                        <?php if ($user['continuing_buddy_id']): ?>
                            <div class="buddy-info">
                                <div class="buddy-avatar">
                                    <img src="<?php echo htmlspecialchars($user['buddy_avatar']); ?>" 
                                         alt="Buddy Avatar" 
                                         onerror="this.src='../../assets/images/default-avatar.png'">
                                </div>
                                <div class="buddy-details">
                                    <h4><?php echo htmlspecialchars($user['buddy_name']); ?></h4>
                                    <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($user['buddy_major']); ?></p>
                                    <a href="messages.php?buddy_id=<?php echo $user['continuing_buddy_id']; ?>" 
                                       class="message-btn">
                                        <i class="fas fa-comment"></i> Send Message
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-buddy">
                                <i class="fas fa-user-friends"></i>
                                <p>You haven't been paired with a continuing student yet.</p>
                                <p class="sub-text">Don't worry! You'll be matched with a buddy soon.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/freshman_footer.php'; ?> 