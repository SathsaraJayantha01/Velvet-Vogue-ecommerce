<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get and clear message
function get_auth_message() {
    $message = [
        'text' => isset($_SESSION['auth_message']) ? $_SESSION['auth_message'] : '',
        'type' => isset($_SESSION['auth_message_type']) ? $_SESSION['auth_message_type'] : ''
    ];
    
    // Clear the message after retrieving
    unset($_SESSION['auth_message']);
    unset($_SESSION['auth_message_type']);
    
    return $message;
}
?>

<script>
// Function to display auth message if exists
document.addEventListener('DOMContentLoaded', function() {
    <?php 
    $message = get_auth_message();
    if (!empty($message['text'])):
    ?>
    
    // Create message element
    var messageElement = document.createElement('div');
    messageElement.className = 'auth-notification <?php echo $message['type']; ?>';
    messageElement.innerHTML = `
        <div class="auth-notification-content">
            <i class="fas <?php echo $message['type'] === 'success' ? 'fa-check-circle' : ($message['type'] === 'error' ? 'fa-times-circle' : 'fa-info-circle'); ?>"></i>
            <p><?php echo addslashes($message['text']); ?></p>
        </div>
        <button class="close-notification"><i class="fas fa-times"></i></button>
    `;
    
    // Add styles
    var style = document.createElement('style');
    style.textContent = `
        .auth-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 300px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            z-index: 1000;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }
        
        .auth-notification.show {
            transform: translateX(0);
        }
        
        .auth-notification.success {
            border-left: 4px solid #4caf50;
        }
        
        .auth-notification.error {
            border-left: 4px solid #f44336;
        }
        
        .auth-notification.info {
            border-left: 4px solid #2196f3;
        }
        
        .auth-notification-content {
            display: flex;
            align-items: flex-start;
            flex-grow: 1;
        }
        
        .auth-notification-content i {
            margin-right: 10px;
            font-size: 1.25rem;
        }
        
        .auth-notification.success .auth-notification-content i {
            color: #4caf50;
        }
        
        .auth-notification.error .auth-notification-content i {
            color: #f44336;
        }
        
        .auth-notification.info .auth-notification-content i {
            color: #2196f3;
        }
        
        .auth-notification-content p {
            margin: 0;
            line-height: 1.4;
        }
        
        .close-notification {
            background: none;
            border: none;
            color: #555555;
            cursor: pointer;
            font-size: 0.9rem;
            margin-left: 10px;
            padding: 0;
            align-self: flex-start;
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(messageElement);
    
    // Show notification
    setTimeout(() => {
        messageElement.classList.add('show');
    }, 10);
    
    // Add close button functionality
    const closeBtn = messageElement.querySelector('.close-notification');
    closeBtn.addEventListener('click', () => {
        messageElement.classList.remove('show');
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (document.body.contains(messageElement)) {
            messageElement.classList.remove('show');
            setTimeout(() => {
                messageElement.remove();
            }, 300);
        }
    }, 5000);
    
    <?php endif; ?>
});
</script>