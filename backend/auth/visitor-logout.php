<?php
/**
 * Visitor Logout
 * Logs out visitor and redirects to home page
 */

require_once '../config/auth_helper.php';

// Clear all session variables
$_SESSION = [];

// Destroy session cookie if using cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
if (session_status() !== PHP_SESSION_NONE) {
    session_destroy();
}

// Redirect to home page
header('Location: ../../public/index.html');
exit();
