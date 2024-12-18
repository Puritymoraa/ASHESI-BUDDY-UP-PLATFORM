<?php
require_once '../includes/auth_check.php';
require_once '../../db/database.php';
checkRole(['Freshman']);

$user_id = $_SESSION['user_id'];
$current_page = 'messages';

try {
    // Get freshman's buddy information
    $stmt = $pdo->prepare("
        SELECT 
            c.user_id as buddy_id,
            c.full_name as buddy_name,
            c.avatar_url as buddy_avatar,
            c.major as buddy_major
        FROM freshman_details f
        LEFT JOIN mentorship m ON f.user_id = m.freshman_id
        LEFT JOIN continuing_student_details c ON c.user_id = m.continuing_id
        WHERE f.user_id = ? AND m.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $buddy = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get conversation history
    if ($buddy) {
        $stmt = $pdo->prepare("
            SELECT 
                m.*,
                CASE 
                    WHEN m.sender_id = ? THEN 'sent'
                    ELSE 'received'
                END as message_type
            FROM messages m
            WHERE (m.sender_id = ? AND m.receiver_id = ?)
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([$user_id, $user_id, $buddy['buddy_id'], $buddy['buddy_id'], $user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load messages: " . $e->getMessage();
}

include '../includes/header.php';
include '../includes/freshman_sidebar.php';
?>

<style>
    .main-content {
        margin-left: var(--sidebar-width);
        padding-top: calc(var(--header-height) + 6rem);
        padding-left: 2rem;
        padding-right: 2rem;
        padding-bottom: 2rem;
        min-height: 100vh;
        background: var(--primary-gray);
        position: relative;
        z-index: 1;
    }

    .chat-container {
        max-width: 1000px;
        margin: 0 auto;
        background: var(--card-bg);
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 2;
        margin-top: 2rem;
        border: 1px solid var(--border-color);
    }

    .chat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
        border-radius: 15px 15px 0 0;
        background: var(--dark-bg);
        position: relative;
        z-index: 3;
        margin-top: 1rem;
    }

    .buddy-avatar img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--accent-orange);
    }

    .buddy-info h3 {
        margin: 0;
        color: var(--white);
    }

    .buddy-info p {
        margin: 0.25rem 0 0;
        color: var(--accent-orange);
        font-size: 0.9rem;
    }

    .messages-list {
        height: calc(60vh - var(--header-height) - 2rem);
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
    }

    .message {
        max-width: 70%;
        padding: 1rem;
        border-radius: 15px;
        position: relative;
        background: var(--card-bg);
    }

    .message.sent {
        align-self: flex-end;
        background: var(--accent-orange);
        color: var(--text-primary);
        border-bottom-right-radius: 5px;
    }

    .message.received {
        align-self: flex-start;
        background: var(--dark-bg);
        color: var(--text-primary);
        border-bottom-left-radius: 5px;
    }

    .message-time {
        font-size: 0.75rem;
        opacity: 0.7;
        margin-top: 0.5rem;
    }

    .message-form {
        padding: 1.5rem;
        display: flex;
        gap: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .message-input {
        flex: 1;
        padding: 1rem;
        border: none;
        border-radius: 25px;
        background: var(--dark-bg);
        color: var(--text-primary);
        font-size: 1rem;
        border: 1px solid var(--border-color);
    }

    .message-input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.15);
    }

    .send-button {
        padding: 1rem 2rem;
        border: none;
        border-radius: 25px;
        background: var(--accent-orange);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .send-button:hover {
        background: var(--white);
        color: var(--accent-orange);
    }

    .no-messages {
        text-align: center;
        padding: 2rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .no-buddy-message {
        text-align: center;
        padding: 3rem;
        color: var(--white);
    }

    .no-buddy-message i {
        font-size: 3rem;
        color: var(--accent-orange);
        margin-bottom: 1rem;
    }

    .no-buddy-message p {
        margin: 0.5rem 0;
    }

    .no-buddy-message .sub-text {
        color: var(--accent-orange);
        font-size: 0.9rem;
    }

    .buddy-info {
        padding-top: 1rem;
    }

    .back-button {
        margin-bottom: 1rem;
        padding: 0 1rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--white);
        text-decoration: none;
        padding: 0.5rem 1rem;
        background: var(--dark-gray);
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        background: var(--accent-orange);
    }

    .back-link i {
        font-size: 0.9rem;
    }
</style>

<div class="main-content">
    <div class="back-button">
        <a href="/buddyup/dashboard/freshman/index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="chat-container">
        <?php if ($buddy): ?>
            <div class="chat-header">
                <div class="buddy-avatar">
                    <img src="<?php echo htmlspecialchars($buddy['buddy_avatar']); ?>" 
                         alt="Buddy Avatar"
                         onerror="this.src='/buddyup/assets/images/default-avatar.png'">
                </div>
                <div class="buddy-info">
                    <h3><?php echo htmlspecialchars($buddy['buddy_name']); ?></h3>
                    <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($buddy['buddy_major']); ?></p>
                </div>
            </div>
            
            <div class="messages-list" id="messagesList">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo $message['message_type']; ?>">
                            <div class="message-content"><?php echo htmlspecialchars($message['content']); ?></div>
                            <div class="message-time">
                                <?php echo date('h:i A', strtotime($message['sent_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-messages">
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                <?php endif; ?>
            </div>

            <form id="messageForm" class="message-form" data-buddy-id="<?php echo $buddy['buddy_id']; ?>">
                <input type="text" 
                       id="messageInput" 
                       class="message-input" 
                       placeholder="Type your message..." 
                       required>
                <button type="submit" class="send-button">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send</span>
                </button>
            </form>
        <?php else: ?>
            <div class="no-buddy-message">
                <i class="fas fa-user-friends"></i>
                <p>You haven't been paired with a continuing student yet.</p>
                <p class="sub-text">Once you're paired, you can start chatting!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messagesList = document.getElementById('messagesList');
    const messageInput = document.getElementById('messageInput');

    function scrollToBottom() {
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    scrollToBottom();

    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            const buddy_id = this.dataset.buddyId;

            if (!message) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `buddy_id=${buddy_id}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const messageElement = document.createElement('div');
                    messageElement.className = 'message sent';
                    messageElement.innerHTML = `
                        <div class="message-content">${message}</div>
                        <div class="message-time">${new Date().toLocaleTimeString()}</div>
                    `;
                    messagesList.appendChild(messageElement);
                    messageInput.value = '';
                    scrollToBottom();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send message. Please try again.');
            });
        });
    }

    // Poll for new messages every 3 seconds
    if (messageForm) {
        setInterval(() => {
            const lastMessage = messagesList.lastElementChild;
            const lastMessageId = lastMessage ? lastMessage.dataset.messageId : 0;
            const buddy_id = messageForm.dataset.buddyId;

            fetch(`get_messages.php?last_id=${lastMessageId}&buddy_id=${buddy_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            const messageElement = document.createElement('div');
                            messageElement.className = `message ${msg.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received'}`;
                            messageElement.dataset.messageId = msg.message_id;
                            messageElement.innerHTML = `
                                <div class="message-content">${msg.content}</div>
                                <div class="message-time">${new Date(msg.sent_at).toLocaleTimeString()}</div>
                            `;
                            messagesList.appendChild(messageElement);
                        });
                        scrollToBottom();
                    }
                })
                .catch(error => console.error('Error polling messages:', error));
        }, 3000);
    }
});
</script>

<?php include '../includes/freshman_footer.php'; ?> 