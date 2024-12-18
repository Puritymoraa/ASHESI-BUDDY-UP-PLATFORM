<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$activity_id = $_GET['id'] ?? null;

if (!$activity_id) {
    $_SESSION['error'] = "Invalid activity ID.";
    header('Location: index.php');
    exit;
}

try {
    // First verify the activity belongs to this faculty
    $stmt = $pdo->prepare("
        SELECT activity_id 
        FROM activities 
        WHERE activity_id = ? AND created_by = ?
    ");
    $stmt->execute([$activity_id, $user_id]);
    
    if (!$stmt->fetch()) {
        $_SESSION['error'] = "Activity not found or access denied.";
        header('Location: index.php');
        exit;
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Delete related records first
    $stmt = $pdo->prepare("DELETE FROM activity_completions WHERE activity_id = ?");
    $stmt->execute([$activity_id]);

    // Then delete the activity
    $stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = ? AND created_by = ?");
    $stmt->execute([$activity_id, $user_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "Activity deleted successfully.";

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to delete activity.";
}

header('Location: index.php');
exit;
?> 