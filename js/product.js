/**
 * Velvet Vogue E-commerce Website
 * Product Details JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize product page
    initProductPage();
});

/**
 * Initialize product page functionality
 */
function initProductPage() {
    // Get product ID from URL
    const params = getUrlParams();
    const productId = params.id;
    
    if (!productId) {
        // If no product ID, redirect to the categories page
        window.location.href = 'categories.php';
        return;
    }
    
    // Load product details
    loadProductDetails(productId);
    
    // Initialize product image gallery
    initProductGallery();
    
    // Initialize product tabs
    initProductTabs();
    
    // Initialize product options (size, color, quantity)
    initProductOptions();
    
    // Initialize product actions (add to cart, wishlist)
    initProductActions();
    
    // Load related products
    loadRelatedProducts();
    
    // Initialize review form
    initReviewForm();
}

/**
 * Load product details
 * @param {string|number} productId - Product ID
 */
function loadProductDetails(productId) {
    // Get product data (in a real application, this would come from an API)
    const product = getProductById(productId);
    
    if (!product) {
        // Product not found, show error or redirect
        showNotification('Product not found.', 'error');
        setTimeout(() => {
            window.location.href = 'categories.html';
        }, 2000);
        return;
    }
    
            // Update product details in the page
            updateProductDetails(product);
            
            // Update breadcrumb
            updateBreadcrumb(product);
}

/**
 * Get product data by ID
 * @param {string|number} productId - Product ID
 * @returns {Object|null} Product data or null if not found
 */
function getProductById(productId) {
    // In a real application, this would fetch data from an API
    const products = [
        {
            id: 1,
            name: 'Men\'s Casual Shirt',
            price: 65.00,
            oldPrice: 85.00,
            discount: 24,
            image: 'PNG/0002.jpg',
            images: [
                'PNG/0002.jpg',
                'PNG/0002.jpg',
                'PNG/0002.jpg',
                'PNG/0002.jpg'
            ],
            description: 'This stylish and comfortable casual shirt is perfect for expressing your unique identity. Made from high-quality cotton-blend fabric, it offers both durability and a trendy look that will turn heads.',
            category: 'Men\'s Casual Wear',
            gender: 'men',
            type: 'casual',
            sizes: ['XS', 'S', 'M', 'L', 'XL'],
            colors: [
                { name: 'Black', code: '#000' },
                { name: 'Blue', code: '#0000ff' },
                { name: 'Red', code: '#ff0000' },
                { name: 'White', code: '#fff' }
            ],
            rating: 4.5,
            reviewCount: 24,
            availability: 'In Stock',
            specifications: {
                material: '60% Cotton, 40% Polyester',
                fit: 'Regular fit',
                care: 'Machine wash cold, tumble dry low',
                collar: 'Button-down collar',
                sleeve: 'Long sleeve',
                countryOfOrigin: 'Made in USA',
                sku: 'VV-MS-12345'
            }
        },
        {
            id: 2,
            name: 'Formal Blazer',
            price: 120.00,
            oldPrice: null,
            discount: null,
            image: 'PNG/0015.jpg',
            images: [
                'PNG/0015.jpg',
                'PNG/0015.jpg',
                'PNG/0015.jpg',
                'PNG/0015.jpg'
            ],
            description: 'Elevate your formal attire with this sophisticated blazer. Tailored to perfection, this blazer combines classic styling with modern details for a timeless look that transitions seamlessly from office to evening events.',
            category: 'Men\'s Formal Wear',
            gender: 'men',
            type: 'formal',
            sizes: ['S', 'M', 'L', 'XL', 'XXL'],
            colors: [
                { name: 'Navy Blue', code: '#000080' },
                { name: 'Black', code: '#000' },
                { name: 'Charcoal', code: '#36454F' }
            ],
            rating: 4.8,
            reviewCount: 32,
            availability: 'In Stock',
            specifications: {
                material: '80% Wool, 20% Polyester',
                fit: 'Slim fit',
                care: 'Dry clean only',
                lapel: 'Notch lapel',
                buttons: 'Two-button closure',
                countryOfOrigin: 'Made in Italy',
                sku: 'VV-FB-23456'
            }
        },
        {
            id: 3,
            name: 'Elegant Dress',
            price: 89.99,
            oldPrice: 119.99,
            discount: 25,
            image: 'PNG/0016.jpg',
            images: [
                'PNG/0016.jpg',
                'PNG/0016.jpg',
                'PNG/0016.jpg',
                'PNG/0016.jpg'
            ],
            description: 'Turn heads with this stunning elegant dress that flatters your figure and makes a statement. Perfect for special occasions, parties, or whenever you want to feel exceptionally confident and beautiful.',
            category: 'Women\'s Formal Wear',
            gender: 'women',
            type: 'formal',
            sizes: ['XS', 'S', 'M', 'L'],
            colors: [
                { name: 'Red', code: '#ff0000' },
                { name: 'Black', code: '#000' },
                { name: 'Navy Blue', code: '#000080' }
            ],
            rating: 4.6,
            reviewCount: 18,
            availability: 'In Stock',
            specifications: {
                material: '95% Polyester, 5% Spandex',
                fit: 'A-line',
                care: 'Hand wash cold, line dry',
                length: 'Knee length',
                closure: 'Hidden back zipper',
                countryOfOrigin: 'Made in Spain',
                sku: 'VV-ED-34567'
            }
        },
        {
            id: 4,
            name: 'Classic Watch',
            price: 149.99,
            oldPrice: null,
            discount: null,
            image: 'PNG/0017.jpg',
            images: [
                'PNG/0017.jpg',
                'PNG/0017.jpg',
                'PNG/0017.jpg',
                'PNG/0017.jpg'
            ],
            description: 'This timeless classic watch combines elegant design with precision engineering. The perfect accessory for any outfit, it adds a touch of sophistication and functionality to your everyday style.',
            category: 'Accessories',
            gender: 'unisex',
            type: 'accessories',
            sizes: ['One Size'],
            colors: [
                { name: 'Silver', code: '#C0C0C0' },
                { name: 'Gold', code: '#FFD700' },
                { name: 'Rose Gold', code: '#B76E79' }
            ],
            rating: 4.9,
            reviewCount: 42,
            availability: 'In Stock',
            specifications: {
                case: 'Stainless steel, 40mm diameter',
                movement: 'Swiss quartz',
                water_resistance: 'Water-resistant to 30 meters',
                band: 'Genuine leather strap',
                features: 'Date display, luminous hands',
                warranty: '2-year international warranty',
                sku: 'VV-CW-45678'
            }
        },
        // Add more products as needed
    ];
    
    return products.find(product => product.id == productId) || null;
}

/**
 * Update product details in the page
 * @param {Object} product - Product data
 */
function updateProductDetails(product) {
    // Update product name
    const productName = document.getElementById('product-name');
    if (productName) {
        productName.textContent = product.name;
    }
    
    // Update product price
    const productPrice = document.getElementById('product-price');
    const productOldPrice = document.getElementById('product-old-price');
    
    if (productPrice) {
        productPrice.textContent = `$${product.price.toFixed(2)}`;
    }
    
    if (productOldPrice) {
        if (product.oldPrice) {
            productOldPrice.textContent = `$${product.oldPrice.toFixed(2)}`;
            productOldPrice.style.display = 'inline';
            
            // Update discount percentage
            const discountElement = document.querySelector('.product-price .discount');
            if (discountElement && product.discount) {
                discountElement.textContent = `(-${product.discount}%)`;
                discountElement.style.display = 'inline';
            }
        } else {
            productOldPrice.style.display = 'none';
            
            // Hide discount percentage
            const discountElement = document.querySelector('.product-price .discount');
            if (discountElement) {
                discountElement.style.display = 'none';
            }
        }
    }
    
    // Update product description
    const productDescription = document.getElementById('product-description');
    if (productDescription) {
        productDescription.innerHTML = `<p>${product.description}</p>`;
    }
    
    // Update product full description
    const productFullDescription = document.querySelector('.product-full-description');
    if (productFullDescription) {
        productFullDescription.innerHTML = `
            <p>${product.description}</p>
            <p>The versatile design makes it suitable for various occasions, from casual outings to more formal events. Its modern cut and attention to detail reflect the latest fashion trends while maintaining a timeless appeal.</p>
            <p>Features:</p>
            <ul>
                <li>Premium quality ${product.specifications.material}</li>
                <li>${product.specifications.fit}</li>
                <li>Durable construction</li>
                <li>${product.specifications.care}</li>
                <li>Versatile styling options</li>
            </ul>
        `;
    }
    
    // Update product specifications
    const productSpecifications = document.querySelector('.product-specifications table');
    if (productSpecifications && product.specifications) {
        let specsHTML = '';
        
        for (const [key, value] of Object.entries(product.specifications)) {
            // Format key (e.g., 'countryOfOrigin' -> 'Country Of Origin')
            const formattedKey = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
            
            specsHTML += `
                <tr>
                    <th>${formattedKey}</th>
                    <td>${value}</td>
                </tr>
            `;
        }
        
        productSpecifications.innerHTML = specsHTML;
    }
    
    // Update product availability
    const productAvailability = document.getElementById('product-availability');
    if (productAvailability) {
        productAvailability.innerHTML = `<i class="fas fa-check-circle"></i> ${product.availability}`;
    }
    
    // Update product rating
    const productRating = document.querySelector('.product-rating .stars');
    if (productRating && product.rating) {
        // Generate stars based on rating
        const fullStars = Math.floor(product.rating);
        const hasHalfStar = product.rating % 1 >= 0.5;
        
        let starsHTML = '';
        
        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                starsHTML += '<i class="fas fa-star"></i>';
            } else if (i === fullStars + 1 && hasHalfStar) {
                starsHTML += '<i class="fas fa-star-half-alt"></i>';
            } else {
                starsHTML += '<i class="far fa-star"></i>';
            }
        }
        
        productRating.innerHTML = starsHTML;
    }
    
    // Update review count
    const ratingCount = document.querySelector('.rating-count');
    if (ratingCount && product.reviewCount) {
        ratingCount.textContent = `(${product.reviewCount} reviews)`;
    }
    
    // Update product main image
    const mainProductImage = document.getElementById('main-product-image');
    if (mainProductImage) {
        mainProductImage.src = product.image;
        mainProductImage.alt = product.name;
    }
    
    // Update thumbnail gallery
    updateThumbnailGallery(product.images);
    
    // Update size options
    updateSizeOptions(product.sizes);
    
    // Update color options
    updateColorOptions(product.colors);
}

/**
 * Update the breadcrumb navigation
 * @param {Object} product - Product data
 */
function updateBreadcrumb(product) {
    const categoryBreadcrumb = document.getElementById('product-category-breadcrumb');
    const nameBreadcrumb = document.getElementById('product-name-breadcrumb');
    const categoryLink = document.getElementById('category-link');
    
    if (categoryBreadcrumb && nameBreadcrumb && categoryLink) {
        categoryBreadcrumb.textContent = product.category;
        nameBreadcrumb.textContent = product.name;
        
        // Set category link
        let categoryUrl = 'categories.html';
        
        if (product.gender) {
            categoryUrl += `?gender=${product.gender}`;
        } else if (product.type) {
            categoryUrl += `?type=${product.type}`;
        }
        
        categoryLink.href = categoryUrl;
    }
}

/**
 * Update thumbnail gallery with product images
 * @param {Array} images - Array of image URLs
 */
function updateThumbnailGallery(images) {
    const thumbnailGallery = document.getElementById('thumbnail-gallery');
    if (!thumbnailGallery || !images || images.length === 0) return;
    
    // Generate HTML for thumbnails
    let thumbnailsHTML = '';
    
    images.forEach((image, index) => {
        thumbnailsHTML += `
            <div class="thumbnail ${index === 0 ? 'active' : ''}" data-image="${image}">
                <img src="${image}" alt="Product thumbnail ${index + 1}">
            </div>
        `;
    });
    
    thumbnailGallery.innerHTML = thumbnailsHTML;
    
    // Add click event listeners to thumbnails
    const thumbnails = thumbnailGallery.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Update main image
            const imageUrl = this.getAttribute('data-image');
            const mainImage = document.getElementById('main-product-image');
            
            if (mainImage && imageUrl) {
                mainImage.src = imageUrl;
            }
            
            // Update active state
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

/**
 * Update size options with product sizes
 * @param {Array} sizes - Array of available sizes
 */
function updateSizeOptions(sizes) {
    const sizeOptions = document.getElementById('size-options');
    if (!sizeOptions || !sizes || sizes.length === 0) return;
    
    // Generate HTML for size options
    let sizesHTML = '';
    
    // Available sizes
    const availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    
    availableSizes.forEach(size => {
        const isAvailable = sizes.includes(size);
        
        sizesHTML += `
            <button class="size-btn ${size === 'M' ? 'active' : ''} ${!isAvailable ? 'disabled' : ''}" 
                data-size="${size}" ${!isAvailable ? 'disabled' : ''}>
                ${size}
            </button>
        `;
    });
    
    sizeOptions.innerHTML = sizesHTML;
    
    // Add click event listeners to size buttons
    const sizeButtons = sizeOptions.querySelectorAll('.size-btn:not(.disabled)');
    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            sizeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

/**
 * Update color options with product colors
 * @param {Array} colors - Array of available colors
 */
function updateColorOptions(colors) {
    const colorOptions = document.getElementById('color-options');
    if (!colorOptions || !colors || colors.length === 0) return;
    
    // Generate HTML for color options
    let colorsHTML = '';
    
    colors.forEach((color, index) => {
        colorsHTML += `
            <button class="color-btn ${index === 0 ? 'active' : ''}" 
                style="background-color: ${color.code}; ${color.name === 'White' ? 'border: 1px solid #ddd;' : ''}" 
                data-color="${color.name.toLowerCase()}">
                <span class="color-name">${color.name}</span>
            </button>
        `;
    });
    
    colorOptions.innerHTML = colorsHTML;
    
    // Add click event listeners to color buttons
    const colorButtons = colorOptions.querySelectorAll('.color-btn');
    colorButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            colorButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

/**
 * Initialize product image gallery
 */
function initProductGallery() {
    // Already initialized in updateThumbnailGallery()
}

/**
 * Initialize product tabs
 */
function initProductTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get tab ID
            const tabId = this.getAttribute('data-tab');
            
            // Update active state for buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update active state for panels
            tabPanels.forEach(panel => panel.classList.remove('active'));
            document.getElementById(`${tabId}-panel`).classList.add('active');
        });
    });
}

/**
 * Initialize product options (size, color, quantity)
 */
function initProductOptions() {
    // Size and color options already initialized in update functions
    
    // Initialize quantity selector
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const quantityInput = document.getElementById('quantity');
    
    if (minusBtn && plusBtn && quantityInput) {
        minusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.getAttribute('max') || 10);
            
            if (value < max) {
                quantityInput.value = value + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            const min = parseInt(this.getAttribute('min') || 1);
            const max = parseInt(this.getAttribute('max') || 10);
            
            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    }
}

/**
 * Initialize product actions (add to cart, wishlist)
 */
function initProductActions() {
    // Add to cart button
    const addToCartBtn = document.getElementById('add-to-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            // Get product ID from URL
            const params = getUrlParams();
            const productId = params.id;
            
            // Get selected quantity
            const quantity = parseInt(document.getElementById('quantity').value);
            
            // Add to cart
            addToCart(productId, quantity);
        });
    }
    
    // Add to wishlist button
    const addToWishlistBtn = document.getElementById('add-to-wishlist');
    if (addToWishlistBtn) {
        addToWishlistBtn.addEventListener('click', function() {
            // Get product ID from URL
            const params = getUrlParams();
            const productId = params.id;
            
            // Add to wishlist
            addToWishlist(productId);
        });
    }
}

/**
 * Load related products
 */
function loadRelatedProducts() {
    const relatedProductsContainer = document.getElementById('related-products');
    if (!relatedProductsContainer) return;
    
    // Get current product ID
    const params = getUrlParams();
    const currentProductId = params.id;
    
    // Get current product to determine category
    const currentProduct = getProductById(currentProductId);
    if (!currentProduct) return;
    
    // Get all products
    const allProducts = [
        {
            id: 1,
            name: 'Men\'s Casual Shirt',
            price: 65.00,
            oldPrice: 85.00,
            image: 'PNG/0002.jpg',
            category: 'Men\'s Casual Wear',
            gender: 'men',
            type: 'casual',
            badge: 'sale'
        },
        {
            id: 2,
            name: 'Formal Blazer',
            price: 120.00,
            oldPrice: null,
            image: 'PNG/0015.jpg',
            category: 'Men\'s Formal Wear',
            gender: 'men',
            type: 'formal',
            badge: null
        },
        {
            id: 3,
            name: 'Elegant Dress',
            price: 89.99,
            oldPrice: 119.99,
            image: 'png/0016.jpg',
            category: 'Women\'s Formal Wear',
            gender: 'women',
            type: 'formal',
            badge: 'sale'
        },
        {
            id: 4,
            name: 'Classic Watch',
            price: 149.99,
            oldPrice: null,
            image: 'png/0017.jpg',
            category: 'Accessories',
            gender: 'unisex',
            type: 'accessories',
            badge: null
        },
        {
            id: 5,
            name: 'Leather Wallet',
            price: 45.00,
            oldPrice: null,
            image: 'png/0018.jpg',
            category: 'Accessories',
            gender: 'unisex',
            type: 'accessories',
            badge: null
        },
        {
            id: 6,
            name: 'Summer T-Shirt',
            price: 29.99,
            oldPrice: null,
            image: 'png/0019.jpg',
            category: 'Men\'s Casual Wear',
            gender: 'men',
            type: 'casual',
            badge: 'new'
        },
        {
            id: 7,
            name: 'Designer Sunglasses',
            price: 75.00,
            oldPrice: 95.00,
            image: 'png/0020.jpg',
            category: 'Accessories',
            gender: 'unisex',
            type: 'accessories',
            badge: 'sale'
        },
        {
            id: 8,
            name: 'Women\'s Casual Jeans',
            price: 59.99,
            oldPrice: null,
            image: 'png/0021.jpg',
            category: 'Women\'s Casual Wear',
            gender: 'women',
            type: 'casual',
            badge: null
        }
    ];
    
    // Filter related products (same category or type, but not the current product)
    const relatedProducts = allProducts.filter(product => {
        return product.id != currentProductId && 
               (product.category === currentProduct.category || 
                product.type === currentProduct.type || 
                product.gender === currentProduct.gender);
    }).slice(0, 4); // Limit to 4 products
    
    // Generate HTML for related products
    relatedProductsContainer.innerHTML = relatedProducts.map(product => generateProductCard(product)).join('');
    
    // Add event listeners to the "Add to Cart" buttons
    addCartButtonListeners();
}

/**
 * Initialize review form
 */
function initReviewForm() {
    // Rating selector
    const ratingStars = document.querySelectorAll('.rating-selector i');
    
    ratingStars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            
            // Update stars on hover
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.className = 'fas fa-star';
                } else {
                    s.className = 'far fa-star';
                }
            });
        });
        
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            
            // Set rating on click
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.className = 'fas fa-star';
                    s.classList.add('active');
                } else {
                    s.className = 'far fa-star';
                    s.classList.remove('active');
                }
            });
            
            // Store the selected rating
            this.closest('.form-group').setAttribute('data-selected-rating', rating);
        });
    });
    
    // Reset stars when leaving the rating selector
    const ratingSelector = document.querySelector('.rating-selector');
    if (ratingSelector) {
        ratingSelector.addEventListener('mouseleave', function() {
            const selectedRating = parseInt(this.closest('.form-group').getAttribute('data-selected-rating') || 0);
            
            ratingStars.forEach((s, index) => {
                if (index < selectedRating) {
                    s.className = 'fas fa-star active';
                } else {
                    s.className = 'far fa-star';
                }
            });
        });
    }
    
    // Review form submission
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const ratingValue = parseInt(this.querySelector('.form-group').getAttribute('data-selected-rating') || 0);
            const titleValue = document.getElementById('review-title').value;
            const contentValue = document.getElementById('review-content').value;
            
            // Validate form
            if (!ratingValue) {
                showNotification('Please select a rating.', 'error');
                return;
            }
            
            if (!titleValue) {
                showNotification('Please enter a review title.', 'error');
                return;
            }
            
            if (!contentValue) {
                showNotification('Please enter your review.', 'error');
                return;
            }
            
            // In a real application, this would submit the review to a server
            showNotification('Thank you for your review! It will be published after moderation.', 'success');
            
            // Reset form
            ratingStars.forEach(s => s.className = 'far fa-star');
            this.querySelector('.form-group').removeAttribute('data-selected-rating');
            this.reset();
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