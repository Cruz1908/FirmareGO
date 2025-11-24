<?php
/**
 * Configuración principal del proyecto FrimanGO
 */

// Configuración de la aplicación
define('APP_NAME', 'FrimanGO');
define('APP_ROOT', __DIR__ . '/..');

// Detectar APP_URL automáticamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', $scriptPath), '/');
define('APP_URL', $protocol . '://' . $host . $basePath);

define('PRODUCTION', false); // Cambiar a true en producción

// Cargar configuración de base de datos MySQL
require_once __DIR__ . '/database.php';

// Tipo de base de datos (mysql o sqlite)
define('DB_TYPE', 'mysql');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Configuración de zona horaria
date_default_timezone_set('Europe/Madrid');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de idioma
// Si no hay idioma seleccionado, usar español por defecto
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'es';
}
define('CURRENT_LANG', $_SESSION['lang']);

// Configuración OAuth
// Para desarrollo, estas constantes pueden estar vacías
// Configúralas en producción
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', APP_URL . '/api/oauth/google-callback.php');

define('FACEBOOK_APP_ID', $_ENV['FACEBOOK_APP_ID'] ?? '');
define('FACEBOOK_APP_SECRET', $_ENV['FACEBOOK_APP_SECRET'] ?? '');
define('FACEBOOK_REDIRECT_URI', APP_URL . '/api/oauth/facebook-callback.php');

define('APPLE_CLIENT_ID', $_ENV['APPLE_CLIENT_ID'] ?? '');
define('APPLE_TEAM_ID', $_ENV['APPLE_TEAM_ID'] ?? '');
define('APPLE_KEY_ID', $_ENV['APPLE_KEY_ID'] ?? '');
define('APPLE_REDIRECT_URI', APP_URL . '/api/oauth/apple-callback.php');

// Configuración de pagos (Stripe)
define('STRIPE_PUBLIC_KEY', $_ENV['STRIPE_PUBLIC_KEY'] ?? 'pk_test_...');
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_...');
define('STRIPE_WEBHOOK_SECRET', $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '');

// Clases de utilidad
require_once APP_ROOT . '/src/Database.php';
require_once APP_ROOT . '/src/Product.php';
require_once APP_ROOT . '/src/Cart.php';
require_once APP_ROOT . '/src/Auth.php';
require_once APP_ROOT . '/src/OAuth.php';
require_once APP_ROOT . '/src/Payment.php';
require_once APP_ROOT . '/src/Lang.php';

// Los modelos se inicializan en index.php
