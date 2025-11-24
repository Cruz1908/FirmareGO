<?php
require_once __DIR__ . '/config/config.php';

echo "=== Verificación de Usuario Admin ===\n\n";

$db = Database::getInstance();
if (!$db->isConnected()) {
    echo "Error: No hay conexión a la base de datos\n";
    exit(1);
}

$conn = $db->getConnection();
$conn->select_db(DB_NAME);

// Verificar si existe el usuario admin
echo "1. Verificando usuario admin en la base de datos...\n";
$stmt = $conn->prepare("SELECT id, email, name, password FROM users WHERE email = 'admin@frimango.com'");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "   ✗ Usuario admin NO existe en la base de datos\n";
    echo "\n2. Creando usuario admin...\n";
    
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
    $email = 'admin@frimango.com';
    $name = 'Administrador';
    $stmt->bind_param('sss', $email, $name, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "   ✓ Usuario admin creado correctamente\n";
        echo "   Email: admin@frimango.com\n";
        echo "   Contraseña: admin123\n";
    } else {
        echo "   ✗ Error al crear usuario: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "   ✓ Usuario admin existe\n";
    echo "   ID: " . $user['id'] . "\n";
    echo "   Email: " . $user['email'] . "\n";
    echo "   Nombre: " . $user['name'] . "\n";
    
    echo "\n2. Verificando contraseña...\n";
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user['password'])) {
        echo "   ✓ La contraseña 'admin123' es correcta\n";
    } else {
        echo "   ✗ La contraseña almacenada NO coincide con 'admin123'\n";
        echo "   Actualizando contraseña...\n";
        
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@frimango.com'");
        $stmt->bind_param('s', $newHash);
        
        if ($stmt->execute()) {
            echo "   ✓ Contraseña actualizada correctamente\n";
        } else {
            echo "   ✗ Error al actualizar: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
}

echo "\n3. Probando login...\n";
if (Auth::login('admin@frimango.com', 'admin123')) {
    echo "   ✓ Login exitoso\n";
    $currentUser = Auth::getCurrentUser();
    echo "   Usuario logueado: " . $currentUser['name'] . "\n";
} else {
    echo "   ✗ Login fallido\n";
}

