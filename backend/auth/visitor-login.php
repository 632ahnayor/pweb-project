<?php
/**
 * Visitor Login Page
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

// Redirect if already logged in
if (is_visitor_logged_in()) {
    header('Location: /pweb-project/public/booking.html');
    exit();
}

$error = '';
$redirect = $_GET['redirect'] ?? '/pweb-project/public/booking.html';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        $visitor = fetch_one('SELECT * FROM pengunjung WHERE username = ? AND is_active = 1', [$username]);

        if ($visitor && verify_password($password, $visitor['password'])) {
            // Set session
            $_SESSION['visitor_id'] = $visitor['id_pengunjung'];
            $_SESSION['visitor_nama'] = $visitor['nama'];
            $_SESSION['visitor_email'] = $visitor['email'];
            $_SESSION['visitor_username'] = $visitor['username'];

            // Update last login
            update_data('pengunjung', ['terakhir_login' => date('Y-m-d H:i:s')], ['id_pengunjung' => $visitor['id_pengunjung']]);

            header('Location: ' . ($redirect ?: '/pweb-project/public/booking.html'));
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Login - MangroveTour</title>
    <link rel="stylesheet" href="../../public/assets/css/bootstrap-5.3.8-dist/bootstrap.css">
    <link rel="stylesheet" href="../../public/assets/css/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1d4d2d 0%, #2d6a3e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            color: #2d5016;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 700;
        }
        .login-container p {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #2d5016;
            box-shadow: 0 0 0 0.2rem rgba(45, 80, 22, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #2d5016 0%, #1d4d2d 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1d4d2d 0%, #0d3d1d 100%);
            color: white;
        }
        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .register-link p {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .btn-register {
            background: #f0f0f0;
            border: 1px solid #ddd;
            color: #2d5016;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-register:hover {
            background: #e8e8e8;
            border-color: #2d5016;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2><i class="bi bi-person-circle"></i> Visitor Login</h2>
        <p>Access your booking and reviews</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <div class="register-link">
            <p>Don't have an account?</p>
            <a href="visitor-register.html" class="btn-register">
                <i class="bi bi-person-plus"></i> Create Account
            </a>
        </div>
    </div>
</body>
</html>
