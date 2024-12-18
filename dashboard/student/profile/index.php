<?php
session_start();
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Student']);

$user_id = $_SESSION['user_id'];
$profile = null;

try {
    // Get continuing student details
    $stmt = $pdo->prepare("
        SELECT 
            c.continuing_id,
            c.avatar_url,
            c.full_name,
            c.student_id,
            c.age,
            c.gender,
            c.major,
            c.nationality,
            c.hobby,
            c.fun_fact,
            u.email,
            u.registration_date as join_date
        FROM Users u
        LEFT JOIN continuing_student_details c ON u.user_id = c.user_id
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
include '../../includes/sidebar.php';
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
                    <?php if ($profile['avatar_url']): ?>
                        <img src="<?php echo htmlspecialchars($profile['avatar_url']); ?>" alt="Profile Avatar">
                    <?php else: ?>
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                    <span class="emoji-badge">🎓</span>
                </div>
                <h1><?php echo htmlspecialchars($profile['full_name'] ?? 'Student'); ?> 
                    <span class="welcome-emoji">👋</span>
                </h1>
                <p class="student-info">
                    <span>Student ID: <?php echo htmlspecialchars($profile['student_id'] ?? 'Not Set'); ?></span>
                    <span class="dot-separator">•</span>
                    <span>Continuing Student 🎊</span>
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
    </div>
</div>

<!-- Include the same CSS and JavaScript as the freshman profile -->
<style>
/* Copy all the CSS from the freshman profile */
[Previous CSS styles here...]
</style>

<script>
function printProfile() {
    window.print();
}

function downloadProfile() {
    alert('PDF download feature coming soon! 📄✨');
}
</script>

<?php include '../../includes/footer.php'; ?> 