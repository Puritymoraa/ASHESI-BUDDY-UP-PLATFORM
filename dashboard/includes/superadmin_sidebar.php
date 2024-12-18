<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-shield-alt"></i>
        <span>Admin Panel</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="../superadmin/index.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section">
            <div class="section-title">User Management</div>
            <a href="../superadmin/users/freshmen.php" class="nav-link <?php echo $current_page == 'freshmen' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate"></i>
                <span>Freshmen</span>
            </a>
            <a href="../superadmin/users/continuing.php" class="nav-link <?php echo $current_page == 'continuing' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Continuing Students</span>
            </a>
            <a href="../superadmin/users/faculty.php" class="nav-link <?php echo $current_page == 'faculty' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Faculty</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="section-title">Content Management</div>
            <a href="../superadmin/activities/index.php" class="nav-link <?php echo $current_page == 'activities' ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i>
                <span>Activities</span>
            </a>
            <a href="../superadmin/blogs/index.php" class="nav-link <?php echo $current_page == 'blogs' ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i>
                <span>Blog Posts</span>
            </a>
            <a href="../../auth/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>

        

      
    </nav>
</div>

<style>
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: var(--sidebar-width);
    background: var(--dark-bg);
    border-right: 1px solid var(--border-color);
    padding: 1rem;
    z-index: 1000;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    color: var(--accent-orange);
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 2rem;
}

.nav-section {
    margin-bottom: 2rem;
}

.section-title {
    color: var(--text-secondary);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0.5rem 1rem;
    margin-bottom: 0.5rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.8rem 1rem;
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: var(--medium-bg);
    color: var(--accent-orange);
}

.nav-link.active {
    background: var(--accent-orange);
    color: var(--text-primary);
}

.nav-link i {
    width: 20px;
    text-align: center;
}
</style> 