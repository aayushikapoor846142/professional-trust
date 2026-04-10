/**
 * Chat Utilities - Shared functions for chat functionality
 */

// Global variables
let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
let baseUrl = window.location.origin + '/';

/**
 * Format timestamp to readable format
 */
function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);
    
    if (diffInHours < 24) {
        return date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    } else if (diffInHours < 48) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        });
    }
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Scroll to bottom of chat container
 */
function scrollToBottom(containerId = 'chat-messages') {
    const container = document.getElementById(containerId);
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

/**
 * Show loading spinner
 */
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="loading-spinner"><i class="fa-solid fa-spinner fa-spin"></i></div>';
    }
}

/**
 * Hide loading spinner
 */
function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '';
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'success') {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        // Fallback notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

/**
 * Check if element is in viewport
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Generate unique ID
 */
function generateUniqueId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

/**
 * Sanitize HTML content
 */
function sanitizeHtml(html) {
    const div = document.createElement('div');
    div.textContent = html;
    return div.innerHTML;
}

/**
 * Validate file upload
 */
function validateFile(file, maxSize = 10 * 1024 * 1024, allowedTypes = []) {
    const errors = [];
    
    if (file.size > maxSize) {
        errors.push(`File size must be less than ${formatFileSize(maxSize)}`);
    }
    
    if (allowedTypes.length > 0) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            errors.push(`File type not allowed. Allowed types: ${allowedTypes.join(', ')}`);
        }
    }
    
    return errors;
}

/**
 * Auto-resize textarea
 */
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

/**
 * Get URL parameters
 */
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

/**
 * Set URL parameter
 */
function setUrlParameter(name, value) {
    const url = new URL(window.location);
    url.searchParams.set(name, value);
    window.history.pushState({}, '', url);
}

/**
 * Remove URL parameter
 */
function removeUrlParameter(name) {
    const url = new URL(window.location);
    url.searchParams.delete(name);
    window.history.pushState({}, '', url);
}

/**
 * Check if user is online
 */
function isUserOnline(lastSeen) {
    if (!lastSeen) return false;
    
    const lastSeenTime = new Date(lastSeen);
    const now = new Date();
    const diffInMinutes = (now - lastSeenTime) / (1000 * 60);
    
    return diffInMinutes < 5; // Consider online if last seen within 5 minutes
}

/**
 * Format relative time
 */
function formatRelativeTime(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diffInSeconds = Math.floor((now - time) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    return navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard');
    });
}

/**
 * Download file
 */
function downloadFile(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Check if mobile device
 */
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

/**
 * Handle keyboard shortcuts
 */
function handleKeyboardShortcuts(event) {
    // Ctrl/Cmd + Enter to send message
    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
        event.preventDefault();
        const sendButton = document.getElementById('send-message-btn');
        if (sendButton) {
            sendButton.click();
        }
    }
    
    // Escape to close modals/popups
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show, .popup.show');
        modals.forEach(modal => {
            modal.classList.remove('show');
        });
    }
}

// Initialize keyboard shortcuts
document.addEventListener('keydown', handleKeyboardShortcuts);

// Export functions for use in other modules
window.ChatUtils = {
    formatTimestamp,
    formatFileSize,
    scrollToBottom,
    showLoading,
    hideLoading,
    showNotification,
    debounce,
    throttle,
    isInViewport,
    generateUniqueId,
    sanitizeHtml,
    validateFile,
    autoResizeTextarea,
    getUrlParameter,
    setUrlParameter,
    removeUrlParameter,
    isUserOnline,
    formatRelativeTime,
    copyToClipboard,
    downloadFile,
    isMobileDevice
}; 