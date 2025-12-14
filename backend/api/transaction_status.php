<?php
/**
 * Get Transaction Status - Check payment status
 * Endpoint: backend/api/transaction_status.php
 * Method: GET
 * 
 * Parameters:
 * - order_id (string): Order ID to check status
 * 
 * Returns:
 * - Transaction details including current status
 */

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET request.'
    ]);
    exit;
}

// Get order_id from query parameter
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Order ID is required'
    ]);
    exit;
}

try {
    // Fetch transaction from database
    $transaction = fetch_one(
        "SELECT * FROM transactions WHERE order_id = ?",
        [$order_id]
    );

    if (!$transaction) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Transaction not found'
        ]);
        exit;
    }

    // Return transaction details
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'order_id' => $transaction['order_id'],
            'gross_amount' => $transaction['gross_amount'],
            'transaction_status' => $transaction['transaction_status'],
            'payment_type' => $transaction['payment_type'],
            'transaction_time' => $transaction['transaction_time'],
            'settlement_time' => $transaction['settlement_time'],
            'fraud_status' => $transaction['fraud_status'],
            'created_at' => $transaction['created_at'],
            'updated_at' => $transaction['updated_at']
        ]
    ]);

} catch (Exception $e) {
    error_log('Error in transaction_status.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving transaction status'
    ]);
    exit;
}

?>
