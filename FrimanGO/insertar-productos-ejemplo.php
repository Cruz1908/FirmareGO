<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Insertar Productos de Ejemplo</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;background:#d4edda;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".error{color:red;font-weight:bold;background:#f8d7da;padding:10px;border-radius:4px;margin:5px 0;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "</style></head><body><h1>📦 Insertar Productos de Ejemplo</h1>";

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo "<div class='box'><p class='error'>✗ Error de conexión a la base de datos</p></div>";
        echo "</body></html>";
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->select_db(DB_NAME);
    
    // Verificar si ya hay productos
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $row = $result->fetch_assoc();
    $totalActual = $row['total'];
    
    echo "<div class='box'>";
    echo "<h2>Estado Actual</h2>";
    echo "<p>Productos en la base de datos: <strong>" . $totalActual . "</strong></p>";
    echo "</div>";
    
    if ($totalActual > 0) {
        echo "<div class='box'>";
        echo "<p class='ok'>✓ Ya hay productos en la base de datos.</p>";
        echo "<p>Si quieres agregar más productos de ejemplo, puedes hacerlo manualmente o ejecutar este script nuevamente.</p>";
        echo "<p><a href='/'>← Volver a inicio</a></p>";
        echo "</div>";
    } else {
        // Productos de ejemplo
        $productos = [
            ['name' => 'Salmón Fresco', 'name_ca' => 'Salmó Fresc', 'slug' => 'salmon-fresco', 'price' => 18.50, 'category' => 'pescado', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Atún Rojo', 'name_ca' => 'Tonyina Vermella', 'slug' => 'atun-rojo', 'price' => 25.00, 'category' => 'pescado', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Merluza', 'name_ca' => 'Lluç', 'slug' => 'merluza', 'price' => 12.00, 'category' => 'pescado', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Gambas', 'name_ca' => 'Gambes', 'slug' => 'gambas', 'price' => 22.00, 'category' => 'marisco', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Mejillones', 'name_ca' => 'Musclos', 'slug' => 'mejillones', 'price' => 8.50, 'category' => 'marisco', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Calamares', 'name_ca' => 'Calamars', 'slug' => 'calamares', 'price' => 15.00, 'category' => 'marisco', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Lubina', 'name_ca' => 'Llobarro', 'slug' => 'lubina', 'price' => 16.50, 'category' => 'pescado', 'unit' => 'kg', 'featured' => 1],
            ['name' => 'Dorada', 'name_ca' => 'Orada', 'slug' => 'dorada', 'price' => 14.00, 'category' => 'pescado', 'unit' => 'kg', 'featured' => 1],
        ];
        
        echo "<div class='box'>";
        echo "<h2>Insertando Productos de Ejemplo</h2>";
        
        $insertados = 0;
        $errores = [];
        
        foreach ($productos as $producto) {
            // Verificar si existe la columna active
            $checkActive = $conn->query("SHOW COLUMNS FROM products LIKE 'active'");
            $hasActive = ($checkActive !== false && $checkActive->num_rows > 0);
            
            if ($hasActive) {
                $query = "INSERT INTO products (name, name_ca, slug, price, category, unit, featured, active, stock) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 1, 100)";
            } else {
                $query = "INSERT INTO products (name, name_ca, slug, price, category, unit, featured, stock) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 100)";
            }
            
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                $errores[] = "Error preparando: " . $producto['name'] . " - " . $conn->error;
                continue;
            }
            
            if ($hasActive) {
                $stmt->bind_param('ssdsssi', 
                    $producto['name'],
                    $producto['name_ca'],
                    $producto['slug'],
                    $producto['price'],
                    $producto['category'],
                    $producto['unit'],
                    $producto['featured']
                );
            } else {
                $stmt->bind_param('ssdsssi', 
                    $producto['name'],
                    $producto['name_ca'],
                    $producto['slug'],
                    $producto['price'],
                    $producto['category'],
                    $producto['unit'],
                    $producto['featured']
                );
            }
            
            if ($stmt->execute()) {
                $insertados++;
            } else {
                $errores[] = "Error insertando " . $producto['name'] . ": " . $stmt->error;
            }
            
            $stmt->close();
        }
        
        echo "<p class='ok'>✓ Productos insertados: " . $insertados . " de " . count($productos) . "</p>";
        
        if (!empty($errores)) {
            echo "<h3>Errores:</h3>";
            echo "<ul>";
            foreach ($errores as $error) {
                echo "<li class='error'>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
        
        echo "</div>";
        
        if ($insertados > 0) {
            echo "<div class='box'>";
            echo "<p class='ok'>✓ ¡Productos insertados correctamente!</p>";
            echo "<p>Ahora puedes ver los productos en la página principal.</p>";
            echo "<p><a href='/' style='display:inline-block;padding:12px 24px;background:#FFD200;color:#111827;text-decoration:none;border-radius:8px;font-weight:600;margin-top:10px;'>Ver página principal</a></p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='box'>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</body></html>";

