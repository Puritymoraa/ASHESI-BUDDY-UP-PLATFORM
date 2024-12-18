<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Freshman']);

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$last_id = filter_input(INPUT_GET, 'last_id', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$buddy_id = filter_input(INPUT_GET, 'buddy_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Verify this is your buddy
    $stmt = $pdo->prepare("
        SELECT mentorship_id 
        FROM mentorship 
        WHERE continuing_id = ? AND freshman_id = ? AND status = 'active'
    ");
    $stmt->execute([$buddy_id, $user_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception("Invalid buddy selected.");
    }

    // Get new messages
    $stmt = $pdo->prepare("
        SELECT message_id, sender_id, content, sent_at
        FROM messages 
        WHERE message_id > ? 
        AND ((sender_id = ? AND receiver_id = ?) 
        OR (sender_id = ? AND receiver_id = ?))
        ORDER BY sent_at ASC
    ");
    $stmt->execute([$last_id, $user_id, $buddy_id, $buddy_id, $user_id]);
    
    echo json_encode([
        'success' => true,
        'messages' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 