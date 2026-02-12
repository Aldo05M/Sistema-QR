<?php
/**
 * =============================================
 * GENERAR IMAGEN JPG CON QR Y FOLIO
 * =============================================
 * 
 * Este endpoint genera una imagen JPG con:
 * - Código QR arriba
 * - Folio del boleto abajo
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['token']) || !isset($data['folio'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Token y folio requeridos']);
    exit;
}

$token = $data['token'];
$folio = $data['folio'];

try {
    // Obtener imagen QR de la API
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($token);
    $qrImageData = @file_get_contents($qrUrl);
    
    if (!$qrImageData) {
        throw new Exception("No se pudo obtener la imagen QR");
    }
    
    // Crear imagen desde los datos
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
    
    // Copiar QR en la parte superior
    imagecopy($finalImage, $qrImage, 0, 0, 0, 0, $qrWidth, $qrHeight);
    
    // Añadir texto del folio
    $fontSize = 32;
    $fontFile = __DIR__ . '/../fonts/arial.ttf'; // Ruta a fuente (opcional)
    
    // Si no hay fuente TrueType, usar fuente integrada
    if (!file_exists($fontFile)) {
        // Usar fuente integrada (más simple)
        $text = "FOLIO: " . $folio;
        $textWidth = imagefontwidth(5) * strlen($text);
        $x = ($qrWidth - $textWidth) / 2;
        $y = $qrHeight + 25;
        imagestring($finalImage, 5, $x, $y, $text, $black);
    } else {
        // Usar TrueType
        $text = "FOLIO: " . $folio;
        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = ($qrWidth - $textWidth) / 2;
        $y = $qrHeight + 50;
        imagettftext($finalImage, $fontSize, 0, $x, $y, $black, $fontFile, $text);
    }
    
    // Guardar temporalmente
    $filename = 'qr_' . $folio . '_' . time() . '.jpg';
    $filepath = __DIR__ . '/../qr_images/' . $filename;
    
    // Crear directorio si no existe
    if (!is_dir(__DIR__ . '/../qr_images/')) {
        mkdir(__DIR__ . '/../qr_images/', 0755, true);
    }
    
    // Guardar como JPG
    imagejpeg($finalImage, $filepath, 90);
    
    // Limpiar memoria
    imagedestroy($qrImage);
    imagedestroy($finalImage);
    
    // Retornar URL de la imagen
    $imageUrl = '/qr_images/' . $filename;
    
    echo json_encode([
        'success' => true,
        'image_url' => $imageUrl,
        'filename' => $filename
    ], JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
