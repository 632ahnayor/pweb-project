<?php
/**
 * Interactive Database Configuration Tester
 * Script untuk test dan manage konfigurasi database
 * 
 * Usage:
 *   php backend/config/test-config.php check      # Check current config
 *   php backend/config/test-config.php local      # Test local connection
 *   php backend/config/test-config.php live       # Test live connection
 *   php backend/config/test-config.php all        # Test both connections
 *   php backend/config/test-config.php tables     # List all tables
 */

define('ROOT_PATH', realpath(__DIR__ . '/../../'));

// Load .env
function load_env() {
    $env_file = ROOT_PATH . '/.env';
    $env_data = [];
    
    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $env_data[trim($key)] = trim($value);
            }
        }
    }
    
    return $env_data;
}

function test_connection($env, $type) {
    $host = $env[$type === 'local' ? 'LOCAL_DB_HOST' : 'LIVE_DB_HOST'];
    $user = $env[$type === 'local' ? 'LOCAL_DB_USER' : 'LIVE_DB_USER'];
    $pass = $env[$type === 'local' ? 'LOCAL_DB_PASS' : 'LIVE_DB_PASS'];
    $name = $env[$type === 'local' ? 'LOCAL_DB_NAME' : 'LIVE_DB_NAME'];
    $port = $env[$type === 'local' ? 'LOCAL_DB_PORT' : 'LIVE_DB_PORT'] ?? 3306;
    
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$name",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function print_header($title) {
    echo "\n";
    echo str_repeat("=", 50) . "\n";
    echo $title . "\n";
    echo str_repeat("=", 50) . "\n";
}

function print_section($title) {
    echo "\n" . str_repeat("-", 50) . "\n";
    echo $title . "\n";
    echo str_repeat("-", 50) . "\n";
}

// Main
$env = load_env();
$command = $argv[1] ?? 'check';

print_header("🔧 DATABASE CONFIGURATION TESTER");

if (!count($env)) {
    echo "❌ Error: .env file not found or empty at " . ROOT_PATH . "\n";
    exit(1);
}

echo "📍 Environment: " . ($env['DB_ENVIRONMENT'] ?? 'not set') . "\n";
echo "📁 Project Root: " . ROOT_PATH . "\n";

switch ($command) {
    case 'check':
        print_section("Current Configuration");
        echo "DB_ENVIRONMENT: " . ($env['DB_ENVIRONMENT'] ?? 'not set') . "\n\n";
        
        echo "LOCAL Configuration:\n";
        echo "  Host: " . ($env['LOCAL_DB_HOST'] ?? 'not set') . "\n";
        echo "  User: " . ($env['LOCAL_DB_USER'] ?? 'not set') . "\n";
        echo "  Pass: " . (isset($env['LOCAL_DB_PASS']) && $env['LOCAL_DB_PASS'] ? '***' : '(empty)') . "\n";
        echo "  Database: " . ($env['LOCAL_DB_NAME'] ?? 'not set') . "\n";
        echo "  Port: " . ($env['LOCAL_DB_PORT'] ?? '3306') . "\n";
        
        echo "\nLIVE Configuration:\n";
        echo "  Host: " . ($env['LIVE_DB_HOST'] ?? 'not set') . "\n";
        echo "  User: " . ($env['LIVE_DB_USER'] ?? 'not set') . "\n";
        echo "  Pass: " . (isset($env['LIVE_DB_PASS']) && $env['LIVE_DB_PASS'] ? '***' : '(empty)') . "\n";
        echo "  Database: " . ($env['LIVE_DB_NAME'] ?? 'not set') . "\n";
        echo "  Port: " . ($env['LIVE_DB_PORT'] ?? '3306') . "\n";
        break;
        
    case 'local':
        print_section("Testing LOCAL Connection");
        $result = test_connection($env, 'local');
        if ($result['success']) {
            echo "✅ Connection Successful!\n";
            $pdo = $result['pdo'];
            $stmt = $pdo->prepare("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = ?");
            $stmt->execute([$env['LOCAL_DB_NAME']]);
            $count = $stmt->fetch()['table_count'];
            echo "📊 Tables: $count\n";
        } else {
            echo "❌ Connection Failed!\n";
            echo "Error: " . $result['error'] . "\n";
        }
        break;
        
    case 'live':
        print_section("Testing LIVE Connection");
        $result = test_connection($env, 'live');
        if ($result['success']) {
            echo "✅ Connection Successful!\n";
            $pdo = $result['pdo'];
            $stmt = $pdo->prepare("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = ?");
            $stmt->execute([$env['LIVE_DB_NAME']]);
            $count = $stmt->fetch()['table_count'];
            echo "📊 Tables: $count\n";
        } else {
            echo "❌ Connection Failed!\n";
            echo "Error: " . $result['error'] . "\n";
        }
        break;
        
    case 'all':
        print_section("Testing LOCAL Connection");
        $result_local = test_connection($env, 'local');
        if ($result_local['success']) {
            echo "✅ LOCAL: Connected\n";
        } else {
            echo "❌ LOCAL: Failed - " . $result_local['error'] . "\n";
        }
        
        print_section("Testing LIVE Connection");
        $result_live = test_connection($env, 'live');
        if ($result_live['success']) {
            echo "✅ LIVE: Connected\n";
        } else {
            echo "❌ LIVE: Failed - " . $result_live['error'] . "\n";
        }
        break;
        
    case 'tables':
        $current_env = $env['DB_ENVIRONMENT'] ?? 'local';
        print_section("Tables in " . strtoupper($current_env));
        
        $result = test_connection($env, $current_env);
        if ($result['success']) {
            $pdo = $result['pdo'];
            $db_name = $current_env === 'local' ? $env['LOCAL_DB_NAME'] : $env['LIVE_DB_NAME'];
            
            $stmt = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? ORDER BY TABLE_NAME");
            $stmt->execute([$db_name]);
            $tables = $stmt->fetchAll();
            
            if (count($tables)) {
                foreach ($tables as $table) {
                    $table_name = $table['TABLE_NAME'];
                    $count_stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM $table_name");
                    $count_stmt->execute();
                    $cnt = $count_stmt->fetch()['cnt'];
                    echo "  📋 $table_name ($cnt rows)\n";
                }
            } else {
                echo "No tables found\n";
            }
        } else {
            echo "❌ Connection Failed: " . $result['error'] . "\n";
        }
        break;
        
    default:
        print_section("Available Commands");
        echo "  php backend/config/test-config.php check      - Show current config\n";
        echo "  php backend/config/test-config.php local      - Test local connection\n";
        echo "  php backend/config/test-config.php live       - Test live connection\n";
        echo "  php backend/config/test-config.php all        - Test both connections\n";
        echo "  php backend/config/test-config.php tables     - List tables in active DB\n";
}

print_header("✅ Done");
