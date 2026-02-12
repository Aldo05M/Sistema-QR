<?php
/**
 * =============================================
 * CONFIGURACIÓN DE LA BASE DE DATOS (EJEMPLO)
 * =============================================
 *
 * INSTRUCCIONES:
 * 1. Copia este archivo como backend/config.php
 * 2. Reemplaza los valores según tu servidor
 */

// =============================================
// CONFIGURACIÓN DE BASE DE DATOS
// =============================================
define('DB_HOST', 'localhost');       // Servidor de BD
define('DB_NAME', 'qr_boletos');      // Nombre de la base de datos
define('DB_USER', 'root');            // Usuario de MySQL
define('DB_PASS', '');                // Contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');      // Juego de caracteres

// =============================================
// CONFIGURACIÓN DEL SISTEMA
// =============================================
define('BASE_URL', 'http://localhost/SISTEMA DE QR/');  // URL base del sistema
define('QR_IMAGES_PATH', __DIR__ . '/../qr_images/');    // Ruta física para guardar QR
define('QR_IMAGES_URL', BASE_URL . 'qr_images/');        // URL pública de las imágenes QR

// =============================================
// ZONA HORARIA
// =============================================
date_default_timezone_set('America/Mexico_City');  // Ajusta según tu ubicación

// =============================================
// CONFIGURACIÓN DE ERRORES (desarrollo)
// =============================================
// Cambiar a 0 en producción
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =============================================
// HEADERS DE SEGURIDAD
// =============================================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

?>
