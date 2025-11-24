<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "=== Test tablesExist() ===\n\n";

try {
    $db = Database::getInstance();
    
    echo "1. isConnected(): " . ($db->isConnected() ? "true" : "false") . "\n";
    
    if ($db->isConnected()) {
        $conn = $db->getConnection();
        
        echo "2. Ejecutando SHOW TABLES LIKE 'products'...\n";
        $result = $conn->query("SHOW TABLES LIKE 'products'");
        
        if ($result === false) {
            echo "   ERROR: " . $conn->error . "\n";
            echo "   tablesExist() retornará: false\n";
        } else {
            echo "   num_rows: " . $result->num_rows . "\n";
            if ($result->num_rows > 0) {
                echo "   tablesExist() retornará: true\n";
            } else {
                echo "   tablesExist() retornará: false\n";
            }
        }
        
        echo "\n3. Probando Product::tablesExist()...\n";
        $exists = Product::tablesExist();
        echo "   Retorna: " . ($exists ? "true" : "false") . "\n";
        
        echo "\n4. Verificando variables en home.php...\n";
        $dbConnected = $db->isConnected();
        $tablesExist = Product::tablesExist();
        echo "   \$dbConnected: " . ($dbConnected ? "true" : "false") . "\n";
        echo "   \$tablesExist: " . ($tablesExist ? "true" : "false") . "\n";
        
        if (!$dbConnected || !$tablesExist) {
            echo "\n   → Debería mostrar: 'Base de datos no configurada'\n";
        } else {
            echo "\n   → Debería mostrar productos\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

