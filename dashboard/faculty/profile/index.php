<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$profile = null;

try {
    // Get faculty details with correct column names from the schema
    $stmt = $pdo->prepare("
        SELECT 
            f.faculty_id,
            f.avatar_url,
            f.full_name,
            f.department,
            f.research_area,
            f.max_mentees,
            u.email,
            u.registration_date as join_date,
            (SELECT COUNT(*) FROM faculty_mentees WHERE faculty_id = f.faculty_id) as current_mentees,
            (SELECT COUNT(*) FROM faculty_mentees fm 
             JOIN mentorship m ON fm.mentee_id = m.continuing_id 
             WHERE fm.faculty_id = f.faculty_id) as active_mentors
        FROM users u
        LEFT JOIN faculty_details f ON u.user_id = f.user_id
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
include '../../includes/faculty_sidebar.php';
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
                    <div class="cover-pattern"></div>
                    <span class="emoji-float">📚</span>
                    <span class="emoji-float">🎓</span>
                    <span class="emoji-float">💡</span>
                </div>
                <div class="profile-avatar">
                    <?php if ($profile['avatar_url']): ?>
                        <img src="<?php echo htmlspecialchars($profile['avatar_url']); ?>" alt="Profile Avatar">
                    <?php else: ?>
                        <i class="fas fa-user-tie"></i>
                    <?php endif; ?>
                    <span class="emoji-badge">👨‍🏫</span>
                </div>
                <h1><?php echo htmlspecialchars($profile['full_name']); ?> 
                    <span class="welcome-emoji">👋</span>
                </h1>
                <p class="faculty-info">
                    <span class="info-badge department">
                        <i class="fas fa-university"></i>
                        <?php echo htmlspecialchars($profile['department']); ?>
                    </span>
                    <span class="info-badge id">
                        <i class="fas fa-id-card"></i>
                        Faculty ID: <?php echo htmlspecialchars($profile['faculty_id']); ?>
                    </span>
                </p>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Current Mentees</h3>
                        <p><?php echo $profile['current_mentees']; ?> / <?php echo $profile['max_mentees']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Mentors</h3>
                        <p><?php echo $profile['active_mentors']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Member Since</h3>
                        <p><?php echo date('M Y', strtotime($profile['join_date'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-card basic-info">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i>
                        <h2>Basic Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-group">
                            <label>Full Name</label>
                            <p><?php echo htmlspecialchars($profile['full_name']); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Email</label>
                            <p><?php echo htmlspecialchars($profile['email']); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Department</label>
                            <p><?php echo htmlspecialchars($profile['department']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="profile-card academic">
                    <div class="card-header">
                        <i class="fas fa-microscope"></i>
                        <h2>Academic Profile</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-group">
                            <label>Research Area</label>
                            <p><?php echo htmlspecialchars($profile['research_area'] ?? 'Not specified'); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Mentorship Capacity</label>
                            <div class="capacity-bar">
                                <div class="capacity-fill" style="width: <?php echo ($profile['current_mentees'] / $profile['max_mentees']) * 100; ?>%"></div>
                            </div>
                            <p class="capacity-text"><?php echo $profile['current_mentees']; ?> of <?php echo $profile['max_mentees']; ?> spots filled</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <button class="action-btn print-btn" onclick="printProfile()">
                    <i class="fas fa-print"></i>
                    Print Profile
                </button>
                <button class="action-btn download-btn" onclick="downloadProfile()">
                    <i class="fas fa-download"></i>
                    Download PDF
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
:root {
    --primary-color: var(--accent-orange);
    --primary-dark: var(--accent-orange-dark);
    --dark-bg: var(--darker-bg);
    --card-bg: var(--dark-bg);
    --text-color: var(--text-primary);
    --border-color: var(--border-color);
}

.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
    background: var(--card-bg);
}

.profile-cover {
    height: 200px;
    background: linear-gradient(135deg, var(--accent-orange-dark), var(--accent-orange));
    border-radius: 20px;
    margin-bottom: -50px;
    position: relative;
    overflow: hidden;
}

.cover-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: linear-gradient(45deg, rgba(0,0,0,0.1) 25%, transparent 25%),
                      linear-gradient(-45deg, rgba(0,0,0,0.1) 25%, transparent 25%),
                      linear-gradient(45deg, transparent 75%, rgba(0,0,0,0.1) 75%),
                      linear-gradient(-45deg, transparent 75%, rgba(0,0,0,0.1) 75%);
    background-size: 20px 20px;
    opacity: 0.3;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: var(--card-bg);
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 5px solid var(--accent-orange);
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
    border: 1px solid var(--border-color);
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-info h3 {
    font-size: 0.9rem;
    color: var(--text-secondary);
    opacity: 0.8;
    margin-bottom: 0.25rem;
}

.stat-info p {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--text-primary);
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.profile-card {
    background: var(--card-bg);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.card-header {
    background: rgba(201, 123, 20, 0.1);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-header i {
    color: var(--primary-color);
    font-size: 1.5rem;
}

.card-content {
    padding: 1.5rem;
}

.info-group {
    margin-bottom: 1.5rem;
}

.info-group:last-child {
    margin-bottom: 0;
}

.info-group label {
    display: block;
    color: var(--text-secondary);
    opacity: 0.7;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.info-group p {
    color: var(--text-primary);
    font-size: 1.1rem;
}

.capacity-bar {
    height: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    overflow: hidden;
    margin: 0.5rem 0;
}

.capacity-fill {
    height: 100%;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.capacity-text {
    font-size: 0.9rem !important;
    opacity: 0.7;
}

.profile-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.action-btn {
    padding: 1rem 2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.print-btn {
    background: var(--card-bg);
    color: var(--text-color);
    border: 1px solid var(--primary-color);
}

.download-btn {
    background: var(--primary-color);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 1rem;
    }

    .profile-grid {
        grid-template-columns: 1fr;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .profile-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
        justify-content: center;
    }
}

.emoji-float {
    position: absolute;
    font-size: 2rem;
    animation: float 6s ease-in-out infinite;
    opacity: 0.8;
    z-index: 1;
}

.emoji-float:nth-child(1) { 
    left: 10%; 
    top: 20%; 
    animation-delay: 0s; 
    animation: float 8s ease-in-out infinite;
}
.emoji-float:nth-child(2) { 
    left: 50%; 
    top: 40%; 
    animation-delay: 2s;
    animation: float 6s ease-in-out infinite;
}
.emoji-float:nth-child(3) { 
    left: 80%; 
    top: 30%; 
    animation-delay: 4s;
    animation: float 7s ease-in-out infinite;
}

.emoji-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    font-size: 2rem;
    background: var(--card-bg);
    border-radius: 50%;
    padding: 5px;
    border: 3px solid var(--primary-color);
    animation: bounce 2s infinite;
}

.welcome-emoji {
    display: inline-block;
    animation: wave 2.5s infinite;
    margin-left: 0.5rem;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--card-bg);
    border-radius: 20px;
    margin: 0.5rem;
    border: 1px solid rgba(201, 123, 20, 0.2);
    transition: all 0.3s ease;
}

.info-badge:hover {
    transform: translateY(-2px);
    border-color: var(--primary-color);
}

.info-badge i {
    color: var(--primary-color);
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-20deg); }
    75% { transform: rotate(20deg); }
}

.stat-card {
    position: relative;
    overflow: hidden;
}

.stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255, 255, 255, 0.03),
        transparent
    );
    transform: rotate(45deg);
    animation: sparkle 3s infinite;
}

@keyframes sparkle {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}

.profile-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.profile-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-color);
    box-shadow: 0 5px 15px rgba(201, 123, 20, 0.1);
}

.action-btn {
    position: relative;
    overflow: hidden;
}

.action-btn::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transform: rotate(45deg);
    transition: 0.5s;
}

.action-btn:hover::after {
    animation: glow 1s;
}

@keyframes glow {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}
</style>

<script>
function printProfile() {
    window.print();
}

function downloadProfile() {
    alert('PDF download feature coming soon! 📄✨');
}
</script>

<?php include '../../includes/faculty_footer.php'; ?> 