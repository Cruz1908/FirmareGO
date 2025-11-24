<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::logout();
    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false]);
}

