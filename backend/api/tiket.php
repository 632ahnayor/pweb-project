<?php
/**
 * Tiket API Endpoints
 * MangroveTour - Ticket Management API
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth_helper.php';

// Only require operator/admin for GET/UPDATE/DELETE
// POST create is public to allow visitor ticket bookings
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if ($method === 'GET' || $method === 'PUT' || $method === 'DELETE') {
    require_operator();
}

try {
    if ($method === 'GET') {
        if ($action === 'list') {
            $tiket = fetch_all('
                SELECT t.*, p.nama, p.email, p.no_hp 
                FROM tiket t 
                JOIN pengunjung p ON t.id_pengunjung = p.id_pengunjung 
                ORDER BY t.created_at DESC
            ');
            clean_for_json();
            echo json_encode(['success' => true, 'data' => $tiket]);
        } else if ($action === 'view' && isset($_GET['id'])) {
            $tiket = fetch_one('
                SELECT t.*, p.nama, p.email, p.no_hp 
                FROM tiket t 
                JOIN pengunjung p ON t.id_pengunjung = p.id_pengunjung 
                WHERE t.id_tiket = ?
            ', [$_GET['id']]);
            if ($tiket) {
                clean_for_json();
                echo json_encode(['success' => true, 'data' => $tiket]);
            } else {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            }
        }
    } else if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'create') {
            $id_pengunjung = $data['id_pengunjung'] ?? null;
            $tanggal_berkunjung = $data['tanggal_berkunjung'] ?? null;
            $harga = $data['harga'] ?? 50000;

            if (empty($id_pengunjung) || empty($tanggal_berkunjung)) {
                throw new Exception('Visitor ID and visit date are required');
            }

            $pengunjung = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$id_pengunjung]);
            if (!$pengunjung) {
                throw new Exception('Visitor not found');
            }

            $id = insert_data('tiket', [
                'id_pengunjung' => $id_pengunjung,
                'tanggal_berkunjung' => $tanggal_berkunjung,
                'harga' => $harga,
                'status' => 'Active'
            ]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Ticket created successfully', 'id' => $id]);
        }
    } else if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'update' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $tiket = fetch_one('SELECT * FROM tiket WHERE id_tiket = ?', [$id]);

            if (!$tiket) {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Ticket not found']);
                exit();
            }

            $update_data = [];
            if (!empty($data['status'])) $update_data['status'] = $data['status'];
            if (!empty($data['tanggal_berkunjung'])) $update_data['tanggal_berkunjung'] = $data['tanggal_berkunjung'];

            if (empty($update_data)) {
                throw new Exception('No data to update');
            }

            update_data('tiket', $update_data, ['id_tiket' => $id]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Ticket updated successfully']);
        }
    } else if ($method === 'DELETE') {
        if ($action === 'delete' && isset($_GET['id'])) {
            require_admin(); // Only admin can delete tickets
            
            $tiket = fetch_one('SELECT * FROM tiket WHERE id_tiket = ?', [$_GET['id']]);

            if (!$tiket) {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Ticket not found']);
                exit();
            }

            delete_data('tiket', ['id_tiket' => $_GET['id']]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully']);
        }
    } else {
        http_response_code(405);
        clean_for_json();
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    clean_for_json();
