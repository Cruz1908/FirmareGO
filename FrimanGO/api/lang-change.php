<?php
/**
 * API para cambiar el idioma de la aplicación
 */
header('Content-Type: application/json');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lang = $_POST['lang'] ?? $_GET['lang'] ?? null;
    
    if ($lang && in_array($lang, ['es', 'ca'])) {
        $_SESSION['lang'] = $lang;
        echo json_encode([
            'success' => true,
            'lang' => $lang,
            'message' => 'Idioma cambiado correctamente'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Idioma no válido. Debe ser "es" o "ca"'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

