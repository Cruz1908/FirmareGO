<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "=== Verificación Detallada de Tablas ===\n\n";

try {
    $db = Database::getInstance();
    
    echo "1. Verificar conexión:\n";
    echo "   isConnected(): " . ($db->isConnected() ? "true" : "false") . "\n\n";
    
    if (!$db->isConnected()) {
        echo "ERROR: No hay conexión a la base de datos\n";
        exit(1);
    }
    
    $conn = $db->getConnection();
    
    echo "2. Verificar si la base de datos existe:\n";
    $result = $conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($result && $result->num_rows > 0) {
        echo "   ✓ Base de datos '" . DB_NAME . "' existe\n\n";
    } else {
        echo "   ✗ Base de datos '" . DB_NAME . "' NO existe\n\n";
        exit(1);
    }
    
    echo "3. Verificar tablas en la base de datos:\n";
    $result = $conn->query("SHOW TABLES");
    if ($result === false) {
        echo "   ERROR al ejecutar SHOW TABLES: " . $conn->error . "\n\n";
        exit(1);
    }
    
    $tableCount = $result->num_rows;
    echo "   Número de tablas: " . $tableCount . "\n";
    
    if ($tableCount > 0) {
        echo "   Tablas encontradas:\n";
        while ($row = $result->fetch_array()) {
            echo "     - " . $row[0] . "\n";
        }
        echo "\n";
    } else {
        echo "   ✗ No hay tablas en la base de datos\n\n";
    }
    
    echo "4. Verificar tabla 'products' específicamente:\n";
    $result = $conn->query("SHOW TABLES LIKE 'products'");
    if ($result === false) {
        echo "   ERROR: " . $conn->error . "\n\n";
    } else {
        echo "   Resultado: " . ($result->num_rows > 0 ? "✓ Tabla 'products' existe" : "✗ Tabla 'products' NO existe") . "\n\n";
    }
    
    echo "5. Probando Product::tablesExist():\n";
    $exists = Product::tablesExist();
    echo "   Retorna: " . ($exists ? "true (✓)" : "false (✗)") . "\n\n";
    
    echo "6. Verificar lógica de home.php:\n";
    $dbConnected = $db->isConnected();
    $tablesExist = Product::tablesExist();
    
    echo "   \$dbConnected = " . ($dbConnected ? "true" : "false") . "\n";
    echo "   \$tablesExist = " . ($tablesExist ? "true" : "false") . "\n";
    echo "   Condición: !\$dbConnected || !\$tablesExist\n";
    echo "   Resultado: " . ((!$dbConnected || !$tablesExist) ? "true → Mostrar 'Base de datos no configurada'" : "false → Mostrar productos") . "\n";
    
    if ($tableCount > 0 && !$tablesExist) {
        echo "\n⚠ PROBLEMA DETECTADO: Hay tablas pero tablesExist() retorna false\n";
        echo "   Esto indica un problema en el método tablesExist()\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

