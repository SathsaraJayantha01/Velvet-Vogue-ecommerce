/**
 * Velvet Vogue E-commerce Website
 * Contact Page JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize contact page functionality
    initContactPage();
});

/**
 * Initialize contact page functionality
 */
function initContactPage() {
    // Initialize contact form
    initContactForm();
    
    // Initialize FAQ accordion
    initFaqAccordion();
    
    // Update cart count
    updateCartCount();
}

/**
 * Initialize contact form submission
 */
function initContactForm() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Validate form
            if (!validateContactForm(name, email, subject, message)) {
                return;
            }
            
            // Prepare form data
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('subject', subject);
            formData.append('message', message);
            
            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            // Send the data to the server
            fetch('contact_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                if (data.status === 'success') {
                    // Show success message
                    showNotification(data.message, 'success');
                    // Reset form
                    contactForm.reset();
                } else {
                    // Show error message
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Show error message
                showNotification('An error occurred while submitting the form. Please try again later.', 'error');
                console.error('Error:', error);
            });
        });
    }
}

/**
 * Validate contact form
 * @param {string} name - Name value
 * @param {string} email - Email value
 * @param {string} subject - Subject value
 * @param {string} message - Message value
 * @returns {boolean} Whether the form is valid
 */
function validateContactForm(name, email, subject, message) {
    // Validate name
    if (!name || name.trim() === '') {
        showNotification('Please enter your name.', 'error');
        return false;
    }
    
    // Validate email
    if (!email || email.trim() === '') {
        showNotification('Please enter your email address.', 'error');
        return false;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address.', 'error');
        return false;
    }
    
    // Validate subject
    if (!subject || subject.trim() === '') {
        showNotification('Please enter a subject.', 'error');
        return false;
    }
    
    // Validate message
    if (!message || message.trim() === '') {
        showNotification('Please enter your message.', 'error');
        return false;
    }
    
    return true;
}

/**
 * Initialize FAQ accordion
 */
function initFaqAccordion() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            // Toggle active class
            this.classList.toggle('active');
            
            // Toggle answer visibility
            const answer = this.nextElementSibling;
            if (answer.style.maxHeight) {
                answer.style.maxHeight = null;
                this.querySelector('.faq-toggle i').className = 'fas fa-chevron-down';
            } else {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                this.querySelector('.faq-toggle i').className = 'fas fa-chevron-up';
            }
        });
    });
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