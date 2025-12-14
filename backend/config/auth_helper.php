<?php
/**
 * Authentication & Authorization Helper Functions
 * MangroveTour - Mangrove Wonorejo Ecotourism Management System
 *
 * Safe to include multiple times (uses function_exists() guards).
 */

/* Start session only if not started */
if (session_status() === PHP_SESSION_NONE) {
    // Configure session timeout (1 hour = 3600 seconds)
    ini_set('session.gc_maxlifetime', 3600);
    session_set_cookie_params(['lifetime' => 3600, 'path' => '/']);
    session_start();
}

/**
 * Check if user is logged in
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }
}

/**
 * Check if user has specific role
 */
if (!function_exists('has_role')) {
    function has_role($role) {
        return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
}

/**
 * Redirect if not logged in
 */
if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            header('Location: /pweb-project/backend/auth/login.php');
            exit();
        }
    }
}

/**
 * Redirect if not admin
 */
if (!function_exists('require_admin')) {
    function require_admin() {
        require_login();
        if (!has_role('Admin')) {
            http_response_code(403);
            die('Access denied. Admin only.');
        }
    }
}

/**
 * Redirect if not operator or admin
 */
if (!function_exists('require_operator')) {
    function require_operator() {
        require_login();
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Operator'])) {
            http_response_code(403);
            die('Access denied. Operator or Admin only.');
        }
    }
}

/**
 * Hash password
 */
if (!function_exists('hash_password')) {
    function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}

/**
 * Verify password
 */
if (!function_exists('verify_password')) {
    function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }
}

/**
 * Sanitize input
 */
if (!function_exists('sanitize_input')) {
    function sanitize_input($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Validate email
 */
if (!function_exists('validate_email')) {
    function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

/**
 * Validate phone number (basic Indonesian format)
 */
if (!function_exists('validate_phone')) {
    function validate_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
}

/**
 * Get current user info
 */
if (!function_exists('get_current_user')) {
    function get_current_user() {
        return [
            'id_user' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }
}

/**
 * Logout user (clears session and cookie)
 */
if (!function_exists('logout_user')) {
    function logout_user() {
        // Unset all session variables
        $_SESSION = [];

        // If session uses cookies, remove the session cookie
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

        header('Location: /pweb-project/backend/auth/login.php');
        exit();
    }
}

/**
 * Check if visitor is logged in
 */
if (!function_exists('is_visitor_logged_in')) {
    function is_visitor_logged_in() {
        return isset($_SESSION['visitor_id']) && isset($_SESSION['visitor_nama']);
    }
}

/**
 * Redirect if visitor not logged in
 */
if (!function_exists('require_visitor_login')) {
    function require_visitor_login() {
        if (!is_visitor_logged_in()) {
            header('Location: /pweb-project/backend/auth/visitor-login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }
}

/**
 * Get current visitor ID
 */
if (!function_exists('get_visitor_id')) {
    function get_visitor_id() {
        return $_SESSION['visitor_id'] ?? null;
    }
}
