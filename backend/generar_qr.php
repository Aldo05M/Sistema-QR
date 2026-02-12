<?php
/**
 * =============================================
 * GENERADOR DE CÓDIGOS QR
 * =============================================
 * 
 * Este endpoint genera un boleto nuevo con QR
 * 
 * MÉTODO: POST
 * PARÁMETROS: tipo (GENERAL|VIP), prefijo (opcional)
 * RESPUESTA: JSON con datos del boleto y URL del QR
 */

// Iniciar buffer de salida para capturar cualquier output inesperado
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener datos
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Si no hay datos JSON, intentar POST tradicional
if ($data === null) {
    $data = $_POST;
}

$tipo = isset($data['tipo']) ? strtoupper(trim($data['tipo'])) : 'GENERAL';
$prefijo = isset($data['prefijo']) ? trim($data['prefijo']) : ($tipo === 'VIP' ? 'VIP' : 'BOL');

// Validar tipo
if (!in_array($tipo, ['GENERAL', 'VIP'])) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de boleto inválido']);
    exit;
}

try {
    // Generar token único
    $token = generateUniqueToken();
    
    // Generar folio único
    $folio = $prefijo . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Insertar en base de datos
    $boletoId = insertBoleto($token, $tipo, $folio);
    
    if (!$boletoId) {
        throw new Exception("No se pudo insertar el boleto en la base de datos");
    }
    
    // Generar imagen JPG con QR y folio
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&format=jpg&data=" . urlencode($token);
    $qrImageData = @file_get_contents($qrUrl);
    
    if (!$qrImageData) {
        throw new Exception("No se pudo generar el código QR");
    }
    
    // Crear imagen con GD
    $qrImage = imagecreatefromstring($qrImageData);
    
    if (!$qrImage) {
        throw new Exception("No se pudo crear la imagen del QR");
    }
    
    // Dimensiones
    $qrWidth = imagesx($qrImage);
    $qrHeight = imagesy($qrImage);
    $textHeight = 80;
    $finalHeight = $qrHeight + $textHeight;
    
    // Crear imagen final
    $finalImage = imagecreatetruecolor($qrWidth, $finalHeight);
    
    // Fondo blanco
    $white = imagecolorallocate($finalImage, 255, 255, 255);
    $black = imagecolorallocate($finalImage, 0, 0, 0);
    imagefilledrectangle($finalImage, 0, 0, $qrWidth, $finalHeight, $white);
    
    // Copiar QR
    imagecopy($finalImage, $qrImage, 0, 0, 0, 0, $qrWidth, $qrHeight);
    
    // Añadir texto del folio
    $text = "FOLIO: " . $folio;
    $textWidth = imagefontwidth(5) * strlen($text);
    $x = ($qrWidth - $textWidth) / 2;
    $y = $qrHeight + 25;
    imagestring($finalImage, 5, $x, $y, $text, $black);
    
    // Convertir imagen a base64
    ob_start();
    imagejpeg($finalImage, null, 90);
    $imageData = ob_get_contents();
    ob_end_clean();
    
    $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
    
    // Limpiar memoria
    imagedestroy($qrImage);
    imagedestroy($finalImage);
    
    // Limpiar buffer y devolver respuesta exitosa
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'boleto' => [
            'id' => $boletoId,
            'token' => $token,
            'tipo' => $tipo,
            'folio' => $folio,
            'estado' => 'NO_USADO',
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'qr_url' => $base64Image
        ]
    ], JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    error_log("Error generando QR: " . $e->getMessage());
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_SLASHES);
}
?>
