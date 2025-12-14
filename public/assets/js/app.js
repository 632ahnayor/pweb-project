/**
 * MangroveTour - Frontend JavaScript
 * Main application logic for visitor-facing pages
 */

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    loadPublicReviews();
    setupFormValidation();
});

/**
 * Load and display public reviews
 */
async function loadPublicReviews() {
    try {
        const response = await fetch('/pweb-project/backend/api/review.php?action=public');
        const data = await response.json();

        if (data.success && data.data) {
            const reviewsContainer = document.getElementById('reviewsContainer');
            
            if (reviewsContainer && data.data.length > 0) {
                reviewsContainer.innerHTML = data.data.map(review => `
                    <div class="col-md-6 col-lg-4">
                        <div class="review-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); height: 100%;">
                            <div class="rating" style="color: #ffc107; margin-bottom: 10px;">
                                ${'<i class="bi bi-star-fill"></i>'.repeat(review.rating)}
                                ${'<i class="bi bi-star"></i>'.repeat(5 - review.rating)}
                            </div>
                            <p style="color: #333; margin: 10px 0; font-size: 14px;">"${escapeHtml(review.komentar)}"</p>
                            <p style="color: #666; font-size: 12px; margin: 0;">— ${escapeHtml(review.nama)}</p>
                        </div>
                    </div>
                `).join('');
            } else if (reviewsContainer) {
                reviewsContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted">No reviews yet. Be the first to share your experience!</p>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Error loading reviews:', error);
    }
}

/**
 * Setup form validation for booking and review forms
 */
function setupFormValidation() {
    const bookingForm = document.getElementById('bookingForm');
    const reviewForm = document.getElementById('reviewForm');

    if (bookingForm) {
        setupBookingFormValidation(bookingForm);
    }

    if (reviewForm) {
        setupReviewFormValidation(reviewForm);
    }
}

/**
 * Setup booking form validation
 */
function setupBookingFormValidation(form) {
    form.addEventListener('input', function(e) {
        const field = e.target;
        
        // Validate phone number format
        if (field.name === 'no_hp') {
            field.value = field.value.replace(/[^0-9+]/g, '');
        }

        // Validate quantity
        if (field.name === 'quantity') {
            const value = parseInt(field.value);
            if (value < 1) field.value = 1;
            if (value > 100) field.value = 100;
        }
    });
}

/**
 * Setup review form validation
 */
function setupReviewFormValidation(form) {
    form.addEventListener('input', function(e) {
        const field = e.target;
        
        // Validate visitor ID
        if (field.name === 'id_pengunjung') {
            field.value = field.value.replace(/[^0-9]/g, '');
        }
    });
}

/**
 * Escape HTML special characters
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Format currency to Indonesian Rupiah
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

/**
 * Format date to Indonesian format
 */
function formatDate(date) {
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(date).toLocaleDateString('id-ID', options);
}

/**
 * Show notification message
 */
function showNotification(message, type = 'success') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';

    const alertElement = document.createElement('div');
    alertElement.className = `alert ${alertClass} alert-dismissible fade show`;
    alertElement.role = 'alert';
    alertElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert at top of main content
    const mainContent = document.querySelector('.container');
    if (mainContent) {
        mainContent.insertBefore(alertElement, mainContent.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertElement.classList.remove('show');
            setTimeout(() => alertElement.remove(), 150);
        }, 5000);
    }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format (Indonesian)
 */
function isValidPhone(phone) {
    const phoneRegex = /^(\+62|0)[0-9]{9,12}$/;
    return phoneRegex.test(phone.replace(/[- ]/g, ''));
}

/**
 * Debounce function for search/filter operations
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
 * Smooth scroll to element
 */
function smoothScroll(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Get query parameter from URL
 */
function getQueryParam(param) {
    const params = new URLSearchParams(window.location.search);
    return params.get(param);
}

/**
 * API Helper Functions
 */

/**
 * Make GET request
 */
async function apiGet(endpoint) {
    try {
        const response = await fetch(endpoint);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('API GET Error:', error);
        throw error;
    }
}

/**
 * Make POST request
 */
async function apiPost(endpoint, data) {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('API POST Error:', error);
        throw error;
    }
}

/**
 * Make PUT request
 */
async function apiPut(endpoint, data) {
    try {
        const response = await fetch(endpoint, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('API PUT Error:', error);
        throw error;
    }
}

/**
 * Make DELETE request
 */
async function apiDelete(endpoint) {
    try {
        const response = await fetch(endpoint, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('API DELETE Error:', error);
        throw error;
    }
}

/**
 * Event delegation helper
 */
function on(event, selector, callback) {
    document.addEventListener(event, function(e) {
        if (e.target.matches(selector)) {
            callback.call(e.target, e);
        }
    });
}

console.log('MangroveTour Frontend loaded successfully');
