<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: index.php');
    exit();
}

$activity_id = filter_input(INPUT_POST, 'activity_id', FILTER_SANITIZE_NUMBER_INT);
$mentorship_id = filter_input(INPUT_POST, 'mentorship_id', FILTER_SANITIZE_NUMBER_INT);
$experience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Verify mentorship exists and is active
    $stmt = $pdo->prepare("
        SELECT mentorship_id 
        FROM mentorship 
        WHERE mentorship_id = ? AND continuing_id = ? AND status = 'active'
    ");
    $stmt->execute([$mentorship_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        throw new Exception("Invalid mentorship selected.");
    }

    // Record the activity completion
    $stmt = $pdo->prepare("
        INSERT INTO activity_completions (
            activity_id, 
            mentorship_id, 
            experience, 
            completion_date
        ) VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$activity_id, $mentorship_id, $experience]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "Activity recorded successfully!";
    
    // If it's an AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit();
    }

    // Redirect to feed page with success message
    header('Location: feed.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    
    // If it's an AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
    
    header('Location: index.php');
    exit();
}
?>
