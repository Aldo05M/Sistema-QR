# 🚀 INSTALACIÓN DIRECTA EN GOOGLE CLOUD (SIN XAMPP)

## 🎯 Flujo de Trabajo

Como NO tienes XAMPP local, vas a crear todo directamente en Google Cloud:

1. ✅ Crear cuenta Google Cloud
2. ✅ Crear base de datos MySQL en Cloud SQL
3. ✅ Configurar el proyecto
4. ✅ Desplegar el sistema
5. ✅ Probar desde internet

---

## 📋 PASO 1: Crear Cuenta Google Cloud

1. Ir a: https://console.cloud.google.com/
2. Crear cuenta (necesitas tarjeta, pero hay **$300 USD gratis** por 90 días)
3. Crear un nuevo proyecto:
   - Nombre: `disco-qr-system`
   - Anotar el Project ID

---

## 💾 PASO 2: Crear Base de Datos MySQL

### 2.1 Crear Instancia Cloud SQL

1. En Google Cloud Console, ir a: **SQL** (menú lateral)
2. Clic en **"Crear instancia"**
3. Seleccionar **MySQL**
4. Configuración:

```
Nombre de la instancia: qr-database
Contraseña root: [Elegir una contraseña segura, ej: TuContraseña123!]
Región: us-central1 (o la más cercana a México)
Zona: us-central1-a
Tipo de máquina: db-f1-micro (gratis/económico)
Almacenamiento: 10 GB
```

5. En **"Conexiones"**:
   - Habilitar **"IP pública"**
   - Agregar red autorizada: `0.0.0.0/0` (temporal para configurar)
   - ⚠️ Más tarde limitarás esto por seguridad

6. Clic en **"Crear instancia"** (tarda 5-10 minutos)

### 2.2 Conectarse a la Base de Datos

Opción A: **Desde Cloud Shell (Recomendado)**

1. En Cloud Console, clic en el ícono de terminal (arriba derecha)
2. Ejecutar:

```bash
gcloud sql connect qr-database --user=root --quiet
```

3. Ingresar la contraseña root que elegiste
4. Ahora estás en MySQL, ejecuta:

```sql
CREATE DATABASE qr_boletos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qr_boletos;
```

### 2.3 Crear las Tablas

Copia y pega esto línea por línea en Cloud Shell MySQL:

```sql
-- Tabla de usuarios
CREATE TABLE usuarios (
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

-- Insertar usuarios
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES
('Oswaldo', 'Meneses12', 'Oswaldo (Administrador)', 'admin'),
('yo123', 'yo123', 'Scanner 1', 'scanner'),
('yo1234', 'yo1234', 'Scanner 2', 'scanner'),
('yo12345', 'yo12345', 'Scanner 3', 'scanner'),
('yo123456', 'yo123456', 'Scanner 4', 'scanner');

-- Tabla de boletos
CREATE TABLE boletos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    estado ENUM('NO_USADO', 'USADO') NOT NULL DEFAULT 'NO_USADO',
    tipo ENUM('GENERAL', 'VIP') NOT NULL DEFAULT 'GENERAL',
    folio VARCHAR(20) NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_uso DATETIME NULL,
    INDEX idx_token (token),
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de prueba
INSERT INTO boletos (token, estado, tipo, folio, fecha_creacion, fecha_uso) VALUES
('A1B2C3D4E5F6G7H8', 'NO_USADO', 'GENERAL', 'BOL-001', NOW(), NULL),
('X9Y8Z7W6V5U4T3S2', 'NO_USADO', 'VIP', 'VIP-001', NOW(), NULL),
('K9L8M7N6O5P4Q3R2', 'NO_USADO', 'GENERAL', 'BOL-003', NOW(), NULL);

-- Verificar
SELECT * FROM usuarios;
SELECT * FROM boletos;
```

Si todo está bien, deberías ver los usuarios y boletos creados.

```sql
EXIT;
```

---

## ⚙️ PASO 3: Configurar el Proyecto

### 3.1 Obtener Datos de Conexión

En Cloud SQL, anota estos datos:

```
IP Pública de la instancia: [ej: 34.123.45.67]
Usuario: root
Contraseña: [la que elegiste]
Base de datos: qr_boletos
```

### 3.2 Actualizar `backend/config.php`

Abre el archivo `backend/config.php` y reemplaza TODO con esto:

```php
<?php
/**
 * CONFIGURACIÓN PARA GOOGLE CLOUD SQL
 */

// Datos de conexión a Cloud SQL
define('DB_HOST', '34.123.45.67');  // ⬅️ CAMBIAR por tu IP pública de Cloud SQL
define('DB_NAME', 'qr_boletos');
define('DB_USER', 'root');
define('DB_PASS', 'TuContraseña123!');  // ⬅️ CAMBIAR por tu contraseña
define('DB_CHARSET', 'utf8mb4');

// URL del sistema (cambiar cuando despliegues)
define('BASE_URL', 'https://disco-qr-system.appspot.com/');  // ⬅️ Actualizar después

// Rutas
define('QR_IMAGES_PATH', __DIR__ . '/../qr_images/');
define('QR_IMAGES_URL', BASE_URL . 'qr_images/');

// Configuración
date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);
ini_set('display_errors', 0);  // En producción: 0

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
?>
```

---

## 🚀 PASO 4: Desplegar en Google App Engine

### 4.1 Instalar Google Cloud SDK

**Windows:**
1. Descargar: https://cloud.google.com/sdk/docs/install
2. Ejecutar el instalador
3. Abrir **PowerShell** y verificar:

```powershell
gcloud --version
```

### 4.2 Inicializar Proyecto

En PowerShell, navega a tu carpeta:

```powershell
cd "C:\Users\aldo0\OneDrive\Escritorio\SISTEMA DE QR"
```

Inicializar:

```powershell
gcloud init
```

- Selecciona tu cuenta Google
- Selecciona el proyecto `disco-qr-system`

### 4.3 Crear Archivo `app.yaml`

Crea un archivo llamado `app.yaml` en la raíz del proyecto con este contenido:

```yaml
runtime: php81

handlers:
  - url: /.*
    secure: always
    redirect_http_response_code: 301
    script: auto

env_variables:
  DB_HOST: "34.123.45.67"  # ⬅️ Tu IP de Cloud SQL
  DB_USER: "root"
  DB_PASS: "TuContraseña123!"  # ⬅️ Tu contraseña
```

### 4.4 Crear `.gcloudignore`

Crea un archivo `.gcloudignore`:

```
.git
.gitignore
*.md
README.md
PRUEBAS.md
INSTALACION.md
node_modules/
```

### 4.5 Desplegar

```powershell
gcloud app deploy
```

Confirma con `Y` cuando pregunte.

Esto tardará 5-10 minutos. Al finalizar verás algo como:

```
Deployed service [default] to [https://disco-qr-system.appspot.com]
```

---

## 🧪 PASO 5: Probar el Sistema

### 5.1 Abrir la Aplicación

```powershell
gcloud app browse
```

O abre en el navegador: `https://tu-proyecto.appspot.com`

### 5.2 Probar Login

**Administrador:**
- Usuario: `Oswaldo`
- Contraseña: `Meneses12`

**Escáneres:**
- Usuario: `yo123` / Contraseña: `yo123`
- Usuario: `yo1234` / Contraseña: `yo1234`
- Usuario: `yo12345` / Contraseña: `yo12345`
- Usuario: `yo123456` / Contraseña: `yo123456`

### 5.3 Probar Funcionalidades

✅ **Como Admin:**
1. Login → Dashboard
2. Generar Boletos → Crear 1 boleto GENERAL
3. Descargar el QR
4. Ir a Escanear
5. Escanear el QR generado → Debe mostrar "VÁLIDO"
6. Escanear el mismo QR → Debe mostrar "YA USADO"

✅ **Como Scanner:**
1. Login con yo123 → Va directo a escanear
2. Escanear QR → Funciona igual

---

## 📱 PASO 6: Probar desde el Celular

1. Abre desde tu celular: `https://tu-proyecto.appspot.com`
2. Login con usuario scanner
3. Permitir acceso a cámara
4. Escanear código QR
5. Debe funcionar perfecto

---

## 🔒 PASO 7: Seguridad (IMPORTANTE)

### 7.1 Restringir Acceso a Cloud SQL

1. En Cloud SQL > Conexiones
2. Eliminar la red `0.0.0.0/0`
3. Agregar solo la IP de App Engine:
   - Ir a App Engine > Settings
   - Copiar "Outbound IP addresses"
   - Agregar cada una en Cloud SQL

### 7.2 Cambiar Contraseñas en Producción

**Opción segura:** Usar Secret Manager

```powershell
echo "TuContraseñaSegura" | gcloud secrets create db-password --data-file=-
```

---

## 💰 COSTOS ESTIMADOS

Con **$300 USD gratis** por 90 días:

- Cloud SQL db-f1-micro: ~$7/mes
- App Engine: ~$0 (capa gratuita)
- Storage: ~$0.50/mes

**Total: ~$7.50/mes** después de los créditos gratuitos.

Durante los primeros 90 días: **GRATIS**

---

## 🆘 Solución de Problemas

### Error: "Connection refused"

✅ Verificar que Cloud SQL esté corriendo
✅ Verificar IP pública en config.php
✅ Verificar red autorizada en Cloud SQL

### Error: "Access denied"

✅ Verificar usuario y contraseña
✅ Verificar que el usuario tenga permisos

### No carga la página

✅ Esperar 2-3 minutos después del deploy
✅ Ver logs: `gcloud app logs tail -s default`

---

## 📞 Comandos Útiles

```powershell
# Ver logs en tiempo real
gcloud app logs tail -s default

# Ver estado del App Engine
gcloud app describe

# Ver instancias de Cloud SQL
gcloud sql instances list

# Conectar a MySQL
gcloud sql connect qr-database --user=root

# Re-desplegar
gcloud app deploy
```

---

## ✅ Checklist Final

- [ ] Cuenta Google Cloud creada
- [ ] Cloud SQL MySQL creado
- [ ] Base de datos `qr_boletos` creada
- [ ] Tablas creadas (usuarios + boletos)
- [ ] config.php actualizado con IP y credenciales
- [ ] app.yaml creado
- [ ] Proyecto desplegado con `gcloud app deploy`
- [ ] Probado login en navegador
- [ ] Probado generación de QR
- [ ] Probado escaneo desde celular
- [ ] Seguridad configurada

---

**¡Listo para producción sin necesidad de XAMPP! 🎉**

Si tienes dudas en algún paso, dime exactamente en cuál estás y te ayudo.
