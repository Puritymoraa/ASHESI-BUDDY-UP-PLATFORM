<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

$user_id = $_SESSION['user_id'];
$mentee_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Get mentee details
    $stmt = $pdo->prepare("
        SELECT f.*, u.email, m.start_date, m.status as mentorship_status
        FROM freshman_details f
        JOIN users u ON f.user_id = u.user_id
        LEFT JOIN mentorship m ON f.user_id = m.freshman_id AND m.continuing_id = ?
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id, $mentee_id]);
    $mentee = $stmt->fetch();

    if (!$mentee) {
        throw new Exception("Mentee not found.");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="main-content">
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($mentee['avatar_url'] ?? '../../assets/img/default-avatar.png'); ?>" 
                 alt="<?php echo htmlspecialchars($mentee['full_name']); ?>'s Avatar" 
                 class="profile-avatar">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($mentee['full_name']); ?></h1>
                <p class="major"><?php echo htmlspecialchars($mentee['major']); ?></p>
                <?php if ($mentee['mentorship_status'] === 'active'): ?>
                    <p class="mentorship-status">
                        Mentee since: <?php echo date('M d, Y', strtotime($mentee['start_date'])); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-details">
            <div class="detail-section">
                <h2>Personal Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Student ID:</span>
                        <span class="value"><?php echo htmlspecialchars($mentee['student_id']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Nationality:</span>
                        <span class="value"><?php echo htmlspecialchars($mentee['nationality']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Hobby:</span>
                        <span class="value"><?php echo htmlspecialchars($mentee['hobby']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Fun Fact:</span>
                        <span class="value"><?php echo htmlspecialchars($mentee['fun_fact']); ?></span>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="chat.php?with=<?php echo $mentee['user_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-comment"></i> Send Message
                </a>
                <?php if ($mentee['mentorship_status'] === 'active'): ?>
                    <form action="end_mentorship.php" method="POST" class="end-mentorship-form">
                        <input type="hidden" name="freshman_id" value="<?php echo $mentee['user_id']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to end this mentorship?')">
                            <i class="fas fa-user-times"></i> End Mentorship
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
        background: var(--dark-gray);
        border-radius: 15px;
        margin-bottom: 2rem;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid var(--accent-orange);
        object-fit: cover;
    }

    .profile-info h1 {
        color: var(--accent-orange);
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }

    .major {
        font-size: 1.2rem;
        color: var(--white);
        opacity: 0.8;
    }

    .mentorship-status {
        margin-top: 0.5rem;
        color: var(--accent-orange);
        font-size: 0.9rem;
    }

    .profile-details {
        background: var(--dark-gray);
        border-radius: 15px;
        padding: 2rem;
    }

    .detail-section {
        margin-bottom: 2rem;
    }

    .detail-section h2 {
        color: var(--accent-orange);
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .label {
        color: var(--white);
        opacity: 0.7;
        font-size: 0.9rem;
    }

    .value {
        color: var(--white);
        font-size: 1.1rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--accent-orange);
        color: var(--white);
    }

    .btn-danger {
        background: #dc3545;
        color: var(--white);
    }

    .btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php include '../../includes/footer.php'; ?> 