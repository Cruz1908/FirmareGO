<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$cart = Cart::get();
if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Carrito vacío']);
    exit;
}

try {
    $total = Cart::getTotal();
    
    $metadata = [
        'user_id' => $_SESSION['user_id'],
        'cart_count' => count($cart)
    ];
    
    $paymentIntent = Payment::createPaymentIntent($total, 'eur', $metadata);
    
    echo json_encode([
        'success' => true,
        'client_secret' => $paymentIntent['client_secret'],
        'payment_intent_id' => $paymentIntent['id']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
