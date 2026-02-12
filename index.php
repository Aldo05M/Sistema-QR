<?php
/**
 * Front Controller para App Engine
 * Este archivo maneja todas las peticiones
 */

// Obtener la URI solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remover query string
$path = parse_url($request_uri, PHP_URL_PATH);

// Si es la raíz, mostrar index.html
if ($path === '/' || $path === '/index.html' || $path === '/index.php') {
    readfile(__DIR__ . '/index.html');
    exit;
}

// Si es un archivo PHP, ejecutarlo
if (preg_match('/\.php$/', $path)) {
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Si es un archivo estático (HTML, CSS, JS, etc), servirlo
$file = __DIR__ . $path;
if (file_exists($file) && is_file($file)) {
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    
    // Tipos MIME comunes
    $mime_types = [
        'html' => 'text/html',
        'htm' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
    ];
    
    $mime = $mime_types[$extension] ?? 'application/octet-stream';
    header('Content-Type: ' . $mime);
    readfile($file);
    exit;
}

// Si no existe, mostrar 404
http_response_code(404);
echo '404 - Página no encontrada';
?>
