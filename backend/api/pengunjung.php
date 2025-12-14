<?php
/**
 * Pengunjung API Endpoints
 * MangroveTour - Visitor Management API
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

// Only require admin for GET/DELETE/UPDATE operations
// POST create is public to allow public ticket bookings
if ($method === 'GET' || $method === 'DELETE' || ($method === 'PUT')) {
    require_admin();
}

try {
    if ($method === 'GET') {
        if ($action === 'list') {
            $pengunjung = fetch_all('SELECT * FROM pengunjung ORDER BY created_at DESC');
            clean_for_json();
            echo json_encode(['success' => true, 'data' => $pengunjung]);
        } else if ($action === 'view' && isset($_GET['id'])) {
            $pengunjung = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$_GET['id']]);
            if ($pengunjung) {
                clean_for_json();
                echo json_encode(['success' => true, 'data' => $pengunjung]);
            } else {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Pengunjung not found']);
            }
        }
    } else if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'create') {
            $nama = sanitize_input($data['nama'] ?? '');
            $no_hp = sanitize_input($data['no_hp'] ?? '');
            $email = sanitize_input($data['email'] ?? '');

            if (empty($nama) || empty($no_hp) || empty($email)) {
                throw new Exception('All fields are required');
            }

            if (!validate_email($email)) {
                throw new Exception('Invalid email format');
            }

            if (!validate_phone($no_hp)) {
                throw new Exception('Invalid phone number');
            }

            $id = insert_data('pengunjung', ['nama' => $nama, 'no_hp' => $no_hp, 'email' => $email]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Visitor created successfully', 'id' => $id]);
        }
    } else if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'update' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $pengunjung = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$id]);

            if (!$pengunjung) {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Pengunjung not found']);
                exit();
            }

            $update_data = [];
            if (!empty($data['nama'])) $update_data['nama'] = sanitize_input($data['nama']);
            if (!empty($data['no_hp'])) $update_data['no_hp'] = sanitize_input($data['no_hp']);
            if (!empty($data['email'])) $update_data['email'] = sanitize_input($data['email']);

            if (empty($update_data)) {
                throw new Exception('No data to update');
            }

            update_data('pengunjung', $update_data, ['id_pengunjung' => $id]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Visitor updated successfully']);
        }
    } else if ($method === 'DELETE') {
        if ($action === 'delete' && isset($_GET['id'])) {
            $pengunjung = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$_GET['id']]);

            if (!$pengunjung) {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Pengunjung not found']);
                exit();
            }

            delete_data('pengunjung', ['id_pengunjung' => $_GET['id']]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Visitor deleted successfully']);
        }
    } else {
        http_response_code(405);
        clean_for_json();
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    clean_for_json();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
