<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

$completion_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['user_id'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $experience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);
        
        $stmt = $pdo->prepare("
            UPDATE activity_completions 
            SET experience = ?
            WHERE completion_id = ? 
            AND mentorship_id IN (
                SELECT mentorship_id 
                FROM mentorship 
                WHERE continuing_id = ?
            )
        ");
        $stmt->execute([$experience, $completion_id, $user_id]);
        
        $_SESSION['success'] = "Activity updated successfully!";
        header('Location: index.php');
        exit();
    }

    // Get activity details for editing
    $stmt = $pdo->prepare("
        SELECT ac.*, a.activity_name
        FROM activity_completions ac
        JOIN activities a ON ac.activity_id = a.activity_id
        WHERE ac.completion_id = ? 
        AND ac.mentorship_id IN (
            SELECT mentorship_id 
            FROM mentorship 
            WHERE continuing_id = ?
        )
    ");
    $stmt->execute([$completion_id, $user_id]);
    $activity = $stmt->fetch();

    if (!$activity) {
        throw new Exception("Activity not found or access denied.");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<!-- Add edit form HTML -->

<div class="main-content">
    <div class="back-section">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Activities
        </a>
    </div>

    <div class="edit-section">
        <h2>Edit Activity</h2>
        <div class="activity-info">
            <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
            <p class="date">Completed on: <?php echo date('M d, Y', strtotime($activity['completion_date'])); ?></p>
        </div>

        <form action="" method="POST" class="edit-form">
            <div class="form-group">
                <label for="experience">Update Your Experience</label>
                <textarea name="experience" id="experience" rows="6" required><?php 
                    echo htmlspecialchars($activity['experience']); 
                ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .edit-section {
        background: var(--dark-gray);
        padding: 2rem;
        border-radius: 15px;
    }

    .activity-info {
        margin-bottom: 2rem;
    }

    .activity-info h3 {
        color: var(--accent-orange);
        margin-bottom: 0.5rem;
    }

    .date {
        color: var(--white);
        opacity: 0.8;
    }

    .edit-form {
        display: grid;
        gap: 1.5rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    textarea {
        width: 100%;
        padding: 1rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--white);
    }
</style>
