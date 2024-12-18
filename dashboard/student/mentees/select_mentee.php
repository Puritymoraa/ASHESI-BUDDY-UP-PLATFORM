<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: index.php');
    exit();
}

$continuing_id = $_SESSION['user_id'];
$freshman_id = filter_input(INPUT_POST, 'freshman_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Check current mentee count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as mentee_count 
        FROM mentorship 
        WHERE continuing_id = ? AND status = 'active'
    ");
    $stmt->execute([$continuing_id]);
    $mentee_count = $stmt->fetch()['mentee_count'];

    if ($mentee_count >= 3) {
        throw new Exception("You have reached the maximum number of mentees (3).");
    }

    // Check if freshman is already assigned
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as is_assigned 
        FROM mentorship 
        WHERE freshman_id = ? AND status = 'active'
    ");
    $stmt->execute([$freshman_id]);
    if ($stmt->fetch()['is_assigned'] > 0) {
        throw new Exception("This freshman is already assigned to a mentor.");
    }

    // Create new mentorship
    $stmt = $pdo->prepare("
        INSERT INTO mentorship (continuing_id, freshman_id, status, start_date) 
        VALUES (?, ?, 'active', CURRENT_TIMESTAMP)
    ");
    $stmt->execute([$continuing_id, $freshman_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "Mentee successfully added!";
    header('Location: index.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit();
}
?> 