<?php
/**
 * Review API Endpoints
 * MangroveTour - Review Management API
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    if ($method === 'GET') {
        if ($action === 'list') {
            require_admin();
            $reviews = fetch_all('
                SELECT r.*, p.nama, p.email 
                FROM review r 
                JOIN pengunjung p ON r.id_pengunjung = p.id_pengunjung 
                ORDER BY r.created_at DESC
            ');
            clean_for_json();
            echo json_encode(['success' => true, 'data' => $reviews]);
        } else if ($action === 'public') {
            // Public reviews for landing page
            $reviews = fetch_all('
                SELECT r.*, p.nama 
                FROM review r 
                JOIN pengunjung p ON r.id_pengunjung = p.id_pengunjung 
                ORDER BY r.created_at DESC
                LIMIT 10
            ');
            clean_for_json();
            echo json_encode(['success' => true, 'data' => $reviews]);
        } else if ($action === 'view' && isset($_GET['id'])) {
            require_admin();
            $review = fetch_one('
                SELECT r.*, p.nama, p.email 
                FROM review r 
                JOIN pengunjung p ON r.id_pengunjung = p.id_pengunjung 
                WHERE r.id_review = ?
            ', [$_GET['id']]);
            if ($review) {
                clean_for_json();
                echo json_encode(['success' => true, 'data' => $review]);
            } else {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Review not found']);
            }
        }
    } else if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'create') {
            // Anyone can submit a review
            $id_pengunjung = $data['id_pengunjung'] ?? null;
            $rating = $data['rating'] ?? null;
            $komentar = $data['komentar'] ?? '';

            if (empty($id_pengunjung) || empty($rating)) {
                throw new Exception('Visitor ID and rating are required');
            }

            if ($rating < 1 || $rating > 5) {
                throw new Exception('Rating must be between 1 and 5');
            }

            $pengunjung = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$id_pengunjung]);
            if (!$pengunjung) {
                throw new Exception('Visitor not found');
            }

            $id = insert_data('review', [
                'id_pengunjung' => $id_pengunjung,
                'rating' => $rating,
                'komentar' => sanitize_input($komentar)
            ]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully', 'id' => $id]);
        }
    } else if ($method === 'DELETE') {
        if ($action === 'delete' && isset($_GET['id'])) {
            require_admin();
            
            $review = fetch_one('SELECT * FROM review WHERE id_review = ?', [$_GET['id']]);

            if (!$review) {
                http_response_code(404);
                clean_for_json();
                echo json_encode(['success' => false, 'message' => 'Review not found']);
                exit();
            }

            delete_data('review', ['id_review' => $_GET['id']]);
            clean_for_json();
            echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
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
