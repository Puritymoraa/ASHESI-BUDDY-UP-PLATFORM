<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Faculty']);

$user_id = $_SESSION['user_id'];
$activity_id = $_GET['id'] ?? null;
$error = null;
$success = null;
$activity = null;

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

// Verify activity exists and belongs to faculty
try {
    $stmt = $pdo->prepare("
        SELECT * FROM activities 
        WHERE activity_id = ? AND created_by = ?
    ");
    $stmt->execute([$activity_id, $user_id]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activity) {
        $_SESSION['error'] = "Activity not found or access denied.";
        header('Location: index.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to load activity.";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['activity_name']) || empty($_POST['description']) || empty($_POST['semester'])) {
            throw new Exception('All fields are required');
        }

        // Updated query without the last_modified field
        $stmt = $pdo->prepare("
            UPDATE activities 
            SET activity_name = ?, 
                description = ?, 
                semester = ?
            WHERE activity_id = ? AND created_by = ?
        ");

        $stmt->execute([
            $_POST['activity_name'],
            $_POST['description'],
            $_POST['semester'],
            $activity_id,
            $user_id
        ]);

        $_SESSION['success'] = "Activity updated successfully!";
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
    <div class="edit-activity-container">
        <div class="page-header">
            <div class="header-content">
                <h1>Edit Activity</h1>
                <p>Update your learning activity details</p>
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
                           value="<?php echo htmlspecialchars($activity['activity_name']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              required><?php echo htmlspecialchars($activity['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <?php
                        $current_year = date('Y');
                        $selected_semester = $activity['semester'];
                        
                        for ($year = $current_year - 1; $year <= $current_year + 1; $year++) {
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
                        <i class="fas fa-save"></i> Save Changes
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

.edit-activity-container {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
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
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-gray);
    text-decoration: none;
    padding: 0.8rem 1.2rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    background: var(--darker-bg);
}

.btn-back:hover {
    color: var(--accent-orange);
    border-color: var(--accent-orange);
    transform: translateY(-2px);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-weight: 500;
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: var(--error-red);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.form-card {
    background: var(--dark-bg);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    border: 1px solid var(--border-color);
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
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    background: var(--darker-bg);
    color: var(--white);
    border: 1px solid var(--border-color);
    padding: 1rem;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: var(--accent-orange);
    outline: none;
    box-shadow: 0 0 0 2px rgba(201, 123, 20, 0.1);
}

.form-group textarea {
    min-height: 150px;
    resize: vertical;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-cancel {
    padding: 0.8rem 1.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--darker-bg);
    color: var(--text-gray);
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.btn-cancel:hover {
    background: var(--dark-bg);
    color: var(--white);
    border-color: var(--accent-orange);
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
    font-weight: 500;
}

.btn-submit:hover {
    background: var(--accent-orange-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel, .btn-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include '../../includes/faculty_footer.php'; ?> 