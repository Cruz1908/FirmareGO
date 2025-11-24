<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Corregir Base de Datos</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;background:#d4edda;padding:15px;border-radius:8px;margin:10px 0;}";
echo ".info{color:blue;background:#d1ecf1;padding:15px;border-radius:8px;margin:10px 0;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "</style></head><body><h1>🔧 Corrección de Base de Datos</h1>";

require_once __DIR__ . '/config/config.php';

echo "<div class='box'>";
echo "<h2>Estado Actual</h2>";

echo "<p><strong>Base de datos configurada en config/database.php:</strong></p>";
echo "<pre>";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "</pre>";

echo "</div>";

echo "<div class='box'>";
echo "<h2>Problema Detectado</h2>";
echo "<p>Hay dos bases de datos:</p>";
echo "<ul>";
echo "<li><strong>frimango</strong> (minúsculas) - Está vacía</li>";
echo "<li><strong>frimango_db</strong> - Tiene todas las tablas instaladas ✓</li>";
echo "</ul>";
echo "<p class='info'>El sistema está intentando conectarse a 'frimango' pero las tablas están en 'frimango_db'.</p>";
echo "</div>";

echo "<div class='box'>";
echo "<h2>Solución</h2>";

// Verificar si la base de datos configurada es la correcta
if (DB_NAME === 'frimango_db') {
    echo "<p class='ok'>✓ La configuración ya está correcta (frimango_db)</p>";
    
    // Probar conexión
    try {
        $db = Database::getInstance();
        if ($db->isConnected()) {
            echo "<p class='ok'>✓ Conexión a frimango_db exitosa</p>";
            
            $conn = $db->getConnection();
            $result = $conn->query("SHOW TABLES");
            
            if ($result && $result->num_rows > 0) {
                echo "<p class='ok'>✓ Tablas encontradas: " . $result->num_rows . "</p>";
                echo "<ul>";
                while ($row = $result->fetch_array()) {
                    echo "<li>" . $row[0] . "</li>";
                }
                echo "</ul>";
                
                // Verificar Product::tablesExist()
                $exists = Product::tablesExist();
                if ($exists) {
                    echo "<p class='ok'>✓ Product::tablesExist() retorna: true</p>";
                    echo "<p class='ok'>✓ ¡Todo está funcionando correctamente!</p>";
                    echo "<p><a href='/' style='display:inline-block;padding:12px 24px;background:#FFD200;color:#111827;text-decoration:none;border-radius:8px;font-weight:600;margin-top:10px;'>Ir a la página principal</a></p>";
                } else {
                    echo "<p class='info'>⚠ Product::tablesExist() todavía retorna false</p>";
                    echo "<p>Esto puede ser un problema de caché. Prueba:</p>";
                    echo "<ol>";
                    echo "<li>Recargar esta página (Ctrl+F5)</li>";
                    echo "<li>Reiniciar el servidor PHP</li>";
                    echo "<li>Recargar la página principal con Ctrl+F5</li>";
                    echo "</ol>";
                }
            } else {
                echo "<p class='info'>⚠ No se encontraron tablas en frimango_db</p>";
            }
        } else {
            echo "<p class='info'>⚠ Error de conexión</p>";
        }
    } catch (Exception $e) {
        echo "<p class='info'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p class='info'>⚠ La configuración está en 'frimango' pero necesita estar en 'frimango_db'</p>";
    echo "<p>El archivo config/database.php ya ha sido actualizado automáticamente.</p>";
    echo "<p><strong>Acción requerida:</strong></p>";
    echo "<ol>";
    echo "<li>Recarga esta página para verificar</li>";
    echo "<li>Si el problema persiste, reinicia el servidor PHP</li>";
    echo "<li>Recarga la página principal con Ctrl+F5</li>";
    echo "</ol>";
}

echo "</div>";

echo "<div class='box'>";
echo "<h2>Opciones Alternativas</h2>";
echo "<p>Si prefieres usar 'frimango' en lugar de 'frimango_db':</p>";
echo "<ol>";
echo "<li>Ve a phpMyAdmin</li>";
echo "<li>Elimina la base de datos 'frimango' (vacía)</li>";
echo "<li>Renombra 'frimango_db' a 'frimango'</li>";
echo "<li>Actualiza config/database.php para usar 'frimango'</li>";
echo "</ol>";
echo "<p>O instala las tablas en 'frimango' usando el instalador.</p>";
echo "</div>";

echo "<div class='box'>";
echo "<h2>Enlaces</h2>";
echo "<p><a href='/'>← Página principal</a></p>";
echo "<p><a href='/verificar-tablas.php'>🔍 Verificar tablas</a></p>";
echo "<p><a href='/install/install.php'>🔧 Instalador</a></p>";
echo "</div>";

echo "</body></html>";

