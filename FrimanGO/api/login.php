<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login');
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (Auth::login($email, $password)) {
    $redirect = $_GET['redirect'] ?? '/';
    header('Location: ' . $redirect);
} else {
    header('Location: /login?error=' . urlencode('Email o contraseña incorrectos'));
}

exit;

