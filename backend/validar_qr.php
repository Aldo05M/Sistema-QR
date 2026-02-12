<?php
/**
 * =============================================
 * VALIDADOR DE CÓDIGOS QR
 * =============================================
 * 
 * Este endpoint valida un boleto escaneado
 * Si el boleto es válido y NO_USADO, lo marca como USADO
 * 
 * MÉTODO: POST
 * PARÁMETROS: token (string) - El código del QR escaneado
 * RESPUESTA: JSON con estado del boleto
 */

// Iniciar buffer de salida para capturar cualquier output inesperado
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

/**
 * Verificar que sea una petición POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido. Usar POST.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Obtener el token del cuerpo de la petición
 */
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// También soportar datos de formulario
if (!$data || !is_array($data)) {
    $data = $_POST;
}

// Validar que se envió el token
if (!isset($data['token']) || empty(trim($data['token']))) {
    ob_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Token no proporcionado'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$token = trim($data['token']);

// Limpiar el token (remover espacios, saltos de línea, etc)
$token = preg_replace('/\s+/', '', $token);

// Si el token es una URL, extraer solo el token
if (strpos($token, 'http') === 0) {
    $parts = explode('/', $token);
    $token = end($parts);
}

/**
 * Validar el boleto
 */
try {
    $resultado = validarBoleto($token);
    
    // Registrar en log (útil para auditoría)
    error_log(sprintf(
        "[%s] Validación: Token=%s, Resultado=%s",
        date('Y-m-d H:i:s'),
        substr($token, 0, 8) . '...',  // Solo mostrar primeros 8 chars por seguridad
        $resultado['codigo']
    ));
    
    // Limpiar buffer y responder según el resultado
    ob_clean();
    
    if ($resultado['valido']) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensaje' => $resultado['mensaje'],
            'codigo' => $resultado['codigo'],
            'tipo' => $resultado['tipo'] ?? null,
            'folio' => $resultado['folio'] ?? null,
            'fecha_uso' => $resultado['fecha_uso'] ?? null
        ], JSON_UNESCAPED_SLASHES);
    } else {
        http_response_code(200);  // 200 aunque sea inválido (el cliente decide qué mostrar)
        echo json_encode([
            'success' => false,
            'mensaje' => $resultado['mensaje'],
            'codigo' => $resultado['codigo'],
            'tipo' => $resultado['tipo'] ?? null,
            'folio' => $resultado['folio'] ?? null
        ], JSON_UNESCAPED_SLASHES);
    }
    
} catch (Exception $e) {
    error_log("Error en validación de QR: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor',
        'mensaje' => 'ERROR'
    ], JSON_UNESCAPED_SLASHES);
}
?>
