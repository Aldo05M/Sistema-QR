<?php
/**
 * =============================================
 * RESETEO DE BOLETOS (SOLO ADMIN)
 * =============================================
 * 
 * Este endpoint permite resetear boletos o eliminarlos
 * REQUIERE CONTRASEÑA DE SEGURIDAD
 * 
 * MÉTODO: POST
 * PARÁMETROS: 
 *   - accion: 'reset_usados' | 'eliminar_todo'
 *   - password: contraseña de admin
 * 
 * RESPUESTA: JSON con resultado de la operación
 */

// Iniciar buffer de salida
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

// Contraseña de seguridad (en producción, usar hash)
define('RESET_PASSWORD', 'oswaldo');

/**
 * Verificar que sea POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit;
}

/**
 * Obtener datos
 */
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

$accion = isset($data['accion']) ? trim($data['accion']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

// Validar contraseña
if ($password !== RESET_PASSWORD) {
    ob_clean();
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Contraseña incorrecta'
    ]);
    exit;
}

/**
 * Ejecutar acción
 */
try {
    $pdo = getDBConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($accion === 'reset_usados') {
        // Resetear todos los boletos USADOS a NO_USADO
        $stmt = $pdo->prepare("
            UPDATE boletos 
            SET estado = 'NO_USADO', 
                fecha_uso = NULL 
            WHERE estado = 'USADO'
        ");
        $stmt->execute();
        $affected = $stmt->rowCount();
        
        ob_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensaje' => "Se resetearon $affected boletos",
            'boletos_reseteados' => $affected
        ]);
        
    } elseif ($accion === 'eliminar_todo') {
        // Eliminar TODOS los boletos
        $stmt = $pdo->prepare("DELETE FROM boletos");
        $stmt->execute();
        $affected = $stmt->rowCount();
        
        ob_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensaje' => "Se eliminaron $affected boletos",
            'boletos_eliminados' => $affected
        ]);
        
    } else {
        ob_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Acción no válida. Usar: reset_usados o eliminar_todo'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error en reseteo: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al ejecutar la operación'
    ]);
}
?>
