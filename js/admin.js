/**
 * Velvet Vogue E-commerce Website
 * Admin Dashboard JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin dashboard
    initAdminDashboard();
});

/**
 * Initialize admin dashboard functionality
 */
function initAdminDashboard() {
    // Initialize admin sidebar tabs
    initAdminTabs();
    
    // Initialize user dropdown menu
    initUserDropdown();
    
    // Initialize product management
    initProductManagement();
    
    // Initialize admin filters
    initAdminFilters();
    
    // Initialize bulk actions
    initBulkActions();
    
    // Initialize logout
    initAdminLogout();
}

/**
 * Initialize admin sidebar tabs
 */
function initAdminTabs() {
    const adminTabLinks = document.querySelectorAll('.admin-sidebar-nav a');
    const adminTabs = document.querySelectorAll('.admin-tab');
    
    adminTabLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            const tabId = this.getAttribute('data-tab');
            
            // Update active link
            adminTabLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding tab
            adminTabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.id === `${tabId}-tab`) {
                    tab.classList.add('active');
                }
            });
        });
    });
    
    // Handle "View All" links
    const viewAllLinks = document.querySelectorAll('.view-all');
    
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            const tabId = this.getAttribute('data-tab');
            
            // Simulate clicking on the tab
            const tabLink = document.querySelector(`.admin-sidebar-nav a[data-tab="${tabId}"]`);
            if (tabLink) {
                tabLink.click();
            }
        });
    });
}

/**
 * Initialize user dropdown menu
 */
function initUserDropdown() {
    const adminUser = document.querySelector('.admin-user');
    const adminDropdown = document.querySelector('.admin-dropdown');
    
    if (adminUser && adminDropdown) {
        adminUser.addEventListener('click', function() {
            adminDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.admin-user') && !event.target.closest('.admin-dropdown')) {
                adminDropdown.classList.remove('active');
            }
        });
    }
}

/**
 * Initialize product management
 */
function initProductManagement() {
    // Add product button
    const addProductBtn = document.getElementById('add-product-btn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', function() {
            showProductModal('add');
        });
    }
    
    // Edit product buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            showProductModal('edit', productId);
        });
    });
    
    // Delete product buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            confirmDeleteProduct(productId);
        });
    });
    
    // Select all products checkbox
    const selectAllCheckbox = document.getElementById('select-all-products');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
}

/**
 * Show product modal for adding or editing a product
 * @param {string} mode - Mode ('add' or 'edit')
 * @param {string|number} productId - Product ID (for edit mode)
 */
function showProductModal(mode, productId) {
    const productModal = document.getElementById('product-modal');
    const productModalTitle = document.getElementById('product-modal-title');
    const productForm = document.getElementById('product-form');
    const productIdInput = document.getElementById('product-id');
    
    if (!productModal || !productModalTitle || !productForm || !productIdInput) return;
    
    // Set modal title and mode
    productModalTitle.textContent = mode === 'add' ? 'Add New Product' : 'Edit Product';
    
    // Reset form
    productForm.reset();
    
    if (mode === 'edit') {
        // Get product data
        const product = getProductById(productId);
        if (product) {
            // Fill form with product data
            fillProductForm(product);
        }
    } else {
        // Clear product ID for add mode
        productIdInput.value = '';
    }
    
    // Show modal
    productModal.style.display = 'block';
    
    // Close modal when clicking the X button
    const closeModal = productModal.querySelector('.close-modal');
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            productModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking the cancel button
    const cancelModal = productModal.querySelector('.cancel-modal');
    if (cancelModal) {
        cancelModal.addEventListener('click', function(event) {
            event.preventDefault();
            productModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside the content
    productModal.addEventListener('click', function(event) {
        if (event.target === productModal) {
            productModal.style.display = 'none';
        }
    });
    
    // Handle form submission
    if (productForm) {
        productForm.addEventListener('submit', function(event) {
            event.preventDefault();
            saveProduct(mode);
        });
    }
    
    // Initialize product image upload
    initProductImageUpload();
    
    // Initialize add custom color button
    initAddCustomColor();
}

/**
 * Fill product form with product data
 * @param {Object} product - Product data
 */
function fillProductForm(product) {
    // Set product ID
    document.getElementById('product-id').value = product.id;
    
    // Basic information
    document.getElementById('product-name').value = product.name;
    document.getElementById('product-sku').value = product.sku || `VV-${product.id}`;
    document.getElementById('product-category').value = getCategoryValue(product);
    document.getElementById('product-price').value = product.price;
    document.getElementById('product-sale-price').value = product.oldPrice || '';
    document.getElementById('product-stock').value = product.stock || 10;
    document.getElementById('product-status').value = product.status || 'published';
    document.getElementById('product-description').value = product.description || '';
    
    // Product options
    // Sizes
    const sizes = Array.isArray(product.sizes) ? product.sizes : ['m'];
    const sizeCheckboxes = document.querySelectorAll('input[name="size"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = sizes.includes(checkbox.value);
    });
    
    // Colors
    const colors = Array.isArray(product.colors) 
        ? product.colors.map(c => typeof c === 'string' ? c : c.name.toLowerCase()) 
        : ['black'];
    
    const colorCheckboxes = document.querySelectorAll('input[name="color"]');
    colorCheckboxes.forEach(checkbox => {
        checkbox.checked = colors.includes(checkbox.value);
    });
    
    // Additional information
    document.getElementById('product-specifications').value = formatSpecifications(product.specifications);
    document.getElementById('product-weight').value = product.weight || '';
    document.getElementById('product-country').value = product.country || '';
    document.getElementById('product-tags').value = product.tags ? product.tags.join(', ') : '';
    
    // Image preview (if available)
    const mainImagePreview = document.getElementById('main-image-preview');
    if (mainImagePreview && product.image) {
        mainImagePreview.src = product.image;
    }
}

/**
 * Get category value for product form
 * @param {Object} product - Product data
 * @returns {string} Category value
 */
function getCategoryValue(product) {
    if (product.gender === 'men' && product.type === 'casual') {
        return 'men-casual';
    } else if (product.gender === 'men' && product.type === 'formal') {
        return 'men-formal';
    } else if (product.gender === 'women' && product.type === 'casual') {
        return 'women-casual';
    } else if (product.gender === 'women' && product.type === 'formal') {
        return 'women-formal';
    } else if (product.type === 'accessories') {
        return 'accessories';
    }
    
    return '';
}

/**
 * Format specifications object to string
 * @param {Object} specifications - Specifications object
 * @returns {string} Formatted specifications string
 */
function formatSpecifications(specifications) {
    if (!specifications) return '';
    
    return Object.entries(specifications)
        .map(([key, value]) => {
            // Format key (e.g., 'countryOfOrigin' -> 'Country Of Origin')
            const formattedKey = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
            return `${formattedKey}: ${value}`;
        })
        .join('\n');
}

/**
 * Initialize product image upload
 */
function initProductImageUpload() {
    const mainImageInput = document.getElementById('product-main-image');
    const mainImagePreview = document.getElementById('main-image-preview');
    
    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    mainImagePreview.src = e.target.result;
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    const additionalImagesInput = document.getElementById('product-additional-images');
    const additionalImagesContainer = document.querySelector('.additional-images');
    
    if (additionalImagesInput && additionalImagesContainer) {
        additionalImagesInput.addEventListener('change', function() {
            const files = this.files;
            
            for (let i = 0; i < Math.min(files.length, 3); i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewElements = additionalImagesContainer.querySelectorAll('.image-preview-small');
                    
                    if (previewElements.length > i) {
                        const imgElement = previewElements[i].querySelector('img');
                        if (imgElement) {
                            imgElement.src = e.target.result;
                        }
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Remove image buttons
    const removeImageButtons = document.querySelectorAll('.remove-image');
    
    removeImageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imagePreview = this.closest('.image-preview-small');
            const imgElement = imagePreview.querySelector('img');
            
            if (imgElement) {
                imgElement.src = 'images/placeholder.jpg';
            }
        });
    });
}

/**
 * Initialize add custom color button
 */
function initAddCustomColor() {
    const addColorBtn = document.getElementById('add-color-btn');
    const colorOptionsContainer = document.querySelector('.color-options-admin');
    
    if (addColorBtn && colorOptionsContainer) {
        addColorBtn.addEventListener('click', function() {
            // Show color picker modal
            showColorPickerModal(colorOptionsContainer);
        });
    }
}

/**
 * Show color picker modal
 * @param {HTMLElement} colorOptionsContainer - Color options container
 */
function showColorPickerModal(colorOptionsContainer) {
    // Create modal element
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'color-picker-modal';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Add Custom Color</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="color-picker-form">
                    <div class="form-group">
                        <label for="color-name">Color Name</label>
                        <input type="text" id="color-name" required>
                    </div>
                    <div class="form-group">
                        <label for="color-code">Color Code</label>
                        <input type="color" id="color-code" value="#3366ff" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="admin-btn primary">Add Color</button>
                        <button type="button" class="admin-btn secondary cancel-modal">Cancel</button>
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
    const colorPickerForm = document.getElementById('color-picker-form');
    if (colorPickerForm) {
        colorPickerForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const colorName = document.getElementById('color-name').value;
            const colorCode = document.getElementById('color-code').value;
            
            // Validate form
            if (!colorName || !colorCode) {
                showNotification('Please fill in all required fields.', 'error');
                return;
            }
            
            // Add new color option
            const colorOption = document.createElement('label');
            colorOption.className = 'color-option';
            colorOption.innerHTML = `
                <input type="checkbox" name="color" value="${colorName.toLowerCase()}" checked>
                <span class="color-swatch" style="background-color: ${colorCode};"></span>
                <span class="color-name">${colorName}</span>
            `;
            
            colorOptionsContainer.appendChild(colorOption);
            
            // Close modal
            modal.style.display = 'none';
            setTimeout(() => {
                modal.remove();
            }, 300);
            
            // Show notification
            showNotification('Color added successfully.', 'success');
        });
    }
}

/**
 * Save product (add or edit)
 * @param {string} mode - Mode ('add' or 'edit')
 */
function saveProduct(mode) {
    // Get form values
    const productId = document.getElementById('product-id').value;
    const productName = document.getElementById('product-name').value;
    const productSku = document.getElementById('product-sku').value;
    const productCategory = document.getElementById('product-category').value;
    const productPrice = parseFloat(document.getElementById('product-price').value);
    const productSalePrice = document.getElementById('product-sale-price').value 
        ? parseFloat(document.getElementById('product-sale-price').value) 
        : null;
    const productStock = parseInt(document.getElementById('product-stock').value);
    const productStatus = document.getElementById('product-status').value;
    const productDescription = document.getElementById('product-description').value;
    
    // Get selected sizes
    const sizeCheckboxes = document.querySelectorAll('input[name="size"]:checked');
    const sizes = Array.from(sizeCheckboxes).map(checkbox => checkbox.value);
    
    // Get selected colors
    const colorCheckboxes = document.querySelectorAll('input[name="color"]:checked');
    const colors = Array.from(colorCheckboxes).map(checkbox => checkbox.value);
    
    // Get additional information
    const productSpecifications = document.getElementById('product-specifications').value;
    const productWeight = document.getElementById('product-weight').value 
        ? parseFloat(document.getElementById('product-weight').value) 
        : null;
    const productCountry = document.getElementById('product-country').value;
    const productTags = document.getElementById('product-tags').value 
        ? document.getElementById('product-tags').value.split(',').map(tag => tag.trim()) 
        : [];
    
    // Parse category to get gender and type
    const { gender, type } = parseCategoryValue(productCategory);
    
    // Create product object
    const product = {
        id: mode === 'add' ? generateProductId() : productId,
        name: productName,
        sku: productSku,
        price: productPrice,
        oldPrice: productSalePrice,
        stock: productStock,
        status: productStatus,
        description: productDescription,
        gender: gender,
        type: type,
        sizes: sizes,
        colors: colors.map(color => ({ name: color, code: getColorCode(color) })),
        specifications: parseSpecifications(productSpecifications),
        weight: productWeight,
        country: productCountry,
        tags: productTags,
        image: document.getElementById('main-image-preview').src
    };
    
    // In a real application, this would send the product data to the server
    // For demonstration, just show a success message
    const modalElement = document.getElementById('product-modal');
    
    if (modalElement) {
        modalElement.style.display = 'none';
    }
    
    // Show notification
    showNotification(`Product ${mode === 'add' ? 'added' : 'updated'} successfully.`, 'success');
    
    // In a real application, this would reload the products list
    // For demonstration, reload the page after a delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

/**
 * Parse category value to get gender and type
 * @param {string} categoryValue - Category value
 * @returns {Object} Object with gender and type
 */
function parseCategoryValue(categoryValue) {
    switch (categoryValue) {
        case 'men-casual':
            return { gender: 'men', type: 'casual' };
        case 'men-formal':
            return { gender: 'men', type: 'formal' };
        case 'women-casual':
            return { gender: 'women', type: 'casual' };
        case 'women-formal':
            return { gender: 'women', type: 'formal' };
        case 'accessories':
            return { gender: 'unisex', type: 'accessories' };
        default:
            return { gender: '', type: '' };
    }
}

/**
 * Parse specifications string to object
 * @param {string} specificationsString - Specifications string
 * @returns {Object} Specifications object
 */
function parseSpecifications(specificationsString) {
    if (!specificationsString) return {};
    
    const specifications = {};
    const lines = specificationsString.split('\n');
    
    lines.forEach(line => {
        const colonIndex = line.indexOf(':');
        
        if (colonIndex !== -1) {
            const key = line.substring(0, colonIndex).trim();
            const value = line.substring(colonIndex + 1).trim();
            
            // Convert key to camelCase
            const camelCaseKey = key.toLowerCase()
                .replace(/(?:^\w|[A-Z]|\b\w)/g, (letter, index) => 
                    index === 0 ? letter.toLowerCase() : letter.toUpperCase()
                )
                .replace(/\s+/g, '');
            
            specifications[camelCaseKey] = value;
        }
    });
    
    return specifications;
}

/**
 * Get color code for a color name
 * @param {string} colorName - Color name
 * @returns {string} Color code
 */
function getColorCode(colorName) {
    const colorMap = {
        'black': '#000',
        'white': '#fff',
        'red': '#ff0000',
        'blue': '#0000ff',
        'green': '#00ff00',
        'yellow': '#ffff00',
        'navy blue': '#000080',
        'charcoal': '#36454F',
        'silver': '#C0C0C0',
        'gold': '#FFD700',
        'rose gold': '#B76E79',
        'brown': '#964B00',
        'pink': '#FFC0CB',
        'nude': '#E3BC9A'
    };
    
    return colorMap[colorName.toLowerCase()] || '#000000';
}

/**
 * Generate a unique product ID
 * @returns {number} New product ID
 */
function generateProductId() {
    // In a real application, this would be handled by the server
    // For demonstration, generate a random ID
    return Math.floor(Math.random() * 1000) + 20;
}

/**
 * Confirm delete product
 * @param {string|number} productId - Product ID
 */
function confirmDeleteProduct(productId) {
    // Create confirmation modal
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'confirm-delete-modal';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" class="admin-btn primary" id="confirm-delete-btn">Delete</button>
                    <button type="button" class="admin-btn secondary cancel-modal">Cancel</button>
                </div>
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
    
    // Handle confirm delete button
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            // In a real application, this would send a request to the server
            // For demonstration, just show a success message
            modal.style.display = 'none';
            setTimeout(() => {
                modal.remove();
                
                // Show notification
                showNotification('Product deleted successfully.', 'success');
                
                // In a real application, this would remove the product from the list
                // For demonstration, reload the page after a delay
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }, 300);
        });
    }
}

/**
 * Initialize admin filters
 */
function initAdminFilters() {
    // Product search
    const productSearch = document.getElementById('product-search');
    const filterProductsBtn = document.getElementById('filter-products-btn');
    
    if (productSearch && filterProductsBtn) {
        // Search when pressing Enter
        productSearch.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                filterProducts();
            }
        });
        
        // Search when clicking the filter button
        filterProductsBtn.addEventListener('click', function() {
            filterProducts();
        });
    }
}

/**
 * Filter products
 */
function filterProducts() {
    // In a real application, this would filter the products list
    // For demonstration, just show a notification
    showNotification('Filtering functionality would be implemented here.', 'info');
}

/**
 * Initialize bulk actions
 */
function initBulkActions() {
    const bulkActionSelect = document.getElementById('bulk-action');
    const applyBulkActionBtn = document.getElementById('apply-bulk-action');
    
    if (bulkActionSelect && applyBulkActionBtn) {
        applyBulkActionBtn.addEventListener('click', function() {
            const selectedAction = bulkActionSelect.value;
            
            if (!selectedAction) {
                showNotification('Please select an action.', 'error');
                return;
            }
            
            // Get selected products
            const selectedProducts = getSelectedProducts();
            
            if (selectedProducts.length === 0) {
                showNotification('Please select at least one product.', 'error');
                return;
            }
            
            // Perform bulk action
            performBulkAction(selectedAction, selectedProducts);
        });
    }
}

/**
 * Get selected products
 * @returns {Array} Array of selected product IDs
 */
function getSelectedProducts() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    return Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        return row ? row.getAttribute('data-id') : null;
    }).filter(id => id !== null);
}

/**
 * Perform bulk action
 * @param {string} action - Bulk action
 * @param {Array} productIds - Array of product IDs
 */
function performBulkAction(action, productIds) {
    // In a real application, this would send a request to the server
    // For demonstration, just show a success message
    
    let message = '';
    
    switch (action) {
        case 'delete':
            message = `Deleted ${productIds.length} products successfully.`;
            break;
        case 'mark-instock':
            message = `Marked ${productIds.length} products as In Stock.`;
            break;
        case 'mark-outofstock':
            message = `Marked ${productIds.length} products as Out of Stock.`;
            break;
        default:
            message = 'Action performed successfully.';
    }
    
    showNotification(message, 'success');
    
    // In a real application, this would update the products list
    // For demonstration, reload the page after a delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

/**
 * Initialize admin logout
 */
function initAdminLogout() {
    const adminLogoutBtn = document.getElementById('admin-logout');
    
    if (adminLogoutBtn) {
        adminLogoutBtn.addEventListener('click', function() {
            // Just let the normal link behavior happen
            // The logout.php script will handle the server-side logout and redirect
            
            // Clear any client-side data if needed
            try {
                localStorage.removeItem('adminSession');
                sessionStorage.removeItem('adminData');
            } catch (e) {
                console.log('Error clearing local storage:', e);
            }
        });
    }
}

/**
 * Get product by ID
 * @param {string|number} productId - Product ID
 * @returns {Object|null} Product data or null if not found
 */
function getProductById(productId) {
    // In a real application, this would fetch data from an API
    const products = [
        {
            id: 1,
            name: 'Men\'s Casual Shirt',
            sku: 'VV-MS-12345',
            price: 65.00,
            oldPrice: 85.00,
            stock: 25,
            status: 'published',
            description: 'This stylish and comfortable casual shirt is perfect for expressing your unique identity. Made from high-quality cotton-blend fabric, it offers both durability and a trendy look that will turn heads.',
            gender: 'men',
            type: 'casual',
            sizes: ['xs', 's', 'm', 'l', 'xl'],
            colors: [
                { name: 'Black', code: '#000' },
                { name: 'Blue', code: '#0000ff' },
                { name: 'Red', code: '#ff0000' },
                { name: 'White', code: '#fff' }
            ],
            specifications: {
                material: '60% Cotton, 40% Polyester',
                fit: 'Regular fit',
                care: 'Machine wash cold, tumble dry low',
                collar: 'Button-down collar',
                sleeve: 'Long sleeve',
                countryOfOrigin: 'Made in USA'
            },
            weight: 0.5,
            country: 'USA',
            tags: ['shirt', 'casual', 'men', 'cotton'],
            image: 'PNG/0002.jpg',
        },
        {
            id: 2,
            name: 'Formal Blazer',
            sku: 'VV-FB-23456',
            price: 120.00,
            oldPrice: null,
            stock: 18,
            status: 'published',
            description: 'Elevate your formal attire with this sophisticated blazer. Tailored to perfection, this blazer combines classic styling with modern details for a timeless look that transitions seamlessly from office to evening events.',
            gender: 'men',
            type: 'formal',
            sizes: ['s', 'm', 'l', 'xl', 'xxl'],
            colors: [
                { name: 'Navy Blue', code: '#000080' },
                { name: 'Black', code: '#000' },
                { name: 'Charcoal', code: '#36454F' }
            ],
            specifications: {
                material: '80% Wool, 20% Polyester',
                fit: 'Slim fit',
                care: 'Dry clean only',
                lapel: 'Notch lapel',
                buttons: 'Two-button closure',
                countryOfOrigin: 'Made in Italy'
            },
            weight: 0.8,
            country: 'Italy',
            tags: ['blazer', 'formal', 'men', 'wool'],
            image: 'Product2.jpg'
        },
        {
            id: 3,
            name: 'Elegant Dress',
            sku: 'VV-ED-34567',
            price: 89.99,
            oldPrice: 119.99,
            stock: 12,
            status: 'published',
            description: 'Turn heads with this stunning elegant dress that flatters your figure and makes a statement. Perfect for special occasions, parties, or whenever you want to feel exceptionally confident and beautiful.',
            gender: 'women',
            type: 'formal',
            sizes: ['xs', 's', 'm', 'l'],
            colors: [
                { name: 'Red', code: '#ff0000' },
                { name: 'Black', code: '#000' },
                { name: 'Navy Blue', code: '#000080' }
            ],
            specifications: {
                material: '95% Polyester, 5% Spandex',
                fit: 'A-line',
                care: 'Hand wash cold, line dry',
                length: 'Knee length',
                closure: 'Hidden back zipper',
                countryOfOrigin: 'Made in Spain'
            },
            weight: 0.6,
            country: 'Spain',
            tags: ['dress', 'formal', 'women', 'elegant'],
            image: 'PNG/0016.jpg'
        },
        {
            id: 4,
            name: 'Classic Watch',
            sku: 'VV-CW-45678',
            price: 149.99,
            oldPrice: null,
            stock: 8,
            status: 'published',
            description: 'This timeless classic watch combines elegant design with precision engineering. The perfect accessory for any outfit, it adds a touch of sophistication and functionality to your everyday style.',
            gender: 'unisex',
            type: 'accessories',
            sizes: ['one size'],
            colors: [
                { name: 'Silver', code: '#C0C0C0' },
                { name: 'Gold', code: '#FFD700' },
                { name: 'Rose Gold', code: '#B76E79' }
            ],
            specifications: {
                case: 'Stainless steel, 40mm diameter',
                movement: 'Swiss quartz',
                waterResistance: 'Water-resistant to 30 meters',
                band: 'Genuine leather strap',
                features: 'Date display, luminous hands',
                warranty: '2-year international warranty'
            },
            weight: 0.2,
            country: 'Switzerland',
            tags: ['watch', 'accessories', 'classic', 'unisex'],
            image: 'PNG/0017.jpg'
        },
        {
            id: 5,
            name: 'Leather Wallet',
            sku: 'VV-LW-56789',
            price: 45.00,
            oldPrice: null,
            stock: 0,
            status: 'published',
            description: 'Crafted from genuine leather, this stylish wallet combines functionality with sophisticated design. With multiple card slots and compartments, it keeps your essentials organized while making a statement about your refined taste.',
            gender: 'unisex',
            type: 'accessories',
            sizes: ['one size'],
            colors: [
                { name: 'Brown', code: '#964B00' },
                { name: 'Black', code: '#000' }
            ],
            specifications: {
                material: 'Genuine leather',
                dimensions: '4.5" x 3.5"',
                features: 'Multiple card slots, bill compartment, coin pocket',
                lining: 'Polyester lining',
                closure: 'Snap closure',
                countryOfOrigin: 'Made in Italy'
            },
            weight: 0.1,
            country: 'Italy',
            tags: ['wallet', 'accessories', 'leather', 'unisex'],
            image: 'PNG/0018.jpg'
        }
    ];
    
    return products.find(product => product.id == productId) || null;
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