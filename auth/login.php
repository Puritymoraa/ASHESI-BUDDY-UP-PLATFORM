<?php
session_start();
require_once '../db/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ashesi Buddy-Up Program</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Arimo:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            background-color: var(--primary-gray);
            color: var(--white);
            min-height: 100vh;
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

        .nav-btn {
            color: var(--white);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .nav-btn:hover {
            background: var(--accent-orange);
        }

        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 2rem;
            background: var(--dark-gray);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(201, 123, 20, 0.2);
        }

        .logo {
            text-align: center;
            color: var(--accent-orange);
            font-size: 2rem;
            margin-bottom: 2rem;
            font-family: "DM Serif Display", serif;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--accent-orange);
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(201, 123, 20, 0.2);
            border-radius: 5px;
            background: var(--primary-gray);
            color: var(--white);
        }

        input:focus {
            outline: none;
            border-color: var(--accent-orange);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .remember-me input {
            width: auto;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: var(--accent-orange);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #b36d11;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .register-link a {
            color: var(--accent-orange);
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .admin-note {
            font-size: 0.9rem;
            color: var(--accent-orange);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.html" class="navbar-brand">
            <img src="../assets/img/logo.png" alt="Ashesi Buddy-Up" class="navbar-logo">
            Ashesi Buddy-Up
        </a>
        <div>
            <a href="choose_role.php" class="nav-btn">Register</a>
            <a href="../index.html" class="nav-btn">Back</a>
        </div>
    </nav>

    <div class="container">
        <div class="logo">
            Welcome Back!
            <?php if (isset($_GET['admin'])): ?>
                <div class="admin-note">Admin Portal</div>
            <?php endif; ?>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       placeholder="Enter your Ashesi email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Enter your password">
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="choose_role.php">Register</a>
        </div>
    </div>
</body>
</html>
