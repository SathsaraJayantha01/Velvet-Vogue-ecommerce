/**
 * Velvet Vogue E-commerce Website
 * User Account JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize account page functionality
    initAccountPage();
});

/**
 * Initialize account page functionality
 */
function initAccountPage() {
    // Initialize dashboard tabs
    initDashboardTabs();
    
    // Initialize account settings form
    initAccountSettingsForm();
    
    // Initialize address editing
    initAddressEditing();
    
    // Initialize wishlist functionality
    initWishlist();
    
    // Initialize logout buttons
    initLogoutButtons();
}

/**
 * Initialize dashboard tabs
 */
function initDashboardTabs() {
    const dashboardLinks = document.querySelectorAll('.dashboard-menu a');
    const dashboardTabs = document.querySelectorAll('.dashboard-tab');
    
    dashboardLinks.forEach(link => {
        if (link.id !== 'logout-btn') {
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
                
                // In a real application, this would validate the current password
                // For demonstration, assume it's correct
            }
            
            // Update user data
            updateUserData(firstName, lastName, displayName, email, newPassword);
        });
    }
}

/**
 * Update user data
 * @param {string} firstName - User's first name
 * @param {string} lastName - User's last name
 * @param {string} displayName - User's display name
 * @param {string} email - User's email
 * @param {string} newPassword - User's new password (optional)
 */
function updateUserData(firstName, lastName, displayName, email, newPassword) {
    // Get current user data
    let user = JSON.parse(localStorage.getItem('user')) || {};
    
    // Update user data
    user.firstName = firstName;
    user.lastName = lastName;
    user.displayName = displayName;
    user.email = email;
    
    // If changing password, update it
    if (newPassword) {
        // In a real application, this would be hashed
        user.password = newPassword;
    }
    
    // Save updated user data
    localStorage.setItem('user', JSON.stringify(user));
    
    // Update UI
    updateUserInfo(user);
    
    // Show success message
    showNotification('Account details updated successfully.', 'success');
    
    // Clear password fields
    document.getElementById('current-password').value = '';
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-new-password').value = '';
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
            showEditAddressModal(addressType);
        });
    });
}

/**
 * Show edit address modal
 * @param {string} addressType - Address type ('billing' or 'shipping')
 */
function showEditAddressModal(addressType) {
    // Get address data (in a real application, this would come from the server)
    const address = getAddressData(addressType);
    
    // Create modal element
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'edit-address-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit ${addressType.charAt(0).toUpperCase() + addressType.slice(1)} Address</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-address-form">
                    <input type="hidden" id="address-type" value="${addressType}">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address-first-name">First Name</label>
                            <input type="text" id="address-first-name" value="${address.firstName}" required>
                        </div>
                        <div class="form-group">
                            <label for="address-last-name">Last Name</label>
                            <input type="text" id="address-last-name" value="${address.lastName}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address-line1">Address Line 1</label>
                        <input type="text" id="address-line1" value="${address.line1}" required>
                    </div>
                    <div class="form-group">
                        <label for="address-line2">Address Line 2</label>
                        <input type="text" id="address-line2" value="${address.line2}">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address-city">City</label>
                            <input type="text" id="address-city" value="${address.city}" required>
                        </div>
                        <div class="form-group">
                            <label for="address-postal-code">Postal Code</label>
                            <input type="text" id="address-postal-code" value="${address.postalCode}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address-country">Country</label>
                        <select id="address-country" required>
                            <option value="US" ${address.country === 'US' ? 'selected' : ''}>United States</option>
                            <option value="CA" ${address.country === 'CA' ? 'selected' : ''}>Canada</option>
                            <option value="UK" ${address.country === 'UK' ? 'selected' : ''}>United Kingdom</option>
                            <option value="AU" ${address.country === 'AU' ? 'selected' : ''}>Australia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address-phone">Phone</label>
                        <input type="tel" id="address-phone" value="${address.phone}" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Address</button>
                        <button type="button" class="btn-secondary cancel-modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => {
        modal.style.display = 'block';
    }, 10);
    
    // Close modal when clicking the X button
    const closeBtn = modal.querySelector('.close-modal');
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        setTimeout(() => {
            modal.remove();
        }, 300);
    });
    
    // Close modal when clicking the cancel button
    const cancelBtn = modal.querySelector('.cancel-modal');
    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        setTimeout(() => {
            modal.remove();
        }, 300);
    });
    
    // Close modal when clicking outside the content
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    });
    
    // Handle form submission
    const addressForm = document.getElementById('edit-address-form');
    if (addressForm) {
        addressForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const type = document.getElementById('address-type').value;
            const firstName = document.getElementById('address-first-name').value;
            const lastName = document.getElementById('address-last-name').value;
            const line1 = document.getElementById('address-line1').value;
            const line2 = document.getElementById('address-line2').value;
            const city = document.getElementById('address-city').value;
            const postalCode = document.getElementById('address-postal-code').value;
            const country = document.getElementById('address-country').value;
            const phone = document.getElementById('address-phone').value;
            
            // Create address object
            const updatedAddress = {
                type: type,
                firstName: firstName,
                lastName: lastName,
                line1: line1,
                line2: line2,
                city: city,
                postalCode: postalCode,
                country: country,
                phone: phone
            };
            
            // Save address
            saveAddress(updatedAddress);
            
            // Close modal
            modal.style.display = 'none';
            setTimeout(() => {
                modal.remove();
            }, 300);
        });
    }
}

/**
 * Get address data
 * @param {string} addressType - Address type ('billing' or 'shipping')
 * @returns {Object} Address data
 */
function getAddressData(addressType) {
    // Get addresses from localStorage
    const addresses = JSON.parse(localStorage.getItem('addresses')) || {};
    
    // Return address if it exists, otherwise return default values
    return addresses[addressType] || {
        firstName: 'John',
        lastName: 'Doe',
        line1: addressType === 'billing' ? '123 Main Street' : '456 Park Avenue',
        line2: addressType === 'billing' ? 'Apt 4B' : 'Suite 10',
        city: 'New York',
        postalCode: addressType === 'billing' ? '10001' : '10022',
        country: 'US',
        phone: '+1 234 567 8900'
    };
}

/**
 * Save address data
 * @param {Object} address - Address data
 */
function saveAddress(address) {
    // Get addresses from localStorage
    let addresses = JSON.parse(localStorage.getItem('addresses')) || {};
    
    // Update address
    addresses[address.type] = address;
    
    // Save addresses to localStorage
    localStorage.setItem('addresses', JSON.stringify(addresses));
    
    // Show success message
    showNotification(`${address.type.charAt(0).toUpperCase() + address.type.slice(1)} address updated successfully.`, 'success');
    
    // Reload page to update UI
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

/**
 * Initialize wishlist functionality
 */
function initWishlist() {
    // Load wishlist items
    loadWishlistItems();
    
    // Initialize remove from wishlist buttons
    initRemoveFromWishlistButtons();
    
    // Initialize add to cart from wishlist buttons
    initAddToCartFromWishlistButtons();
}

/**
 * Load wishlist items
 */
function loadWishlistItems() {
    const wishlistItemsContainer = document.querySelector('.wishlist-items .product-grid');
    if (!wishlistItemsContainer) return;
    
    // Get wishlist items from localStorage
    const wishlistItems = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Get products data
    const products = getAllProducts();
    
    // Filter products in wishlist
    const wishlistProducts = products.filter(product => wishlistItems.includes(product.id.toString()));
    
    // Update wishlist count
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = wishlistProducts.length;
    }
    
    // Generate HTML for wishlist items
    if (wishlistProducts.length === 0) {
        wishlistItemsContainer.innerHTML = `
            <div class="empty-wishlist">
                <div class="empty-wishlist-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h3>Your wishlist is empty</h3>
                <p>Add items to your wishlist to keep track of products you're interested in.</p>
                <a href="categories.html" class="btn-primary">Start Shopping</a>
            </div>
        `;
    } else {
        wishlistItemsContainer.innerHTML = wishlistProducts.map(product => generateWishlistItemHTML(product)).join('');
    }
}

/**
 * Initialize remove from wishlist buttons
 */
function initRemoveFromWishlistButtons() {
    const removeButtons = document.querySelectorAll('.remove-from-wishlist');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            removeFromWishlist(productId);
        });
    });
}

/**
 * Initialize add to cart from wishlist buttons
 */
function initAddToCartFromWishlistButtons() {
    const addToCartButtons = document.querySelectorAll('.wishlist-items .add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCartFromWishlist(productId);
        });
    });
}

/**
 * Remove product from wishlist
 * @param {string|number} productId - Product ID
 */
function removeFromWishlist(productId) {
    // Get wishlist from localStorage
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Remove product from wishlist
    wishlist = wishlist.filter(id => id !== productId.toString());
    
    // Save updated wishlist
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    
    // Reload wishlist items
    loadWishlistItems();
    
    // Show notification
    showNotification('Product removed from wishlist.', 'success');
}

/**
 * Add product to cart from wishlist
 * @param {string|number} productId - Product ID
 */
function addToCartFromWishlist(productId) {
    // Add to cart
    addToCart(productId, 1);
    
    // Remove from wishlist
    removeFromWishlist(productId);
}

/**
 * Initialize logout buttons
 */
function initLogoutButtons() {
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
 * Generate HTML for a wishlist item
 * @param {Object} product - Product data
 * @returns {string} HTML string for the wishlist item
 */
function generateWishlistItemHTML(product) {
    return `
        <div class="product-card" data-id="${product.id}">
            ${product.badge ? `<div class="product-badge ${product.badge}">${product.badge}</div>` : ''}
            <div class="product-image">
                <a href="product.html?id=${product.id}">
                    <img src="${product.image}" alt="${product.name}">
                </a>
                <div class="product-actions">
                    <button class="add-to-cart" data-id="${product.id}"><i class="fas fa-shopping-cart"></i></button>
                    <button class="remove-from-wishlist" data-id="${product.id}"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="product-info">
                <h3 class="product-title"><a href="product.html?id=${product.id}">${product.name}</a></h3>
                <div class="product-price">
                    <span class="current-price">$${product.price.toFixed(2)}</span>
                    ${product.oldPrice ? `<span class="old-price">$${product.oldPrice.toFixed(2)}</span>` : ''}
                </div>
            </div>
        </div>
    `;
}

/**
 * Get all products data
 * @returns {Array} Array of product objects
 */
function getAllProducts() {
    // In a real application, this data would come from an API
    return [
        {
            id: 1,
            name: 'Men\'s Casual Shirt',
            price: 65.00,
            oldPrice: 85.00,
            image: 'PNG/0002.jpg',
            badge: 'sale'
        },
        {
            id: 2,
            name: 'Formal Blazer',
            price: 120.00,
            oldPrice: null,
            image: 'PNG/0015.jpg',
            badge: null
        },
        {
            id: 3,
            name: 'Elegant Dress',
            price: 89.99,
            oldPrice: 119.99,
            image: 'PNG/0016.jpg',
            badge: 'sale'
        },
        {
            id: 4,
            name: 'Classic Watch',
            price: 149.99,
            oldPrice: null,
            image: 'PNG/0017.jpg',
            badge: null
        },
        {
            id: 5,
            name: 'Leather Wallet',
            price: 45.00,
            oldPrice: null,
            image: 'PNG/0018.jpg',
            badge: null
        },
        {
            id: 6,
            name: 'Summer T-Shirt',
            price: 29.99,
            oldPrice: null,
            image: 'PNG/0019.jpg',
            badge: 'new'
        },
        {
            id: 7,
            name: 'Designer Sunglasses',
            price: 75.00,
            oldPrice: 95.00,
            image: 'PNG/0020.jpg',
            badge: 'sale'
        },
        {
            id: 8,
            name: 'Women\'s Casual Jeans',
            price: 59.99,
            oldPrice: null,
            image: 'PNG/0021.jpg',
            badge: null
        }
    ];
}

/**
 * Add product to cart
 * @param {string|number} productId - Product ID
 * @param {number} quantity - Product quantity
 */
function addToCart(productId, quantity = 1) {
    // Get current cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if product already exists in cart
    const existingProductIndex = cart.findIndex(item => item.id == productId);
    
    if (existingProductIndex !== -1) {
        // Update quantity if product already in cart
        cart[existingProductIndex].quantity += quantity;
    } else {
        // Add new product to cart
        cart.push({
            id: productId,
            quantity: quantity
        });
    }
    
    // Save cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count in the header
    updateCartCount();
    
    // Show notification
    showNotification('Product added to cart successfully!', 'success');
}

/**
 * Update cart count in the header
 */
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) return;
    
    // Get current cart from localStorage
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Calculate total quantity
    const totalQuantity = cart.reduce((total, item) => total + item.quantity, 0);
    
    // Update the count
    cartCountElement.textContent = totalQuantity;
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