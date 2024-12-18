<?php
require_once '../../includes/auth_check.php';
require_once '../../../db/database.php';
checkRole(['Continuing']);

$user_id = $_SESSION['user_id'];

try {
    // Get filter parameters
    $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
    $semester = filter_input(INPUT_GET, 'semester', FILTER_SANITIZE_STRING);
    $mentee_id = filter_input(INPUT_GET, 'mentee_id', FILTER_SANITIZE_NUMBER_INT);

    // Base query for activities
    $query = "SELECT * FROM activities WHERE 1=1";
    $params = [];

    // Add filters if set
    if ($semester) {
        $query .= " AND semester = ?";
        $params[] = $semester;
    }

    $query .= " ORDER BY creation_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $activities = $stmt->fetchAll();

    // Get unique semesters for filter
    $stmt = $pdo->prepare("SELECT DISTINCT semester FROM activities ORDER BY semester DESC");
    $stmt->execute();
    $semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get completed activities for this mentor
    $stmt = $pdo->prepare("
        SELECT ac.*, a.activity_name, a.description, 
               f.full_name as mentee_name
        FROM activity_completions ac
        JOIN activities a ON ac.activity_id = a.activity_id
        JOIN mentorship m ON ac.mentorship_id = m.mentorship_id
        JOIN freshman_details f ON m.freshman_id = f.user_id
        WHERE m.continuing_id = ?
        ORDER BY ac.completion_date DESC
    ");
    $stmt->execute([$user_id]);
    $completed_activities = $stmt->fetchAll();

    // Get current mentees for activity selection
    $stmt = $pdo->prepare("
        SELECT m.mentorship_id, f.full_name, f.user_id
        FROM mentorship m
        JOIN freshman_details f ON m.freshman_id = f.user_id
        WHERE m.continuing_id = ? AND m.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $mentees = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching activities.";
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="navigation-buttons">
        <a href="../index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <a href="feed.php" class="btn btn-secondary">
            <i class="fas fa-globe"></i> View Public Activity Feed
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="activities-container">
        <!-- Add Filter Section -->
        <div class="filters-section">
            <form action="" method="GET" class="filters-form">
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select name="semester" id="semester">
                        <option value="">All Semesters</option>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php echo ($semester === $sem) ? 'selected' : ''; ?>>
                                <?php echo $sem; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mentee_filter">Mentee</label>
                    <select name="mentee_id" id="mentee_filter">
                        <option value="">All Mentees</option>
                        <?php foreach ($mentees as $mentee): ?>
                            <option value="<?php echo $mentee['user_id']; ?>" 
                                    <?php echo ($mentee_id == $mentee['user_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mentee['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </form>
        </div>

        <!-- Add Search Section -->
        <div class="search-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="activity-search" placeholder="Search activities...">
            </div>
            <div class="activity-count">
                Showing <span id="activity-count">0</span> activities
            </div>
        </div>

        <!-- Record New Activity Section -->
        <div class="section">
            <h2>Record New Activity</h2>
            <form action="record_activity.php" method="POST" class="activity-form">
                <div class="form-group">
                    <label for="activity_id">Select Activity</label>
                    <select name="activity_id" id="activity_id" required>
                        <option value="">Choose an activity...</option>
                        <?php foreach ($activities as $activity): ?>
                            <option value="<?php echo $activity['activity_id']; ?>">
                                <?php echo htmlspecialchars($activity['activity_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mentee_id">Select Mentee</label>
                    <select name="mentorship_id" id="mentee_id" required>
                        <option value="">Choose a mentee...</option>
                        <?php foreach ($mentees as $mentee): ?>
                            <option value="<?php echo $mentee['mentorship_id']; ?>">
                                <?php echo htmlspecialchars($mentee['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="experience">Share Your Experience</label>
                    <textarea name="experience" id="experience" rows="4" required 
                              placeholder="Describe how the activity went..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record Activity
                </button>
            </form>
        </div>

        <!-- Activity History Section -->
        <div class="section">
            <h2>Activity History</h2>
            <div class="activity-grid">
                <?php if (count($completed_activities) > 0): ?>
                    <?php foreach ($completed_activities as $activity): ?>
                        <div class="activity-card">
                            <div class="activity-actions">
                                <a href="edit_activity.php?id=<?php echo $activity['completion_id']; ?>" 
                                   class="btn btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="delete_activity.php" method="POST" class="delete-form" 
                                      onsubmit="return confirm('Are you sure you want to delete this activity?');">
                                    <input type="hidden" name="completion_id" value="<?php echo $activity['completion_id']; ?>">
                                    <button type="submit" class="btn btn-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <h3><?php echo htmlspecialchars($activity['activity_name']); ?></h3>
                            <p class="mentee">With: <?php echo htmlspecialchars($activity['mentee_name']); ?></p>
                            <p class="date">
                                <?php echo date('M d, Y', strtotime($activity['completion_date'])); ?>
                            </p>
                            <p class="experience"><?php echo htmlspecialchars($activity['experience']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-activities">No activities recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Public Feed Link -->
        <div class="feed-section">
            <a href="feed.php" class="btn btn-secondary">
                <i class="fas fa-globe"></i> View Public Activity Feed
            </a>
        </div>
    </div>
</div>

<style>
    .activities-container {
        display: grid;
        gap: 2rem;
    }

    .section {
        background: var(--dark-gray);
        padding: 2rem;
        border-radius: 15px;
    }

    .section h2 {
        color: var(--accent-orange);
        margin-bottom: 1.5rem;
    }

    .activity-form {
        display: grid;
        gap: 1.5rem;
    }

    .form-group {
        display: grid;
        gap: 0.5rem;
    }

    .form-group label {
        color: var(--white);
        font-size: 0.9rem;
    }

    select, textarea {
        padding: 0.8rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: var(--dark-gray);
        color: var(--white);
        width: 100%;
    }

    textarea {
        resize: vertical;
    }

    .activity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .activity-card {
        background: rgba(255, 255, 255, 0.05);
        padding: 1.5rem;
        border-radius: 8px;
    }

    .activity-card h3 {
        color: var(--accent-orange);
        margin-bottom: 0.5rem;
    }

    .mentee, .date {
        color: var(--white);
        opacity: 0.8;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .experience {
        color: var(--white);
        line-height: 1.5;
    }

    .btn-primary {
        background: var(--accent-orange);
        color: var(--white);
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .activity-grid {
            grid-template-columns: 1fr;
        }
    }

    .filters-section {
        margin-bottom: 2rem;
        background: var(--dark-gray);
        padding: 1.5rem;
        border-radius: 15px;
    }

    .filters-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }

    .activity-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit, .btn-delete {
        padding: 0.5rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: var(--accent-orange);
        color: var(--white);
    }

    .btn-delete {
        background: #dc3545;
        color: var(--white);
    }

    .feed-section {
        margin-top: 2rem;
        text-align: center;
    }

    .search-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 300px;
    }

    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--white);
        opacity: 0.6;
    }

    .search-box input {
        width: 100%;
        padding: 0.8rem 1rem;
    }

    /* Add styles for select options */
    select option {
        background: var(--dark-gray);
        color: var(--white);
        padding: 0.5rem;
    }

    /* Add hover effect for options */
    select option:hover {
        background: var(--accent-orange);
    }

    /* Fix header overlap by adding margin/padding to main-content */
    .main-content {
        padding-top: 80px; /* Adjust this value based on your header height */
        margin-left: var(--sidebar-width);
        padding-right: 2rem;
        padding-bottom: 2rem;
    }

    .navigation-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        background: var(--dark-gray);
        color: var(--accent-orange);
        text-decoration: none;
        border: 1px solid var(--accent-orange);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: var(--accent-orange);
        color: var(--white);
        transform: translateY(-2px);
    }
</style>

<script>
// Delete confirmation and AJAX handling
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This activity record will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const formData = new FormData(form);
                const response = await fetch('delete_activity.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Remove the activity card with animation
                    const card = form.closest('.activity-card');
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Show success message
                        Swal.fire(
                            'Deleted!',
                            'The activity has been deleted.',
                            'success'
                        );
                        // Update activity count if needed
                        updateActivityCount();
                    }, 300);
                } else {
                    throw new Error(data.error || 'Failed to delete activity');
                }
            } catch (error) {
                Swal.fire(
                    'Error!',
                    error.message,
                    'error'
                );
            }
        }
    });
});

// Real-time search functionality
const searchInput = document.getElementById('activity-search');
const activityCards = document.querySelectorAll('.activity-card');

searchInput.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    
    activityCards.forEach(card => {
        const activityName = card.querySelector('h3').textContent.toLowerCase();
        const experience = card.querySelector('.experience').textContent.toLowerCase();
        
        if (activityName.includes(searchTerm) || experience.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Function to update activity count
function updateActivityCount() {
    const count = document.querySelectorAll('.activity-card:not([style*="display: none"])').length;
    document.getElementById('activity-count').textContent = count;
}

// Add this to your existing JavaScript
document.querySelector('.activity-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('record_activity.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            // Clear form
            e.target.reset();
            
            // Show success message
            Swal.fire({
                title: 'Success!',
                text: 'Activity recorded successfully!',
                icon: 'success',
                showConfirmButton: true,
                confirmButtonText: 'View in Feed',
                showCancelButton: true,
                cancelButtonText: 'Record Another'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'feed.php';
                }
            });
            
            // Update activity count
            updateActivityCount();
        } else {
            throw new Error(data.error || 'Failed to record activity');
        }
    } catch (error) {
        Swal.fire(
            'Error!',
            error.message,
            'error'
        );
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
