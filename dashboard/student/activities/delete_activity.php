<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: index.php');
    exit();
}

$completion_id = filter_input(INPUT_POST, 'completion_id', FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['user_id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Verify ownership and delete
    $stmt = $pdo->prepare("
        DELETE FROM activity_completions 
        WHERE completion_id = ? 
        AND mentorship_id IN (
            SELECT mentorship_id 
            FROM mentorship 
            WHERE continuing_id = ?
        )
    ");
    $result = $stmt->execute([$completion_id, $user_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Activity not found or access denied.");
    }

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "Activity deleted successfully!";
    
    // Handle AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit();
    }

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    
    // Handle AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

header('Location: index.php');
exit();
?>
