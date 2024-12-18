<?php
session_start();
require_once '../db/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Role - Ashesi Buddy-Up</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Arimo:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gray: #535555;
            --dark-gray: #0a0a0a;
            --accent-orange: #c97b14;
            --white: #faf6f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arimo", sans-serif;
            background: var(--primary-gray);
            color: var(--white);
            min-height: 100vh;
            overflow: hidden;
        }

        .navbar {
            background: linear-gradient(to right, var(--primary-gray), var(--dark-gray));
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .navbar-brand {
            color: var(--accent-orange);
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .navbar-logo {
            height: 40px;
            margin-right: 10px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 70px);
            padding: 2rem;
            gap: 2rem;
            position: relative;
            z-index: 1;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .role-card {
            background: var(--dark-gray);
            border-radius: 15px;
            padding: 2rem;
            width: 350px;
            height: 350px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .role-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-orange);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .role-icon {
            font-size: 3rem;
            color: var(--accent-orange);
            margin-bottom: 1rem;
        }

        h2 {
            color: var(--white);
            margin-bottom: 1rem;
            font-family: "DM Serif Display", serif;
        }

        p {
            color: var(--white);
            opacity: 0.8;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            max-width: 100%;
            height: auto;
            overflow: hidden;
        }

        .choose-button {
            display: inline-block;
            background: var(--accent-orange);
            color: var(--white);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .choose-button:hover {
            background: var(--white);
            color: var(--dark-gray);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .role-card {
                width: 100%;
                max-width: 300px;
            }
        }

        .video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            opacity: 0.3;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <video autoplay muted loop class="video-background">
        <source src="../assets/img/dark.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <nav class="navbar">
        <a href="../index.html" class="navbar-brand">
            <img src="../assets/img/logo.png" alt="Ashesi Buddy-Up" class="navbar-logo">
            Ashesi Buddy-Up
        </a>
    </nav>

    <div class="container">
        <div class="role-card">
            <i class="fas fa-user-graduate role-icon"></i>
            <div>
                <h2>Freshman</h2>
                <p>New to Ashesi? Join as a freshman to get paired with experienced buddies and access resources to help you succeed.</p>
            </div>
            <a href="register.php?role=Freshman" class="choose-button">Choose Freshman</a>
        </div>

        <div class="role-card">
            <i class="fas fa-users role-icon"></i>
            <div>
                <h2>Continuing Student</h2>
                <p>Share your experience and guide freshmen as they begin their Ashesi journey. Become a buddy today!</p>
            </div>
            <a href="register.php?role=Continuing" class="choose-button">Choose Continuing Student</a>
        </div>

        <div class="role-card">
            <i class="fas fa-chalkboard-teacher role-icon"></i>
            <div>
                <h2>Faculty</h2>
                <p>Mentor students and help shape their academic journey through guidance and support.</p>
            </div>
            <a href="register.php?role=Faculty" class="choose-button">Choose Faculty</a>
        </div>
    </div>
</body>
</html>
