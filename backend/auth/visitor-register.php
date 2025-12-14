<?php
/**
 * Visitor Registration API
 * Allows public visitors to create their own account
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth_helper.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $username = sanitize_input($data['username'] ?? '');
    $nama = sanitize_input($data['nama'] ?? '');
    $email = sanitize_input($data['email'] ?? '');
    $no_hp = sanitize_input($data['no_hp'] ?? '');
    $password = $data['password'] ?? '';
    $password_confirm = $data['password_confirm'] ?? '';

    if (empty($username) || empty($nama) || empty($email) || empty($no_hp) || empty($password)) {
        throw new Exception('All fields are required');
    }

    // Validate username (3-20 characters, alphanumeric + underscore)
    if (strlen($username) < 3 || strlen($username) > 20) {
        throw new Exception('Username must be 3-20 characters long');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }

    // Validate email
    if (!validate_email($email)) {
        throw new Exception('Invalid email format');
    }

    // Validate phone
    if (!validate_phone($no_hp)) {
        throw new Exception('Invalid phone number format');
    }

    // Validate password (min 6 characters)
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Check password confirmation
    if ($password !== $password_confirm) {
        throw new Exception('Passwords do not match');
    }

    // Check if username already exists
    $existing_user = fetch_one('SELECT id_pengunjung FROM pengunjung WHERE username = ?', [$username]);
    if ($existing_user) {
        throw new Exception('Username already taken');
    }

    // Check if email already exists
    $existing_email = fetch_one('SELECT id_pengunjung FROM pengunjung WHERE email = ?', [$email]);
    if ($existing_email) {
        throw new Exception('Email already registered');
    }

    // Hash password
    $password_hash = hash_password($password);

    // Create visitor account
    $id_pengunjung = insert_data('pengunjung', [
        'nama' => $nama,
        'email' => $email,
        'no_hp' => $no_hp,
        'username' => $username,
        'password' => $password_hash,
        'is_active' => 1
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'visitor_id' => $id_pengunjung,
        'username' => $username
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
