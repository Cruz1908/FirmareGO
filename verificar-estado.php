<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Estado del Sistema</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .warning{color:orange;font-weight:bold;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "</style></head><body><h1>Estado del Sistema FrimanGO</h1>";

$db = Database::getInstance();
$connected = $db->isConnected();
$tablesExist = Product::tablesExist();

echo "<div class='box'>";
echo "<h2>Estado de la Base de Datos</h2>";
echo "<p>Conexión MySQL: <span class='" . ($connected ? "ok" : "error") . "'>" . ($connected ? "✓ CONECTADA" : "✗ NO CONECTADA") . "</span></p>";
echo "<p>Tablas instaladas: <span class='" . ($tablesExist ? "ok" : "warning") . "'>" . ($tablesExist ? "✓ SÍ" : "✗ NO") . "</span></p>";

if ($connected && !$tablesExist) {
    echo "<div style='background:#fff3cd;padding:15px;border-radius:8px;margin-top:15px;border-left:4px solid #ffc107;'>";
    echo "<h3>⚠ Acción Requerida</h3>";
    echo "<p>La base de datos está conectada pero <strong>no tiene tablas instaladas</strong>.</p>";
    echo "<p><strong>Solución:</strong> Debes instalar las tablas usando el instalador.</p>";
    echo "<p><a href='/install/install.php' style='display:inline-block;padding:12px 24px;background:#FFD200;color:#111827;text-decoration:none;border-radius:8px;font-weight:600;margin-top:10px;'>Ir a Instalador de Base de Datos</a></p>";
    echo "</div>";
}

if ($connected && $tablesExist) {
    $conn = $db->getConnection();
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    if ($result) {
        $row = $result->fetch_assoc();
        $productCount = $row['total'];
        echo "<p>Productos en base de datos: <strong>" . $productCount . "</strong></p>";
    }
}

echo "</div>";

echo "<div class='box'>";
echo "<h2>¿Qué hacer ahora?</h2>";
if (!$connected) {
    echo "<ol><li>Verifica que MySQL esté corriendo en XAMPP</li>";
    echo "<li>Verifica las credenciales en <code>config/database.php</code></li></ol>";
} elseif (!$tablesExist) {
    echo "<ol><li>Haz clic en el botón de arriba para ir al instalador</li>";
    echo "<li>O ve manualmente a: <a href='/install/install.php'>/install/install.php</a></li>";
    echo "<li>Completa el formulario con tus credenciales MySQL</li>";
    echo "<li>El instalador creará todas las tablas necesarias</li></ol>";
} else {
    echo "<p class='ok'>✓ Todo está configurado correctamente. Puedes usar el sitio normalmente.</p>";
    echo "<p><a href='/'>Ir a la página principal</a></p>";
}
echo "</div>";

echo "<div class='box'>";
echo "<h2>Rutas del Sistema</h2>";
echo "<ul>";
echo "<li><a href='/'>Página principal</a></li>";
echo "<li><a href='/category'>Productos</a></li>";
echo "<li><a href='/login'>Login</a></li>";
echo "<li><a href='/install/install.php'>Instalador de BD</a></li>";
echo "<li><a href='/debug-page.php'>Página de Debug</a></li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";

