<?php
session_start();
require_once '../db/config.php';

// Update role validation to match the capitalized roles from choose_role.php
if(!isset($_GET['role']) || !in_array($_GET['role'], ['Freshman', 'Continuing', 'Faculty'])) {
    header('Location: choose_role.php');
    exit();
}
$role = $_GET['role'];

// Add this function to generate random avatar
function getRandomAvatar() {
    // Using DiceBear API for avatars
    $styles = ['adventurer', 'avataaars', 'big-ears', 'bottts', 'croodles', 'fun-emoji'];
    $style = $styles[array_rand($styles)];
    return "https://api.dicebear.com/6.x/{$style}/svg?seed=" . uniqid();
}

// Generate avatar URL for the session
$_SESSION['avatar_url'] = getRandomAvatar();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as <?php echo $role; ?> - Ashesi Buddy-Up</title>
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
            background-color: var(--primary-gray);
            color: var(--white);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: linear-gradient(to right, var(--primary-gray), var(--dark-gray));
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
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

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: var(--accent-orange);
        }

        .main-content {
            padding-top: 80px; /* Increased to account for navbar height */
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Changed from center to flex-start */
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: var(--dark-gray);
            border-radius: 20px;
            border: 1px solid rgba(201, 123, 20, 0.2);
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .left-section {
            flex: 1;
            padding: 3rem;
            background: linear-gradient(135deg, var(--dark-gray), var(--primary-gray));
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-section {
            flex: 1.5;
            padding: 3rem;
            background: var(--dark-gray);
            overflow-y: auto;
            max-height: 80vh;
        }

        h2 {
            color: var(--accent-orange);
            font-family: "DM Serif Display", serif;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: var(--accent-orange);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            background: rgba(250, 246, 247, 0.05);
            border: 1px solid rgba(201, 123, 20, 0.2);
            border-radius: 8px;
            color: var(--white);
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 2px rgba(201, 123, 20, 0.1);
        }

        select option {
            background: var(--dark-gray);
            color: var(--white);
        }

        .register-btn {
            width: 100%;
            padding: 1rem;
            background: var(--accent-orange);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background: #d68616;
            transform: translateY(-2px);
        }

        .error-message {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--white);
        }

        .login-link a {
            color: var(--accent-orange);
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .main-content {
                padding: 1rem;
                padding-top: 80px;
            }

            .container {
                margin: 0;
                border-radius: 0;
            }
        }

        .avatar-section {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 1rem;
            border: 3px solid var(--accent-orange);
            background: var(--white);
        }

        .refresh-avatar {
            background: var(--accent-orange);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .refresh-avatar:hover {
            transform: scale(1.05);
            background: #d68616;
        }

        .email-info-box {
            position: relative;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .email-info-box i {
            color: var(--accent-orange);
            cursor: pointer;
            font-size: 1.2rem;
        }

        .email-format-info {
            display: none;
            position: absolute;
            left: 30px;
            top: -5px;
            background: var(--dark-gray);
            border: 1px solid var(--accent-orange);
            padding: 1rem;
            border-radius: 8px;
            width: 300px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .email-info-box:hover .email-format-info {
            display: block;
        }

        .email-format-info strong {
            display: block;
            color: var(--accent-orange);
            margin-bottom: 0.5rem;
        }

        .email-format-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .email-format-info li {
            color: var(--white);
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .form-text {
            font-size: 0.8rem;
            color: var(--accent-orange);
            margin-top: 0.25rem;
            display: block;
        }

        /* Add animation for the info box */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .email-format-info {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.html" class="navbar-brand">
            <img src="../assets/img/logo.png" alt="Ashesi Buddy-Up" class="navbar-logo">
            Ashesi Buddy-Up
        </a>
        <div class="nav-buttons">
            <a href="login.php" class="nav-btn">Login</a>
            <a href="choose_role.php" class="nav-btn">Back</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="left-section">
                <div class="logo">
                    Ashesi Buddy-Up<span class="highlight"></span>
                </div>
                <h1>Welcome to Ashesi Buddy-Up</h1>
                <p>Join our community and connect with fellow students, mentors, and peers. Your journey starts here.</p>
            </div>

            <div class="right-section">
                <h2>Register as <?php echo $role; ?></h2>
                <p class="subtitle">Create your account to start networking and engaging</p>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <?php 
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="register_process.php" method="POST">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                    <input type="hidden" name="avatar_url" value="<?php echo $_SESSION['avatar_url']; ?>">
                    
                    <!-- Avatar Preview Section -->
                    <div class="avatar-section">
                        <img src="<?php echo $_SESSION['avatar_url']; ?>" alt="Your Avatar" class="avatar-preview">
                        <button type="button" class="refresh-avatar" onclick="refreshAvatar()">
                            <i class="fas fa-sync-alt"></i> New Avatar
                        </button>
                    </div>

                    <!-- Common fields for all users -->
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               placeholder="Enter your full name" 
                               required>
                    </div>

                    <!-- Student ID field for both Freshman and Continuing students -->
                    <?php if (in_array($role, ['Freshman', 'Continuing'])): ?>
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" 
                                   id="student_id" 
                                   name="student_id" 
                                   placeholder="Enter your Student ID" 
                                   pattern="[0-9]{8}" 
                                   title="Please enter your 8-digit student ID"
                                   required>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="email">Ashesi Email</label>
                        <div class="email-info-box">
                            <i class="fas fa-info-circle"></i>
                            <div class="email-format-info">
                                <strong>Valid Ashesi Email Formats:</strong>
                                <ul>
                                    <li>Students: firstname.lastname@ashesi.edu.gh</li>
                                    <li>Students: studentID@ashesi.edu.gh</li>
                                    <li>Faculty: firstname.lastname@ashesi.edu.gh</li>
                                </ul>
                            </div>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               placeholder="firstname.lastname@ashesi.edu.gh" 
                               required 
                               pattern="[a-zA-Z0-9._%+-]+@ashesi\.edu\.gh$"
                               title="Please use your Ashesi email address">
                        <small class="form-text">Must be a valid Ashesi email address</small>
                    </div>
                    
                    <!-- Fields for both Freshman and Continuing students -->
                    <?php if (in_array($role, ['Freshman', 'Continuing'])): ?>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" 
                                   id="age" 
                                   name="age" 
                                   placeholder="Enter your age" 
                                   min="16" 
                                   max="30" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="major">Major</label>
                            <select id="major" name="major" required>
                                <option value="">Select Major</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Management Information Systems">Management Information Systems</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" 
                                   id="nationality" 
                                   name="nationality" 
                                   placeholder="Enter your nationality" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="hobby">Hobby</label>
                            <input type="text" 
                                   id="hobby" 
                                   name="hobby" 
                                   placeholder="Tell us about your hobby">
                        </div>

                        <div class="form-group">
                            <label for="fun_fact">Fun Fact</label>
                            <input type="text" 
                                   id="fun_fact" 
                                   name="fun_fact" 
                                   placeholder="Share an interesting fact about yourself">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Faculty-specific fields -->
                    <?php if ($role == 'Faculty'): ?>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Humanities & Social Sciences">Humanities & Social Sciences</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="research_area">Research Area</label>
                            <input type="text" 
                                   id="research_area" 
                                   name="research_area" 
                                   placeholder="Enter your research area">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Password fields for all users -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Create a password" 
                               required 
                               minlength="8"
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                               title="Must contain at least one number, one uppercase and lowercase letter, and be at least 8 characters long">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Confirm your password" 
                               required
                               minlength="8">
                    </div>
                    
                    <button type="submit" class="register-btn">Create Account</button>
                </form>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Client-side password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        function validatePassword() {
            if (password.value != confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords do not match");
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);

        function refreshAvatar() {
            fetch('refresh_avatar.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.avatar-preview').src = data.avatar_url;
                    document.querySelector('input[name="avatar_url"]').value = data.avatar_url;
                });
        }

        // Optional: Add click handler for better mobile support
        document.querySelector('.email-info-box i').addEventListener('click', function(e) {
            const info = document.querySelector('.email-format-info');
            info.style.display = info.style.display === 'block' ? 'none' : 'block';
            e.stopPropagation();
        });

        // Close info box when clicking outside
        document.addEventListener('click', function() {
            document.querySelector('.email-format-info').style.display = 'none';
        });
    </script>
</body>
</html>