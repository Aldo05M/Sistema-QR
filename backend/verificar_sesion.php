<?php
/**
 * =============================================
 * VERIFICAR SESIÓN
 * =============================================
 */

ob_start();

header('Content-Type: application/json; charset=utf-8');

session_start();

require_once 'auth.php';

ob_clean();

if (estaLogueado()) {
    $usuario = getUsuarioActual();
    echo json_encode([
        'logueado' => true,
        'usuario' => $usuario
    ], JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        'logueado' => false
    ], JSON_UNESCAPED_SLASHES);
}

?>
