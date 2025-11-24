<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

echo json_encode([
    'count' => Cart::getCount(),
    'total' => Cart::getTotal()
]);

