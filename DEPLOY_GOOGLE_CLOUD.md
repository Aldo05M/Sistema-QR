# 🚀 GUÍA DE DESPLIEGUE EN GOOGLE CLOUD

## 📋 Requisitos Previos

- Cuenta de Google Cloud (con tarjeta de crédito)
- Dominio propio (opcional pero recomendado)

---

## 🎯 OPCIÓN 1: Google Cloud App Engine (Recomendado)

### Ventajas:
- ✅ Fácil de configurar
- ✅ HTTPS automático
- ✅ Escalamiento automático
- ✅ Sin gestión de servidores

### Paso 1: Preparar el Proyecto

1. Crear archivo `app.yaml` en la raíz:

```yaml
runtime: php81
entrypoint: serve .

handlers:
- url: /.*
  secure: always
  redirect_http_response_code: 301
  script: auto
```

2. Crear `.gcloudignore`:

```
.git
.gitignore
README.md
PRUEBAS.md
node_modules/
```

### Paso 2: Configurar Google Cloud SQL (MySQL)

1. Ir a: https://console.cloud.google.com/sql
2. Crear instancia MySQL:
   - Nombre: `qr-database`
   - Versión: MySQL 8.0
   - Región: us-central1 (o la más cercana)
   - Tipo: db-f1-micro (gratis o económico)

3. Configurar:
   - Crear base de datos: `qr_boletos`
   - Crear usuario: `qr_user` / contraseña segura
   - Habilitar conexión desde App Engine

4. Importar SQL:
   - Conectarse via Cloud Shell
   - Importar `sql/boletos.sql`

### Paso 3: Actualizar `backend/config.php`

```php
<?php
// Detectar si está en producción
$isProduction = (getenv('GAE_APPLICATION') !== false);

if ($isProduction) {
    // Configuración para Google Cloud SQL
    $connectionName = getenv('CLOUD_SQL_CONNECTION_NAME');
    $dbUser = getenv('CLOUD_SQL_USER');
    $dbPass = getenv('CLOUD_SQL_PASSWORD');
    $dbName = 'qr_boletos';
    
    define('DB_HOST', "/cloudsql/{$connectionName}");
    define('DB_USER', $dbUser);
    define('DB_PASS', $dbPass);
    define('DB_NAME', $dbName);
    define('BASE_URL', 'https://tu-proyecto.appspot.com/');
} else {
    // Configuración local
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'qr_boletos');
    define('BASE_URL', 'http://localhost/SISTEMA DE QR/');
}

define('DB_CHARSET', 'utf8mb4');
define('QR_IMAGES_PATH', __DIR__ . '/../qr_images/');
define('QR_IMAGES_URL', BASE_URL . 'qr_images/');

date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
?>
```

### Paso 4: Desplegar

1. Instalar Google Cloud SDK:
   ```bash
   https://cloud.google.com/sdk/docs/install
   ```

2. Inicializar proyecto:
   ```bash
   gcloud init
   gcloud app create --region=us-central
   ```

3. Configurar variables de entorno:
   ```bash
   gcloud app deploy app.yaml --set-env-vars="CLOUD_SQL_CONNECTION_NAME=tu-proyecto:us-central1:qr-database,CLOUD_SQL_USER=qr_user,CLOUD_SQL_PASSWORD=tu-contraseña-segura"
   ```

4. Desplegar:
   ```bash
   gcloud app deploy
   ```

5. Ver la app:
   ```bash
   gcloud app browse
   ```

---

## 🎯 OPCIÓN 2: Compute Engine (VM)

### Más control pero más configuración

1. Crear VM:
   - e2-micro (gratis en capa gratuita)
   - Ubuntu 22.04
   - Permitir HTTP/HTTPS

2. Instalar LAMP:
   ```bash
   sudo apt update
   sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
   ```

3. Configurar SSL con Let's Encrypt:
   ```bash
   sudo apt install certbot python3-certbot-apache
   sudo certbot --apache
   ```

4. Subir archivos via SCP o SFTP

---

## 🔐 CONFIGURACIÓN DE SEGURIDAD

### 1. Variables de Entorno (Google Cloud)

No hardcodear contraseñas. Usar Secret Manager:

```bash
echo -n "Meneses12" | gcloud secrets create admin-password --data-file=-
```

### 2. Firewall

Solo permitir puertos:
- 80 (HTTP)
- 443 (HTTPS)

### 3. Cloud Armor (opcional)

Protección contra DDoS y ataques.

---

## 📱 CONFIGURAR DOMINIO PERSONALIZADO

1. Ir a: Cloud Console > App Engine > Settings > Custom Domains

2. Verificar dominio

3. Configurar DNS:
   ```
   A Record: 216.239.32.21
   AAAA Record: 2001:4860:4802:32::15
   ```

4. Mapear dominio:
   ```bash
   gcloud app domain-mappings create 'tudisco.com'
   ```

---

## 💰 COSTOS ESTIMADOS

### App Engine + Cloud SQL (e2-micro):

- App Engine: ~$0 (capa gratuita)
- Cloud SQL db-f1-micro: ~$7-10/mes
- Storage: ~$0.02/GB

**Total: ~$10/mes** para 600 usuarios

### Capa Gratuita de Google Cloud:

- 28 horas/día de instancias f1-micro
- 1 GB de tráfico por mes
- 30 GB de almacenamiento

---

## 🧪 PROBAR ANTES DE DESPLEGAR

1. Local con XAMPP
2. Migrar base de datos
3. Probar todas las funciones
4. Verificar HTTPS
5. Probar en celulares

---

## 📊 MONITOREO

1. Google Cloud Console > Monitoring
2. Ver logs:
   ```bash
   gcloud app logs tail -s default
   ```

3. Alertas configurables

---

## 🚨 BACKUP AUTOMÁTICO

Cloud SQL hace backups automáticos diarios.

Configurar:
- Retention: 7 días
- Hora: 3:00 AM

---

## ✅ CHECKLIST PRE-PRODUCCIÓN

- [ ] Base de datos creada e importada
- [ ] Usuarios creados en BD
- [ ] Variables de entorno configuradas
- [ ] SSL/HTTPS habilitado
- [ ] Dominio configurado
- [ ] Probado en celular real
- [ ] Backups configurados
- [ ] Logs monitoreados

---

## 🆘 SOPORTE

- Documentación: https://cloud.google.com/docs
- Foro: https://stackoverflow.com/questions/tagged/google-cloud-platform
- Chat: Cloud Console Support

---

**¡Listo para producción! 🎉**
