<?php
/**
 * =============================================
 * CONEXIÓN A BASE DE DATOS
 * =============================================
 * 
 * Este archivo maneja la conexión a MySQL usando PDO
 * PDO es más seguro que mysqli y previene inyecciones SQL
 */

require_once 'config.php';

/**
 * Obtener conexión a la base de datos
 * 
 * @return PDO Conexión activa a la base de datos
 * @throws PDOException Si falla la conexión
 */
function getDBConnection() {
    try {
        // Detectar si estamos en App Engine (Google Cloud)
        if (getenv('GAE_ENV')) {
            // En App Engine, usar Unix Socket
            $dsn = "mysql:unix_socket=/cloudsql/sistemas-qr-disco:us-central1:qr-database;dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        } else {
            // En local o servidor externo, usar host normal
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        }
        
        // Opciones de PDO para mejor seguridad y manejo de errores
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanzar excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retornar arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                    // Usar prepared statements reales
        ];
        
        // Crear conexión
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // En producción, NO mostrar el mensaje de error real
        error_log("Error de conexión a BD: " . $e->getMessage());
        die(json_encode([
            'success' => false,
            'error' => 'Error de conexión a la base de datos. Contacta al administrador.'
        ]));
    }
}

/**
 * Verificar si un token ya existe en la base de datos
 * 
 * @param string $token Token a verificar
 * @return bool True si existe, False si no existe
 */
function tokenExists($token) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM boletos WHERE token = ?");
    $stmt->execute([$token]);
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Generar un token único y seguro
 * 
 * @param int $length Longitud del token (por defecto 16)
 * @return string Token único
 */
function generateUniqueToken($length = 16) {
    $maxAttempts = 100;  // Evitar bucle infinito
    $attempts = 0;
    
    do {
        // Generar token aleatorio con caracteres alfanuméricos
        $token = bin2hex(random_bytes($length / 2));
        $token = strtoupper($token);
        $attempts++;
        
        // Si después de 100 intentos sigue habiendo colisión, algo está mal
        if ($attempts >= $maxAttempts) {
            throw new Exception("No se pudo generar un token único después de $maxAttempts intentos");
        }
        
    } while (tokenExists($token));  // Repetir si el token ya existe
    
    return $token;
}

/**
 * Insertar un nuevo boleto en la base de datos
 * 
 * @param string $token Token único
 * @param string $tipo Tipo de boleto (GENERAL o VIP)
 * @param string $folio Folio del boleto
 * @return int ID del boleto insertado
 */
function insertBoleto($token, $tipo, $folio) {
    $db = getDBConnection();
    
    $stmt = $db->prepare("
        INSERT INTO boletos (token, tipo, folio, estado, fecha_creacion) 
        VALUES (?, ?, ?, 'NO_USADO', NOW())
    ");
    
    $stmt->execute([$token, $tipo, $folio]);
    
    return $db->lastInsertId();
}

/**
 * Validar un boleto y marcarlo como usado si es válido
 * 
 * @param string $token Token del boleto a validar
 * @return array Resultado de la validación
 */
function validarBoleto($token) {
    $db = getDBConnection();
    
    // Buscar el boleto
    $stmt = $db->prepare("SELECT id, estado, tipo, folio FROM boletos WHERE token = ?");
    $stmt->execute([$token]);
    $boleto = $stmt->fetch();
    
    // Token no existe
    if (!$boleto) {
        return [
            'valido' => false,
            'mensaje' => 'INVÁLIDO',
            'codigo' => 'TOKEN_NO_EXISTE'
        ];
    }
    
    // Boleto ya fue usado
    if ($boleto['estado'] === 'USADO') {
        return [
            'valido' => false,
            'mensaje' => 'YA USADO',
            'codigo' => 'YA_USADO',
            'folio' => $boleto['folio'],
            'tipo' => $boleto['tipo']
        ];
    }
    
    // Boleto válido - Marcarlo como usado
    $stmt = $db->prepare("UPDATE boletos SET estado = 'USADO', fecha_uso = NOW() WHERE id = ?");
    $stmt->execute([$boleto['id']]);
    
    return [
        'valido' => true,
        'mensaje' => 'VÁLIDO',
        'codigo' => 'VALIDO',
        'folio' => $boleto['folio'],
        'tipo' => $boleto['tipo']
    ];
}

/**
 * Obtener estadísticas de boletos
 * 
 * @return array Estadísticas
 */
function getEstadisticas() {
    $db = getDBConnection();
    
    $stats = [];
    
    // Total de boletos
    $stmt = $db->query("SELECT COUNT(*) FROM boletos");
    $stats['total'] = $stmt->fetchColumn();
    
    // Boletos usados
    $stmt = $db->query("SELECT COUNT(*) FROM boletos WHERE estado = 'USADO'");
    $stats['usados'] = $stmt->fetchColumn();
    
    // Boletos disponibles
    $stmt = $db->query("SELECT COUNT(*) FROM boletos WHERE estado = 'NO_USADO'");
    $stats['disponibles'] = $stmt->fetchColumn();
    
    // Por tipo
    $stmt = $db->query("SELECT tipo, COUNT(*) as total FROM boletos GROUP BY tipo");
    $stats['por_tipo'] = $stmt->fetchAll();
    
    return $stats;
}

?>
