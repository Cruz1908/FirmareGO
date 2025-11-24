<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "=== Verificación Post-Instalación ===\n\n";

try {
    $db = Database::getInstance();
    
    echo "1. Conexión MySQL:\n";
    if (!$db->isConnected()) {
        echo "   ✗ NO CONECTADA\n";
        echo "   Verifica las credenciales en config/database.php\n";
        exit(1);
    }
    echo "   ✓ CONECTADA\n\n";
    
    $conn = $db->getConnection();
    
    // Asegurar que estamos en la base de datos correcta
    if (!empty(DB_NAME)) {
        if (!$conn->select_db(DB_NAME)) {
            echo "   ⚠ Error al seleccionar base de datos '" . DB_NAME . "': " . $conn->error . "\n";
            echo "   Esto puede significar que la base de datos no existe.\n\n";
        } else {
            echo "   ✓ Base de datos '" . DB_NAME . "' seleccionada\n\n";
        }
    }
    
    echo "2. Verificar tablas:\n";
    $result = $conn->query("SHOW TABLES");
    
    if ($result === false) {
        echo "   ✗ Error al ejecutar SHOW TABLES: " . $conn->error . "\n\n";
    } else {
        $tableCount = $result->num_rows;
        echo "   Número de tablas: " . $tableCount . "\n";
        
        if ($tableCount > 0) {
            echo "   Tablas encontradas:\n";
            $tables = [];
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
                echo "     ✓ " . $row[0] . "\n";
            }
            
            if (in_array('products', $tables)) {
                echo "\n   ✓ Tabla 'products' encontrada\n";
            } else {
                echo "\n   ✗ Tabla 'products' NO encontrada (esto es un problema)\n";
            }
        } else {
            echo "   ✗ NO HAY TABLAS - Necesitas instalar la base de datos\n";
        }
        echo "\n";
    }
    
    echo "3. Probando Product::tablesExist():\n";
    $exists = Product::tablesExist();
    echo "   Resultado: " . ($exists ? "true (✓)" : "false (✗)") . "\n\n";
    
    echo "4. Verificar lógica de vistas:\n";
    $dbConnected = $db->isConnected();
    $tablesExist = Product::tablesExist();
    
    echo "   \$dbConnected = " . ($dbConnected ? "true" : "false") . "\n";
    echo "   \$tablesExist = " . ($tablesExist ? "true" : "false") . "\n";
    echo "   Condición: !\$dbConnected || !\$tablesExist = " . ((!$dbConnected || !$tablesExist) ? "true" : "false") . "\n\n";
    
    if (!$dbConnected || !$tablesExist) {
        echo "   → La página mostrará: 'Base de datos no configurada'\n";
        echo "\n   SOLUCIÓN:\n";
        echo "   1. Ve a: http://localhost:8000/install/install.php\n";
        echo "   2. Completa el formulario con tus credenciales MySQL\n";
        echo "   3. Asegúrate de que la instalación se complete sin errores\n";
        echo "   4. Recarga esta página para verificar\n";
    } else {
        echo "   ✓ Todo está correcto - La página debería mostrar productos\n";
        
        // Verificar productos
        $prodResult = $conn->query("SELECT COUNT(*) as total FROM products");
        if ($prodResult) {
            $row = $prodResult->fetch_assoc();
            $count = $row['total'];
            echo "\n   Productos en base de datos: " . $count . "\n";
            if ($count == 0) {
                echo "   (La base de datos está instalada pero vacía)\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

