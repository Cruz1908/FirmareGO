<?php
declare(strict_types=1);

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Eliminar el base path si existe
// Por ejemplo, si está en /FrimanGO/login, convertir a /login
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace(basename($scriptName), '', $scriptName);
$basePath = rtrim($basePath, '/');

if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$uri = rtrim($uri, '/') ?: '/';

// Definir rutas
$routes = [
    '/' => __DIR__ . '/views/home.php',
    '/category' => __DIR__ . '/views/category.php',
    '/cart' => __DIR__ . '/views/cart.php',
    '/checkout' => __DIR__ . '/views/checkout.php',
    '/order-complete' => __DIR__ . '/views/order_complete.php',
    '/login' => __DIR__ . '/views/login.php',
    '/register' => __DIR__ . '/views/register.php',
    '/product' => __DIR__ . '/views/product.php',
    // Rutas de administración
    '/admin' => __DIR__ . '/views/admin/dashboard.php',
    '/admin/login' => __DIR__ . '/views/admin/login.php',
    '/admin/products' => __DIR__ . '/views/admin/products.php',
    '/admin/products/add' => __DIR__ . '/views/admin/product-add.php',
    '/admin/products/edit' => __DIR__ . '/views/admin/product-edit.php',
    '/admin/categories' => __DIR__ . '/views/admin/categories.php',
    '/admin/orders' => __DIR__ . '/views/admin/orders.php',
];

// Buscar la ruta correspondiente
$contentView = null;

// Ruta exacta
if (isset($routes[$uri])) {
    $contentView = $routes[$uri];
} else {
    // Ruta con prefijo (ej: /category?cat=xyz)
    foreach ($routes as $route => $file) {
        if ($route !== '/' && strpos($uri, $route) === 0) {
            $contentView = $file;
            break;
        }
    }
}

// Si no se encontró, usar 404
if (!$contentView) {
    $contentView = __DIR__ . '/views/404.php';
}

// Verificar que el archivo existe
if (!file_exists($contentView)) {
    $contentView = __DIR__ . '/views/404.php';
}

$pageTitle = APP_NAME;

// Las rutas de administración tienen su propio layout completo
// No usar el layout principal para ellas
if (strpos($uri, '/admin') === 0) {
    // Las vistas de admin ya incluyen su propio HTML completo
    require $contentView;
} else {
    // Para el resto de rutas, usar el layout principal
    include __DIR__ . '/views/layout.php';
}
