<?php
require_once __DIR__ . '/../../config/config.php';

$code = $_GET['code'] ?? null;

if (!$code) {
    header('Location: /login?error=' . urlencode('Error en autenticación de Facebook'));
    exit;
}

try {
    $userData = OAuth::handleFacebookCallback($code);
    if ($userData && Auth::loginOAuth(
        $userData['provider'],
        $userData['oauth_id'],
        $userData['email'],
        $userData['name'],
        $userData['avatar']
    )) {
        header('Location: /');
    } else {
        header('Location: /login?error=' . urlencode('Error al iniciar sesión con Facebook'));
    }
} catch (Exception $e) {
    header('Location: /login?error=' . urlencode($e->getMessage()));
}

exit;


