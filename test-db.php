<?php
/**
 * Database Connection Tester
 * Jalankan: php test-db.php
 * 
 * Script ini akan test koneksi ke database yang aktif di .env
 */

// Change to project root
chdir(__DIR__);

// Load environment dan database config
require_once 'backend/config/database.php';

echo "\n========================================\n";
echo "DATABASE CONNECTION TEST\n";
echo "========================================\n\n";

// Display current environment
echo "📍 Current Environment: " . strtoupper(DB_ENVIRONMENT) . "\n\n";

// Display connection info
echo "🔗 Connection Details:\n";
echo "   Host: " . DB_HOST . "\n";
echo "   Port: " . DB_PORT . "\n";
echo "   User: " . DB_USER . "\n";
echo "   Database: " . DB_NAME . "\n\n";

// Test connection
try {
    echo "🔄 Testing connection...\n";
    
    // Simple query to test connection
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    
    echo "✅ Connection successful!\n\n";
    
    // Get table count
    $stmt = $pdo->prepare("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = ?");
    $stmt->execute([DB_NAME]);
    $result = $stmt->fetch();
    
    echo "📊 Database Statistics:\n";
    echo "   Total Tables: " . $result['table_count'] . "\n\n";
    
    // List all tables
    $stmt = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? ORDER BY TABLE_NAME");
    $stmt->execute([DB_NAME]);
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "📋 Tables in database:\n";
        foreach ($tables as $table) {
            $table_name = $table['TABLE_NAME'];
            
            // Get row count
            $count_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table_name");
            $count_stmt->execute();
            $count = $count_stmt->fetch()['count'];
            
            echo "   • " . $table_name . " (" . $count . " rows)\n";
        }
    }
    
    echo "\n========================================\n";
    echo "✅ ALL TESTS PASSED\n";
    echo "========================================\n\n";
    
} catch (PDOException $e) {
    echo "❌ Connection failed!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "========================================\n";
    echo "❌ TEST FAILED\n";
    echo "========================================\n\n";
    
    echo "🔍 Troubleshooting:\n";
    echo "   1. Check .env file exists in project root\n";
    echo "   2. Verify DB_ENVIRONMENT setting\n";
    echo "   3. Verify database credentials\n";
    echo "   4. Check network/firewall for live database\n";
    echo "   5. Verify database exists\n\n";
}