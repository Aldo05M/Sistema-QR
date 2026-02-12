<?php
/**
 * =============================================
 * SISTEMA DE AUTENTICACIÓN
 * =============================================
 */

// Iniciar sesión
session_start();

require_once 'config.php';
require_once 'db.php';

/**
 * Verificar credenciales de usuario
 */
function verificarLogin($username, $password) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT id, username, password, nombre_completo, rol FROM usuarios WHERE username = ? AND activo = 1");
    $stmt->execute([$username]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        return false;
    }
    
    // Verificar contraseña (las específicas del sistema)
    $passwordsValidos = [
        'Oswaldo' => 'Meneses12',
        'yo123' => 'yo123',
        'yo1234' => 'yo1234',
        'yo12345' => 'yo12345',
        'yo123456' => 'yo123456'
    ];
    
    // Verificar contraseña directa o hash
    $passwordCorrecto = false;
    if (isset($passwordsValidos[$username]) && $password === $passwordsValidos[$username]) {
        $passwordCorrecto = true;
    } elseif (password_verify($password, $usuario['password'])) {
        $passwordCorrecto = true;
    }
    
    if ($passwordCorrecto) {
        // Actualizar último acceso
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        
        return [
            'id' => $usuario['id'],
            'username' => $usuario['username'],
            'nombre' => $usuario['nombre_completo'],
            'rol' => $usuario['rol']
        ];
    }
    
    return false;
}

/**
 * Verificar si el usuario está logueado
 */
function estaLogueado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Verificar si el usuario es admin
 */
function esAdmin() {
    return estaLogueado() && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Obtener información del usuario actual
 */
function getUsuarioActual() {
    if (!estaLogueado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'username' => $_SESSION['usuario_username'],
        'nombre' => $_SESSION['usuario_nombre'],
        'rol' => $_SESSION['usuario_rol']
    ];
}

/**
 * Cerrar sesión
 */
function cerrarSesion() {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

/**
 * Requerir login
 */
function requireLogin() {
    if (!estaLogueado()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

/**
 * Requerir admin
 */
function requireAdmin() {
    requireLogin();
    if (!esAdmin()) {
        die('Acceso denegado. Solo administradores.');
    }
}

?>
