# Módulo de Personal Externo

## Descripción
Sistema de registro y control de acceso para personal externo (visitantes, contratistas, proveedores) que **NO tienen carnet SENA**. Permite registrar entrada, salida, motivos de visita y controlar el tiempo de permanencia.

## Características

### ✅ Registro de Entrada
- Captura de datos del visitante (documento, nombres, empresa, contacto)
- Motivo de la visita y persona visitada
- Área de destino
- Registro automático de fecha/hora de entrada
- Vigilante que registra la entrada

### ✅ Control de Permanencia
- Lista de personas actualmente dentro
- Cálculo automático del tiempo de permanencia
- Registro de salida rápido

### ✅ Historial Completo
- Lista de todos los registros con filtros
- Búsqueda por documento, nombre o empresa
- Filtro por estado (dentro/salió)
- Filtro por rango de fechas
- Paginación

### ✅ Detalles y Reportes
- Vista detallada de cada registro
- Información del vigilante de entrada y salida
- Observaciones de entrada y salida
- Estadísticas de tiempo de permanencia

## Instalación

### 1. Crear la tabla en la base de datos

**IMPORTANTE:** Ejecuta el archivo SQL en phpMyAdmin:

```sql
-- Ubicación: database/registros_acceso_externo.sql
```

**Pasos:**
1. Abre phpMyAdmin
2. Selecciona la base de datos `sena_acceso`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido del archivo `database/registros_acceso_externo.sql`
5. Haz clic en "Continuar"

### 2. Verificar instalación

El módulo ya está integrado en el sistema:
- ✅ Controlador: `app/controllers/ExternalAccessController.php`
- ✅ Modelo: `app/models/ExternalAccessModel.php`
- ✅ Vistas: `app/views/external_access/`
- ✅ Rutas: Agregadas en `public/index.php`
- ✅ Dashboard: Módulo visible para Admin y Vigilante

## Acceso al Módulo

### Permisos
Solo usuarios con rol de **Admin** o **Vigilante** pueden acceder.

### URLs del módulo:
- `/acceso-externo` - Lista de registros
- `/acceso-externo/registro-entrada` - Formulario de entrada
- `/acceso-externo/personas-dentro` - Personas actualmente dentro
- `/acceso-externo/detalle/{id}` - Ver detalle de registro

## Uso del Sistema

### Registrar Entrada
1. Ir a Dashboard → **Personal Externo**
2. Clic en "Registrar Entrada"
3. Completar formulario:
   - **Datos del Visitante**: Documento, nombres, empresa, contacto
   - **Información de Visita**: Motivo, persona visitada, área destino
4. Guardar

### Registrar Salida
**Opción 1 - Desde lista principal:**
1. Ir a "Personal Externo"
2. Buscar el registro (ícono de salida verde)
3. Confirmar salida

**Opción 2 - Desde personas dentro:**
1. Ir a "Personas Dentro"
2. Clic en "Registrar Salida"
3. Agregar observaciones (opcional)
4. Confirmar

### Ver Detalles
1. En cualquier lista, clic en ícono de ojo
2. Ver información completa del registro
3. Si está dentro, se puede registrar salida desde aquí

## Estructura de Datos

### Tabla: `registros_acceso_externo`

Campos principales:
- **Datos del visitante**: documento, nombre, empresa, teléfono, email
- **Visita**: motivo_visita, persona_visitada, area_destino
- **Control**: fecha_entrada, fecha_salida, tiempo_permanencia
- **Vigilancia**: vigilante_entrada_id, vigilante_salida_id
- **Estado**: DENTRO / SALIO

### Vista SQL: `vista_acceso_externo`

Combina datos de:
- Registro de acceso
- Información de vigilantes (entrada y salida)
- Cálculo automático de tiempo transcurrido

## Validaciones

### Campos Obligatorios
- ✅ Documento
- ✅ Nombres
- ✅ Motivo de visita

### Campos Opcionales
- Apellidos
- Empresa
- Teléfono
- Email
- Persona visitada
- Área destino
- Observaciones

## Características Técnicas

### Seguridad
- ✅ Protección CSRF en formularios
- ✅ Validación de datos
- ✅ Control de permisos por rol
- ✅ Sanitización de entradas

### Performance
- ✅ Uso de vistas SQL para consultas optimizadas
- ✅ Paginación de resultados
- ✅ Índices en campos de búsqueda

### Auditoría
- ✅ Registro de vigilante que captura entrada
- ✅ Registro de vigilante que captura salida
- ✅ Timestamps automáticos
- ✅ Observaciones de entrada y salida

## Próximas Mejoras (Opcional)

- 📋 Exportar registros a Excel/PDF
- 📊 Dashboard de estadísticas de visitas
- 📷 Captura de foto del visitante
- 🔔 Notificaciones a persona visitada
- 📱 Registro desde tablet/móvil
- 🎫 Generación de pase temporal

## Soporte

Si encuentras algún error, revisa:
1. `storage/logs/database_errors.log` - Errores de base de datos
2. Consola del navegador (F12) - Errores JavaScript
3. Verificar que la tabla esté creada correctamente

---

**Módulo creado:** 2026
**Autor:** Sistema SENA Control de Acceso
**Versión:** 1.0.0
