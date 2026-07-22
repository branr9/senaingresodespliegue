# Sistema de Control de Ingreso con Código de Barras

## Descripción de los Cambios

El sistema ha sido modificado para funcionar con un **lector láser de código de barras** en lugar de huella dactilar.

## ¿Qué se Modificó?

### 1. Vista del Kiosko ([kiosk.php](app/views/access_control/kiosk.php))
- ✅ Cambio de icono: de 👆 (dedo) a 📷 (código de barras)
- ✅ Instrucciones actualizadas: "Escanee su código de barras"
- ✅ Campo de entrada optimizado para lector láser
- ✅ Detección automática de entrada (el lector envía Enter automáticamente)
- ✅ Prevención de doble lectura
- ✅ Mantenimiento automático del foco en el campo de entrada
- ✅ Simulador actualizado para pruebas con código de barras

### 2. Controlador ([AccessControlController.php](app/controllers/AccessControlController.php))
- ✅ Método `processFingerprint()` actualizado para recibir código de barras
- ✅ Búsqueda directa por documento (código de barras = documento)
- ✅ Método de registro cambiado de 'HUELLA' a 'BARCODE'
- ✅ Mensajes de error actualizados

### 3. Modelo ([AccessControlModel.php](app/models/AccessControlModel.php))
- ✅ Método `findByFingerprint()` eliminado (ya no se necesita)
- ✅ Método `getPersonasActivas()` reemplaza a `getPersonasWithFingerprint()`
- ✅ Filtros actualizados: de 'HUELLA' a 'BARCODE' en consultas SQL
- ✅ Valor por defecto del método cambiado a 'BARCODE'

## ¿Cómo Funciona el Sistema?

### Flujo de Operación

1. **El vigilante accede al kiosko** (requiere inicio de sesión)
2. **El campo de entrada queda activo** esperando el escaneo
3. **Usuario escanea su código de barras** con el lector láser
4. **El lector escribe el código automáticamente** y presiona Enter
5. **El sistema busca el documento** en la base de datos
6. **Valida el estado** de la persona (ACTIVO/INACTIVO)
7. **Determina si es ENTRADA o SALIDA** según el último registro
8. **Registra la marcación** en la base de datos
9. **Muestra el resultado** en pantalla (permitido/denegado)
10. **Regresa al estado de espera** automáticamente después de 4 segundos

### Prevención de Doble Lectura

El sistema implementa protección contra doble lectura:
- Si el mismo código se escanea en menos de 2 segundos, se ignora
- Esto evita marcaciones duplicadas accidentales

### Mantenimiento de Foco

El sistema mantiene automáticamente el foco en el campo de entrada:
- Cada 500ms verifica que el campo esté activo
- Si el foco se pierde, lo restaura automáticamente
- Garantiza que el lector láser siempre funcione

## Configuración del Lector Láser

### Requisitos del Lector

El lector láser debe estar configurado para:
- ✅ **Modo Teclado (Keyboard Wedge)**: simula entrada de teclado
- ✅ **Sufijo Enter**: envía la tecla Enter después del código
- ✅ **Prefijo**: ninguno (opcional)
- ✅ **Formato**: números/texto según el documento

### Tipos de Códigos Compatibles

El sistema acepta cualquier tipo de código de barras que contenga el número de documento:
- Code 39
- Code 128
- EAN-13
- QR Code (si el lector lo soporta)
- Cualquier formato que devuelva texto alfanumérico

## Formato del Código de Barras

El código de barras debe contener el **número de documento** de la persona:
```
Ejemplo: 1234567890
```

Este número debe coincidir con el campo `documento` en la tabla `personas` de la base de datos.

## Simulador (Modo Testing)

Para pruebas sin lector físico:

1. **Seleccionar persona** del menú desplegable
2. **Clic en "Simular Escaneo"**
3. El sistema procesará como si se hubiera escaneado el código

También puedes:
- **Escribir manualmente** el número de documento y presionar Enter
- **Simular código desconocido** con el botón "Código Desconocido"

## Acceso al Kiosko

**URL**: `http://tu-dominio.com/control-ingreso/kiosk`

**Requisitos**:
- Usuario con rol: `vigilante` o `admin`
- Sesión activa

## Base de Datos

### Tabla de Marcaciones

Los registros quedan guardados con:
```sql
metodo = 'BARCODE'
documento_capturado = código escaneado
```

### Consulta de Marcaciones

Para ver solo las marcaciones por código de barras:
```sql
SELECT * FROM marcaciones WHERE metodo = 'BARCODE';
```

## Ventajas del Código de Barras

✅ **Más rápido**: lectura instantánea  
✅ **Sin contacto**: más higiénico  
✅ **Más económico**: lectores más baratos que biométricos  
✅ **Sin entrenamiento**: cualquier vigilante puede usarlo  
✅ **Más confiable**: menos errores de lectura  
✅ **Compatible**: funciona con cualquier lector láser USB  
✅ **Plug & Play**: solo conectar y usar  

## Solución de Problemas

### El lector no funciona

1. Verificar que el lector esté conectado (USB)
2. Probar en un bloc de notas para ver si escribe
3. Verificar que el cursor esté en el campo de entrada
4. Revisar la configuración del lector (debe enviar Enter)

### El código se duplica

- El sistema tiene protección de 2 segundos
- Si persiste, verificar la configuración del lector
- Puede que esté enviando el código dos veces

### No reconoce el código

1. Verificar que el documento existe en la base de datos
2. Revisar que el código de barras contenga el número correcto
3. Verificar que no haya espacios ni caracteres extraños

### La pantalla no regresa al inicio

- Debe regresar automáticamente después de 4 segundos
- Si no lo hace, revisar la consola del navegador (F12)
- Puede haber un error JavaScript

## Personalización

### Cambiar el tiempo de espera

En [kiosk.php](app/views/access_control/kiosk.php), línea ~540:
```javascript
processingTimeout = setTimeout(() => {
    resetScanner();
}, 4000); // Cambiar 4000 (4 segundos)
```

### Cambiar el tiempo de protección contra doble lectura

En [kiosk.php](app/views/access_control/kiosk.php), línea ~490:
```javascript
if (barcode === lastBarcode && (now - lastBarcodeTime) < 2000) {
    // Cambiar 2000 (2 segundos)
```

### Ocultar el simulador en producción

En [kiosk.php](app/views/access_control/kiosk.php), agregar en el CSS:
```css
.simulator-panel {
    display: none; /* Ocultar en producción */
}
```

## Soporte

Para problemas o preguntas sobre el sistema, revisar:
- [README.md](README.md) - Documentación general
- [MODULO_USUARIOS_README.md](MODULO_USUARIOS_README.md) - Módulo de usuarios

---

**Fecha de modificación**: 2026-02-05  
**Sistema**: Control de Ingreso SENA  
**Método**: Código de Barras (BARCODE)
