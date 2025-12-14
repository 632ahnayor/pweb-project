<?php
/**
 * Database Configuration
 * MangroveTour - Mangrove Wonorejo Ecotourism Management System
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mangrove_wonorejo');
define('DB_PORT', 3306);

// Create PDO connection
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

/**
 * Helper function to execute prepared statements
 */
function execute_query($query, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Helper function to fetch single row
 */
function fetch_one($query, $params = []) {
    $stmt = execute_query($query, $params);
    return $stmt->fetch();
}

/**
 * Helper function to fetch all rows
 */
function fetch_all($query, $params = []) {
    $stmt = execute_query($query, $params);
    return $stmt->fetchAll();
}

/**
 * Helper function to insert data
 */
function insert_data($table, $data) {
    global $pdo;
    $columns = implode(',', array_keys($data));
    $placeholders = implode(',', array_fill(0, count($data), '?'));
    $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array_values($data));
    return $pdo->lastInsertId();
}

/**
 * Helper function to update data
 */
function update_data($table, $data, $where = []) {
    global $pdo;
    $set_clause = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
    $where_clause = implode(' AND ', array_map(fn($k) => "$k=?", array_keys($where)));
    $query = "UPDATE $table SET $set_clause WHERE $where_clause";
    $values = array_merge(array_values($data), array_values($where));
    $stmt = $pdo->prepare($query);
    return $stmt->execute($values);
}

/**
 * Helper function to delete data
 */
function delete_data($table, $where = []) {
    global $pdo;
    $where_clause = implode(' AND ', array_map(fn($k) => "$k=?", array_keys($where)));
    $query = "DELETE FROM $table WHERE $where_clause";
    $stmt = $pdo->prepare($query);
    return $stmt->execute(array_values($where));
}
