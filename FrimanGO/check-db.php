<?php
require 'config/config.php';

echo "=== Verificación de Base de Datos ===\n\n";

try {
    $db = Database::getInstance();
    
    if ($db->isConnected()) {
        echo "✓ Conexión a MySQL: OK\n";
        $conn = $db->getConnection();
        
        $result = $conn->query("SHOW TABLES");
        if ($result && $result->num_rows > 0) {
            echo "✓ Tablas encontradas: " . $result->num_rows . "\n";
            while ($row = $result->fetch_array()) {
                echo "  - " . $row[0] . "\n";
            }
            
            // Verificar productos
            $prodResult = $conn->query("SELECT COUNT(*) as total FROM products");
            if ($prodResult) {
                $prodData = $prodResult->fetch_assoc();
                echo "\n✓ Productos en base de datos: " . $prodData['total'] . "\n";
            }
        } else {
            echo "⚠ Base de datos vacía (sin tablas)\n";
            echo "  → Ve a: http://localhost:8000/install/install.php para instalar\n";
        }
    } else {
        echo "✗ Error de conexión a MySQL\n";
        echo "  Verifica:\n";
        echo "  1. Que MySQL esté corriendo en XAMPP\n";
        echo "  2. Las credenciales en config/database.php\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Configuración actual ===\n";
echo "Host: " . DB_HOST . "\n";
echo "Usuario: " . DB_USER . "\n";
echo "Base de datos: " . DB_NAME . "\n";
echo "\n";

