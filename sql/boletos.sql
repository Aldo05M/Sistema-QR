-- =============================================
-- BASE DE DATOS: Sistema de Validación QR
-- Autor: Sistema de Boletos QR
-- Fecha: Enero 2026
-- =============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS qr_boletos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE qr_boletos;

-- =============================================
-- TABLA: usuarios
-- Almacena usuarios del sistema con permisos
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'scanner') NOT NULL DEFAULT 'scanner',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    
    INDEX idx_username (username),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuarios del sistema
-- Admin: Oswaldo / Meneses12
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES
('Oswaldo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oswaldo (Administrador)', 'admin');
-- Nota: La contraseña es 'Meneses12' hasheada

-- Usuarios escáneres (yo123 / yo123, yo1234 / yo1234, yo12345 / yo12345, yo123456 / yo123456)
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES
('yo123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Scanner 1', 'scanner'),
('yo1234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Scanner 2', 'scanner'),
('yo12345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Scanner 3', 'scanner'),
('yo123456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Scanner 4', 'scanner');
-- Nota: Todas las contraseñas están hasheadas, se verificarán en login.php

-- =============================================
-- TABLA: boletos
-- Almacena todos los boletos generados
-- =============================================
CREATE TABLE IF NOT EXISTS boletos (
    -- ID interno autoincremental
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Token único que va en el QR (16-32 caracteres)
    token VARCHAR(64) NOT NULL UNIQUE,
    
    -- Estado del boleto
    estado ENUM('NO_USADO', 'USADO') NOT NULL DEFAULT 'NO_USADO',
    
    -- Tipo de boleto
    tipo ENUM('GENERAL', 'VIP') NOT NULL DEFAULT 'GENERAL',
    
    -- Folio visible para el usuario (ej: BOL-001)
    folio VARCHAR(20) NOT NULL,
    
    -- Fecha y hora de creación del boleto
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Fecha y hora de uso (cuando se escanea)
    fecha_uso DATETIME NULL,
    
    -- Índices para optimizar búsquedas
    INDEX idx_token (token),
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DATOS DE PRUEBA (5 boletos de ejemplo)
-- =============================================

-- Boleto 1: No usado - GENERAL
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('A1B2C3D4E5F6G7H8', 'NO_USADO', 'GENERAL', 'BOL-001', NOW(), NULL);

-- Boleto 2: No usado - VIP
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('X9Y8Z7W6V5U4T3S2', 'NO_USADO', 'VIP', 'VIP-001', NOW(), NULL);

-- Boleto 3: Ya usado - GENERAL
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('M1N2O3P4Q5R6S7T8', 'USADO', 'GENERAL', 'BOL-002', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW());

-- Boleto 4: No usado - GENERAL
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('K9L8M7N6O5P4Q3R2', 'NO_USADO', 'GENERAL', 'BOL-003', NOW(), NULL);

-- Boleto 5: No usado - VIP
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('J1K2L3M4N5O6P7Q8', 'NO_USADO', 'VIP', 'VIP-002', NOW(), NULL);

-- =============================================
-- CONSULTAS ÚTILES
-- =============================================

-- Ver todos los boletos
-- SELECT * FROM boletos ORDER BY fecha_creacion DESC;

-- Ver boletos no usados
-- SELECT * FROM boletos WHERE estado = 'NO_USADO';

-- Ver boletos usados
-- SELECT * FROM boletos WHERE estado = 'USADO';

-- Contar boletos por estado
-- SELECT estado, COUNT(*) as total FROM boletos GROUP BY estado;

-- Estadísticas por tipo
-- SELECT tipo, estado, COUNT(*) as total FROM boletos GROUP BY tipo, estado;
