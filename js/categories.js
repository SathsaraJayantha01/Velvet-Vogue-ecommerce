/**
 * Velvet Vogue E-commerce Website
 * Categories Page JavaScript File
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the categories page
    initCategories();
    
    // Set up event listeners
    setupEventListeners();
});

/**
 * Initialize categories page functionality
 */
function initCategories() {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const gender = urlParams.get('gender');
    const type = urlParams.get('type');
    
    // Set page title and breadcrumb based on URL parameters
    updatePageTitle(gender, type);
    
    // Fetch categories from API
    fetchCategories();
    
    // Fetch products based on filters
    fetchProducts(gender, type);
}

// Set up event listeners for the page
function setupEventListeners() {
    // Filter toggle button
    const filterToggleBtn = document.getElementById('filter-toggle-btn');
    const filterSidebar = document.getElementById('filter-sidebar');
    const closeFilterBtn = document.querySelector('.close-filter');
    
    if (filterToggleBtn) {
        filterToggleBtn.addEventListener('click', function() {
            filterSidebar.classList.toggle('active');
        });
    }
    
    if (closeFilterBtn) {
        closeFilterBtn.addEventListener('click', function() {
            filterSidebar.classList.remove('active');
        });
    }
    
    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            // Re-fetch products with the new sort option
            const urlParams = new URLSearchParams(window.location.search);
            fetchProducts(urlParams.get('gender'), urlParams.get('type'), this.value);
        });
    }
    
    // Apply filters button
    const applyFiltersBtn = document.getElementById('apply-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    // Clear filters button
    const clearFiltersBtn = document.getElementById('clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearFilters();
        });
    }
    
    // Price range slider
    const priceRange = document.getElementById('price-range');
    const priceRangeValue = document.getElementById('price-range-value');
    if (priceRange && priceRangeValue) {
        priceRange.addEventListener('input', function() {
            priceRangeValue.textContent = '$' + this.value;
        });
    }
}

/**
 * Update page title and breadcrumb based on URL parameters
 * @param {string} gender - Gender parameter
 * @param {string} type - Type parameter
 */
function updatePageTitle(gender, type) {
    let title = 'All Products';
    
    if (gender) {
        if (gender === 'men') {
            title = 'Men\'s Collection';
        } else if (gender === 'women') {
            title = 'Women\'s Collection';
        }
    } else if (type) {
        if (type === 'casual') {
            title = 'Casual Wear';
        } else if (type === 'formal') {
            title = 'Formal Wear';
        } else if (type === 'accessories') {
            title = 'Accessories';
        }
    }
    
    // Update page title
    const categoryTitle = document.getElementById('category-title');
    if (categoryTitle) {
        categoryTitle.textContent = title;
    }
    
    // Update breadcrumb
    const categoryBreadcrumb = document.getElementById('category-breadcrumb');
    if (categoryBreadcrumb) {
        categoryBreadcrumb.textContent = title;
    }
}

/**
 * Fetch categories from API
 */
function fetchCategories() {
    fetch('categories_api.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Update category filters
                updateCategoryFilters(data.data.hierarchical);
            } else {
                console.error('Error fetching categories:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching categories:', error);
        });
}

/**
 * Update category filters with fetched data
 * @param {Array} categories - Array of category objects
 */
function updateCategoryFilters(categories) {
    // This function will update the category checkboxes in the filter sidebar
    const categorySection = document.querySelector('.filter-section:nth-child(2) .filter-options');
    
    if (!categorySection) return;
    
    // Clear existing categories
    categorySection.innerHTML = '';
    
    // Add each parent category
    categories.forEach(category => {
        const checkbox = document.createElement('label');
        checkbox.innerHTML = `
            <input type="checkbox" name="category" value="${category.category_id}" class="filter-checkbox">
            ${category.name}
        `;
        categorySection.appendChild(checkbox);
        
        // Add subcategories if any
        if (category.subcategories && category.subcategories.length > 0) {
            const subcategoryDiv = document.createElement('div');
            subcategoryDiv.className = 'subcategories';
            subcategoryDiv.style.paddingLeft = '20px';
            
            category.subcategories.forEach(subcat => {
                const subCheckbox = document.createElement('label');
                subCheckbox.innerHTML = `
                    <input type="checkbox" name="subcategory" value="${subcat.category_id}" class="filter-checkbox">
                    ${subcat.name}
                `;
                subcategoryDiv.appendChild(subCheckbox);
            });
            
            categorySection.appendChild(subcategoryDiv);
        }
    });
}

/**
 * Fetch products based on filters
 * @param {string} gender - Gender parameter
 * @param {string} type - Type parameter
 * @param {string} sort - Sort parameter
 */
function fetchProducts(gender, type, sort = 'default') {
    // In a real scenario, this would fetch from an API
    // For demonstration, we'll generate mock products
    const productGrid = document.getElementById('product-grid');
    
    if (!productGrid) return;
    
    // Clear existing products
    productGrid.innerHTML = '';
    
    // Show loading state
    productGrid.innerHTML = '<div class="loading">Loading products...</div>';
    
    // Build filter parameters for API request
    let queryParams = new URLSearchParams();
    if (gender) queryParams.append('gender', gender);
    if (type) queryParams.append('type', type);
    if (sort) queryParams.append('sort', sort);
    
    // In a real scenario, this would be an API call
    // For now, we'll simulate with a timeout and mock data
    setTimeout(() => {
        // Generate mock products (in a real scenario, this would come from the API)
        const products = generateMockProducts(gender, type, sort);
    
    if (products.length === 0) {
        productGrid.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
        return;
    }
    
        // Clear loading state
        productGrid.innerHTML = '';
        
        // Add products to the grid
    products.forEach(product => {
            const productCard = createProductCard(product);
            productGrid.appendChild(productCard);
        });
    }, 500);
}

/**
 * Apply filters
 */
function applyFilters() {
    let filterParams = new URLSearchParams(window.location.search);
    
    // Get all checked gender filters
    const genderCheckboxes = document.querySelectorAll('input[name="gender"]:checked');
    if (genderCheckboxes.length > 0) {
        // If only one gender is selected, use it as a param
        if (genderCheckboxes.length === 1) {
            filterParams.set('gender', genderCheckboxes[0].value);
        } else {
            // If multiple, we'll need to handle this on the backend
            // For now, we'll just display all products and filter client-side
            filterParams.delete('gender');
        }
    } else {
        filterParams.delete('gender');
    }
    
    // Get all checked category filters
    const categoryCheckboxes = document.querySelectorAll('input[name="category"]:checked');
    if (categoryCheckboxes.length > 0) {
        // Similar to gender, if one category is selected, use it
        if (categoryCheckboxes.length === 1) {
            filterParams.set('type', categoryCheckboxes[0].value);
        } else {
            // Again, multiple selections would need backend support
            filterParams.delete('type');
        }
    } else {
        filterParams.delete('type');
    }
    
    // Get price range
    const priceRange = document.getElementById('price-range');
    if (priceRange) {
        filterParams.set('max_price', priceRange.value);
    }
    
    // Redirect with new filters
    window.location.href = 'categories.html?' + filterParams.toString();
}

/**
 * Clear all filters
 */
function clearFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset price range
    const priceRange = document.getElementById('price-range');
    if (priceRange) {
        priceRange.value = priceRange.max;
        const priceRangeValue = document.getElementById('price-range-value');
        if (priceRangeValue) {
            priceRangeValue.textContent = '$' + priceRange.value;
        }
    }
    
    // Redirect to categories page without filters
    window.location.href = 'categories.html';
}

/**
 * Create a product card element
 * @param {Object} product - Product data
 * @returns {Element} Product card element
 */
function createProductCard(product) {
    const productCard = document.createElement('div');
    productCard.className = 'product-card';
    
    productCard.innerHTML = `
        <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
            <div class="product-actions">
                <button class="quick-view" data-product-id="${product.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="add-to-cart" data-product-id="${product.id}">
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <button class="add-to-wishlist" data-product-id="${product.id}">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-name">
                <a href="product.html?id=${product.id}">${product.name}</a>
            </h3>
            <div class="product-price">
                ${product.discountPrice ? 
                    `<span class="original-price">$${product.price.toFixed(2)}</span>
                     <span class="discount-price">$${product.discountPrice.toFixed(2)}</span>`
                    : 
                    `<span class="regular-price">$${product.price.toFixed(2)}</span>`
                }
            </div>
            <div class="product-rating">
                ${generateRatingStars(product.rating)}
                <span class="rating-count">(${product.ratingCount})</span>
            </div>
        </div>
    `;
    
    // Add event listeners for the buttons
    const quickViewBtn = productCard.querySelector('.quick-view');
    const addToCartBtn = productCard.querySelector('.add-to-cart');
    const addToWishlistBtn = productCard.querySelector('.add-to-wishlist');
    
    if (quickViewBtn) {
        quickViewBtn.addEventListener('click', function() {
            openQuickView(product.id);
        });
    }
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            addToCart(product);
        });
    }
    
    if (addToWishlistBtn) {
        addToWishlistBtn.addEventListener('click', function() {
            addToWishlist(product.id);
        });
    }
    
    return productCard;
}

/**
 * Generate rating stars HTML
 * @param {number} rating - Product rating
 * @returns {string} HTML string for rating stars
 */
function generateRatingStars(rating) {
    let starsHtml = '';
    
    // Round to nearest 0.5
    rating = Math.round(rating * 2) / 2;
    
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            starsHtml += '<i class="fas fa-star"></i>';
        } else if (i - 0.5 === rating) {
            starsHtml += '<i class="fas fa-star-half-alt"></i>';
        } else {
            starsHtml += '<i class="far fa-star"></i>';
        }
    }
    
    return starsHtml;
}

/**
 * Open quick view modal
 * @param {string|number} productId - Product ID
 */
function openQuickView(productId) {
    console.log('Quick view for product:', productId);
    // In a real implementation, this would open a modal with product details
    // You can implement this feature using a modal component
}

/**
 * Add product to cart
 * @param {Object} product - Product data
 */
function addToCart(product) {
    console.log('Adding to cart:', product);
    
    // Get the existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if product is already in cart
    const existingProduct = cart.find(item => item.id === product.id);
    
    if (existingProduct) {
        // Increment quantity if product already exists
        existingProduct.quantity++;
    } else {
        // Add product with quantity 1
        cart.push({
            id: product.id,
            name: product.name,
            price: product.discountPrice || product.price,
            image: product.image,
            quantity: 1
        });
    }
    
    // Save the updated cart
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count in UI
    updateCartCount();
    
    // Show success message
    showToast('Product added to cart!');
}

/**
 * Add product to wishlist
 * @param {string|number} productId - Product ID
 */
function addToWishlist(productId) {
    console.log('Adding to wishlist:', productId);
    
    // Get the existing wishlist from localStorage
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Check if product is already in wishlist
    if (!wishlist.includes(productId)) {
        // Add product id to wishlist
        wishlist.push(productId);
        
        // Save the updated wishlist
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        
        // Show success message
        showToast('Product added to wishlist!');
    } else {
        // Show info message
        showToast('Product already in wishlist!', 'info');
    }
}

/**
 * Show toast notification
 * @param {string} message - Message text
 * @param {string} type - Message type ('success', 'info')
 */
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to the document
    document.body.appendChild(toast);
    
    // Show the toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Hide and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

/**
 * Update cart count in the header
 */
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    }
}

/**
 * Generate mock products (in a real scenario, this would be fetched from the API)
 * @param {string} gender - Gender parameter
 * @param {string} type - Type parameter
 * @param {string} sort - Sort parameter
 * @returns {Array} Array of product objects
 */
function generateMockProducts(gender, type, sort) {
    const products = [];
    
    // Number of products to generate
    const count = Math.floor(Math.random() * 8) + 8; // 8-15 products
    
    // Sample product data
    const menNames = ['Men\'s Basic Tee', 'Slim-Fit Jeans', 'Classic Oxford Shirt', 'Casual Blazer', 'Leather Belt', 'Formal Trousers', 'Wool Sweater', 'Bomber Jacket'];
    const womenNames = ['Women\'s Blouse', 'High-Waist Jeans', 'A-Line Dress', 'Fitted Blazer', 'Leather Handbag', 'Pleated Skirt', 'Cashmere Sweater', 'Denim Jacket'];
    const accessoryNames = ['Leather Wallet', 'Silk Tie', 'Silver Necklace', 'Sunglasses', 'Watch', 'Beanie Hat', 'Scarf', 'Backpack'];
    
    // Generate products
    for (let i = 1; i <= count; i++) {
        const id = i + (gender === 'men' ? 100 : (gender === 'women' ? 200 : 300));
        
        let name, image;
        
        if (gender === 'men' || (type === 'casual' && Math.random() > 0.5) || (type === 'formal' && Math.random() > 0.5)) {
            name = menNames[Math.floor(Math.random() * menNames.length)];
            image = `images/products/men/product${Math.floor(Math.random() * 8) + 1}.jpg`;
        } else if (gender === 'women' || (type === 'casual' && Math.random() > 0.5) || (type === 'formal' && Math.random() > 0.5)) {
            name = womenNames[Math.floor(Math.random() * womenNames.length)];
            image = `images/products/women/product${Math.floor(Math.random() * 8) + 1}.jpg`;
        } else {
            name = accessoryNames[Math.floor(Math.random() * accessoryNames.length)];
            image = `images/products/accessories/product${Math.floor(Math.random() * 8) + 1}.jpg`;
        }
        
        // Add a color or size to the name to make each unique
        const colors = ['Black', 'White', 'Blue', 'Grey', 'Red', 'Green', 'Navy', 'Burgundy'];
        const sizes = ['S', 'M', 'L', 'XL'];
        
        const color = colors[Math.floor(Math.random() * colors.length)];
        const size = sizes[Math.floor(Math.random() * sizes.length)];
        
        name = `${name} - ${color}`;
        
        const price = Math.floor(Math.random() * 120) + 20; // $20-$140
        const hasDiscount = Math.random() > 0.7; // 30% chance of having a discount
        const discountPrice = hasDiscount ? price * 0.8 : null; // 20% discount
        const rating = (Math.random() * 2) + 3; // Rating between 3-5
        const ratingCount = Math.floor(Math.random() * 100) + 5; // 5-104 ratings
        
        products.push({
            id,
            name,
            price,
            discountPrice,
            image,
            rating,
            ratingCount,
            color,
            size,
            gender: gender || (Math.random() > 0.5 ? 'men' : 'women'),
            type: type || (Math.random() > 0.7 ? 'formal' : (Math.random() > 0.5 ? 'casual' : 'accessories'))
        });
    }
    
    // Sort products based on sort option
    if (sort === 'price-asc') {
        products.sort((a, b) => (a.discountPrice || a.price) - (b.discountPrice || b.price));
    } else if (sort === 'price-desc') {
        products.sort((a, b) => (b.discountPrice || b.price) - (a.discountPrice || a.price));
    } else if (sort === 'newest') {
        // In a real scenario, we would sort by date
        // For mock data, we'll just randomize
        products.sort(() => Math.random() - 0.5);
    }
    
    return products;
}