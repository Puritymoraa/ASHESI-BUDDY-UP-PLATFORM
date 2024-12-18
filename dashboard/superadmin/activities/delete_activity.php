<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if (!isset($_GET['activity_id'])) {
    $_SESSION['error'] = "Invalid request";
    header('Location: index.php');
    exit();
}

try {
    $activity_id = $_GET['activity_id'];
    
    // First check if activity exists
    $stmt = $pdo->prepare("SELECT activity_name FROM activities WHERE activity_id = ?");
    $stmt->execute([$activity_id]);
    $activity = $stmt->fetch();
    
    if (!$activity) {
        throw new Exception("Activity not found");
    }

    // Check if activity is being used in activity_completions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_completions WHERE activity_id = ?");
    $stmt->execute([$activity_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        throw new Exception("Cannot delete activity as it has associated completions");
    }

    // Delete the activity
    $stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = ?");
    $stmt->execute([$activity_id]);
    
    $_SESSION['success'] = "Activity '" . htmlspecialchars($activity['activity_name']) . "' has been deleted.";
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to delete activity: " . $e->getMessage();
}

header('Location: index.php');
exit();
?> 