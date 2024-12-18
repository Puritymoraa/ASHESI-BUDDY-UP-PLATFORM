<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

header('Content-Type: application/json');

if (!isset($_GET['activity_id'])) {
    echo json_encode(['error' => 'Activity ID not provided']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT activity_id, activity_name, description, 
               semester, created_by, creation_date
        FROM activities 
        WHERE activity_id = ?
    ");
    $stmt->execute([$_GET['activity_id']]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($activity) {
        echo json_encode($activity);
    } else {
        echo json_encode(['error' => 'Activity not found']);
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}
?> 