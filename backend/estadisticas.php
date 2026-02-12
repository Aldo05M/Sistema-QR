<?php
/**
 * =============================================
 * API DE ESTADÍSTICAS
 * =============================================
 * 
 * Devuelve estadísticas del sistema
 */

// Iniciar buffer de salida
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

// Limpiar output previo
ob_clean();

try {
    $stats = getEstadisticas();
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ], JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
    
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener estadísticas'
    ], JSON_UNESCAPED_SLASHES);
}

?>
