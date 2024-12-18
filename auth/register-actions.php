<?php
// Sanitize user input to prevent XSS and unwanted characters
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password for secure storage
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Register a new user in the database
function registerUser($pdo, $role, $username, $email, $password) {
    try {
        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT email FROM buddyup_users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email is already registered'];
        }

        // Check if the username is already taken
        $stmt = $pdo->prepare("SELECT username FROM buddyup_users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username is already taken'];
        }

        // Hash the password
        $hashedPassword = hashPassword($password);

        // Insert the new user into the database
        $stmt = $pdo->prepare("
            INSERT INTO buddyup_users (username, email, password, role, registration_date, last_login_date, is_active) 
            VALUES (:username, :email, :password, :role, NOW(), NULL, 1)
        ");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);

        return ['success' => true, 'message' => 'Registration successful!'];
    } catch (PDOException $e) {
        // Log the error for debugging
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again later.'];
    }
}

// Create a session for a logged-in user
function createSession($userId, $userRole) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = $userRole;
    $_SESSION['last_activity'] = time();
}

// Check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Ensure the user has the correct role to access a page
function checkUserType($allowedRoles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles)) {
        header('Location: login.php');
        exit();
    }
}
?>
