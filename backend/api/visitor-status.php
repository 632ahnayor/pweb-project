<?php
/**
 * Visitor Status API
 * Check if visitor is logged in and return their info
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth_helper.php';

$response = [
    'logged_in' => false,
    'id_pengunjung' => null,
    'nama' => null,
    'email' => null,
    'no_hp' => null,
    'username' => null
];

if (is_visitor_logged_in()) {
    $visitor = fetch_one(
        'SELECT id_pengunjung, nama, email, no_hp, username FROM pengunjung WHERE id_pengunjung = ?',
        [get_visitor_id()]
    );

    if ($visitor) {
        $response['logged_in'] = true;
        $response['id_pengunjung'] = $visitor['id_pengunjung'];
        $response['nama'] = $visitor['nama'];
        $response['email'] = $visitor['email'];
        $response['no_hp'] = $visitor['no_hp'];
        $response['username'] = $visitor['username'];
    }
}

echo json_encode($response);
