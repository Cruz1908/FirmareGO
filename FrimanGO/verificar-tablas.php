<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Verificación de Tablas</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;background:#d4edda;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".error{color:red;font-weight:bold;background:#f8d7da;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".warning{color:orange;font-weight:bold;background:#fff3cd;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:4px;overflow:auto;}";
echo "</style></head><body><h1>🔍 Verificación Detallada de Tablas</h1>";

require_once __DIR__ . '/config/config.php';

try {
    echo "<div class='box'>";
    echo "<h2>1. Información de Conexión</h2>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Usuario:</strong> " . DB_USER . "</p>";
    echo "<p><strong>Base de datos:</strong> " . DB_NAME . "</p>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>2. Estado de Conexión</h2>";
    
    $db = Database::getInstance();
    $isConnected = $db->isConnected();
    
    if ($isConnected) {
        echo "<p class='ok'>✓ Conexión MySQL establecida</p>";
    } else {
        echo "<p class='error'>✗ Error de conexión MySQL</p>";
        echo "</div></body></html>";
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Verificar selección de base de datos
    echo "<h3>Selección de Base de Datos</h3>";
    if ($conn->select_db(DB_NAME)) {
        echo "<p class='ok'>✓ Base de datos '" . DB_NAME . "' seleccionada correctamente</p>";
    } else {
        echo "<p class='error'>✗ Error al seleccionar base de datos '" . DB_NAME . "': " . $conn->error . "</p>";
        echo "<p>Esto significa que la base de datos no existe o no tienes permisos.</p>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>3. Verificación de Tablas</h2>";
    
    // Método 1: SHOW TABLES
    echo "<h3>Método 1: SHOW TABLES</h3>";
    $result = $conn->query("SHOW TABLES");
    
    if ($result === false) {
        echo "<p class='error'>✗ Error al ejecutar SHOW TABLES: " . $conn->error . "</p>";
    } else {
        $tableCount = $result->num_rows;
        echo "<p>Número de tablas encontradas: <strong>" . $tableCount . "</strong></p>";
        
        if ($tableCount > 0) {
            echo "<p class='ok'>✓ Se encontraron tablas en la base de datos</p>";
            echo "<ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>✗ No se encontraron tablas en la base de datos</p>";
            echo "<p><strong>SOLUCIÓN:</strong> Ve a <a href='/install/install.php'>/install/install.php</a> para instalar las tablas.</p>";
        }
    }
    
    // Método 2: Verificar tabla products específicamente
    echo "<h3>Método 2: Verificar tabla 'products'</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'products'");
    
    if ($result === false) {
        echo "<p class='error'>✗ Error: " . $conn->error . "</p>";
    } else {
        if ($result->num_rows > 0) {
            echo "<p class='ok'>✓ Tabla 'products' existe</p>";
        } else {
            echo "<p class='error'>✗ Tabla 'products' NO existe</p>";
        }
    }
    
    // Método 3: information_schema
    echo "<h3>Método 3: information_schema</h3>";
    $query = "SELECT COUNT(*) as count FROM information_schema.tables 
              WHERE table_schema = ? AND table_name = 'products'";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        echo "<p class='warning'>⚠ No se pudo preparar consulta con information_schema: " . $conn->error . "</p>";
    } else {
        $dbName = DB_NAME;
        $stmt->bind_param('s', $dbName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                echo "<p class='ok'>✓ Tabla 'products' encontrada en information_schema</p>";
            } else {
                echo "<p class='error'>✗ Tabla 'products' NO encontrada en information_schema</p>";
            }
        }
        $stmt->close();
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>4. Prueba de Product::tablesExist()</h2>";
    
    $exists = Product::tablesExist();
    
    if ($exists) {
        echo "<p class='ok'>✓ Product::tablesExist() retorna: <strong>true</strong></p>";
        echo "<p>Esto significa que el sistema debería mostrar productos correctamente.</p>";
    } else {
        echo "<p class='error'>✗ Product::tablesExist() retorna: <strong>false</strong></p>";
        echo "<p>Esto significa que la página mostrará 'Base de datos no configurada'.</p>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>5. Simulación de home.php</h2>";
    
    $dbConnected = $db->isConnected();
    $tablesExist = Product::tablesExist();
    
    echo "<p><strong>\$dbConnected:</strong> " . ($dbConnected ? "true" : "false") . "</p>";
    echo "<p><strong>\$tablesExist:</strong> " . ($tablesExist ? "true" : "false") . "</p>";
    echo "<p><strong>Condición:</strong> !\$dbConnected || !\$tablesExist</p>";
    
    if (!$dbConnected || !$tablesExist) {
        echo "<p class='error'>✗ Resultado: <strong>true</strong></p>";
        echo "<p>→ La página mostrará: 'Base de datos no configurada'</p>";
        echo "<p><strong>SOLUCIÓN:</strong> Si instalaste las tablas pero esto sigue mostrando false, puede ser un problema de caché.</p>";
        echo "<p>1. Asegúrate de que las tablas se crearon correctamente</p>";
        echo "<p>2. Recarga esta página (Ctrl+F5 para limpiar caché)</p>";
        echo "<p>3. Si el problema persiste, reinicia el servidor PHP</p>";
    } else {
        echo "<p class='ok'>✓ Resultado: <strong>false</strong></p>";
        echo "<p>→ La página debería mostrar productos (o 'No hay productos' si la BD está vacía)</p>";
        
        // Verificar productos
        $prodResult = $conn->query("SELECT COUNT(*) as total FROM products");
        if ($prodResult) {
            $row = $prodResult->fetch_assoc();
            $count = $row['total'];
            echo "<p><strong>Productos en base de datos:</strong> " . $count . "</p>";
        }
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>6. Acciones</h2>";
    echo "<p><a href='/'>← Volver a inicio</a></p>";
    echo "<p><a href='/install/install.php'>🔧 Ir al instalador</a></p>";
    echo "<p><a href='/verificar-estado.php'>📊 Ver estado del sistema</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='box'>";
    echo "<h2 class='error'>Error</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";

