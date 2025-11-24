<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register');
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    header('Location: /register?error=' . urlencode('Todos los campos son obligatorios'));
    exit;
}

try {
    $userId = Auth::register($email, $name, $password);
    // Auto-login después del registro
    Auth::login($email, $password);
    header('Location: /');
} catch (Exception $e) {
    header('Location: /register?error=' . urlencode($e->getMessage()));
}

exit;

