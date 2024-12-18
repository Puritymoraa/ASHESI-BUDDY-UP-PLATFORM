<div class="sidebar" id="sidebar">
    <div class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="logo">
        <img src="../../assets/img/logo.png" alt="Buddy-Up Logo">
        <span>Buddy-Up</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="../faculty/index.php" class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'faculty/index.php') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href=".../faculty/activities/index.php" class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'activities') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-tasks"></i>
            <span>Activities</span>
        </a>
        
        <a href="../faculty/profile/index.php" class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'profile') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../../auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
    .sidebar {
        position: fixed;
        left: -250px; /* Start hidden */
        top: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: var(--dark-gray);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        border-right: 1px solid rgba(201, 123, 20, 0.1);
        transition: left 0.3s ease;
    }

    .sidebar:hover {
        left: 0; /* Show on hover */
    }

    .sidebar-toggle {
        position: absolute;
        right: -40px;
        top: 20px;
        width: 40px;
        height: 40px;
        background: var(--dark-gray);
        border-radius: 0 8px 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--white);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo img {
        height: 40px;
    }

    .logo span {
        color: var(--accent-orange);
        font-family: 'DM Serif Display', serif;
        font-size: 1.5rem;
    }

    .sidebar-nav {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        color: var(--white);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-item:hover, .nav-item.active {
        background: rgba(201, 123, 20, 0.1);
        color: var(--accent-orange);
    }

    .mentee-badge {
        position: absolute;
        right: 1rem;
        background: var(--accent-orange);
        color: var(--white);
        padding: 0.2rem 0.6rem;
        border-radius: 12px;
        font-size: 0.8rem;
    }

    .sidebar-footer {
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        color: var(--white);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Update main content margin */
    .main-content {
        margin-left: 40px; /* Only show toggle button width by default */
        transition: margin-left 0.3s ease;
    }

    /* When sidebar is hovered */
    .sidebar:hover + .main-content {
        margin-left: var(--sidebar-width);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if(sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
            mainContent.style.marginLeft = '40px';
        } else {
            sidebar.style.left = '0px';
            mainContent.style.marginLeft = 'var(--sidebar-width)';
        }
    }
</script> 