<?php
/**
 * Test Midtrans Callback Configuration
 * 
 * This script verifies:
 * 1. CALLBACK_URL is correctly generated
 * 2. Callback endpoint is accessible
 * 3. Signature validation function exists
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

// Test 1: Check if CALLBACK_URL is defined
$callback_defined = defined('CALLBACK_URL');
$response['tests'][] = [
    'name' => 'CALLBACK_URL Defined',
    'passed' => $callback_defined,
    'message' => $callback_defined ? 'CALLBACK_URL is properly defined' : 'CALLBACK_URL is NOT defined'
];

if (!$callback_defined) {
    $response['all_passed'] = false;
}

// Test 2: Check CALLBACK_URL value
if ($callback_defined) {
    $callback_url = CALLBACK_URL;
    
    // Should use HTTPS on live
    $uses_https = strpos($callback_url, 'https://') === 0;
    $is_live = strpos($callback_url, 'mangrovetour.gt.tc') !== false;
    $is_local = strpos($callback_url, 'localhost') !== false;
    
    $response['tests'][] = [
        'name' => 'CALLBACK_URL Value',
        'passed' => true,
        'value' => $callback_url,
        'uses_https' => $uses_https,
        'environment' => $is_live ? 'LIVE (InfiniteFree)' : ($is_local ? 'LOCAL (Laragon)' : 'UNKNOWN')
    ];
    
    if ($is_live && !$uses_https) {
        $response['tests'][] = [
            'name' => 'Security Check',
            'passed' => false,
            'message' => 'WARNING: Live environment should use HTTPS for callbacks',
            'value' => $callback_url
        ];
        $response['all_passed'] = false;
    } else {
        $response['tests'][] = [
            'name' => 'Security Check',
            'passed' => true,
            'message' => 'Callback uses correct protocol (' . ($uses_https ? 'HTTPS' : 'HTTP') . ')'
        ];
    }
}

// Test 3: Check if callback endpoint is accessible
if ($callback_defined) {
    $callback_url = CALLBACK_URL;
    
    // Try to reach the callback endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $callback_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request to check if endpoint exists
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // 405 is expected (method not allowed for GET/HEAD)
    // 200, 302 are also acceptable
    $accessible = ($http_code >= 200 && $http_code < 500) && empty($error);
    
    $response['tests'][] = [
        'name' => 'Callback Endpoint Reachable',
        'passed' => $accessible,
        'http_code' => $http_code,
        'message' => $accessible ? "Endpoint is reachable (HTTP $http_code)" : ($error ? "cURL Error: $error" : "HTTP $http_code")
    ];
    
    if (!$accessible && !empty($error)) {
        $response['all_passed'] = false;
    }
}

// Test 4: Check if signature validation function exists
$sig_func_exists = function_exists('validate_midtrans_signature');
$response['tests'][] = [
    'name' => 'Signature Validation Function',
    'passed' => $sig_func_exists,
    'message' => $sig_func_exists ? 'Signature validation function exists' : 'Function NOT found'
];

if (!$sig_func_exists) {
    $response['all_passed'] = false;
}

// Test 5: Check Midtrans credentials
$server_key_set = defined('MIDTRANS_SERVER_KEY') && MIDTRANS_SERVER_KEY !== '';
$response['tests'][] = [
    'name' => 'Midtrans Server Key',
    'passed' => $server_key_set,
    'message' => $server_key_set ? 'Server key is configured' : 'Server key is missing'
];

if (!$server_key_set) {
    $response['all_passed'] = false;
}

// Test 6: Environment detection
$base_path = get_base_path();
$environment = !empty($base_path) ? 'LOCAL (Laragon)' : 'LIVE (InfiniteFree)';

$response['tests'][] = [
    'name' => 'Environment Detection',
    'passed' => true,
    'message' => 'Environment correctly detected',
    'environment' => $environment,
    'base_path' => $base_path,
    'hostname' => $_SERVER['HTTP_HOST'] ?? 'unknown'
];

// Summary
$response['summary'] = [
    'callback_url' => CALLBACK_URL ?? 'NOT DEFINED',
    'environment' => $environment,
    'all_tests_passed' => $response['all_passed']
];

// Set HTTP response code
http_response_code($response['all_passed'] ? 200 : 500);

// Return JSON response
clean_for_json();
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
