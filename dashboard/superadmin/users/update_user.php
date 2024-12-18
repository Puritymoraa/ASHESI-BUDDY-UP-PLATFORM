<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: freshmen.php');
    exit();
}

try {
    $pdo->beginTransaction();
    
    $user_id = $_POST['user_id'];
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // First get the user's role
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Update email in Users table
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
    $stmt->execute([$email, $user_id]);
    
    // Update role-specific details
    switch($user['role']) {
        case 'Freshman':
            $stmt = $pdo->prepare("
                UPDATE freshman_details 
                SET full_name = ?, student_id = ?, major = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['student_id'],
                $_POST['major'],
                $user_id
            ]);
            $redirect = 'freshmen.php';
            break;
            
        case 'Continuing':
            $stmt = $pdo->prepare("
                UPDATE continuing_student_details 
                SET full_name = ?, student_id = ?, major = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['student_id'],
                $_POST['major'],
                $user_id
            ]);
            $redirect = 'continuing.php';
            break;
            
        case 'Faculty':
            $stmt = $pdo->prepare("
                UPDATE faculty_details 
                SET full_name = ?, department = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['department'],
                $user_id
            ]);
            $redirect = 'faculty.php';
            break;
    }
    
    $pdo->commit();
    $_SESSION['success'] = "User updated successfully";
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to update user";
    $redirect = 'freshmen.php';
}

header("Location: $redirect");
exit();
?> 