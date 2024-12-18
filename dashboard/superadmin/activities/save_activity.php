<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

try {
    $activity_id = $_POST['activity_id'] ?? null;
    $activity_name = trim($_POST['activity_name']);
    $description = trim($_POST['description']);
    $semester = trim($_POST['semester']);

    // Validate inputs
    if (empty($activity_name) || empty($description) || empty($semester)) {
        throw new Exception("All fields are required");
    }

    // Validate semester format (optional)
    if (!preg_match('/^\d{4}\/\d{4}$/', $semester)) {
        throw new Exception("Invalid semester format. Use YYYY/YYYY format");
    }

    if ($activity_id) {
        // Update existing activity
        $stmt = $pdo->prepare("
            UPDATE activities 
            SET activity_name = ?, description = ?, 
                semester = ?
            WHERE activity_id = ?
        ");
        $stmt->execute([
            $activity_name, $description, 
            $semester, $activity_id
        ]);
        $_SESSION['success'] = "Activity updated successfully!";
    } else {
        // Create new activity
        $stmt = $pdo->prepare("
            INSERT INTO activities (
                activity_name, description, semester, 
                created_by
            ) VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $activity_name, $description, 
            $semester, $_SESSION['user_id']
        ]);
        $_SESSION['success'] = "New activity created successfully!";
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to save activity: " . $e->getMessage();
}

header('Location: index.php');
exit();
?> 