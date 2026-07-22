# MÓDULO DE USUARIOS - GUÍA DE IMPLEMENTACIÓN

## ✅ ARCHIVOS CREADOS

### SQL y Base de Datos
- `database/usuarios_module.sql` - Schema extendido con nuevas tablas y columnas

### Modelos
- `app/models/UserModel.php` - CRUD completo + importación masiva
- `app/helpers/Validator.php` - Validador de datos reutilizable

### Controlador
- `app/controllers/UsersController.php` - 10 acciones (index, create, store, edit, update, toggle, delete, importForm, importPreview, importConfirm)

### Vistas
- `app/views/usuarios/index.php` - Listado con filtros y paginación
- `app/views/usuarios/create.php` - Formulario de creación
- `app/views/usuarios/edit.php` - Formulario de edición
- `app/views/usuarios/import.php` - Formulario de importación CSV
- `app/views/usuarios/import_preview.php` - Vista previa de importación

### Configuración
- `public/index.php` - Rutas agregadas
- `app/views/layouts/header.php` - Menú actualizado
- `storage/temp/` - Directorio para archivos temporales

---

## 🚀 PASOS DE INSTALACIÓN

### 1. Ejecutar SQL

```bash
mysql -u root -p senaaccses < database/usuarios_module.sql
```

**⚠️ IMPORTANTE:** Si ya tienes datos en la tabla `usuarios`, primero debes:

```sql
-- Agregar columna documento a usuarios existentes
UPDATE usuarios SET documento = CONCAT('DOC', LPAD(id, 8, '0')) WHERE documento IS NULL;
```

### 2. Verificar Permisos

```bash
# Windows PowerShell
New-Item -ItemType Directory -Path "storage/temp" -Force
icacls "storage/temp" /grant Everyone:F
```

### 3. Probar el Módulo

1. Inicia sesión como admin
2. Ve a: `http://localhost:8000/usuarios`
3. Deberías ver el listado vacío (solo los 3 usuarios de prueba)

---

## 📋 CHECKLIST DE SEGURIDAD IMPLEMENTADO

✅ **Autenticación y Autorización**
- Solo usuarios autenticados pueden acceder
- Admin: acceso total (crear, editar, eliminar)
- Instructor/Vigilante: solo lectura (listar y ver)
- Protección contra escalada de privilegios

✅ **Validación de Datos**
- Validación de todos los campos en servidor
- Documento único (no duplicados)
- Email válido (formato)
- Username único
- Password mínimo 8 caracteres con hash bcrypt

✅ **Protección CSRF**
- Token CSRF en todos los formularios POST
- Validación en cada acción destructiva

✅ **SQL Injection**
- PDO con prepared statements en todas las consultas
- Parámetros bindeados, nunca concatenación

✅ **XSS Prevention**
- Función `e()` (htmlspecialchars) en todas las salidas
- Sanitización de entradas con `sanitize()`

✅ **Auditoría**
- Tabla `auditoria_usuarios` registra:
  - Quién hizo la acción (usuario_ejecutor_id)
  - Qué acción (crear, editar, eliminar, activar, desactivar)
  - Cuándo (created_at)
  - Desde dónde (ip_address)
  - Qué cambió (datos_anteriores, datos_nuevos en JSON)

✅ **Borrado Lógico**
- No se eliminan registros físicamente
- Campo `deleted_at` marca registros eliminados
- Se pueden recuperar si es necesario

✅ **Control de Errores**
- Try-catch en operaciones críticas
- Transacciones en operaciones múltiples
- Rollback automático en caso de error
- Logs de error en archivo (error_log)

---

## 📊 ESTRUCTURA DE DATOS

### Tabla: usuarios (extendida)

```sql
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo_persona ENUM('admin', 'instructor', 'vigilante', 'aprendiz', 'contratista', 'visitante', 'proveedor'),
    empresa VARCHAR(150) NULL,
    email VARCHAR(150) NULL,
    username VARCHAR(50) NULL,
    password_hash VARCHAR(255) NULL,
    rol ENUM('admin', 'instructor', 'vigilante', 'persona'),
    estado ENUM('activo', 'inactivo'),
    ultimo_acceso DATETIME NULL,
    intentos_fallidos TINYINT UNSIGNED DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL,
    -- Índices y foreign keys...
);
```

### Tabla: auditoria_usuarios

```sql
CREATE TABLE auditoria_usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    accion ENUM('crear', 'editar', 'eliminar', 'activar', 'desactivar', 'cambio_password'),
    usuario_ejecutor_id INT UNSIGNED NULL,
    datos_anteriores TEXT NULL COMMENT 'JSON',
    datos_nuevos TEXT NULL COMMENT 'JSON',
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Foreign keys...
);
```

### Tabla: importaciones

```sql
CREATE TABLE importaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    archivo_nombre VARCHAR(255) NOT NULL,
    tipo ENUM('usuarios', 'aprendices', 'instructores'),
    usuario_id INT UNSIGNED NOT NULL,
    total_filas INT UNSIGNED DEFAULT 0,
    insertados INT UNSIGNED DEFAULT 0,
    actualizados INT UNSIGNED DEFAULT 0,
    omitidos INT UNSIGNED DEFAULT 0,
    errores INT UNSIGNED DEFAULT 0,
    estado ENUM('pendiente', 'procesando', 'completado', 'error'),
    log_errores TEXT NULL COMMENT 'JSON',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
);
```

---

## 📁 FORMATO DEL ARCHIVO CSV DE IMPORTACIÓN

### Ejemplo con encabezados (recomendado):

```csv
documento,nombre,tipo_persona,empresa,email,username
1234567890,Juan Pérez González,aprendiz,SENA,juan.perez@example.com,
9876543210,María López Ruiz,instructor,SENA,maria.lopez@sena.edu.co,mlopez
CC987654,Carlos Rodríguez,vigilante,Seguridad Total,carlos@example.com,crodriguez
1122334455,Ana García,contratista,Empresa XYZ,ana@xyz.com,
CC112233,Pedro Martínez,visitante,Gobierno,,
```

### Reglas de Validación:

1. **documento** (obligatorio):
   - Único en el sistema
   - 6-20 caracteres alfanuméricos
   - Se convierte a mayúsculas automáticamente

2. **nombre** (obligatorio):
   - 3-100 caracteres

3. **tipo_persona** (obligatorio):
   - Valores válidos: `aprendiz`, `instructor`, `admin`, `vigilante`, `contratista`, `visitante`, `proveedor`

4. **empresa** (opcional):
   - Hasta 150 caracteres

5. **email** (opcional):
   - Debe ser formato válido si se proporciona
   - Único si se proporciona

6. **username** (opcional):
   - Obligatorio para `admin`, `instructor`, `vigilante`
   - 4-50 caracteres
   - Único si se proporciona

### Modos de Importación:

- **UPSERT (recomendado)**: Si el documento ya existe, actualiza los datos
- **INSERT**: Si el documento ya existe, omite la fila

### Proceso:

1. **Subir archivo** → Validación inmediata
2. **Vista previa** → Muestra primeras 20 filas + resumen de errores
3. **Confirmar** → Ejecuta la importación con transacción
4. **Resultado** → Muestra insertados/actualizados/omitidos/errores

---

## 🧪 PRUEBAS MANUALES

### Caso 1: Crear Usuario Aprendiz

1. Ve a `/usuarios/create`
2. Ingresa:
   - Documento: `1234567890`
   - Nombre: `Juan Pérez`
   - Tipo: `aprendiz`
   - Estado: `activo`
3. Clic en "Guardar Usuario"
4. Verifica que aparece en el listado

### Caso 2: Crear Usuario Administrador

1. Ve a `/usuarios/create`
2. Ingresa:
   - Documento: `CC987654`
   - Nombre: `Carlos Admin`
   - Tipo: `admin`
   - Username: `cadmin`
   - Password: `Admin123!`
   - Estado: `activo`
3. Verifica que se muestra el formulario de acceso
4. Guarda y verifica

### Caso 3: Editar Usuario

1. Ve a `/usuarios`
2. Clic en "Editar" en cualquier usuario
3. Cambia el nombre
4. Guarda
5. Verifica en la auditoría (tabla `auditoria_usuarios`)

### Caso 4: Activar/Desactivar

1. Clic en botón de estado
2. Confirma
3. Verifica cambio visual inmediato

### Caso 5: Eliminar Usuario

1. Clic en botón rojo de eliminar
2. Confirma
3. Usuario desaparece del listado (borrado lógico)
4. Verifica en BD que `deleted_at` no es NULL

### Caso 6: Buscar Usuarios

1. Usa filtros:
   - Buscar por documento: `1234`
   - Filtrar por tipo: `aprendiz`
   - Filtrar por estado: `activo`
2. Verifica resultados correctos

### Caso 7: Importar CSV

1. Ve a `/usuarios/import`
2. Descarga plantilla CSV
3. Agrega 10 usuarios de prueba
4. Sube el archivo
5. Revisa vista previa
6. Confirma importación
7. Verifica en listado

### Caso 8: Importar con Errores

1. Crea CSV con errores intencionales:
   - Documento duplicado
   - Email inválido
   - Tipo de persona incorrecto
2. Sube archivo
3. Vista previa debe mostrar errores en rojo
4. Solo se importan filas válidas

### Caso 9: Probar UPSERT

1. Importa CSV con 5 usuarios nuevos
2. Modifica el CSV (mismos documentos, cambiar nombres)
3. Importa nuevamente en modo UPSERT
4. Verifica que se actualizaron (no duplicaron)

### Caso 10: Validar Permisos

1. Inicia sesión como `instructor` o `vigilante`
2. Ve a `/usuarios`
3. Verifica que NO aparecen botones de crear/editar/eliminar
4. Intenta acceder directamente a `/usuarios/create`
5. Debe redirigir al dashboard con mensaje de error

---

## 📝 CONSULTAS SQL ÚTILES

### Ver últimos usuarios creados

```sql
SELECT documento, nombre, tipo_persona, estado, created_at 
FROM usuarios 
WHERE deleted_at IS NULL 
ORDER BY created_at DESC 
LIMIT 10;
```

### Ver auditoría de un usuario

```sql
SELECT 
    a.accion,
    a.ip_address,
    a.created_at,
    u.nombre AS ejecutor,
    a.datos_anteriores,
    a.datos_nuevos
FROM auditoria_usuarios a
LEFT JOIN usuarios u ON a.usuario_ejecutor_id = u.id
WHERE a.usuario_id = 1
ORDER BY a.created_at DESC;
```

### Ver estadísticas por tipo

```sql
SELECT 
    tipo_persona,
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
FROM usuarios
WHERE deleted_at IS NULL
GROUP BY tipo_persona;
```

### Ver historial de importaciones

```sql
SELECT 
    i.archivo_nombre,
    i.total_filas,
    i.insertados,
    i.actualizados,
    i.errores,
    i.created_at,
    u.nombre AS importado_por
FROM importaciones i
JOIN usuarios u ON i.usuario_id = u.id
ORDER BY i.created_at DESC
LIMIT 20;
```

### Recuperar usuario eliminado

```sql
UPDATE usuarios 
SET deleted_at = NULL 
WHERE id = 123 AND deleted_at IS NOT NULL;
```

---

## ⚠️ PROBLEMAS COMUNES Y SOLUCIONES

### Error: "Duplicate entry for key 'documento'"

**Causa:** Intentando crear usuario con documento que ya existe

**Solución:** 
- Verificar que el documento sea único
- Si es importación, usar modo UPSERT para actualizar

### Error: "Call to undefined method Auth::hasRole()"

**Causa:** Método no existe en clase Auth

**Solución:** Ya implementado en `app/middleware/Auth.php` líneas 113-116

### Error: "Failed to open stream: Permission denied" en storage/temp

**Causa:** Sin permisos de escritura

**Solución:**
```bash
# Windows
icacls "storage\temp" /grant Everyone:F

# Linux/Mac
chmod -R 777 storage/temp
```

### Error: "Unknown column 'documento' in field list"

**Causa:** No ejecutaste el SQL de migración

**Solución:**
```bash
mysql -u root -p senaaccses < database/usuarios_module.sql
```

### Importación se queda en "procesando"

**Causa:** Error durante la importación no capturado

**Solución:**
- Revisar logs de PHP: `storage/logs/`
- Verificar que el archivo CSV es válido
- Probar con archivo más pequeño primero

---

## 🔄 PRÓXIMOS PASOS (FUTURO)

1. **Exportar a Excel:**
   - Botón "Exportar" en listado
   - Generar XLSX con PHPSpreadsheet o CSV simple

2. **Foto de perfil:**
   - Subir foto en crear/editar
   - Almacenar en `storage/uploads/avatars/`
   - Mostrar en listado y perfil

3. **Historial de cambios:**
   - Vista dedicada para ver auditoría por usuario
   - Diff visual de cambios

4. **Importación avanzada:**
   - Soporte para XLSX (requiere librería)
   - Mapeo de columnas personalizado
   - Importación programada

5. **API REST:**
   - Endpoints JSON para integración externa
   - Autenticación con token

---

## 📞 SOPORTE

Si encuentras errores o tienes preguntas:

1. Revisa los logs: `error_log` de PHP
2. Verifica la consola del navegador (F12)
3. Revisa la tabla `auditoria_usuarios` para debugging

---

**Desarrollado con ❤️ para SENA**  
**Fecha:** Enero 2026  
**Versión:** 1.0.0
