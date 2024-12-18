<?php
session_start();
require_once '../db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        // First get user details from Users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Debugging output
            error_log("User role: " . $user['role']);

            // Get additional user details based on role
            if ($user['role'] === 'Continuing') {
                $stmt = $pdo->prepare("
                    SELECT full_name, avatar_url 
                    FROM continuing_student_details 
                    WHERE user_id = ?
                ");
            } elseif ($user['role'] === 'Freshman') {
                $stmt = $pdo->prepare("
                    SELECT full_name, avatar_url 
                    FROM freshman_details 
                    WHERE user_id = ?
                ");
            } elseif ($user['role'] === 'Faculty') {
                $stmt = $pdo->prepare("
                    SELECT full_name, avatar_url 
                    FROM faculty_details 
                    WHERE user_id = ?
                ");
            } elseif ($user['role'] === 'SuperAdmin') {
                $stmt = $pdo->prepare("
                    SELECT full_name, avatar_url 
                    FROM superadmin_details 
                    WHERE user_id = ?
                ");
            }
            echo "Hre";
            if ($user['role'] !== 'SuperAdmin') {
                $stmt->execute([$user['user_id']]);
                $userDetails = $stmt->fetch();

                // Store additional details in session
                $_SESSION['full_name'] = $userDetails['full_name'] ?? 'user';
                $_SESSION['avatar_url'] = $userDetails['avatar_url'] ?? '../assets/img/default-avatar.png';
                $_SESSION['profile_completed'] = !empty($userDetails);
            } else {
                // For SuperAdmin, get details from SuperAdmin_Details
                $stmt->execute([$user['user_id']]);
                $adminDetails = $stmt->fetch();
                $_SESSION['full_name'] = $adminDetails['full_name'] ?? 'Super Administrator';
                $_SESSION['avatar_url'] = $adminDetails['avatar_url'] ?? '../assets/img/admin-avatar.png';
                $_SESSION['profile_completed'] = true;
            }

            // Redirect based on role
            switch($user['role']) {
                case 'Freshman':
                    header('Location: ../dashboard/freshman/');
                    exit();
                case 'Faculty':
                    header('Location: ../dashboard/faculty/');
                    exit();
                case 'Continuing':
                    header('Location: ../dashboard/student');
                    exit();
                case 'SuperAdmin':
                    header('Location: ../dashboard/superadmin/');
                    exit();
                default:
                    $_SESSION['error'] = "Invalid role";
                    header('Location: login.php');
                    exit();
            }
            // exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred during login";
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
