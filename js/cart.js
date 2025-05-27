/**
 * Velvet Vogue E-commerce Website
 * Cart Management JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const cartCount = document.getElementById('cart-count');
    const cartItemsContainer = document.getElementById('cart-items-container');
    const emptyCart = document.getElementById('empty-cart');
    const cartWithItems = document.getElementById('cart-with-items');
    const cartSubtotal = document.getElementById('cart-subtotal');
    const cartShipping = document.getElementById('cart-shipping');
    const cartDiscount = document.getElementById('cart-discount');
    const discountRow = document.getElementById('discount-row');
    const cartTotal = document.getElementById('cart-total');
    const updateCartBtn = document.getElementById('update-cart');
    const checkoutBtn = document.getElementById('checkout-btn');
    const applyCouponBtn = document.getElementById('apply-coupon');
    const couponCodeInput = document.getElementById('coupon-code');
    
    // Modal elements
    const checkoutModal = document.getElementById('checkout-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const cancelCheckoutBtn = document.querySelector('.cancel-checkout');
    const modalSubtotal = document.getElementById('modal-subtotal');
    const modalShipping = document.getElementById('modal-shipping');
    const modalDiscount = document.getElementById('modal-discount');
    const modalDiscountRow = document.getElementById('modal-discount-row');
    const modalTotal = document.getElementById('modal-total');
    
    // Add to cart buttons on product pages
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    // Format currency helper
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }
    
    // Update the cart counts everywhere on the page
    function updateCartCount(count) {
        if (cartCount) {
            cartCount.textContent = count;
        }
    }
    
    // Update the cart summary
    function updateCartSummary(subtotal, shipping, discount, total) {
        if (cartSubtotal) cartSubtotal.textContent = formatCurrency(subtotal);
        if (cartShipping) cartShipping.textContent = shipping > 0 ? formatCurrency(shipping) : 'Free';
        if (cartDiscount) cartDiscount.textContent = formatCurrency(discount * -1);
        if (cartTotal) cartTotal.textContent = formatCurrency(total);
        
        // Show/hide discount row
        if (discountRow) {
            discountRow.style.display = discount > 0 ? 'flex' : 'none';
        }
        
        // Update modal values if they exist
        if (modalSubtotal) modalSubtotal.textContent = formatCurrency(subtotal);
        if (modalShipping) modalShipping.textContent = shipping > 0 ? formatCurrency(shipping) : 'Free';
        if (modalDiscount) modalDiscount.textContent = formatCurrency(discount * -1);
        if (modalTotal) modalTotal.textContent = formatCurrency(total);
        
        if (modalDiscountRow) {
            modalDiscountRow.style.display = discount > 0 ? 'flex' : 'none';
        }
    }
    
    // Toggle between empty cart and cart with items
    function toggleCartDisplay(isEmpty) {
        if (emptyCart && cartWithItems) {
            emptyCart.style.display = isEmpty ? 'block' : 'none';
            cartWithItems.style.display = isEmpty ? 'none' : 'block';
        }
    }
    
    /**
     * Add to cart function
     */
    function addToCart(productId, quantity, options = {}) {
        fetch('cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'add',
                product_id: productId,
                quantity: quantity || 1,
                options: options
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the cart count
                updateCartCount(data.cart_count);
                
                // Show success message
                alert(data.message || 'Product added to cart!');
                
                // If on cart page, reload the page to show updated cart
                if (window.location.href.indexOf('cart.php') > -1) {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Failed to add product to cart.');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    // Update cart quantity
    function updateCartQuantity(productId, quantity) {
        fetch('cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'update',
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the cart count
                updateCartCount(data.cart_count);
                
                // Update the cart summary
                updateCartSummary(data.subtotal, data.shipping, data.discount, data.total);
                
                // Update product subtotal
                const item = document.querySelector(`.cart-item[data-id="${productId}"]`);
                if (item) {
                    const subtotalElement = item.querySelector('.subtotal-col');
                    if (subtotalElement) {
                        subtotalElement.textContent = formatCurrency(data.item_subtotal);
                    }
                }
                
                // If cart is empty, show empty cart view
                toggleCartDisplay(data.cart_count === 0);
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
        });
    }
    
    // Remove from cart
    function removeFromCart(productId) {
        fetch('cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'remove',
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the cart count
                updateCartCount(data.cart_count);
                
                // Update the cart summary
                updateCartSummary(data.subtotal, data.shipping, data.discount, data.total);
                
                // Remove the item from the DOM
                const item = document.querySelector(`.cart-item[data-id="${productId}"]`);
                if (item) {
                    item.remove();
                }
                
                // If cart is empty, show empty cart view
                toggleCartDisplay(data.cart_count === 0);
            }
        })
        .catch(error => {
            console.error('Error removing from cart:', error);
        });
    }
    
    // Apply coupon
    function applyCoupon(couponCode) {
        fetch('cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'apply_coupon',
                coupon_code: couponCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the cart summary
                updateCartSummary(data.subtotal, data.shipping, data.discount, data.total);
                
                // Show success message
                alert(data.message || 'Coupon applied successfully!');
            } else {
                alert(data.message || 'Invalid coupon code.');
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    // Event Listeners for Cart Page
    if (cartItemsContainer) {
        // Quantity change events
        cartItemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('minus') || e.target.parentElement.classList.contains('minus')) {
                const btn = e.target.classList.contains('minus') ? e.target : e.target.parentElement;
                const productId = btn.dataset.id;
                const input = btn.parentElement.querySelector('input');
                let quantity = parseInt(input.value) - 1;
                
                if (quantity >= 1) {
                    input.value = quantity;
                    updateCartQuantity(productId, quantity);
                }
            }
            
            if (e.target.classList.contains('plus') || e.target.parentElement.classList.contains('plus')) {
                const btn = e.target.classList.contains('plus') ? e.target : e.target.parentElement;
                const productId = btn.dataset.id;
                const input = btn.parentElement.querySelector('input');
                let quantity = parseInt(input.value) + 1;
                
                if (quantity <= parseInt(input.getAttribute('max'))) {
                    input.value = quantity;
                    updateCartQuantity(productId, quantity);
                }
            }
            
            if (e.target.classList.contains('remove-item') || e.target.parentElement.classList.contains('remove-item')) {
                const btn = e.target.classList.contains('remove-item') ? e.target : e.target.parentElement;
                const productId = btn.dataset.id;
                
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    removeFromCart(productId);
                }
            }
        });
        
        // Manual quantity input change
        cartItemsContainer.addEventListener('change', function(e) {
            if (e.target.type === 'number') {
                const productId = e.target.dataset.id;
                let quantity = parseInt(e.target.value);
                
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    e.target.value = 1;
                }
                
                const max = parseInt(e.target.getAttribute('max'));
                if (quantity > max) {
                    quantity = max;
                    e.target.value = max;
                }
                
                updateCartQuantity(productId, quantity);
            }
        });
    }
    
    // Update Cart button
    if (updateCartBtn) {
        updateCartBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }
    
    // Apply Coupon button
    if (applyCouponBtn && couponCodeInput) {
        applyCouponBtn.addEventListener('click', function() {
            const couponCode = couponCodeInput.value.trim();
            if (couponCode) {
                applyCoupon(couponCode);
            } else {
                alert('Please enter a coupon code.');
            }
        });
    }
    
    // Checkout button
    if (checkoutBtn && checkoutModal) {
        checkoutBtn.addEventListener('click', function() {
            checkoutModal.style.display = 'flex';
        });
    }
    
    // Modal close buttons
    if (closeModalBtn && checkoutModal) {
        closeModalBtn.addEventListener('click', function() {
            checkoutModal.style.display = 'none';
        });
    }
    
    if (cancelCheckoutBtn && checkoutModal) {
        cancelCheckoutBtn.addEventListener('click', function() {
            checkoutModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (checkoutModal && e.target === checkoutModal) {
            checkoutModal.style.display = 'none';
        }
    });
    
    // Add to cart buttons
    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.product;
                
                // Get options if available (size, color, etc.)
                let options = {};
                
                // Check if we're on a product detail page with options
                const sizeSelect = document.getElementById('product-size');
                const colorSelect = document.getElementById('product-color');
                const quantityInput = document.getElementById('product-quantity');
                
                if (sizeSelect) {
                    options.size = sizeSelect.value;
                }
                
                if (colorSelect) {
                    options.color = colorSelect.value;
                }
                
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                addToCart(productId, quantity, options);
            });
        });
    }
    
    // Initialize cart displays
    if (typeof cartData !== 'undefined') {
        const itemCount = Object.keys(cartData.items).length;
        updateCartCount(itemCount);
        toggleCartDisplay(itemCount === 0);
    }

    // Force reload the page if on cart page
    if (window.location.href.indexOf('cart.php') > -1) {
    window.location.reload(true); // true forces reload from server, not cache
    }
});