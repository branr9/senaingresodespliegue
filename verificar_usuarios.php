<?php
/**
 * Script de verificación de usuarios migrados
 */

// Definir constantes
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');
define('STORAGE_PATH', __DIR__ . '/storage');
define('APP_DEBUG', true);

require_once APP_PATH . '/models/Database.php';

$db = Database::getInstance();

echo "=== VERIFICACIÓN DE USUARIOS MIGRADOS ===\n\n";

// Consultar todos los usuarios del sistema
$sql = "SELECT us.id as usuario_sistema_id,
               us.username,
               us.estado as usuario_estado,
               p.id as persona_id,
               p.documento,
               p.nombres,
               p.apellidos,
               p.email,
               p.estado as persona_estado,
               crs.codigo as rol,
               crs.nombre as rol_nombre
        FROM usuarios_sistema us
        INNER JOIN personas p ON us.persona_id = p.id
        INNER JOIN cat_rol_sistema crs ON us.rol_id = crs.id
        WHERE p.deleted_at IS NULL
        ORDER BY us.id";

$usuarios = $db->fetchAll($sql, []);

if (empty($usuarios)) {
    echo "⚠️ NO SE ENCONTRARON USUARIOS EN EL SISTEMA\n";
    echo "Ejecuta el script de migración: database/migracion_schema_completo.sql\n\n";
    exit;
}

echo "✅ Usuarios encontrados: " . count($usuarios) . "\n\n";

foreach ($usuarios as $user) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID Usuario Sistema: {$user['usuario_sistema_id']}\n";
    echo "Username: {$user['username']}\n";
    echo "Email: " . ($user['email'] ?? 'N/A') . "\n";
    echo "Documento: {$user['documento']}\n";
    echo "Nombre: {$user['nombres']} {$user['apellidos']}\n";
    echo "Rol: {$user['rol_nombre']} ({$user['rol']})\n";
    echo "Estado Usuario: {$user['usuario_estado']}\n";
    echo "Estado Persona: {$user['persona_estado']}\n";
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📝 CREDENCIALES DE ACCESO:\n\n";
echo "Para el usuario ADMIN:\n";
echo "  Username: admin\n";
echo "  Email: admin@sena.edu.co\n";
echo "  Password: Admin123!\n\n";

echo "Para el usuario INSTRUCTOR:\n";
echo "  Username: instructor\n";
echo "  Email: instructor@sena.edu.co\n";
echo "  Password: Admin123!\n\n";

echo "Para el usuario VIGILANTE:\n";
echo "  Username: vigilante\n";
echo "  Email: vigilante@sena.edu.co\n";
echo "  Password: Admin123!\n\n";

echo "💡 Puedes iniciar sesión usando USERNAME o EMAIL\n";
