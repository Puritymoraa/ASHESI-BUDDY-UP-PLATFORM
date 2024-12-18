<div class="sidebar" id="sidebar">
    <div class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="sidebar-header">
        <img src="../../assets/img/logo.png" alt="Logo" class="logo">
        <h2>Buddy-Up</h2>
    </div>
    
    <nav class="sidebar-nav">
        <a href="../freshman/index.php" class="sidebar-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="../freshman/profile/index.php" class="sidebar-link <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="../freshman/blog/index.php" class="sidebar-link <?php echo $current_page === 'blog' ? 'active' : ''; ?>">
            <i class="fas fa-blog"></i>
            <span>Blog</span>
        </a>
        <a href="../freshman/messages.php" class="sidebar-link <?php echo $current_page === 'messages' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i>
            <span>Messages</span>
        </a>
        <a href="../../auth/logout.php" class="sidebar-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<style>
.sidebar {
    position: fixed;
    left: -250px;
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
    left: 0;
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

.main-content {
    margin-left: 40px;
    transition: margin-left 0.3s ease;
}

.sidebar:hover + .main-content {
    margin-left: var(--sidebar-width);
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
    width: 40px;
    height: 40px;
}

.sidebar-header h2 {
    color: var(--accent-orange);
    font-family: "DM Serif Display", serif;
    font-size: 1.5rem;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 2rem;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    color: var(--white);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar-link:hover, .sidebar-link.active {
    background: rgba(201, 123, 20, 0.1);
    color: var(--accent-orange);
}

.sidebar-link i {
    width: 20px;
    text-align: center;
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