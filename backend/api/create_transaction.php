<?php
/**
 * Create Transaction - Midtrans SNAP Integration
 * Endpoint: backend/api/create_transaction.php
 * Method: POST
 * 
 * This endpoint:
 * 1. Receives payment request from frontend
 * 2. Generates unique order ID
 * 3. Calls Midtrans SNAP API to get transaction token
 * 4. Returns token to frontend for payment gateway
 * 
 * Request Parameters:
 * - amount (int): Payment amount in IDR
 * - customer_name (string): Customer name
 * - customer_email (string): Customer email
 * - customer_phone (string): Customer phone number
 * - id_tiket (int, optional): Ticket ID if applicable
 */

header('Content-Type: application/json');

// Clean output buffer to prevent BOM/whitespace issues
if (ob_get_level() === 0) {
    ob_start();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    clean_for_json();
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST request.'
    ]);
    exit;
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$amount = $input['amount'] ?? null;
$customer_name = $input['customer_name'] ?? null;
$customer_email = $input['customer_email'] ?? null;
$customer_phone = $input['customer_phone'] ?? null;
$id_tiket = $input['id_tiket'] ?? null;

// Validate required fields
$errors = [];

if (!$amount || $amount <= 0) {
    $errors[] = 'Amount must be a positive number';
}

if (!$customer_name || strlen($customer_name) < 3) {
    $errors[] = 'Customer name is required (minimum 3 characters)';
}

if (!$customer_email || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid customer email is required';
}

if (!$customer_phone || strlen($customer_phone) < 10) {
    $errors[] = 'Valid customer phone number is required (minimum 10 digits)';
}

if (!empty($errors)) {
    http_response_code(400);
    clean_for_json();
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Generate unique order ID
    $order_id = generate_order_id();
    
    // Log for debugging
    error_log("Creating transaction: Order ID = $order_id, Amount = $amount");
    
    // Prepare transaction data for Midtrans
    $transaction_data = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int)$amount
        ],
        'customer_details' => [
            'first_name' => $customer_name,
            'email' => $customer_email,
            'phone' => $customer_phone
        ],
        'callbacks' => [
            'finish' => get_full_url('/public/index.html'),
            'unfinish' => get_full_url('/public/index.html'),
            'error' => get_full_url('/public/index.html')
        ]
    ];
    
    // Log transaction data for debugging
    error_log("Midtrans transaction data: " . json_encode($transaction_data));
    
    // Call Midtrans API to get Snap Token
    error_log("Calling Midtrans API at: " . MIDTRANS_SNAP_API_URL);
    $snap_response = midtrans_api_request(
        'POST',
        MIDTRANS_SNAP_API_URL,
        $transaction_data
    );
    
    // Log Midtrans response
    error_log("Midtrans response: " . json_encode($snap_response));
    
    if (!$snap_response || !isset($snap_response['token'])) {
        $error_msg = 'Failed to generate payment token from Midtrans';
        if (isset($snap_response['error_id'])) {
            $error_msg .= ' (' . $snap_response['error_id'] . ': ' . ($snap_response['error_message'] ?? 'Unknown error') . ')';
        }
        throw new Exception($error_msg);
    }
    
    $snap_token = $snap_response['token'];
    
    // Store transaction in database
    $query = "
        INSERT INTO transactions 
        (id_tiket, order_id, gross_amount, transaction_status, created_at, updated_at) 
        VALUES 
        (?, ?, ?, 'pending', NOW(), NOW())
    ";
    
    $stmt = execute_query($query, [
        $id_tiket ?? null,
        $order_id,
        (int)$amount
    ]);
    
    // Return success response with token
    http_response_code(200);
    clean_for_json();
    echo json_encode([
        'success' => true,
        'message' => 'Transaction created successfully',
        'token' => $snap_token,
        'redirect_url' => $snap_response['redirect_url'] ?? null,
        'order_id' => $order_id
    ]);
    
} catch (Exception $e) {
    error_log('Error in create_transaction.php: ' . $e->getMessage());
    http_response_code(500);
    clean_for_json();
    echo json_encode([
        'success' => false,
        'message' => 'Error creating transaction: ' . $e->getMessage()
    ]);
    exit;
}

?>
