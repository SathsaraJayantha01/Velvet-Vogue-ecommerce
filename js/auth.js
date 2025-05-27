/**
 * Velvet Vogue Authentication System
 * Handles user login, registration, session management and dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in
    checkAuthStatus();
    
    // Initialize tab functionality
    initAuthTabs();
    
    // Initialize registration form
    initRegistrationForm();
    
    // Initialize login form
    initLoginForm();
    
    // Initialize logout button
    initLogoutButton();
});

/**
 * Check if user is already logged in
 */
function checkAuthStatus() {
    // Send AJAX request to check authentication status
    fetch('auth.php', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.logged_in) {
            // User is logged in, show dashboard
            showDashboard(data.user);
        } else {
            // User is not logged in, show login form
            showLoginForm();
        }
    })
    .catch(error => {
        console.error('Authentication check error:', error);
        showLoginForm(); // Fallback to login form
    });
}

/**
 * Show dashboard for logged in user
 * @param {Object} user - User data
 */
function showDashboard(user) {
    const authContainer = document.getElementById('auth-container');
    const dashboardContainer = document.getElementById('dashboard-container');
    
    if (authContainer && dashboardContainer) {
        // Hide auth container
        authContainer.style.display = 'none';
        
        // Create dashboard HTML if it doesn't exist
        if (dashboardContainer.children.length === 0) {
            dashboardContainer.innerHTML = generateDashboardHTML(user);
            
            // Initialize dashboard functionality
            initDashboardFunctionality();
        }
        
        // Show dashboard container
        dashboardContainer.style.display = 'block';
    }
}

/**
 * Generate HTML for the user dashboard
 * @param {Object} user - User data
 * @returns {string} Dashboard HTML
 */
function generateDashboardHTML(user) {
    return `
        <div class="dashboard-container">
            <aside class="dashboard-sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="images/user-avatar.jpg" alt="${user.first_name} ${user.last_name}">
                    </div>
                    <div class="user-details">
                        <h3>${user.first_name} ${user.last_name}</h3>
                        <p>${user.email}</p>
                    </div>
                </div>
                <ul class="dashboard-menu">
                    <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#" data-tab="orders"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="#" data-tab="addresses"><i class="fas fa-map-marker-alt"></i> Addresses</a></li>
                    <li><a href="#" data-tab="wishlist"><i class="fas fa-heart"></i> Wishlist</a></li>
                    <li><a href="#" data-tab="account-details"><i class="fas fa-user-cog"></i> Account Details</a></li>
                    <li><a href="#" id="dashboard-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </aside>
            <div class="dashboard-content">
                <div class="dashboard-tab active" id="dashboard-tab">
                    <h2>Dashboard</h2>
                    <p>Hello ${user.first_name} ${user.last_name} (not ${user.first_name}? <a href="#" id="logout-btn">Log out</a>)</p>
                    <p>From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                    
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Orders</h3>
                                <p>0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Wishlist</h3>
                                <p>0</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Addresses</h3>
                                <p>0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="recent-orders">
                        <h3>Recent Orders</h3>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="no-orders">
                                    <td colspan="5">No order has been made yet.</td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="#" class="view-all-link" data-tab="orders">View All Orders</a>
                    </div>
                </div>
                
                <div class="dashboard-tab" id="orders-tab">
                    <h2>Orders</h2>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="no-orders">
                                <td colspan="5">No order has been made yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="dashboard-tab" id="addresses-tab">
                    <h2>Addresses</h2>
                    <p>The following addresses will be used on the checkout page by default.</p>
                    
                    <div class="addresses-container">
                        <div class="address-box">
                            <div class="address-header">
                                <h3>Billing Address</h3>
                                <a href="#" class="edit-address" data-type="billing">Edit</a>
                            </div>
                            <div class="address-content">
                                <p>You have not set up this type of address yet.</p>
                            </div>
                        </div>
                        
                        <div class="address-box">
                            <div class="address-header">
                                <h3>Shipping Address</h3>
                                <a href="#" class="edit-address" data-type="shipping">Edit</a>
                            </div>
                            <div class="address-content">
                                <p>You have not set up this type of address yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-tab" id="wishlist-tab">
                    <h2>Wishlist</h2>
                    <div class="wishlist-items">
                        <div class="product-grid" id="wishlist-products"></div>
                    </div>
                </div>
                
                <div class="dashboard-tab" id="account-details-tab">
                    <h2>Account Details</h2>
                    <form id="account-details-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="account-first-name">First Name *</label>
                                <input type="text" id="account-first-name" name="first_name" required value="${user.first_name}">
                            </div>
                            <div class="form-group">
                                <label for="account-last-name">Last Name *</label>
                                <input type="text" id="account-last-name" name="last_name" required value="${user.last_name}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="account-display-name">Display Name *</label>
                            <input type="text" id="account-display-name" name="display_name" required value="${user.username}">
                            <small>This will be how your name will be displayed in the account section and in reviews</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="account-email">Email Address *</label>
                            <input type="email" id="account-email" name="email" required value="${user.email}">
                        </div>
                        
                        <h3>Password Change</h3>
                        
                        <div class="form-group">
                            <label for="current-password">Current Password (leave blank to leave unchanged)</label>
                            <input type="password" id="current-password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new-password">New Password (leave blank to leave unchanged)</label>
                            <input type="password" id="new-password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm-new-password">Confirm New Password</label>
                            <input type="password" id="confirm-new-password" name="confirm_new_password">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
}

/**
 * Show login form for guest users
 */
function showLoginForm() {
    const authContainer = document.getElementById('auth-container');
    const dashboardContainer = document.getElementById('dashboard-container');
    
    if (authContainer && dashboardContainer) {
        // Show auth container
        authContainer.style.display = 'block';
        
        // Hide dashboard container
        dashboardContainer.style.display = 'none';
        
        // Clear dashboard container
        dashboardContainer.innerHTML = '';
    }
}

/**
 * Initialize tab functionality for login and register forms
 */
function initAuthTabs() {
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');
    const loginFormContainer = document.getElementById('login-form-container');
    const registerFormContainer = document.getElementById('register-form-container');
    
    if (loginTab && registerTab && loginFormContainer && registerFormContainer) {
        // Login tab click
        loginTab.addEventListener('click', function() {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            loginFormContainer.classList.add('active');
            registerFormContainer.classList.remove('active');
        });
        
        // Register tab click
        registerTab.addEventListener('click', function() {
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            registerFormContainer.classList.add('active');
            loginFormContainer.classList.remove('active');
        });
        
        // Check URL for tab parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'register') {
            registerTab.click();
        }
    }
}

/**
 * Initialize registration form
 */
function initRegistrationForm() {
    const registerForm = document.getElementById('register-form');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Create form data
            const formData = new FormData(registerForm);
            
            // Validate passwords match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                showMessage('register-message', 'Passwords do not match.', 'error');
                return;
            }
            
            // Disable submit button and show loading state
            const submitButton = registerForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;
            
            // Send registration request
            fetch('register.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
                
                if (data.status === 'success') {
                    // Registration successful
                    showMessage('register-message', data.message, 'success');
                    
                    // Clear form
                    registerForm.reset();
                    
                    // Switch to login tab after a delay
                    setTimeout(() => {
                        document.getElementById('login-tab').click();
                    }, 2000);
                } else {
                    // Registration failed
                    showMessage('register-message', data.message || 'Registration failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                // Reset button
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
                
                console.error('Registration error:', error);
                showMessage('register-message', 'An error occurred. Please try again.', 'error');
            });
        });
    }
}

/**
 * Initialize login form
 */
function initLoginForm() {
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Create form data
            const formData = new FormData(loginForm);
            
            // Disable submit button and show loading state
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;
            
            // Send login request
            fetch('login.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                // Reset button
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
                
                if (data.status === 'success') {
                    // Login successful
                    showMessage('login-message', data.message, 'success');
                    
                    // Check if redirect URL is provided
                    if (data.redirect) {
                        // Redirect after a brief delay
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        // If no redirect URL, just reload the page
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } else {
                    // Login failed
                    showMessage('login-message', data.message || 'Invalid email or password.', 'error');
                }
            })
            .catch(error => {
                // Reset button
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
                
                console.error('Login error:', error);
                showMessage('login-message', 'An error occurred. Please try again.', 'error');
            });
        });
    }
}

/**
 * Initialize dashboard functionality
 */
function initDashboardFunctionality() {
    // Initialize dashboard tabs
    initDashboardTabs();
    
    // Initialize account settings form
    initAccountSettingsForm();
    
    // Initialize address editing
    initAddressEditing();
    
    // Initialize wishlist functionality
    initWishlist();
    
    // Initialize logout buttons
    initLogoutButton();
}

/**
 * Initialize dashboard tabs
 */
function initDashboardTabs() {
    const dashboardLinks = document.querySelectorAll('.dashboard-menu a');
    const dashboardTabs = document.querySelectorAll('.dashboard-tab');
    
    dashboardLinks.forEach(link => {
        if (link.id !== 'logout-btn' && link.id !== 'dashboard-logout') {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                
                const tabId = this.getAttribute('data-tab');
                
                // Update active link
                dashboardLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding tab
                dashboardTabs.forEach(tab => {
                    tab.classList.remove('active');
                    if (tab.id === `${tabId}-tab`) {
                        tab.classList.add('active');
                    }
                });
            });
        }
    });
    
    // Handle "View All Orders" link
    const viewAllOrdersLink = document.querySelector('.view-all-link[data-tab="orders"]');
    if (viewAllOrdersLink) {
        viewAllOrdersLink.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Simulate clicking on the Orders tab
            const ordersLink = document.querySelector('.dashboard-menu a[data-tab="orders"]');
            if (ordersLink) {
                ordersLink.click();
            }
        });
    }
}

/**
 * Initialize account settings form
 */
function initAccountSettingsForm() {
    const accountDetailsForm = document.getElementById('account-details-form');
    
    if (accountDetailsForm) {
        accountDetailsForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const firstName = document.getElementById('account-first-name').value;
            const lastName = document.getElementById('account-last-name').value;
            const displayName = document.getElementById('account-display-name').value;
            const email = document.getElementById('account-email').value;
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmNewPassword = document.getElementById('confirm-new-password').value;
            
            // Validate form
            if (!firstName || !lastName || !email) {
                showNotification('Please fill in all required fields.', 'error');
                return;
            }
            
            // Check if changing password
            if (newPassword || confirmNewPassword) {
                if (!currentPassword) {
                    showNotification('Please enter your current password to change it.', 'error');
                    return;
                }
                
                if (newPassword !== confirmNewPassword) {
                    showNotification('New passwords do not match.', 'error');
                    return;
                }
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('display_name', displayName);
            formData.append('email', email);
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('confirm_new_password', confirmNewPassword);
            
            // Send update request
            fetch('profile.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update successful
                    showNotification('Account details updated successfully.', 'success');
                    
                    // Clear password fields
                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                    document.getElementById('confirm-new-password').value = '';
                    
                    // Reload page after a brief delay to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Update failed
                    showNotification(data.message || 'Failed to update account details.', 'error');
                }
            })
            .catch(error => {
                console.error('Profile update error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            });
        });
    }
}

/**
 * Initialize address editing
 */
function initAddressEditing() {
    const editAddressButtons = document.querySelectorAll('.edit-address');
    
    editAddressButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            const addressType = this.getAttribute('data-type');
            // Here you would implement address editing functionality
            showNotification(`Edit ${addressType} address functionality would be implemented here.`, 'info');
        });
    });
}

/**
 * Initialize wishlist functionality
 */
function initWishlist() {
    // Implementation would depend on your wishlist functionality
    // This is a placeholder
}

/**
 * Initialize logout button
 */
function initLogoutButton() {
    const logoutBtn = document.getElementById('logout-btn');
    const dashboardLogout = document.getElementById('dashboard-logout');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {
            event.preventDefault();
            logoutUser();
        });
    }
    
    if (dashboardLogout) {
        dashboardLogout.addEventListener('click', function(event) {
            event.preventDefault();
            logoutUser();
        });
    }
}

/**
 * Logout user
 */
function logoutUser() {
    // Send logout request
    fetch('logout.php', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(() => {
        // Redirect to login page
        window.location.href = 'account.html';
    })
    .catch(error => {
        console.error('Logout error:', error);
        showNotification('An error occurred during logout. Please try again.', 'error');
    });
}

/**
 * Show message in form
 * @param {string} elementId - ID of the message element
 * @param {string} message - Message to display
 * @param {string} type - Message type ('success' or 'error')
 */
function showMessage(elementId, message, type) {
    const messageElement = document.getElementById(elementId);
    
    if (messageElement) {
        messageElement.textContent = message;
        messageElement.className = `auth-message ${type}`;
    }
}

/**
 * Show notification message
 * @param {string} message - Message text
 * @param {string} type - Message type ('success', 'error', 'info')
 */
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
            <p>${message}</p>
        </div>
        <button class="close-notification"><i class="fas fa-times"></i></button>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Add close button functionality
    const closeBtn = notification.querySelector('.close-notification');
    closeBtn.addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}