<footer class="faculty-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="../faculty/activities/create.php">Create Activity</a></li>
                <li><a href="../faculty/mentees/select.php">Select Mentees</a></li>
                <li><a href="../faculty/profile/index.php">Update Profile</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Support</h4>
            <ul>
                <li><a href="mailto:support@buddyup.com">Faculty Support</a></li>
                <li><a href="#" onclick="showGuide()">Mentoring Guide</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Faculty Resources</h4>
            <ul>
                <li><a href="#" onclick="showMentoringTips()">Mentoring Best Practices</a></li>
                <li><a href="#" onclick="showActivityGuidelines()">Activity Guidelines</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Buddy-Up Faculty Portal | Ashesi University</p>
    </div>
</footer>

<style>
.faculty-footer {
    background: var(--dark-bg);
    padding: 2rem 0;
    margin-top: 3rem;
    border-top: 1px solid rgba(201, 123, 20, 0.1);
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.footer-section h4 {
    color: var(--accent-orange);
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section ul li a {
    color: var(--text-gray);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.footer-section ul li a:hover {
    color: var(--accent-orange);
    padding-left: 5px;
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom p {
    color: var(--text-gray);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .footer-section {
        padding: 1rem 0;
    }
}
</style>

<script>
function showGuide() {
    alert('Mentoring guide will be available soon!');
}

function showMentoringTips() {
    alert('Mentoring best practices guide will be available soon!');
}

function showActivityGuidelines() {
    alert('Activity guidelines document will be available soon!');
}
</script> 