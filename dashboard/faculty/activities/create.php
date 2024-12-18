<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$error = null;
$success = null;

// Get faculty details for header
try {
    $stmt = $pdo->prepare("
        SELECT 
            f.faculty_id,
            f.full_name,
            f.department,
            f.avatar_url
        FROM Faculty_Details f
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['activity_name']) || empty($_POST['description']) || empty($_POST['semester'])) {
            throw new Exception('All fields are required');
        }

        $stmt = $pdo->prepare("
            INSERT INTO activities (
                activity_name, 
                description, 
                semester, 
                created_by, 
                creation_date
            ) VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $_POST['activity_name'],
            $_POST['description'],
            $_POST['semester'],
            $user_id
        ]);

        $_SESSION['success'] = "Activity created successfully!";
        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include '../../includes/header.php';
include '../../includes/faculty_sidebar.php';
?>

<div class="main-content">
    <div class="create-activity-container">
        <div class="page-header">
            <div class="header-content">
                <h1>Create New Activity</h1>
                <p>Create a new learning activity for your mentees</p>
            </div>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Activities
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" class="activity-form">
                <div class="form-group">
                    <label for="activity_name">Activity Name</label>
                    <input type="text" 
                           id="activity_name" 
                           name="activity_name" 
                           required
                           placeholder="Enter activity name"
                           value="<?php echo isset($_POST['activity_name']) ? htmlspecialchars($_POST['activity_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                            name="description" 
                            required
                            placeholder="Describe the activity"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">Select semester</option>
                        <?php
                        $current_year = date('Y');
                        $selected_semester = isset($_POST['semester']) ? $_POST['semester'] : '';
                        
                        for ($year = $current_year; $year <= $current_year + 1; $year++) {
                            $spring = "Spring {$year}";
                            $fall = "Fall {$year}";
                            
                            echo "<option value='{$spring}'" . ($selected_semester === $spring ? ' selected' : '') . ">{$spring}</option>";
                            echo "<option value='{$fall}'" . ($selected_semester === $fall ? ' selected' : '') . ">{$fall}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus"></i> Create Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --darker-bg: #121212;
    --dark-bg: #1E1E1E;
    --card-bg: #252525;
    --border-color: #333333;
    --text-gray: #B3B3B3;
    --white: #FFFFFF;
    --accent-orange: #C97B14;
    --accent-orange-dark: #A66610;
    --error-red: #dc3545;
    --success-green: #28a745;
    --header-height: 60px;
}

.main-content {
    padding-top: calc(var(--header-height) + 2rem);
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    background: var(--darker-bg);
    padding: 2rem;
}

.create-activity-container {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    color: var(--white);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.header-content p {
    color: var(--text-gray);
}

.btn-back {
    background: var(--darker-bg);
    color: var(--text-gray);
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    color: var(--accent-orange);
}

.form-card {
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.activity-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    color: var(--white);
    font-size: 1rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 0.8rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--darker-bg);
    color: var(--white);
    font-size: 1rem;
}

.form-group textarea {
    min-height: 150px;
    resize: vertical;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 1rem;
}

.btn-submit {
    background: var(--accent-orange);
    color: var(--white);
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<?php include '../../includes/faculty_footer.php'; ?> 