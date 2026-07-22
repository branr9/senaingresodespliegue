# Sistema de Control de Ingreso SENA

Sistema MVC en PHP puro para gestión de accesos, autenticación y control de ingreso del SENA.

## 🚀 Características

- ✅ Autenticación segura con bcrypt
- ✅ Sistema de roles (Admin, Instructor, Vigilante)
- ✅ Protección CSRF
- ✅ Control de intentos de login
- ✅ Auditoría de accesos
- ✅ Sesiones seguras
- ✅ Arquitectura MVC limpia

## 📋 Requisitos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Extensiones PHP: PDO, pdo_mysql, mbstring

## 🔧 Instalación

### 1. Configurar Base de Datos

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql
```

### 2. Configurar Variables de Entorno

Edita el archivo `.env` con tus credenciales:

```env
DB_HOST=localhost
DB_NAME=sistema_ingreso
DB_USER=root
DB_PASS=tu_password
```

### 3. Iniciar Servidor

```bash
php -S localhost:8000 -t public
```

### 4. Acceder al Sistema

Abre tu navegador en: `http://localhost:8000`

## 👤 Credenciales de Prueba

- **Admin:** `admin` / `Admin123!`
- **Instructor:** `instructor` / `Admin123!`
- **Vigilante:** `vigilante` / `Admin123!`

⚠️ **IMPORTANTE:** Cambia estas contraseñas en producción.

## 📁 Estructura del Proyecto

```
desarrollo-ingreso/
├── app/
│   ├── controllers/      # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── views/           # Vistas HTML/PHP
│   ├── middleware/      # Middleware de autenticación
│   └── helpers/         # Funciones auxiliares
├── config/              # Configuración
├── database/            # Scripts SQL
├── public/              # Punto de entrada web
│   ├── css/
│   ├── js/
│   └── index.php
└── storage/             # Logs y almacenamiento
```

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()`
- Consultas preparadas (PDO)
- Protección CSRF en formularios
- Sesiones seguras con httponly y samesite
- Bloqueo temporal por intentos fallidos
- Auditoría completa de accesos

## 🛠️ Desarrollo

Este es el módulo base de autenticación. Los siguientes módulos incluirán:

- Control de ingreso por huella
- Reserva de ambientes
- Control de llaves
- Permisos de salida

## 📝 Licencia

Desarrollado para el SENA - 2026

## 👨‍💻 Autor

Sistema desarrollado como proyecto de Control de Ingreso SENA
