<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Debug Productos</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;background:#d4edda;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".error{color:red;font-weight:bold;background:#f8d7da;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".warning{color:orange;font-weight:bold;background:#fff3cd;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:4px;overflow:auto;}";
echo "table{border-collapse:collapse;width:100%;margin:10px 0;}";
echo "td,th{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background:#f2f2f2;}";
echo "</style></head><body><h1>🔍 Debug de Productos</h1>";

require_once __DIR__ . '/config/config.php';

echo "<div class='box'>";
echo "<h2>1. Configuración de Base de Datos</h2>";
echo "<pre>";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "</pre>";
echo "</div>";

try {
    $db = Database::getInstance();
    
    echo "<div class='box'>";
    echo "<h2>2. Estado de Conexión</h2>";
    
    if (!$db->isConnected()) {
        echo "<p class='error'>✗ Error de conexión MySQL</p>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<p class='ok'>✓ Conexión MySQL establecida</p>";
    
    $conn = $db->getConnection();
    
    // Verificar base de datos
    if ($conn->select_db(DB_NAME)) {
        echo "<p class='ok'>✓ Base de datos '" . DB_NAME . "' seleccionada</p>";
    } else {
        echo "<p class='error'>✗ Error al seleccionar base de datos: " . $conn->error . "</p>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>3. Verificación de Tablas</h2>";
    
    $result = $conn->query("SHOW TABLES");
    if ($result && $result->num_rows > 0) {
        echo "<p class='ok'>✓ Tablas encontradas: " . $result->num_rows . "</p>";
        
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
        
        // Verificar si existe tabla products
        if (in_array('products', $tables)) {
            echo "<p class='ok'>✓ Tabla 'products' existe</p>";
        } else {
            echo "<p class='error'>✗ Tabla 'products' NO existe</p>";
        }
    } else {
        echo "<p class='error'>✗ No se encontraron tablas</p>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>4. Verificar Product::tablesExist()</h2>";
    
    $exists = Product::tablesExist();
    
    if ($exists) {
        echo "<p class='ok'>✓ Product::tablesExist() retorna: <strong>true</strong></p>";
    } else {
        echo "<p class='error'>✗ Product::tablesExist() retorna: <strong>false</strong></p>";
        echo "<p>Esto causaría que la página muestre 'Base de datos no configurada'</p>";
    }
    
    echo "</div>";
    
    // Verificar productos en la base de datos
    if (in_array('products', $tables)) {
        echo "<div class='box'>";
        echo "<h2>5. Productos en Base de Datos</h2>";
        
        // Contar productos
        $result = $conn->query("SELECT COUNT(*) as total FROM products");
        if ($result) {
            $row = $result->fetch_assoc();
            $total = $row['total'];
            
            echo "<p><strong>Total de productos:</strong> " . $total . "</p>";
            
            if ($total > 0) {
                echo "<p class='ok'>✓ Hay productos en la base de datos</p>";
                
                // Mostrar productos destacados
                $result = $conn->query("SELECT * FROM products WHERE featured = 1 LIMIT 10");
                if ($result && $result->num_rows > 0) {
                    echo "<h3>Productos destacados (featured = 1):</h3>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Nombre</th><th>Nombre CA</th><th>Precio</th><th>Featured</th><th>Active</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row['name_ca'] ?? '') . "</td>";
                        echo "<td>" . $row['price'] . " €</td>";
                        echo "<td>" . ($row['featured'] ? 'Sí' : 'No') . "</td>";
                        echo "<td>" . (isset($row['active']) && $row['active'] ? 'Sí' : 'No') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='warning'>⚠ No hay productos con featured = 1</p>";
                    
                    // Mostrar todos los productos
                    $result = $conn->query("SELECT * FROM products LIMIT 10");
                    if ($result && $result->num_rows > 0) {
                        echo "<h3>Todos los productos (primeros 10):</h3>";
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Nombre</th><th>Nombre CA</th><th>Precio</th><th>Featured</th><th>Active</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['name_ca'] ?? '') . "</td>";
                            echo "<td>" . $row['price'] . " €</td>";
                            echo "<td>" . ($row['featured'] ? 'Sí' : 'No') . "</td>";
                        echo "<td>" . (isset($row['active']) && $row['active'] ? 'Sí' : 'No') . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
                
                // Verificar estructura de la tabla
                echo "<h3>Estructura de la tabla products:</h3>";
                $result = $conn->query("DESCRIBE products");
                if ($result) {
                    echo "<table>";
                    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Field'] . "</td>";
                        echo "<td>" . $row['Type'] . "</td>";
                        echo "<td>" . $row['Null'] . "</td>";
                        echo "<td>" . $row['Key'] . "</td>";
                        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<p class='warning'>⚠ La base de datos está instalada pero NO HAY PRODUCTOS</p>";
                echo "<p>Esto es normal si acabas de instalar la base de datos. La página debería mostrar:</p>";
                echo "<p><strong>'No hay productos destacados disponibles'</strong></p>";
                echo "<p>NO debería mostrar 'Base de datos no configurada'</p>";
            }
        } else {
            echo "<p class='error'>✗ Error al contar productos: " . $conn->error . "</p>";
        }
        
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>6. Prueba de Product::getFeatured()</h2>";
        
        try {
            $featuredProducts = Product::getFeatured();
            
            echo "<p><strong>Productos retornados por getFeatured():</strong> " . count($featuredProducts) . "</p>";
            
            if (count($featuredProducts) > 0) {
                echo "<p class='ok'>✓ getFeatured() retorna productos</p>";
                echo "<ul>";
                foreach (array_slice($featuredProducts, 0, 5) as $product) {
                    echo "<li>" . htmlspecialchars($product['name_ca'] ?? $product['name'] ?? 'Sin nombre') . " - " . $product['price'] . " €</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='warning'>⚠ getFeatured() retorna un array vacío</p>";
                echo "<p>Esto causaría que la página muestre 'No hay productos destacados disponibles'</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error al llamar getFeatured(): " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        echo "</div>";
        
        echo "<div class='box'>";
        echo "<h2>7. Simulación de home.php</h2>";
        
        $dbConnected = $db->isConnected();
        $tablesExist = Product::tablesExist();
        
        echo "<p><strong>\$dbConnected:</strong> " . ($dbConnected ? "true" : "false") . "</p>";
        echo "<p><strong>\$tablesExist:</strong> " . ($tablesExist ? "true" : "false") . "</p>";
        echo "<p><strong>Condición:</strong> !\$dbConnected || !\$tablesExist</p>";
        
        if (!$dbConnected || !$tablesExist) {
            echo "<p class='error'>✗ Resultado: <strong>true</strong></p>";
            echo "<p>→ La página mostraría: 'Base de datos no configurada'</p>";
        } else {
            echo "<p class='ok'>✓ Resultado: <strong>false</strong></p>";
            echo "<p>→ La página debería mostrar productos o 'No hay productos destacados'</p>";
            
            $featuredProducts = Product::getFeatured();
            
            if (empty($featuredProducts)) {
                echo "<p class='warning'>⚠ Pero getFeatured() retorna array vacío</p>";
                echo "<p>→ La página mostrará: 'No hay productos destacados disponibles'</p>";
            } else {
                echo "<p class='ok'>✓ Y getFeatured() retorna " . count($featuredProducts) . " productos</p>";
                echo "<p>→ La página debería mostrar los productos correctamente</p>";
            }
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='box'>";
    echo "<h2 class='error'>Error</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<div class='box'>";
echo "<h2>8. Acciones</h2>";
echo "<p><a href='/'>← Volver a inicio</a></p>";
echo "<p><a href='/fix-database-name.php'>🔧 Verificar configuración de BD</a></p>";
echo "<p><a href='/verificar-tablas.php'>🔍 Verificar tablas</a></p>";
echo "</div>";

echo "</body></html>";

