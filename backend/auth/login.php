<?php
/**
 * Login Page
 * MangroveTour Admin/Operator Login
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: /pweb-project/backend/views/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        $user = fetch_one('SELECT * FROM `user` WHERE username = ?', [$username]);

        if ($user && verify_password($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: /pweb-project/backend/views/dashboard.php');
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
    <title>Login - MangroveTour Admin</title>
    <link rel="stylesheet" href="../../public/assets/css/bootstrap-5.3.8-dist/bootstrap.css">
    <style>
        :root {
            --primary-green: #2d5016;
            --secondary-green: #3a7d4e;
            --accent-green: #5a9f6e;
            --light-green: #a8d5a8;
        }
        
        body {
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--accent-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            color: var(--primary-green);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 0.2rem rgba(90, 159, 110, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--accent-green) 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--accent-green) 0%, var(--secondary-green) 100%);
            color: white;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🌱 MangroveTour</h2>
        <h5 style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;">Admin Login</h5>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #666; font-size: 12px;">
            Demo Credentials:<br>
            <strong>Admin:</strong> admin / admin123<br>
            <strong>Operator:</strong> operator / operator123
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
