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

    // Verify mentorship exists and is active
    $stmt = $pdo->prepare("
        SELECT mentorship_id 
        FROM mentorship 
        WHERE continuing_id = ? AND freshman_id = ? AND status = 'active'
    ");
    $stmt->execute([$continuing_id, $freshman_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception("Invalid mentorship selected.");
    }

    // End mentorship
    $stmt = $pdo->prepare("
        UPDATE mentorship 
        SET status = 'inactive', end_date = NOW()
        WHERE continuing_id = ? AND freshman_id = ? AND status = 'active'
    ");
    $stmt->execute([$continuing_id, $freshman_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "Mentorship has been ended successfully.";
    
    // Send JSON response if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit();
    }
    
    header('Location: index.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    
    // Send JSON response if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
    
    header('Location: index.php');
    exit();
}
?> 