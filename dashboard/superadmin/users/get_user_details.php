<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

try {
    $user_id = $_GET['user_id'];
    
    // First get the user's role
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    // Based on role, get the appropriate details
    switch($user['role']) {
        case 'Freshman':
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.email, f.full_name, f.student_id, 
                       f.major, f.avatar_url
                FROM users u
                JOIN freshman_details f ON u.user_id = f.user_id
                WHERE u.user_id = ?
            ");
            break;
            
        case 'Continuing':
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.email, c.full_name, c.student_id, 
                       c.major
                FROM users u
                JOIN continuing_student_details c ON u.user_id = c.user_id
                WHERE u.user_id = ?
            ");
            break;
            
        case 'Faculty':
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.email, f.full_name, f.department
                FROM users u
                JOIN faculty_details f ON u.user_id = f.user_id
                WHERE u.user_id = ?
            ");
            break;
            
        default:
            echo json_encode(['error' => 'Invalid user role']);
            exit;
    }
    
    $stmt->execute([$user_id]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($details) {
        echo json_encode($details);
    } else {
        echo json_encode(['error' => 'User details not found']);
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}
?> 