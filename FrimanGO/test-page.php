<?php
/**
 * Script de prueba para verificar el estado de la base de datos
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test DB</title></head><body>";
echo "<h1>Test de Base de Datos</h1>";

echo "<h2>1. Verificar conexión</h2>";
try {
    $db = Database::getInstance();
    $isConnected = $db->isConnected();
    echo "<p>Conexión: " . ($isConnected ? "<span style='color:green'>✓ OK</span>" : "<span style='color:red'>✗ FALLO</span>") . "</p>";
    
    if ($isConnected) {
        $conn = $db->getConnection();
        echo "<p>Conexión MySQL establecida</p>";
        
        echo "<h2>2. Verificar tablas</h2>";
        $result = $conn->query("SHOW TABLES");
        if ($result === false) {
            echo "<p style='color:red'>Error al ejecutar SHOW TABLES: " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {
            echo "<p style='color:green'>✓ Tablas encontradas: " . $result->num_rows . "</p>";
            echo "<ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:orange'>⚠ Base de datos vacía (sin tablas)</p>";
        }
        
        echo "<h2>3. Verificar método tablesExist()</h2>";
        $exists = Product::tablesExist();
        echo "<p>Product::tablesExist(): " . ($exists ? "<span style='color:green'>true</span>" : "<span style='color:red'>false</span>") . "</p>";
        
        echo "<h2>4. Verificar getFeatured()</h2>";
        $products = Product::getFeatured();
        echo "<p>Productos destacados: " . count($products) . "</p>";
        if (count($products) > 0) {
            echo "<ul>";
            foreach (array_slice($products, 0, 3) as $p) {
                echo "<li>" . htmlspecialchars($p['name'] ?? 'Sin nombre') . "</li>";
            }
            echo "</ul>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>5. Variables en home.php</h2>";
echo "<p>Probando la lógica que usaría home.php:</p>";

$dbConnected = false;
$tablesExist = false;

try {
    $db = Database::getInstance();
    $dbConnected = $db->isConnected();
    if ($dbConnected) {
        $tablesExist = Product::tablesExist();
    }
    echo "<p>\$dbConnected: " . ($dbConnected ? "true" : "false") . "</p>";
    echo "<p>\$tablesExist: " . ($tablesExist ? "true" : "false") . "</p>";
    
    if (!$dbConnected || !$tablesExist) {
        echo "<p style='color:orange; font-weight:bold'>→ Mostraría: 'Base de datos no configurada'</p>";
    } else {
        $featuredProducts = Product::getFeatured();
        if (empty($featuredProducts)) {
            echo "<p style='color:blue; font-weight:bold'>→ Mostraría: 'No hay productos destacados disponibles'</p>";
        } else {
            echo "<p style='color:green; font-weight:bold'>→ Mostraría productos (total: " . count($featuredProducts) . ")</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>Volver a inicio</a> | <a href='/install/install.php'>Ir a instalación</a></p>";
echo "</body></html>";

