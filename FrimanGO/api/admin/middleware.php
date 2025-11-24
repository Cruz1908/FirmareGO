<?php
/**
 * Middleware para verificar autenticación de admin
 */
function requireAdminAuth() {
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }
    
    // En producción, verificar si el usuario es admin
    // Por ahora, cualquier usuario autenticado puede ser admin
    return true;
}

