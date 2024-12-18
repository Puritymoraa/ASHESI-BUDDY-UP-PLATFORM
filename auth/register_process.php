<?php
session_start();
require_once '../db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $avatar_url = filter_input(INPUT_POST, 'avatar_url', FILTER_SANITIZE_URL);

    try {
        $pdo->beginTransaction();

        // Check if email exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered");
        }

        // Insert into Users table
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $role]);
        $user_id = $pdo->lastInsertId();

        if ($role === 'Freshman') {
            // Get the next available freshman_id
            $stmt = $pdo->query("SELECT COALESCE(MAX(freshman_id) + 1, 0) as next_id FROM freshman_details");
            $result = $stmt->fetch();
            $next_freshman_id = $result['next_id'];

            // Insert into Freshman_Details
            $stmt = $pdo->prepare("INSERT INTO freshman_details 
                (freshman_id, user_id, avatar_url, full_name, student_id, age, gender, major, nationality, hobby, fun_fact) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $next_freshman_id,
                $user_id,
                $avatar_url,
                $full_name,
                $_POST['student_id'],
                $_POST['age'],
                $_POST['gender'],
                $_POST['major'],
                $_POST['nationality'],
                $_POST['hobby'],
                $_POST['fun_fact']
            ]);
        }

        if ($role === 'Faculty') {
            // Get the next available faculty_id
            $stmt = $pdo->query("SELECT COALESCE(MAX(faculty_id) + 1, 0) as next_id FROM faculty_details");
            $result = $stmt->fetch();
            $next_faculty_id = $result['next_id'];

            // Insert into Faculty_Details
            $stmt = $pdo->prepare("INSERT INTO faculty_details 
                (faculty_id, user_id, avatar_url, full_name, department, research_area, max_mentees) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $next_faculty_id,
                $user_id,
                $avatar_url,
                $full_name,
                $_POST['department'],
                $_POST['research_area'] ?? null,
                10  // Default max mentees
            ]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Registration successful! Please log in.";
        header('Location: login.php');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Registration Error: " . $e->getMessage());
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header('Location: register.php?role=' . urlencode($role));
        exit();
    }
} else {
    header('Location: choose_role.php');
    exit();
}
?>
