<?php
/**
 * Router simple para servidor PHP incorporado
 * Maneja las rutas cuando no hay .htaccess disponible
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si es un archivo estático, servirlo directamente
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|pdf)$/', $uri)) {
    return false; // Dejar que el servidor lo sirva
}

// Si el archivo existe físicamente, servirlo
$filePath = __DIR__ . $uri;
if (file_exists($filePath) && is_file($filePath) && $uri !== '/') {
    return false;
}

// Para todo lo demás, usar index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';

