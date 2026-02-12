# 🧪 GUÍA DE PRUEBAS

## ✅ Checklist de Pruebas Pre-Evento

### 1️⃣ Prueba de Base de Datos

**Objetivo:** Verificar que la BD esté configurada correctamente

1. Abrir phpMyAdmin: http://localhost/phpmyadmin
2. Seleccionar base de datos `qr_boletos`
3. Verificar que existe la tabla `boletos`
4. Verificar que hay 5 registros de prueba

**Resultado esperado:** ✅ Tabla existe con datos de prueba

---

### 2️⃣ Prueba de Generación de QR

**Objetivo:** Verificar que se pueden crear boletos

1. Ir a: http://localhost/SISTEMA DE QR/admin/crear_boleto.html
2. Seleccionar tipo: GENERAL
3. Cantidad: 1
4. Clic en "Generar Boletos"

**Resultado esperado:** 
- ✅ Mensaje "Boleto generado exitosamente"
- ✅ Aparece imagen del QR
- ✅ Se muestra folio, token, tipo y fecha

**Verificación en BD:**
```sql
SELECT * FROM boletos ORDER BY id DESC LIMIT 1;
```
Debe aparecer el boleto recién creado con estado `NO_USADO`

---

### 3️⃣ Prueba de Validación con Token Válido

**Objetivo:** Verificar que un boleto NO USADO se valida correctamente

1. Usar token de prueba: `A1B2C3D4E5F6G7H8`
2. Generar QR con este token (puedes usar: https://www.qr-code-generator.com/)
3. Abrir escáner: http://localhost/SISTEMA DE QR/frontend/scan.html
4. Escanear el QR

**Resultado esperado:**
- ✅ Pantalla VERDE
- ✅ Texto "VÁLIDO"
- ✅ Muestra folio: BOL-001
- ✅ Contador aumenta a 1

**Verificación en BD:**
```sql
SELECT estado, fecha_uso FROM boletos WHERE token = 'A1B2C3D4E5F6G7H8';
```
Debe mostrar: `estado = USADO` y `fecha_uso` con la hora actual

---

### 4️⃣ Prueba de Boleto Ya Usado

**Objetivo:** Verificar que un boleto USADO se rechaza

1. Usar el mismo token del paso anterior: `A1B2C3D4E5F6G7H8`
2. Volver a escanear el QR

**Resultado esperado:**
- ✅ Pantalla ROJA
- ✅ Texto "YA USADO"
- ✅ Muestra folio: BOL-001
- ✅ Contador NO aumenta

---

### 5️⃣ Prueba de Token Inválido

**Objetivo:** Verificar que tokens falsos se rechazan

1. Crear QR con token falso: `TOKENFALSO123456`
2. Escanear el QR

**Resultado esperado:**
- ✅ Pantalla ROJA
- ✅ Texto "INVÁLIDO"
- ✅ Mensaje "Boleto no encontrado"
- ✅ Contador NO aumenta

---

### 6️⃣ Prueba de Velocidad

**Objetivo:** Medir tiempo de validación

1. Generar 3 boletos diferentes
2. Imprimir los QR
3. Cronometrar: escanear los 3 consecutivamente

**Resultado esperado:**
- ✅ Tiempo promedio: 2-3 segundos por boleto
- ✅ Sin errores de lectura
- ✅ Respuesta fluida

---

### 7️⃣ Prueba desde Celular

**Objetivo:** Verificar funcionamiento en dispositivo móvil

1. Conectar celular al mismo WiFi
2. Abrir: http://[IP-PC]/SISTEMA DE QR/frontend/scan.html
3. Permitir acceso a cámara
4. Escanear boletos de prueba

**Resultado esperado:**
- ✅ Cámara se activa correctamente
- ✅ Lee QR sin problemas
- ✅ Muestra resultados correctos
- ✅ Pantalla se adapta al móvil

**Probar en:**
- [ ] Android + Chrome
- [ ] iPhone + Safari
- [ ] Diferentes tamaños de pantalla

---

### 8️⃣ Prueba de Múltiples Dispositivos

**Objetivo:** Verificar que varios celulares pueden escanear simultáneamente

1. Conectar 2-3 celulares
2. Abrir escáner en todos
3. Escanear boletos diferentes al mismo tiempo

**Resultado esperado:**
- ✅ Todos validan correctamente
- ✅ No hay conflictos en BD
- ✅ Estadísticas se actualizan

---

### 9️⃣ Prueba de Estadísticas

**Objetivo:** Verificar contador de boletos

1. Ir a: http://localhost/SISTEMA DE QR/admin/crear_boleto.html
2. Verificar los números en las tarjetas superiores:
   - Total Generados
   - Disponibles
   - Usados

**Resultado esperado:**
- ✅ Números coinciden con la realidad
- ✅ Se actualizan al generar nuevos
- ✅ Se actualizan al validar

---

### 🔟 Prueba de Resistencia

**Objetivo:** Verificar estabilidad con uso intensivo

1. Generar 20 boletos
2. Validar 10 rápidamente
3. Intentar validar duplicados
4. Generar 20 más
5. Validar otros 15

**Resultado esperado:**
- ✅ Sistema responde sin ralentizarse
- ✅ No hay errores de BD
- ✅ Tokens únicos siempre
- ✅ Estadísticas correctas

---

## 🔍 Pruebas de Errores Comunes

### Error: Cámara no disponible

**Cómo provocar:**
- Denegar permisos de cámara
- Usar HTTP en lugar de HTTPS (en producción)

**Resultado esperado:**
- ✅ Mensaje claro explicando el problema
- ✅ Instrucciones para habilitar cámara

---

### Error: Sin conexión a BD

**Cómo provocar:**
- Apagar MySQL en XAMPP
- Intentar generar boleto

**Resultado esperado:**
- ✅ Mensaje de error claro
- ✅ No se rompe la aplicación

---

### Error: QR mal impreso

**Cómo provocar:**
- Imprimir QR muy pequeño
- Arrugar el papel
- Usar QR borroso

**Resultado esperado:**
- ✅ Sistema reintenta lectura
- ✅ No lee token incorrecto
- ✅ Timeout después de varios intentos

---

## 📊 Tabla de Resultados

| Prueba | ✅ / ❌ | Observaciones |
|--------|---------|---------------|
| 1. Base de datos | | |
| 2. Generación QR | | |
| 3. Token válido | | |
| 4. Token usado | | |
| 5. Token inválido | | |
| 6. Velocidad | | |
| 7. Celular | | |
| 8. Múltiples dispositivos | | |
| 9. Estadísticas | | |
| 10. Resistencia | | |

---

## 🎯 Escenario de Simulación Real

### Preparación

1. Generar 50 boletos (40 GENERAL, 10 VIP)
2. Imprimir 30 de ellos
3. Configurar 2 celulares como escáneres

### Simulación

1. **T+0 min:** Sistema listo, celulares cargados
2. **T+5 min:** Empezar a escanear boletos (simular entrada)
3. **T+10 min:** Intentar escanear un boleto ya usado
4. **T+15 min:** Intentar escanear un QR falso
5. **T+20 min:** Escanear rápido 10 boletos consecutivos
6. **T+25 min:** Verificar estadísticas
7. **T+30 min:** Finalizar y revisar logs

### Métricas a medir

- Tiempo promedio por validación: ______ segundos
- Boletos validados en 30 min: ______
- Errores encontrados: ______
- Falsos positivos: ______
- Falsos negativos: ______

---

## ✅ Criterios de Aceptación

El sistema está listo si:

- ✅ Genera boletos únicos sin duplicados
- ✅ Valida correctamente (verde/rojo según estado)
- ✅ Marca como usado inmediatamente
- ✅ Rechaza duplicados y falsos
- ✅ Funciona en celulares
- ✅ Velocidad < 3 segundos por boleto
- ✅ Estadísticas precisas
- ✅ Sin errores críticos

---

## 🚨 Qué Hacer Si Algo Falla

1. **Documentar el error:**
   - Qué estabas haciendo
   - Qué esperabas
   - Qué pasó realmente
   - Captura de pantalla

2. **Revisar logs:**
   - Backend: `C:\xampp\apache\logs\error.log`
   - Navegador: Consola (F12)

3. **Verificar configuración:**
   - `backend/config.php`
   - Conexión a BD
   - Permisos de carpetas

4. **Reiniciar servicios:**
   - Apache
   - MySQL
   - Navegador

---

**¡Sistema probado = Sistema confiable! 🎉**
