# 🎫 Sistema de Validación de Boletos con QR

Sistema completo y autosuficiente para generar, validar y gestionar boletos con códigos QR para eventos (discotecas, conciertos, etc).

## 📋 Características

✅ **Generación de QR** - Crea boletos únicos con códigos QR  
✅ **Sin duplicados** - Verificación automática de tokens únicos  
✅ **Base de datos** - Almacenamiento seguro en MySQL  
✅ **Escáner móvil** - Funciona desde el navegador del celular  
✅ **Validación en tiempo real** - Respuesta inmediata  
✅ **Marca como usado** - Control automático de boletos escaneados  
✅ **Interfaz rápida** - Diseñada para entrada rápida de personas  

---

## 🚀 Instalación

### Requisitos

- **XAMPP** (o cualquier servidor con PHP + MySQL)
- **Navegador moderno** (Chrome, Firefox, Safari)
- **HTTPS** (requerido para acceso a la cámara en producción)

### Paso 1: Instalar XAMPP

1. Descargar XAMPP desde: https://www.apachefriends.org/
2. Instalar con PHP 7.4+ y MySQL
3. Iniciar Apache y MySQL desde el panel de control

### Paso 2: Configurar la Base de Datos

1. Abrir phpMyAdmin: http://localhost/phpmyadmin
2. Crear una nueva base de datos llamada `qr_boletos`
3. Importar el archivo `sql/boletos.sql` o ejecutar el script manualmente

### Paso 3: Copiar los Archivos

1. Copiar toda la carpeta del sistema a:
   ```
   C:\xampp\htdocs\SISTEMA DE QR\
   ```

### Paso 4: Configurar el Backend

1. Copiar el archivo `backend/config.example.php` como `backend/config.php`
2. Verificar la configuración:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'qr_boletos');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vacío para XAMPP local
define('BASE_URL', 'http://localhost/SISTEMA DE QR/');

// Nota: No subas backend/config.php a GitHub porque contiene credenciales.
```

### Paso 5: Probar el Sistema

1. Abrir en el navegador:
   ```
   http://localhost/SISTEMA DE QR/
   ```

2. Deberías ver el menú principal con dos opciones:
   - 📱 **Escanear Boletos**
   - ✨ **Generar Boletos**

---

## 📁 Estructura del Proyecto

```
SISTEMA DE QR/
│
├── index.html              # Página principal
│
├── backend/                # Backend PHP
│   ├── config.php          # Configuración
│   ├── db.php              # Conexión a BD y funciones
│   ├── generar_qr.php      # API para generar boletos
│   ├── validar_qr.php      # API para validar boletos
│   └── estadisticas.php    # API de estadísticas
│
├── frontend/               # Escáner móvil
│   └── scan.html           # Página de escaneo
│
├── admin/                  # Panel administrativo
│   └── crear_boleto.html   # Generador de boletos
│
├── sql/                    # Base de datos
│   └── boletos.sql         # Script de creación
│
└── qr_images/              # Imágenes QR generadas
```

---

## 🎯 Uso del Sistema

### Generar Boletos

1. Ir a: http://localhost/SISTEMA DE QR/admin/crear_boleto.html
2. Seleccionar tipo de boleto (General o VIP)
3. Indicar cantidad a generar
4. Clic en "Generar Boletos"
5. Descargar o imprimir los códigos QR

### Escanear Boletos

1. Abrir desde el celular: http://[TU-IP]/SISTEMA DE QR/frontend/scan.html
2. Permitir acceso a la cámara
3. Apuntar al código QR del boleto
4. Ver resultado:
   - 🟢 **VÁLIDO** - Permitir entrada
   - 🔴 **YA USADO** - Denegar entrada (boleto duplicado)
   - 🔴 **INVÁLIDO** - Denegar entrada (boleto falso)

---

## 🧪 Datos de Prueba

El sistema incluye 5 boletos de ejemplo:

| Token              | Folio   | Tipo    | Estado    |
| ------------------ | ------- | ------- | --------- |
| A1B2C3D4E5F6G7H8   | BOL-001 | GENERAL | NO_USADO  |
| X9Y8Z7W6V5U4T3S2   | VIP-001 | VIP     | NO_USADO  |
| M1N2O3P4Q5R6S7T8   | BOL-002 | GENERAL | USADO     |
| K9L8M7N6O5P4Q3R2   | BOL-003 | GENERAL | NO_USADO  |
| J1K2L3M4N5O6P7Q8   | VIP-002 | VIP     | NO_USADO  |

Puedes generar códigos QR con estos tokens para probar.

---

## 🔧 Configuración Avanzada

### Usar desde otros dispositivos en la red local

1. Obtener la IP de tu computadora:
   ```
   ipconfig
   ```
   Buscar "Dirección IPv4" (ej: 192.168.1.100)

2. En `backend/config.php` cambiar:
   ```php
   define('BASE_URL', 'http://192.168.1.100/SISTEMA DE QR/');
   ```

3. Acceder desde el celular:
   ```
   http://192.168.1.100/SISTEMA DE QR/
   ```

### Configurar para producción (servidor real)

1. Subir archivos al servidor
2. Crear base de datos MySQL
3. Importar `sql/boletos.sql`
4. Editar `backend/config.php`:
   ```php
   define('DB_HOST', 'tu-servidor.com');
   define('DB_NAME', 'tu_bd');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('BASE_URL', 'https://tudisco.com/');
   ```

5. **IMPORTANTE**: Usar HTTPS (obligatorio para cámara)

---

## ⚠️ Solución de Problemas

### La cámara no funciona

- Verificar que estés usando HTTPS (en producción)
- Dar permisos de cámara al navegador
- En iOS Safari: Settings > Safari > Camera

### Error de conexión a base de datos

- Verificar que MySQL esté corriendo
- Revisar credenciales en `backend/config.php`
- Verificar que la base de datos exista

### Los QR no se generan

- Verificar que la carpeta `qr_images/` tenga permisos de escritura
- Revisar conexión a internet (usa API externa para generar QR)

### El escáner no valida

- Verificar que `backend/validar_qr.php` sea accesible
- Revisar la consola del navegador (F12) para ver errores
- Verificar que la URL del API esté correcta en `frontend/scan.html`

---

## 🔐 Seguridad

### Recomendaciones básicas:

✅ Usar tokens de 16+ caracteres  
✅ HTTPS obligatorio en producción  
✅ Cambiar contraseñas de base de datos  
✅ Limitar acceso al panel admin  
✅ Mantener logs de validaciones  

### Opcional (más seguridad):

- Agregar autenticación de usuarios
- Implementar rate limiting
- Usar tokens JWT para el API
- Cifrar comunicaciones

---

## 📊 Estadísticas

El sistema registra automáticamente:

- Total de boletos generados
- Boletos usados
- Boletos disponibles
- Fecha de creación
- Fecha de uso
- Tipo de boleto (General/VIP)

---

## 🎨 Personalización

### Cambiar colores

Editar los archivos CSS en:
- `admin/crear_boleto.html`
- `frontend/scan.html`

### Agregar más tipos de boletos

1. Modificar el ENUM en `sql/boletos.sql`:
   ```sql
   tipo ENUM('GENERAL', 'VIP', 'BACKSTAGE') NOT NULL DEFAULT 'GENERAL'
   ```

2. Actualizar los formularios HTML

### Cambiar diseño del QR

En `backend/generar_qr.php`, modificar la URL de la API:
```php
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=...";
```

---

## 💡 Consejos para el Evento

### Antes del evento:

1. Probar el sistema con boletos de prueba
2. Verificar conexión a internet
3. Cargar batería de los celulares
4. Tener backup de los boletos generados
5. Imprimir lista de folios como respaldo

### Durante el evento:

1. Mantener el escáner a la altura de los boletos
2. Iluminación adecuada para leer QR
3. Tener más de un dispositivo escaneando (si hay mucha gente)
4. Revisar periódicamente las estadísticas

### Protocolo del staff:

- 🟢 **VÁLIDO** → Permitir entrada
- 🔴 **YA USADO** → Denegar y reportar (posible duplicado)
- 🔴 **INVÁLIDO** → Denegar (boleto falso)

---

## 🐛 Reporte de Bugs

Si encuentras algún problema:

1. Revisar los logs de PHP en: `C:\xampp\apache\logs\error.log`
2. Revisar la consola del navegador (F12)
3. Documentar pasos para reproducir el error

---

## 📝 Licencia

Este sistema es de uso libre para eventos privados.

---

## 👨‍💻 Créditos

Desarrollado con:
- PHP 7.4+
- MySQL 5.7+
- html5-qrcode (librería de escaneo)
- QR Server API (generación de QR)

---

## 🚀 Próximas Mejoras (Opcional)

- [ ] Panel de estadísticas en tiempo real
- [ ] Exportar reportes a Excel
- [ ] Notificaciones push
- [ ] App nativa Android/iOS
- [ ] Sistema de reimpresión de boletos
- [ ] Integración con sistemas de pago

---

## 📞 Contacto

Para soporte o dudas sobre el sistema, revisar la documentación o contactar al administrador del sistema.

---

**¡Listo para usar! 🎉**

