<?php
/**
 * Midtrans SNAP Configuration
 * Academic Project - Sandbox Mode Only
 * 
 * IMPORTANT: Do NOT expose Server Key in frontend
 * Server Key should only be used in backend (this file)
 */

// ============================================================================
// SANDBOX CREDENTIALS (Academic Project)
// ============================================================================

// Midtrans Sandbox Mode
define('MIDTRANS_ENVIRONMENT', 'sandbox');
define('MIDTRANS_IS_PRODUCTION', false);

// Server Key (BACKEND ONLY - DO NOT EXPOSE TO FRONTEND)
// Get from: https://dashboard.sandbox.midtrans.com/settings/config_info
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-oNmwdYis1j-jh6gYuHt0oFl0');

// Client Key (Can be used in frontend)
// Get from: https://dashboard.sandbox.midtrans.com/settings/config_info
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-nM-SgvUqZcaYjTns');

// ============================================================================
// MERCHANT INFORMATION
// ============================================================================
define('MERCHANT_ID', 'G534484741');
define('MERCHANT_NAME', 'Mangrove Wonorejo Ecotourism');

// ============================================================================
// MIDTRANS SNAP URLS
// ============================================================================
define('MIDTRANS_SNAP_API_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
define('MIDTRANS_STATUS_API_URL', 'https://api.sandbox.midtrans.com/v2/');

// ============================================================================
// APPLICATION CALLBACK URL (Where Midtrans sends payment notifications)
// ============================================================================
// Auto-generated based on environment (local or live)
// Uses get_full_url() from database.php which auto-detects protocol, domain, and base path
if (!defined('CALLBACK_URL')) {
    // Require database.php first to ensure get_full_url() is available
    if (!function_exists('get_full_url')) {
        require_once __DIR__ . '/database.php';
    }
    define('CALLBACK_URL', get_full_url('/backend/api/midtrans_callback.php'));
}

// ============================================================================
// PAYMENT CONFIGURATION
// ============================================================================
// Default payment methods
define('PAYMENT_METHODS', [
    'bank_transfer',
    'credit_card',
    'echannel',
    'cimb_clicks',
    'bca_klik_bca',
    'bca_klik_pay',
    'bri_epay',
    'telkomsel_cashback',
    'indosat_dompetku',
    'linkaja_deeplink',
    'gopay',
    'shopeepay',
    'gcash',
    'dana',
    'akulaku',
    'qris'
]);

// ============================================================================
// HELPER FUNCTION: Initialize Midtrans
// ============================================================================
/**
 * Initialize Midtrans SNAP with server key
 * This function sets up the basic configuration for Midtrans API calls
 */
function initialize_midtrans() {
    // This would require Midtrans PHP SDK
    // For now, we'll handle HTTP requests manually
    return [
        'server_key' => MIDTRANS_SERVER_KEY,
        'client_key' => MIDTRANS_CLIENT_KEY,
        'is_production' => MIDTRANS_IS_PRODUCTION,
    ];
}

// ============================================================================
// HELPER FUNCTION: Generate Unique Order ID
// ============================================================================
/**
 * Generate unique order ID for each transaction
 * Format: ORD-{timestamp}-{random}
 * Example: ORD-1702432156-5a8b9c
 */
function generate_order_id() {
    $timestamp = time();
    $random = bin2hex(random_bytes(3)); // 6 character hex string
    return 'ORD-' . $timestamp . '-' . $random;
}

// ============================================================================
// HELPER FUNCTION: Make Midtrans API Request
// ============================================================================
/**
 * Make HTTP request to Midtrans API with proper authentication
 * 
 * @param string $method HTTP method (GET, POST, etc)
 * @param string $url Full URL to Midtrans API endpoint
 * @param array $data Request payload (for POST requests)
 * @return array|false Response from Midtrans or false on error
 */
function midtrans_api_request($method, $url, $data = null) {
    // Check if cURL is available
    if (!function_exists('curl_init')) {
        error_log("WARNING: cURL is not available on this server. Midtrans payment may not work.");
        return false;
    }
    
    // Prepare authentication header
    $server_key = MIDTRANS_SERVER_KEY;
    $auth = base64_encode($server_key . ':');
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $server_key . ':');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For sandbox only
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
    
    // Set headers
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . $auth
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Add payload for POST/PUT requests
    if ($method !== 'GET' && $data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Handle errors
    if ($error) {
        error_log("Midtrans API cURL Error: $error");
        return false;
    }
    
    // Check if response is empty
    if (empty($response)) {
        error_log("Midtrans API returned empty response (HTTP $http_code)");
        return false;
    }
    
    // Decode response
    $result = json_decode($response, true);
    
    // Log response status
    error_log("Midtrans API Response (HTTP $http_code): " . json_encode($result));
    
    // Return result if HTTP status is success
    if ($http_code >= 200 && $http_code < 300) {
        return $result;
    }
    
    // Log error response
    error_log("Midtrans API Error: HTTP $http_code - " . json_encode($result));
    return false;
}

// ============================================================================
// HELPER FUNCTION: Validate Callback Signature
// ============================================================================
/**
 * Validate Midtrans callback signature to ensure request is from Midtrans
 * 
 * @param array $payload Callback payload from Midtrans
 * @param string $signature Signature from Midtrans header
 * @return bool True if signature is valid, false otherwise
 */
function validate_midtrans_signature($payload, $signature) {
    $server_key = MIDTRANS_SERVER_KEY;
    
    // Extract order_id, status_code, gross_amount from payload
    $order_id = $payload['order_id'] ?? '';
    $status_code = $payload['status_code'] ?? '';
    $gross_amount = $payload['gross_amount'] ?? '';
    
    // Create signature string: order_id|status_code|gross_amount|server_key
    $signature_data = $order_id . '|' . $status_code . '|' . $gross_amount . '|' . $server_key;
    
    // Generate expected signature using SHA512
    $expected_signature = hash('sha512', $signature_data);
    
    // Compare signatures (constant time comparison)
    return hash_equals($expected_signature, $signature);
}

?>
