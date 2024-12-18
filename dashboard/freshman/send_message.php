<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Freshman']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = filter_input(INPUT_POST, 'buddy_id', FILTER_SANITIZE_NUMBER_INT);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

try {
    // Verify this is actually your buddy
    $stmt = $pdo->prepare("
        SELECT mentorship_id 
        FROM mentorship 
        WHERE continuing_id = ? AND freshman_id = ? AND status = 'active'
    ");
    $stmt->execute([$receiver_id, $sender_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception("Invalid buddy selected.");
    }

    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, content, sent_at) 
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 