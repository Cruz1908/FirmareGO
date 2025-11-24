<?php
/**
 * Script de debug para verificar configuración
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "<h1>Debug FrimanGO</h1>";
echo "<h2>Configuración de Base de Datos</h2>";
echo "<pre>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "\n";
echo "DB_TYPE: " . (defined('DB_TYPE') ? DB_TYPE : 'NO DEFINIDO') . "\n";
echo "</pre>";

echo "<h2>Prueba de Conexión</h2>";
try {
    require_once __DIR__ . '/src/Database.php';
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($db->isConnected()) {
        echo "<p style='color: green;'>✅ Conexión exitosa a MySQL</p>";
        
        // Verificar tablas
        echo "<h3>Tablas en la base de datos:</h3>";
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
        
        // Verificar categorías
        echo "<h3>Categorías:</h3>";
        $result = $conn->query("SELECT COUNT(*) as total FROM categories");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Total: " . $row['total'] . "</p>";
            
            if ($row['total'] > 0) {
                $result = $conn->query("SELECT * FROM categories LIMIT 5");
                echo "<pre>";
                while ($cat = $result->fetch_assoc()) {
                    print_r($cat);
                }
                echo "</pre>";
            }
        }
        
        // Verificar productos
        echo "<h3>Productos:</h3>";
        $result = $conn->query("SELECT COUNT(*) as total FROM products");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Total: " . $row['total'] . "</p>";
            
            if ($row['total'] > 0) {
                $result = $conn->query("SELECT * FROM products LIMIT 5");
                echo "<pre>";
                while ($prod = $result->fetch_assoc()) {
                    print_r($prod);
                }
                echo "</pre>";
            } else {
                echo "<p style='color: orange;'>⚠️ No hay productos en la base de datos. Revisa install/database.sql</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Prueba de Modelo Product</h2>";
try {
    $categories = Product::getCategories();
    echo "<p>Categorías obtenidas: " . count($categories) . "</p>";
    
    $products = Product::getFeatured();
    echo "<p>Productos destacados obtenidos: " . count($products) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Rutas</h2>";
echo "<pre>";
$routes = [
    '/' => 'views/home.php',
    '/login' => 'views/login.php',
    '/register' => 'views/register.php',
    '/category' => 'views/category.php',
];
foreach ($routes as $route => $file) {
    $path = __DIR__ . '/' . $file;
    echo "$route => " . (file_exists($path) ? '✅' : '❌') . " $file\n";
}
echo "</pre>";

