<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ashesi Buddy-Up</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Arimo:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Dark theme colors */
            --darker-bg: #121212;        /* Darkest - for main background */
            --dark-bg: #1E1E1E;         /* Dark - for cards */
            --medium-bg: #252525;       /* Medium - for elements */
            --light-bg: #2D2D2D;        /* Light - for hover states */
            --lighter-bg: #333333;      /* Lighter - for borders */
            
            /* Text colors */
            --text-primary: #FFFFFF;    /* White text */
            --text-secondary: #B3B3B3;  /* Light gray text */
            --text-muted: #808080;      /* Muted text */
            
            /* Accent colors */
            --accent-orange: #C97B14;   /* Primary accent */
            --accent-orange-light: #E69B3A;
            --accent-orange-dark: #A66610;
            
            /* Layout */
            --sidebar-width: 250px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arimo", sans-serif;
            background-color: var(--medium-bg); /* This sets the global background */
            color: var(--text-primary);
            min-height: 100vh;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: var(--dark-bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            z-index: 1000;
            border-bottom: 1px solid var(--lighter-bg);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            height: 50px;
            width: auto;
        }

        .brand-text {
            font-family: 'DM Serif Display', serif;
            color: var(--accent-orange);
            font-size: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .brand-text small {
            font-size: 0.8rem;
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            background: var(--card-bg);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: var(--card-bg);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 1rem;
        }

        .user-role {
            color: var(--accent-orange);
            font-size: 0.8rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--accent-orange);
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(var(--header-height) - 10px);
            right: 2rem;
            background: var(--dark-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            min-width: 200px;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .dropdown-menu a:hover {
            background: var(--card-bg);
            color: var(--accent-orange);
        }

        /* Ensure content below header is not covered */
        .main-content {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--header-height));
            padding: 2rem;
            background: var(--medium-bg);
        }

        /* Add a subtle gradient to the background for more depth */
        .main-content::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--medium-bg), var(--dark-bg));
            z-index: -1;
        }

        @media (max-width: 768px) {
            .main-header {
                left: 0;
                padding: 0 1rem;
            }

            .brand-text {
                display: none;
            }

            .user-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <div class="brand-section">
                <img src="../../assets/img/logo.png" alt="Logo" class="logo">
                <div class="brand-text">
                    Buddy-Up
                    <small>Ashesi University</small>
                </div>
            </div>
        </div>

        <div class="header-right">
            <div class="user-profile" onclick="toggleDropdown()">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></div>
                </div>
                <div class="user-avatar">
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar_url'] ?? '../../assets/img/default-avatar.png'); ?>" 
                         alt="Profile Avatar">
                </div>
            </div>

            <div class="dropdown-menu" id="userDropdown">
            
                </a>
                <a href="../../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const userProfile = document.querySelector('.user-profile');
            if (!userProfile.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>