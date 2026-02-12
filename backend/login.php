<?php
/**
 * =============================================
 * PROCESADOR DE LOGIN
 * =============================================
 */

ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

session_start();

require_once 'auth.php';

ob_clean();

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_SLASHES);
    exit;
}

// Obtener datos
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// Validar datos
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'error' => 'Usuario y contraseña requeridos'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Verificar credenciales
$usuario = verificarLogin($username, $password);

if ($usuario) {
    // Guardar en sesión
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_username'] = $usuario['username'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['login_time'] = time();
    
    // Determinar página de redirección
    $redirect = ($usuario['rol'] === 'admin') ? 'dashboard.php' : 'frontend/scan.html';
    
    echo json_encode([
        'success' => true,
        'mensaje' => 'Bienvenido ' . $usuario['nombre'],
        'redirect' => $redirect,
        'rol' => $usuario['rol']
    ], JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Usuario o contraseña incorrectos'
    ], JSON_UNESCAPED_SLASHES);
}

?>
