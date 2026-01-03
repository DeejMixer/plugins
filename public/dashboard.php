<?php
// Protect user dashboard - require authentication
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Mixlar Marketplace</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Check authentication on page load
        (function() {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');

            if (!token) {
                // Not authenticated - redirect to login
                window.location.href = 'login.html';
            }
        })();
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo" onclick="window.location.href='/'">
                    <i class="fa-solid fa-cube"></i>
                    <span>Mixlar Marketplace</span>
                </div>
                <div class="nav-links">
                    <a href="index.html">Browse Plugins</a>
                    <a href="dashboard.php" class="active">My Dashboard</a>
                </div>
                <div class="nav-auth">
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar" id="userAvatar">U</div>
                            <span id="userName">User</span>
                        </div>
                    </div>
                    <button class="btn btn-secondary" onclick="logout()">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <!-- Welcome Header -->
        <div class="admin-header">
            <div class="container">
                <h1>Welcome back, <span id="welcomeName">User</span>! 👋</h1>
                <p style="color: var(--text-secondary);">Manage your account and explore new plugins</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="admin-stats" style="margin-bottom: 3rem;">
            <div class="stat-card">
                <h3>My Plugins</h3>
                <div class="number" id="myPluginsCount">0</div>
            </div>
            <div class="stat-card">
                <h3>Total Downloads</h3>
                <div class="number" id="totalDownloads">0</div>
            </div>
            <div class="stat-card">
                <h3>Account Status</h3>
                <div class="number" style="font-size: 1.5rem; color: var(--success);">Active</div>
            </div>
        </div>

        <!-- Dashboard Sections -->
        <div class="admin-tabs">
            <button class="tab-btn active" data-tab="profile">
                <i class="fa-solid fa-user"></i> Profile
            </button>
            <button class="tab-btn" data-tab="myplugins">
                <i class="fa-solid fa-puzzle-piece"></i> My Plugins
            </button>
            <button class="tab-btn" data-tab="settings">
                <i class="fa-solid fa-gear"></i> Settings
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Profile Tab -->
            <div class="tab-pane active" id="profile-tab">
                <div class="table-container" style="padding: 2rem;">
                    <h2 style="margin-bottom: 1.5rem;">My Profile</h2>

                    <div style="max-width: 600px;">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="profileUsername" readonly style="background: var(--bg-darker);">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="profileEmail" readonly style="background: var(--bg-darker);">
                        </div>

                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text" id="memberSince" readonly style="background: var(--bg-darker);">
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" id="profileRole" readonly style="background: var(--bg-darker); text-transform: capitalize;">
                        </div>

                        <div style="margin-top: 2rem;">
                            <button class="btn btn-primary" onclick="window.location.href='index.html'">
                                <i class="fa-solid fa-compass"></i> Browse Plugins
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Plugins Tab -->
            <div class="tab-pane" id="myplugins-tab" style="display: none;">
                <div class="table-container">
                    <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
                        <h2>My Submitted Plugins</h2>
                        <button class="btn btn-primary" onclick="alert('Plugin submission coming soon!')">
                            <i class="fa-solid fa-plus"></i> Submit Plugin
                        </button>
                    </div>

                    <div id="myPluginsList" style="padding: 2rem;">
                        <p style="text-align: center; color: var(--text-secondary);">
                            <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            You haven't submitted any plugins yet.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane" id="settings-tab" style="display: none;">
                <div class="table-container" style="padding: 2rem;">
                    <h2 style="margin-bottom: 1.5rem;">Account Settings</h2>

                    <div style="max-width: 600px;">
                        <h3 style="margin-bottom: 1rem; color: var(--primary);">Change Password</h3>

                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" id="currentPassword" placeholder="Enter current password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="newPassword" placeholder="Enter new password">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password">
                        </div>

                        <div class="error-message hidden" id="passwordError"></div>
                        <div class="success-message hidden" id="passwordSuccess"></div>

                        <button class="btn btn-primary" onclick="changePassword()">
                            <i class="fa-solid fa-key"></i> Update Password
                        </button>

                        <hr style="margin: 2rem 0; border: 1px solid var(--border-color);">

                        <h3 style="margin-bottom: 1rem; color: var(--danger);">Danger Zone</h3>
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        <button class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete your account? This cannot be undone!')) alert('Account deletion coming soon!')">
                            <i class="fa-solid fa-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/auth.js"></script>
    <script>
        // Load user data on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadUserProfile();
            setupTabs();
        });

        function loadUserProfile() {
            const user = getCurrentUser();
            if (user) {
                document.getElementById('userAvatar').textContent = user.username.charAt(0).toUpperCase();
                document.getElementById('userName').textContent = user.username;
                document.getElementById('welcomeName').textContent = user.username;
                document.getElementById('profileUsername').value = user.username;
                document.getElementById('profileEmail').value = user.email;
                document.getElementById('profileRole').value = user.role;

                // Mock data for now
                document.getElementById('memberSince').value = 'January 2024';
                document.getElementById('myPluginsCount').textContent = '0';
                document.getElementById('totalDownloads').textContent = '0';
            }
        }

        function setupTabs() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');

                    // Remove active class from all buttons and panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active');
                        pane.style.display = 'none';
                    });

                    // Add active class to clicked button and corresponding pane
                    button.classList.add('active');
                    const targetPane = document.getElementById(targetTab + '-tab');
                    targetPane.classList.add('active');
                    targetPane.style.display = 'block';
                });
            });
        }

        async function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorDiv = document.getElementById('passwordError');
            const successDiv = document.getElementById('passwordSuccess');

            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            // Validation
            if (!currentPassword || !newPassword || !confirmPassword) {
                errorDiv.textContent = 'Please fill in all password fields';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (newPassword !== confirmPassword) {
                errorDiv.textContent = 'New passwords do not match';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (newPassword.length < 6) {
                errorDiv.textContent = 'New password must be at least 6 characters';
                errorDiv.classList.remove('hidden');
                return;
            }

            // TODO: Implement actual password change API call
            successDiv.textContent = 'Password change functionality coming soon!';
            successDiv.classList.remove('hidden');

            // Clear fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }
    </script>
</body>
</html>
