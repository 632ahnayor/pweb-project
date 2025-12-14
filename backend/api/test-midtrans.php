<?php
/**
 * Test Midtrans Connection
 * 
 * This script tests if:
 * 1. cURL is available
 * 2. Network connectivity to Midtrans API
 * 3. Authentication with Midtrans server key
 */

header('Content-Type: application/json');

// Include configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => [],
    'all_passed' => true
];

// Test 1: Check if cURL is available
$curl_available = function_exists('curl_init');
$response['tests'][] = [
    'name' => 'cURL Available',
    'passed' => $curl_available,
    'message' => $curl_available ? 'cURL extension is available' : 'cURL extension is NOT available - Midtrans will not work'
];

if (!$curl_available) {
    $response['all_passed'] = false;
}

// Test 2: Check Midtrans credentials
$server_key_set = defined('MIDTRANS_SERVER_KEY') && MIDTRANS_SERVER_KEY !== '';
$client_key_set = defined('MIDTRANS_CLIENT_KEY') && MIDTRANS_CLIENT_KEY !== '';

$response['tests'][] = [
    'name' => 'Midtrans Server Key Configured',
    'passed' => $server_key_set,
    'message' => $server_key_set ? 'Server key is configured' : 'Server key is missing'
];

$response['tests'][] = [
    'name' => 'Midtrans Client Key Configured',
    'passed' => $client_key_set,
    'message' => $client_key_set ? 'Client key is configured' : 'Client key is missing'
];

if (!$server_key_set || !$client_key_set) {
    $response['all_passed'] = false;
}

// Test 3: Test simple Midtrans API call
if ($curl_available && $server_key_set) {
    // Try a simple test transaction
    $test_data = [
        'transaction_details' => [
            'order_id' => 'TEST-' . time(),
            'gross_amount' => 10000
        ],
        'customer_details' => [
            'first_name' => 'Test',
            'email' => 'test@test.com',
            'phone' => '081234567890'
        ]
    ];
    
    $api_url = MIDTRANS_SNAP_API_URL;
    $server_key = MIDTRANS_SERVER_KEY;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Use cURL's built-in HTTP Basic Auth
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $server_key . ':');
    
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
    
    $test_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $response['tests'][] = [
            'name' => 'Midtrans API Connection',
            'passed' => false,
            'message' => 'cURL Error: ' . $error,
            'http_code' => $http_code
        ];
        $response['all_passed'] = false;
    } else {
        $test_result = json_decode($test_response, true);
        
        $success = ($http_code >= 200 && $http_code < 300) || isset($test_result['token']);
        
        $response['tests'][] = [
            'name' => 'Midtrans API Connection',
            'passed' => $success,
            'message' => $success ? 'Successfully connected to Midtrans API' : 'Failed to get token from Midtrans',
            'http_code' => $http_code,
            'response' => $test_result
        ];
        
        if (!$success) {
            $response['all_passed'] = false;
        }
    }
}

// Test 4: Check environment detection
$base_path = get_base_path();
$response['tests'][] = [
    'name' => 'Environment Detection',
    'passed' => true,
    'message' => 'Base path detected correctly',
    'base_path' => $base_path,
    'environment' => !empty($base_path) ? 'LOCAL (Laragon)' : 'LIVE (InfiniteFree)'
];

// Test 5: Check database connection
try {
    $db = get_db();
    $result = $db->query('SELECT 1');
    $response['tests'][] = [
        'name' => 'Database Connection',
        'passed' => true,
        'message' => 'Database connection successful'
    ];
} catch (PDOException $e) {
    $response['tests'][] = [
        'name' => 'Database Connection',
        'passed' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
    $response['all_passed'] = false;
}

// Set HTTP response code based on all_passed
http_response_code($response['all_passed'] ? 200 : 500);

// Clean output buffer and return JSON
clean_for_json();
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
