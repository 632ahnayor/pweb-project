<?php
/**
 * Midtrans Callback Handler - Payment Notification Webhook
 * Endpoint: backend/api/midtrans_callback.php
 * Method: POST
 * 
 * This endpoint:
 * 1. Receives payment notification from Midtrans
 * 2. Validates the callback signature for security
 * 3. Extracts transaction details
 * 4. Updates transaction status in database
 * 5. Updates related ticket status if applicable
 * 
 * IMPORTANT: This endpoint is called ONLY by Midtrans servers
 * Do NOT rely on frontend to update payment status
 * 
 * Response:
 * - HTTP 200: Callback processed successfully
 * - HTTP 400/401: Invalid callback or signature
 */

header('Content-Type: application/json');

// Enable error reporting (to file only)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/callback-errors.log');

// Include configuration files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

// Log incoming callback
$raw_input = file_get_contents('php://input');
error_log('Midtrans Callback Received: ' . $raw_input);

// Parse callback payload
$payload = json_decode($raw_input, true);

if (!$payload) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON payload']);
    error_log('Invalid JSON payload in callback');
    exit;
}

try {
    // Extract key information from payload
    $order_id = $payload['order_id'] ?? null;
    $status_code = $payload['status_code'] ?? null;
    $transaction_status = $payload['transaction_status'] ?? null;
    $payment_type = $payload['payment_type'] ?? null;
    $gross_amount = $payload['gross_amount'] ?? null;
    $transaction_time = $payload['transaction_time'] ?? null;
    $settlement_time = $payload['settlement_time'] ?? null;
    $fraud_status = $payload['fraud_status'] ?? null;
    $signature_key = $payload['signature_key'] ?? null;

    // Validate required fields
    if (!$order_id || !$transaction_status) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        error_log('Missing required fields in callback: order_id=' . $order_id . ', transaction_status=' . $transaction_status);
        exit;
    }

    // Validate callback signature (Security check)
    if (!validate_midtrans_signature($payload, $signature_key)) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid signature']);
        error_log('Invalid signature in callback for order_id: ' . $order_id);
        exit;
    }

    error_log("Callback validation passed for order_id: $order_id, status: $transaction_status");

    // Check if transaction exists in database
    $existing_transaction = fetch_one(
        "SELECT * FROM transactions WHERE order_id = ?",
        [$order_id]
    );

    if (!$existing_transaction) {
        http_response_code(404);
        echo json_encode(['message' => 'Transaction not found']);
        error_log('Transaction not found for order_id: ' . $order_id);
        exit;
    }

    // Update transaction in database
    $query = "
        UPDATE transactions SET 
            transaction_status = ?,
            payment_type = ?,
            transaction_time = ?,
            settlement_time = ?,
            fraud_status = ?,
            response_code = ?,
            status_message = ?,
            updated_at = NOW()
        WHERE order_id = ?
    ";

    $stmt = execute_query($query, [
        $transaction_status,
        $payment_type,
        $transaction_time ? date('Y-m-d H:i:s', strtotime($transaction_time)) : null,
        $settlement_time ? date('Y-m-d H:i:s', strtotime($settlement_time)) : null,
        $fraud_status,
        $status_code,
        json_encode($payload),
        $order_id
    ]);

    // If payment is successful, update ticket status
    if ($transaction_status === 'settlement' || $transaction_status === 'capture') {
        if ($existing_transaction['id_tiket']) {
            $ticket_query = "
                UPDATE tiket SET 
                    status = 'Active',
                    updated_at = NOW()
                WHERE id_tiket = ?
            ";
            execute_query($ticket_query, [$existing_transaction['id_tiket']]);
            error_log('Ticket activated for id_tiket: ' . $existing_transaction['id_tiket']);
        }
    }

    // If payment is expired or cancelled, mark ticket as expired
    if ($transaction_status === 'expire' || $transaction_status === 'cancel' || $transaction_status === 'deny') {
        if ($existing_transaction['id_tiket']) {
            $ticket_query = "
                UPDATE tiket SET 
                    status = 'Expired',
                    updated_at = NOW()
                WHERE id_tiket = ?
            ";
            execute_query($ticket_query, [$existing_transaction['id_tiket']]);
            error_log('Ticket expired for id_tiket: ' . $existing_transaction['id_tiket']);
        }
    }

    // Log successful update
    error_log("Transaction updated successfully - order_id: $order_id, status: $transaction_status");

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Callback processed successfully',
        'order_id' => $order_id,
        'transaction_status' => $transaction_status
    ]);

} catch (PDOException $e) {
    error_log('Database error in callback: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Database error']);
    exit;
} catch (Exception $e) {
    error_log('Error in callback: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
    exit;
}

?>
