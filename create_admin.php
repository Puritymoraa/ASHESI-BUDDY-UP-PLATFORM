<?php
require_once 'db/database.php';

try {
    // Create password hash
    $password = 'Superadmin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // First, check if superadmin already exists
    $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->execute(['superadmin@ashesi.edu.gh']);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        echo "SuperAdmin already exists!";
        exit;
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Insert into Users table
    $stmt = $pdo->prepare("
        INSERT INTO Users (email, password, role, registration_date) 
        VALUES (?, ?, 'SuperAdmin', CURRENT_TIMESTAMP)
    ");
    $stmt->execute(['superadmin@ashesi.edu.gh', $hashed_password]);
    
    $user_id = $pdo->lastInsertId();

    // Insert into SuperAdmin_Details
    $stmt = $pdo->prepare("
        INSERT INTO SuperAdmin_Details (user_id, full_name, avatar_url) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        'Super Administrator',
        'https://api.dicebear.com/6.x/initials/svg?seed=SuperAdmin'
    ]);

    // Commit transaction
    $pdo->commit();

    echo "SuperAdmin created successfully!<br>";
    echo "Email: superadmin@ashesi.edu.gh<br>";
    echo "Password: Superadmin123";

} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>