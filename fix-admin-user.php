<?php
/**
 * Script para crear/corregir el usuario admin
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Corregir Usuario Admin</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f6;}";
echo ".ok{color:green;font-weight:bold;background:#d4edda;padding:15px;border-radius:8px;margin:10px 0;}";
echo ".error{color:red;font-weight:bold;background:#f8d7da;padding:15px;border-radius:8px;margin:10px 0;}";
echo ".box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "</style></head><body><h1>🔧 Corregir Usuario Admin</h1>";

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo "<div class='box'><p class='error'>✗ Error de conexión a la base de datos</p></div>";
        echo "</body></html>";
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->select_db(DB_NAME);
    
    echo "<div class='box'>";
    echo "<h2>1. Verificando usuario admin</h2>";
    
    $stmt = $conn->prepare("SELECT id, email, name, password FROM users WHERE email = ?");
    $email = 'admin@frimango.com';
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user) {
        echo "<p>✓ Usuario admin encontrado (ID: " . $user['id'] . ")</p>";
        
        // Verificar contraseña
        $testPassword = 'admin123';
        if (password_verify($testPassword, $user['password'])) {
            echo "<p class='ok'>✓ La contraseña es correcta</p>";
        } else {
            echo "<p class='error'>✗ La contraseña NO es correcta. Actualizando...</p>";
            
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $newHash, $email);
            
            if ($stmt->execute()) {
                echo "<p class='ok'>✓ Contraseña actualizada correctamente</p>";
            } else {
                echo "<p class='error'>✗ Error al actualizar: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    } else {
        echo "<p class='error'>✗ Usuario admin NO existe. Creando...</p>";
        
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $name = 'Administrador';
        
        $stmt = $conn->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $email, $name, $hashedPassword);
        
        if ($stmt->execute()) {
            echo "<p class='ok'>✓ Usuario admin creado correctamente</p>";
            echo "<p>Email: admin@frimango.com</p>";
            echo "<p>Contraseña: admin123</p>";
        } else {
            echo "<p class='error'>✗ Error al crear: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>2. Probando login</h2>";
    
    if (Auth::login('admin@frimango.com', 'admin123')) {
        echo "<p class='ok'>✓ Login exitoso</p>";
        $currentUser = Auth::getCurrentUser();
        echo "<p>Usuario logueado: " . htmlspecialchars($currentUser['name']) . "</p>";
        echo "<p><a href='/admin' style='display:inline-block;padding:12px 24px;background:#FFD200;color:#111827;text-decoration:none;border-radius:8px;font-weight:600;margin-top:10px;'>Ir al Panel Admin</a></p>";
    } else {
        echo "<p class='error'>✗ Login fallido</p>";
        echo "<p>Por favor, ejecuta este script nuevamente o verifica manualmente en phpMyAdmin.</p>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>3. Credenciales</h2>";
    echo "<p><strong>Email:</strong> admin@frimango.com</p>";
    echo "<p><strong>Contraseña:</strong> admin123</p>";
    echo "<p><a href='/admin/login'>Ir al login de admin</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='box'>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</body></html>";

