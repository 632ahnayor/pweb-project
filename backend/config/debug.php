<?php
/**
 * Database Configuration Debugger
 * Menampilkan informasi detail tentang konfigurasi database
 * 
 * Akses melalui browser: http://localhost/pweb-project/backend/config/debug.php
 * (hanya untuk development, matikan di production)
 */

// Security check - hanya untuk localhost/development
$allowed_ips = ['127.0.0.1', '::1', 'localhost'];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Uncomment line di bawah untuk production
// if (!in_array($client_ip, $allowed_ips)) die('Access denied');

require_once 'database.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration Debugger</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h2 {
            color: #667eea;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
            min-width: 200px;
        }
        
        .info-value {
            color: #666;
            word-break: break-all;
            font-family: 'Courier New', monospace;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
        }
        
        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .table tr:hover {
            background: #f9f9f9;
        }
        
        .status-icon {
            font-size: 20px;
        }
        
        .warning {
            background: #fff8e1;
            border-left: 4px solid #fbc02d;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .warning strong {
            color: #f57f17;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Configuration Debugger</h1>
        
        <!-- Current Configuration -->
        <div class="card">
            <h2>⚙️ Current Configuration</h2>
            <div class="info-row">
                <span class="info-label">Environment:</span>
                <span class="info-value">
                    <span class="badge <?= strtolower(DB_ENVIRONMENT) === 'local' ? 'badge-success' : 'badge-warning' ?>">
                        <?= strtoupper(DB_ENVIRONMENT) ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Host:</span>
                <span class="info-value"><?= htmlspecialchars(DB_HOST) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Port:</span>
                <span class="info-value"><?= DB_PORT ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Database:</span>
                <span class="info-value"><?= htmlspecialchars(DB_NAME) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">User:</span>
                <span class="info-value"><?= htmlspecialchars(DB_USER) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Password:</span>
                <span class="info-value"><?= DB_PASS ? '••••••••' : '(empty)' ?></span>
            </div>
        </div>
        
        <!-- Connection Status -->
        <div class="card">
            <h2>🔗 Connection Status</h2>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT 1");
                $stmt->execute();
                echo '<div class="info-row">';
                echo '<span class="info-label">Status:</span>';
                echo '<span class="info-value"><span class="badge badge-success">✅ Connected</span></span>';
                echo '</div>';
            } catch (PDOException $e) {
                echo '<div class="info-row">';
                echo '<span class="info-label">Status:</span>';
                echo '<span class="info-value"><span class="badge badge-danger">❌ Failed</span></span>';
                echo '</div>';
                echo '<div class="info-row">';
                echo '<span class="info-label">Error:</span>';
                echo '<span class="info-value">' . htmlspecialchars($e->getMessage()) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Database Tables -->
        <div class="card">
            <h2>📋 Database Tables</h2>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT TABLE_NAME, TABLE_ROWS FROM information_schema.tables WHERE table_schema = ? ORDER BY TABLE_NAME");
                $stmt->execute([DB_NAME]);
                $tables = $stmt->fetchAll();
                
                if (count($tables) > 0) {
                    echo '<table class="table">';
                    echo '<tr><th>Table Name</th><th>Rows</th></tr>';
                    foreach ($tables as $table) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($table['TABLE_NAME']) . '</td>';
                        echo '<td><strong>' . ($table['TABLE_ROWS'] ?? 'N/A') . '</strong></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p style="color: #999;">No tables found in database</p>';
                }
            } catch (Exception $e) {
                echo '<p style="color: #d32f2f;">Error fetching tables: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <!-- .env File Status -->
        <div class="card">
            <h2>📄 .env File Status</h2>
            <?php
            $env_file = realpath(__DIR__ . '/../../.env');
            $env_example = realpath(__DIR__ . '/../../.env.example');
            
            echo '<div class="info-row">';
            echo '<span class="info-label">.env File:</span>';
            if (file_exists($env_file)) {
                echo '<span class="info-value"><span class="badge badge-success">✅ Found</span> - ' . $env_file . '</span>';
            } else {
                echo '<span class="info-value"><span class="badge badge-danger">❌ Not Found</span></span>';
            }
            echo '</div>';
            
            echo '<div class="info-row">';
            echo '<span class="info-label">.env.example File:</span>';
            if (file_exists($env_example)) {
                echo '<span class="info-value"><span class="badge badge-success">✅ Found</span> - ' . $env_example . '</span>';
            } else {
                echo '<span class="info-value"><span class="badge badge-warning">⚠️ Not Found</span></span>';
            }
            echo '</div>';
            ?>
        </div>
        
        <!-- Security Warning -->
        <div class="card">
            <div class="warning">
                <strong>⚠️ SECURITY WARNING:</strong><br>
                This debug page contains sensitive information. Make sure to:<br>
                ✓ Disable in production environment<br>
                ✓ Add authentication/IP whitelist<br>
                ✓ Remove from public access
            </div>
        </div>
        
        <!-- Environment Variables -->
        <div class="card">
            <h2>🌍 Environment Variables</h2>
            <?php
            $env_vars = [
                'DB_ENVIRONMENT',
                'LOCAL_DB_HOST',
                'LOCAL_DB_USER',
                'LOCAL_DB_NAME',
                'LIVE_DB_HOST',
                'LIVE_DB_USER',
                'LIVE_DB_NAME'
            ];
            
            echo '<table class="table">';
            echo '<tr><th>Variable</th><th>Value</th></tr>';
            foreach ($env_vars as $var) {
                $value = $_ENV[$var] ?? '(not set)';
                echo '<tr>';
                echo '<td><strong>' . $var . '</strong></td>';
                echo '<td>' . htmlspecialchars($value) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
</body>
</html>
