<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkRole($allowed_roles) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ../../auth/login.php');
        exit();
    }

    // Check if user's role is allowed
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        $_SESSION['error'] = "You don't have permission to access this page.";
        
        // Redirect based on role
        switch($_SESSION['role']) {
            case 'Freshman':
                header('Location: ../freshman/');
                exit();
            case 'Faculty':
                header('Location: ../faculty/');
                exit();
            case 'Continuing':
                header('Location: ../student/');
                exit();
            case 'SuperAdmin':
                header('Location: ../superadmin/');
                exit();
            default:
                header('Location: ../auth/login.php');
                exit();
        }
        exit();
    }

    // Check if user has completed their profile (except for SuperAdmin)
    // if ($_SESSION['role'] !== 'SuperAdmin') {
    //     if (!isset($_SESSION['profile_completed']) || !$_SESSION['profile_completed']) {
    //         if ($_SESSION['role'] === 'Continuing') {
    //             // Check if current page is not profile setup
    //             if (strpos($_SERVER['PHP_SELF'], 'profile/setup.php') === false) {
    //                 header('Location: ../student/');
    //                 exit();
    //             }
    //         }
    //     }
    // }
}

// Function to check if user has completed their profile
function isProfileComplete($pdo, $user_id, $role) {
    switch($role) {
        case 'Continuing':
            $stmt = $pdo->prepare("
                SELECT * FROM continuing_student_details 
                WHERE user_id = ?
            ");
            break;
        case 'Freshman':
            $stmt = $pdo->prepare("
                SELECT * FROM freshman_details 
                WHERE user_id = ?
            ");
            break;
        case 'Faculty':
            $stmt = $pdo->prepare("
                SELECT * FROM faculty_details 
                WHERE user_id = ?
            ");
            break;
        case 'SuperAdmin':
            $stmt = $pdo->prepare("
                SELECT * FROM superadmin_details 
                WHERE user_id = ?
            ");
            break;
        default:
            return false;
    }
    
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
    
    return !empty($profile);
}