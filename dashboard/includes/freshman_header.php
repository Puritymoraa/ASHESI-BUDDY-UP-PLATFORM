<?php
require_once __DIR__ . '/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freshman Dashboard - Ashesi Buddy-Up</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Arimo:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gray: #535555;
            --dark-gray: #0a0a0a;
            --accent-orange: #c97b14;
            --white: #faf6f7;
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arimo", sans-serif;
            background-color: var(--primary-gray);
            color: var(--white);
        }

        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: var(--dark-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .header-title {
            color: var(--accent-orange);
            font-family: "DM Serif Display", serif;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            color: var(--white);
            font-weight: 500;
        }

        .user-role {
            color: var(--accent-orange);
            font-size: 0.8rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-orange);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar i {
            font-size: 1.5rem;
            color: var(--white);
        }

        @media (max-width: 768px) {
            .header {
                left: 0;
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1 class="header-title">Freshman Dashboard</h1>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                <div class="user-role">Freshman</div>
            </div>
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>
</body>
</html> 