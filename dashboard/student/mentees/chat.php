<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

$user_id = $_SESSION['user_id'];
$mentee_id = filter_input(INPUT_GET, 'with', FILTER_SANITIZE_NUMBER_INT);

try {
    // Verify this is actually your mentee
    $stmt = $pdo->prepare("
        SELECT f.*, u.email 
        FROM mentorship m
        JOIN freshman_details f ON m.freshman_id = f.user_id
        JOIN users u ON f.user_id = u.user_id
        WHERE m.continuing_id = ? AND m.freshman_id = ? AND m.status = 'active'
    ");
    $stmt->execute([$user_id, $mentee_id]);
    $mentee = $stmt->fetch();

    if (!$mentee) {
        throw new Exception("Invalid mentee selected.");
    }

    // Get chat history
    $stmt = $pdo->prepare("
        SELECT m.*, 
            CASE 
                WHEN m.sender_id = ? THEN 'sent'
                ELSE 'received'
            END as message_type
        FROM messages m
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
        OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC
    ");
    $stmt->execute([$user_id, $user_id, $mentee_id, $mentee_id, $user_id]);
    $messages = $stmt->fetchAll();

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit();
}

include '../../includes/header.php';
?>

<div class="main-content">
    <div class="back-section">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Mentees
        </a>
    </div>

    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-user-info">
                <img src="<?php echo htmlspecialchars($mentee['avatar_url'] ?? '../../../assets/img/default-avatar.png'); ?>" 
                     alt="<?php echo htmlspecialchars($mentee['full_name']); ?>" 
                     class="chat-avatar">
                <div class="chat-user-details">
                    <h2><?php echo htmlspecialchars($mentee['full_name']); ?></h2>
                    <p class="status">Online</p>
                </div>
            </div>
            <a href="view_profile.php?id=<?php echo $mentee_id; ?>" class="view-profile-btn">
                <i class="fas fa-user"></i> View Profile
            </a>
        </div>

        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['message_type']; ?>">
                    <div class="message-content">
                        <?php echo htmlspecialchars($message['content']); ?>
                    </div>
                    <div class="message-time">
                        <?php echo date('g:i A', strtotime($message['sent_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="chat-input-form" id="chat-form">
            <input type="hidden" name="mentee_id" value="<?php echo $mentee_id; ?>">
            <div class="input-group">
                <textarea name="message" placeholder="Type your message..." required></textarea>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        background: var(--dark-gray);
        border-radius: 15px;
        overflow: hidden;
    }

    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .chat-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--accent-orange);
    }

    .chat-user-details h2 {
        color: var(--white);
        margin-bottom: 0.2rem;
    }

    .status {
        color: #28a745;
        font-size: 0.9rem;
    }

    .chat-messages {
        flex: 1;
        padding: 2rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        max-width: 70%;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .message.received {
        align-self: flex-start;
    }

    .message.sent {
        align-self: flex-end;
    }

    .message-content {
        padding: 1rem;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.1);
    }

    .message.sent .message-content {
        background: var(--accent-orange);
    }

    .message-time {
        font-size: 0.8rem;
        opacity: 0.7;
        align-self: flex-end;
    }

    .chat-input-form {
        padding: 1rem 2rem;
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .input-group {
        display: flex;
        gap: 1rem;
    }

    textarea {
        flex: 1;
        padding: 1rem;
        border-radius: 8px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: var(--white);
        resize: none;
        height: 60px;
    }

    textarea:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.15);
    }

    .chat-input-form button {
        padding: 0 1.5rem;
        border: none;
        border-radius: 8px;
        background: var(--accent-orange);
        color: var(--white);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .chat-input-form button:hover {
        opacity: 0.9;
    }

    .back-section {
        margin-bottom: 2rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        color: var(--accent-orange);
        text-decoration: none;
        background: rgba(201, 123, 20, 0.1);
        transition: all 0.3s ease;
    }

    .back-button:hover {
        background: rgba(201, 123, 20, 0.2);
        transform: translateX(-2px);
    }
</style>

<script>
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');

    // Scroll to bottom of messages
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(chatForm);

        try {
            const response = await fetch('send_message.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Add message to chat
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message sent';
                    messageDiv.innerHTML = `
                        <div class="message-content">
                            ${formData.get('message')}
                        </div>
                        <div class="message-time">
                            ${new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })}
                        </div>
                    `;
                    chatMessages.appendChild(messageDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    chatForm.reset();
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    const ws = new WebSocket('ws://localhost:8080');
</script>

<?php include '../../includes/footer.php'; ?> 