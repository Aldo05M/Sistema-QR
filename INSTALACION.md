# 📋 GUÍA DE INSTALACIÓN RÁPIDA

## ⚡ Pasos Básicos (5 minutos)

### 1. Instalar XAMPP

- Descargar: https://www.apachefriends.org/
- Instalar con opciones por defecto
- Iniciar **Apache** y **MySQL**

### 2. Crear Base de Datos

1. Abrir: http://localhost/phpmyadmin
2. Clic en "Nueva" (crear base de datos)
3. Nombre: `qr_boletos`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Clic en "Crear"
6. Ir a "Importar"
7. Seleccionar archivo: `sql/boletos.sql`
8. Clic en "Continuar"

### 3. Copiar Archivos

Copiar la carpeta completa a:
```
C:\xampp\htdocs\
```

Resultado:
```
C:\xampp\htdocs\SISTEMA DE QR\
```

### 4. Abrir el Sistema

Navegador: http://localhost/SISTEMA DE QR/

---

## 📱 Usar desde el Celular

### 1. Obtener IP de tu PC

Abrir CMD y ejecutar:
```
ipconfig
```

Buscar "Dirección IPv4", ejemplo: `192.168.1.100`

### 2. Editar Configuración

Abrir: `backend/config.php`

Cambiar:
```php
define('BASE_URL', 'http://192.168.1.100/SISTEMA DE QR/');
```

### 3. Acceder desde el Celular

Conectar el celular al mismo WiFi y abrir:
```
http://192.168.1.100/SISTEMA DE QR/
```

---

## ✅ Verificar que Funciona

### Probar Generador
1. Ir a: http://localhost/SISTEMA DE QR/admin/crear_boleto.html
2. Generar 1 boleto tipo GENERAL
3. Debería aparecer el QR

### Probar Escáner
1. Ir a: http://localhost/SISTEMA DE QR/frontend/scan.html
2. Permitir acceso a cámara
3. Escanear un boleto de prueba
4. Debería mostrar "VÁLIDO" en verde

### Tokens de Prueba

Puedes generar QR con estos tokens para probar:

- `A1B2C3D4E5F6G7H8` → Válido
- `M1N2O3P4Q5R6S7T8` → Ya usado
- `TOKENFALSO123456` → Inválido

---

## 🔧 Solución de Problemas Comunes

### Apache no inicia
- Cerrar Skype (usa el puerto 80)
- O cambiar el puerto en XAMPP

### No encuentra la base de datos
- Verificar que MySQL esté corriendo
- Verificar nombre: `qr_boletos` (todo en minúsculas)

### Cámara no funciona en el celular
- Verificar que sea http://IP (no localhost)
- Dar permisos de cámara en el navegador
- En producción necesitas HTTPS

---

## 📞 Checklist del Día del Evento

- [ ] Probar sistema 1 día antes
- [ ] Generar todos los boletos necesarios
- [ ] Imprimir boletos o enviar por correo
- [ ] Verificar conexión a internet en el local
- [ ] Cargar completamente los celulares
- [ ] Tener al menos 2 dispositivos para escanear
- [ ] Probar con boletos reales antes de abrir

---

**¿Listo? ¡A validar boletos! 🎉**
