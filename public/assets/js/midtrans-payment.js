/**
 * Midtrans SNAP Payment Integration
 * Frontend Payment Handler
 * 
 * This script:
 * 1. Communicates with backend to create transactions
 * 2. Initializes Midtrans SNAP payment gateway
 * 3. Handles payment callbacks
 * 
 * NOTE: Requires base path detection (getBasePath/apiUrl) to be loaded first
 */

class MidtransPaymentHandler {
    constructor(clientKey) {
        this.clientKey = clientKey;
        this.initializeSnap();
    }

    /**
     * Initialize Midtrans SNAP environment
     */
    initializeSnap() {
        // SNAP JS library is loaded from CDN in HTML
        // This function ensures SNAP is ready
        if (typeof snap === 'undefined') {
            console.error('Midtrans SNAP library not loaded. Check script tag in HTML.');
        }
    }

    /**
     * Create transaction and get payment token from backend
     * @param {Object} paymentData - Payment details
     * @returns {Promise} Response from backend
     */
    async createTransaction(paymentData) {
        try {
            const response = await fetch(apiUrl('/backend/api/create_transaction.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: paymentData.amount,
                    customer_name: paymentData.customer_name,
                    customer_email: paymentData.customer_email,
                    customer_phone: paymentData.customer_phone,
                    id_tiket: paymentData.id_tiket || null
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to create transaction');
            }

            return result;

        } catch (error) {
            console.error('Error creating transaction:', error);
            throw error;
        }
    }

    /**
     * Process payment through Midtrans SNAP
     * @param {String} token - Snap token from backend
     * @returns {Promise} Payment result
     */
    processPayment(token) {
        return new Promise((resolve, reject) => {
            snap.pay(token, {
                onSuccess: (result) => {
                    console.log('Payment successful:', result);
                    this.handlePaymentSuccess(result);
                    resolve(result);
                },
                onPending: (result) => {
                    console.log('Payment pending:', result);
                    this.handlePaymentPending(result);
                    resolve(result);
                },
                onError: (result) => {
                    console.error('Payment error:', result);
                    this.handlePaymentError(result);
                    reject(result);
                },
                onClose: () => {
                    console.log('Payment popup closed');
                }
            });
        });
    }

    /**
     * Handle successful payment
     */
    handlePaymentSuccess(result) {
        showNotification('success', '✓ Pembayaran Berhasil!', 'Terima kasih atas pembayaran Anda. Tiket Anda siap digunakan.');
        console.log('Payment details:', result);
        
        // Store order ID in session/localStorage
        if (result.order_id) {
            sessionStorage.setItem('last_order_id', result.order_id);
        }
    }

    /**
     * Handle pending payment
     */
    handlePaymentPending(result) {
        showNotification('info', 'Pembayaran Tertunda', 'Pembayaran Anda sedang diproses. Silakan cek email untuk detail lebih lanjut.');
        console.log('Pending payment:', result);
    }

    /**
     * Handle payment error
     */
    handlePaymentError(result) {
        showNotification('error', '✗ Pembayaran Gagal', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        console.error('Payment error details:', result);
    }

    /**
     * Check transaction status
     * @param {String} orderId - Order ID to check
     */
    async checkTransactionStatus(orderId) {
        try {
            const response = await fetch(apiUrl(`/backend/api/transaction_status.php?order_id=${orderId}`));
            const result = await response.json();
            return result.data || null;
        } catch (error) {
            console.error('Error checking transaction status:', error);
            return null;
        }
    }
}

/**
 * Show notification to user
 * @param {String} type - Notification type (success, error, info, warning)
 * @param {String} title - Notification title
 * @param {String} message - Notification message
 */
function showNotification(type, title, message) {
    const notificationId = 'notification-' + Date.now();
    
    // Create notification container if doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        `;
        document.body.appendChild(container);
    }

    const container = document.getElementById('notification-container');
    
    // Color scheme for notification types
    const colorScheme = {
        success: { bg: '#d4edda', border: '#c3e6cb', text: '#155724', icon: '✓' },
        error: { bg: '#f8d7da', border: '#f5c6cb', text: '#721c24', icon: '✕' },
        info: { bg: '#d1ecf1', border: '#bee5eb', text: '#0c5460', icon: 'ⓘ' },
        warning: { bg: '#fff3cd', border: '#ffeeba', text: '#856404', icon: '⚠' }
    };

    const colors = colorScheme[type] || colorScheme.info;

    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.style.cssText = `
        background-color: ${colors.bg};
        border: 1px solid ${colors.border};
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
    `;

    notification.innerHTML = `
        <div style="color: ${colors.text}; font-weight: 600; margin-bottom: 5px;">
            <span style="margin-right: 8px;">${colors.icon}</span>${title}
        </div>
        <div style="color: ${colors.text}; font-size: 14px;">${message}</div>
    `;

    container.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

/**
 * Format currency to IDR
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Add CSS animations for notifications
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
