/**
 * Velvet Vogue E-commerce Website
 * Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the website
    initNavigation();
    initFeaturedProducts();
    initNewArrivals();
    initNewsletterForm();
});

/**
 * Initialize navigation functionality
 */
function initNavigation() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', function() {
            menu.classList.toggle('active');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.menu') && !event.target.closest('.menu-toggle') && menu.classList.contains('active')) {
            menu.classList.remove('active');
        }
    });

    // Highlight current page in navigation
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.menu a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath || 
            (currentPath.includes(link.getAttribute('href')) && link.getAttribute('href') !== 'index.html')) {
            link.classList.add('active');
        }
    });
}

/**
 * Load featured products on the homepage
 */
function initFeaturedProducts() {
    const featuredProductsContainer = document.getElementById('featured-products');
    
    if (!featuredProductsContainer) return;

    // Sample featured products data (in a real application, this would come from an API or database)
    const featuredProducts = [
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
            badge: 'sale'
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
            badge: 'sale'
        }
    ];
    
                // Generate HTML for each product
    featuredProductsContainer.innerHTML = featuredProducts.map(product => generateProductCard(product)).join('');
                
                // Add event listeners to the "Add to Cart" buttons
                addCartButtonListeners();
}

/**
 * Load new arrivals on the homepage
 */
function initNewArrivals() {
    const newArrivalsContainer = document.getElementById('new-arrivals');
    
    if (!newArrivalsContainer) return;
    
    // Sample new arrivals data
    const newArrivals = [
        {
            id: 5,
            name: 'Leather Wallet',
            price: 45.00,
            oldPrice: null,
            image: 'PNG/0018.jpg',
            badge: 'new'
        },
        {
            id: 6,
            name: 'Summer T-Shirt',
            price: 29.99,
            oldPrice: null,
            image: 'png/0019.jpg',
            badge: 'new'
        },
        {
            id: 7,
            name: 'Designer Sunglasses',
            price: 75.00,
            oldPrice: 95.00,
            image: 'PNG/0020.jpg',
            badge: 'new'
        },
        {
            id: 8,
            name: 'Women\'s Casual Jeans',
            price: 59.99,
            oldPrice: null,
            image: 'PNG/0021.jpg',
            badge: 'new'
        }
    ];
    
                // Generate HTML for each product
    newArrivalsContainer.innerHTML = newArrivals.map(product => generateProductCard(product)).join('');
                
                // Add event listeners to the "Add to Cart" buttons
                addCartButtonListeners();
}

/**
 * Initialize newsletter form submission
 */
function initNewsletterForm() {
    const newsletterForm = document.getElementById('newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (email) {
                // In a real application, this would send the email to a server
                alert('Thank you for subscribing to our newsletter!');
                emailInput.value = '';
            }
        });
    }
}

/**
 * Generate HTML for a product card
 * @param {Object} product - Product data
 * @returns {string} HTML string for the product card
 */
function generateProductCard(product) {
    return `
        <div class="product-card" data-id="${product.id}">
            ${product.badge ? `<div class="product-badge ${product.badge}">${product.badge}</div>` : ''}
            <div class="product-image">
                <a href="product.html?id=${product.id}">
                    <img src="${product.image}" alt="${product.name}">
                </a>
                <div class="product-actions">
                    <button class="add-to-cart" data-id="${product.id}"><i class="fas fa-shopping-cart"></i></button>
                    <button class="add-to-wishlist" data-id="${product.id}"><i class="far fa-heart"></i></button>
                    <button class="quick-view" data-id="${product.id}"><i class="far fa-eye"></i></button>
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
 * Add event listeners to "Add to Cart" buttons
 */
function addCartButtonListeners() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-id');
            addToCart(productId, 1);
        });
    });

    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-id');
            addToWishlist(productId);
        });
    });

    const quickViewButtons = document.querySelectorAll('.quick-view');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-id');
            showQuickView(productId);
        });
    });
}

/**
 * Get URL parameters
 * @returns {Object} Object containing URL parameters
 */
function getUrlParams() {
    const params = {};
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    for (const [key, value] of urlParams) {
        params[key] = value;
    }
    
    return params;
}

/**
 * Format price with currency symbol
 * @param {number} price - Price value
 * @returns {string} Formatted price
 */
function formatPrice(price) {
    return '$' + price.toFixed(2);
}

/**
 * Calculate discount percentage
 * @param {number} oldPrice - Original price
 * @param {number} currentPrice - Current price
 * @returns {number} Discount percentage
 */
function calculateDiscount(oldPrice, currentPrice) {
    return Math.round(((oldPrice - currentPrice) / oldPrice) * 100);
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
 * Add product to wishlist
 * @param {string|number} productId - Product ID
 */
function addToWishlist(productId) {
    // Get current wishlist from localStorage
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Check if product already exists in wishlist
    const exists = wishlist.includes(productId.toString());
    
    if (!exists) {
        // Add product to wishlist
        wishlist.push(productId.toString());
        
        // Save wishlist to localStorage
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        
        // Show notification
        showNotification('Product added to wishlist!', 'success');
    } else {
        // Show notification
        showNotification('Product is already in your wishlist!', 'info');
    }
}

/**
 * Show quick view modal for a product
 * @param {string|number} productId - Product ID
 */
function showQuickView(productId) {
    // In a real application, this would fetch product details from an API
    // For demonstration purposes, show a notification
    showNotification('Quick view functionality would be implemented here.', 'info');
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});