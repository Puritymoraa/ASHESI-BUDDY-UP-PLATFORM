<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['SuperAdmin']);

if (!isset($_GET['user_id']) || !isset($_GET['role'])) {
    $_SESSION['error'] = "Invalid request";
    header('Location: freshmen.php');
    exit();
}

try {
    $pdo->beginTransaction();
    
    $user_id = $_GET['user_id'];
    $role = $_GET['role'];
    
    // First delete any messages
    $stmt = $pdo->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
    $stmt->execute([$user_id, $user_id]);
    
    // Delete from role-specific details and related tables
    switch($role) {
        case 'Freshman':
            // Delete activity completions related to mentorships
            $stmt = $pdo->prepare("
                DELETE ac FROM activity_completions ac
                INNER JOIN mentorship m ON ac.mentorship_id = m.mentorship_id
                WHERE m.freshman_id = ?
            ");
            $stmt->execute([$user_id]);
            
            // Delete mentorship records
            $stmt = $pdo->prepare("DELETE FROM mentorship WHERE freshman_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete from continuing_freshman_buddies
            $stmt = $pdo->prepare("DELETE FROM continuing_freshman_buddies WHERE freshman_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete from freshman details
            $stmt = $pdo->prepare("DELETE FROM freshman_details WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            $redirect = 'freshmen.php';
            break;
            
        case 'Continuing':
            // Delete activity completions related to mentorships
            $stmt = $pdo->prepare("
                DELETE ac FROM activity_completions ac
                INNER JOIN mentorship m ON ac.mentorship_id = m.mentorship_id
                WHERE m.continuing_id = ?
            ");
            $stmt->execute([$user_id]);
            
            // Delete mentorship records
            $stmt = $pdo->prepare("DELETE FROM mentorship WHERE continuing_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete from continuing_freshman_buddies
            $stmt = $pdo->prepare("DELETE FROM continuing_freshman_buddies WHERE continuing_student_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete from continuing student details
            $stmt = $pdo->prepare("DELETE FROM continuing_student_details WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            $redirect = 'continuing.php';
            break;
            
        case 'Faculty':
            // Delete faculty mentee relationships
            $stmt = $pdo->prepare("
                DELETE fm FROM faculty_mentees fm
                INNER JOIN faculty_details fd ON fm.faculty_id = fd.faculty_id
                WHERE fd.user_id = ?
            ");
            $stmt->execute([$user_id]);
            
            // Delete from faculty details
            $stmt = $pdo->prepare("DELETE FROM faculty_details WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            $redirect = 'faculty.php';
            break;
            
        default:
            throw new Exception('Invalid role');
    }
    
    // Delete any blog posts
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Finally delete from Users table
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    $pdo->commit();
    $_SESSION['success'] = "User deleted successfully";
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to delete user: " . $e->getMessage();
}

header("Location: $redirect");
exit();
?> 