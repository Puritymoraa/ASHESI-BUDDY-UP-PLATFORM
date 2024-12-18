<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Freshman']);

$user_id = $_SESSION['user_id'];
$profile = null;

try {
    // Get freshman details with correct column names from the schema
    $stmt = $pdo->prepare("
        SELECT 
            f.freshman_id,
            f.avatar_url,
            f.full_name,
            f.student_id,
            f.age,
            f.gender,
            f.major,
            f.nationality,
            f.hobby,
            f.fun_fact,
            u.email,
            u.registration_date as join_date
        FROM users u
        LEFT JOIN freshman_details f ON u.user_id = f.user_id
        WHERE u.user_id = ?
    ");
    
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        $_SESSION['error'] = "Profile not found.";
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load profile data.";
}

include '../../includes/header.php';
include '../../includes/freshman_sidebar.php';
?>

<div class="main-content">
    <div class="content-wrapper">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($profile): ?>
            <div class="profile-header">
                <div class="profile-cover">
                    <span class="emoji-float">✨</span>
                    <span class="emoji-float">🌟</span>
                    <span class="emoji-float">💫</span>
                </div>
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                    <span class="emoji-badge">🎓</span>
                </div>
                <h1><?php echo htmlspecialchars($profile['full_name'] ?? 'Student'); ?> 
                    <span class="welcome-emoji">👋</span>
                </h1>
                <p class="student-info">
                    <span>Student ID: <?php echo htmlspecialchars($profile['student_id'] ?? 'Not Set'); ?></span>
                    <span class="dot-separator">•</span>
                    <span>Class of <?php echo htmlspecialchars($profile['graduation_year'] ?? 'Not Set'); ?> 🎊</span>
                </p>
            </div>

            <div class="profile-grid">
                <div class="profile-card basic-info">
                    <h2>Basic Information <span>📝</span></h2>
                    <div class="info-group">
                        <label>Full Name:</label>
                        <p><?php echo htmlspecialchars($profile['full_name'] ?? 'Not Set'); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Student ID:</label>
                        <p><?php echo htmlspecialchars($profile['student_id'] ?? 'Not Set'); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email:</label>
                        <p><?php echo htmlspecialchars($profile['email'] ?? 'Not Set'); ?> ✉️</p>
                    </div>
                    <div class="info-group">
                        <label>Major:</label>
                        <p><?php echo htmlspecialchars($profile['major'] ?? 'Not Set'); ?> 📚</p>
                    </div>
                    <div class="info-group">
                        <label>Nationality:</label>
                        <p><?php echo htmlspecialchars($profile['nationality'] ?? 'Not Set'); ?> 🌍</p>
                    </div>
                </div>

                <div class="profile-card preferences">
                    <h2>Personal Information <span>⭐</span></h2>
                    <div class="info-group">
                        <label>Age:</label>
                        <p><?php echo htmlspecialchars($profile['age'] ?? 'Not Set'); ?> 🎂</p>
                    </div>
                    <div class="info-group">
                        <label>Gender:</label>
                        <p><?php echo htmlspecialchars($profile['gender'] ?? 'Not Set'); ?> 👤</p>
                    </div>
                    <div class="info-group">
                        <label>Hobby:</label>
                        <p><?php echo htmlspecialchars($profile['hobby'] ?? 'Not specified'); ?> 🎯</p>
                    </div>
                    <div class="info-group">
                        <label>Fun Fact:</label>
                        <p><?php echo htmlspecialchars($profile['fun_fact'] ?? 'Not specified'); ?> 🎈</p>
                    </div>
                </div>

                <div class="profile-card contact">
                    <h2>Contact Information <span>📱</span></h2>
                    <div class="info-group">
                        <label>Phone:</label>
                        <p><?php echo htmlspecialchars($profile['phone'] ?? 'Not specified'); ?> ☎️</p>
                    </div>
                    <div class="info-group">
                        <label>Emergency Contact:</label>
                        <p><?php echo htmlspecialchars($profile['emergency_contact'] ?? 'Not specified'); ?> 🆘</p>
                    </div>
                </div>

                <div class="profile-card actions">
                    <h2>Profile Actions <span>⚡</span></h2>
                    <div class="action-buttons">
                        <button class="btn btn-secondary" onclick="printProfile()">
                            <i class="fas fa-print"></i> Print Profile 🖨️
                        </button>
                        <button class="btn btn-primary" onclick="downloadProfile()">
                            <i class="fas fa-download"></i> Download as PDF 📄
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="profile-error">
                <h2>Profile Not Found</h2>
                <p>Sorry, we couldn't load your profile information. Please contact support.</p>
            </div>
        <?php endif; ?>

        <div class="profile-note">
            <p>
                <i class="fas fa-info-circle"></i>
                Need to update your profile information? Please contact your student mentor or the administrative office. 
                <span class="emoji-note">📝✨</span>
            </p>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    max-width: 1200px;
    margin: 60px auto 0;
    padding: 2rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.profile-cover {
    height: 200px;
    background: linear-gradient(135deg, var(--dark-gray), var(--accent-orange));
    border-radius: 20px;
    margin-bottom: -50px;
    position: relative;
    overflow: hidden;
}

.emoji-float {
    position: absolute;
    font-size: 2rem;
    animation: float 6s ease-in-out infinite;
}

.emoji-float:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; }
.emoji-float:nth-child(2) { left: 50%; top: 40%; animation-delay: 2s; }
.emoji-float:nth-child(3) { left: 80%; top: 30%; animation-delay: 4s; }

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
    100% { transform: translateY(0) rotate(360deg); }
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: var(--dark-gray);
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 5px solid var(--accent-orange);
    position: relative;
}

.profile-avatar i {
    font-size: 4rem;
    color: var(--white);
}

.emoji-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    font-size: 2rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.profile-header h1 {
    color: var(--white);
    margin: 1rem 0;
    font-size: 2.5rem;
}

.welcome-emoji {
    display: inline-block;
    animation: wave 2.5s infinite;
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-20deg); }
    75% { transform: rotate(20deg); }
}

.student-info {
    color: var(--white);
    opacity: 0.8;
    font-size: 1.1rem;
}

.dot-separator {
    margin: 0 0.5rem;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.profile-card {
    background: var(--dark-gray);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.profile-card h2 {
    color: var(--accent-orange);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-group {
    margin-bottom: 1.5rem;
}

.info-group label {
    display: block;
    color: var(--white);
    opacity: 0.8;
    margin-bottom: 0.5rem;
}

.info-group p {
    color: var(--white);
    font-size: 1.1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 1rem 2rem;
    min-width: 200px;
    justify-content: center;
}

.btn-primary {
    background: var(--accent-orange);
    color: var(--white);
}

.btn-secondary {
    background: var(--dark-gray);
    color: var(--white);
    border: 1px solid var(--accent-orange);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .profile-header h1 {
        font-size: 2rem;
    }
    
    .btn {
        width: 100%;
    }
    
    .profile-note {
        margin: 2rem 1rem;
    }
}

.profile-note {
    margin-top: 2rem;
    padding: 1rem;
    background: rgba(201, 123, 20, 0.1);
    border-left: 4px solid var(--accent-orange);
    border-radius: 8px;
}

.profile-note p {
    color: var(--white);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.emoji-note {
    margin-left: auto;
}
</style>

<script>
function printProfile() {
    window.print();
}

function downloadProfile() {
    // You can implement PDF generation here
    alert('PDF download feature coming soon! 📄✨');
}
</script>

<?php include '../../includes/footer.php'; ?> 