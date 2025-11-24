<?php
/**
 * Script de instalación automática para XAMPP
 * Crea la base de datos y las tablas necesarias
 * 
 * IMPORTANTE: Este script NO carga ningún archivo de configuración
 * para evitar conflictos con credenciales preexistentes
 */

// Asegurar que no hay configuraciones previas interfiriendo
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación FrimanGO</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #111827;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #111827;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #FFD200;
            box-shadow: 0 0 0 3px rgba(255, 210, 0, 0.1);
        }
        button {
            background: #FFD200;
            color: #111827;
            border: 0;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #e6bd00;
        }
        .success {
            background: #10b981;
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            background: #ef4444;
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .warning {
            background: #f59e0b;
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info {
            background: #3b82f6;
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .code {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            margin-top: 20px;
            overflow-x: auto;
            font-size: 13px;
        }
        small {
            color: #6b7280;
            font-size: 14px;
            display: block;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Instalación FrimanGO</h1>
        <p class="subtitle">Configuración de base de datos MySQL para XAMPP</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y limpiar datos del formulario
            $host = trim($_POST['host'] ?? 'localhost');
            $username = trim($_POST['username'] ?? 'root');
            $password = trim($_POST['password'] ?? '');
            $database = trim($_POST['database'] ?? 'frimango');

            // Validar datos
            if (empty($host) || empty($username) || empty($database)) {
                echo '<div class="error">❌ Error: Host, usuario y base de datos son obligatorios</div>';
            } else {
                try {
                    // IMPORTANTE: No usar ninguna constante predefinida
                    // Usar directamente las variables del POST
                    
                    // Suprimir warnings de mysqli para manejar errores manualmente
                    $conn = @new mysqli($host, $username, $password);
                    
                    // Verificar errores de conexión
                    if ($conn->connect_errno) {
                        $errorMsg = $conn->connect_error;
                        $errorCode = $conn->connect_errno;
                        
                        // Mensajes más amigables
                        if ($errorCode == 1045) {
                            $errorMsg = "Acceso denegado. Verifica que el usuario '$username' y la contraseña sean correctos.";
                        } elseif ($errorCode == 2002) {
                            $errorMsg = "No se puede conectar al servidor MySQL en '$host'. Verifica que MySQL esté corriendo.";
                        }
                        
                        throw new Exception($errorMsg);
                    }

                    // Crear base de datos
                    $sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    if (!$conn->query($sql)) {
                        throw new Exception("Error al crear base de datos: " . $conn->error);
                    }

                    // Seleccionar base de datos
                    if (!$conn->select_db($database)) {
                        throw new Exception("Error al seleccionar base de datos: " . $conn->error);
                    }

                    // Leer y ejecutar script SQL
                    $sqlFile = __DIR__ . '/database.sql';
                    
                    // Intentar múltiples paths posibles
                    $possiblePaths = [
                        __DIR__ . '/database.sql',
                        dirname(__DIR__) . '/install/database.sql',
                        $_SERVER['DOCUMENT_ROOT'] . '/install/database.sql',
                    ];
                    
                    $foundFile = null;
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $foundFile = $path;
                            break;
                        }
                    }
                    
                    if ($foundFile === null) {
                        $errorMsg = "Archivo database.sql no encontrado. Buscado en:\n";
                        foreach ($possiblePaths as $path) {
                            $errorMsg .= "  - " . $path . " (" . (file_exists($path) ? "EXISTE" : "NO EXISTE") . ")\n";
                        }
                        throw new Exception($errorMsg);
                    }
                    
                    $sqlFile = $foundFile;
                    
                    // Verificar que el archivo no esté vacío
                    $fileSize = filesize($sqlFile);
                    if ($fileSize == 0) {
                        throw new Exception("El archivo database.sql está vacío. Path: " . $sqlFile);
                    }
                    
                    // Debug: mostrar qué archivo se está usando
                    echo '<div class="info">📄 Usando archivo SQL: ' . htmlspecialchars($sqlFile) . ' (' . number_format($fileSize) . ' bytes)</div>';

                    $sql = file_get_contents($sqlFile);
                    
                    // Reemplazar el nombre de la base de datos en el SQL
                    // Reemplazar CREATE DATABASE IF NOT EXISTS frimango
                    $sql = preg_replace(
                        '/CREATE DATABASE IF NOT EXISTS\s+[`\']?[\w]+[`\']?/i',
                        "CREATE DATABASE IF NOT EXISTS `$database`",
                        $sql
                    );
                    
                    // Reemplazar USE frimango con el nombre de BD correcto
                    $sql = preg_replace(
                        '/USE\s+[`\']?[\w]+[`\']?/i',
                        "USE `$database`",
                        $sql
                    );
                    
                    // Dividir en múltiples queries - Método robusto
                    // Primero remover comentarios de bloque completos (/* ... */)
                    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                    
                    // Separar queries por punto y coma, pero respetando strings
                    // Este método es más confiable que procesar línea por línea
                    $queries = [];
                    $currentQuery = '';
                    $inString = false;
                    $stringChar = '';
                    $escaped = false;
                    
                    // Recorrer carácter por carácter para manejar strings correctamente
                    for ($i = 0; $i < strlen($sql); $i++) {
                        $char = $sql[$i];
                        
                        // Manejar escape
                        if ($escaped) {
                            $currentQuery .= $char;
                            $escaped = false;
                            continue;
                        }
                        
                        if ($char === '\\') {
                            $escaped = true;
                            $currentQuery .= $char;
                            continue;
                        }
                        
                        // Detectar inicio/fin de strings
                        if (($char === '"' || $char === "'") && !$inString) {
                            $inString = true;
                            $stringChar = $char;
                            $currentQuery .= $char;
                            continue;
                        }
                        
                        if ($char === $stringChar && $inString) {
                            $inString = false;
                            $stringChar = '';
                            $currentQuery .= $char;
                            continue;
                        }
                        
                        $currentQuery .= $char;
                        
                        // Si encontramos punto y coma fuera de strings, es fin de query
                        if ($char === ';' && !$inString) {
                            $query = trim($currentQuery);
                            
                            // Limpiar la query
                            // Remover comentarios de línea
                            $lines = explode("\n", $query);
                            $cleanedLines = [];
                            foreach ($lines as $line) {
                                $line = trim($line);
                                // Saltar líneas vacías y comentarios
                                if (empty($line) || preg_match('/^--/', $line)) {
                                    continue;
                                }
                                $cleanedLines[] = $line;
                            }
                            
                            $query = implode(' ', $cleanedLines);
                            $query = trim($query);
                            
                            // Normalizar espacios
                            $query = preg_replace('/\s+/', ' ', $query);
                            $query = trim($query);
                            
                            // Solo agregar si no está vacía y no es solo comentario
                            if (!empty($query) && !preg_match('/^--/', $query)) {
                                $queries[] = $query;
                            }
                            
                            $currentQuery = '';
                        }
                    }
                    
                    // Si queda una query sin terminar, agregarla
                    if (!empty(trim($currentQuery))) {
                        $query = trim($currentQuery);
                        $lines = explode("\n", $query);
                        $cleanedLines = [];
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line) || preg_match('/^--/', $line)) {
                                continue;
                            }
                            $cleanedLines[] = $line;
                        }
                        $query = implode(' ', $cleanedLines);
                        $query = preg_replace('/\s+/', ' ', $query);
                        $query = trim($query);
                        if (!empty($query) && !preg_match('/^--/', $query)) {
                            $queries[] = $query;
                        }
                    }
                    
                    // Limpiar y filtrar queries finales
                    $finalQueries = [];
                    foreach ($queries as $query) {
                        $query = trim($query);
                        
                        // Saltar queries vacías
                        if (empty($query)) {
                            continue;
                        }
                        
                        // Saltar queries que son solo comentarios
                        if (preg_match('/^--/', $query)) {
                            continue;
                        }
                        
                        // Normalizar espacios (redundante pero seguro)
                        $query = preg_replace('/\s+/', ' ', $query);
                        $query = trim($query);
                        
                        // Solo agregar si no está vacía después de limpiar
                        if (!empty($query)) {
                            $finalQueries[] = $query;
                        }
                    }
                    
                    $queries = $finalQueries;
                    
                    // Debug: mostrar cuántas queries se encontraron
                    $totalQueriesFound = count($queries);
                    
                    if ($totalQueriesFound == 0) {
                        // Intentar diagnóstico adicional
                        $sqlPreview = substr($sql, 0, 500);
                        $sqlLines = substr_count($sql, "\n");
                        $errorMsg = "Error: No se encontraron queries en el archivo SQL.\n";
                        $errorMsg .= "Archivo: " . $sqlFile . "\n";
                        $errorMsg .= "Tamaño: " . number_format(filesize($sqlFile)) . " bytes\n";
                        $errorMsg .= "Líneas: " . $sqlLines . "\n";
                        $errorMsg .= "Primeros 500 caracteres del archivo:\n" . htmlspecialchars($sqlPreview);
                        throw new Exception($errorMsg);
                    }
                    
                    // Mostrar preview de la primera query (para debug)
                    if ($totalQueriesFound > 0) {
                        $firstQueryPreview = substr($queries[0], 0, 150);
                        echo '<div class="info">ℹ️ Primera query detectada: ' . htmlspecialchars($firstQueryPreview) . '...</div>';
                    }

                    $errors = [];
                    $queriesExecuted = 0;
                    $queriesSkipped = 0;
                    
                    // Mostrar cuántas queries se encontraron
                    echo '<div class="info">ℹ️ Se encontraron ' . $totalQueriesFound . ' queries en el archivo SQL</div>';
                    
                    // Asegurar que estamos en la base de datos correcta antes de ejecutar queries
                    if (!$conn->select_db($database)) {
                        throw new Exception("Error al seleccionar base de datos antes de ejecutar queries: " . $conn->error);
                    }
                    
                    foreach ($queries as $index => $query) {
                        $query = trim($query);
                        if (empty($query)) {
                            continue;
                        }
                        
                        // Normalizar la query (remover espacios extras y saltos de línea)
                        $query = preg_replace('/\s+/', ' ', $query);
                        $query = trim($query);
                        
                        // Saltar CREATE DATABASE y USE ya que ya los ejecutamos
                        $queryUpper = strtoupper(trim($query));
                        if (strpos($queryUpper, 'CREATE DATABASE') === 0) {
                            $queriesSkipped++;
                            continue;
                        }
                        
                        if (strpos($queryUpper, 'USE ') === 0) {
                            $queriesSkipped++;
                            continue;
                        }
                        
                        if (strpos($queryUpper, 'SELECT ') === 0 && strpos($queryUpper, 'AS mensaje') !== false) {
                            $queriesSkipped++;
                            continue;
                        }
                        
                        // Asegurar que la query termine con punto y coma
                        if (!preg_match('/;\s*$/', $query)) {
                            $query .= ';';
                        }
                        
                        // Ejecutar query
                        $result = @$conn->query($query);
                        if ($result === false && $conn->errno != 0) {
                            // Solo reportar errores críticos
                            $errorMsg = $conn->error;
                            $isDuplicateError = (
                                strpos($errorMsg, 'already exists') !== false || 
                                strpos($errorMsg, 'Duplicate entry') !== false ||
                                strpos($errorMsg, 'Duplicate key') !== false ||
                                strpos($errorMsg, 'ON DUPLICATE KEY') !== false ||
                                strpos($errorMsg, 'Duplicate') !== false
                            );
                            
                            if (!$isDuplicateError) {
                                $errors[] = "Error en query #" . ($index + 1) . ": " . $errorMsg . " | Query: " . substr($query, 0, 150);
                            } else {
                                // Los errores de duplicado son normales (ON DUPLICATE KEY UPDATE), contar como ejecutado
                                $queriesExecuted++;
                            }
                        } else {
                            $queriesExecuted++;
                        }
                    }
                    
                    // Log de queries ejecutadas
                    if ($queriesExecuted > 0) {
                        echo '<div class="info">ℹ️ Se ejecutaron ' . $queriesExecuted . ' queries correctamente</div>';
                    }

                    if (!empty($errors)) {
                        echo '<div class="warning">⚠️ Advertencias durante la instalación:</div>';
                        foreach ($errors as $error) {
                            echo '<div class="code">' . htmlspecialchars($error) . '</div>';
                        }
                    }
                    
                    // Verificar inmediatamente después de ejecutar queries si hay tablas
                    $quickCheck = $conn->query("SHOW TABLES");
                    if ($quickCheck === false) {
                        echo '<div class="error">❌ Error al verificar tablas: ' . htmlspecialchars($conn->error) . '</div>';
                    } else {
                        $quickCount = $quickCheck->num_rows;
                        if ($quickCount == 0 && $queriesExecuted > 0) {
                            echo '<div class="warning">⚠️ Se ejecutaron ' . $queriesExecuted . ' queries pero no se detectaron tablas. Esto puede indicar un problema con las queries SQL.</div>';
                        }
                    }

                    // Crear archivo de configuración
                    $configContent = "<?php\n";
                    $configContent .= "/**\n";
                    $configContent .= " * Configuración de base de datos MySQL\n";
                    $configContent .= " * Generado automáticamente por install.php\n";
                    $configContent .= " */\n\n";
                    $configContent .= "// Si no existe, usar valores por defecto para XAMPP\n";
                    $configContent .= "if (!defined('DB_HOST')) {\n";
                    $configContent .= "    define('DB_HOST', " . var_export($host, true) . ");\n";
                    $configContent .= "    define('DB_USER', " . var_export($username, true) . ");\n";
                    $configContent .= "    define('DB_PASS', " . var_export($password, true) . ");\n";
                    $configContent .= "    define('DB_NAME', " . var_export($database, true) . ");\n";
                    $configContent .= "    define('DB_CHARSET', 'utf8mb4');\n";
                    $configContent .= "}\n";

                    $configFile = __DIR__ . '/../config/database.php';
                    $configDir = dirname($configFile);
                    
                    // Asegurar que el directorio existe
                    if (!is_dir($configDir)) {
                        if (!mkdir($configDir, 0755, true)) {
                            throw new Exception("No se pudo crear el directorio de configuración: " . $configDir);
                        }
                    }
                    
                    if (file_put_contents($configFile, $configContent) === false) {
                        throw new Exception("Error al crear archivo de configuración en: " . $configFile . ". Verifica permisos de escritura.");
                    }

                    // Asegurar que estamos en la base de datos correcta antes de verificar
                    if (!$conn->select_db($database)) {
                        throw new Exception("Error al seleccionar base de datos para verificación: " . $conn->error);
                    }
                    
                    // Verificar que las tablas se crearon correctamente
                    $tableCheck = $conn->query("SHOW TABLES");
                    if ($tableCheck === false) {
                        throw new Exception("Error al verificar tablas: " . $conn->error);
                    }
                    
                    $tableCount = $tableCheck ? $tableCheck->num_rows : 0;
                    
                    if ($tableCount == 0) {
                        // Intentar más información de diagnóstico
                        $dbCheck = $conn->query("SELECT DATABASE() as current_db");
                        $currentDb = $dbCheck ? $dbCheck->fetch_assoc()['current_db'] : 'desconocida';
                        
                        // Intentar ver si hay algún problema con la base de datos
                        $dbExistsCheck = $conn->query("SHOW DATABASES LIKE '" . $conn->real_escape_string($database) . "'");
                        $dbExists = $dbExistsCheck && $dbExistsCheck->num_rows > 0;
                        
                        $diagnosticMsg = "Error: No se crearon tablas. ";
                        $diagnosticMsg .= "Base de datos actual: " . $currentDb . ". ";
                        $diagnosticMsg .= "Base de datos existe: " . ($dbExists ? "Sí" : "No") . ". ";
                        $diagnosticMsg .= "Queries encontradas en SQL: " . $totalQueriesFound . ". ";
                        $diagnosticMsg .= "Queries ejecutadas: " . $queriesExecuted . ". ";
                        $diagnosticMsg .= "Queries saltadas: " . $queriesSkipped . ". ";
                        
                        if ($totalQueriesFound == 0) {
                            $diagnosticMsg .= "PROBLEMA: No se encontraron queries en el archivo SQL. Verifica que database.sql tenga contenido válido.";
                        } elseif ($queriesExecuted == 0 && $totalQueriesFound > 0) {
                            $diagnosticMsg .= "PROBLEMA: Se encontraron " . $totalQueriesFound . " queries pero ninguna se ejecutó. Verifica que las queries sean válidas y que MySQL esté corriendo.";
                        }
                        
                        $diagnosticMsg .= " Verifica el SQL y que las queries se ejecutaron correctamente.";
                        
                        throw new Exception($diagnosticMsg);
                    }
                    
                    // Mostrar tablas creadas
                    $tableNames = [];
                    while ($row = $tableCheck->fetch_array()) {
                        $tableNames[] = $row[0];
                    }
                    echo '<div class="success">✅ Se crearon ' . $tableCount . ' tablas correctamente: ' . implode(', ', $tableNames) . '</div>';
                    
                    // Crear/actualizar usuario admin con hash correcto
                    // Verificar primero que la tabla users existe
                    $tableExists = $conn->query("SHOW TABLES LIKE 'users'");
                    if ($tableExists && $tableExists->num_rows > 0) {
                        $adminEmail = 'admin@frimango.com';
                        $adminName = 'Administrador';
                        $adminPassword = 'admin123';
                        $adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
                        
                        // Verificar si existe columna role
                        $roleCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
                        $hasRole = ($roleCheck !== false && $roleCheck->num_rows > 0);
                        
                        // Verificar si existe
                        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                        if ($checkStmt !== false) {
                            $checkStmt->bind_param('s', $adminEmail);
                            $checkStmt->execute();
                            $checkResult = $checkStmt->get_result();
                            $checkStmt->close();
                            
                            if ($checkResult && $checkResult->num_rows > 0) {
                                // Actualizar contraseña y role si existe
                                if ($hasRole) {
                                    $updateStmt = $conn->prepare("UPDATE users SET password = ?, name = ?, role = 'admin' WHERE email = ?");
                                } else {
                                    $updateStmt = $conn->prepare("UPDATE users SET password = ?, name = ? WHERE email = ?");
                                }
                                if ($updateStmt !== false) {
                                    if ($hasRole) {
                                        $updateStmt->bind_param('sss', $adminHash, $adminName, $adminEmail);
                                    } else {
                                        $updateStmt->bind_param('sss', $adminHash, $adminName, $adminEmail);
                                    }
                                    if ($updateStmt->execute()) {
                                        echo '<div class="success">✅ Usuario admin actualizado correctamente</div>';
                                    }
                                    $updateStmt->close();
                                }
                            } else {
                                // Crear usuario
                                if ($hasRole) {
                                    $insertStmt = $conn->prepare("INSERT INTO users (email, name, password, role) VALUES (?, ?, ?, 'admin')");
                                } else {
                                    $insertStmt = $conn->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
                                }
                                if ($insertStmt !== false) {
                                    $insertStmt->bind_param('sss', $adminEmail, $adminName, $adminHash);
                                    if ($insertStmt->execute()) {
                                        echo '<div class="success">✅ Usuario admin creado correctamente</div>';
                                    }
                                    $insertStmt->close();
                                }
                            }
                        }
                    } else {
                        echo '<div class="error">❌ Error: La tabla users no se creó correctamente</div>';
                    }
                    
                    // Verificar productos insertados
                    $prodCheck = $conn->query("SELECT COUNT(*) as total FROM products");
                    if ($prodCheck) {
                        $prodData = $prodCheck->fetch_assoc();
                        $prodCount = $prodData['total'] ?? 0;
                        if ($prodCount > 0) {
                            echo '<div class="success">✅ ' . $prodCount . ' productos insertados correctamente</div>';
                        } else {
                            echo '<div class="warning">⚠️ No se insertaron productos. Puedes agregarlos manualmente desde el panel de administración.</div>';
                        }
                    }

                    echo '<div class="success">✅ Base de datos instalada correctamente!</div>';
                    echo '<div class="info">';
                    echo '<strong>Datos de conexión guardados:</strong><br>';
                    echo 'Host: ' . htmlspecialchars($host) . '<br>';
                    echo 'Base de datos: ' . htmlspecialchars($database) . '<br>';
                    echo 'Usuario: ' . htmlspecialchars($username) . '<br>';
                    echo '</div>';
                    echo '<div class="code">';
                    echo '<strong>Archivo de configuración:</strong><br>';
                    echo htmlspecialchars($configFile) . '<br><br>';
                    echo '<strong>Usuario administrador por defecto:</strong><br>';
                    echo 'Email: admin@frimango.com<br>';
                    echo 'Contraseña: admin123<br>';
                    echo '<small style="color: #ef4444;">⚠️ IMPORTANTE: Cambia esta contraseña después del primer inicio de sesión</small>';
                    echo '</div>';
                    echo '<a href="/"><button style="margin-top: 20px;">Ir al sitio web</button></a>';

                    $conn->close();

                } catch (Exception $e) {
                    echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<div class="info" style="margin-top: 20px;">';
                    echo '<strong>Sugerencias para solucionar el problema:</strong><br>';
                    echo '<ul style="margin-left: 20px; margin-top: 10px;">';
                    echo '<li>Verifica que MySQL esté corriendo en XAMPP</li>';
                    echo '<li>Confirma que el usuario y contraseña sean correctos</li>';
                    echo '<li>Por defecto en XAMPP: usuario "root" sin contraseña</li>';
                    echo '<li>Si cambiaste la contraseña de root, úsala aquí</li>';
                    echo '</ul>';
                    echo '</div>';
                }
            }
        } else {
            ?>
            <form method="POST">
                <div class="form-group">
                    <label>Host de MySQL:</label>
                    <input type="text" name="host" value="localhost" required>
                    <small>Normalmente es "localhost"</small>
                </div>
                
                <div class="form-group">
                    <label>Usuario de MySQL:</label>
                    <input type="text" name="username" value="root" required>
                    <small>Por defecto en XAMPP es "root"</small>
                </div>
                
                <div class="form-group">
                    <label>Contraseña de MySQL:</label>
                    <input type="password" name="password" value="" placeholder="(vacío por defecto en XAMPP)">
                    <small>Por defecto en XAMPP está vacía. Si cambiaste la contraseña de root, introdúcela aquí.</small>
                </div>
                
                <div class="form-group">
                    <label>Nombre de la base de datos:</label>
                    <input type="text" name="database" value="frimango" required>
                    <small>Se creará automáticamente si no existe</small>
                </div>
                
                <button type="submit">Instalar Base de Datos</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>
