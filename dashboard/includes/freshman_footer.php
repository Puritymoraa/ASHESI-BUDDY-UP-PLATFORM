        <!-- Toast Messages Container -->
        <div id="toast-container"></div>

        <footer class="footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> Ashesi Buddy-Up. All rights reserved.</p>
            </div>
        </footer>

        <script>
            // Toast notification function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                                      type === 'error' ? 'fa-exclamation-circle' : 
                                      'fa-info-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                document.getElementById('toast-container').appendChild(toast);
                
                // Animate in
                setTimeout(() => toast.classList.add('show'), 100);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Mobile sidebar toggle
            document.addEventListener('DOMContentLoaded', function() {
                const menuToggle = document.querySelector('.menu-toggle');
                const sidebar = document.querySelector('.sidebar');
                
                if (menuToggle) {
                    menuToggle.addEventListener('click', () => {
                        sidebar.classList.toggle('active');
                    });
                }

                // Close sidebar when clicking outside
                document.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768 && 
                        !e.target.closest('.sidebar') && 
                        !e.target.closest('.menu-toggle')) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Show PHP session messages as toasts
            <?php
            if (isset($_SESSION['success'])) {
                echo "showToast('" . addslashes($_SESSION['success']) . "', 'success');";
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo "showToast('" . addslashes($_SESSION['error']) . "', 'error');";
                unset($_SESSION['error']);
            }
            ?>
        </script>

        <style>
            .footer {
                margin-left: var(--sidebar-width);
                padding: 1rem;
                text-align: center;
                background: var(--dark-gray);
                color: var(--white);
            }

            #toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
            }

            .toast {
                background: var(--dark-gray);
                color: var(--white);
                padding: 1rem;
                border-radius: 8px;
                margin-bottom: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                transform: translateX(120%);
                transition: transform 0.3s ease;
                display: flex;
                align-items: center;
                min-width: 300px;
            }

            .toast.show {
                transform: translateX(0);
            }

            .toast-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .toast-success {
                border-left: 4px solid #4CAF50;
            }

            .toast-error {
                border-left: 4px solid #f44336;
            }

            .toast-info {
                border-left: 4px solid #2196F3;
            }

            @media (max-width: 768px) {
                .footer {
                    margin-left: 0;
                }

                #toast-container {
                    left: 20px;
                    right: 20px;
                }

                .toast {
                    min-width: auto;
                }
            }
        </style>
    </body>
</html> 