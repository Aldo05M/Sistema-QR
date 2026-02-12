# 🎯 GUÍA PASO A PASO - DESPLIEGUE EN GOOGLE CLOUD

## ✅ Ya tienes cuenta Google Cloud, ahora sigue estos pasos:

---

## 📦 PASO 1: CREAR UN PROYECTO

### 1.1 Ir a Google Cloud Console
- Abre: https://console.cloud.google.com/

### 1.2 Crear Nuevo Proyecto
1. En la parte superior, al lado del logo de Google Cloud, clic en el selector de proyectos
2. Clic en **"NUEVO PROYECTO"**
3. Llenar los datos:
   ```
   Nombre del proyecto: Sistema QR Disco
   ID del proyecto: sistema-qr-disco-123 (puede ser diferente)
   ```
4. Clic en **"CREAR"**
5. Esperar 30 segundos hasta que aparezca notificación
6. Clic en **"SELECCIONAR PROYECTO"**

✅ **Verificar:** En la parte superior debe decir "Sistema QR Disco"

---

## 💾 PASO 2: CREAR BASE DE DATOS (Cloud SQL)

### 2.1 Ir a Cloud SQL
1. En el menú lateral izquierdo (☰), buscar **"SQL"**
2. O ir directo: https://console.cloud.google.com/sql/instances

### 2.2 Habilitar API (primera vez)
- Si te pide "Habilitar Cloud SQL Admin API", clic en **"HABILITAR"**
- Esperar 1-2 minutos

### 2.3 Crear Instancia MySQL
1. Clic en **"CREAR INSTANCIA"**
2. Seleccionar **"MySQL"**
3. Clic en **"Habilitar API"** si aparece
4. Llenar el formulario:

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONFIGURACIÓN DE LA INSTANCIA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

ID de instancia: qr-database

Contraseña para root:
┌─────────────────────────────────┐
│ Disco2026!                      │  ⬅️ Anota esto, lo necesitarás
└─────────────────────────────────┘

Versión de base de datos: MySQL 8.0

Región: us-central1 (Iowa)
Zona: us-central1-a

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONFIGURACIÓN DE TIPO DE MÁQUINA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Seleccionar: "Desarrollo" o personalizar:

Núcleos compartidos
Núcleos: 1 vCPU compartida
Memoria: 0.614 GB

Almacenamiento: 10 GB
Tipo: SSD

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONEXIONES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

☑️ IP pública
☐ IP privada

En "Redes autorizadas":
Clic en "AGREGAR RED"

Nombre: temporal
Red: 0.0.0.0/0

⚠️ Esto es temporal para configurar
```

5. Clic en **"CREAR INSTANCIA"**
6. **ESPERAR 5-10 MINUTOS** (toma café ☕)

### 2.4 Obtener IP Pública de la Instancia

Cuando termine de crear:

1. Verás la instancia `qr-database` en la lista
2. Clic en el nombre de la instancia
3. En la pestaña "DESCRIPCIÓN GENERAL"
4. Buscar **"IP pública"**

```
┌──────────────────────────────────┐
│ IP pública: 34.123.45.67         │  ⬅️ ANOTA ESTA IP
└──────────────────────────────────┘
```

✅ **Anotar:**
- IP pública: `________________`
- Contraseña root: `Disco2026!`

---

## 🗄️ PASO 3: CREAR LA BASE DE DATOS Y TABLAS

### 3.1 Abrir Cloud Shell
1. En la parte superior derecha de Cloud Console
2. Clic en el ícono **">_"** (Activar Cloud Shell)
3. Esperar que cargue (aparece una terminal en la parte inferior)

### 3.2 Conectarse a MySQL

En Cloud Shell, ejecutar:

```bash
gcloud sql connect qr-database --user=root --quiet
```

Te pedirá la contraseña:
```
Password: [escribe: Disco2026!]
```

✅ **Debes ver:**
```
mysql>
```

### 3.3 Crear la Base de Datos

Copiar y pegar línea por línea:

```sql
CREATE DATABASE qr_boletos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Presionar ENTER. Debe decir: `Query OK`

```sql
USE qr_boletos;
```

Presionar ENTER. Debe decir: `Database changed`

### 3.4 Crear Tabla de Usuarios

Copiar TODO este bloque y pegar:

```sql
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
```

Presionar ENTER. Debe decir: `Query OK`

### 3.5 Insertar Usuarios

Copiar y pegar:

```sql
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES
('Oswaldo', 'Meneses12', 'Oswaldo (Administrador)', 'admin'),
('yo123', 'yo123', 'Scanner 1', 'scanner'),
('yo1234', 'yo1234', 'Scanner 2', 'scanner'),
('yo12345', 'yo12345', 'Scanner 3', 'scanner'),
('yo123456', 'yo123456', 'Scanner 4', 'scanner');
```

Debe decir: `Query OK, 5 rows affected`

### 3.6 Crear Tabla de Boletos

Copiar y pegar:

```sql
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
```

Debe decir: `Query OK`

### 3.7 Insertar Boletos de Prueba

Copiar y pegar:

```sql
INSERT INTO boletos (token, estado, tipo, folio) VALUES
('A1B2C3D4E5F6G7H8', 'NO_USADO', 'GENERAL', 'BOL-001'),
('X9Y8Z7W6V5U4T3S2', 'NO_USADO', 'VIP', 'VIP-001'),
('K9L8M7N6O5P4Q3R2', 'NO_USADO', 'GENERAL', 'BOL-003');
```

Debe decir: `Query OK, 3 rows affected`

### 3.8 Verificar que Todo se Creó

```sql
SELECT * FROM usuarios;
```

Debes ver 5 usuarios (Oswaldo, yo123, yo1234, yo12345, yo123456)

```sql
SELECT * FROM boletos;
```

Debes ver 3 boletos de prueba

```sql
EXIT;
```

Salir de MySQL.

✅ **Base de datos lista!**

---

## ⚙️ PASO 4: CONFIGURAR TU PROYECTO LOCAL

### 4.1 Editar `backend/config.php`

1. Abre VS Code con tu proyecto
2. Abre el archivo: `backend/config.php`
3. Busca estas líneas y cámbialas:

```php
define('DB_HOST', '34.123.45.67');  // ⬅️ PONER TU IP PÚBLICA DE CLOUD SQL
define('DB_NAME', 'qr_boletos');
define('DB_USER', 'root');
define('DB_PASS', 'Disco2026!');  // ⬅️ TU CONTRASEÑA
```

4. Guardar el archivo (Ctrl + S)

### 4.2 Editar `app.yaml`

1. Abrir archivo: `app.yaml`
2. Cambiar estas líneas:

```yaml
env_variables:
  DB_HOST: "34.123.45.67"  # ⬅️ TU IP PÚBLICA
  DB_USER: "root"
  DB_PASS: "Disco2026!"  # ⬅️ TU CONTRASEÑA
  DB_NAME: "qr_boletos"
```

3. Guardar (Ctrl + S)

✅ **Configuración lista!**

---

## 🚀 PASO 5: INSTALAR GOOGLE CLOUD SDK

### 5.1 Descargar SDK

1. Ir a: https://cloud.google.com/sdk/docs/install
2. Descargar **"Google Cloud CLI installer for Windows"**
3. Ejecutar el instalador
4. Seguir los pasos (Next, Next, Install)
5. Al final, marcar:
   - ☑️ "Run gcloud init"
6. Clic en "Finish"

### 5.2 Inicializar gcloud

Se abrirá una ventana de PowerShell automáticamente.

Te preguntará:

```
1. Login con tu cuenta Google
   → Presiona ENTER
   → Se abre el navegador
   → Selecciona tu cuenta de Google Cloud
   → Clic en "Permitir"
```

```
2. Pick cloud project to use:
   → Busca "sistema-qr-disco-123" (o el nombre de tu proyecto)
   → Escribe el número y ENTER
```

```
3. Región por defecto:
   → Escribe: 14 (us-central1)
   → ENTER
```

✅ **SDK instalado y configurado!**

---

## 📤 PASO 6: DESPLEGAR EL SISTEMA

### 6.1 Abrir PowerShell en tu Proyecto

1. Abrir PowerShell (Win + X → Windows PowerShell)
2. Navegar a tu carpeta:

```powershell
cd "C:\Users\aldo0\OneDrive\Escritorio\SISTEMA DE QR"
```

### 6.2 Verificar que todo esté listo

```powershell
ls
```

Debes ver archivos como: `app.yaml`, `index.html`, carpetas `backend`, `frontend`, etc.

### 6.3 Desplegar a Google Cloud

```powershell
gcloud app deploy
```

Te preguntará:

```
Do you want to continue (Y/n)?
```

Escribe: `Y` y ENTER

**ESPERAR 5-10 MINUTOS** ⏳

Verás algo como:

```
Updating service [default]...done.
Setting traffic split for service [default]...done.
Deployed service [default] to [https://sistema-qr-disco-123.uc.r.appspot.com]
```

✅ **Sistema desplegado!**

---

## 🎉 PASO 7: PROBAR EL SISTEMA

### 7.1 Abrir la URL

```powershell
gcloud app browse
```

O copia y pega en el navegador la URL que apareció.

### 7.2 Probar Login

**Como Administrador:**
```
Usuario: Oswaldo
Contraseña: Meneses12
```

Debes ver el Dashboard con opciones de Generar y Escanear.

**Como Scanner:**
```
Usuario: yo123
Contraseña: yo123
```

Debes ir directamente a la página de escaneo.

### 7.3 Probar Generar Boleto

1. Login como Oswaldo
2. Clic en "Generar Boletos"
3. Tipo: GENERAL
4. Cantidad: 1
5. Clic en "Generar Boletos"
6. Debe aparecer un código QR
7. Clic derecho → "Guardar imagen como" → Guardar en tu celular

### 7.4 Probar Escaneo

1. Desde el celular, abrir la URL del sistema
2. Login con yo123 / yo123
3. Permitir acceso a cámara
4. Apuntar a un código QR impreso
5. Debe mostrar "VÁLIDO" en verde

✅ **Sistema funcionando completo!**

---

## 📱 PASO 8: COMPARTIR CON TU EQUIPO

### URL del Sistema:
```
https://sistema-qr-disco-123.uc.r.appspot.com
```

### Usuarios para el personal:
```
Scanner 1: yo123 / yo123
Scanner 2: yo1234 / yo1234
Scanner 3: yo12345 / yo12345
Scanner 4: yo123456 / yo123456
```

### Solo tú tienes acceso admin:
```
Oswaldo / Meneses12
```

---

## 🔒 PASO 9 (OPCIONAL): MEJORAR SEGURIDAD

### 9.1 Restringir Acceso a Cloud SQL

1. Ir a: Cloud SQL → qr-database → Conexiones
2. Eliminar la red "temporal" (0.0.0.0/0)
3. Dejar solo App Engine (se conecta automáticamente por VPC)

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### ❌ Error: "Connection refused"

**Solución:**
1. Verificar que Cloud SQL esté corriendo (verde en console)
2. Verificar IP en `config.php` y `app.yaml`
3. Verificar que la red 0.0.0.0/0 esté agregada

### ❌ Error: "Access denied for user 'root'"

**Solución:**
1. Verificar contraseña en `config.php`
2. Intentar conectar desde Cloud Shell para probar

### ❌ La página no carga

**Solución:**
1. Esperar 2-3 minutos después del deploy
2. Ver logs: `gcloud app logs tail -s default`
3. Buscar errores en rojo

### ❌ Error al hacer deploy

**Solución:**
1. Verificar que `app.yaml` esté en la raíz
2. Verificar que estés en la carpeta correcta
3. Ejecutar: `gcloud config set project sistema-qr-disco-123`

---

## 📋 COMANDOS ÚTILES

```powershell
# Ver logs en tiempo real
gcloud app logs tail -s default

# Re-desplegar después de cambios
gcloud app deploy

# Abrir el navegador con la app
gcloud app browse

# Ver información del proyecto
gcloud app describe

# Conectar a MySQL desde local
gcloud sql connect qr-database --user=root
```

---

## ✅ CHECKLIST FINAL

Marca lo que ya hiciste:

- [ ] Proyecto creado en Google Cloud
- [ ] Cloud SQL instancia creada
- [ ] IP pública obtenida
- [ ] Base de datos `qr_boletos` creada
- [ ] Tabla `usuarios` creada con 5 usuarios
- [ ] Tabla `boletos` creada con boletos de prueba
- [ ] Archivo `config.php` editado con IP y contraseña
- [ ] Archivo `app.yaml` editado
- [ ] Google Cloud SDK instalado
- [ ] `gcloud init` ejecutado
- [ ] `gcloud app deploy` exitoso
- [ ] Login probado (Oswaldo y yo123)
- [ ] Generación de QR probada
- [ ] Escaneo desde celular probado
- [ ] URL compartida con el equipo

---

## 🎊 ¡FELICIDADES!

Tu sistema ya está en producción y listo para el evento.

**URL de tu sistema:**
```
https://[tu-proyecto].uc.r.appspot.com
```

**Próximo paso:** Generar todos los boletos necesarios para la disco.

---

**¿Algún paso no funcionó? Dime exactamente en cuál estás y te ayudo! 💪**
