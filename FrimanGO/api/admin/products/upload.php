<?php
/**
 * Upload de imágenes de productos
 */
require_once __DIR__ . '/../../../../config/config.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Verificar que hay un archivo
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún archivo']);
    exit;
}

$file = $_FILES['image'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validar tipo de archivo
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido. Solo JPG, PNG, WEBP o GIF']);
    exit;
}

// Validar tamaño
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 5MB']);
    exit;
}

// Crear directorio si no existe
$uploadDir = __DIR__ . '/../../../../uploads/products/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al crear directorio de uploads']);
        exit;
    }
}

// Generar nombre único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('product_', true) . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Mover archivo
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el archivo']);
    exit;
}

// Retornar URL de la imagen
$imageUrl = APP_URL . '/uploads/products/' . $filename;

echo json_encode([
    'success' => true,
    'url' => $imageUrl,
    'filename' => $filename
]);

