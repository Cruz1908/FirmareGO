<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = intval($data['product_id'] ?? 0);
$quantity = intval($data['quantity'] ?? 1);

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

if (Cart::add($productId, $quantity)) {
    echo json_encode([
        'success' => true,
        'count' => Cart::getCount(),
        'total' => Cart::getTotal()
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Product not found']);
}

