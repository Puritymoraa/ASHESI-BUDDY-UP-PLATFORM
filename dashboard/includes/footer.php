<footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Ashesi Buddy-Up</h3>
                <p>Connecting continuing students with freshmen for mentorship and guidance.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../../index.html">Dashboard</a></li>
                    <li><a href="../student/mentees/index.php">Mentees</a></li>
                    <li><a href="../student/activities/index.php">Activities</a></li>
                    <li><a href="../student/blog/index.php">Blog</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: buddyup@ashesi.edu.gh</p>
                <p>Phone: +233 XX XXX XXXX</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Ashesi Buddy-Up. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .footer {
            background: var(--dark-gray);
            padding: 3rem 2rem 1rem;
            margin-top: 4rem;
            border-top: 1px solid rgba(201, 123, 20, 0.1);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3 {
            color: var(--accent-orange);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section p {
            color: var(--white);
            opacity: 0.8;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: var(--white);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer-section ul li a:hover {
            opacity: 1;
            color: var(--accent-orange);
        }

        .footer-bottom {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: var(--white);
            opacity: 0.6;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer {
                padding: 2rem 1rem 1rem;
            }
        }
    </style>
</body>
</html>