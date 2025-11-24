<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular exactamente lo que hace home.php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Debug - Inicio';
$dbConnected = false;
$tablesExist = false;

try {
    $db = Database::getInstance();
    $dbConnected = $db->isConnected();
    if ($dbConnected) {
        $tablesExist = Product::tablesExist();
    }
    $featuredProducts = $dbConnected && $tablesExist ? Product::getFeatured() : [];
    if (!is_array($featuredProducts)) {
        $featuredProducts = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo productos destacados: " . $e->getMessage());
    $featuredProducts = [];
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Debug - <?= APP_NAME ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .debug-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .var { margin: 10px 0; padding: 10px; background: #f0f0f0; border-left: 4px solid #007cba; }
    .true { color: green; font-weight: bold; }
    .false { color: red; font-weight: bold; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>🔍 Debug de Página de Inicio</h1>
  
  <div class="debug-box">
    <h2>Variables de Estado</h2>
    <div class="var">
      <strong>\$dbConnected:</strong> 
      <span class="<?= $dbConnected ? 'true' : 'false' ?>"><?= $dbConnected ? 'true' : 'false' ?></span>
    </div>
    <div class="var">
      <strong>\$tablesExist:</strong> 
      <span class="<?= $tablesExist ? 'true' : 'false' ?>"><?= $tablesExist ? 'true' : 'false' ?></span>
    </div>
    <div class="var">
      <strong>\$featuredProducts:</strong> 
      Array con <?= count($featuredProducts) ?> elementos
    </div>
  </div>
  
  <div class="debug-box">
    <h2>Condición PHP</h2>
    <div class="section">
      <strong>if (!$dbConnected || !$tablesExist):</strong><br>
      <?php if (!$dbConnected || !$tablesExist): ?>
        <span class="true">✓ TRUE - Debería mostrar "Base de datos no configurada"</span>
      <?php else: ?>
        <span class="false">✗ FALSE - No entraría en esta condición</span>
      <?php endif; ?>
    </div>
    
    <?php if (!$dbConnected || !$tablesExist): ?>
      <div class="section" style="background: #fff3cd; border-color: #ffc107;">
        <h3>Mensaje que se mostraría:</h3>
        <p style="font-size: 18px; margin-bottom: 16px;">Base de datos no configurada</p>
        <p style="font-size: 14px; margin-bottom: 20px;">Es necesario instalar la base de datos para que el sitio funcione correctamente.</p>
        <a href="/install/install.php" style="display: inline-block; padding: 12px 24px; background: #FFD200; color: #111827; text-decoration: none; border-radius: 8px; font-weight: 600;">Instalar base de datos</a>
      </div>
    <?php elseif (empty($featuredProducts)): ?>
      <div class="section" style="background: #d1ecf1; border-color: #0c5460;">
        <h3>Mensaje que se mostraría:</h3>
        <p style="font-size: 18px; margin-bottom: 16px;">No hay productos destacados disponibles</p>
        <p style="font-size: 14px;">Próximamente agregaremos productos nuevos.</p>
      </div>
    <?php else: ?>
      <div class="section" style="background: #d4edda; border-color: #155724;">
        <h3>Mostraría productos:</h3>
        <p>Total de productos: <?= count($featuredProducts) ?></p>
      </div>
    <?php endif; ?>
  </div>
  
  <div class="debug-box">
    <h2>Prueba de Conexión Detallada</h2>
    <?php
    try {
        $db = Database::getInstance();
        echo "<p>Conexión establecida: " . ($db->isConnected() ? "<span class='true'>✓ Sí</span>" : "<span class='false'>✗ No</span>") . "</p>";
        
        if ($db->isConnected()) {
            $conn = $db->getConnection();
            $result = $conn->query("SHOW TABLES");
            if ($result === false) {
                echo "<p>Error al verificar tablas: " . htmlspecialchars($conn->error) . "</p>";
            } else {
                echo "<p>Número de tablas: " . $result->num_rows . "</p>";
                if ($result->num_rows > 0) {
                    echo "<ul>";
                    while ($row = $result->fetch_array()) {
                        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
                    }
                    echo "</ul>";
                }
            }
        }
    } catch (Exception $e) {
        echo "<p class='false'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
  </div>
  
  <hr>
  <p><a href="/">Volver a inicio</a> | <a href="/install/install.php">Ir a instalación</a> | <a href="/test-page.php">Test completo</a></p>
</body>
</html>

