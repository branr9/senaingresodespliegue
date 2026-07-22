# Instrucciones de Instalación - Sistema de Control de Ingreso SENA

## Requisitos Previos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Servidor web (Apache/Nginx) o PHP built-in server
- Extensiones PHP requeridas:
  - PDO
  - pdo_mysql
  - mbstring
  - session

## Instalación Paso a Paso

### 1. Configurar la Base de Datos

```bash
# Crear la base de datos ejecutando:
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql
```

### 2. Configurar Variables de Entorno

```bash
# El archivo .env ya está creado, editarlo con tus credenciales
# Abrir .env y modificar:
DB_HOST=localhost
DB_NAME=sistema_ingreso
DB_USER=root
DB_PASS=TU_PASSWORD_AQUI
```

### 3. Verificar Permisos

```bash
# Asegurar que la carpeta storage tenga permisos de escritura
chmod -R 755 storage/
```

### 4. Iniciar el Servidor

**Opción A: PHP Built-in Server (Desarrollo - RECOMENDADO)**

```bash
# Desde la raíz del proyecto:
php -S localhost:8000 -t public
```

**Opción B: XAMPP/WAMP**

1. Copiar el proyecto a `C:\xampp\htdocs\` o `C:\wamp64\www\`
2. Acceder via: `http://localhost/desarrollo-ingreso/public`

**Opción C: Apache con VirtualHost (Avanzado)**

```apache
<VirtualHost *:80>
    ServerName ingreso.local
    DocumentRoot "C:/Users/Brandon/Documents/ingreso sena/desarrollo ingreso/public"
    
    <Directory "C:/Users/Brandon/Documents/ingreso sena/desarrollo ingreso/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Agregar a `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 ingreso.local
```

### 5. Probar el Sistema

1. Abrir navegador en: `http://localhost:8000/login`
2. Usar credenciales de prueba:
   - **Admin:** `admin` / `Admin123!`
   - **Instructor:** `instructor` / `Admin123!`
   - **Vigilante:** `vigilante` / `Admin123!`

## Solución de Problemas

### Error de Conexión a BD

**Síntoma:** "Error de conexión a la base de datos"

**Solución:**
- Verificar que MySQL está corriendo
- Verificar credenciales en `.env`
- Confirmar que la base de datos `sistema_ingreso` existe

```bash
# Verificar MySQL en Windows
netstat -an | find "3306"
```

### Errores de Sesión

**Síntoma:** "Session failed" o sesión no se mantiene

**Solución:**
- Verificar permisos en carpeta `storage/logs`
- Revisar configuración de `session.save_path` en `php.ini`

### 404 en Rutas

**Síntoma:** Todas las rutas dan 404

**Solución:**
- Verificar que el servidor apunta a `/public`
- Si usa Apache, verificar `.htaccess` y `mod_rewrite`

### Warnings de PHP

**Síntoma:** Warnings o notices visibles

**Solución:**
- Cambiar `APP_DEBUG=false` en `.env` para producción

## Verificación de Instalación

✅ Acceder a `/login` muestra el formulario
✅ Login con credenciales correctas funciona
✅ Dashboard muestra información del usuario
✅ Logout cierra sesión correctamente
✅ Intentos fallidos se registran en BD

## Seguridad en Producción

Antes de desplegar en producción:

1. ✅ Cambiar `APP_ENV=production` en `.env`
2. ✅ Cambiar `APP_DEBUG=false` en `.env`
3. ✅ Cambiar contraseñas de usuarios por defecto
4. ✅ Configurar `SESSION_SECURE=true` si usa HTTPS
5. ✅ Revisar permisos de archivos (755 carpetas, 644 archivos)
6. ✅ Configurar backups automáticos de BD
7. ✅ Configurar SSL/HTTPS
8. ✅ Revisar logs regularmente

## Estructura de Base de Datos

El sistema crea 3 tablas:

- **usuarios**: Datos de usuarios y autenticación
- **sesiones**: Tracking de sesiones activas
- **auditoria_accesos**: Log de todos los accesos

## Próximos Pasos

Una vez instalado el módulo de autenticación:

1. Cambiar contraseñas por defecto
2. Crear usuarios reales
3. Probar todos los roles
4. Revisar auditoría de accesos
5. Preparar para módulos adicionales

## Soporte

Para problemas o dudas, revisar:
- Logs en `storage/logs/`
- Tabla `auditoria_accesos` en BD
- Configuración en `config/config.php`

---

**¡Instalación Completa!** 🎉

El sistema está listo para usar. Procede a probar el login y explorar el dashboard.
