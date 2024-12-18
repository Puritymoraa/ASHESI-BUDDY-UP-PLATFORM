<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

$user_id = $_SESSION['user_id'];

try {
    // Get current mentee count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as mentee_count 
        FROM mentorship 
        WHERE continuing_id = ? AND status = 'active'
    ");
    $stmt->execute([$user_id]);
    $mentee_count = $stmt->fetch()['mentee_count'];

    // Get all available freshmen who don't have mentors
    $stmt = $pdo->prepare("
        SELECT f.*, u.email 
        FROM freshman_details f
        JOIN users u ON f.user_id = u.user_id
        LEFT JOIN mentorship m ON f.user_id = m.freshman_id
        WHERE m.mentorship_id IS NULL
        OR m.status = 'inactive'
        ORDER BY f.full_name ASC
    ");
    $stmt->execute();
    $available_freshmen = $stmt->fetchAll();

    // Get current mentees
    $stmt = $pdo->prepare("
        SELECT f.*, m.status, m.start_date, u.email
        FROM mentorship m
        JOIN freshman_details f ON m.freshman_id = f.user_id
        JOIN users u ON f.user_id = u.user_id
        WHERE m.continuing_id = ? AND m.status = 'active'
        ORDER BY f.full_name ASC
    ");
    $stmt->execute([$user_id]);
    $current_mentees = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching mentee data.";
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="back-section">
        <a href="../index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="header-content">
            <div class="header-top">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h1>Mentee Management</h1>
            </div>
            <p class="subtitle">Manage your mentorship relationships and find new mentees</p>
        </div>
    </div>

    <div class="mentee-dashboard">
        <!-- Mentee Capacity Card -->
        <div class="dashboard-card capacity-card">
            <div class="card-content">
                <div class="capacity-header">
                    <h2><i class="fas fa-users"></i> Mentee Capacity</h2>
                    <span class="capacity-number"><?php echo $mentee_count; ?>/3</span>
                </div>
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo ($mentee_count / 3) * 100; ?>%"></div>
                </div>
                <p class="capacity-status">
                    <?php if ($mentee_count < 3): ?>
                        You can mentor <?php echo 3 - $mentee_count; ?> more student(s)
                    <?php else: ?>
                        You've reached your mentee capacity
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Current Mentees Section -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-star"></i> Current Mentees</h2>
            </div>
            <div class="card-content">
                <div class="mentee-grid">
                    <?php if (count($current_mentees) > 0): ?>
                        <?php foreach ($current_mentees as $mentee): ?>
                            <div class="mentee-card">
                                <div class="mentee-header">
                                    <img src="<?php echo htmlspecialchars($mentee['avatar_url'] ?? '../../../assets/img/default-avatar.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($mentee['full_name']); ?>'s Avatar"
                                         class="mentee-avatar">
                                    <div class="mentee-info">
                                        <h3><?php echo htmlspecialchars($mentee['full_name']); ?></h3>
                                        <span class="major"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($mentee['major']); ?></span>
                                    </div>
                                </div>
                                <div class="mentee-details">
                                    <p><i class="fas fa-calendar-alt"></i> Mentee since: <?php echo date('M d, Y', strtotime($mentee['start_date'])); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($mentee['email']); ?></p>
                                </div>
                                <div class="mentee-actions">
                                    <a href="chat.php?with=<?php echo $mentee['user_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-comment"></i> Message
                                    </a>
                                    <a href="view_profile.php?id=<?php echo $mentee['user_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-user"></i> View Profile
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-friends"></i>
                            <p>You currently have no mentees</p>
                            <?php if ($mentee_count < 3): ?>
                                <a href="#available-freshmen" class="btn btn-primary">Find Mentees</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Available Freshmen Section -->
        <?php if ($mentee_count < 3): ?>
            <div class="dashboard-card" id="available-freshmen">
                <div class="card-header">
                    <h2><i class="fas fa-user-plus"></i> Available Freshmen</h2>
                </div>
                <div class="card-content">
                    <div class="freshmen-grid">
                        <?php if (count($available_freshmen) > 0): ?>
                            <?php foreach ($available_freshmen as $freshman): ?>
                                <div class="freshman-card">
                                    <div class="freshman-header">
                                        <img src="<?php echo htmlspecialchars($freshman['avatar_url'] ?? '../../../assets/img/default-avatar.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($freshman['full_name']); ?>'s Avatar"
                                             class="freshman-avatar">
                                        <div class="freshman-info">
                                            <h3><?php echo htmlspecialchars($freshman['full_name']); ?></h3>
                                            <span class="major"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($freshman['major']); ?></span>
                                        </div>
                                    </div>
                                    <div class="freshman-actions">
                                        <form action="select_mentee.php" method="POST" class="select-form">
                                            <input type="hidden" name="freshman_id" value="<?php echo $freshman['user_id']; ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-user-plus"></i> Select as Mentee
                                            </button>
                                        </form>
                                        <a href="view_profile.php?id=<?php echo $freshman['user_id']; ?>" class="btn btn-secondary">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>No freshmen are available for mentorship at this time</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .main-content {
        padding: 2rem;
        margin-left: var(--sidebar-width);
        margin-top: 70px;
    }

    .page-header {
        background: var(--dark-gray);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        border: 1px solid rgba(201, 123, 20, 0.2);
    }

    .page-header h1 {
        color: var(--accent-orange);
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .subtitle {
        color: var(--white);
        opacity: 0.8;
    }

    .dashboard-card {
        background: var(--dark-gray);
        border-radius: 15px;
        margin-bottom: 2rem;
        border: 1px solid rgba(201, 123, 20, 0.2);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(201, 123, 20, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        color: var(--accent-orange);
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Capacity Card Styles */
    .capacity-card {
        background: linear-gradient(145deg, var(--dark-gray), var(--primary-gray));
    }

    .capacity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .capacity-number {
        font-size: 2rem;
        color: var(--accent-orange);
    }

    .progress-bar {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress {
        background: var(--accent-orange);
        height: 100%;
        transition: width 0.3s ease;
    }

    .capacity-status {
        color: var(--white);
        opacity: 0.8;
        text-align: center;
        font-size: 0.9rem;
    }

    /* Grid Layouts */
    .mentee-grid, .freshmen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    /* Card Styles */
    .mentee-card, .freshman-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 1.5rem;
        transition: transform 0.3s ease;
    }

    .mentee-card:hover, .freshman-card:hover {
        transform: translateY(-5px);
    }

    .mentee-header, .freshman-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .mentee-avatar, .freshman-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid var(--accent-orange);
        object-fit: cover;
    }

    .mentee-info h3, .freshman-info h3 {
        color: var(--white);
        margin-bottom: 0.25rem;
    }

    .major {
        color: var(--accent-orange);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .mentee-details {
        margin: 1rem 0;
    }

    .mentee-details p {
        color: var(--white);
        opacity: 0.8;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Button Styles */
    .mentee-actions, .freshman-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.8rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--accent-orange);
        color: var(--white);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: var(--white);
    }

    .btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--white);
        opacity: 0.8;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--accent-orange);
        margin-bottom: 1rem;
    }

    /* Alert Styles */
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .alert-error {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .mentee-actions, .freshman-actions {
            grid-template-columns: 1fr;
        }
    }

    .header-top {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--accent-orange);
        text-decoration: none;
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: rgba(201, 123, 20, 0.1);
    }

    .back-button:hover {
        background: rgba(201, 123, 20, 0.2);
        transform: translateX(-2px);
    }

    .back-button i {
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .header-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>

<script>
document.querySelectorAll('.end-mentorship-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to end this mentorship?')) {
            return;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch('end_mentorship.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                // Remove the mentee card from the UI
                const menteeCard = form.closest('.mentee-card');
                menteeCard.style.opacity = '0';
                setTimeout(() => {
                    menteeCard.remove();
                    
                    // Update mentee count
                    const menteeCount = document.querySelectorAll('.mentee-card').length;
                    const progressBar = document.querySelector('.progress');
                    progressBar.style.width = `${(menteeCount / 3) * 100}%`;
                    progressBar.textContent = `${menteeCount}/3 Mentees`;
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.textContent = 'Mentorship ended successfully';
                    document.querySelector('.main-content').prepend(alert);
                    
                    // Remove alert after 3 seconds
                    setTimeout(() => alert.remove(), 3000);
                }, 300);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
